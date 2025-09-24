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

// Include required files
require_once '../config/config.php';
require_once '../auth/auth.php';
require_once '../auth/permission-check.php';

// Validate API permissions (includes authentication check)
validateAPIPermissions();

// Set JSON content type
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    
    // Get database connection
    $db = Database::getInstance();
    
    // Get tenant code from request
    $tenantCode = isset($_GET['tenant_code']) ? $_GET['tenant_code'] : (isset($_POST['tenant_code']) ? $_POST['tenant_code'] : null);
    
    if (!$tenantCode) {
        throw new Exception('Tenant code is required');
    }
    
    // Build the SQL query
    $sql = "SELECT 
                t.tenant_code,
                t.tenant_name,
                t.real_property_code,
                t.building_code,
                t.unit_no,
                t.terminated,
                p.real_property_name,
                u.meter_number,
                u.unit_type
            FROM m_tenant t
            LEFT JOIN m_real_property p ON t.real_property_code = p.real_property_code
            LEFT JOIN m_units u ON t.real_property_code = u.real_property_code 
                AND t.building_code = u.building_code
                AND t.unit_no = u.unit_no
            WHERE t.tenant_code = ?";
    
    // Execute query
    $tenant = $db->querySingle($sql, [$tenantCode]);
    
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
    
    // Process tenant data
    $processedTenant = [
        'tenant_code' => trim($tenant['tenant_code']),
        'tenant_name' => trim($tenant['tenant_name']),
        'real_property_code' => trim($tenant['real_property_code']),
        'building_code' => trim(isset($tenant['building_code']) ? $tenant['building_code'] : ''),
        'unit_no' => trim($tenant['unit_no']),
        'terminated' => trim(isset($tenant['terminated']) ? $tenant['terminated'] : 'N'),
        'real_property_name' => trim(isset($tenant['real_property_name']) ? $tenant['real_property_name'] : $tenant['real_property_code']),
        'meter_number' => trim(isset($tenant['meter_number']) ? $tenant['meter_number'] : ''),
        'unit_type' => trim(isset($tenant['unit_type']) ? $tenant['unit_type'] : '')
    ];
    
    // Generate QR data for this tenant
    $qrData = [
        'propertyId' => $processedTenant['real_property_code'],
        'unitNumber' => $processedTenant['unit_no'],
        'meterNumber' => $processedTenant['meter_number'] ?: null,
        'tenantCode' => $processedTenant['tenant_code'],
        'propertyName' => $processedTenant['real_property_name']
    ];
    
    $qrContent = json_encode($qrData);
    
    // Validate tenant data
    $isValid = !empty($processedTenant['real_property_code']) && 
               !empty($processedTenant['unit_no']) && 
               !empty($processedTenant['tenant_code']);
    
    // Log successful retrieval
    logActivity("Retrieved tenant details for {$tenantCode}");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Tenant details retrieved successfully',
        'tenant' => $processedTenant,
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
?>
