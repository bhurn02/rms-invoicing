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

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Set JSON content type
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Include database configuration and Database class
    require_once '../config/config.php';
    
    // Get database connection
    $db = Database::getInstance();
    
    // Get property filter from query parameters
    $propertyFilter = isset($_GET['property']) ? $_GET['property'] : null;
    
    // Build the SQL query - using exact column names from database schema
    $sql = "SELECT 
                t.tenant_code,
                t.tenant_name,
                t.real_property_code,
                t.building_code,
                t.unit_no,
                ISNULL(t.terminated, 'N') as terminated,
                p.real_property_name,
                u.meter_number,
                u.unit_type
            FROM m_tenant t
            LEFT JOIN m_real_property p ON t.real_property_code = p.real_property_code
            LEFT JOIN m_units u ON t.real_property_code = u.real_property_code 
                AND t.building_code = u.building_code
                AND t.unit_no = u.unit_no
            WHERE ISNULL(t.terminated, 'N') = 'N'
                AND ISNULL(t.tenant_code,'') <> ''
                AND ISNULL(t.tenant_name,'') <> ''
                AND ISNULL(t.real_property_code,'')<>''
                AND ISNULL(t.unit_no,'')<>''";
    
    $params = array();
    
    // Add property filter if specified
    if ($propertyFilter) {
        $sql .= " AND t.real_property_code = ?";
        $params[] = $propertyFilter;
    }
    
    $sql .= " ORDER BY p.real_property_name, t.unit_no";
    
    // Execute query
    $tenants = $db->query($sql, $params);
    
    // Process results
    $processedTenants = array();
    $properties = array();
    $propertyMap = array();
    
    foreach ($tenants as $tenant) {
        $processedTenant = array(
            'tenant_code' => trim($tenant['tenant_code']),
            'tenant_name' => trim($tenant['tenant_name']),
            'real_property_code' => trim($tenant['real_property_code']),
            'building_code' => trim(isset($tenant['building_code']) ? $tenant['building_code'] : ''),
            'unit_no' => trim($tenant['unit_no']),
            'terminated' => trim(isset($tenant['terminated']) ? $tenant['terminated'] : 'N'),
            'real_property_name' => trim(isset($tenant['real_property_name']) ? $tenant['real_property_name'] : $tenant['real_property_code']),
            'meter_number' => trim(isset($tenant['meter_number']) ? $tenant['meter_number'] : ''),
            'unit_type' => trim(isset($tenant['unit_type']) ? $tenant['unit_type'] : '')
        );
        
        $processedTenants[] = $processedTenant;
        
        // Collect unique properties
        $propCode = $processedTenant['real_property_code'];
        if (!isset($propertyMap[$propCode])) {
            $propertyMap[$propCode] = array(
                'real_property_code' => $propCode,
                'real_property_name' => $processedTenant['real_property_name']
            );
        }
    }
    
    $properties = array_values($propertyMap);
    usort($properties, function($a, $b) {
        return strcasecmp($a['real_property_name'], $b['real_property_name']);
    });
    
    // Log successful retrieval (with error handling)
    try {
        logActivity("Retrieved " . count($processedTenants) . " active tenants for QR generation");
    } catch (Exception $logError) {
        // Log error silently to avoid breaking the API response
        error_log("Logging error: " . $logError->getMessage());
    }
    
    // Return success response
    echo json_encode(array(
        'success' => true,
        'message' => 'Active tenants retrieved successfully',
        'tenants' => $processedTenants,
        'properties' => $properties,
        'count' => count($processedTenants),
        'filtered_by' => $propertyFilter,
        'timestamp' => date('Y-m-d H:i:s')
    ));
    
} catch (Exception $e) {
    // Log error
    error_log('Get Active Tenants API Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Failed to retrieve active tenants',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ));
}
?>
