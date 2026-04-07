<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteListController extends Controller
{
    /**
     * Get all favorite lists for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        try {
            $lists = DB::table('favorite_lists')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Count favorites in each list
            $lists = $lists->map(function ($list) {
                $list->favorites_count = DB::table('favorite_list_items')
                    ->where('favorite_list_id', $list->id)
                    ->count();
                return $list;
            });

            return response()->json(['data' => $lists]);
        } catch (\Exception $e) {
            // If favorite_lists table doesn't exist yet, return empty array
            return response()->json(['data' => []]);
        }
    }

    /**
     * Create a new favorite list
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        try {
            $listId = DB::table('favorite_lists')->insertGetId([
                'user_id' => $request->user()->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_public' => $validated['is_public'] ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $list = DB::table('favorite_lists')->find($listId);

            return response()->json([
                'message' => 'Favorite list created successfully',
                'data' => $list,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create favorite list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a vehicle to a specific favorite list
     */
    public function addItem(Request $request, int $listId): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|integer|exists:vehicles,id',
        ]);

        $user = $request->user();

        // Verify list ownership
        $list = DB::table('favorite_lists')
            ->where('id', $listId)
            ->where('user_id', $user->id)
            ->first();

        if (!$list) {
            return response()->json(['message' => 'Favorite list not found'], 404);
        }

        try {
            // Check if already in list
            $exists = DB::table('favorite_list_items')
                ->where('favorite_list_id', $listId)
                ->where('vehicle_id', $validated['vehicle_id'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Vehicle already in this list'], 409);
            }

            DB::table('favorite_list_items')->insert([
                'favorite_list_id' => $listId,
                'vehicle_id' => $validated['vehicle_id'],
                'created_at' => now(),
            ]);

            return response()->json(['message' => 'Vehicle added to list successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add vehicle to list',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a vehicle from a specific favorite list
     */
    public function removeItem(Request $request, int $listId, int $vehicleId): JsonResponse
    {
        $user = $request->user();

        // Verify list ownership
        $list = DB::table('favorite_lists')
            ->where('id', $listId)
            ->where('user_id', $user->id)
            ->first();

        if (!$list) {
            return response()->json(['message' => 'Favorite list not found'], 404);
        }

        DB::table('favorite_list_items')
            ->where('favorite_list_id', $listId)
            ->where('vehicle_id', $vehicleId)
            ->delete();

        return response()->json(['message' => 'Vehicle removed from list successfully']);
    }
}
