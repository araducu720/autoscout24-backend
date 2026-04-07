<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Remove information disclosure headers aggressively
        // nginx may re-add Server header, so we use multiple strategies
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('X-Turbo-Charged-By');
        
        // Set Server to empty to override nginx's value
        $response->headers->set('Server', '');
        
        // PHP-level header removal (works after response is sent)
        header_remove('Server');
        header_remove('X-Powered-By');
        header_remove('X-Turbo-Charged-By');
        
        // Override with empty value at PHP level
        @header('Server: ', true);
        @header_remove('Server');

        // Register a shutdown function to remove Server header as late as possible
        register_shutdown_function(function () {
            if (!headers_sent()) {
                header_remove('Server');
                header_remove('X-Powered-By');
            }
        });

        // Prevent clickjacking (set once, not duplicate)
        if (!$response->headers->has('X-Frame-Options')) {
            $response->headers->set('X-Frame-Options', 'DENY');
        } else {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        // Prevent MIME type sniffing (set once, not duplicate)
        if (!$response->headers->has('X-Content-Type-Options')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        } else {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // Enable XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=(self)'
        );

        // Strict Transport Security (only in production)
        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content Security Policy
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; img-src 'self' data: blob: https:; font-src 'self' data: https://fonts.bunny.net; connect-src 'self' https://www.autoscout24safetrade.com https://adminautoscout.dev; worker-src 'self' blob:; frame-ancestors 'none'; base-uri 'self'; form-action 'self'"
        );

        // Prevent cross-domain policy file requests
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        return $response;
    }
}
