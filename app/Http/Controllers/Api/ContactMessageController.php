<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contactMessage = ContactMessage::create($validated);

        // Send confirmation email to the customer
        try {
            Notification::route('mail', $validated['email'])
                ->notify(new ContactMessageReceivedNotification($contactMessage));
        } catch (\Exception $e) {
            // Log but don't fail the request if email fails
            \Log::warning('Failed to send contact message confirmation: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $contactMessage,
        ], 201);
    }
}
