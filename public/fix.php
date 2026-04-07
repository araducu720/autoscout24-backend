<?php
/**
 * Fix cached package manifest — removes dev-only packages (like laravel/sail)
 * that aren't installed in production.
 * 
 * Usage: GET /fix.php?key=AS24_DEPLOY_x7K9mP2qR5
 */

$secret = 'AS24_DEPLOY_x7K9mP2qR5';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

header('Content-Type: application/json');
$results = [];

$cacheDir = __DIR__ . '/../bootstrap/cache';

// 1. Fix packages.php — remove packages whose source doesn't exist
$packagesFile = $cacheDir . '/packages.php';
if (file_exists($packagesFile)) {
    $packages = require $packagesFile;
    $fixed = [];
    $removed = [];
    
    foreach ($packages as $packageName => $config) {
        // Check if the package directory actually exists
        $packageDir = __DIR__ . '/../vendor/' . $packageName;
        if (is_dir($packageDir)) {
            $fixed[$packageName] = $config;
        } else {
            $removed[] = $packageName;
        }
    }
    
    if (!empty($removed)) {
        // Write corrected packages.php
        $content = '<?php return ' . var_export($fixed, true) . ';' . PHP_EOL;
        file_put_contents($packagesFile, $content);
        $results[] = 'Removed missing packages from packages.php: ' . implode(', ', $removed);
    } else {
        $results[] = 'packages.php is clean, no missing packages';
    }
} else {
    $results[] = 'packages.php not found';
}

// 2. Delete services.php if it exists (will be regenerated cleanly)
$servicesFile = $cacheDir . '/services.php';
if (file_exists($servicesFile)) {
    @unlink($servicesFile);
    $results[] = 'Deleted stale services.php';
}

// 3. Delete events.php cache
$eventsFile = $cacheDir . '/events.php';
if (file_exists($eventsFile)) {
    @unlink($eventsFile);
    $results[] = 'Deleted events.php cache';
}

// 4. Try to bootstrap Laravel now
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $results[] = 'Laravel bootstrapped successfully!';
    
    // 5. Run package:discover to regenerate caches properly
    try {
        \Illuminate\Support\Facades\Artisan::call('package:discover');
        $results[] = 'package:discover: ' . trim(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Throwable $e) {
        $results[] = 'package:discover failed: ' . $e->getMessage();
    }
    
    // 6. Cache config and routes for production
    try {
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        $results[] = 'config:cache: OK';
    } catch (\Throwable $e) {
        $results[] = 'config:cache failed: ' . $e->getMessage();
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('route:cache');
        $results[] = 'route:cache: OK';
    } catch (\Throwable $e) {
        $results[] = 'route:cache failed: ' . $e->getMessage();
    }
    
} catch (\Throwable $e) {
    $results[] = 'Laravel bootstrap failed: ' . get_class($e) . ': ' . $e->getMessage();
    $results[] = 'File: ' . $e->getFile() . ':' . $e->getLine();
}

// 7. Verify cache files
$results[] = 'Cache files: ' . implode(', ', array_map('basename', glob($cacheDir . '/*')));

echo json_encode([
    'status' => empty(array_filter($results, fn($r) => str_contains($r, 'failed'))) ? 'ok' : 'partial',
    'results' => $results,
    'timestamp' => date('Y-m-d H:i:s'),
], JSON_PRETTY_PRINT);
