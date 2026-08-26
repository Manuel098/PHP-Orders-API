<?php

header('Content-Type: application/json');

echo json_encode([
    'message' => 'PHP API is running on port: '.getenv('APP_PORT'),
    'php_version' => PHP_VERSION
]);