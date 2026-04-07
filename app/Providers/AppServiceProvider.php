<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SettingsService::class, function () {
            return new \App\Services\SettingsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter
        RateLimiter::for('api', function (Request $request) {
            // Authenticated users get higher limit
            if ($request->user()) {
                // Dealers get even higher limits
                $limit = $request->user()->dealer
                    ? (int) env('RATE_LIMIT_DEALER_PER_MINUTE', 600)
                    : (int) env('RATE_LIMIT_AUTH_PER_MINUTE', 300);
                return Limit::perMinute($limit)->by($request->user()->id);
            }

            // Guests — generous limit since Vercel/CDN IPs represent many real users
            return Limit::perMinute(
                (int) env('RATE_LIMIT_PER_MINUTE', 500)
            )->by($request->ip());
        });

        // Stricter limit for authentication endpoints (prevent brute force)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Very strict limit for password reset (prevent enumeration)
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Contact messages limit
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(10)->by(
                $request->user()?->id ?: $request->ip()
            );
        });
    }
}
