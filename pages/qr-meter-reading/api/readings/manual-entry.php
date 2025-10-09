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
    
    // Validate tenant exists (allow both active and terminated tenants)
    $tenantSql = "SELECT t.tenant_code, t.tenant_name, t.real_property_code, t.unit_no,
                         p.real_property_name, ISNULL(t.terminated,'N') as is_terminated
                  FROM m_tenant t
                  INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                  WHERE t.tenant_code = ?";
    
    $tenantStmt = $pdo->prepare($tenantSql);
    $tenantStmt->execute([$input['tenant_code']]);
    $tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tenant) {
        throw new Exception('Invalid tenant');
    }
    
    // Validate that frontend data matches tenant data (security check)
    if (isset($input['real_property_code']) && $input['real_property_code'] !== $tenant['real_property_code']) {
        throw new Exception('Property code mismatch');
    }
    if (isset($input['unit_no']) && $input['unit_no'] !== $tenant['unit_no']) {
        throw new Exception('Unit number mismatch');
    }
    
    // Validate reading values
    $currentReading = floatval($input['current_reading']);
    $prevReading = floatval($input['prev_reading']);
    
    if ($currentReading <= $prevReading) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Current reading must be greater than previous reading'
        ]);
        exit();
    }
    
    // Check for duplicate reading in same period (based on date_from/date_to overlap)
    try {
        $duplicateSql = "SELECT COUNT(*) as count 
                         FROM t_tenant_reading 
                         WHERE tenant_code = ? 
                         AND (
                             (date_from <= ? AND date_to >= ?) OR  -- New period overlaps with existing
                             (date_from <= ? AND date_to >= ?) OR  -- New period contains existing
                             (date_from >= ? AND date_to <= ?)     -- New period is contained in existing
                         )";
        
        $duplicateStmt = $pdo->prepare($duplicateSql);
        $duplicateStmt->execute([
            $input['tenant_code'], 
            $input['date_from'], $input['date_from'],  // New period start overlaps
            $input['date_to'], $input['date_to'],      // New period end overlaps  
            $input['date_from'], $input['date_to']     // New period contains existing
        ]);
        $duplicateCount = $duplicateStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        
        if ($duplicateCount > 0) {
            // Get the existing period details for better messaging
            $existingPeriodSql = "SELECT TOP 1 date_from, date_to 
                                  FROM t_tenant_reading 
                                  WHERE tenant_code = ? 
                                  AND (
                                      (date_from <= ? AND date_to >= ?) OR
                                      (date_from <= ? AND date_to >= ?) OR
                                      (date_from >= ? AND date_to <= ?)
                                  )
                                  ORDER BY date_from DESC";
            
            $existingStmt = $pdo->prepare($existingPeriodSql);
            $existingStmt->execute([
                $input['tenant_code'],
                $input['date_from'], $input['date_from'],
                $input['date_to'], $input['date_to'],
                $input['date_from'], $input['date_to']
            ]);
            $existingPeriod = $existingStmt->fetch(PDO::FETCH_ASSOC);
            
            $inputPeriod = date('m/d/Y', strtotime($input['date_from'])) . ' - ' . date('m/d/Y', strtotime($input['date_to']));
            $existingPeriodFormatted = '';
            
            if ($existingPeriod) {
                $existingPeriodFormatted = date('m/d/Y', strtotime($existingPeriod['date_from'])) . ' - ' . date('m/d/Y', strtotime($existingPeriod['date_to']));
            }
            
            $message = $existingPeriodFormatted && $existingPeriodFormatted === $inputPeriod
                ? "This period ({$inputPeriod}) already has a reading. Please select a different date range."
                : $existingPeriodFormatted 
                    ? "This period overlaps with existing reading ({$existingPeriodFormatted}). Please select a different date range."
                    : "This period overlaps with an existing reading. Please select a different date range.";
            
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $message
            ]);
            exit();
        }
    } catch (Exception $e) {
        // If duplicate check fails, treat it as a duplicate (safer approach)
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'This period overlaps with an existing reading. Please select a different date range.'
        ]);
        exit();
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
                'tenant_name' => trim($tenant['tenant_name']),
                'property_name' => trim($tenant['real_property_name']),
                'unit_no' => trim($tenant['unit_no']),
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