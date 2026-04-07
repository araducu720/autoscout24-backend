<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeAttachment;
use App\Models\SafetradeTransaction;
use App\Models\User;
use App\Events\SafetradeDisputeOpened;
use App\Notifications\DisputeUpdateNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DisputeController extends Controller
{
    /**
     * List disputes for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $disputes = Dispute::with(['transaction.vehicle', 'openedBy', 'assignedTo'])
            ->whereIn('transaction_id', function ($query) use ($user) {
                $query->select('id')
                    ->from('safetrade_transactions')
                    ->where(function ($q) use ($user) {
                        $q->where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id);
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($disputes);
    }

    /**
     * Get single dispute
     */
    public function show(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        // Check ownership — buyer or seller can view
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $dispute->load([
            'transaction.vehicle.make',
            'transaction.vehicle.model',
            'transaction.buyer',
            'transaction.seller',
            'openedBy',
            'assignedTo',
            'attachments.uploadedBy',
            'messages' => function ($query) {
                // Don't show internal messages to non-admins
                $query->where('is_internal', false);
            },
            'messages.user',
        ]);

        return response()->json(['data' => $dispute]);
    }

    /**
     * Open a new dispute
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'transaction_id' => 'required|exists:safetrade_transactions,id',
            'type' => 'required|in:' . implode(',', array_keys(Dispute::TYPE_LABELS)),
            'description' => 'required|string|min:20|max:2000',
        ]);

        $transaction = SafetradeTransaction::findOrFail($validated['transaction_id']);

        // Verify ownership — buyer or seller can open dispute
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check transaction is in a valid state for disputes
        if (in_array($transaction->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Cannot open dispute for a completed or cancelled transaction'
            ], 422);
        }

        // Check if there's already an open dispute
        $existingDispute = Dispute::where('transaction_id', $transaction->id)
            ->open()
            ->first();

        if ($existingDispute) {
            return response()->json([
                'message' => 'There is already an open dispute for this transaction',
                'dispute' => $existingDispute,
            ], 422);
        }

        // Determine priority based on type
        $priority = match($validated['type']) {
            'fraud' => 'urgent',
            'payment_not_received', 'vehicle_not_delivered' => 'high',
            default => 'medium',
        };

        $dispute = Dispute::create([
            'transaction_id' => $transaction->id,
            'opened_by' => $user->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'priority' => $priority,
        ]);

        // Update transaction status
        $transaction->update(['status' => 'disputed']);

        // Add initial message
        $dispute->messages()->create([
            'user_id' => $user->id,
            'message' => $validated['description'],
            'is_system' => false,
        ]);

        event(new SafetradeDisputeOpened($transaction, $validated['description']));

        return response()->json([
            'message' => 'Dispute opened successfully. Our team will review it shortly.',
            'data' => $dispute->load(['transaction', 'openedBy']),
        ], 201);
    }

    /**
     * Add a message to dispute
     */
    public function addMessage(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        // Check ownership — buyer or seller can add messages
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($dispute->isResolved()) {
            return response()->json(['message' => 'Cannot add messages to resolved dispute'], 422);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:2|max:2000',
        ]);

        $message = $dispute->addMessage($user->id, $validated['message']);

        // Notify the other party about the new dispute message
        $otherPartyId = $transaction->buyer_id === $user->id ? $transaction->seller_id : $transaction->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new DisputeUpdateNotification($dispute, 'message', $validated['message']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send dispute message notification: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Message added',
            'data' => $message->load('user'),
        ]);
    }

    /**
     * Upload attachment to dispute
     */
    public function uploadAttachment(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        // Check ownership — buyer or seller can upload
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($dispute->isResolved()) {
            return response()->json(['message' => 'Cannot add attachments to resolved dispute'], 422);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store('dispute-attachments', 'public');

        $attachment = DisputeAttachment::create([
            'dispute_id' => $dispute->id,
            'uploaded_by' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        // Add system message about attachment
        $dispute->addMessage($user->id, "Uploaded attachment: {$validated['title']}");

        return response()->json([
            'message' => 'Attachment uploaded',
            'data' => $attachment,
        ]);
    }

    /**
     * Get dispute types
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'data' => collect(Dispute::TYPE_LABELS)->map(function ($label, $value) {
                return ['value' => $value, 'label' => $label];
            })->values(),
        ]);
    }

    /**
     * Get dispute timeline
     */
    public function timeline(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        // Check ownership
        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id &&
            $dispute->opened_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $timeline = [];

        // Opening event
        $timeline[] = [
            'id' => 0,
            'dispute_id' => $dispute->id,
            'event_type' => 'opened',
            'title' => 'Dispute Opened',
            'description' => $dispute->description,
            'actor_id' => $dispute->opened_by,
            'actor_name' => $dispute->openedBy->name ?? 'Unknown',
            'actor_role' => $transaction->seller_id === $dispute->opened_by ? 'seller' : 'buyer',
            'created_at' => $dispute->created_at?->toISOString(),
        ];

        // Messages as timeline events
        $messages = $dispute->messages()
            ->with('user')
            ->where('is_internal', false)
            ->orderBy('created_at')
            ->get();

        foreach ($messages as $i => $message) {
            $timeline[] = [
                'id' => $i + 1,
                'dispute_id' => $dispute->id,
                'event_type' => $message->is_system ? 'updated' : 'message',
                'title' => $message->is_system ? 'System Update' : 'New Message',
                'description' => $message->message,
                'actor_id' => $message->user_id,
                'actor_name' => $message->user->name ?? 'System',
                'actor_role' => $message->is_system ? 'admin' : ($transaction->seller_id === $message->user_id ? 'seller' : 'buyer'),
                'created_at' => $message->created_at?->toISOString(),
            ];
        }

        // Resolution event
        if ($dispute->resolved_at) {
            $timeline[] = [
                'id' => count($timeline),
                'dispute_id' => $dispute->id,
                'event_type' => 'resolved',
                'title' => 'Dispute Resolved',
                'description' => $dispute->proposed_resolution ?? 'Dispute has been resolved',
                'actor_id' => $dispute->assigned_to ?? $dispute->opened_by,
                'actor_name' => $dispute->assignedTo->name ?? 'Admin',
                'actor_role' => 'admin',
                'created_at' => $dispute->resolved_at,
            ];
        }

        return response()->json(['data' => $timeline]);
    }

    /**
     * Add evidence to a dispute
     */
    public function addEvidence(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id &&
            $dispute->opened_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($dispute->isResolved()) {
            return response()->json(['message' => 'Cannot add evidence to resolved dispute'], 422);
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,mp4,mov,doc,docx|max:20480',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store('dispute-evidence', 'public');

        $mimeType = $file->getMimeType();
        $type = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        }

        $evidence = DisputeAttachment::create([
            'dispute_id' => $dispute->id,
            'uploaded_by' => $user->id,
            'title' => $file->getClientOriginalName(),
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
        ]);

        $dispute->addMessage($user->id, "Evidence uploaded: {$file->getClientOriginalName()}");

        return response()->json([
            'message' => 'Evidence uploaded successfully',
            'data' => [
                'id' => $evidence->id,
                'dispute_id' => $dispute->id,
                'type' => $type,
                'url' => Storage::url($path),
                'description' => $evidence->description,
                'uploaded_by' => $user->id,
                'created_at' => $evidence->created_at?->toISOString(),
            ],
        ], 201);
    }

    /**
     * Propose a resolution for the dispute
     */
    public function proposeResolution(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id &&
            $dispute->opened_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($dispute->isResolved()) {
            return response()->json(['message' => 'Dispute is already resolved'], 422);
        }

        $validated = $request->validate([
            'proposed_resolution' => 'required|string|min:10|max:2000',
        ]);

        $dispute->update([
            'proposed_resolution' => $validated['proposed_resolution'],
            'status' => 'mediation',
        ]);

        $dispute->addMessage($user->id, "Resolution proposed: {$validated['proposed_resolution']}");

        // Notify the other party about the proposed resolution
        $otherPartyId = $transaction->buyer_id === $user->id ? $transaction->seller_id : $transaction->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new DisputeUpdateNotification($dispute, 'resolution_proposed', $validated['proposed_resolution']));
            } catch (\Exception $e) {
                \Log::warning('Failed to send dispute resolution proposed notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Resolution proposed successfully']);
    }

    /**
     * Accept a proposed resolution
     */
    public function acceptResolution(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id &&
            $dispute->opened_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$dispute->proposed_resolution) {
            return response()->json(['message' => 'No resolution has been proposed'], 422);
        }

        $isSeller = $transaction->seller_id === $user->id;

        $dispute->update([
            $isSeller ? 'seller_accepted_resolution' : 'buyer_accepted_resolution' => true,
        ]);

        // If both parties accepted, resolve the dispute
        $dispute->refresh();
        if ($dispute->buyer_accepted_resolution && $dispute->seller_accepted_resolution) {
            $dispute->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            $dispute->addMessage($user->id, 'Both parties accepted the resolution. Dispute resolved.');

            // Notify both parties about resolution
            foreach ([$transaction->buyer_id, $transaction->seller_id] as $partyId) {
                $party = User::find($partyId);
                if ($party) {
                    try {
                        $party->notify(new DisputeUpdateNotification($dispute, 'resolved', 'Both parties have accepted the resolution. The dispute is now resolved.'));
                    } catch (\Exception $e) {
                        \Log::warning('Failed to send dispute resolved notification: ' . $e->getMessage());
                    }
                }
            }
        } else {
            $roleName = $isSeller ? 'Seller' : 'Buyer';
            $dispute->addMessage($user->id, "{$roleName} accepted the proposed resolution.");

            // Notify the other party
            $otherPartyId = $transaction->buyer_id === $user->id ? $transaction->seller_id : $transaction->buyer_id;
            $otherParty = User::find($otherPartyId);
            if ($otherParty) {
                try {
                    $otherParty->notify(new DisputeUpdateNotification($dispute, 'resolution_accepted', "{$roleName} has accepted the proposed resolution."));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send dispute acceptance notification: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'Resolution accepted']);
    }

    /**
     * Reject a proposed resolution
     */
    public function rejectResolution(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        if ($transaction->buyer_id !== $user->id && $transaction->seller_id !== $user->id &&
            $dispute->opened_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$dispute->proposed_resolution) {
            return response()->json(['message' => 'No resolution has been proposed'], 422);
        }

        $dispute->update([
            'proposed_resolution' => null,
            'buyer_accepted_resolution' => null,
            'seller_accepted_resolution' => null,
        ]);

        $isSeller = $transaction->seller_id === $user->id;
        $roleName = $isSeller ? 'Seller' : 'Buyer';
        $dispute->addMessage($user->id, "{$roleName} rejected the proposed resolution.");

        // Notify the other party about rejection
        $otherPartyId = $transaction->buyer_id === $user->id ? $transaction->seller_id : $transaction->buyer_id;
        $otherParty = User::find($otherPartyId);
        if ($otherParty) {
            try {
                $otherParty->notify(new DisputeUpdateNotification($dispute, 'resolution_rejected', "{$roleName} has rejected the proposed resolution."));
            } catch (\Exception $e) {
                \Log::warning('Failed to send dispute rejection notification: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Resolution rejected']);
    }

    /**
     * Close a dispute
     */
    public function close(Request $request, Dispute $dispute): JsonResponse
    {
        $user = $request->user();
        $transaction = $dispute->transaction;

        // Only the dispute opener or an admin can close it
        if ($dispute->opened_by !== $user->id &&
            $transaction->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($dispute->isResolved()) {
            return response()->json(['message' => 'Dispute is already resolved'], 422);
        }

        $dispute->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        // Restore transaction status
        if ($transaction->status === 'disputed') {
            $transaction->update(['status' => 'confirmed']);
        }

        $dispute->addMessage($user->id, 'Dispute closed.');

        // Notify both parties about dispute closure
        foreach ([$transaction->buyer_id, $transaction->seller_id] as $partyId) {
            if ($partyId === $user->id) continue; // Don't notify the person who closed it
            $party = User::find($partyId);
            if ($party) {
                try {
                    $party->notify(new DisputeUpdateNotification($dispute, 'closed', 'The dispute has been closed.'));
                } catch (\Exception $e) {
                    \Log::warning('Failed to send dispute closed notification: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'Dispute closed successfully']);
    }
}
