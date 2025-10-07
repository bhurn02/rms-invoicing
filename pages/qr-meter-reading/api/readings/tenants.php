<?php
/**
 * RMS QR Meter Reading System - Tenants API
 * Phase 17: Tenant resource management for manual entry
 * 
 * Endpoints:
 * GET /api/readings/tenants.php - Search and filter tenants
 * Purpose: Retrieve tenants with filters for manual reading entry
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
    // Get database connection
    $pdo = getDatabaseConnection();
    
    // Get query parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchCriteria = isset($_GET['search_criteria']) ? trim($_GET['search_criteria']) : 'tenant_name';
    $realPropertyCode = isset($_GET['real_property_code']) ? trim($_GET['real_property_code']) : '';
    $unitNo = isset($_GET['unit_no']) ? trim($_GET['unit_no']) : '';
    $includeTerminated = isset($_GET['include_terminated']) ? $_GET['include_terminated'] === '1' : false;
    $statusFilter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = min(50, max(10, intval($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause based on status filter
    if ($statusFilter === 'active') {
        $whereConditions = ["ISNULL(t.terminated,'N') = 'N'"]; // Only active tenants
    } elseif ($statusFilter === 'terminated') {
        $whereConditions = ["ISNULL(t.terminated,'N') = 'Y'"]; // Only terminated tenants
    } elseif ($includeTerminated) {
        $whereConditions = ["1=1"]; // Include all tenants (active and terminated)
    } else {
        $whereConditions = ["ISNULL(t.terminated,'N') = 'N'"]; // Default: only active tenants
    }
    $params = [];
    
    if (!empty($search)) {
        // Build search condition based on search criteria
        switch ($searchCriteria) {
            case 'tenant_name':
                $whereConditions[] = "t.tenant_name LIKE :search";
                $params['search'] = "%$search%";
                break;
            case 'tenant_code':
                $whereConditions[] = "t.tenant_code LIKE :search";
                $params['search'] = "%$search%";
                break;
            default:
                // Fallback to generic search across tenant fields
                $whereConditions[] = "(t.tenant_code LIKE :search1 OR t.tenant_name LIKE :search2)";
                $searchParam = "%$search%";
                $params['search1'] = $searchParam;
                $params['search2'] = $searchParam;
                break;
        }
    }
    
    if (!empty($realPropertyCode)) {
        $whereConditions[] = "t.real_property_code = :propertyCode";
        $params['propertyCode'] = $realPropertyCode;
    }
    
    if (!empty($unitNo)) {
        $whereConditions[] = "t.unit_no = :unitNo";
        $params['unitNo'] = $unitNo;
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Get total count
    $countSql = "
        SELECT COUNT(*) as total
        FROM m_tenant t
        INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
        INNER JOIN m_units u ON t.real_property_code = u.real_property_code AND t.unit_no = u.unit_no
        WHERE $whereClause
    ";
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get tenants with pagination (simple tenant lookup for manual entry)
    $sql = "
        SELECT 
            t.tenant_code,
            t.tenant_name, 
            t.real_property_code,
            t.unit_no,
            p.real_property_name,
            u.meter_number,
            u.unit_type,
            t.actual_move_in_date,
            t.contract_eff_date,
            t.contract_expiry_date,
            t.date_terminated,
            CASE WHEN ISNULL(t.terminated, 'N') = 'Y' THEN 1 ELSE 0 END as is_terminated
        FROM m_tenant t
        INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
        INNER JOIN m_units u ON t.real_property_code = u.real_property_code AND t.unit_no = u.unit_no
        WHERE $whereClause
        ORDER BY 
            CASE WHEN ISNULL(t.terminated, 'N') = 'N' THEN 0 ELSE 1 END,  -- Active tenants first
            t.actual_move_in_date DESC,  -- Then by move-in date descending
            p.real_property_name, 
            t.unit_no
        OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tenants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get properties for filter dropdown
    $propertiesSql = "
        SELECT real_property_code, real_property_name 
        FROM m_real_property 
        ORDER BY real_property_name
    ";
    $propertiesStmt = $pdo->prepare($propertiesSql);
    $propertiesStmt->execute();
    $properties = $propertiesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get units for filter dropdown (filtered by property if specified)
    $unitsSql = "
        SELECT 
            u.unit_no, 
            u.real_property_code, 
            p.real_property_name
        FROM m_units u
        INNER JOIN m_real_property p ON u.real_property_code = p.real_property_code
    ";
    
    $unitsParams = [];
    if (!empty($realPropertyCode)) {
        $unitsSql .= " WHERE u.real_property_code = :propertyCode";
        $unitsParams['propertyCode'] = $realPropertyCode;
    }
    
    $unitsSql .= " ORDER BY p.real_property_name, u.unit_no";
    
    $unitsStmt = $pdo->prepare($unitsSql);
    $unitsStmt->execute($unitsParams);
    $units = $unitsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform tenants data with trimmed string fields
    $transformedTenants = array_map(function($tenant) {
        return [
            'tenant_code' => trim($tenant['tenant_code']),
            'tenant_name' => trim($tenant['tenant_name']),
            'real_property_code' => trim($tenant['real_property_code']),
            'unit_no' => trim($tenant['unit_no']),
            'real_property_name' => trim($tenant['real_property_name']),
            'meter_number' => trim($tenant['meter_number']),
            'unit_type' => trim($tenant['unit_type']),
            'actual_move_in_date' => $tenant['actual_move_in_date'],
            'contract_eff_date' => $tenant['contract_eff_date'],
            'contract_expiry_date' => $tenant['contract_expiry_date'],
            'date_terminated' => $tenant['date_terminated'],
            'is_terminated' => $tenant['is_terminated']
        ];
    }, $tenants);
    
    // Transform properties data with trimmed string fields
    $transformedProperties = array_map(function($property) {
        return [
            'real_property_code' => trim($property['real_property_code']),
            'real_property_name' => trim($property['real_property_name'])
        ];
    }, $properties);
    
    // Transform units data with trimmed string fields
    $transformedUnits = array_map(function($unit) {
        return [
            'real_property_code' => trim($unit['real_property_code']),
            'unit_no' => trim($unit['unit_no']),
            'real_property_name' => trim($unit['real_property_name'])
        ];
    }, $units);
    
    // Calculate pagination info
    $totalPages = ceil($totalCount / $limit);
    $hasNextPage = $page < $totalPages;
    $hasPrevPage = $page > 1;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'tenants' => $transformedTenants,
            'properties' => $transformedProperties,
            'units' => $transformedUnits,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit,
                'has_next_page' => $hasNextPage,
                'has_prev_page' => $hasPrevPage
            ],
            'filters' => [
                'search' => $search,
                'real_property_code' => $realPropertyCode,
                'unit_no' => $unitNo
            ]
        ],
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