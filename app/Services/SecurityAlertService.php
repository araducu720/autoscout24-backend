<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class SecurityAlertService
{
    /**
     * Log and notify about suspicious login activity.
     */
    public static function suspiciousLogin(User $user, string $ip, string $reason): void
    {
        Log::channel('single')->warning('Suspicious login detected', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
            'reason' => $reason,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log failed login attempts beyond threshold.
     */
    public static function bruteForceAttempt(string $email, string $ip, int $attempts): void
    {
        Log::channel('single')->warning('Possible brute force attack', [
            'email' => $email,
            'ip' => $ip,
            'attempts' => $attempts,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log sensitive operations (password change, 2FA changes, bank detail updates).
     */
    public static function sensitiveOperation(User $user, string $operation, string $ip): void
    {
        Log::channel('single')->info('Sensitive operation performed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'operation' => $operation,
            'ip' => $ip,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
