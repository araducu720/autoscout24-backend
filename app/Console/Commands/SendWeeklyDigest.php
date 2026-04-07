<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SafetradeTransaction;
use App\Models\Vehicle;
use App\Notifications\WeeklyDigestNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyDigest extends Command
{
    protected $signature = 'digest:send-weekly';
    protected $description = 'Send weekly digest emails to users who have opted in';

    public function handle(): int
    {
        $users = User::whereHas('notificationPreferences', function ($q) {
            $q->where('channel', 'email')->where('weekly_digest', true);
        })->get();

        // If no users have explicit preferences, send to all verified users
        // (default preference for weekly_digest is true)
        if ($users->isEmpty()) {
            $users = User::whereNotNull('email_verified_at')->get();
        }

        $sent = 0;
        $errors = 0;
        $weekAgo = now()->subWeek();

        foreach ($users as $user) {
            // Check user preference (respects defaults)
            if (!$user->shouldNotify('weekly_digest', 'email')) {
                continue;
            }

            try {
                $stats = $this->buildStatsForUser($user, $weekAgo);

                // Skip if user has no activity
                if ($this->isEmptyStats($stats)) {
                    continue;
                }

                $user->notify(new WeeklyDigestNotification($stats));
                $sent++;
            } catch (\Exception $e) {
                $errors++;
                Log::error('Failed to send weekly digest', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("User #{$user->id}: {$e->getMessage()}");
            }
        }

        $this->info("Weekly digest sent: {$sent} emails, {$errors} errors.");

        return self::SUCCESS;
    }

    private function buildStatsForUser(User $user, $since): array
    {
        // Transactions where user is buyer or seller
        $transactions = SafetradeTransaction::forUser($user->id)
            ->where('created_at', '>=', $since)
            ->get();

        $completedTransactions = SafetradeTransaction::forUser($user->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $since)
            ->count();

        // Vehicle listing stats
        $newListings = Vehicle::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->count();

        $totalViews = Vehicle::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('views_count');

        // Price alerts triggered this week
        $alertsTriggered = $user->priceAlerts()
            ->where('last_triggered_at', '>=', $since)
            ->count();

        return [
            'new_transactions' => $transactions->count(),
            'completed_transactions' => $completedTransactions,
            'pending_transactions' => $transactions->where('status', 'pending')->count(),
            'total_transaction_value' => $transactions->sum('vehicle_price'),
            'new_listings' => $newListings,
            'total_views' => $totalViews,
            'alerts_triggered' => $alertsTriggered,
            'period_start' => $since->toDateString(),
            'period_end' => now()->toDateString(),
        ];
    }

    private function isEmptyStats(array $stats): bool
    {
        return $stats['new_transactions'] === 0
            && $stats['completed_transactions'] === 0
            && $stats['new_listings'] === 0
            && $stats['alerts_triggered'] === 0
            && $stats['total_views'] === 0;
    }
}
