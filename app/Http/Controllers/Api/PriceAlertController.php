<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceAlertController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $alerts = PriceAlert::where('user_id', $request->user()->id)
            ->with(['vehicle:id,title,price,make_id,model_id', 'vehicle.make:id,name', 'vehicle.model:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($alerts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required_without:saved_search_id|nullable|exists:vehicles,id',
            'saved_search_id' => 'required_without:vehicle_id|nullable|exists:saved_searches,id',
            'target_price' => 'required|numeric|min:0',
            'alert_type' => 'required|in:below,above,change,drop_percent',
            'notify_email' => 'boolean',
            'notify_push' => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        if (isset($validated['vehicle_id'])) {
            $vehicle = \App\Models\Vehicle::find($validated['vehicle_id']);
            $validated['current_price'] = $vehicle?->price;
        }

        $alert = PriceAlert::create($validated);

        return response()->json([
            'message' => 'Price alert created successfully',
            'data' => $alert->load('vehicle'),
        ], 201);
    }

    public function update(Request $request, PriceAlert $alert): JsonResponse
    {
        if ($alert->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'target_price' => 'numeric|min:0',
            'alert_type' => 'in:below,above,change,drop_percent',
            'is_active' => 'boolean',
            'notify_email' => 'boolean',
            'notify_push' => 'boolean',
        ]);

        $alert->update($validated);

        return response()->json([
            'message' => 'Price alert updated',
            'data' => $alert,
        ]);
    }

    public function destroy(Request $request, PriceAlert $alert): JsonResponse
    {
        if ($alert->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $alert->delete();

        return response()->json(['message' => 'Price alert deleted']);
    }
}
