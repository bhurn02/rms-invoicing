<?php
/**
 * RMS QR Meter Reading System - Get Tenant by Unit API
 * Resolves tenant information for a property/unit combination
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
    
    // Primary: Look for active tenant
    $tenantSql = "SELECT t.tenant_code, t.tenant_name, t.real_property_code, t.unit_no,
                          t.actual_move_in_date, t.contract_expiry_date,
                          p.real_property_name, u.meter_number, u.unit_type
                   FROM m_tenant t
                   INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                   LEFT JOIN m_units u ON t.real_property_code = u.real_property_code 
                                     AND t.unit_no = u.unit_no
                   WHERE t.real_property_code = ? 
                     AND t.unit_no = ? 
                     AND ISNULL(t.terminated,'N') = 'N'
                   ORDER BY t.actual_move_in_date DESC";
    
    $tenantStmt = $pdo->prepare($tenantSql);
    $tenantStmt->execute([$propertyCode, $unitNo]);
    $tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback: If no active tenant found, get last active tenant from vw_TenantReading
    if (!$tenant) {
        $fallbackSql = "SELECT TOP 1 
                               tr.tenant_code,
                               tr.tenant_name,
                               tr.real_property_code,
                               tr.unit_no,
                               tr.actual_move_in_date,
                               tr.contract_expiry_date,
                               tr.real_property_name,
                               tr.meter_number,
                               tr.unit_type
                        FROM vw_TenantReading tr
                        WHERE tr.real_property_code = ? 
                          AND tr.unit_no = ?
                        ORDER BY tr.date_created DESC";
        
        $fallbackStmt = $pdo->prepare($fallbackSql);
        $fallbackStmt->execute([$propertyCode, $unitNo]);
        $tenant = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tenant) {
            // Return empty result if no tenant found
            echo json_encode([
                'success' => true,
                'data' => null,
                'message' => 'No tenant found for this property/unit combination'
            ]);
            exit();
        }
    }
    
    // Return tenant information with trimmed string fields
    echo json_encode([
        'success' => true,
        'data' => [
            'tenantCode' => trim($tenant['tenant_code']),
            'tenantName' => trim($tenant['tenant_name']),
            'realPropertyCode' => trim($tenant['real_property_code']),
            'realPropertyName' => trim($tenant['real_property_name']),
            'unitNo' => trim($tenant['unit_no']),
            'meterNumber' => trim($tenant['meter_number']),
            'unitType' => trim($tenant['unit_type']),
            'actualMoveInDate' => $tenant['actual_move_in_date'],
            'contractExpiryDate' => $tenant['contract_expiry_date'],
            'isActive' => true
        ]
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log("QR Get Tenant by Unit Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error retrieving tenant information: ' . $e->getMessage()
    ]);
}
?>
