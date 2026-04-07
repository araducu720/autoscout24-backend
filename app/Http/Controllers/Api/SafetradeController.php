<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SafetradeTransaction;
use App\Models\EscrowTransaction;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Vehicle;
use App\Events\SafetradeTransactionCreated;
use App\Events\SafetradeDeliveryConfirmed;
use App\Notifications\TransactionStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SafetradeController extends Controller
{
    /**
     * GET /transactions — list SafeTrade transactions for authenticated user (as buyer or seller)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = SafetradeTransaction::with(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'escrow', 'order'])
            ->forUser($user->id)
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $transactions = $query->paginate($request->query('per_page', 20));

        // Transform to match frontend Transaction type
        $transactions->getCollection()->transform(function ($txn) {
            return $this->formatTransaction($txn);
        });

        return response()->json([
            'data' => $transactions->items(),
            'links' => [
                'first' => $transactions->url(1),
                'last' => $transactions->url($transactions->lastPage()),
                'prev' => $transactions->previousPageUrl(),
                'next' => $transactions->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'from' => $transactions->firstItem(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'to' => $transactions->lastItem(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * GET /transactions/{id} — single transaction detail
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'escrow', 'order', 'invoice', 'timeline'])
            ->forUser($user->id)
            ->findOrFail($id);

        return response()->json([
            'data' => $this->formatTransaction($txn),
        ]);
    }

    /**
     * GET /transactions/{id}/details — full transaction with invoice, escrow, timeline
     */
    public function details(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'escrow', 'order', 'invoice', 'timeline'])
            ->forUser($user->id)
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'transaction' => $this->formatTransaction($txn),
                'escrow' => $txn->escrow ? [
                    'id' => $txn->escrow->id,
                    'transaction_id' => $txn->id,
                    'amount' => (float) $txn->escrow->amount,
                    'status' => $txn->escrow->status,
                    'buyer_id' => $txn->escrow->buyer_id,
                    'seller_id' => $txn->escrow->seller_id,
                    'release_conditions' => $txn->escrow->release_conditions ?? [
                        'buyer_confirmed' => false,
                        'seller_confirmed' => false,
                        'payment_verified' => $txn->escrow->status === 'funded',
                    ],
                    'dispute_reason' => $txn->escrow->dispute_reason,
                    'dispute_evidence' => $txn->escrow->dispute_evidence ?? [],
                    'created_at' => $txn->escrow->created_at->toIso8601String(),
                    'updated_at' => $txn->escrow->updated_at->toIso8601String(),
                ] : null,
                'invoice' => $txn->invoice ? [
                    'id' => $txn->invoice->id,
                    'transaction_id' => $txn->id,
                    'invoice_number' => $txn->invoice->invoice_number,
                    'issue_date' => $txn->invoice->issue_date->format('Y-m-d'),
                    'due_date' => $txn->invoice->due_date?->format('Y-m-d'),
                    'amount' => (float) $txn->invoice->amount,
                    'status' => $txn->invoice->status,
                    'notes' => $txn->invoice->notes,
                    'created_at' => $txn->invoice->created_at->toIso8601String(),
                    'updated_at' => $txn->invoice->updated_at->toIso8601String(),
                ] : null,
                'timeline' => $txn->timeline->map(function ($entry) {
                    return [
                        'id' => $entry->id,
                        'transaction_id' => $entry->safetrade_transaction_id,
                        'event' => $entry->event,
                        'description' => $entry->description,
                        'actor_id' => $entry->actor_id,
                        'actor_name' => $entry->actor_name,
                        'timestamp' => $entry->timestamp->toIso8601String(),
                    ];
                }),
            ],
        ]);
    }

    /**
     * POST /transactions — create SafeTrade transaction (buyer initiates purchase)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'message' => 'nullable|string|max:1000',
            'delivery_method' => 'required|in:pickup,delivery,shipping',
            'delivery_address' => 'nullable|required_if:delivery_method,delivery,shipping|string|max:500',
        ]);

        $user = $request->user();
        $vehicle = Vehicle::with(['user', 'make', 'model'])->findOrFail($validated['vehicle_id']);

        // Can't buy your own vehicle
        if ($vehicle->user_id === $user->id) {
            return response()->json(['message' => 'You cannot purchase your own vehicle'], 422);
        }

        // Vehicle must have a valid seller/owner
        if (empty($vehicle->user_id)) {
            return response()->json(['message' => 'This vehicle does not have a registered seller and cannot be purchased through SafeTrade.'], 422);
        }

        // Check vehicle is active
        if ($vehicle->status !== 'active') {
            return response()->json(['message' => 'This vehicle is no longer available'], 422);
        }

        try {
            $vehiclePrice = (float) $vehicle->price;
            $feePercent = app(\App\Services\SettingsService::class)->escrowFeePercent();
            $escrowFee = round($vehiclePrice * ($feePercent / 100), 2);
            $totalAmount = $vehiclePrice + $escrowFee;

        $vehicleTitle = $vehicle->title ?? ($vehicle->make->name . ' ' . $vehicle->model->name . ' ' . $vehicle->year);

        $txn = \Illuminate\Support\Facades\DB::transaction(function () use ($user, $vehicle, $validated, $vehiclePrice, $escrowFee, $totalAmount, $vehicleTitle) {
            // Create Order
            $order = Order::create([
                'buyer_id' => $user->id,
                'seller_id' => $vehicle->user_id,
                'vehicle_id' => $vehicle->id,
                'total_price' => $totalAmount,
                'escrow_fee' => $escrowFee,
                'status' => 'pending',
                'delivery_method' => $validated['delivery_method'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'message' => $validated['message'] ?? null,
            ]);

            // Auto-accept order (SafeTrade flow — immediate)
            $order->accept();

            // Create SafeTrade Transaction
            $txn = SafetradeTransaction::create([
                'order_id' => $order->id,
                'buyer_id' => $user->id,
                'seller_id' => $vehicle->user_id,
                'vehicle_id' => $vehicle->id,
                'vehicle_title' => $vehicleTitle,
                'vehicle_price' => $vehiclePrice,
                'amount' => $totalAmount,
                'escrow_fee' => $escrowFee,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending',
                'status' => 'pending',
                'escrow_status' => 'pending',
                'delivery_method' => $validated['delivery_method'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'notes' => $validated['message'] ?? null,
            ]);

            // Create Escrow record
            EscrowTransaction::create([
                'safetrade_transaction_id' => $txn->id,
                'buyer_id' => $user->id,
                'seller_id' => $vehicle->user_id,
                'amount' => $totalAmount,
                'status' => 'pending',
                'release_conditions' => [
                    'buyer_confirmed' => false,
                    'seller_confirmed' => false,
                    'payment_verified' => false,
                ],
                'payment_reference' => $txn->reference,
            ]);

            // Create Invoice
            Invoice::create([
                'safetrade_transaction_id' => $txn->id,
                'issue_date' => now(),
                'due_date' => now()->addDays(3),
                'amount' => $vehiclePrice,
                'escrow_fee' => $escrowFee,
                'total' => $totalAmount,
                'status' => 'issued',
            ]);

            // Timeline
            $txn->addTimelineEvent(
                'transaction_created',
                "SafeTrade transaction created for {$vehicleTitle}",
                $user->id,
                $user->name,
                'buyer',
                ['vehicle_id' => $vehicle->id, 'amount' => $totalAmount]
            );

            return $txn;
        });

        $txn->load(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'escrow', 'order']);

        // Fire event for notifications (non-blocking)
        try {
            event(new SafetradeTransactionCreated($txn));
        } catch (\Exception $e) {
            \Log::warning('Failed to fire SafetradeTransactionCreated event: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $this->formatTransaction($txn),
        ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('SafeTrade transaction DB error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'vehicle_id' => $validated['vehicle_id'],
                'sql_code' => $e->getCode(),
            ]);
            return response()->json([
                'message' => 'Failed to create transaction. Database error — please ensure all migrations have been run.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        } catch (\Exception $e) {
            \Log::error('SafeTrade transaction error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'vehicle_id' => $validated['vehicle_id'],
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'message' => 'Failed to create transaction. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * PUT /transactions/{id}/status — update transaction status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'status' => 'required|in:confirmed,in_transit,delivered,completed,cancelled',
        ]);

        $txn = SafetradeTransaction::forUser($user->id)->findOrFail($id);

        $isBuyer = $txn->buyer_id === $user->id;
        $isSeller = $txn->seller_id === $user->id;

        // Valid status transitions
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['in_transit', 'cancelled'],
            'in_transit' => ['delivered', 'cancelled'],
            'delivered' => ['completed'],
        ];

        $currentStatus = $txn->status;
        $newStatus = $validated['status'];

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            return response()->json([
                'message' => "Cannot transition from '{$currentStatus}' to '{$newStatus}'.",
            ], 422);
        }

        // Role-based status validation
        $sellerOnlyStatuses = ['confirmed', 'in_transit'];
        $buyerOnlyStatuses = ['delivered'];

        if ($isBuyer && in_array($validated['status'], $sellerOnlyStatuses)) {
            return response()->json(['message' => 'Only the seller can set this status'], 403);
        }

        if ($isSeller && in_array($validated['status'], $buyerOnlyStatuses)) {
            return response()->json(['message' => 'Only the buyer can confirm delivery'], 403);
        }

        $txn->update(['status' => $validated['status']]);

        // Update timestamps based on status
        if ($validated['status'] === 'delivered') {
            $txn->update(['delivered_at' => now()]);
        } elseif ($validated['status'] === 'confirmed') {
            $txn->update(['confirmed_at' => now()]);
        }

        $txn->addTimelineEvent(
            'status_updated',
            "Transaction status updated to {$validated['status']}",
            $user->id,
            $user->name,
            $isBuyer ? 'buyer' : 'seller'
        );

        // Notify the other party about status change
        $otherPartyId = $txn->buyer_id === $user->id ? $txn->seller_id : $txn->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new TransactionStatusNotification($txn, $validated['status']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send transaction status notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Status updated']);
    }

    /**
     * PUT /transactions/{id}/tracking — add tracking number
     */
    public function updateTracking(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:200',
        ]);

        $txn = SafetradeTransaction::forUser($user->id)->findOrFail($id);

        // Only the seller can add tracking numbers
        if ($txn->seller_id !== $user->id) {
            return response()->json(['message' => 'Only the seller can add tracking numbers'], 403);
        }

        $txn->update([
            'tracking_number' => $validated['tracking_number'],
            'status' => 'in_transit',
        ]);

        $txn->addTimelineEvent(
            'tracking_added',
            "Tracking number added: {$validated['tracking_number']}",
            $user->id,
            $user->name,
            'seller'
        );

        // Notify the buyer about tracking number
        $buyer = User::find($txn->buyer_id);
        if ($buyer) {
            try {
                $buyer->notify(new TransactionStatusNotification($txn, 'tracking_added', null, $validated['tracking_number']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send tracking notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Tracking updated']);
    }

    /**
     * POST /transactions/{id}/confirm-delivery — buyer confirms vehicle received
     */
    public function confirmDelivery(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with('escrow')->findOrFail($id);

        if ($txn->buyer_id !== $user->id) {
            return response()->json(['message' => 'Only the buyer can confirm delivery'], 403);
        }

        $txn->confirmReceipt();

        $txn->addTimelineEvent(
            'delivery_confirmed',
            'Buyer confirmed vehicle receipt. Funds released to seller.',
            $user->id,
            $user->name,
            'buyer'
        );

        // Fire event for notifications
        event(new SafetradeDeliveryConfirmed($txn));

        return response()->json(['message' => 'Delivery confirmed. Funds released to seller.']);
    }

    /**
     * POST /transactions/{id}/complete — complete transaction
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with('escrow')->forUser($user->id)->findOrFail($id);

        // Only the buyer can complete a transaction (releases funds to seller)
        if ($txn->buyer_id !== $user->id) {
            return response()->json(['message' => 'Only the buyer can complete the transaction'], 403);
        }

        if (!in_array($txn->status, ['delivered', 'confirmed'])) {
            return response()->json(['message' => 'Transaction must be in delivered or confirmed state'], 422);
        }

        $txn->releaseFunds();

        $txn->addTimelineEvent(
            'transaction_completed',
            'Transaction completed successfully',
            $user->id,
            $user->name,
            $txn->buyer_id === $user->id ? 'buyer' : 'seller'
        );

        // Notify both parties about completion
        foreach ([$txn->buyer_id, $txn->seller_id] as $partyId) {
            if ($partyId === $user->id) continue;
            $party = User::find($partyId);
            if ($party) {
                try {
                    $party->notify(new TransactionStatusNotification($txn, 'completed'));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send transaction completed notification: ' . $e->getMessage());
                }
            }
        }

        // Mark invoice as paid
        if ($txn->invoice) {
            $txn->invoice->markPaid();
        }

        return response()->json(['message' => 'Transaction completed']);
    }

    /**
     * POST /transactions/{id}/cancel — cancel transaction
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $txn = SafetradeTransaction::with('escrow')->forUser($user->id)->findOrFail($id);

        if (!$txn->cancelTransaction($validated['reason'])) {
            return response()->json(['message' => 'Cannot cancel this transaction'], 422);
        }

        $txn->addTimelineEvent(
            'transaction_cancelled',
            "Transaction cancelled: {$validated['reason']}",
            $user->id,
            $user->name,
            $txn->buyer_id === $user->id ? 'buyer' : 'seller'
        );

        // Notify the other party about cancellation
        $otherPartyId = $txn->buyer_id === $user->id ? $txn->seller_id : $txn->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new TransactionStatusNotification($txn, 'cancelled', $validated['reason']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send transaction cancelled notification: ' . $e->getMessage());
            }
        }

        // Cancel invoice
        if ($txn->invoice) {
            $txn->invoice->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'Transaction cancelled']);
    }

    /**
     * Transform SafetradeTransaction to match frontend Transaction type
     */
    private function formatTransaction(SafetradeTransaction $txn): array
    {
        return [
            'id' => $txn->id,
            'order_id' => $txn->order_id,
            'buyer_id' => $txn->buyer_id,
            'seller_id' => $txn->seller_id,
            'vehicle_id' => $txn->vehicle_id,
            'buyer_name' => $txn->buyer?->name ?? 'Unknown',
            'seller_name' => $txn->seller?->name ?? 'Unknown',
            'vehicle_title' => $txn->vehicle_title,
            'vehicle_price' => (float) $txn->vehicle_price,
            'status' => $txn->status,
            'payment_method' => $txn->payment_method,
            'payment_status' => $txn->payment_status,
            'escrow_status' => $txn->escrow_status,
            'delivery_method' => $txn->delivery_method,
            'delivery_address' => $txn->delivery_address,
            'tracking_number' => $txn->tracking_number,
            'notes' => $txn->notes,
            'reference' => $txn->reference,
            'amount' => (float) $txn->amount,
            'escrow_fee' => (float) $txn->escrow_fee,
            'payment_proof_url' => $txn->payment_proof_path ? Storage::url($txn->payment_proof_path) : null,
            'created_at' => $txn->created_at->toIso8601String(),
            'updated_at' => $txn->updated_at->toIso8601String(),
            'completed_at' => $txn->completed_at?->toIso8601String(),
        ];
    }
}
