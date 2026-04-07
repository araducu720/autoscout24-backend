<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmyleVehicle;
use App\Models\SmyleOrder;
use App\Models\SmyleFinancing;
use App\Models\Vehicle;
use App\Services\SmyleService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SmyleController extends Controller
{
    protected SmyleService $smyleService;

    public function __construct(SmyleService $smyleService)
    {
        $this->smyleService = $smyleService;
    }

    // ============================================
    // PUBLIC ENDPOINTS
    // ============================================

    /**
     * GET /smyle/vehicles - List Smyle-eligible vehicles
     */
    public function vehicles(Request $request): JsonResponse
    {
        $query = SmyleVehicle::with([
            'vehicle.make', 'vehicle.model', 'vehicle.images', 'vehicle.user',
            'latestQualityCheck',
        ])
            ->available();

        // Apply filters
        if ($request->filled('make_id')) {
            $query->whereHas('vehicle', fn($q) => $q->where('make_id', $request->make_id));
        }
        if ($request->filled('model_id')) {
            $query->whereHas('vehicle', fn($q) => $q->where('model_id', $request->model_id));
        }
        if ($request->filled('price_min')) {
            $query->whereHas('vehicle', fn($q) => $q->where('price', '>=', $request->price_min));
        }
        if ($request->filled('price_max')) {
            $query->whereHas('vehicle', fn($q) => $q->where('price', '<=', $request->price_max));
        }
        if ($request->filled('year_min')) {
            $query->whereHas('vehicle', fn($q) => $q->where('year', '>=', $request->year_min));
        }
        if ($request->filled('mileage_max')) {
            $query->whereHas('vehicle', fn($q) => $q->where('mileage', '<=', $request->mileage_max));
        }
        if ($request->filled('fuel_type')) {
            $query->whereHas('vehicle', fn($q) => $q->where('fuel_type', $request->fuel_type));
        }
        if ($request->filled('transmission')) {
            $query->whereHas('vehicle', fn($q) => $q->where('transmission', $request->transmission));
        }
        if ($request->filled('body_type')) {
            $query->whereHas('vehicle', fn($q) => $q->where('body_type', $request->body_type));
        }

        // Sorting
        $sortBy = $request->query('sort_by', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        if (in_array($sortBy, ['price', 'year', 'mileage'])) {
            $query->join('vehicles', 'smyle_vehicles.vehicle_id', '=', 'vehicles.id')
                ->orderBy("vehicles.{$sortBy}", $sortOrder)
                ->select('smyle_vehicles.*');
        } else {
            $query->orderBy($sortOrder === 'desc' ? 'created_at' : 'created_at', $sortOrder);
        }

        $smyleVehicles = $query->paginate($request->query('per_page', 20));

        $smyleVehicles->getCollection()->transform(function ($sv) {
            return $this->formatSmyleVehicle($sv);
        });

        return response()->json([
            'data' => $smyleVehicles->items(),
            'meta' => [
                'current_page' => $smyleVehicles->currentPage(),
                'last_page' => $smyleVehicles->lastPage(),
                'per_page' => $smyleVehicles->perPage(),
                'total' => $smyleVehicles->total(),
            ],
        ]);
    }

    /**
     * GET /smyle/vehicles/{id} - Get single Smyle vehicle details
     */
    public function vehicleShow(int $id): JsonResponse
    {
        $smyleVehicle = SmyleVehicle::with([
            'vehicle.make', 'vehicle.model', 'vehicle.images', 'vehicle.user',
            'latestQualityCheck', 'qualityChecks',
        ])->findOrFail($id);

        return response()->json([
            'data' => $this->formatSmyleVehicle($smyleVehicle, true),
        ]);
    }

    /**
     * POST /smyle/delivery-cost - Calculate delivery cost
     */
    public function deliveryCost(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:smyle_vehicles,id',
            'delivery_postal_code' => 'required|string|size:5',
        ]);

        $smyleVehicle = SmyleVehicle::findOrFail($validated['vehicle_id']);
        $fromPostalCode = $smyleVehicle->location_postal_code ?? '80331';

        $result = $this->smyleService->calculateDeliveryCost($fromPostalCode, $validated['delivery_postal_code']);

        return response()->json(['data' => $result]);
    }

    /**
     * GET /smyle/eligibility/{vehicleId} - Check vehicle eligibility
     */
    public function checkEligibility(int $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $result = $this->smyleService->isEligible($vehicle);

        return response()->json(['data' => $result]);
    }

    /**
     * POST /smyle/financing/calculate - Calculate financing options
     */
    public function calculateFinancing(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_price' => 'required|numeric|min:1000|max:500000',
            'down_payment' => 'nullable|numeric|min:0',
            'loan_term_months' => 'nullable|integer|in:12,24,36,48,60,72,84',
            'interest_rate' => 'nullable|numeric|min:0|max:20',
        ]);

        $result = $this->smyleService->calculateFinancing(
            $validated['vehicle_price'],
            $validated['down_payment'] ?? 0,
            $validated['loan_term_months'] ?? 48,
            $validated['interest_rate'] ?? null
        );

        return response()->json(['data' => $result]);
    }

    /**
     * GET /smyle/stats - Get Smyle program stats
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => [
                'total_vehicles' => SmyleVehicle::available()->count(),
                'total_orders' => SmyleOrder::count(),
                'completed_orders' => SmyleOrder::where('status', 'completed')->count(),
                'avg_delivery_days' => 28,
                'satisfaction_rate' => 97,
                'return_rate' => 2,
            ],
        ]);
    }

    // ============================================
    // PROTECTED ENDPOINTS (Authenticated)
    // ============================================

    /**
     * POST /smyle/orders - Create Smyle order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'smyle_vehicle_id' => 'required|exists:smyle_vehicles,id',
            'delivery_postal_code' => 'required|string|size:5',
            'delivery_city' => 'required|string|max:255',
            'delivery_street' => 'required|string|max:500',
            'delivery_house_number' => 'nullable|string|max:20',
            'desired_license_plate' => 'nullable|string|max:20',
            'registration_district' => 'nullable|string|max:255',
            'buyer_phone' => 'nullable|string|max:30',
            'buyer_notes' => 'nullable|string|max:1000',
            'preferred_delivery_date' => 'nullable|date|after:today',
            'payment_method' => 'nullable|in:paypal,instant_transfer,financing,bank_transfer',
            // Financing fields
            'down_payment' => 'nullable|numeric|min:0',
            'loan_term_months' => 'nullable|integer|in:12,24,36,48,60,72,84',
            'employment_status' => 'nullable|string',
            'monthly_income' => 'nullable|numeric|min:0',
            'monthly_expenses' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $smyleVehicle = SmyleVehicle::with('vehicle')->findOrFail($validated['smyle_vehicle_id']);

        if (!$smyleVehicle->is_active || !$smyleVehicle->is_eligible) {
            return response()->json(['message' => 'This vehicle is no longer available for Smyle purchase'], 422);
        }

        // Can't buy own vehicle
        if ($smyleVehicle->vehicle->user_id === $user->id) {
            return response()->json(['message' => 'You cannot purchase your own vehicle'], 422);
        }

        try {
            $order = $this->smyleService->createOrder($user, $smyleVehicle, $validated);

            return response()->json([
                'message' => 'Smyle order created successfully',
                'data' => $this->formatOrder($order->load([
                    'buyer', 'vehicle.make', 'vehicle.model', 'delivery',
                    'registration', 'insurance', 'warranty', 'financing', 'timeline',
                ])),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Smyle order creation error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create Smyle order',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * GET /smyle/orders - List user's Smyle orders
     */
    public function orders(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = SmyleOrder::with([
            'buyer', 'vehicle.make', 'vehicle.model', 'vehicle.images',
            'delivery', 'registration', 'insurance', 'warranty', 'financing',
        ])
            ->forBuyer($user->id)
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate($request->query('per_page', 20));

        $orders->getCollection()->transform(function ($order) {
            return $this->formatOrder($order);
        });

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * GET /smyle/orders/{id} - Get single order details
     */
    public function orderShow(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $order = SmyleOrder::with([
            'buyer', 'vehicle.make', 'vehicle.model', 'vehicle.images',
            'smyleVehicle', 'delivery', 'registration', 'insurance',
            'warranty', 'financing', 'timeline',
        ])
            ->forBuyer($user->id)
            ->findOrFail($id);

        return response()->json([
            'data' => $this->formatOrder($order, true),
        ]);
    }

    /**
     * POST /smyle/orders/{id}/deposit - Mark deposit as paid
     */
    public function payDeposit(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'payment_method' => 'required|in:paypal,instant_transfer,bank_transfer',
        ]);

        $order = SmyleOrder::forBuyer($user->id)->findOrFail($id);

        if (!$order->markDepositPaid($validated['payment_method'])) {
            return response()->json(['message' => 'Cannot process deposit for this order'], 422);
        }

        return response()->json(['message' => 'Deposit payment recorded successfully']);
    }

    /**
     * POST /smyle/orders/{id}/cancel - Cancel order
     */
    public function cancelOrder(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = SmyleOrder::forBuyer($user->id)->findOrFail($id);

        if (!$order->cancel($validated['reason'])) {
            return response()->json(['message' => 'This order cannot be cancelled'], 422);
        }

        // Re-activate the Smyle vehicle
        if ($order->smyleVehicle) {
            $order->smyleVehicle->update(['is_active' => true]);
        }

        return response()->json(['message' => 'Order cancelled successfully']);
    }

    /**
     * POST /smyle/orders/{id}/return - Exercise 14-day return right
     */
    public function returnOrder(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $order = SmyleOrder::forBuyer($user->id)->findOrFail($id);

        if (!$order->initiateReturn($validated['reason'])) {
            if ($order->status !== 'delivered') {
                return response()->json(['message' => 'Returns can only be initiated for delivered orders'], 422);
            }
            return response()->json(['message' => 'The 14-day return period has expired'], 422);
        }

        return response()->json(['message' => 'Return initiated successfully. We will arrange pickup of the vehicle.']);
    }

    /**
     * POST /smyle/orders/{id}/status - Update order status (admin)
     */
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $order = SmyleOrder::findOrFail($id);

        $statusMethods = [
            'deposit_paid' => 'markDepositPaid',
            'quality_check' => 'startQualityCheck',
            'registration' => 'startRegistration',
            'insurance_active' => 'activateInsurance',
            'ready_for_delivery' => 'markReadyForDelivery',
            'in_transit' => 'markInTransit',
            'delivered' => 'markDelivered',
            'completed' => 'complete',
        ];

        $method = $statusMethods[$validated['status']] ?? null;

        if (!$method || !$order->$method()) {
            return response()->json(['message' => 'Invalid status transition'], 422);
        }

        return response()->json(['message' => 'Order status updated to ' . $validated['status']]);
    }

    /**
     * GET /smyle/orders/{id}/timeline - Get order timeline
     */
    public function orderTimeline(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = SmyleOrder::forBuyer($user->id)->findOrFail($id);

        $timeline = $order->timeline()->get()->map(function ($event) {
            return [
                'id' => $event->id,
                'event' => $event->event,
                'description' => $event->description,
                'actor_name' => $event->actor_name,
                'actor_role' => $event->actor_role,
                'metadata' => $event->metadata,
                'timestamp' => $event->timestamp->toIso8601String(),
            ];
        });

        return response()->json(['data' => $timeline]);
    }

    /**
     * POST /smyle/financing/apply - Submit financing application
     */
    public function applyFinancing(Request $request, int $orderId): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'down_payment' => 'required|numeric|min:0',
            'loan_term_months' => 'required|integer|in:12,24,36,48,60,72,84',
            'employment_status' => 'required|string|in:employed,self_employed,civil_servant,retired,student,unemployed',
            'monthly_income' => 'required|numeric|min:0',
            'monthly_expenses' => 'required|numeric|min:0',
        ]);

        $order = SmyleOrder::forBuyer($user->id)->findOrFail($orderId);

        $financing = $order->financing;
        if (!$financing) {
            return response()->json(['message' => 'No financing record found for this order'], 404);
        }

        $finCalc = $this->smyleService->calculateFinancing(
            (float) $order->vehicle_price,
            $validated['down_payment'],
            $validated['loan_term_months']
        );

        $financing->update([
            'status' => 'submitted',
            'down_payment' => $validated['down_payment'],
            'loan_amount' => $finCalc['loan_amount'],
            'monthly_payment' => $finCalc['monthly_payment'],
            'loan_term_months' => $validated['loan_term_months'],
            'total_cost' => $finCalc['total_cost'],
            'interest_rate' => $finCalc['interest_rate'],
            'effective_rate' => $finCalc['effective_rate'],
            'employment_status' => $validated['employment_status'],
            'monthly_income' => $validated['monthly_income'],
            'monthly_expenses' => $validated['monthly_expenses'],
            'submitted_at' => now(),
        ]);

        $order->addTimelineEvent(
            'financing_submitted',
            'Finanzierungsantrag bei Santander eingereicht',
            $user->id,
            $user->name,
            'buyer'
        );

        return response()->json([
            'message' => 'Financing application submitted successfully',
            'data' => $finCalc,
        ]);
    }

    // ============================================
    // FORMATTING HELPERS
    // ============================================

    private function formatSmyleVehicle(SmyleVehicle $sv, bool $detailed = false): array
    {
        $vehicle = $sv->vehicle;
        $storageUrl = rtrim(config('app.url'), '/') . '/storage';

        $data = [
            'id' => $sv->id,
            'vehicle_id' => $vehicle->id,
            'is_eligible' => $sv->is_eligible,
            'is_active' => $sv->is_active,
            'quality_checked' => $sv->quality_checked,
            'delivery_base_price' => (float) $sv->delivery_base_price,
            'location_postal_code' => $sv->location_postal_code,
            'location_city' => $sv->location_city,
            'smyle_highlights' => $sv->smyle_highlights,
            'included_services' => $sv->included_services ?? [
                'quality_check', 'registration', 'insurance', 'warranty',
                'roadside_assistance', 'delivery',
            ],
            'vehicle' => [
                'id' => $vehicle->id,
                'title' => $vehicle->title ?? ($vehicle->make->name . ' ' . $vehicle->model->name),
                'price' => (float) $vehicle->price,
                'year' => $vehicle->year,
                'mileage' => $vehicle->mileage,
                'fuel_type' => $vehicle->fuel_type,
                'transmission' => $vehicle->transmission,
                'body_type' => $vehicle->body_type,
                'power' => $vehicle->power,
                'color' => $vehicle->color,
                'condition' => $vehicle->condition,
                'make' => [
                    'id' => $vehicle->make->id ?? null,
                    'name' => $vehicle->make->name ?? '',
                    'logo' => $vehicle->make->logo ? $storageUrl . '/' . $vehicle->make->logo : null,
                ],
                'model' => [
                    'id' => $vehicle->model->id ?? null,
                    'name' => $vehicle->model->name ?? '',
                ],
                'images' => $vehicle->images->map(fn($img) => [
                    'id' => $img->id,
                    'url' => str_starts_with($img->image_path, 'http')
                        ? $img->image_path
                        : $storageUrl . '/' . $img->image_path,
                    'is_primary' => $img->is_primary,
                ])->values(),
            ],
            'quality_check' => $sv->latestQualityCheck ? [
                'status' => $sv->latestQualityCheck->status,
                'score' => $sv->latestQualityCheck->overall_score,
                'roadworthy' => $sv->latestQualityCheck->roadworthy,
                'inspection_date' => $sv->latestQualityCheck->inspection_date?->toDateString(),
                'valid_until' => $sv->latestQualityCheck->valid_until?->toDateString(),
            ] : null,
            'listed_at' => $sv->listed_at?->toIso8601String(),
        ];

        if ($detailed) {
            $data['quality_checks'] = $sv->qualityChecks->map(fn($qc) => [
                'id' => $qc->id,
                'reference' => $qc->reference,
                'status' => $qc->status,
                'overall_score' => $qc->overall_score,
                'exterior_check' => $qc->exterior_check,
                'interior_check' => $qc->interior_check,
                'engine_check' => $qc->engine_check,
                'electronics_check' => $qc->electronics_check,
                'tires_brakes_check' => $qc->tires_brakes_check,
                'roadworthy' => $qc->roadworthy,
                'inspector_notes' => $qc->inspector_notes,
                'issues_found' => $qc->issues_found,
                'inspection_date' => $qc->inspection_date?->toDateString(),
            ]);
        }

        return $data;
    }

    private function formatOrder(SmyleOrder $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'reference' => $order->reference,
            'buyer_id' => $order->buyer_id,
            'buyer_name' => $order->buyer?->name ?? 'Unknown',
            'vehicle_id' => $order->vehicle_id,
            'vehicle_title' => $order->vehicle_title,
            'vehicle_price' => (float) $order->vehicle_price,
            'delivery_cost' => (float) $order->delivery_cost,
            'service_fee' => (float) $order->service_fee,
            'registration_fee' => (float) $order->registration_fee,
            'total_amount' => (float) $order->total_amount,
            'deposit_amount' => (float) $order->deposit_amount,
            'remaining_amount' => (float) $order->remaining_amount,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'delivery_postal_code' => $order->delivery_postal_code,
            'delivery_city' => $order->delivery_city,
            'desired_license_plate' => $order->desired_license_plate,
            'estimated_delivery_date' => $order->estimated_delivery_date?->toDateString(),
            'actual_delivery_date' => $order->actual_delivery_date?->toDateString(),
            'can_return' => $order->can_return,
            'return_deadline' => $order->return_deadline?->toDateString(),
            'created_at' => $order->created_at->toIso8601String(),
            'updated_at' => $order->updated_at->toIso8601String(),
        ];

        // Include vehicle images
        if ($order->relationLoaded('vehicle') && $order->vehicle) {
            $storageUrl = rtrim(config('app.url'), '/') . '/storage';
            $data['vehicle_image'] = $order->vehicle->images->first()?->image_path
                ? (str_starts_with($order->vehicle->images->first()->image_path, 'http')
                    ? $order->vehicle->images->first()->image_path
                    : $storageUrl . '/' . $order->vehicle->images->first()->image_path)
                : null;
            $data['vehicle_make'] = $order->vehicle->make->name ?? '';
            $data['vehicle_model'] = $order->vehicle->model->name ?? '';
        }

        // Include related records
        if ($order->relationLoaded('delivery') && $order->delivery) {
            $data['delivery'] = [
                'status' => $order->delivery->status,
                'tracking_number' => $order->delivery->tracking_number,
                'driver_name' => $order->delivery->driver_name,
                'driver_phone' => $order->delivery->driver_phone,
                'scheduled_delivery_date' => $order->delivery->scheduled_delivery_date?->toDateString(),
                'delivery_time_slot' => $order->delivery->delivery_time_slot,
            ];
        }

        if ($order->relationLoaded('registration') && $order->registration) {
            $data['registration'] = [
                'status' => $order->registration->status,
                'desired_plate' => $order->registration->desired_plate,
                'assigned_plate' => $order->registration->assigned_plate,
                'documents_complete' => $order->registration->documents_complete,
            ];
        }

        if ($order->relationLoaded('insurance') && $order->insurance) {
            $data['insurance'] = [
                'status' => $order->insurance->status,
                'type' => $order->insurance->type,
                'policy_number' => $order->insurance->policy_number,
                'liability_included' => $order->insurance->liability_included,
                'comprehensive_included' => $order->insurance->comprehensive_included,
                'roadside_assistance' => $order->insurance->roadside_assistance,
            ];
        }

        if ($order->relationLoaded('warranty') && $order->warranty) {
            $data['warranty'] = [
                'status' => $order->warranty->status,
                'duration_months' => $order->warranty->duration_months,
                'start_date' => $order->warranty->start_date?->toDateString(),
                'end_date' => $order->warranty->end_date?->toDateString(),
                'roadside_assistance' => $order->warranty->roadside_assistance,
            ];
        }

        if ($order->relationLoaded('financing') && $order->financing) {
            $data['financing'] = [
                'status' => $order->financing->status,
                'bank_name' => $order->financing->bank_name,
                'loan_amount' => (float) $order->financing->loan_amount,
                'interest_rate' => (float) $order->financing->interest_rate,
                'monthly_payment' => (float) $order->financing->monthly_payment,
                'loan_term_months' => $order->financing->loan_term_months,
                'total_cost' => (float) ($order->financing->total_cost ?? 0),
                'down_payment' => (float) $order->financing->down_payment,
            ];
        }

        if ($detailed && $order->relationLoaded('timeline')) {
            $data['timeline'] = $order->timeline->map(fn($event) => [
                'id' => $event->id,
                'event' => $event->event,
                'description' => $event->description,
                'actor_name' => $event->actor_name,
                'actor_role' => $event->actor_role,
                'timestamp' => $event->timestamp->toIso8601String(),
            ]);
        }

        return $data;
    }
}
