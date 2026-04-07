<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * GET /conversations — list conversations for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 20);

        $conversations = Conversation::with(['buyer', 'seller', 'vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $conversations->getCollection()->transform(function ($conv) use ($user) {
            return $this->formatConversation($conv, $user->id);
        });

        return response()->json($conversations);
    }

    /**
     * GET /conversations/{id} — get a single conversation's details
     */
    public function show(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();

        $conversation = Conversation::with(['buyer', 'seller', 'vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                  ->orWhere('seller_id', $user->id);
            })
            ->findOrFail($conversationId);

        return response()->json([
            'data' => $this->formatConversation($conversation, $user->id),
        ]);
    }

    /**
     * POST /conversations — start a new conversation
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'seller_id' => 'required|integer|exists:users,id',
            'vehicle_id' => 'nullable|integer|exists:vehicles,id',
            'first_message' => 'required|string|max:5000',
        ]);

        $user = $request->user();

        if ((int) $validated['seller_id'] === $user->id) {
            return response()->json(['message' => 'You cannot start a conversation with yourself'], 422);
        }

        // Check for existing conversation
        $existing = Conversation::where(function ($q) use ($user, $validated) {
            $q->where(function ($inner) use ($user, $validated) {
                $inner->where('buyer_id', $user->id)
                      ->where('seller_id', $validated['seller_id']);
            })->orWhere(function ($inner) use ($user, $validated) {
                $inner->where('buyer_id', $validated['seller_id'])
                      ->where('seller_id', $user->id);
            });
        });

        if (isset($validated['vehicle_id'])) {
            $existing->where('vehicle_id', $validated['vehicle_id']);
        }

        $existingConv = $existing->first();

        if ($existingConv) {
            // Add message to existing conversation
            $message = $existingConv->messages()->create([
                'sender_id' => $user->id,
                'content' => $validated['first_message'],
            ]);

            $existingConv->update([
                'last_message' => $validated['first_message'],
                'last_message_at' => now(),
            ]);

            // Notify the other party
            $recipientId = $existingConv->buyer_id === $user->id ? $existingConv->seller_id : $existingConv->buyer_id;
            $recipient = User::find($recipientId);
            if ($recipient) {
                try {
                    $recipient->notify(new NewMessageNotification($existingConv, $user->name, $validated['first_message']));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send new message notification: ' . $e->getMessage());
                }
            }

            return response()->json([
                'message' => 'Message added to existing conversation',
                'data' => $this->formatConversation($existingConv->load(['buyer', 'seller', 'vehicle']), $user->id),
            ], 409);
        }

        // Create new conversation
        $conversation = Conversation::create([
            'buyer_id' => $user->id,
            'seller_id' => $validated['seller_id'],
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'last_message' => $validated['first_message'],
            'last_message_at' => now(),
        ]);

        // Create the first message
        $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $validated['first_message'],
        ]);

        // Notify the seller about new conversation
        $seller = User::find($validated['seller_id']);
        if ($seller) {
            try {
                $seller->notify(new NewMessageNotification($conversation, $user->name, $validated['first_message']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send new message notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Conversation started',
            'data' => $this->formatConversation($conversation->load(['buyer', 'seller', 'vehicle']), $user->id),
        ], 201);
    }

    /**
     * GET /conversations/{id}/messages — get messages in a conversation
     */
    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 50);

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
              ->orWhere('seller_id', $user->id);
        })->findOrFail($conversationId);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $messages->getCollection()->transform(function ($msg) {
            return [
                'id' => $msg->id,
                'conversation_id' => $msg->conversation_id,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender->name ?? 'Unknown',
                'sender_avatar' => $msg->sender->avatar ?? null,
                'content' => $msg->content,
                'attachments' => $msg->attachments,
                'read_at' => $msg->read_at?->toISOString(),
                'created_at' => $msg->created_at->toISOString(),
            ];
        });

        return response()->json($messages);
    }

    /**
     * POST /conversations/{id}/messages — send a message
     */
    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*.type' => 'required_with:attachments|in:image,document',
            'attachments.*.url' => 'required_with:attachments|url',
        ]);

        $user = $request->user();

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
              ->orWhere('seller_id', $user->id);
        })->findOrFail($conversationId);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'content' => $validated['content'],
            'attachments' => $validated['attachments'] ?? null,
        ]);

        $conversation->update([
            'last_message' => $validated['content'],
            'last_message_at' => now(),
        ]);

        // Notify the other party
        $recipientId = $conversation->buyer_id === $user->id ? $conversation->seller_id : $conversation->buyer_id;
        $recipient = User::find($recipientId);
        if ($recipient) {
            try {
                $recipient->notify(new NewMessageNotification($conversation, $user->name, $validated['content']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send new message notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Message sent',
            'data' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $user->name,
                'sender_avatar' => $user->avatar,
                'content' => $message->content,
                'attachments' => $message->attachments,
                'read_at' => null,
                'created_at' => $message->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * PUT /conversations/{id}/read — mark conversation as read
     */
    public function markRead(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
              ->orWhere('seller_id', $user->id);
        })->findOrFail($conversationId);

        // Mark all messages from the other participant as read
        $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Conversation marked as read']);
    }

    /**
     * DELETE /conversations/{id} — archive/delete conversation
     */
    public function destroy(Request $request, int $conversationId): JsonResponse
    {
        $user = $request->user();

        $conversation = Conversation::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
              ->orWhere('seller_id', $user->id);
        })->findOrFail($conversationId);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted']);
    }

    /**
     * GET /conversations/{id}/typing — get typing status (stub)
     */
    public function getTyping(Request $request, int $conversationId): JsonResponse
    {
        return response()->json(['typing_user_ids' => []]);
    }

    /**
     * POST /conversations/{id}/typing — set typing status (stub)
     */
    public function setTyping(Request $request, int $conversationId): JsonResponse
    {
        return response()->json(['message' => 'ok']);
    }

    /**
     * Format conversation for API response
     */
    private function formatConversation(Conversation $conv, int $currentUserId): array
    {
        return [
            'id' => $conv->id,
            'buyer_id' => $conv->buyer_id,
            'seller_id' => $conv->seller_id,
            'vehicle_id' => $conv->vehicle_id,
            'listing_id' => null,
            'buyer_name' => $conv->buyer->name ?? 'Unknown',
            'seller_name' => $conv->seller->name ?? 'Unknown',
            'buyer_avatar' => $conv->buyer->avatar ?? null,
            'seller_avatar' => $conv->seller->avatar ?? null,
            'last_message' => $conv->last_message,
            'last_message_at' => $conv->last_message_at?->toISOString() ?? $conv->created_at->toISOString(),
            'unread_count' => $conv->unreadCountFor($currentUserId),
            'created_at' => $conv->created_at->toISOString(),
            'updated_at' => $conv->updated_at->toISOString(),
        ];
    }
}
