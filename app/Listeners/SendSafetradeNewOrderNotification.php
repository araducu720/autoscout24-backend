<?php

namespace App\Listeners;

use App\Events\SafetradeTransactionCreated;
use App\Models\User;
use App\Notifications\SafetradeNewOrderNotification;
use Illuminate\Support\Facades\Log;

class SendSafetradeNewOrderNotification
{
    public function handle(SafetradeTransactionCreated $event): void
    {
        $transaction = $event->transaction;

        // Notify the seller about the new order
        $seller = User::find($transaction->seller_id);
        if ($seller && $seller->shouldNotify('transaction_update', 'email')) {
            try {
                $seller->notify(new SafetradeNewOrderNotification($transaction));
            } catch (\Exception $e) {
                Log::error('Failed to send SafeTrade new order notification: ' . $e->getMessage(), [
                    'transaction_id' => $transaction->id,
                    'seller_id' => $seller->id,
                ]);
            }
        }
    }
}
