<?php
header('Content-Type: application/json');
$data = [
    'git_log' => trim(shell_exec('cd ' . dirname(__DIR__) . ' && git log --oneline -5 2>&1')),
    'git_remote' => trim(shell_exec('cd ' . dirname(__DIR__) . ' && git remote -v 2>&1')),
    'middleware_exists' => file_exists(dirname(__DIR__) . '/app/Http/Middleware/ForceJsonResponse.php'),
    'bootstrap_has_force_json' => str_contains(file_get_contents(dirname(__DIR__) . '/bootstrap/app.php'), 'ForceJsonResponse'),
    'cached_config_exists' => file_exists(dirname(__DIR__) . '/bootstrap/cache/config.php'),
    'route_cache_exists' => file_exists(dirname(__DIR__) . '/bootstrap/cache/routes-v7.php'),
    'php_version' => PHP_VERSION,
    'opcache_enabled' => function_exists('opcache_get_status') ? opcache_get_status(false)['opcache_enabled'] ?? false : 'N/A',
];
echo json_encode($data, JSON_PRETTY_PRINT);
