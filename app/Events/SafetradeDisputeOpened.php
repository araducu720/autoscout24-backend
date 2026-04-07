<?php

namespace App\Events;

use App\Models\SafetradeTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SafetradeDisputeOpened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SafetradeTransaction $transaction,
        public string $reason
    ) {}
}
