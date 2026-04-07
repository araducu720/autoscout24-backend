<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    /**
     * Get seller profile by user ID (public).
     */
    public function profile($sellerId): JsonResponse
    {
        $seller = User::select([
            'id', 'name', 'avatar', 'country', 'created_at',
        ])->with('dealer')->findOrFail($sellerId);

        $vehiclesCount = Vehicle::where('user_id', $seller->id)
            ->where('status', 'active')
            ->count();

        // Calculate average rating and count from reviews in a single query
        $reviewStats = Review::whereHas('vehicle', function ($q) use ($seller) {
            $q->where('user_id', $seller->id);
        })->where('status', 'approved')
          ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
          ->first();

        // Check if user is also a dealer
        $dealer = $seller->dealer;

        return response()->json([
            'data' => [
                'id' => $seller->id,
                'name' => $seller->name,
                'avatar' => $seller->avatar,
                'country' => $seller->country,
                'member_since' => $seller->created_at,
                'vehicles_count' => $vehiclesCount,
                'average_rating' => $reviewStats->avg_rating ? round((float) $reviewStats->avg_rating, 1) : null,
                'reviews_count' => (int) $reviewStats->total,
                'is_dealer' => $dealer !== null,
                'dealer_info' => $dealer ? [
                    'company_name' => $dealer->company_name,
                    'is_verified' => $dealer->is_verified,
                ] : null,
            ],
        ]);
    }

    /**
     * Get seller's active vehicles (public).
     */
    public function vehicles(Request $request, $sellerId): JsonResponse
    {
        $seller = User::findOrFail($sellerId);

        $vehicles = Vehicle::with(['make', 'model', 'images', 'primaryImage'])
            ->where('user_id', $seller->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 12));

        return response()->json(VehicleResource::collection($vehicles)->response()->getData());
    }

    /**
     * Get seller ratings/reviews (public).
     */
    public function ratings(Request $request, $sellerId): JsonResponse
    {
        $seller = User::findOrFail($sellerId);

        $reviews = Review::with(['user:id,name,avatar', 'vehicle:id,title'])
            ->whereHas('vehicle', function ($q) use ($seller) {
                $q->where('user_id', $seller->id);
            })
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        // Hide user info for anonymous reviews
        $reviews->through(function ($review) {
            if ($review->anonymous) {
                $review->setRelation('user', null);
                $review->user_id = null;
            }
            return $review;
        });

        return response()->json($reviews);
    }
}
