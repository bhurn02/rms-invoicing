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
    
    // Build the SQL query - list all property units with tenant information
    $sql = "SELECT 
                u.real_property_code,
                u.building_code,
                u.unit_no,
                ISNULL(u.meter_number, '') AS meter_number,
                ISNULL(u.unit_type, '') AS unit_type,
                ISNULL(p.real_property_name, u.real_property_code) AS real_property_name,
                -- Prefer active tenant; fallback to last from reading view
                ISNULL(t_active.tenant_code, tr_last.tenant_code) AS tenant_code,
                ISNULL(t_active.tenant_name, tr_last.tenant_name) AS tenant_name,
                CASE WHEN t_active.tenant_code IS NOT NULL THEN 'N' ELSE 'Y' END AS terminated
            FROM m_units u
            LEFT JOIN m_real_property p 
                ON p.real_property_code = u.real_property_code
             LEFT JOIN (
                 -- Get the most recent active tenant per unit (sorted by contract_eff_date DESC, then date_updated DESC)
                 SELECT t.tenant_code, t.tenant_name, t.real_property_code, t.building_code, t.unit_no
                 FROM m_tenant t
                 WHERE ISNULL(t.terminated,'N') = 'N'
                     AND t.contract_eff_date = (
                         SELECT MAX(t2.contract_eff_date)
                         FROM m_tenant t2
                         WHERE t2.real_property_code = t.real_property_code
                             AND t2.building_code = t.building_code
                             AND t2.unit_no = t.unit_no
                             AND ISNULL(t2.terminated,'N') = 'N'
                     )
                     AND t.date_updated = (
                         SELECT MAX(t3.date_updated)
                         FROM m_tenant t3
                         WHERE t3.real_property_code = t.real_property_code
                             AND t3.building_code = t.building_code
                             AND t3.unit_no = t.unit_no
                             AND ISNULL(t3.terminated,'N') = 'N'
                             AND t3.contract_eff_date = t.contract_eff_date
                     )
             ) t_active
                 ON t_active.real_property_code = u.real_property_code
                 AND t_active.building_code = u.building_code
                 AND t_active.unit_no = u.unit_no
             OUTER APPLY (
                 -- Fallback: Get the most recent tenant (active or terminated) per unit
                 SELECT TOP 1 t.tenant_code, t.tenant_name
                 FROM m_tenant t
                 WHERE t.real_property_code = u.real_property_code
                     AND t.building_code = u.building_code
                     AND t.unit_no = u.unit_no
                 ORDER BY t.contract_eff_date DESC, t.date_updated DESC
             ) tr_last
            WHERE ISNULL(u.real_property_code,'') <> ''
                AND ISNULL(u.unit_no,'') <> ''";
    
    $params = array();
    
    // Add property filter if specified
    if ($propertyFilter) {
        $sql .= " AND u.real_property_code = ?";
        $params[] = $propertyFilter;
    }
    
    $sql .= " ORDER BY p.real_property_name, u.unit_no";
    
    // Execute query
    $tenants = $db->query($sql, $params);
    
    // Process results
    $processedTenants = array();
    $properties = array();
    $propertyMap = array();
    
    foreach ($tenants as $unit) {
        $processedTenant = array(
            'tenant_code' => trim(isset($unit['tenant_code']) ? $unit['tenant_code'] : ''),
            'tenant_name' => trim(isset($unit['tenant_name']) ? $unit['tenant_name'] : ''),
            'real_property_code' => trim($unit['real_property_code']),
            'building_code' => trim(isset($unit['building_code']) ? $unit['building_code'] : ''),
            'unit_no' => trim($unit['unit_no']),
            'terminated' => trim(isset($unit['terminated']) ? $unit['terminated'] : 'Y'),
            'real_property_name' => trim(isset($unit['real_property_name']) ? $unit['real_property_name'] : $unit['real_property_code']),
            'meter_number' => trim(isset($unit['meter_number']) ? $unit['meter_number'] : ''),
            'unit_type' => trim(isset($unit['unit_type']) ? $unit['unit_type'] : '')
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
        logActivity("Retrieved " . count($processedTenants) . " property units for QR generation");
    } catch (Exception $logError) {
        // Log error silently to avoid breaking the API response
        error_log("Logging error: " . $logError->getMessage());
    }
    
    // Return success response
    echo json_encode(array(
        'success' => true,
        'message' => 'Property units retrieved successfully',
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
        'message' => 'Failed to retrieve property units',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ));
}
?>
