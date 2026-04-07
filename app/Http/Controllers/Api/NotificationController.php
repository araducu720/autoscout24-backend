<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json($notifications);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy(Request $request, string $notificationId): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    public function getPreferences(Request $request): JsonResponse
    {
        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id, 'channel' => 'email'],
            NotificationPreference::getDefaultPreferences()
        );

        return response()->json(['data' => $preferences]);
    }

    public function updatePreferences(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'channel' => 'in:email,push,sms',
            'bid_received' => 'boolean',
            'bid_accepted' => 'boolean',
            'bid_rejected' => 'boolean',
            'payment_received' => 'boolean',
            'payment_verified' => 'boolean',
            'transaction_update' => 'boolean',
            'message_received' => 'boolean',
            'dispute_update' => 'boolean',
            'price_alert' => 'boolean',
            'new_listing_match' => 'boolean',
            'pickup_reminder' => 'boolean',
            'marketing' => 'boolean',
            'weekly_digest' => 'boolean',
        ]);

        $channel = $validated['channel'] ?? 'email';
        unset($validated['channel']);

        $preferences = NotificationPreference::updateOrCreate(
            ['user_id' => $request->user()->id, 'channel' => $channel],
            $validated
        );

        return response()->json([
            'message' => 'Notification preferences updated',
            'data' => $preferences,
        ]);
    }
}
