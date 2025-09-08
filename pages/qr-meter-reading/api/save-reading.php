<?php
/**
 * RMS QR Meter Reading System - Enhanced Save Reading API
 * Saves meter readings with business logic for date calculations
 * and comprehensive audit trail
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

// Check authentication
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit();
}

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
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $requiredFields = ['propertyCode', 'unitNo', 'currentReading'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    $propertyCode = trim($input['propertyCode']);
    $unitNo = trim($input['unitNo']);
    $currentReading = floatval($input['currentReading']);
    $remarks = isset($input['remarks']) ? trim($input['remarks']) : '';
    $locationData = isset($input['locationData']) ? trim($input['locationData']) : null;
    
    // Validate current reading
    if ($currentReading <= 0) {
        throw new Exception('Current reading must be greater than 0');
    }
    
    // Get current user info
    $currentUserId = getCurrentUserId();
    $currentUsername = getCurrentUsername();
    
    if (!$currentUserId) {
        throw new Exception('Unable to identify current user');
    }
    
    // =====================================================
    // CALL STORED PROCEDURE
    // =====================================================
    
    $procedureSql = "EXEC [dbo].[sp_t_SaveTenantReading] 
                     @propertyCode = ?, 
                     @unitNo = ?, 
                     @currentReading = ?, 
                     @remarks = ?, 
                     @readingBy = ?, 
                     @createdBy = ?, 
                     @ipAddress = ?, 
                     @userAgent = ?, 
                     @deviceInfo = ?,
                     @locationData = ?";
    
    $procedureStmt = $pdo->prepare($procedureSql);
    
    $procedureStmt->execute([
        $propertyCode,
        $unitNo,
        $currentReading,
        $remarks,
        $currentUsername,
        $currentUserId,
        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'QR System Mobile',
        $locationData
    ]);
    
    // Get the result set from stored procedure
    $result = $procedureStmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug: Log the actual result for troubleshooting
    error_log("Stored procedure result: " . json_encode($result));
    
    // Check if there are multiple result sets and get the last one (our success result)
    while ($procedureStmt->nextRowset()) {
        $nextResult = $procedureStmt->fetch(PDO::FETCH_ASSOC);
        if ($nextResult) {
            error_log("Additional result set: " . json_encode($nextResult));
            // Use the last result set that has our expected fields
            if (isset($nextResult['status']) || isset($nextResult['errorMessage'])) {
                $result = $nextResult;
            }
        }
    }
    
    if (!$result) {
        throw new Exception('Stored procedure did not return expected results');
    }
    
    // Check if procedure returned an error (CATCH block result set)
    if (isset($result['errorMessage'])) {
        $errorDetails = $result['errorMessage'];
        if (isset($result['errorSeverity'])) {
            $errorDetails .= ' (Severity: ' . $result['errorSeverity'] . ')';
        }
        if (isset($result['errorState'])) {
            $errorDetails .= ' (State: ' . $result['errorState'] . ')';
        }
        throw new Exception($errorDetails);
    }
    
    // Check if procedure was successful (TRY block result set)
    if (!isset($result['status']) || $result['status'] !== 'success') {
        // Debug: Log what we actually got
        error_log("Expected 'status' field not found or not 'success'. Got: " . json_encode($result));
        throw new Exception($result['message'] ?? 'Unknown error from stored procedure - result: ' . json_encode($result));
    }
    
    // Log successful reading
    logActivity("QR Reading Saved", "Reading saved for tenant {$result['tenantCode']}: {$result['prevReading']} -> {$result['currentReading']}");
        
        // Return success response
        echo json_encode([
            'success' => true,
        'message' => $result['message'],
            'data' => [
            'readingId' => $result['readingId'],
            'tenantCode' => $result['tenantCode'],
            'tenantName' => $result['tenantName'],
            'prevReading' => $result['prevReading'],
            'currentReading' => $result['currentReading'],
            'readingDate' => $result['readingDate'],
                'readingPeriod' => [
                'from' => $result['dateFrom'],
                'to' => $result['dateTo']
                ],
                'billingPeriod' => [
                'from' => $result['billingDateFrom'],
                'to' => $result['billingDateTo']
            ],
            'usage' => $result['usage'],
            'remarks' => $result['remarks']
        ]
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log("QR Reading Save Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error saving reading: ' . $e->getMessage()
    ]);
}
?>
