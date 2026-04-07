<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $vehicles = Auth::user()
            ->favoriteVehicles()
            ->with(['make', 'model', 'images', 'primaryImage'])
            ->where('status', 'active')
            ->get();

        return VehicleResource::collection($vehicles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        // Check if favorite already exists
        $exists = Favorite::where('user_id', Auth::id())
            ->where('vehicle_id', $request->vehicle_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Vehicle is already in favorites',
                'errors' => [
                    'vehicle_id' => ['This vehicle is already in your favorites.'],
                ],
            ], 422);
        }

        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $request->vehicle_id,
        ]);

        return response()->json([
            'message' => 'Vehicle added to favorites',
            'favorite' => $favorite,
        ], 201);
    }

    public function destroy($vehicleId)
    {
        $deleted = Favorite::where('user_id', Auth::id())
            ->where('vehicle_id', $vehicleId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Vehicle removed from favorites',
            ]);
        }

        return response()->json([
            'message' => 'Favorite not found',
        ], 404);
    }
}
