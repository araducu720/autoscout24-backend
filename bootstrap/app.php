<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Token-based API authentication (Bearer tokens)
        // statefulApi() removed — frontend is on a different domain,
        // uses localStorage tokens, not cookie/session auth.

        $middleware->api(prepend: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        ]);

        // Global middleware - security headers on all responses
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\CompressResponse::class);

        // Named middleware aliases for route-level usage
        $middleware->alias([
            'audit'       => \App\Http\Middleware\AuditLogMiddleware::class,
            'idempotent'  => \App\Http\Middleware\IdempotencyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render consistent JSON error responses for API requests
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });

        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'The requested endpoint could not be found.'], 404);
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
                ], 429);
            }
        });
    })->create();
