<?php
/**
 * RMS QR Meter Reading System - Batch Operations API
 * Phase 17: Batch operations for multiple readings
 * 
 * Endpoints:
 * POST /api/readings/batch-update.php - Update multiple readings simultaneously
 * Purpose: Batch operations (date corrections, bulk deletes, etc.)
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
    
    // Validate required fields
    if (!isset($input['reading_ids']) || !is_array($input['reading_ids']) || empty($input['reading_ids'])) {
        throw new Exception('reading_ids array is required');
    }
    
    if (!isset($input['operation']) || empty($input['operation'])) {
        throw new Exception('operation is required');
    }
    
    $readingIds = $input['reading_ids'];
    $operation = $input['operation'];
    
    // Validate operation type
    $allowedOperations = ['date_correction', 'bulk_delete'];
    if (!in_array($operation, $allowedOperations)) {
        throw new Exception('Invalid operation. Allowed: ' . implode(', ', $allowedOperations));
    }
    
    // Validate reading IDs are not invoiced
    $placeholders = str_repeat('?,', count($readingIds) - 1) . '?';
    $invoicedSql = "SELECT r.reading_id 
                    FROM t_tenant_reading r
                    WHERE r.reading_id IN ($placeholders)
                    AND EXISTS (
                        SELECT 1 FROM t_invoice_detail_reading idr
                        INNER JOIN t_invoice_detail id ON idr.invoice_detail_id = id.invoice_detail_id
                        WHERE idr.reading_id = r.reading_id
                    )";
    
    $invoicedStmt = $pdo->prepare($invoicedSql);
    $invoicedStmt->execute($readingIds);
    $invoicedReadings = $invoicedStmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($invoicedReadings)) {
        throw new Exception('Cannot modify readings that have been invoiced: ' . implode(', ', $invoicedReadings));
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        switch ($operation) {
            case 'date_correction':
                $results = handleDateCorrection($pdo, $readingIds, $input, $currentUserId);
                break;
            case 'bulk_delete':
                $results = handleBulkDelete($pdo, $readingIds, $currentUserId);
                break;
        }
        
        // Count successes and errors
        foreach ($results as $result) {
            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Batch operation completed. Success: $successCount, Errors: $errorCount",
            'data' => [
                'operation' => $operation,
                'total_processed' => count($readingIds),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'results' => $results
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

/**
 * Handle date correction batch operation
 */
function handleDateCorrection($pdo, $readingIds, $input, $currentUserId) {
    // Validate required fields for date correction
    $requiredFields = ['date_from', 'date_to', 'billing_date_from', 'billing_date_to'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field for date correction: $field");
        }
    }
    
    $results = [];
    
    foreach ($readingIds as $readingId) {
        try {
            // Update reading dates
            $updateSql = "UPDATE t_tenant_reading 
                          SET date_from = ?, date_to = ?, 
                              billing_date_from = ?, billing_date_to = ?,
                              updated_by = ?, date_updated = GETDATE()
                          WHERE reading_id = ?";
            
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                $input['date_from'],
                $input['date_to'],
                $input['billing_date_from'],
                $input['billing_date_to'],
                $currentUserId,
                $readingId
            ]);
            
            if ($updateStmt->rowCount() === 0) {
                throw new Exception('Reading not found');
            }
            
            // Update audit trail
            $auditSql = "UPDATE t_tenant_reading_ext 
                         SET device_info = ?, ip_address = ?, user_agent = ?
                         WHERE reading_id = ?";
            
            $auditStmt = $pdo->prepare($auditSql);
            $auditStmt->execute([
                'Manual Entry - Date Corrected',
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $readingId
            ]);
            
            $results[] = [
                'reading_id' => $readingId,
                'success' => true,
                'message' => 'Date correction applied successfully'
            ];
            
        } catch (Exception $e) {
            $results[] = [
                'reading_id' => $readingId,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}

/**
 * Handle bulk delete operation
 */
function handleBulkDelete($pdo, $readingIds, $currentUserId) {
    $results = [];
    
    foreach ($readingIds as $readingId) {
        try {
            // Delete audit trail first
            $deleteAuditSql = "DELETE FROM t_tenant_reading_ext WHERE reading_id = ?";
            $deleteAuditStmt = $pdo->prepare($deleteAuditSql);
            $deleteAuditStmt->execute([$readingId]);
            
            // Delete reading
            $deleteSql = "DELETE FROM t_tenant_reading WHERE reading_id = ?";
            $deleteStmt = $pdo->prepare($deleteSql);
            $deleteStmt->execute([$readingId]);
            
            if ($deleteStmt->rowCount() === 0) {
                throw new Exception('Reading not found');
            }
            
            $results[] = [
                'reading_id' => $readingId,
                'success' => true,
                'message' => 'Reading deleted successfully'
            ];
            
        } catch (Exception $e) {
            $results[] = [
                'reading_id' => $readingId,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}
?>
