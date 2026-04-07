<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminAccess
{
    /**
     * Handle an incoming request.
     *
     * Restricts access to Filament admin panel based on:
     * - IP whitelist (if configured via FILAMENT_ALLOWED_IPS)
     * - Always allows access in local/development environment
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow in local/development environment
        if (app()->environment(['local', 'development', 'testing'])) {
            return $next($request);
        }

        // Check IP whitelist if configured
        $allowedIps = config('app.filament_allowed_ips', env('FILAMENT_ALLOWED_IPS'));
        
        if (!empty($allowedIps)) {
            $allowedIpList = array_map('trim', explode(',', $allowedIps));
            $clientIp = $request->ip();

            if (!in_array($clientIp, $allowedIpList)) {
                // Log unauthorized access attempt
                logger()->warning('Unauthorized admin panel access attempt', [
                    'ip' => $clientIp,
                    'url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent(),
                ]);

                abort(403, 'Access denied. Your IP is not authorized.');
            }
        }

        return $next($request);
    }
}
