<?php

namespace App\Listeners;

use App\Events\SafetradeFundsReleased;
use App\Models\User;
use App\Notifications\SafetradeFundsReleasedNotification;
use Illuminate\Support\Facades\Log;

class SendSafetradeFundsReleasedNotification
{
    public function handle(SafetradeFundsReleased $event): void
    {
        $transaction = $event->transaction;

        try {
            $seller = User::find($transaction->seller_id);
            if ($seller && $seller->shouldNotify('payment_verified', 'email')) {
                $seller->notify(new SafetradeFundsReleasedNotification($transaction));
            }

            $buyer = User::find($transaction->buyer_id);
            if ($buyer && $buyer->shouldNotify('payment_verified', 'email')) {
                $buyer->notify(new SafetradeFundsReleasedNotification($transaction));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send funds released notification: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }
}
