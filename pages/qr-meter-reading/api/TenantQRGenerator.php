<?php
/**
 * TenantQRGenerator Class
 * Business logic for tenant data retrieval and QR code generation
 * 
 * Handles:
 * - Active tenant data retrieval
 * - Property information management
 * - QR code data formatting
 * - Database operations for tenant management
 */

class TenantQRGenerator {
    private $conn;
    private $logger;
    
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
        $this->logger = new QRLogger();
    }
    
    /**
     * Get all active tenants with property information
     * @param string|null $propertyFilter Optional property code filter
     * @return array Array containing tenants and properties
     */
    public function getActiveTenants($propertyFilter = null) {
        try {
            $sql = "SELECT 
                        t.tenant_code,
                        t.tenant_name,
                        t.real_property_code,
                        t.building_code,
                        t.unit_no,
                        ISNULL(t.status, 'ACTIVE') as status,
                        p.real_property_name,
                        u.meter_id,
                        u.is_residential
                    FROM m_tenant t
                    INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                    LEFT JOIN m_units u ON t.real_property_code = u.real_property_code 
                        AND t.unit_no = u.unit_no
                    WHERE UPPER(LTRIM(RTRIM(ISNULL(t.status, 'ACTIVE')))) = 'ACTIVE'
                        AND t.tenant_code IS NOT NULL
                        AND t.tenant_name IS NOT NULL
                        AND t.real_property_code IS NOT NULL
                        AND t.unit_no IS NOT NULL";
            
            // Add property filter if specified
            if ($propertyFilter) {
                $sql .= " AND t.real_property_code = ?";
            }
            
            $sql .= " ORDER BY p.real_property_name, t.unit_no";
            
            if ($propertyFilter) {
                $stmt = sqlsrv_prepare($this->conn, $sql, array($propertyFilter));
                if (!$stmt || !sqlsrv_execute($stmt)) {
                    throw new Exception('Failed to execute filtered query');
                }
            } else {
                $stmt = sqlsrv_query($this->conn, $sql);
            }
            
            if ($stmt === false) {
                $errors = sqlsrv_errors();
                throw new Exception('Database query failed: ' . $errors[0]['message']);
            }
            
            $tenants = [];
            $properties = [];
            $propertyMap = [];
            
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $tenant = $this->formatTenantData($row);
                $tenants[] = $tenant;
                
                // Collect unique properties
                $propCode = $tenant['real_property_code'];
                if (!isset($propertyMap[$propCode])) {
                    $propertyMap[$propCode] = [
                        'real_property_code' => $propCode,
                        'real_property_name' => $tenant['real_property_name']
                    ];
                }
            }
            
            $properties = array_values($propertyMap);
            usort($properties, function($a, $b) {
                return strcasecmp($a['real_property_name'], $b['real_property_name']);
            });
            
            sqlsrv_free_stmt($stmt);
            
            $this->logger->log("Retrieved " . count($tenants) . " active tenants");
            
            return [
                'tenants' => $tenants,
                'properties' => $properties,
                'count' => count($tenants)
            ];
            
        } catch (Exception $e) {
            $this->logger->error("Error retrieving active tenants: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get specific tenant details by tenant code
     * @param string $tenantCode Tenant code to lookup
     * @return array|null Tenant data or null if not found
     */
    public function getTenantDetails($tenantCode) {
        try {
            $sql = "SELECT 
                        t.tenant_code,
                        t.tenant_name,
                        t.real_property_code,
                        t.building_code,
                        t.unit_no,
                        t.status,
                        p.real_property_name,
                        u.meter_id,
                        u.is_residential
                    FROM m_tenant t
                    INNER JOIN m_real_property p ON t.real_property_code = p.real_property_code
                    LEFT JOIN m_units u ON t.real_property_code = u.real_property_code 
                        AND t.unit_no = u.unit_no
                    WHERE t.tenant_code = ?";
            
            $stmt = sqlsrv_prepare($this->conn, $sql, array($tenantCode));
            
            if (!$stmt || !sqlsrv_execute($stmt)) {
                throw new Exception('Failed to execute tenant details query');
            }
            
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmt);
            
            if ($row) {
                return $this->formatTenantData($row);
            }
            
            return null;
            
        } catch (Exception $e) {
            $this->logger->error("Error retrieving tenant details for $tenantCode: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Format tenant data from database row
     * @param array $row Database row
     * @return array Formatted tenant data
     */
    private function formatTenantData($row) {
        return [
            'tenant_code' => trim($row['tenant_code']),
            'tenant_name' => trim($row['tenant_name']),
            'real_property_code' => trim($row['real_property_code']),
            'building_code' => trim($row['building_code'] ?? ''),
            'unit_no' => trim($row['unit_no']),
            'status' => trim($row['status'] ?? 'ACTIVE'),
            'real_property_name' => trim($row['real_property_name'] ?? $row['real_property_code']),
            'meter_id' => trim($row['meter_id'] ?? ''),
            'is_residential' => intval($row['is_residential'] ?? 1)
        ];
    }
    
    /**
     * Generate QR code data for a tenant
     * @param array $tenant Tenant data
     * @return array QR code data
     */
    public function generateQRData($tenant) {
        return [
            'propertyId' => $tenant['real_property_code'],
            'unitNumber' => $tenant['unit_no'],
            'meterId' => $tenant['meter_id'] ?: null,
            'tenantCode' => $tenant['tenant_code'],
            'propertyName' => $tenant['real_property_name']
        ];
    }
    
    /**
     * Format QR content as JSON string
     * @param array $tenant Tenant data
     * @return string JSON formatted QR content
     */
    public function formatQRContent($tenant) {
        $qrData = $this->generateQRData($tenant);
        return json_encode($qrData);
    }
    
    /**
     * Search tenants by name, property, or unit
     * @param array $tenants Array of tenant data
     * @param string $searchTerm Search term
     * @return array Filtered tenant array
     */
    public function searchTenants($tenants, $searchTerm) {
        if (empty($searchTerm)) {
            return $tenants;
        }
        
        $searchTerm = strtolower(trim($searchTerm));
        
        return array_filter($tenants, function($tenant) use ($searchTerm) {
            return strpos(strtolower($tenant['tenant_name']), $searchTerm) !== false ||
                   strpos(strtolower($tenant['real_property_name']), $searchTerm) !== false ||
                   strpos(strtolower($tenant['unit_no']), $searchTerm) !== false ||
                   strpos(strtolower($tenant['real_property_code']), $searchTerm) !== false;
        });
    }
    
    /**
     * Validate tenant data for QR generation
     * @param array $tenant Tenant data
     * @return bool True if valid
     */
    public function validateTenantData($tenant) {
        return !empty($tenant['real_property_code']) && 
               !empty($tenant['unit_no']) && 
               !empty($tenant['tenant_code']);
    }
}

/**
 * Simple logger class for QR generation operations
 */
class QRLogger {
    
    public function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $user = $_SESSION['user_name'] ?? 'unknown';
        error_log("[$timestamp] QR Generator - User: $user - $message");
    }
    
    public function error($message) {
        $timestamp = date('Y-m-d H:i:s');
        $user = $_SESSION['user_name'] ?? 'unknown';
        error_log("[$timestamp] QR Generator ERROR - User: $user - $message");
    }
}
?>
