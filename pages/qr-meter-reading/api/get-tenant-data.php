<?php
/**
 * Get Tenant Data API Endpoint
 * Phase 9: Cache-first tenant resolution system
 * Returns tenant information for property/unit combination with fallback strategies
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
    // Get request parameters
    $propertyCode = isset($_GET['propertyCode']) ? trim($_GET['propertyCode']) : '';
    $unitNo = isset($_GET['unitNo']) ? trim($_GET['unitNo']) : '';
    
    // Validate required parameters
    if (empty($propertyCode) || empty($unitNo)) {
        throw new Exception('Property code and unit number are required');
    }
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Strategy 1: Get active tenant for property/unit
    $sql = "
        SELECT 
            t.tenant_code,
            t.tenant_name,
            p.real_property_name,
            t.unit_no,
            u.meter_number,
            u.unit_type,
            t.actual_move_in_date,
            t.contract_expiry_date,
            t.terminated
        FROM m_tenant t
        INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
        LEFT JOIN m_units u ON t.real_property_code = u.real_property_code AND t.unit_no = u.unit_no
        WHERE t.real_property_code = :propertyCode
          AND t.unit_no = :unitNo
          AND ISNULL(t.terminated, 'N') = 'N'
        ORDER BY t.actual_move_in_date DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'propertyCode' => $propertyCode,
        'unitNo' => $unitNo
    ]);
    
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tenant) {
        // Active tenant found
        echo json_encode([
            'success' => true,
            'data' => [
                'tenantCode' => $tenant['tenant_code'],
                'tenantName' => $tenant['tenant_name'],
                'realPropertyName' => $tenant['real_property_name'],
                'unitNo' => $tenant['unit_no'],
                'meterNumber' => $tenant['meter_number'],
                'unitType' => $tenant['unit_type'],
                'actualMoveInDate' => $tenant['actual_move_in_date'],
                'contractExpiryDate' => $tenant['contract_expiry_date'],
                'terminated' => $tenant['terminated']
            ],
            'source' => 'active_tenant',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Strategy 2: Get last active tenant from vw_TenantReading
    $sql = "
        SELECT TOP 1
            tenant_code,
            tenant_name,
            property_name as real_property_name,
            unit_no,
            date_created
        FROM vw_TenantReading
        WHERE property_code = :propertyCode
          AND unit_no = :unitNo
        ORDER BY date_created DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'propertyCode' => $propertyCode,
        'unitNo' => $unitNo
    ]);
    
    $lastTenant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastTenant) {
        // Last active tenant found
        echo json_encode([
            'success' => true,
            'data' => [
                'tenantCode' => $lastTenant['tenant_code'],
                'tenantName' => $lastTenant['tenant_name'],
                'realPropertyName' => $lastTenant['real_property_name'],
                'unitNo' => $lastTenant['unit_no'],
                'meterNumber' => null,
                'unitType' => null,
                'actualMoveInDate' => null,
                'contractExpiryDate' => null,
                'terminated' => 'Y'
            ],
            'source' => 'last_active_tenant',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Strategy 3: Get property defaults
    $sql = "
        SELECT 
            p.real_property_name,
            u.meter_number,
            u.unit_type
        FROM m_real_property p
        LEFT JOIN m_units u ON p.real_property_code = u.real_property_code AND u.unit_no = :unitNo
        WHERE p.real_property_code = :propertyCode
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'propertyCode' => $propertyCode,
        'unitNo' => $unitNo
    ]);
    
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($property) {
        // Property found, return with default tenant
        echo json_encode([
            'success' => true,
            'data' => [
                'tenantCode' => 'DEFAULT',
                'tenantName' => 'Default Tenant',
                'realPropertyName' => $property['real_property_name'],
                'unitNo' => $unitNo,
                'meterNumber' => $property['meter_number'],
                'unitType' => $property['unit_type'],
                'actualMoveInDate' => null,
                'contractExpiryDate' => null,
                'terminated' => 'N'
            ],
            'source' => 'property_defaults',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // No tenant found
    echo json_encode([
        'success' => false,
        'message' => 'No tenant information found for this property/unit combination',
        'timestamp' => date('Y-m-d H:i:s')
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
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
