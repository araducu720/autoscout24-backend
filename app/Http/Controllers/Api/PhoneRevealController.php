<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhoneReveal;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PhoneRevealController extends Controller
{
    public function reveal(Request $request, $vehicleId)
    {
        // Per-user/IP + vehicle rate limit: max 5 reveals per vehicle per user (or IP) per hour
        $identifier = auth()->id() ? 'user:' . auth()->id() : 'ip:' . md5($request->ip());
        $cacheKey = 'phone_reveal:' . $vehicleId . ':' . $identifier;
        $attempts = (int) Cache::get($cacheKey, 0);

        if ($attempts >= 5) {
            return response()->json([
                'message' => 'Too many phone reveal requests. Please try again later.',
            ], 429);
        }

        Cache::put($cacheKey, $attempts + 1, now()->addHour());

        $vehicle = Vehicle::with(['user.dealer'])->findOrFail($vehicleId);

        // Try user phone first, then dealer phone
        $phone = null;
        $sellerName = null;

        if ($vehicle->user) {
            $sellerName = $vehicle->user->name;

            if ($vehicle->user->phone) {
                $phone = $vehicle->user->phone;
            } elseif ($vehicle->user->dealer && $vehicle->user->dealer->phone) {
                $phone = $vehicle->user->dealer->phone;
                $sellerName = $vehicle->user->dealer->company_name;
            }
        }

        if (!$phone) {
            return response()->json([
                'message' => 'Phone number not available for this vehicle',
                'phone' => null,
            ], 200);
        }

        // Track the phone reveal
        PhoneReveal::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => auth()->id(), // null if guest
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'phone' => $phone,
            'seller_name' => $sellerName,
        ]);
    }
}
