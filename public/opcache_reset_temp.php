<?php
header('Content-Type: application/json');
echo json_encode(['reset' => function_exists('opcache_reset') ? opcache_reset() : 'N/A']);
