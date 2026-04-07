<?php
/**
 * Emergency deploy helper — clear caches and seed DB.
 * Access: GET /deploy.php?key=AS24_DEPLOY_x7K9mP2qR5
 * 
 * This file bypasses Laravel routing entirely, so it works
 * even when the route cache is stale.
 */

// Catch all errors including fatal
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Secret key to prevent unauthorized access
$secret = 'AS24_DEPLOY_x7K9mP2qR5';

if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

header('Content-Type: application/json');
$results = [];

// 1. Clear route cache
$cacheDir = __DIR__ . '/../bootstrap/cache';
$routeFiles = glob($cacheDir . '/routes-*.php');
foreach ($routeFiles as $file) {
    if (@unlink($file)) {
        $results[] = "Deleted: " . basename($file);
    } else {
        $results[] = "Failed to delete: " . basename($file);
    }
}

// Also clear the route hash so ensureFreshRouteCache recalculates
$hashFile = __DIR__ . '/../storage/framework/route-hash.txt';
if (file_exists($hashFile)) {
    @unlink($hashFile);
    $results[] = "Deleted route-hash.txt";
}

// 2. Clear config cache
$configCache = $cacheDir . '/config.php';
if (file_exists($configCache) && @unlink($configCache)) {
    $results[] = "Deleted config.php cache";
}

// 3. Clear compiled services
$servicesCache = $cacheDir . '/services.php';
if (file_exists($servicesCache) && @unlink($servicesCache)) {
    $results[] = "Deleted services.php cache";
}

// 4. Bootstrap Laravel if we need to run artisan commands
$laravelBooted = false;
$bootLaravel = function () use (&$laravelBooted, &$results) {
    if ($laravelBooted) return;
    try {
        require __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $laravelBooted = true;
    } catch (\Throwable $e) {
        $results[] = "Laravel bootstrap error: " . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    }
};

// 5. Run database migrations if requested
if (isset($_GET['migrate'])) {
    $bootLaravel();
    if ($laravelBooted) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $results[] = "Migrations run: " . trim(\Illuminate\Support\Facades\Artisan::output());
        } catch (\Exception $e) {
            $results[] = "Migration error: " . $e->getMessage();
        }
    }
}

// 6. Run database seeder if requested
if (isset($_GET['seed'])) {
    $bootLaravel();
    if ($laravelBooted) {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $results[] = "Database seeded: " . trim(\Illuminate\Support\Facades\Artisan::output());
        } catch (\Exception $e) {
            $results[] = "Seed error: " . $e->getMessage();
        }
    }
}

// 7. Clear OPcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    $results[] = "OPcache cleared";
} else {
    $results[] = "OPcache not available";
}

// 8. Diagnostic mode: try to boot Laravel and report any errors
if (isset($_GET['diagnose'])) {
    // Check PHP version and key extensions
    $results[] = "PHP version: " . phpversion();
    $results[] = "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown');
    
    // Check if vendor/autoload.php exists
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    $results[] = "vendor/autoload.php: " . (file_exists($autoloadPath) ? 'EXISTS' : 'MISSING');
    
    // Check latest Laravel log for errors
    $logFile = __DIR__ . '/../storage/logs/laravel.log';
    if (file_exists($logFile)) {
        $logSize = filesize($logFile);
        $results[] = "laravel.log size: " . round($logSize / 1024) . "KB";
        // Read last 2000 chars
        $fh = fopen($logFile, 'r');
        if ($fh && $logSize > 0) {
            fseek($fh, max(0, $logSize - 2000));
            $tail = fread($fh, 2000);
            fclose($fh);
            $results[] = "laravel.log tail:\n" . $tail;
        }
    } else {
        $results[] = "laravel.log: NOT FOUND";
    }
    
    $bootLaravel();
    if ($laravelBooted) {
        $results[] = "Laravel boots OK";
        // Try a simple DB query
        try {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            $results[] = "DB connection OK (driver: " . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) . ")";
        } catch (\Throwable $e) {
            $results[] = "DB connection FAILED: " . $e->getMessage();
        }
        // Check pending migrations
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate:status', ['--no-interaction' => true]);
            $migrationOutput = trim(\Illuminate\Support\Facades\Artisan::output());
            $pending = substr_count($migrationOutput, 'Pending');
            $results[] = "Pending migrations: {$pending}";
            if ($pending > 0) {
                $results[] = "Migration status:\n" . $migrationOutput;
            }
        } catch (\Throwable $e) {
            $results[] = "Migration status check failed: " . $e->getMessage();
        }
        // Check if key tables exist
        $tables = ['settings', 'vehicles', 'users', 'cache', 'dealers', 'reviews', 'disputes', 'safetrade_transactions', 'conversations', 'messages'];
        foreach ($tables as $table) {
            try {
                $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
                $results[] = "Table '{$table}': " . ($exists ? 'EXISTS' : 'MISSING');
            } catch (\Throwable $e) {
                $results[] = "Table '{$table}': CHECK FAILED - " . $e->getMessage();
            }
        }
    } else {
        $results[] = "Laravel could not boot — see error above";
    }
}

echo json_encode([
    'status' => 'ok',
    'results' => $results,
    'timestamp' => date('Y-m-d H:i:s'),
], JSON_PRETTY_PRINT);
