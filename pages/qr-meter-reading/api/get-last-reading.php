<?php
/**
 * RMS QR Meter Reading System - Get Last Reading API
 * Retrieves the most recent saved reading for a property/unit combination
 */

// Set content type to JSON
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../config/config.php';
require_once '../auth/auth.php';
require_once '../auth/permission-check.php';

// Validate API permissions (includes authentication check)
validateAPIPermissions();

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Get query parameters
    $propertyCode = isset($_GET['propertyCode']) ? trim($_GET['propertyCode']) : '';
    $unitNo = isset($_GET['unitNo']) ? trim($_GET['unitNo']) : '';
    
    // Validate required parameters
    if (empty($propertyCode) || empty($unitNo)) {
        throw new Exception('Property code and unit number are required');
    }
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Use the existing vw_TenantReading view which is designed for this purpose
    $sql = "SELECT TOP 1 
                   tenant_code,
                   current_reading,
                   prev_reading,
                   reading_date,
                   reading_date_from,
                   reading_date_to,
                   billing_from,
                   billing_to,
                   usage,
                   remarks,
                   tenant_name,
                   property_name
            FROM vw_TenantReading
            WHERE property_code = ? 
              AND unit_no = ?
            ORDER BY ISNULL(reading_date, date_created) DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$propertyCode, $unitNo]);
    $lastReading = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastReading) {
        // Return the last reading data with trimmed string fields
        echo json_encode([
            'success' => true,
            'data' => [
                'tenantCode' => trim($lastReading['tenant_code']),
                'tenantName' => trim($lastReading['tenant_name']),
                'propertyName' => trim($lastReading['property_name']),
                'prevReading' => $lastReading['prev_reading'] ? floatval($lastReading['prev_reading']) : null,
                'currentReading' => floatval($lastReading['current_reading']),
                'readingDate' => $lastReading['reading_date'],
                'dateFrom' => $lastReading['reading_date_from'],
                'dateTo' => $lastReading['reading_date_to'],
                'billingDateFrom' => $lastReading['billing_from'],
                'billingDateTo' => $lastReading['billing_to'],
                'usage' => $lastReading['usage'],
                'remarks' => trim($lastReading['remarks'])
            ]
        ]);
    } else {
        // No previous reading found
        echo json_encode([
            'success' => true,
            'data' => null,
            'message' => 'No previous reading found for this property/unit'
        ]);
    }
    
} catch (Exception $e) {
    // Log error
    error_log("QR Get Last Reading Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving last reading: ' . $e->getMessage()
    ]);
}
?>
