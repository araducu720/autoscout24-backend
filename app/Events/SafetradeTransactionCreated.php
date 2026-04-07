<?php

namespace App\Events;

use App\Models\SafetradeTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SafetradeTransactionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SafetradeTransaction $transaction
    ) {}
}
