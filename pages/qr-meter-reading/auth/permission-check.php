<?php
/**
 * QR Meter Reading System - Permission Check Functions
 * Provides functions to validate user access rights for QR Meter Reading modules
 */

// Include database configuration
require_once __DIR__ . '/../config/config.php';

/**
 * Check if user has access to QR Meter Reading module
 * 
 * @param string $userId User ID to check
 * @return bool True if user has access, false otherwise
 */
function hasQRMeterReadingAccess($userId) {
    global $database;
    
    try {
        $sql = "SELECT COUNT(*) as access_count
                FROM s_user_group_users ugu
                INNER JOIN s_user_group_modules ugm ON ugu.group_code = ugm.group_code
                WHERE ugu.user_id = ? AND ugm.module_id = 25";
        
        $result = $database->query($sql, [$userId]);
        
        if ($result && count($result) > 0) {
            return $result[0]['access_count'] > 0;
        }
        
        return false;
    } catch (Exception $e) {
        // Log error and return false for security
        error_log("Permission check error for user $userId: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user has access to QR Meter Reading module (with current user)
 * 
 * @return bool True if current user has access, false otherwise
 */
function hasCurrentUserQRMeterReadingAccess() {
    if (!isAuthenticated()) {
        return false;
    }
    
    $currentUserId = getCurrentUserId();
    return hasQRMeterReadingAccess($currentUserId);
}

/**
 * Require QR Meter Reading access - redirects if user doesn't have access
 * 
 * @return void
 */
function requireQRMeterReadingAccess() {
    if (!hasCurrentUserQRMeterReadingAccess()) {
        // Log access attempt
        if (isAuthenticated()) {
            $currentUserId = getCurrentUserId();
            logActivity($currentUserId, 'ACCESS_DENIED', 'QR Meter Reading - Insufficient permissions', $_SERVER['REMOTE_ADDR']);
        }
        
        // Redirect to access denied page
        header('Location: access-denied.php');
        exit;
    }
}

/**
 * Get user's QR Meter Reading permissions details
 * 
 * @param string $userId User ID to check
 * @return array Permission details or empty array if no access
 */
function getQRMeterReadingPermissions($userId) {
    global $database;
    
    try {
        $sql = "SELECT 
                    ugu.user_id,
                    ug.group_code,
                    ug.group_desc,
                    m.module_id,
                    m.module_name
                FROM s_user_group_users ugu
                INNER JOIN s_user_group ug ON ugu.group_code = ug.group_code
                INNER JOIN s_user_group_modules ugm ON ug.group_code = ugm.group_code
                INNER JOIN s_modules m ON ugm.module_id = m.module_id
                WHERE ugu.user_id = ? AND m.module_id = 25";
        
        $result = $database->query($sql, [$userId]);
        
        if ($result && count($result) > 0) {
            return $result[0];
        }
        
        return [];
    } catch (Exception $e) {
        error_log("Permission details error for user $userId: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if user is in Field Technician group
 * 
 * @param string $userId User ID to check
 * @return bool True if user is in Field Technician group, false otherwise
 */
function isFieldTechnician($userId) {
    global $database;
    
    try {
        $sql = "SELECT COUNT(*) as count
                FROM s_user_group_users ugu
                INNER JOIN s_user_group ug ON ugu.group_code = ug.group_code
                WHERE ugu.user_id = ? AND ug.group_code = 12";
        
        $result = $database->query($sql, [$userId]);
        
        if ($result && count($result) > 0) {
            return $result[0]['count'] > 0;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Field technician check error for user $userId: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all users with QR Meter Reading access
 * 
 * @return array List of users with access
 */
function getUsersWithQRMeterReadingAccess() {
    global $database;
    
    try {
        $sql = "SELECT DISTINCT
                    u.user_id,
                    u.first_name,
                    u.last_name,
                    ug.group_desc,
                    m.module_name
                FROM s_users u
                INNER JOIN s_user_group_users ugu ON u.user_id = ugu.user_id
                INNER JOIN s_user_group ug ON ugu.group_code = ug.group_code
                INNER JOIN s_user_group_modules ugm ON ug.group_code = ugm.group_code
                INNER JOIN s_modules m ON ugm.module_id = m.module_id
                WHERE m.module_id = 25
                ORDER BY u.last_name, u.first_name";
        
        $result = $database->query($sql, []);
        return $result ? $result : [];
    } catch (Exception $e) {
        error_log("Get users with QR access error: " . $e->getMessage());
        return [];
    }
}

/**
 * Validate API request permissions
 * 
 * @return void
 */
function validateAPIPermissions() {
    // Check if user is authenticated
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required',
            'error_code' => 'AUTH_REQUIRED'
        ]);
        exit;
    }
    
    // Check if user has QR Meter Reading access
    if (!hasCurrentUserQRMeterReadingAccess()) {
        // Log access attempt
        $currentUserId = getCurrentUserId();
        logActivity($currentUserId, 'API_ACCESS_DENIED', 'QR Meter Reading API - Insufficient permissions', $_SERVER['REMOTE_ADDR']);
        
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. You do not have permission to access QR Meter Reading system.',
            'error_code' => 'ACCESS_DENIED'
        ]);
        exit;
    }
}

/**
 * Get permission status for current user
 * 
 * @return array Permission status information
 */
function getCurrentUserPermissionStatus() {
    if (!isAuthenticated()) {
        return [
            'authenticated' => false,
            'has_access' => false,
            'user_id' => null,
            'username' => null,
            'permissions' => []
        ];
    }
    
    $currentUserId = getCurrentUserId();
    $currentUsername = getCurrentUsername();
    $hasAccess = hasCurrentUserQRMeterReadingAccess();
    $permissions = $hasAccess ? getQRMeterReadingPermissions($currentUserId) : [];
    
    return [
        'authenticated' => true,
        'has_access' => $hasAccess,
        'user_id' => $currentUserId,
        'username' => $currentUsername,
        'permissions' => $permissions
    ];
}

/**
 * Log permission-related activities
 * 
 * @param string $userId User ID
 * @param string $action Action performed
 * @param string $description Description of action
 * @param string $ipAddress IP address
 * @return void
 */
function logPermissionActivity($userId, $action, $description, $ipAddress = null) {
    if (!$ipAddress) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    logActivity($userId, $action, $description, $ipAddress);
}

?>
