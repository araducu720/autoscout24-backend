<?php

namespace App\Listeners;

use App\Events\SafetradeDisputeOpened;
use App\Models\User;
use App\Notifications\SafetradeDisputeNotification;
use Illuminate\Support\Facades\Log;

class SendSafetradeDisputeNotification
{
    public function handle(SafetradeDisputeOpened $event): void
    {
        $transaction = $event->transaction;
        $reason = $event->reason;

        try {
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);

            if ($buyer && $buyer->shouldNotify('dispute_update', 'email')) {
                $buyer->notify(new SafetradeDisputeNotification($transaction, $reason));
            }
            if ($seller && $seller->shouldNotify('dispute_update', 'email')) {
                $seller->notify(new SafetradeDisputeNotification($transaction, $reason));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send dispute notification: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }
}
