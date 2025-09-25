<?php
/**
 * Get Application Configuration for JavaScript
 * Exposes safe configuration values to the frontend
 */

// Include configuration
require_once __DIR__ . '/../config/config.php';

// Set JSON header
header('Content-Type: application/json');

// Only expose safe configuration values
$config = [
    'appEnv' => APP_ENV,
    'appName' => APP_NAME,
    'appVersion' => APP_VERSION,
    'isTesting' => APP_ENV === 'testing',
    'isDevelopment' => APP_ENV === 'development',
    'isProduction' => APP_ENV === 'production'
];

// Return configuration as JSON
echo json_encode($config);
?>
