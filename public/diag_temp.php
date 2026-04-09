<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: application/json');

// Check cached files
$cacheDir = dirname(__DIR__) . '/bootstrap/cache';
$cacheFiles = glob($cacheDir . '/*');

// Get middleware groups from the kernel
$router = $app->make('router');
$middlewareGroups = [];
try {
    $ref = new ReflectionProperty($router, 'middlewareGroups');
    $ref->setAccessible(true);
    $middlewareGroups = $ref->getValue($router);
} catch (\Throwable $e) {
    $middlewareGroups = ['error' => $e->getMessage()];
}

// Check if ForceJsonResponse class exists
$classExists = class_exists(\App\Http\Middleware\ForceJsonResponse::class);

// Check bootstrap/app.php content (relevant parts)
$bootstrapContent = file_get_contents(dirname(__DIR__) . '/bootstrap/app.php');
preg_match_all('/ForceJsonResponse|->api\(|prepend/', $bootstrapContent, $matches);

echo json_encode([
    'cache_files' => array_map('basename', $cacheFiles),
    'middleware_groups' => $middlewareGroups,
    'force_json_class_exists' => $classExists,
    'bootstrap_matches' => $matches[0] ?? [],
    'bootstrap_file_size' => strlen($bootstrapContent),
], JSON_PRETTY_PRINT);
