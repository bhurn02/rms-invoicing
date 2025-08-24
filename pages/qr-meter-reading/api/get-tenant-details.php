<?php
/**
 * Get Tenant Details API Endpoint
 * Retrieves specific tenant information for QR code generation
 * 
 * Expected Parameters:
 * - tenant_code: The tenant code to lookup
 * 
 * Returns JSON response with detailed tenant information
 */

// Include authentication check
require_once '../auth/auth.php';

// Set JSON content type
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Include database configuration and TenantQRGenerator class
    require_once '../config/config.php';
    require_once 'TenantQRGenerator.php';
    
    // Check if database connection is available
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection not available');
    }
    
    // Get tenant code from request
    $tenantCode = $_GET['tenant_code'] ?? $_POST['tenant_code'] ?? null;
    
    if (!$tenantCode) {
        throw new Exception('Tenant code is required');
    }
    
    // Initialize TenantQRGenerator
    $qrGenerator = new TenantQRGenerator($conn);
    
    // Retrieve tenant details
    $tenant = $qrGenerator->getTenantDetails($tenantCode);
    
    if (!$tenant) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Tenant not found',
            'tenant_code' => $tenantCode,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Generate QR data for this tenant
    $qrData = $qrGenerator->generateQRData($tenant);
    $qrContent = $qrGenerator->formatQRContent($tenant);
    
    // Validate tenant data
    $isValid = $qrGenerator->validateTenantData($tenant);
    
    // Log successful retrieval
    error_log("Retrieved tenant details for {$tenantCode} by user: " . $_SESSION['user_name']);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Tenant details retrieved successfully',
        'tenant' => $tenant,
        'qr_data' => $qrData,
        'qr_content' => $qrContent,
        'is_valid' => $isValid,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Get Tenant Details API Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve tenant details',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// Close database connection if it exists
if (isset($conn) && $conn) {
    sqlsrv_close($conn);
}
?>
