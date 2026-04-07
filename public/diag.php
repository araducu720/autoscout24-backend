<?php
/**
 * Standalone diagnostic file - does NOT require Laravel.
 * Tests each bootstrap step independently.
 * Access: GET /diag.php?key=AS24_DEPLOY_x7K9mP2qR5
 */

$secret = 'AS24_DEPLOY_x7K9mP2qR5';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

header('Content-Type: application/json');
$results = [];
$results['php_version'] = PHP_VERSION;
$results['server_software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';
$results['document_root'] = $_SERVER['DOCUMENT_ROOT'] ?? 'unknown';

// Check .env exists
$envFile = __DIR__ . '/../.env';
$results['env_exists'] = file_exists($envFile);
if ($results['env_exists']) {
    $envContent = file_get_contents($envFile);
    // Check critical values (don't expose secrets)
    $results['env_has_app_key'] = (bool) preg_match('/^APP_KEY=base64:.+/m', $envContent);
    $results['env_app_env'] = preg_match('/^APP_ENV=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_app_debug'] = preg_match('/^APP_DEBUG=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_db_connection'] = preg_match('/^DB_CONNECTION=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_db_host'] = preg_match('/^DB_HOST=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_db_database'] = preg_match('/^DB_DATABASE=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_cache_store'] = preg_match('/^CACHE_STORE=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
    $results['env_session_driver'] = preg_match('/^SESSION_DRIVER=(.+)/m', $envContent, $m) ? trim($m[1]) : 'not set';
} else {
    $results['env_error'] = 'NO .env FILE FOUND - this is the likely cause of the 500 error';
}

// Check vendor directory
$results['vendor_autoload_exists'] = file_exists(__DIR__ . '/../vendor/autoload.php');

// Check bootstrap
$results['bootstrap_app_exists'] = file_exists(__DIR__ . '/../bootstrap/app.php');

// Check cache directory
$cacheDir = __DIR__ . '/../bootstrap/cache';
$results['cache_dir_exists'] = is_dir($cacheDir);
$results['cache_dir_writable'] = is_writable($cacheDir);
$results['cache_files'] = is_dir($cacheDir) ? array_map('basename', glob($cacheDir . '/*')) : [];

// Check storage directory
$storageDir = __DIR__ . '/../storage';
$results['storage_dir_writable'] = is_writable($storageDir);
$results['storage_logs_writable'] = is_writable($storageDir . '/logs');
$results['storage_framework_writable'] = is_writable($storageDir . '/framework');

// Check PHP extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$results['php_extensions'] = [];
foreach ($requiredExtensions as $ext) {
    $results['php_extensions'][$ext] = extension_loaded($ext);
}

// Check redis extension (might be needed)
$results['php_extensions']['redis'] = extension_loaded('redis');

// Try raw PDO connection
if ($results['env_exists']) {
    $dbHost = preg_match('/^DB_HOST=(.+)/m', $envContent, $m) ? trim($m[1]) : '127.0.0.1';
    $dbPort = preg_match('/^DB_PORT=(.+)/m', $envContent, $m) ? trim($m[1]) : '3306';
    $dbName = preg_match('/^DB_DATABASE=(.+)/m', $envContent, $m) ? trim($m[1]) : '';
    $dbUser = preg_match('/^DB_USERNAME=(.+)/m', $envContent, $m) ? trim($m[1]) : '';
    $dbPass = preg_match('/^DB_PASSWORD=(.+)/m', $envContent, $m) ? trim($m[1]) : '';
    
    try {
        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName}", $dbUser, $dbPass, [
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $results['db_connection'] = 'OK';
        
        // Check tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $results['db_table_count'] = count($tables);
        
        // Check for key tables
        $keyTables = ['users', 'vehicles', 'settings', 'cache', 'migrations', 'price_alerts', 'notification_preferences'];
        $results['db_tables'] = [];
        foreach ($keyTables as $t) {
            $results['db_tables'][$t] = in_array($t, $tables);
        }
        
        // Check migration status
        if (in_array('migrations', $tables)) {
            $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT 5");
            $results['db_latest_migrations'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (PDOException $e) {
        $results['db_connection'] = 'FAILED: ' . $e->getMessage();
    }
}

// Try to boot Laravel (in error-catching wrapper)
$results['laravel_boot'] = 'not attempted';
try {
    require __DIR__ . '/../vendor/autoload.php';
    $results['laravel_autoload'] = 'OK';
    
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $results['laravel_app_created'] = 'OK';
    
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $results['laravel_kernel_created'] = 'OK';
    
    $kernel->bootstrap();
    $results['laravel_boot'] = 'OK';
    
    // Check Laravel logs for recent errors
    $logFile = $storageDir . '/logs/laravel.log';
    if (file_exists($logFile)) {
        $logSize = filesize($logFile);
        $results['laravel_log_size'] = $logSize;
        // Read last 2000 chars
        if ($logSize > 0) {
            $fp = fopen($logFile, 'r');
            fseek($fp, max(0, $logSize - 2000));
            $lastLog = fread($fp, 2000);
            fclose($fp);
            // Extract last error
            if (preg_match_all('/\[\d{4}-\d{2}-\d{2}.*?\] .+?ERROR.+/m', $lastLog, $matches)) {
                $results['laravel_last_error'] = end($matches[0]);
            }
        }
    }
} catch (\Throwable $e) {
    $results['laravel_boot'] = 'FAILED';
    $results['laravel_error'] = $e->getMessage();
    $results['laravel_error_file'] = $e->getFile() . ':' . $e->getLine();
    $results['laravel_error_trace'] = array_slice(
        array_map(function ($t) {
            return ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?') . ' ' . ($t['class'] ?? '') . ($t['type'] ?? '') . ($t['function'] ?? '');
        }, $e->getTrace()),
        0,
        10
    );
}

// FIX MODE: repair packages.php by removing dev-only packages not installed
if (isset($_GET['fix'])) {
    $packagesFile = $cacheDir . '/packages.php';
    if (file_exists($packagesFile)) {
        $packages = require $packagesFile;
        $removed = [];
        $cleaned = [];
        foreach ($packages as $name => $config) {
            $packageDir = __DIR__ . '/../vendor/' . $name;
            if (is_dir($packageDir)) {
                $cleaned[$name] = $config;
            } else {
                $removed[] = $name;
            }
        }
        if (!empty($removed)) {
            $content = '<?php return ' . var_export($cleaned, true) . ';' . PHP_EOL;
            file_put_contents($packagesFile, $content);
            $results['fix_packages'] = 'Removed: ' . implode(', ', $removed);
        } else {
            $results['fix_packages'] = 'No missing packages to remove';
        }
    }
    
    // Delete stale services.php
    $servicesFile = $cacheDir . '/services.php';
    if (file_exists($servicesFile)) {
        @unlink($servicesFile);
        $results['fix_services'] = 'Deleted stale services.php';
    }
    
    // Try to reboot Laravel after fix
    try {
        // Need fresh app instance
        $app2 = require __DIR__ . '/../bootstrap/app.php';
        $kernel2 = $app2->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel2->bootstrap();
        $results['fix_boot'] = 'Laravel boots OK after fix!';
        
        // Regenerate caches
        \Illuminate\Support\Facades\Artisan::call('package:discover');
        $results['fix_discover'] = trim(\Illuminate\Support\Facades\Artisan::output());
        \Illuminate\Support\Facades\Artisan::call('config:cache');
        $results['fix_config_cache'] = 'OK';
        \Illuminate\Support\Facades\Artisan::call('route:cache');
        $results['fix_route_cache'] = 'OK';
    } catch (\Throwable $e) {
        $results['fix_boot'] = 'FAILED: ' . $e->getMessage();
    }
}

// REDISTRIBUTE MODE: even out vehicle distribution across dealers
if (isset($_GET['redistribute'])) {
    // Use raw PDO to redistribute vehicles
    if (isset($pdo)) {
        try {
            // Get all active dealer user_ids
            $stmt = $pdo->query("SELECT user_id FROM dealers WHERE is_active = 1 ORDER BY id");
            $dealerUserIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $dealerCount = count($dealerUserIds);
            
            // Get all vehicle ids
            $stmt = $pdo->query("SELECT id FROM vehicles ORDER BY id");
            $vehicleIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $vehicleCount = count($vehicleIds);
            
            if ($dealerCount > 0 && $vehicleCount > 0) {
                // Shuffle for variety
                shuffle($vehicleIds);
                
                // Round-robin assignment
                $pdo->beginTransaction();
                foreach ($vehicleIds as $index => $vehicleId) {
                    $dealerIndex = $index % $dealerCount;
                    $userId = $dealerUserIds[$dealerIndex];
                    $stmt = $pdo->prepare("UPDATE vehicles SET user_id = ? WHERE id = ?");
                    $stmt->execute([$userId, $vehicleId]);
                }
                $pdo->commit();
                
                $perDealer = floor($vehicleCount / $dealerCount);
                $remainder = $vehicleCount % $dealerCount;
                $results['redistribute'] = "Distributed {$vehicleCount} vehicles across {$dealerCount} dealers ({$perDealer}-" . ($perDealer + 1) . " each)";
                
                // Sample: top 5 dealers by vehicle count
                $stmt = $pdo->query("SELECT d.company_name, COUNT(v.id) as cnt FROM vehicles v JOIN dealers d ON v.user_id = d.user_id GROUP BY d.id, d.company_name ORDER BY cnt DESC LIMIT 5");
                $results['redistribute_top5'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $results['redistribute'] = "No dealers ({$dealerCount}) or vehicles ({$vehicleCount})";
            }
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $results['redistribute_error'] = $e->getMessage();
        }
    } else {
        $results['redistribute'] = 'No database connection available';
    }
}

// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    $results['opcache_cleared'] = true;
} else {
    $results['opcache_cleared'] = false;
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
