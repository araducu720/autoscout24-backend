<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * GET /user/reviews — Get reviews written BY the authenticated user.
     */
    public function userReviews(Request $request): JsonResponse
    {
        $user = $request->user();

        $reviews = Review::with(['vehicle:id,title,price,year', 'vehicle.make:id,name', 'vehicle.model:id,name,make_id', 'vehicle.primaryImage'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 10));

        return response()->json($reviews);
    }

    /**
     * GET /user/received-reviews — Get reviews received by the user (on their vehicles).
     */
    public function receivedReviews(Request $request): JsonResponse
    {
        $user = $request->user();

        $vehicleIds = Vehicle::where('user_id', $user->id)->pluck('id');

        $reviews = Review::with(['user:id,name,avatar', 'vehicle:id,title,price,year', 'vehicle.make:id,name', 'vehicle.model:id,name,make_id'])
            ->whereIn('vehicle_id', $vehicleIds)
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

    /**
     * Get reviews for a vehicle (public).
     */
    public function index(Request $request, $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $reviews = Review::with(['user:id,name,avatar'])
            ->where('vehicle_id', $vehicle->id)
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

    /**
     * Create a review for a vehicle (protected).
     */
    public function store(Request $request, $vehicleId): JsonResponse
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $user = $request->user();

        // Check if user already reviewed this vehicle
        $existingReview = Review::where('vehicle_id', $vehicle->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this vehicle',
            ], 422);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:2000',
            'rating_breakdown' => 'nullable|array',
            'rating_breakdown.vehicle' => 'nullable|integer|min:1|max:5',
            'rating_breakdown.seller' => 'nullable|integer|min:1|max:5',
            'rating_breakdown.shipping' => 'nullable|integer|min:1|max:5',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'string|url',
            'anonymous' => 'nullable|boolean',
            'transaction_id' => 'nullable|integer|exists:safetrade_transactions,id',
        ]);

        $review = Review::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $user->id,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'rating_vehicle' => $validated['rating_breakdown']['vehicle'] ?? null,
            'rating_seller' => $validated['rating_breakdown']['seller'] ?? null,
            'rating_shipping' => $validated['rating_breakdown']['shipping'] ?? null,
            'photos' => $validated['photos'] ?? null,
            'anonymous' => $validated['anonymous'] ?? false,
        ]);

        $review->load('user:id,name,avatar');

        // Notify the vehicle owner about the new review
        if ($vehicle->user_id && $vehicle->user_id !== $user->id) {
            $vehicleOwner = User::find($vehicle->user_id);
            if ($vehicleOwner) {
                try {
                    $vehicleOwner->notify(new NewReviewNotification($review, $vehicle));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send new review notification: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }

    /**
     * Update a review (protected).
     */
    public function update(Request $request, $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);
        $user = $request->user();

        if ($review->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|min:10|max:2000',
            'rating_breakdown' => 'nullable|array',
            'rating_breakdown.vehicle' => 'nullable|integer|min:1|max:5',
            'rating_breakdown.seller' => 'nullable|integer|min:1|max:5',
            'rating_breakdown.shipping' => 'nullable|integer|min:1|max:5',
            'photos' => 'nullable|array|max:5',
            'anonymous' => 'nullable|boolean',
        ]);

        $updateData = [];
        if (isset($validated['rating'])) $updateData['rating'] = $validated['rating'];
        if (isset($validated['comment'])) $updateData['comment'] = $validated['comment'];
        if (isset($validated['photos'])) $updateData['photos'] = $validated['photos'];
        if (isset($validated['anonymous'])) $updateData['anonymous'] = $validated['anonymous'];
        if (isset($validated['rating_breakdown'])) {
            $updateData['rating_vehicle'] = $validated['rating_breakdown']['vehicle'] ?? $review->rating_vehicle;
            $updateData['rating_seller'] = $validated['rating_breakdown']['seller'] ?? $review->rating_seller;
            $updateData['rating_shipping'] = $validated['rating_breakdown']['shipping'] ?? $review->rating_shipping;
        }

        $review->update($updateData);

        return response()->json([
            'message' => 'Review updated successfully',
        ]);
    }

    /**
     * Delete a review (protected).
     */
    public function destroy(Request $request, $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);
        $user = $request->user();

        if ($review->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }

    /**
     * Mark a review as helpful (protected).
     */
    public function markHelpful(Request $request, $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);
        $user = $request->user();

        // Check if already marked
        if ($review->helpfulBy()->where('user_id', $user->id)->exists()) {
            // Toggle off
            $review->helpfulBy()->detach($user->id);
            $review->decrement('helpful_count');
            return response()->json(['message' => 'Helpful mark removed']);
        }

        $review->helpfulBy()->attach($user->id);
        $review->increment('helpful_count');

        return response()->json(['message' => 'Review marked as helpful']);
    }
}
