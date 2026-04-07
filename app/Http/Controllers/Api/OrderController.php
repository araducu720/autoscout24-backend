<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    /**
     * GET /orders — list orders for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = Order::with(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'safetradeTransaction'])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate($request->query('per_page', 20));

        $orders->getCollection()->transform(function ($order) {
            return $this->formatOrder($order);
        });

        return response()->json($orders);
    }

    /**
     * GET /orders/{id} — single order
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $order = Order::with(['buyer', 'seller', 'vehicle.make', 'vehicle.model', 'safetradeTransaction'])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->findOrFail($id);

        return response()->json([
            'data' => $this->formatOrder($order),
        ]);
    }

    /**
     * POST /orders — create an order (standalone, without SafeTrade flow)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'quantity' => 'nullable|integer|min:1|max:1',
            'message' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        $vehicle = \App\Models\Vehicle::with('user')->findOrFail($validated['vehicle_id']);

        if (!$vehicle->user_id) {
            return response()->json(['message' => 'This vehicle is not available for direct orders'], 422);
        }

        if ($vehicle->user_id === $user->id) {
            return response()->json(['message' => 'You cannot order your own vehicle'], 422);
        }

        $order = Order::create([
            'buyer_id' => $user->id,
            'seller_id' => $vehicle->user_id,
            'vehicle_id' => $vehicle->id,
            'total_price' => $vehicle->price,
            'status' => 'pending',
            'delivery_method' => 'pickup',
            'message' => $validated['message'] ?? null,
        ]);

        // Notify the seller about the new order
        $seller = User::find($vehicle->user_id);
        if ($seller) {
            try {
                $seller->notify(new OrderStatusNotification($order->load(['vehicle.make', 'vehicle.model', 'buyer']), 'new'));
            } catch (\Exception $e) {
                \Log::warning('Failed to send new order notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Order created',
            'data' => $this->formatOrder($order->load(['buyer', 'seller', 'vehicle.make', 'vehicle.model'])),
        ], 201);
    }

    /**
     * POST /orders/{id}/accept — seller accepts order
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::findOrFail($id);

        if ($order->seller_id !== $user->id) {
            return response()->json(['message' => 'Only the seller can accept this order'], 403);
        }

        if (!$order->accept()) {
            return response()->json(['message' => 'Cannot accept this order'], 422);
        }

        // Notify the buyer that order was accepted
        $buyer = User::find($order->buyer_id);
        if ($buyer) {
            try {
                $buyer->notify(new OrderStatusNotification($order->load(['vehicle.make', 'vehicle.model']), 'accepted'));
            } catch (\Exception $e) {
                \Log::warning('Failed to send order accepted notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Order accepted']);
    }

    /**
     * POST /orders/{id}/reject — seller rejects order
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::findOrFail($id);

        if ($order->seller_id !== $user->id) {
            return response()->json(['message' => 'Only the seller can reject this order'], 403);
        }

        if (!$order->reject($validated['reason'])) {
            return response()->json(['message' => 'Cannot reject this order'], 422);
        }

        // Notify the buyer that order was rejected
        $buyer = User::find($order->buyer_id);
        if ($buyer) {
            try {
                $buyer->notify(new OrderStatusNotification($order->load(['vehicle.make', 'vehicle.model']), 'rejected', $validated['reason']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send order rejected notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Order rejected']);
    }

    /**
     * POST /orders/{id}/cancel — cancel order
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $order = Order::findOrFail($id);

        if ($order->buyer_id !== $user->id && $order->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$order->cancel()) {
            return response()->json(['message' => 'Cannot cancel this order'], 422);
        }

        // Notify the other party about cancellation
        $otherPartyId = $order->buyer_id === $user->id ? $order->seller_id : $order->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new OrderStatusNotification($order->load(['vehicle.make', 'vehicle.model']), 'cancelled'));
            } catch (\Exception $e) {
                \Log::warning('Failed to send order cancelled notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Order cancelled']);
    }

    /**
     * Format order for frontend
     */
    private function formatOrder(Order $order): array
    {
        $vehicleTitle = $order->vehicle
            ? ($order->vehicle->title ?? ($order->vehicle->make->name . ' ' . $order->vehicle->model->name))
            : 'Unknown Vehicle';

        return [
            'id' => $order->id,
            'transaction_id' => $order->safetradeTransaction?->id,
            'buyer_id' => $order->buyer_id,
            'seller_id' => $order->seller_id,
            'vehicle_id' => $order->vehicle_id,
            'quantity' => 1,
            'total_price' => (float) $order->total_price,
            'status' => $order->status,
            'payment_deadline' => $order->payment_deadline?->toIso8601String(),
            'message' => $order->message,
            'buyer_name' => $order->buyer?->name ?? 'Unknown',
            'seller_name' => $order->seller?->name ?? 'Unknown',
            'vehicle_title' => $vehicleTitle,
            'created_at' => $order->created_at->toIso8601String(),
            'updated_at' => $order->updated_at->toIso8601String(),
        ];
    }
}
