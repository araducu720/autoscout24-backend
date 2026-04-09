<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Force JSON responses for API routes.
 *
 * Ensures all API requests receive JSON responses, even when the
 * Accept header is missing or set to text/html. This prevents
 * Sanctum from attempting redirects on unauthenticated API requests.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
