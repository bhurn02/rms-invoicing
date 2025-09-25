<?php
/**
 * Ping endpoint for connection stability testing
 * Used by QR Meter Reading System to verify stable connection before auto-sync
 */

// Set headers for lightweight response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Simple response to indicate server is reachable
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => 'QR Meter Reading System'
]);
?>
