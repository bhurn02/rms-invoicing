<?php
/**
 * RMS QR Meter Reading System - Authentication Check
 * Simple page to check authentication status
 */

require_once 'auth.php';

// Set content type to JSON
header('Content-Type: application/json');

$response = [
    'authenticated' => isAuthenticated(),
    'user_id' => getCurrentUserId(),
    'username' => getCurrentUsername(),
    'company_code' => getCurrentCompanyCode(),
    'session_info' => getSessionInfo()
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
