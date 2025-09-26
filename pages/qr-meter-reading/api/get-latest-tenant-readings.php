<?php
/**
 * Get Latest Tenant Readings API Endpoint
 * Phase 9: Cache-first tenant resolution system
 * Returns data from vw_LatestTenantReadings for comprehensive cache initialization
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
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Query to get latest tenant readings from vw_LatestTenantReadings
    $sql = "
        SELECT 
            property_code,
            unit_no,
            property_name,
            tenant_code,
            tenant_name,
            terminated,
            current_reading,
            prev_reading,
            usage,
            reading_date,
            billing_from,
            billing_to,
            reading_date_from,
            reading_date_to,
            remarks,
            unit_desc,
            date_created
        FROM vw_LatestTenantReadings
        ORDER BY property_code, unit_no
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform data for frontend consumption
    $transformedReadings = array_map(function($reading) {
        return [
            'property_code' => $reading['property_code'],
            'unit_no' => $reading['unit_no'],
            'property_name' => $reading['property_name'],
            'tenant_code' => $reading['tenant_code'],
            'tenant_name' => $reading['tenant_name'],
            'terminated' => $reading['terminated'],
            'current_reading' => (float)$reading['current_reading'],
            'prev_reading' => (float)$reading['prev_reading'],
            'usage' => (float)$reading['usage'],
            'reading_date' => $reading['reading_date'],
            'billing_from' => $reading['billing_from'],
            'billing_to' => $reading['billing_to'],
            'reading_date_from' => $reading['reading_date_from'],
            'reading_date_to' => $reading['reading_date_to'],
            'remarks' => $reading['remarks'],
            'unit_desc' => $reading['unit_desc'],
            'date_created' => $reading['date_created']
        ];
    }, $readings);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $transformedReadings,
        'count' => count($transformedReadings),
        'timestamp' => date('Y-m-d H:i:s'),
        'source' => 'vw_LatestTenantReadings'
    ]);
    
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // General error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
