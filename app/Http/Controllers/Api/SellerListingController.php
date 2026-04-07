<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerListingController extends Controller
{
    /**
     * Resolve the user_id from a seller/dealer ID.
     * The frontend sends dealer.id, but vehicles are owned by user_id.
     */
    private function resolveUserId(int $sellerId): int
    {
        $dealer = Dealer::find($sellerId);
        return $dealer ? $dealer->user_id : $sellerId;
    }

    /**
     * Get the authenticated user's own listings (My Vehicles / Meine Inserate)
     */
    public function myVehicles(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->get('status'); // optional filter: active, sold, inactive

        $query = Vehicle::where('user_id', $user->id)
            ->with(['make', 'model', 'images', 'primaryImage']);

        if ($status) {
            $query->where('status', $status);
        }

        $vehicles = $query->orderByRaw("FIELD(status, 'active', 'inactive', 'sold')")
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    /**
     * Get the authenticated user's vehicle inventory stats
     */
    public function myVehicleStats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $stats = Vehicle::where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total_listings,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_listings,
                SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_listings,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_listings,
                COALESCE(SUM(views_count), 0) as total_views
            ")
            ->first();

        return response()->json([
            'data' => [
                'total_listings' => (int) $stats->total_listings,
                'active_listings' => (int) $stats->active_listings,
                'sold_listings' => (int) $stats->sold_listings,
                'featured_listings' => (int) $stats->featured_listings,
                'total_views' => (int) $stats->total_views,
            ],
        ]);
    }

    /**
     * Get seller's listings
     */
    public function index(Request $request, int $sellerId): JsonResponse
    {
        $userId = $this->resolveUserId($sellerId);

        $vehicles = Vehicle::where('user_id', $userId)
            ->where('status', 'active')
            ->with(['make', 'model', 'images', 'primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $vehicles->items(),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'per_page' => $vehicles->perPage(),
                'total' => $vehicles->total(),
            ],
        ]);
    }

    /**
     * Get seller's inventory stats
     */
    public function inventoryStats(Request $request, int $sellerId): JsonResponse
    {
        $userId = $this->resolveUserId($sellerId);

        $stats = Vehicle::where('user_id', $userId)
            ->selectRaw("
                COUNT(*) as total_listings,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_listings,
                SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_listings,
                SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_listings
            ")
            ->first();

        $inventory = Vehicle::where('user_id', $userId)
            ->where('status', 'active')
            ->with(['make', 'model', 'primaryImage'])
            ->take(50)
            ->get();

        return response()->json([
            'data' => [
                'id' => $sellerId,
                'seller_id' => $sellerId,
                'total_listings' => (int) $stats->total_listings,
                'active_listings' => (int) $stats->active_listings,
                'sold_listings' => (int) $stats->sold_listings,
                'featured_listings' => (int) $stats->featured_listings,
                'inventory' => $inventory,
            ],
        ]);
    }

    /**
     * Get listing analytics for a specific vehicle
     */
    public function analytics(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::with(['make', 'model'])->findOrFail($vehicleId);

        // Ensure the user owns this vehicle
        if ($vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Count inquiries from contact messages for this vehicle
        $inquiriesCount = 0;
        try {
            $inquiriesCount = $vehicle->contactMessages()->count();
        } catch (\Exception $e) {
            // contactMessages relationship may not exist
        }

        return response()->json([
            'data' => [
                'vehicle_id' => $vehicle->id,
                'title' => $vehicle->title ?? (($vehicle->make?->name ?? '') . ' ' . ($vehicle->model?->name ?? '')),
                'price' => $vehicle->price,
                'status' => $vehicle->status,
                'views' => $vehicle->views_count ?? 0,
                'inquiries' => $inquiriesCount,
                'days_listed' => $vehicle->created_at ? now()->diffInDays($vehicle->created_at) : 0,
                'featured' => (bool) ($vehicle->is_featured ?? false),
                'listing_url' => "/vehicles/{$vehicle->id}",
            ],
        ]);
    }

    /**
     * Bulk update listings
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'listing_ids' => 'required|array|min:1',
            'listing_ids.*' => 'integer|exists:vehicles,id',
            'action' => 'required|in:mark_sold,delist,promote_featured,unpromote_featured,renew,delete',
        ]);

        $user = $request->user();
        $listingIds = $validated['listing_ids'];
        $action = $validated['action'];

        // Verify ownership
        $vehicles = Vehicle::whereIn('id', $listingIds)
            ->where('user_id', $user->id)
            ->get();

        if ($vehicles->count() !== count($listingIds)) {
            return response()->json(['message' => 'You can only modify your own listings'], 403);
        }

        $count = 0;
        foreach ($vehicles as $vehicle) {
            switch ($action) {
                case 'mark_sold':
                    $vehicle->update(['status' => 'sold']);
                    $count++;
                    break;
                case 'delist':
                    $vehicle->update(['status' => 'inactive']);
                    $count++;
                    break;
                case 'promote_featured':
                    $vehicle->update(['is_featured' => true]);
                    $count++;
                    break;
                case 'unpromote_featured':
                    $vehicle->update(['is_featured' => false]);
                    $count++;
                    break;
                case 'renew':
                    $vehicle->update(['created_at' => now(), 'status' => 'active']);
                    $count++;
                    break;
                case 'delete':
                    $vehicle->delete();
                    $count++;
                    break;
            }
        }

        return response()->json([
            'message' => "Successfully applied '{$action}' to {$count} listing(s)",
        ]);
    }

    /**
     * Reorder listings
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'listing_ids' => 'required|array|min:1',
            'listing_ids.*' => 'integer|exists:vehicles,id',
        ]);

        $user = $request->user();

        foreach ($validated['listing_ids'] as $position => $id) {
            Vehicle::where('id', $id)
                ->where('user_id', $user->id)
                ->update(['sort_order' => $position]);
        }

        return response()->json(['message' => 'Listings reordered successfully']);
    }

    /**
     * Mark a vehicle as sold
     */
    public function markSold(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $vehicle->update(['status' => 'sold']);

        return response()->json(['message' => 'Vehicle marked as sold']);
    }

    /**
     * Promote to featured
     */
    public function promoteFeatured(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $vehicle->update(['is_featured' => true]);

        return response()->json(['message' => 'Vehicle promoted to featured']);
    }

    /**
     * Renew listing
     */
    public function renew(Request $request, int $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::where('id', $vehicleId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $vehicle->update([
            'created_at' => now(),
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Listing renewed successfully']);
    }
}
