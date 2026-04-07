<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealerController extends Controller
{
    /**
     * Get dealer profile for authenticated user
     */
    public function profile(): JsonResponse
    {
        $dealer = Dealer::where('user_id', Auth::id())->first();

        if (!$dealer) {
            return response()->json([
                'message' => 'Dealer profile not found',
                'is_dealer' => false,
            ], 404);
        }

        return response()->json([
            'data' => $dealer,
            'is_dealer' => true,
        ]);
    }

    /**
     * Register as dealer
     */
    public function register(Request $request): JsonResponse
    {
        // Check if user is already a dealer
        if (Dealer::where('user_id', Auth::id())->exists()) {
            return response()->json([
                'message' => 'You are already registered as a dealer',
            ], 422);
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:independent,franchise,manufacturer',
            'offers_home_delivery' => 'boolean',
            'offers_financing' => 'boolean',
            'offers_warranty' => 'boolean',
        ]);

        $dealer = Dealer::create([
            ...$validated,
            'user_id' => Auth::id(),
            'is_verified' => false,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Dealer registration submitted. Awaiting verification.',
            'data' => $dealer,
        ], 201);
    }

    /**
     * Update dealer profile
     */
    public function update(Request $request): JsonResponse
    {
        $dealer = Dealer::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:2000',
            'offers_home_delivery' => 'boolean',
            'offers_financing' => 'boolean',
            'offers_warranty' => 'boolean',
        ]);

        $dealer->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $dealer->fresh(),
        ]);
    }

    /**
     * Get dealer statistics
     */
    public function statistics(): JsonResponse
    {
        $dealer = Dealer::where('user_id', Auth::id())->firstOrFail();

        $stats = [
            'total_purchases' => $dealer->total_purchases ?? 0,
            'is_verified' => $dealer->is_verified,
            'is_active' => $dealer->is_active,
        ];

        return response()->json(['data' => $stats]);
    }
}
