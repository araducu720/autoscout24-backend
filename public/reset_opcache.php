<?php
header('Content-Type: application/json');
$result = [];
if (function_exists('opcache_reset')) {
    $result['opcache_reset'] = opcache_reset();
    $result['message'] = 'OPcache reset successfully';
} else {
    $result['message'] = 'opcache_reset not available';
}
echo json_encode($result, JSON_PRETTY_PRINT);
