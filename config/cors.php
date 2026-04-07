<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_merge(
        [
            // Production origins
            'https://www.autoscout24safetrade.com',
            'https://autoscout24safetrade.com',
            'https://adminautoscout.dev',
        ],
        // Localhost only in non-production (via env variable)
        env('APP_ENV', 'production') !== 'production' ? [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:3002',
        ] : [],
        // Additional origins from environment
        env('CORS_ALLOWED_ORIGINS') ? explode(',', env('CORS_ALLOWED_ORIGINS')) : []
    )),

    'allowed_origins_patterns' => [
        // Allow only our team's Vercel preview deployments (scoped to team slug)
        '#^https://autoscout24-frontend-[a-z0-9]+-anemetees-projects\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Content-Type', 'X-Total-Count', 'X-Page-Number'],

    'max_age' => 3600,

    'supports_credentials' => true,

];
