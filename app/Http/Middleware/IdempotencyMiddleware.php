<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IdempotencyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to POST/PUT/PATCH with idempotency key
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        $idempotencyKey = $request->header('Idempotency-Key');
        if (!$idempotencyKey) {
            return $next($request);
        }

        $userId = $request->user()?->id ?? 'guest';
        $cacheKey = 'idempotency:' . $userId . ':' . $idempotencyKey;
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return response()->json(
                $cached['body'],
                $cached['status'],
                ['X-Idempotency-Replayed' => 'true']
            );
        }

        $response = $next($request);

        if ($response->isSuccessful()) {
            Cache::put($cacheKey, [
                'body' => json_decode($response->getContent(), true),
                'status' => $response->getStatusCode(),
            ], 7200); // 2 hours
        }

        return $response;
    }
}
