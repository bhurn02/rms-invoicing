<?php
/**
 * Get Previous Reading Data API Endpoint
 * Phase 9: Cache-first tenant resolution system
 * Returns previous reading information for property/unit combination
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
    
    // Get previous reading from vw_LatestTenantReadings
    $sql = "
        SELECT 
            current_reading,
            prev_reading,
            usage,
            reading_date,
            billing_from,
            billing_to,
            reading_date_from,
            reading_date_to,
            remarks,
            date_created
        FROM vw_LatestTenantReadings
        WHERE property_code = :propertyCode
          AND unit_no = :unitNo
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'propertyCode' => $propertyCode,
        'unitNo' => $unitNo
    ]);
    
    $reading = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($reading) {
        // Previous reading found
        echo json_encode([
            'success' => true,
            'data' => [
                'prevReading' => (float)$reading['prev_reading'],
                'currentReading' => (float)$reading['current_reading'],
                'usage' => (float)$reading['usage'],
                'readingDate' => $reading['reading_date'],
                'dateFrom' => $reading['reading_date_from'],
                'dateTo' => $reading['reading_date_to'],
                'billingDateFrom' => $reading['billing_from'],
                'billingDateTo' => $reading['billing_to'],
                'remarks' => $reading['remarks'],
                'dateCreated' => $reading['date_created']
            ],
            'source' => 'vw_LatestTenantReadings',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Fallback: Get from t_tenant_reading directly
    $sql = "
        SELECT TOP 1
            r.current_reading,
            r.prev_reading,
            (r.current_reading - r.prev_reading) as usage,
            r.reading_date,
            r.date_from,
            r.date_to,
            r.billing_date_from,
            r.billing_date_to,
            r.remarks,
            r.date_created
        FROM t_tenant_reading r
        INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
        WHERE t.real_property_code = :propertyCode
          AND t.unit_no = :unitNo
        ORDER BY r.reading_date DESC, r.date_created DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'propertyCode' => $propertyCode,
        'unitNo' => $unitNo
    ]);
    
    $reading = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($reading) {
        // Previous reading found from direct table
        echo json_encode([
            'success' => true,
            'data' => [
                'prevReading' => (float)$reading['prev_reading'],
                'currentReading' => (float)$reading['current_reading'],
                'usage' => (float)$reading['usage'],
                'readingDate' => $reading['reading_date'],
                'dateFrom' => $reading['date_from'],
                'dateTo' => $reading['date_to'],
                'billingDateFrom' => $reading['billing_date_from'],
                'billingDateTo' => $reading['billing_date_to'],
                'remarks' => $reading['remarks'],
                'dateCreated' => $reading['date_created']
            ],
            'source' => 't_tenant_reading',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // No previous reading found
    echo json_encode([
        'success' => false,
        'message' => 'No previous reading found for this property/unit combination',
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
