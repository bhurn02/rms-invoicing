<?php
/**
 * RMS QR Meter Reading System - Meter Reading Report API
 * Provides comprehensive reading reports for audit and validation
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
    $startDate = isset($_GET['startDate']) ? trim($_GET['startDate']) : '';
    $endDate = isset($_GET['endDate']) ? trim($_GET['endDate']) : '';
    $propertyFilter = isset($_GET['propertyFilter']) ? trim($_GET['propertyFilter']) : '';
    $technicianFilter = isset($_GET['technicianFilter']) ? trim($_GET['technicianFilter']) : '';
    $statusFilter = isset($_GET['statusFilter']) ? trim($_GET['statusFilter']) : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
    $export = isset($_GET['export']) ? trim($_GET['export']) : '';
    
    // Validate required parameters
    if (empty($startDate) || empty($endDate)) {
        throw new Exception('Start date and end date are required');
    }
    
    // Validate date format
    $startDateTime = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    
    if ($startDateTime > $endDateTime) {
        throw new Exception('Start date cannot be after end date');
    }
    
    // Validate pagination
    if ($page < 1) $page = 1;
    if ($limit < 1 || $limit > 1000) $limit = 50;
    
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Build base query
    $baseSql = "FROM t_tenant_reading r
                 INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                 INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                 LEFT JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
                 WHERE r.reading_date BETWEEN ? AND ?";
    
    $params = [$startDate, $endDate];
    
    // Add filters
    if (!empty($propertyFilter)) {
        $baseSql .= " AND t.real_property_code = ?";
        $params[] = $propertyFilter;
    }
    
    if (!empty($technicianFilter)) {
        $baseSql .= " AND r.reading_by = ?";
        $params[] = $technicianFilter;
    }
    
    if (!empty($statusFilter)) {
        switch ($statusFilter) {
            case 'valid':
                $baseSql .= " AND r.current_reading >= ISNULL(r.prev_reading, 0)";
                break;
            case 'invalid':
                $baseSql .= " AND r.current_reading < ISNULL(r.prev_reading, 0)";
                break;
            case 'no_prev':
                $baseSql .= " AND r.prev_reading IS NULL";
                break;
        }
    }
    
    // Count total records - use simpler approach to avoid JOIN issues in COUNT
    $countSql = "SELECT COUNT(DISTINCT r.reading_id) as total 
                 FROM t_tenant_reading r
                 INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                 WHERE r.reading_date BETWEEN ? AND ?";
    
    $countParams = [$startDate, $endDate];
    
    // Add filters to count query
    if (!empty($propertyFilter)) {
        $countSql .= " AND t.real_property_code = ?";
        $countParams[] = $propertyFilter;
    }
    
    if (!empty($technicianFilter)) {
        $countSql .= " AND r.reading_by = ?";
        $countParams[] = $technicianFilter;
    }
    
    if (!empty($statusFilter)) {
        switch ($statusFilter) {
            case 'valid':
                $countSql .= " AND r.current_reading >= ISNULL(r.prev_reading, 0)";
                break;
            case 'invalid':
                $countSql .= " AND r.current_reading < ISNULL(r.prev_reading, 0)";
                break;
            case 'no_prev':
                $countSql .= " AND r.prev_reading IS NULL";
                break;
        }
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $countResult['total'];
    
    // Calculate pagination
    $totalPages = ceil($totalRecords / $limit);
    $offset = ($page - 1) * $limit;
    
    // Build main query with pagination
    $mainSql = "SELECT 
                     p.real_property_name,
                     t.unit_no,
                     t.tenant_name,
                     r.reading_date,
                     r.date_from as reading_period_start,
                     r.date_to as reading_period_end,
                     r.billing_date_from,
                     r.billing_date_to,
                     r.prev_reading,
                     r.current_reading,
                     (r.current_reading - ISNULL(r.prev_reading, 0)) as usage,
                     r.reading_by,
                     r.remarks,
                     r.date_created,
                     ext.ip_address,
                     ext.user_agent,
                     ext.device_info
                 " . $baseSql . "
                 ORDER BY r.reading_date DESC, p.real_property_name, t.unit_no
                 OFFSET " . intval($offset) . " ROWS FETCH NEXT " . intval($limit) . " ROWS ONLY";
    
    $mainStmt = $pdo->prepare($mainSql);
    $mainStmt->execute($params);
    $readings = $mainStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get unique properties for filter dropdown
    $propertiesSql = "SELECT DISTINCT t.real_property_code, p.real_property_name
                      FROM m_tenant t
                      INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                      ORDER BY p.real_property_name";
    $propertiesStmt = $pdo->query($propertiesSql);
    $properties = $propertiesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get unique technicians for filter dropdown
    $techniciansSql = "SELECT DISTINCT reading_by 
                       FROM t_tenant_reading 
                       WHERE reading_by IS NOT NULL 
                       ORDER BY reading_by";
    $techniciansStmt = $pdo->query($techniciansSql);
    $technicians = $techniciansStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate summary statistics
    $summarySql = "SELECT 
                       COUNT(*) as total_readings,
                       COUNT(DISTINCT r.reading_by) as unique_technicians,
                       COUNT(DISTINCT t.real_property_code) as unique_properties,
                       COUNT(DISTINCT t.unit_no) as unique_units,
                       SUM(r.current_reading - ISNULL(r.prev_reading, 0)) as total_usage,
                       AVG(r.current_reading - ISNULL(r.prev_reading, 0)) as avg_usage
                   FROM t_tenant_reading r
                   INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                   WHERE r.reading_date BETWEEN ? AND ?";
    
    $summaryParams = [$startDate, $endDate];
    
    // Add filters to summary query
    if (!empty($propertyFilter)) {
        $summarySql .= " AND t.real_property_code = ?";
        $summaryParams[] = $propertyFilter;
    }
    
    if (!empty($technicianFilter)) {
        $summarySql .= " AND r.reading_by = ?";
        $summaryParams[] = $technicianFilter;
    }
    
    if (!empty($statusFilter)) {
        switch ($statusFilter) {
            case 'valid':
                $summarySql .= " AND r.current_reading >= ISNULL(r.prev_reading, 0)";
                break;
            case 'invalid':
                $summarySql .= " AND r.current_reading < ISNULL(r.prev_reading, 0)";
                break;
            case 'no_prev':
                $summarySql .= " AND r.prev_reading IS NULL";
                break;
        }
    }
    
    $summaryStmt = $pdo->prepare($summarySql);
    $summaryStmt->execute($summaryParams);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);
    
    // Prepare response data
    $response = [
        'success' => true,
        'data' => [
            'readings' => $readings,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'limit' => $limit,
                'offset' => $offset
            ],
            'filters' => [
                'properties' => $properties,
                'technicians' => $technicians
            ],
            'summary' => [
                'totalReadings' => intval($summary['total_readings']),
                'uniqueTechnicians' => intval($summary['unique_technicians']),
                'uniqueProperties' => intval($summary['unique_properties']),
                'uniqueUnits' => intval($summary['unique_units']),
                'totalUsage' => floatval($summary['total_usage']),
                'averageUsage' => floatval($summary['avg_usage'])
            ]
        ]
    ];
    
    // Handle export requests
    if (!empty($export)) {
        switch (strtolower($export)) {
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="meter_reading_report_' . date('Y-m-d') . '.csv"');
                
                $output = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($output, [
                    'Property', 'Unit', 'Tenant', 'Reading Date', 'Reading Period Start', 
                    'Reading Period End', 'Billing Period Start', 'Billing Period End',
                    'Previous Reading', 'Current Reading', 'Usage', 'Technician', 
                    'Remarks', 'Date Created', 'IP Address', 'User Agent'
                ]);
                
                // CSV data
                foreach ($readings as $reading) {
                    fputcsv($output, [
                        $reading['real_property_name'],
                        $reading['unit_no'],
                        $reading['tenant_name'],
                        $reading['reading_date'],
                        $reading['reading_period_start'],
                        $reading['reading_period_end'],
                        $reading['billing_date_from'],
                        $reading['billing_date_to'],
                        $reading['prev_reading'],
                        $reading['current_reading'],
                        $reading['usage'],
                        $reading['reading_by'],
                        $reading['remarks'],
                        $reading['date_created'],
                        $reading['ip_address'],
                        $reading['user_agent']
                    ]);
                }
                
                fclose($output);
                exit();
                
            case 'excel':
                // For Excel export, we'll return the data and let the frontend handle it
                $response['export'] = 'excel';
                break;
                
            case 'pdf':
                // For PDF export, we'll return the data and let the frontend handle it
                $response['export'] = 'pdf';
                break;
        }
    }
    
    // Return JSON response
    echo json_encode($response);
    
} catch (Exception $e) {
    // Log error
    error_log("QR Meter Reading Report Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error generating report: ' . $e->getMessage()
    ]);
}
?>
