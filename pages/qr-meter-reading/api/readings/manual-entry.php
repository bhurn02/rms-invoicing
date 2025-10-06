<?php
/**
 * RMS QR Meter Reading System - Manual Entry API
 * Phase 17: Manual reading entry operations
 * 
 * Endpoints:
 * POST /api/readings/manual-entry.php - Create readings manually with tenant selection
 * Purpose: Manual reading creation without QR scanning
 */

// Set content type to JSON
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once '../../config/config.php';
require_once '../../auth/auth.php';
require_once '../../auth/permission-check.php';

// Validate API permissions (includes authentication check)
validateAPIPermissions();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    
    // Get current user
    $currentUserId = getCurrentUserId();
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields (using REAL schema fields only)
    $requiredFields = [
        'tenant_code', 'date_from', 'date_to', 'billing_date_from', 'billing_date_to',
        'current_reading', 'prev_reading'
    ];
    
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate tenant exists and is active
    $tenantSql = "SELECT t.tenant_code, t.tenant_name, t.real_property_code, t.unit_no,
                         p.real_property_name
                  FROM m_tenant t
                  INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                  WHERE t.tenant_code = ? AND ISNULL(t.terminated,'N') = 'N'";
    
    $tenantStmt = $pdo->prepare($tenantSql);
    $tenantStmt->execute([$input['tenant_code']]);
    $tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tenant) {
        throw new Exception('Invalid or inactive tenant');
    }
    
    // Validate reading values
    $currentReading = floatval($input['current_reading']);
    $prevReading = floatval($input['prev_reading']);
    
    if ($currentReading <= $prevReading) {
        throw new Exception('Current reading must be greater than previous reading');
    }
    
    // Check for duplicate reading in same period
    $duplicateSql = "SELECT COUNT(*) as count 
                     FROM t_tenant_reading 
                     WHERE tenant_code = ? 
                     AND YEAR(reading_date) = YEAR(?) 
                     AND MONTH(reading_date) = MONTH(?)";
    
    $duplicateStmt = $pdo->prepare($duplicateSql);
    $duplicateStmt->execute([$input['tenant_code'], $input['date_from'], $input['date_from']]);
    $duplicateCount = $duplicateStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($duplicateCount > 0) {
        throw new Exception('Duplicate reading for this period already exists');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Insert reading using REAL schema
        $insertSql = "INSERT INTO t_tenant_reading (
            tenant_code, date_from, date_to, billing_date_from, billing_date_to,
            prev_reading, current_reading, remarks, reading_date, reading_by, created_by, date_created
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?, ?, GETDATE())";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            $input['tenant_code'],
            $input['date_from'],
            $input['date_to'], 
            $input['billing_date_from'],
            $input['billing_date_to'],
            $prevReading,
            $currentReading,
            $input['remarks'] ?? '',
            $currentUserId,
            $currentUserId
        ]);
        
        $readingId = $pdo->lastInsertId();
        
        // Insert audit trail
        $auditSql = "INSERT INTO t_tenant_reading_ext (
            reading_id, ip_address, user_agent, device_info, location_data, created_date
        ) VALUES (?, ?, ?, ?, ?, GETDATE())";
        
        $auditStmt = $pdo->prepare($auditSql);
        $auditStmt->execute([
            $readingId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            'Manual Entry',
            null
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Reading created successfully',
            'data' => [
                'reading_id' => $readingId,
                'tenant_name' => $tenant['tenant_name'],
                'property_name' => $tenant['real_property_name'],
                'unit_no' => $tenant['unit_no'],
                'usage' => $currentReading - $prevReading,
                'date_from' => $input['date_from'],
                'date_to' => $input['date_to']
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>