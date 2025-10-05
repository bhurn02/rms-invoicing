<?php
/**
 * RMS QR Meter Reading System - Tenant Readings Management API
 * Phase 17: Comprehensive CRUD operations for tenant readings
 * 
 * Endpoints:
 * GET /api/readings.php - List readings with filters and pagination
 * POST /api/readings.php - Create new reading
 * PUT /api/readings.php?id={id} - Update existing reading
 * DELETE /api/readings.php?id={id} - Delete reading
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

try {
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Get current user
    $currentUserId = getCurrentUserId();
    
    // Route based on HTTP method
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetReadings($pdo, $currentUserId);
            break;
        case 'POST':
            handleCreateReading($pdo, $currentUserId);
            break;
        case 'PUT':
            handleUpdateReading($pdo, $currentUserId);
            break;
        case 'DELETE':
            handleDeleteReading($pdo, $currentUserId);
            break;
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

/**
 * Handle GET request - List readings with filters and pagination
 */
function handleGetReadings($pdo, $currentUserId) {
    // Check if this is a request for a single reading
    if (isset($_GET['id'])) {
        handleGetSingleReading($pdo, $currentUserId, $_GET['id']);
        return;
    }
    
    // Get query parameters for list request
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(10, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    // Filter parameters
    $propertyCode = $_GET['property'] ?? null;
    $tenantCode = $_GET['tenant'] ?? null;
    $dateFrom = $_GET['date_from'] ?? null;
    $dateTo = $_GET['date_to'] ?? null;
    $search = $_GET['search'] ?? null;
    $source = $_GET['source'] ?? null;
    $sortBy = $_GET['sort_by'] ?? 'date_created';
    $sortOrder = $_GET['sort_order'] ?? 'DESC';
    
    // Validate sort parameters
    $allowedSortFields = ['date_created', 'reading_date', 'tenant_name', 'property_name', 'current_reading'];
    if (!in_array($sortBy, $allowedSortFields)) {
        $sortBy = 'date_created';
    }
    if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) {
        $sortOrder = 'DESC';
    }
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if ($propertyCode) {
        $whereConditions[] = "t.real_property_code = ?";
        $params[] = $propertyCode;
    }
    
    if ($tenantCode) {
        $whereConditions[] = "r.tenant_code = ?";
        $params[] = $tenantCode;
    }
    
    if ($dateFrom) {
        $whereConditions[] = "r.reading_date >= ?";
        $params[] = $dateFrom;
    }
    
    if ($dateTo) {
        $whereConditions[] = "r.reading_date <= ?";
        $params[] = $dateTo;
    }
    
    if ($search) {
        $whereConditions[] = "(t.tenant_name LIKE ? OR p.real_property_name LIKE ? OR r.remarks LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if ($source) {
        switch($source) {
            case 'Legacy':
                $whereConditions[] = "ext.device_info IS NULL";
                break;
            case 'Manual Entry':
                $whereConditions[] = "ext.device_info LIKE 'Manual Entry%'";
                break;
            case 'QR Scanner':
                $whereConditions[] = "ext.device_info IS NOT NULL AND ext.device_info NOT LIKE 'Manual Entry%'";
                break;
        }
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total
                 FROM t_tenant_reading r
                 INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                 INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                 LEFT JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
                 $whereClause";
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get readings with pagination
    $sql = "SELECT 
                r.reading_id,
                r.tenant_code,
                r.date_from,
                r.date_to,
                r.prev_reading,
                r.current_reading,
                r.remarks,
                r.reading_date,
                r.reading_by,
                r.billing_date_from,
                r.billing_date_to,
                r.created_by,
                r.date_created,
                r.updated_by,
                r.date_updated,
                t.tenant_name,
                t.real_property_code as property_code,
                t.unit_no,
                p.real_property_name as property_name,
                ext.device_info,
                ext.ip_address,
                ext.user_agent,
                ext.location_data,
                ext.created_date as ext_created_date,
                -- Check if reading has been invoiced
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM t_invoice_detail_reading idr
                        INNER JOIN t_invoice_detail id ON idr.invoice_detail_id = id.invoice_detail_id
                        WHERE idr.reading_id = r.reading_id
                    ) THEN 1
                    ELSE 0
                END as is_invoiced,
                -- Determine reading source
                CASE 
                    WHEN ext.device_info IS NULL THEN 'Legacy'
                    WHEN ext.device_info = 'Manual Entry' THEN 'Manual Entry'
                    WHEN ext.device_info LIKE 'Manual Entry%' THEN 'Manual Entry'
                    ELSE 'QR Scanner'
                END as reading_source
            FROM t_tenant_reading r
            INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
            INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
            LEFT JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
            $whereClause
            ORDER BY r.$sortBy $sortOrder
            OFFSET $offset ROWS
            FETCH NEXT $limit ROWS ONLY";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $readings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate pagination info
    $totalPages = ceil($totalCount / $limit);
    
    echo json_encode([
        'success' => true,
        'data' => $readings,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_count' => $totalCount,
            'limit' => $limit,
            'offset' => $offset
        ]
    ]);
}

/**
 * Handle GET request for single reading
 */
function handleGetSingleReading($pdo, $currentUserId, $readingId) {
    // Validate reading ID
    if (!$readingId || !is_numeric($readingId)) {
        throw new Exception('Invalid reading ID');
    }
    
    $sql = "SELECT 
                r.reading_id,
                r.tenant_code,
                r.date_from,
                r.date_to,
                r.prev_reading,
                r.current_reading,
                r.remarks,
                r.reading_date,
                r.reading_by,
                r.billing_date_from,
                r.billing_date_to,
                r.created_by,
                r.date_created,
                r.updated_by,
                r.date_updated,
                t.tenant_name,
                t.real_property_code as property_code,
                t.unit_no,
                p.real_property_name as property_name,
                ext.device_info,
                ext.ip_address,
                ext.user_agent,
                ext.location_data,
                ext.created_date as ext_created_date,
                -- Check if reading has been invoiced
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM t_invoice_detail_reading idr
                        INNER JOIN t_invoice_detail id ON idr.invoice_detail_id = id.invoice_detail_id
                        WHERE idr.reading_id = r.reading_id
                    ) THEN 1
                    ELSE 0
                END as is_invoiced,
                -- Determine reading source
                CASE 
                    WHEN ext.device_info IS NULL THEN 'Legacy'
                    WHEN ext.device_info = 'Manual Entry' THEN 'Manual Entry'
                    WHEN ext.device_info LIKE 'Manual Entry%' THEN 'Manual Entry'
                    ELSE 'QR Scanner'
                END as reading_source
            FROM t_tenant_reading r
            INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
            INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
            LEFT JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
            WHERE r.reading_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$readingId]);
    $reading = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reading) {
        throw new Exception('Reading not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $reading
    ]);
}

/**
 * Handle POST request - Create new reading
 */
function handleCreateReading($pdo, $currentUserId) {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $requiredFields = ['tenant_code', 'current_reading', 'date_from', 'date_to', 'billing_date_from', 'billing_date_to'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate tenant exists and is active
    $tenantSql = "SELECT tenant_code, tenant_name, real_property_code, unit_no 
                  FROM m_tenant 
                  WHERE tenant_code = ? AND ISNULL(terminated,'N') = 'N'";
    $tenantStmt = $pdo->prepare($tenantSql);
    $tenantStmt->execute([$input['tenant_code']]);
    $tenant = $tenantStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$tenant) {
        throw new Exception('Invalid or inactive tenant');
    }
    
    // Get previous reading for the tenant
    $prevReadingSql = "SELECT TOP 1 current_reading 
                       FROM t_tenant_reading 
                       WHERE tenant_code = ? 
                       ORDER BY reading_date DESC";
    $prevStmt = $pdo->prepare($prevReadingSql);
    $prevStmt->execute([$input['tenant_code']]);
    $prevReading = $prevStmt->fetch(PDO::FETCH_ASSOC);
    $prevReadingValue = $prevReading ? $prevReading['current_reading'] : 0;
    
    // Validate current reading
    if ($input['current_reading'] <= $prevReadingValue) {
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
        // Insert reading
        $insertSql = "INSERT INTO t_tenant_reading 
                      (tenant_code, date_from, date_to, prev_reading, current_reading, 
                       remarks, billing_date_from, billing_date_to, reading_date, reading_by, 
                       created_by, date_created)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, GETDATE(), ?, ?, GETDATE())";
        
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            $input['tenant_code'],
            $input['date_from'],
            $input['date_to'],
            $prevReadingValue,
            $input['current_reading'],
            $input['remarks'] ?? '',
            $input['billing_date_from'],
            $input['billing_date_to'],
            $currentUserId,
            $currentUserId
        ]);
        
        $readingId = $pdo->lastInsertId();
        
        // Insert audit trail
        $auditSql = "INSERT INTO t_tenant_reading_ext 
                     (reading_id, ip_address, user_agent, device_info, location_data, created_date)
                     VALUES (?, ?, ?, ?, ?, GETDATE())";
        
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
                'tenant_code' => $input['tenant_code'],
                'current_reading' => $input['current_reading'],
                'prev_reading' => $prevReadingValue,
                'usage' => $input['current_reading'] - $prevReadingValue
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Handle PUT request - Update existing reading
 */
function handleUpdateReading($pdo, $currentUserId) {
    // Get reading ID from URL
    $readingId = $_GET['id'] ?? null;
    if (!$readingId) {
        throw new Exception('Reading ID is required');
    }
    
    // Check if reading can be edited (not invoiced)
    $canEditSql = "SELECT CASE 
                       WHEN EXISTS (
                           SELECT 1 FROM t_invoice_detail_reading idr
                           INNER JOIN t_invoice_detail id ON idr.invoice_detail_id = id.invoice_detail_id
                           WHERE idr.reading_id = ?
                       ) THEN 0
                       ELSE 1
                   END as can_edit";
    $canEditStmt = $pdo->prepare($canEditSql);
    $canEditStmt->execute([$readingId]);
    $canEdit = $canEditStmt->fetch(PDO::FETCH_ASSOC)['can_edit'];
    
    if (!$canEdit) {
        throw new Exception('Cannot edit reading - invoice already exists');
    }
    
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $requiredFields = ['current_reading', 'date_from', 'date_to', 'billing_date_from', 'billing_date_to'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Get current reading data
    $currentSql = "SELECT tenant_code, prev_reading FROM t_tenant_reading WHERE reading_id = ?";
    $currentStmt = $pdo->prepare($currentSql);
    $currentStmt->execute([$readingId]);
    $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$current) {
        throw new Exception('Reading not found');
    }
    
    // Validate current reading
    if ($input['current_reading'] <= $current['prev_reading']) {
        throw new Exception('Current reading must be greater than previous reading');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Update reading
        $updateSql = "UPDATE t_tenant_reading 
                      SET date_from = ?, date_to = ?, current_reading = ?, 
                          remarks = ?, billing_date_from = ?, billing_date_to = ?,
                          reading_by = ?, updated_by = ?, date_updated = GETDATE()
                      WHERE reading_id = ?";
        
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            $input['date_from'],
            $input['date_to'],
            $input['current_reading'],
            $input['remarks'] ?? '',
            $input['billing_date_from'],
            $input['billing_date_to'],
            $currentUserId,
            $currentUserId,
            $readingId
        ]);
        
        // Update audit trail
        $auditUpdateSql = "UPDATE t_tenant_reading_ext 
                           SET ip_address = ?, user_agent = ?, device_info = ?
                           WHERE reading_id = ?";
        
        $auditUpdateStmt = $pdo->prepare($auditUpdateSql);
        $auditUpdateStmt->execute([
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            'Manual Entry - Updated',
            $readingId
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Reading updated successfully',
            'data' => [
                'reading_id' => $readingId,
                'current_reading' => $input['current_reading'],
                'prev_reading' => $current['prev_reading'],
                'usage' => $input['current_reading'] - $current['prev_reading']
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Handle DELETE request - Delete reading
 */
function handleDeleteReading($pdo, $currentUserId) {
    // Get reading ID from URL
    $readingId = $_GET['id'] ?? null;
    if (!$readingId) {
        throw new Exception('Reading ID is required');
    }
    
    // Check if reading can be deleted (not invoiced)
    $canDeleteSql = "SELECT CASE 
                         WHEN EXISTS (
                             SELECT 1 FROM t_invoice_detail_reading idr
                             INNER JOIN t_invoice_detail id ON idr.invoice_detail_id = id.invoice_detail_id
                             WHERE idr.reading_id = ?
                         ) THEN 0
                         ELSE 1
                     END as can_delete";
    $canDeleteStmt = $pdo->prepare($canDeleteSql);
    $canDeleteStmt->execute([$readingId]);
    $canDelete = $canDeleteStmt->fetch(PDO::FETCH_ASSOC)['can_delete'];
    
    if (!$canDelete) {
        throw new Exception('Cannot delete reading - invoice already exists');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
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
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Reading deleted successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
