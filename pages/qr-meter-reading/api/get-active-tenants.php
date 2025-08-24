<?php
/**
 * Get Active Tenants API Endpoint
 * Retrieves active tenants with property information for batch QR generation
 * 
 * Returns JSON response with tenant data including:
 * - tenant_code, tenant_name, real_property_code, unit_no
 * - real_property_name for display purposes
 * - Filtered for active tenants only
 */

// Include authentication check
require_once '../auth/auth.php';

// Set JSON content type
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Include database configuration and TenantQRGenerator class
    require_once '../config/config.php';
    require_once 'TenantQRGenerator.php';
    
    // Check if database connection is available
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection not available');
    }
    
    // Initialize TenantQRGenerator
    $qrGenerator = new TenantQRGenerator($conn);
    
    // Get property filter from query parameters
    $propertyFilter = $_GET['property'] ?? null;
    
    // Retrieve active tenants
    $result = $qrGenerator->getActiveTenants($propertyFilter);
    
    // Log successful retrieval
    error_log("Retrieved " . $result['count'] . " active tenants for QR generation by user: " . $_SESSION['user_name']);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Active tenants retrieved successfully',
        'tenants' => $result['tenants'],
        'properties' => $result['properties'],
        'count' => $result['count'],
        'filtered_by' => $propertyFilter,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Get Active Tenants API Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve active tenants',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// Close database connection if it exists
if (isset($conn) && $conn) {
    sqlsrv_close($conn);
}
?>
