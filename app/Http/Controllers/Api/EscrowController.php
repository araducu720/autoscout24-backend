<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SafetradeTransaction;
use App\Models\EscrowTransaction;
use App\Events\SafetradePaymentFunded;
use App\Events\SafetradeFundsReleased;
use App\Events\SafetradeDisputeOpened;
use App\Notifications\DisputeUpdateNotification;
use App\Models\Dispute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EscrowController extends Controller
{
    /**
     * GET /escrow/transaction/{transactionId} — get escrow details for a transaction
     */
    public function show(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with('escrow')
            ->forUser($user->id)
            ->findOrFail($transactionId);

        if (!$txn->escrow) {
            return response()->json(['message' => 'No escrow found for this transaction'], 404);
        }

        return response()->json([
            'data' => $this->formatEscrow($txn->escrow, $txn),
        ]);
    }

    /**
     * POST /escrow/{transactionId}/fund — buyer uploads payment proof
     */
    public function fund(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer',
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $txn = SafetradeTransaction::with('escrow')->findOrFail($transactionId);

        // Only buyer can fund
        if ($txn->buyer_id !== $user->id) {
            return response()->json(['message' => 'Only the buyer can fund this escrow'], 403);
        }

        if (!in_array($txn->escrow_status, ['pending', 'awaiting_verification'])) {
            return response()->json(['message' => 'Escrow is already funded or in another state'], 422);
        }

        // Store payment proof file
        $file = $request->file('payment_proof');
        $proofPath = $file->store('payment-proofs/' . $txn->reference, 'public');

        // Update transaction — awaiting admin verification
        $txn->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'awaiting_verification',
            'payment_proof_path' => $proofPath,
            'escrow_status' => 'awaiting_verification',
            'status' => 'payment_uploaded',
        ]);

        // Update escrow release conditions
        if ($escrow = $txn->escrow) {
            $conditions = $escrow->release_conditions ?? [];
            $conditions['payment_proof_uploaded'] = true;
            $conditions['payment_verified'] = false;
            $escrow->update([
                'release_conditions' => $conditions,
            ]);
        }

        // Timeline
        $txn->addTimelineEvent(
            'payment_proof_uploaded',
            "Buyer uploaded payment proof via {$validated['payment_method']}. Amount: €" . number_format($txn->amount, 2, ',', '.') . ". Awaiting admin verification.",
            $user->id,
            $user->name,
            'buyer',
            [
                'payment_method' => $validated['payment_method'],
                'amount' => (float) $txn->amount,
                'proof_file' => $file->getClientOriginalName(),
            ]
        );

        // Fire event for notifications
        event(new SafetradePaymentFunded($txn, $validated['payment_method']));

        return response()->json([
            'message' => 'Payment proof uploaded successfully. Our team will verify within 24 hours.',
            'payment_proof_url' => Storage::url($proofPath),
        ]);
    }

    /**
     * POST /escrow/{transactionId}/confirm-receipt — buyer confirms vehicle received
     */
    public function confirmReceipt(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with('escrow')->findOrFail($transactionId);

        if ($txn->buyer_id !== $user->id) {
            return response()->json(['message' => 'Only the buyer can confirm receipt'], 403);
        }

        if ($txn->escrow_status !== 'funded') {
            return response()->json(['message' => 'Escrow must be funded before confirming receipt'], 422);
        }

        DB::transaction(function () use ($txn, $user) {
            // Confirm receipt and auto-release funds
            $txn->confirmReceipt();

            // Timeline
            $txn->addTimelineEvent(
                'receipt_confirmed',
                'Buyer confirmed vehicle receipt. Funds released to seller.',
                $user->id,
                $user->name,
                'buyer'
            );
        });

        // Fire event for notifications (outside transaction - non-critical)
        event(new SafetradeFundsReleased($txn->fresh()));

        return response()->json([
            'message' => 'Vehicle receipt confirmed. Payment released to seller.',
        ]);
    }

    /**
     * POST /escrow/{transactionId}/release — manually release funds to seller
     */
    public function release(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $txn = SafetradeTransaction::with('escrow')->findOrFail($transactionId);

        // Only buyer or admin can release
        if ($txn->buyer_id !== $user->id) {
            return response()->json(['message' => 'Only the buyer can release funds'], 403);
        }

        if (!$txn->releaseFunds()) {
            return response()->json(['message' => 'Cannot release funds at this stage'], 422);
        }

        $txn->addTimelineEvent(
            'funds_released',
            'Funds released to seller',
            $user->id,
            $user->name,
            'buyer'
        );

        // Fire event for notifications
        event(new SafetradeFundsReleased($txn));

        return response()->json(['message' => 'Funds released to seller']);
    }

    /**
     * POST /escrow/{transactionId}/dispute — open a dispute
     */
    public function dispute(Request $request, int $transactionId): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'evidence' => 'nullable|array',
            'evidence.*' => 'string|max:500',
        ]);

        $txn = SafetradeTransaction::with('escrow')->findOrFail($transactionId);

        // Only buyer or seller can dispute
        if ($txn->buyer_id !== $user->id && $txn->seller_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$txn->dispute($validated['reason'], $validated['evidence'] ?? [])) {
            return response()->json(['message' => 'Cannot open dispute at this stage'], 422);
        }

        $role = $txn->buyer_id === $user->id ? 'buyer' : 'seller';

        $txn->addTimelineEvent(
            'dispute_opened',
            "Dispute opened by {$role}: {$validated['reason']}",
            $user->id,
            $user->name,
            $role,
            ['reason' => $validated['reason']]
        );

        // Fire event for notifications
        event(new SafetradeDisputeOpened($txn, $validated['reason']));

        return response()->json(['message' => 'Dispute opened. Our team will review within 48 hours.']);
    }

    /**
     * POST /escrow/{escrowId}/resolve — resolve dispute (admin action)
     */
    public function resolve(Request $request, int $escrowId): JsonResponse
    {
        $user = $request->user();

        // Admin-only action
        abort_unless($user->is_admin, 403, 'Only administrators can resolve disputes.');

        $validated = $request->validate([
            'resolution' => 'required|in:buyer,seller,split',
        ]);

        $escrow = EscrowTransaction::with('safetradeTransaction')->findOrFail($escrowId);
        $txn = $escrow->safetradeTransaction;

        if ($escrow->status !== 'disputed') {
            return response()->json(['message' => 'Only disputed escrows can be resolved'], 422);
        }

        switch ($validated['resolution']) {
            case 'buyer':
                $txn->refund();
                $description = 'Dispute resolved in favor of buyer. Funds refunded.';
                break;
            case 'seller':
                $txn->releaseFunds();
                $description = 'Dispute resolved in favor of seller. Funds released.';
                break;
            case 'split':
                // For split, just mark as released (in real system, split payment)
                $txn->releaseFunds();
                $description = 'Dispute resolved with split payment.';
                break;
        }

        $txn->addTimelineEvent(
            'dispute_resolved',
            $description,
            $user->id,
            $user->name,
            'mediator',
            ['resolution' => $validated['resolution']]
        );

        // Notify buyer and seller about admin resolution
        $dispute = Dispute::where('transaction_id', $txn->id)->latest()->first();
        foreach ([$txn->buyer_id, $txn->seller_id] as $partyId) {
            $party = User::find($partyId);
            if ($party) {
                try {
                    if ($dispute) {
                        $party->notify(new DisputeUpdateNotification($dispute, 'admin_resolved', $description));
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to send dispute admin resolved notification: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => $description]);
    }

    /**
     * Format escrow data for frontend
     */
    private function formatEscrow(EscrowTransaction $escrow, SafetradeTransaction $txn): array
    {
        return [
            'id' => $escrow->id,
            'transaction_id' => $txn->id,
            'amount' => (float) $escrow->amount,
            'status' => $escrow->status,
            'buyer_id' => $escrow->buyer_id,
            'seller_id' => $escrow->seller_id,
            'release_conditions' => $escrow->release_conditions ?? [
                'buyer_confirmed' => false,
                'seller_confirmed' => false,
                'payment_verified' => false,
            ],
            'dispute_reason' => $escrow->dispute_reason,
            'dispute_evidence' => $escrow->dispute_evidence ?? [],
            'created_at' => $escrow->created_at->toIso8601String(),
            'updated_at' => $escrow->updated_at->toIso8601String(),
        ];
    }
}
