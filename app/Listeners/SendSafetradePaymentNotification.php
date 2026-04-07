<?php

namespace App\Listeners;

use App\Events\SafetradePaymentFunded;
use App\Models\User;
use App\Notifications\SafetradePaymentReceivedNotification;
use Illuminate\Support\Facades\Log;

class SendSafetradePaymentNotification
{
    public function handle(SafetradePaymentFunded $event): void
    {
        $transaction = $event->transaction;

        $buyer = User::find($transaction->buyer_id);
        $seller = User::find($transaction->seller_id);

        try {
            if ($buyer && $buyer->shouldNotify('payment_received', 'email')) {
                $buyer->notify(new SafetradePaymentReceivedNotification($transaction));
            }
            if ($seller && $seller->shouldNotify('payment_received', 'email')) {
                $seller->notify(new SafetradePaymentReceivedNotification($transaction));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send payment notification: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
        }
    }
}
