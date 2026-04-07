<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check price alerts every 15 minutes
Schedule::command('alerts:check-prices')->everyFifteenMinutes();

// Send weekly digest every Monday at 8:00 AM
Schedule::command('digest:send-weekly')->weeklyOn(1, '08:00');

// Prune expired Sanctum tokens daily
Schedule::command('sanctum:prune-expired --hours=168')->daily();

// Cleanup abandoned orders/transactions (pending > 72 hours)
Schedule::command('cleanup:abandoned-orders')->hourly();

// Process queued emails/notifications every minute
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=90')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
