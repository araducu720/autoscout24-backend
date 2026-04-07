<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SafetradeTransaction;
use App\Models\Order;
use App\Models\Vehicle;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for the authenticated user
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->id;

        // Seller stats
        $totalSales = SafetradeTransaction::where('seller_id', $userId)
            ->where('status', 'completed')
            ->count();

        $earnings = SafetradeTransaction::where('seller_id', $userId)
            ->where('status', 'completed')
            ->sum('vehicle_price');

        $monthlySales = SafetradeTransaction::where('seller_id', $userId)
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->startOfMonth())
            ->count();

        // Buyer stats
        $totalPurchases = SafetradeTransaction::where('buyer_id', $userId)
            ->where('status', 'completed')
            ->count();

        $savedListings = 0;
        if (class_exists(\App\Models\Favorite::class)) {
            try {
                $savedListings = DB::table('favorites')
                    ->where('user_id', $userId)
                    ->count();
            } catch (\Exception $e) {
                $savedListings = 0;
            }
        }

        $activeDisputes = 0;
        try {
            $activeDisputes = DB::table('disputes')
                ->join('safetrade_transactions', 'disputes.transaction_id', '=', 'safetrade_transactions.id')
                ->where(function ($q) use ($userId) {
                    $q->where('safetrade_transactions.seller_id', $userId)
                      ->orWhere('safetrade_transactions.buyer_id', $userId)
                      ->orWhere('disputes.opened_by', $userId);
                })
                ->whereIn('disputes.status', ['open', 'under_review', 'awaiting_info', 'mediation', 'escalated'])
                ->count();
        } catch (\Exception $e) {
            $activeDisputes = 0;
        }

        $unreadNotifications = 0;
        try {
            $unreadNotifications = DB::table('notifications')
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            $unreadNotifications = 0;
        }

        // Recent activity
        $recentMessages = ['count' => 0, 'unread' => 0];
        try {
            $recentMessages = [
                'count' => DB::table('messages')
                    ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                    ->where(function ($q) use ($userId) {
                        $q->where('conversations.buyer_id', $userId)
                          ->orWhere('conversations.seller_id', $userId);
                    })
                    ->where('messages.created_at', '>=', now()->subDays(30))
                    ->count(),
                'unread' => DB::table('messages')
                    ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                    ->where('messages.sender_id', '!=', $userId)
                    ->where(function ($q) use ($userId) {
                        $q->where('conversations.buyer_id', $userId)
                          ->orWhere('conversations.seller_id', $userId);
                    })
                    ->whereNull('messages.read_at')
                    ->count(),
            ];
        } catch (\Exception $e) {
            // Messages table may not exist
        }

        $pendingTransactions = SafetradeTransaction::where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->where('status', 'pending')->count();

        $totalTransactions = SafetradeTransaction::where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->count();

        // Average rating (from reviews on user's vehicles)
        $averageRating = 0;
        $reviewsCount = 0;
        try {
            $ratings = DB::table('reviews')
                ->whereIn('vehicle_id', function ($q) use ($userId) {
                    $q->select('id')->from('vehicles')->where('user_id', $userId);
                })
                ->where('status', 'approved');
            $averageRating = round((clone $ratings)->avg('rating') ?? 0, 1);
            $reviewsCount = (clone $ratings)->count();
        } catch (\Exception $e) {
            // Reviews table may not exist
        }

        return response()->json([
            'data' => [
                // Seller stats
                'total_sales' => $totalSales,
                'average_rating' => $averageRating,
                'reviews_count' => $reviewsCount,
                'earnings' => round($earnings, 2),
                'monthly_sales' => $monthlySales,

                // Buyer stats
                'total_purchases' => $totalPurchases,
                'saved_listings' => $savedListings,
                'active_disputes' => $activeDisputes,
                'unread_notifications' => $unreadNotifications,

                // Activity
                'recent_messages' => $recentMessages,
                'recent_transactions' => [
                    'count' => $totalTransactions,
                    'pending' => $pendingTransactions,
                ],
            ],
        ]);
    }

    /**
     * Get user activity summary
     */
    public function activity(Request $request): JsonResponse
    {
        $user = $request->user();
        $userId = $user->id;

        $totalMessages = 0;
        $unreadMessages = 0;
        try {
            $totalMessages = DB::table('messages')
                ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                ->where(function ($q) use ($userId) {
                    $q->where('conversations.buyer_id', $userId)
                      ->orWhere('conversations.seller_id', $userId);
                })
                ->count();
            $unreadMessages = DB::table('messages')
                ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
                ->where('messages.sender_id', '!=', $userId)
                ->where(function ($q) use ($userId) {
                    $q->where('conversations.buyer_id', $userId)
                      ->orWhere('conversations.seller_id', $userId);
                })
                ->whereNull('messages.read_at')
                ->count();
        } catch (\Exception $e) {
            // Messages table may not exist
        }

        $totalTransactions = SafetradeTransaction::where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->count();

        $pendingTransactions = SafetradeTransaction::where(function ($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })->where('status', 'pending')->count();

        return response()->json([
            'data' => [
                'total_messages' => $totalMessages,
                'unread_messages' => $unreadMessages,
                'total_transactions' => $totalTransactions,
                'pending_transactions' => $pendingTransactions,
            ],
        ]);
    }
}
