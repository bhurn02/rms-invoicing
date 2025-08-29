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
    // Get database connection - create fresh connection for this request
    $pdo = getDatabaseConnection();
    
    // Ensure clean connection state - reset any existing transactions
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Set connection attributes for this request
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
    
    // Set isolation level to prevent conflicts
    $pdo->exec("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
    
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
    
    // Validate current reading
    if ($currentReading < 0) {
        throw new Exception('Current reading must be non-negative');
    }
    
    // Get current user info
    $currentUserId = getCurrentUserId();
    $currentUsername = getCurrentUsername();
    
    if (!$currentUserId) {
        throw new Exception('Unable to identify current user');
    }
    
    // =====================================================
    // TENANT LOOKUP LOGIC
    // =====================================================
    
    // Primary: Look for active tenant
    $tenantSql = "SELECT tenant_code, tenant_name, real_property_code, unit_no, 
                          actual_move_in_date, contract_expiry_date
                  FROM m_tenant 
                   WHERE real_property_code = ? 
                     AND unit_no = ? 
                     AND ISNULL(terminated,'N') = 'N'
                   ORDER BY actual_move_in_date DESC";
    
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
                               tr.contract_expiry_date
                        FROM vw_TenantReading tr
                        WHERE tr.real_property_code = ? 
                          AND tr.unit_no = ?
                        ORDER BY tr.date_created DESC";
        
        $fallbackStmt = $pdo->prepare($fallbackSql);
        $fallbackStmt->execute([$propertyCode, $unitNo]);
        $tenant = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tenant) {
            throw new Exception("No tenant history found for property $propertyCode unit $unitNo");
        }
    }
    
    $tenantCode = $tenant['tenant_code'];
    $tenantName = $tenant['tenant_name'];
    
    // =====================================================
    // GET PREVIOUS READING
    // =====================================================
    
    // Get last reading for the unit (property + unit combination)
    $prevReadingSql = "SELECT TOP 1 current_reading, reading_date, date_to
                       FROM t_tenant_reading r
                       INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                       WHERE t.real_property_code = ? 
                         AND t.unit_no = ?
                       ORDER BY r.reading_date DESC";
    
    $prevReadingStmt = $pdo->prepare($prevReadingSql);
    $prevReadingStmt->execute([$propertyCode, $unitNo]);
    $prevReading = $prevReadingStmt->fetch(PDO::FETCH_ASSOC);
    
    $prevReadingValue = $prevReading ? floatval($prevReading['current_reading']) : null;
    
    // =====================================================
    // DEFAULT VALUES LOOKUP
    // =====================================================
    
    // Check for default charge code values
    $defaultsSql = "SELECT trd_charge_code, trd_date_from, trd_date_to, 
                           trd_billing_date_from, trd_billing_date_to
                    FROM s_tenant_reading_default 
                    WHERE trd_charge_code IN ('CUCF', 'CUCNF')
                      AND trd_date_from IS NOT NULL
                      AND trd_date_to IS NOT NULL";
    
    $defaultsStmt = $pdo->query($defaultsSql);
    $defaults = $defaultsStmt->fetch(PDO::FETCH_ASSOC);
    
    // =====================================================
    // DATE CALCULATION LOGIC
    // =====================================================
    
    $readingDate = new DateTime(); // Current server time
    $readingMonth = $readingDate->format('Y-m');
    
    // Reading period: 1st to last day of reading month
    $dateFrom = new DateTime($readingMonth . '-01 00:00:00');
    $dateTo = new DateTime($readingMonth . '-' . $dateFrom->format('t') . ' 23:59:59');
    
    // Billing period: 1st to last day of next month
    $billingDateFrom = clone $dateFrom;
    $billingDateFrom->add(new DateInterval('P1M'));
    $billingDateTo = clone $billingDateFrom;
    $billingDateTo->add(new DateInterval('P' . $billingDateTo->format('t') . 'D'));
    $billingDateTo->sub(new DateInterval('P1D'));
    $billingDateTo->setTime(23, 59, 59);
    
    // =====================================================
    // TENANT MOVE-IN/OUT OVERRIDES
    // =====================================================
    
    // If reading is taken between tenants (post move-out, pre move-in)
    // Check if this is a transition reading
    if ($prevReading && $prevReading['date_to']) {
        $lastReadingDateTo = new DateTime($prevReading['date_to']);
        $daysSinceLastReading = $readingDate->diff($lastReadingDateTo)->days;
        
        // If reading is within 7 days of last reading's end date, it might be a transition
        if ($daysSinceLastReading <= 7) {
            // Check if there's a move-out scenario
            $moveOutSql = "SELECT TOP 1 tenant_code, actual_move_out_date
                          FROM m_tenant 
                          WHERE real_property_code = ? 
                            AND unit_no = ? 
                            AND actual_move_out_date IS NOT NULL
                            AND actual_move_out_date >= ?
                          ORDER BY actual_move_out_date DESC";
            
            $moveOutStmt = $pdo->prepare($moveOutSql);
            $moveOutStmt->execute([$propertyCode, $unitNo, $lastReadingDateTo->format('Y-m-d')]);
            $moveOut = $moveOutStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($moveOut) {
                // This is a move-out reading - adjust periods
                $moveOutDate = new DateTime($moveOut['actual_move_out_date']);
                $dateTo = clone $moveOutDate;
                $billingDateTo = clone $moveOutDate;
                $billingDateTo->add(new DateInterval('P1D')); // +1 day to move-out date
            }
        }
    }
    
    // =====================================================
    // SAVE READING TO DATABASE
    // =====================================================
    
    // Double-check transaction state and ensure clean start
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Verify we're not in a transaction before starting
    if (!$pdo->inTransaction()) {
        // Begin transaction
        $pdo->beginTransaction();
    } else {
        throw new Exception('Unable to start transaction - connection is busy');
    }
    
    try {
        // Insert into t_tenant_reading
        $insertReadingSql = "INSERT INTO t_tenant_reading 
                (tenant_code, date_from, date_to, prev_reading, current_reading, 
                             reading_date, reading_by, remarks, billing_date_from, billing_date_to,
                             created_by, date_created)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";
        
        $insertReadingStmt = $pdo->prepare($insertReadingSql);
        $insertReadingStmt->execute([
            $tenantCode,
            $dateFrom->format('Y-m-d H:i:s'),
            $dateTo->format('Y-m-d H:i:s'),
            $prevReadingValue,
            $currentReading,
            $readingDate->format('Y-m-d H:i:s'),
            $currentUsername,
            $remarks ?: 'QR System Reading',
            $billingDateFrom->format('Y-m-d H:i:s'),
            $billingDateTo->format('Y-m-d H:i:s'),
            $currentUserId
        ]);
        
        $readingId = $pdo->lastInsertId();
        
        // Insert into t_tenant_reading_ext for audit trail
        $insertExtSql = "INSERT INTO t_tenant_reading_ext 
                         (reading_id, ip_address, user_agent, device_info, created_date)
                         VALUES (?, ?, ?, ?, GETDATE())";
        
        $insertExtStmt = $pdo->prepare($insertExtSql);
        $insertExtStmt->execute([
            $readingId,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'QR System Mobile'
        ]);
        
        // Commit transaction
        $pdo->commit();
        
        // Reset connection state for future requests
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        
        // Log successful reading
        logActivity("QR Reading Saved", "Reading saved for tenant $tenantCode: $prevReadingValue -> $currentReading");
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Reading saved successfully',
            'data' => [
                'readingId' => $readingId,
                'tenantCode' => $tenantCode,
                'tenantName' => $tenantName,
                'prevReading' => $prevReadingValue,
                'currentReading' => $currentReading,
                'readingDate' => $readingDate->format('Y-m-d H:i:s'),
                'readingPeriod' => [
                    'from' => $dateFrom->format('Y-m-d'),
                    'to' => $dateTo->format('Y-m-d')
                ],
                'billingPeriod' => [
                    'from' => $billingDateFrom->format('Y-m-d'),
                    'to' => $billingDateTo->format('Y-m-d')
                ],
                'usage' => $prevReadingValue ? ($currentReading - $prevReadingValue) : null,
                'remarks' => $remarks ?: 'QR System Reading'
            ]
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Reset connection state
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
        
        throw $e;
    }
    
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
