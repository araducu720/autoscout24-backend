<?php
// Quick diagnostic — bypasses Laravel entirely
// Access: /check.php?key=AS24_DEPLOY_x7K9mP2qR5
if (($_GET['key'] ?? '') !== 'AS24_DEPLOY_x7K9mP2qR5') {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: application/json');
$out = [];

$out['php_version'] = phpversion();
$out['server'] = $_SERVER['SERVER_SOFTWARE'] ?? 'unknown';
$out['timestamp'] = date('Y-m-d H:i:s');

// Check key files exist
$out['files'] = [
    'vendor/autoload.php' => file_exists(__DIR__ . '/../vendor/autoload.php'),
    'bootstrap/app.php' => file_exists(__DIR__ . '/../bootstrap/app.php'),
    '.env' => file_exists(__DIR__ . '/../.env'),
    'deploy.php mtime' => date('Y-m-d H:i:s', filemtime(__DIR__ . '/deploy.php')),
];

// Check latest git commit if possible
$gitHead = __DIR__ . '/../.git/refs/heads/main';
if (file_exists($gitHead)) {
    $out['git_commit'] = trim(file_get_contents($gitHead));
} else {
    $out['git_commit'] = 'unknown (no .git)';
}

// Read last 3000 chars of laravel.log
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $size = filesize($logFile);
    $out['log_size_kb'] = round($size / 1024);
    $fh = fopen($logFile, 'r');
    if ($fh && $size > 0) {
        fseek($fh, max(0, $size - 3000));
        $out['log_tail'] = fread($fh, 3000);
        fclose($fh);
    }
} else {
    $out['log_tail'] = 'NO LOG FILE';
}

// Try booting Laravel
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $out['laravel_boot'] = 'OK';
    
    // Check DB
    try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $out['db'] = 'OK (' . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) . ')';
    } catch (\Throwable $e) {
        $out['db'] = 'FAILED: ' . $e->getMessage();
    }
} catch (\Throwable $e) {
    $out['laravel_boot'] = 'FAILED';
    $out['boot_error'] = get_class($e) . ': ' . $e->getMessage();
    $out['boot_error_file'] = $e->getFile() . ':' . $e->getLine();
    $out['boot_error_trace'] = array_slice(
        array_map(fn($f) => ($f['file'] ?? '?') . ':' . ($f['line'] ?? '?') . ' ' . ($f['class'] ?? '') . ($f['type'] ?? '') . ($f['function'] ?? ''), $e->getTrace()),
        0, 10
    );
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
