<?php
/**
 * RMS QR Meter Reading System - Get Recent Readings API
 * Retrieves recent meter readings from the database using correct RMS schema
 */

// Require authentication
require_once '../auth/auth.php';
requireAuth();

// Include configuration
require_once '../config/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Get current user for filtering (optional - can be removed if you want all readings)
    $currentUserId = getCurrentUserId();
    
    // Query recent readings with proper joins to get property and unit information
    // Use t_tenant_reading_ext to identify QR readings since all entries there are from QR system
    $sql = "SELECT TOP 10 
                r.reading_id,
                r.tenant_code,
                r.date_from as readingDate,
                r.date_to as readingDateTo,
                r.prev_reading,
                r.current_reading as meterReading,
                r.remarks,
                r.reading_date,
                r.reading_by,
                r.created_by as createdBy,
                r.date_created as createdAt,
                r.updated_by as updatedBy,
                r.date_updated as updatedAt,
                t.tenant_name,
                t.real_property_code as propertyId,
                t.building_code,
                t.unit_no as unitNumber,
                p.real_property_name as propertyName,
                ext.device_info,
                ext.ip_address,
                ext.location_data
            FROM t_tenant_reading r
            INNER JOIN t_tenant_reading_ext ext ON r.reading_id = ext.reading_id
            INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
            LEFT JOIN m_real_property p ON t.real_property_code = p.real_property_code
            WHERE ext.device_info LIKE '%QR System%'
            ORDER BY r.date_created DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $readings = $stmt->fetchAll();
    
    // Debug: Log the query and results
    error_log("Get Recent Readings Query: " . $sql);
    error_log("Get Recent Readings Count: " . count($readings));
    
    // If no readings found with device_info, try fallback query using remarks
    if (count($readings) === 0) {
        error_log("No readings found with device_info, trying fallback query");
        
        $fallbackSql = "SELECT TOP 10 
                    r.reading_id,
                    r.tenant_code,
                    r.date_from as readingDate,
                    r.date_to as readingDateTo,
                    r.prev_reading,
                    r.current_reading as meterReading,
                    r.remarks,
                    r.reading_date,
                    r.reading_by,
                    r.created_by as createdBy,
                    r.date_created as createdAt,
                    r.updated_by as updatedBy,
                    r.date_updated as updatedAt,
                    t.tenant_name,
                    t.real_property_code as propertyId,
                    t.building_code,
                    t.unit_no as unitNumber,
                    p.real_property_name as propertyName
                FROM t_tenant_reading r
                INNER JOIN m_tenant t ON r.tenant_code = t.tenant_code
                LEFT JOIN m_real_property p ON t.real_property_code = p.real_property_code
                WHERE r.remarks LIKE '%QR System%' OR r.reading_by IS NOT NULL
                ORDER BY r.date_created DESC";
        
        $fallbackStmt = $pdo->prepare($fallbackSql);
        $fallbackStmt->execute();
        $readings = $fallbackStmt->fetchAll();
        
        error_log("Fallback query count: " . count($readings));
    }
    
    // Format the data for JSON response
    $formattedReadings = [];
    foreach ($readings as $reading) {
        $formattedReadings[] = [
            'readingId' => $reading['reading_id'],
            'tenantCode' => $reading['tenant_code'],
            'tenantName' => $reading['tenant_name'],
            'propertyId' => $reading['propertyId'],
            'propertyName' => $reading['propertyName'],
            'buildingCode' => $reading['building_code'],
            'unitNumber' => $reading['unitNumber'],
            'readingDate' => $reading['reading_date'] ?: $reading['readingDate'],
            'readingDateTo' => $reading['readingDateTo'],
            'prevReading' => floatval(isset($reading['prev_reading']) ? $reading['prev_reading'] : 0),
            'meterReading' => floatval(isset($reading['meterReading']) ? $reading['meterReading'] : 0),
            'usage' => floatval(isset($reading['meterReading']) ? $reading['meterReading'] : 0) - floatval(isset($reading['prev_reading']) ? $reading['prev_reading'] : 0),
            'remarks' => $reading['remarks'],
            'readingBy' => $reading['reading_by'],
            'deviceInfo' => isset($reading['device_info']) ? $reading['device_info'] : 'QR System',
            'ipAddress' => isset($reading['ip_address']) ? $reading['ip_address'] : null,
            'locationData' => isset($reading['location_data']) ? $reading['location_data'] : null,
            'createdBy' => $reading['createdBy'],
            'createdAt' => $reading['createdAt'],
            'updatedBy' => $reading['updatedBy'],
            'updatedAt' => $reading['updatedAt']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $formattedReadings,
        'count' => count($formattedReadings)
    ]);
    
} catch (Exception $e) {
    error_log("QR Get Recent Readings Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
}
?>
