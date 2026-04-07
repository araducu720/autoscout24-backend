<?php

namespace App\Console\Commands;

use App\Models\SafetradeTransaction;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupAbandonedOrders extends Command
{
    protected $signature = 'cleanup:abandoned-orders {--hours=72 : Hours after which pending orders are considered abandoned}';
    protected $description = 'Cancel orders and transactions that have been pending beyond the timeout period';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $abandonedTxns = SafetradeTransaction::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->get();

        $count = 0;
        foreach ($abandonedTxns as $txn) {
            $txn->cancelTransaction('Automatically cancelled — no payment received within ' . $hours . ' hours.');
            $count++;
        }

        $abandonedOrders = Order::where('status', 'pending')
            ->where('created_at', '<', $cutoff)
            ->whereDoesntHave('safetradeTransaction')
            ->get();

        foreach ($abandonedOrders as $order) {
            $order->cancel();
            $count++;
        }

        if ($count > 0) {
            Log::info("Cleaned up {$count} abandoned orders/transactions older than {$hours} hours.");
            $this->info("Cleaned up {$count} abandoned orders/transactions.");
        } else {
            $this->info('No abandoned orders found.');
        }

        return self::SUCCESS;
    }
}
