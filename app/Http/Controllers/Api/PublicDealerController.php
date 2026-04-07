<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VehicleResource;
use App\Models\Dealer;
use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicDealerController extends Controller
{
    /**
     * List all active and verified dealers (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Dealer::where('is_active', true)
            ->select([
                'id', 'user_id', 'company_name', 'slug', 'logo', 'description',
                'city', 'country', 'phone', 'email', 'website',
                'is_verified', 'rating', 'total_reviews', 'type',
                'offers_home_delivery', 'offers_financing', 'offers_warranty',
            ]);

        // Search by name or city
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->has('country') && $request->country !== 'All Countries') {
            $query->where('country', $request->country);
        }

        // Filter by verified status
        if ($request->has('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        $dealers = $query->orderByDesc('is_verified')
            ->orderByDesc('rating')
            ->paginate($request->get('per_page', 20));

        // Pre-fetch vehicle counts to avoid N+1 queries
        $userIds = $dealers->pluck('user_id')->unique();
        $vehicleCounts = Vehicle::whereIn('user_id', $userIds)
            ->where('status', 'active')
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as count')
            ->pluck('count', 'user_id');

        // Transform data to match frontend expectations
        $transformed = $dealers->through(function ($dealer) use ($vehicleCounts) {
            return [
                'id' => $dealer->id,
                'user_id' => $dealer->user_id,
                'company_name' => $dealer->company_name,
                'slug' => $dealer->slug,
                'logo' => $dealer->logo,
                'description' => $dealer->description,
                'city' => $dealer->city,
                'country' => $dealer->country,
                'phone' => $dealer->phone,
                'email' => $dealer->email,
                'website' => $dealer->website,
                'is_verified' => $dealer->is_verified,
                'vehicles_count' => $vehicleCounts[$dealer->user_id] ?? 0,
                'average_rating' => $dealer->rating ? (float) $dealer->rating : null,
                'reviews_count' => $dealer->total_reviews ?? 0,
                'response_time_hours' => rand(1, 4), // placeholder
                'type' => $dealer->type,
                'offers_home_delivery' => $dealer->offers_home_delivery,
                'offers_financing' => $dealer->offers_financing,
                'offers_warranty' => $dealer->offers_warranty,
            ];
        });

        return response()->json($transformed);
    }

    /**
     * Show single dealer profile (public).
     */
    public function show($id): JsonResponse
    {
        $dealer = Dealer::where('is_active', true)->findOrFail($id);

        $vehiclesCount = Vehicle::where('user_id', $dealer->user_id)
            ->where('status', 'active')
            ->count();

        return response()->json([
            'data' => [
                'id' => $dealer->id,
                'user_id' => $dealer->user_id,
                'company_name' => $dealer->company_name,
                'slug' => $dealer->slug,
                'logo' => $dealer->logo,
                'description' => $dealer->description,
                'address' => $dealer->address,
                'city' => $dealer->city,
                'postal_code' => $dealer->postal_code,
                'country' => $dealer->country,
                'phone' => $dealer->phone,
                'email' => $dealer->email,
                'website' => $dealer->website,
                'is_verified' => $dealer->is_verified,
                'vehicles_count' => $vehiclesCount,
                'average_rating' => $dealer->rating ? (float) $dealer->rating : null,
                'reviews_count' => $dealer->total_reviews ?? 0,
                'type' => $dealer->type,
                'offers_home_delivery' => $dealer->offers_home_delivery,
                'offers_financing' => $dealer->offers_financing,
                'offers_warranty' => $dealer->offers_warranty,
                'response_time_hours' => rand(1, 4),
                'registration_number' => $dealer->registration_number,
                'created_at' => $dealer->created_at,
            ],
        ]);
    }

    /**
     * Get dealer's vehicles (public).
     */
    public function vehicles(Request $request, $id): JsonResponse
    {
        $dealer = Dealer::where('is_active', true)->findOrFail($id);

        $vehicles = Vehicle::with(['make', 'model', 'images', 'primaryImage'])
            ->where('user_id', $dealer->user_id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 12));

        return response()->json(VehicleResource::collection($vehicles)->response()->getData());
    }

    /**
     * Get dealer's ratings (public).
     */
    public function ratings(Request $request, $id): JsonResponse
    {
        $dealer = Dealer::where('is_active', true)->findOrFail($id);

        // Get reviews on vehicles belonging to this dealer's user
        $reviews = Review::with(['user:id,name,avatar'])
            ->whereHas('vehicle', function ($q) use ($dealer) {
                $q->where('user_id', $dealer->user_id);
            })
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }
}
