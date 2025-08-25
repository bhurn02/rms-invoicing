<?php
/**
 * RMS QR Meter Reading System - Save Reading API
 * Handles saving meter readings to the database using correct RMS schema
 */

// Require authentication
require_once '../auth/auth.php';
requireAuth();

// Include configuration
require_once '../config/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required_fields = ['propertyId', 'unitNumber', 'readingDate', 'meterReading'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Sanitize and validate input
    $propertyId = sanitizeInput($input['propertyId']);
    $unitNumber = sanitizeInput($input['unitNumber']);
    $meterId = sanitizeInput(isset($input['meterId']) ? $input['meterId'] : '');
    $readingDate = sanitizeInput($input['readingDate']);
    $meterReading = floatval($input['meterReading']);
    $timestamp = date('Y-m-d H:i:s');
    $userId = getCurrentUserId(); // Get current authenticated user
    
    // Validate date format
    if (!validateDate($readingDate)) {
        throw new Exception('Invalid date format');
    }
    
    // Validate meter reading
    if ($meterReading < 0) {
        throw new Exception('Meter reading must be positive');
    }
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // First, find the tenant_code based on property and unit
    $tenantSql = "SELECT tenant_code, real_property_code, building_code, unit_no 
                  FROM m_tenant 
                  WHERE real_property_code = ? AND unit_no = ?";
    $tenantStmt = $pdo->prepare($tenantSql);
    $tenantStmt->execute([$propertyId, $unitNumber]);
    $tenant = $tenantStmt->fetch();
    
    if (!$tenant) {
        throw new Exception('Tenant not found for the specified property and unit');
    }
    
    $tenantCode = $tenant['tenant_code'];
    
    // Check if reading already exists for this tenant and date range
    $checkSql = "SELECT TOP 1 reading_id, current_reading 
                 FROM t_tenant_reading 
                 WHERE tenant_code = ? AND date_from = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$tenantCode, $readingDate]);
                $existingReading = $checkStmt->fetch();
    
    if ($existingReading && $existingReading !== false) {
        // Update existing reading
        $sql = "UPDATE t_tenant_reading 
                SET current_reading = ?, 
                    updated_by = ?, 
                    date_updated = ?,
                    remarks = CONCAT(ISNULL(remarks, ''), '; QR Reading: ', ?)
                WHERE reading_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $meterReading,
            $userId,
            $timestamp,
            $meterReading,
            $existingReading['reading_id']
        ]);
        
        $message = 'Reading updated successfully';
    } else {
        // Insert new reading
        $sql = "INSERT INTO t_tenant_reading 
                (tenant_code, date_from, date_to, prev_reading, current_reading, 
                 billing_date_from, billing_date_to, remarks, created_by, date_created) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            $tenantCode,
            $readingDate,
            $readingDate, // date_to same as date_from for single day readings
            0, // prev_reading - will be calculated later if needed
            $meterReading,
            $readingDate, // billing_date_from
            $readingDate, // billing_date_to
            "QR System Reading: $meterReading" . ($meterId ? " (Meter: $meterId)" : ""),
            $userId,
            $timestamp
        ]);
        
        $message = 'Reading saved successfully';
    }
    
    if ($result) {
        // Log the activity
        logActivity("QR Reading Saved", "User $userId saved reading for Tenant: $tenantCode, Property: $propertyId, Unit: $unitNumber, Reading: $meterReading");
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => [
                'tenantCode' => $tenantCode,
                'propertyId' => $propertyId,
                'unitNumber' => $unitNumber,
                'meterId' => $meterId,
                'readingDate' => $readingDate,
                'meterReading' => $meterReading,
                'timestamp' => $timestamp,
                'userId' => $userId
            ]
        ]);
    } else {
        throw new Exception('Failed to save reading');
    }
    
} catch (Exception $e) {
    error_log("QR Save Reading Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
}
?>
