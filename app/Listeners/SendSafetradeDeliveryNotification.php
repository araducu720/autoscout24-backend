<?php

namespace App\Listeners;

use App\Events\SafetradeDeliveryConfirmed;
use App\Models\User;
use App\Notifications\SafetradeDeliveryConfirmedNotification;
use Illuminate\Support\Facades\Log;

class SendSafetradeDeliveryNotification
{
    public function handle(SafetradeDeliveryConfirmed $event): void
    {
        $transaction = $event->transaction;

        $seller = User::find($transaction->seller_id);
        if ($seller && $seller->shouldNotify('transaction_update', 'email')) {
            try {
                $seller->notify(new SafetradeDeliveryConfirmedNotification($transaction));
            } catch (\Exception $e) {
                Log::error('Failed to send delivery notification: ' . $e->getMessage(), [
                    'transaction_id' => $transaction->id,
                ]);
            }
        }
    }
}
