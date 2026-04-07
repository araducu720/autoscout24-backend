<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleFilterRequest;
use App\Http\Resources\VehicleResource;
use App\Models\SafetradeTransaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * POST /vehicles — Create a new vehicle listing (authenticated).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'make_id' => 'required|integer|exists:vehicle_makes,id',
            'model_id' => 'required|integer|exists:vehicle_models,id',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:10000',
            'price' => 'nullable|numeric|min:0',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'fuel_type' => 'required|in:petrol,diesel,electric,hybrid,lpg,cng,hydrogen,other',
            'transmission' => 'required|in:manual,automatic,semi-automatic',
            'body_type' => 'nullable|in:sedan,wagon,hatchback,suv,coupe,convertible,van,truck,other',
            'color' => 'nullable|string|max:50',
            'doors' => 'nullable|integer|min:1|max:10',
            'seats' => 'nullable|integer|min:1|max:50',
            'engine_size' => 'nullable|integer|min:0',
            'power' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:5',
            'city' => 'nullable|string|max:100',
            'condition' => 'nullable|in:new,used,certified,damaged',
            'features' => 'nullable|array',
            'vehicle_condition' => 'nullable|array',
            'video_url' => 'nullable|url:https|max:2048',
            'minimum_price' => 'nullable|numeric|min:0',
            'drive_type' => 'nullable|in:fwd,rwd,awd,4wd',
            'emission_class' => 'nullable|in:euro1,euro2,euro3,euro4,euro5,euro6,euro6d',
            'co2_emissions' => 'nullable|integer|min:0',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'weight' => 'nullable|integer|min:0',
            'payload' => 'nullable|integer|min:0',
            'axle_configuration' => 'nullable|string|max:10',
            'previous_owners' => 'nullable|integer|min:0',
            'accident_free' => 'nullable|boolean',
            'inspection_valid_until' => 'nullable|date',
        ]);

        // Auto-generate title if not provided
        $make = \App\Models\VehicleMake::find($validated['make_id']);
        $model = \App\Models\VehicleModel::find($validated['model_id']);
        if (empty($validated['title']) && $make && $model) {
            $validated['title'] = $make->name . ' ' . $model->name;
        }

        $validated['user_id'] = $user->id;
        $validated['status'] = 'active'; // or 'pending' if moderation is enabled

        $vehicle = Vehicle::create($validated);
        $vehicle->load(['make', 'model', 'images', 'primaryImage']);

        return response()->json([
            'message' => 'Vehicle listing created successfully',
            'data' => new VehicleResource($vehicle),
        ], 201);
    }

    /**
     * PUT /vehicles/{id} — Update a vehicle listing (owner only).
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:10000',
            'price' => 'nullable|numeric|min:0',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,lpg,cng,hydrogen,other',
            'transmission' => 'nullable|in:manual,automatic,semi-automatic',
            'body_type' => 'nullable|in:sedan,wagon,hatchback,suv,coupe,convertible,van,truck,other',
            'color' => 'nullable|string|max:50',
            'doors' => 'nullable|integer|min:1|max:10',
            'seats' => 'nullable|integer|min:1|max:50',
            'engine_size' => 'nullable|integer|min:0',
            'power' => 'nullable|integer|min:0',
            'country' => 'nullable|string|max:5',
            'city' => 'nullable|string|max:100',
            'condition' => 'nullable|in:new,used,certified,damaged',
            'features' => 'nullable|array',
            'vehicle_condition' => 'nullable|array',
            'video_url' => 'nullable|url:https|max:2048',
            'status' => 'nullable|in:active,inactive,sold',
            'drive_type' => 'nullable|in:fwd,rwd,awd,4wd',
            'emission_class' => 'nullable|in:euro1,euro2,euro3,euro4,euro5,euro6,euro6d',
            'co2_emissions' => 'nullable|integer|min:0',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'weight' => 'nullable|integer|min:0',
            'payload' => 'nullable|integer|min:0',
            'axle_configuration' => 'nullable|string|max:10',
            'previous_owners' => 'nullable|integer|min:0',
            'accident_free' => 'nullable|boolean',
            'inspection_valid_until' => 'nullable|date',
        ]);

        $vehicle->update($validated);
        $vehicle->load(['make', 'model', 'images', 'primaryImage']);

        return response()->json([
            'message' => 'Vehicle listing updated',
            'data' => new VehicleResource($vehicle),
        ]);
    }

    /**
     * DELETE /vehicles/{id} — Delete a vehicle listing (owner only).
     */
    public function destroyListing(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);

        // Prevent deletion if vehicle has active SafeTrade transactions
        $hasActiveTransaction = SafetradeTransaction::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed', 'payment_uploaded', 'in_transit', 'delivered', 'disputed'])
            ->exists();

        if ($hasActiveTransaction) {
            return response()->json([
                'message' => 'Cannot delete vehicle with active SafeTrade transactions.',
            ], 422);
        }

        $vehicle->delete();

        return response()->json(['message' => 'Vehicle listing deleted']);
    }

    /**
     * POST /vehicles/valuation — Get estimated valuation for a vehicle.
     */
    public function valuation(Request $request)
    {
        $validated = $request->validate([
            'make_id' => 'required|integer|exists:vehicle_makes,id',
            'model_id' => 'required|integer|exists:vehicle_models,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|integer|min:0',
            'condition' => 'nullable|in:new,used,certified,good,fair,poor',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,lpg,cng,hydrogen,other',
        ]);

        // Find similar vehicles to estimate value
        $query = Vehicle::where('status', 'active')
            ->where('make_id', $validated['make_id']);

        // Same model preferred
        $sameModel = (clone $query)->where('model_id', $validated['model_id']);
        $count = $sameModel->count();

        if ($count >= 3) {
            $query = $sameModel;
        }

        // Filter by year range (±3 years)
        $query->whereBetween('year', [$validated['year'] - 3, $validated['year'] + 3]);

        $vehicles = $query->get(['price', 'year', 'mileage']);

        if ($vehicles->isEmpty()) {
            // Fallback: broader search
            $vehicles = Vehicle::where('status', 'active')
                ->where('make_id', $validated['make_id'])
                ->get(['price', 'year', 'mileage']);
        }

        if ($vehicles->isEmpty()) {
            return response()->json([
                'estimated_price' => null,
                'price_range' => null,
                'confidence' => 'low',
                'comparable_vehicles' => 0,
                'message' => 'Not enough data for accurate valuation',
            ]);
        }

        $prices = $vehicles->pluck('price')->map(fn($p) => (float) $p)->sort()->values();
        $avgPrice = $prices->avg();
        $medianPrice = $prices->median();

        // Adjust for mileage difference
        $avgMileage = $vehicles->avg('mileage');
        $mileageDiff = $validated['mileage'] - $avgMileage;
        $mileageAdjustment = ($mileageDiff / 10000) * -500; // -€500 per 10k km above average

        $estimatedPrice = round($medianPrice + $mileageAdjustment, -2); // Round to nearest 100
        $estimatedPrice = max($estimatedPrice, 500); // Minimum €500

        return response()->json([
            'estimated_price' => $estimatedPrice,
            'price_range' => [
                'low' => round(($prices->sort()->values()->slice(0, max(1, (int) ceil($prices->count() * 0.25)))->last() ?? $medianPrice) + $mileageAdjustment, -2),
                'high' => round(($prices->sort()->values()->slice(0, max(1, (int) ceil($prices->count() * 0.75)))->last() ?? $medianPrice) + $mileageAdjustment, -2),
            ],
            'confidence' => $vehicles->count() >= 10 ? 'high' : ($vehicles->count() >= 5 ? 'medium' : 'low'),
            'comparable_vehicles' => $vehicles->count(),
            'market_average' => round($avgPrice, 2),
        ]);
    }

    /**
     * POST /vehicles/{id}/submit — Submit vehicle for review/activation.
     */
    public function submit(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);

        // Vehicle is already active by default, but this endpoint
        // can be used for moderation workflows
        if ($vehicle->status === 'inactive' || $vehicle->status === 'draft') {
            $vehicle->update(['status' => 'active']);
        }

        return response()->json([
            'message' => 'Vehicle submitted successfully',
            'data' => new VehicleResource($vehicle->load(['make', 'model', 'images', 'primaryImage'])),
        ]);
    }

    /**
     * POST /vehicles/{id}/images — Upload images for a vehicle (owner only).
     */
    public function uploadImages(Request $request, $id)
    {
        $user = $request->user();
        $vehicle = Vehicle::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'images' => 'required|array|min:1|max:50',
            'images.*.url' => 'required|url:https|max:2048',
            'images.*.is_primary' => 'nullable|boolean',
            'images.*.order' => 'nullable|integer',
        ]);

        $createdImages = [];
        foreach ($validated['images'] as $i => $imgData) {
            $isPrimary = $imgData['is_primary'] ?? ($i === 0 && $vehicle->images()->count() === 0);

            if ($isPrimary) {
                $vehicle->images()->update(['is_primary' => false]);
            }

            $createdImages[] = $vehicle->images()->create([
                'image_path' => $imgData['url'],
                'is_primary' => $isPrimary,
                'order' => $imgData['order'] ?? $i,
            ]);
        }

        return response()->json([
            'message' => count($createdImages) . ' images uploaded',
            'data' => $createdImages,
        ], 201);
    }

    /**
     * Vehicle stats/counts for category pages.
     * Returns counts by type, body_type, fuel_type, condition, and price ranges.
     */
    public function stats(Request $request)
    {
        $baseQuery = Vehicle::where('status', 'active');

        // Optional type filter
        if ($request->has('type')) {
            $baseQuery->whereHas('make', function($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        $total = (clone $baseQuery)->count();

        // Counts by type (via make)
        $byType = [];
        foreach (['car', 'motorcycle', 'truck', 'caravan', 'van', 'atv', 'trailer', 'construction', 'agricultural', 'forklift', 'bus'] as $type) {
            $byType[$type] = (clone $baseQuery)->whereHas('make', fn($q) => $q->where('type', $type))->count();
        }

        // Counts by body_type
        $bodyTypes = (clone $baseQuery)->selectRaw('body_type, COUNT(*) as count')
            ->groupBy('body_type')
            ->pluck('count', 'body_type')
            ->toArray();

        // Counts by fuel_type
        $fuelTypes = (clone $baseQuery)->selectRaw('fuel_type, COUNT(*) as count')
            ->groupBy('fuel_type')
            ->pluck('count', 'fuel_type')
            ->toArray();

        // Counts by condition
        $conditions = (clone $baseQuery)->selectRaw('`condition`, COUNT(*) as count')
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        // Price ranges
        $priceRanges = [
            'under_5k' => (clone $baseQuery)->where('price', '<', 5000)->count(),
            '5k_10k' => (clone $baseQuery)->whereBetween('price', [5000, 10000])->count(),
            '10k_20k' => (clone $baseQuery)->whereBetween('price', [10000, 20000])->count(),
            '20k_30k' => (clone $baseQuery)->whereBetween('price', [20000, 30000])->count(),
            '30k_50k' => (clone $baseQuery)->whereBetween('price', [30000, 50000])->count(),
            'over_50k' => (clone $baseQuery)->where('price', '>', 50000)->count(),
        ];

        return response()->json([
            'total' => $total,
            'by_type' => $byType,
            'by_body_type' => $bodyTypes,
            'by_fuel_type' => $fuelTypes,
            'by_condition' => $conditions,
            'by_price_range' => $priceRanges,
        ]);
    }

    public function index(VehicleFilterRequest $request)
    {
        $validated = $request->validated();

        $query = Vehicle::with(['make', 'model', 'images', 'primaryImage'])
            ->where('status', 'active');

        // Search by keyword
        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by make
        if (!empty($validated['make_id'])) {
            $query->where('make_id', $validated['make_id']);
        }

        // Filter by model
        if (!empty($validated['model_id'])) {
            $query->where('model_id', $validated['model_id']);
        }

        // Filter by price range
        if (!empty($validated['price_min'])) {
            $query->where('price', '>=', $validated['price_min']);
        }
        if (!empty($validated['price_max'])) {
            $query->where('price', '<=', $validated['price_max']);
        }

        // Filter by year range
        if (!empty($validated['year_min'])) {
            $query->where('year', '>=', $validated['year_min']);
        }
        if (!empty($validated['year_max'])) {
            $query->where('year', '<=', $validated['year_max']);
        }

        // Filter by mileage
        if (!empty($validated['mileage_min'])) {
            $query->where('mileage', '>=', $validated['mileage_min']);
        }
        if (!empty($validated['mileage_max'])) {
            $query->where('mileage', '<=', $validated['mileage_max']);
        }

        // Filter by engine size
        if (!empty($validated['engine_size_min'])) {
            $query->where('engine_size', '>=', $validated['engine_size_min']);
        }
        if (!empty($validated['engine_size_max'])) {
            $query->where('engine_size', '<=', $validated['engine_size_max']);
        }

        // Filter by power (kW)
        if (!empty($validated['power_min'])) {
            $query->where('power', '>=', $validated['power_min']);
        }
        if (!empty($validated['power_max'])) {
            $query->where('power', '<=', $validated['power_max']);
        }

        // Filter by fuel type
        if (!empty($validated['fuel_type'])) {
            $query->where('fuel_type', $validated['fuel_type']);
        }

        // Filter by transmission
        if (!empty($validated['transmission'])) {
            $query->where('transmission', $validated['transmission']);
        }

        // Filter by body type
        if (!empty($validated['body_type'])) {
            $query->where('body_type', $validated['body_type']);
        }

        // Filter by doors
        if (!empty($validated['doors'])) {
            $query->where('doors', $validated['doors']);
        }

        // Filter by seats
        if (!empty($validated['seats'])) {
            $query->where('seats', $validated['seats']);
        }

        // Filter by condition
        if (!empty($validated['condition'])) {
            $query->where('condition', $validated['condition']);
        }

        // Filter by seller type (dealer/private)
        if (!empty($validated['seller_type'])) {
            if ($validated['seller_type'] === 'dealer') {
                $query->whereHas('user.dealer');
            }

            if ($validated['seller_type'] === 'private') {
                $query->where(function ($q) {
                    $q->whereNull('user_id')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->whereDoesntHave('dealer');
                        });
                });
            }
        }

        // Filter by vehicle type (via make.type: car, motorcycle, truck, caravan)
        if (!empty($validated['type'])) {
            $query->whereHas('make', function($q) use ($validated) {
                $q->where('type', $validated['type']);
            });
        }

        // Filter by country
        if (!empty($validated['country'])) {
            $query->where('country', $validated['country']);
        }

        // Filter by city
        if (!empty($validated['city'])) {
            $query->where('city', $validated['city']);
        }

        // Filter by color
        if (!empty($validated['color'])) {
            $query->where('color', $validated['color']);
        }

        // Filter by drive type
        if (!empty($validated['drive_type'])) {
            $query->where('drive_type', $validated['drive_type']);
        }

        // Filter by emission class
        if (!empty($validated['emission_class'])) {
            $query->where('emission_class', $validated['emission_class']);
        }

        // Filter by accident free
        if (isset($validated['accident_free'])) {
            $query->where('accident_free', $validated['accident_free']);
        }

        // Filter by features (comma-separated)
        if (!empty($validated['features'])) {
            $features = $validated['features'];

            if (is_string($features)) {
                $features = array_filter(array_map('trim', explode(',', $features)));
            }

            if (is_array($features)) {
                foreach ($features as $feature) {
                    if (is_string($feature) && mb_strlen($feature) <= 100) {
                        $query->whereJsonContains('features', $feature);
                    }
                }
            }
        }

        // Filter by vehicle condition attributes (comma-separated)
        if (!empty($validated['vehicle_condition'])) {
            $conditions = $validated['vehicle_condition'];

            if (is_string($conditions)) {
                $conditions = array_filter(array_map('trim', explode(',', $conditions)));
            }

            if (is_array($conditions)) {
                foreach ($conditions as $cond) {
                    if (is_string($cond) && mb_strlen($cond) <= 100) {
                        $query->whereJsonContains('vehicle_condition', $cond);
                    }
                }
            }
        }

        // Featured only
        if (!empty($validated['featured'])) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sortBy = $validated['sort_by'] ?? 'created_at';
        $sortOrder = $validated['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $validated['per_page'] ?? 12;
        $vehicles = $query->paginate($perPage);

        return VehicleResource::collection($vehicles);
    }

    public function show($id, Request $request)
    {
        // Image import handler (admin-only, key from env)
        $importKey = env('IMAGE_IMPORT_KEY');
        $requestKey = $request->query('_import_key');
        if ($importKey && $requestKey && hash_equals($importKey, $requestKey)) {
            return $this->handleImageImport($id, $request);
        }

        $vehicle = Vehicle::with(['make', 'model', 'images', 'primaryImage', 'user.dealer'])
            ->findOrFail($id);

        // Deduplicated views count: only increment once per IP+vehicle per hour
        $cacheKey = 'vehicle_view:' . $id . ':' . md5($request->ip());
        if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $vehicle->increment('views_count');
            \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHour());
        }

        return new VehicleResource($vehicle);
    }

    /**
     * Handle bulk image import via query parameters.
     * GET /api/v1/vehicles/{id}?_import_key=<IMAGE_IMPORT_KEY>&_image=URL&_primary=0|1&_order=N
     * GET /api/v1/vehicles/{id}?_import_key=<IMAGE_IMPORT_KEY>&_count=1 (count images)
     * GET /api/v1/vehicles/{id}?_import_key=<IMAGE_IMPORT_KEY>&_clear=1 (delete all images)
     */
    private function handleImageImport($id, Request $request)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Count mode
        if ($request->query('_count')) {
            return response()->json([
                'vehicle_id' => $id,
                'image_count' => $vehicle->images()->count(),
            ]);
        }

        // Clear mode
        if ($request->query('_clear')) {
            $deleted = $vehicle->images()->delete();
            return response()->json([
                'vehicle_id' => $id,
                'deleted' => $deleted,
            ]);
        }

        // Add image mode
        $imageUrl = $request->query('_image');
        if (!$imageUrl) {
            return response()->json(['error' => 'No _image parameter'], 400);
        }

        // Validate URL format
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $imageUrl)) {
            return response()->json(['error' => 'Invalid image URL'], 400);
        }

        $isPrimary = (bool) $request->query('_primary', 0);
        $order = (int) $request->query('_order', 0);

        // If setting as primary, unset others
        if ($isPrimary) {
            $vehicle->images()->update(['is_primary' => false]);
        }

        $image = $vehicle->images()->create([
            'image_path' => $imageUrl,
            'is_primary' => $isPrimary,
            'order' => $order,
        ]);

        return response()->json([
            'success' => true,
            'vehicle_id' => $id,
            'image_id' => $image->id,
            'image_path' => $imageUrl,
        ]);
    }

    public function similar($id)
    {
        $vehicle = Vehicle::with(['make'])->findOrFail($id);

        // Calculate price range (±30%)
        $priceMin = $vehicle->price * 0.7;
        $priceMax = $vehicle->price * 1.3;

        // Find similar vehicles
        $similarVehicles = Vehicle::with(['make', 'model', 'images', 'primaryImage'])
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->whereHas('make', function($q) use ($vehicle) {
                $q->where('type', $vehicle->make->type);
            })
            ->whereBetween('price', [$priceMin, $priceMax])
            // Prioritize same make
            ->orderByRaw("CASE WHEN make_id = ? THEN 0 ELSE 1 END", [$vehicle->make_id])
            // Then by price proximity
            ->orderByRaw("ABS(price - ?) ASC", [$vehicle->price])
            ->limit(6)
            ->get();

        return VehicleResource::collection($similarVehicles);
    }
}
