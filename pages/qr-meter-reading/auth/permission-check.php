<?php
/**
 * RMS QR Meter Reading System - Permission Validation Functions
 * Provides user access rights validation for QR Meter Reading modules
 */

// Include required files
require_once __DIR__ . '/auth.php';

/**
 * Check if user has QR Meter Reading access
 * @param string $userId Optional user ID, defaults to current user
 * @return bool
 */
function hasQRMeterReadingAccess($userId = null) {
    if (!$userId) {
        $userId = getCurrentUserId();
    }
    
    if (!$userId) {
        return false;
    }
    
    try {
        $pdo = getDatabaseConnection();
        
        // Check if user has access to QR Meter Reading module through user groups
        $sql = "SELECT COUNT(*) as access_count
                FROM s_users u
                INNER JOIN s_user_group_modules ugm ON u.group_code = ugm.group_code
                INNER JOIN s_modules m ON ugm.module_id = m.module_id
                WHERE u.user_id = ? AND m.module_name = 'QR METER READING'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        return $result && $result['access_count'] > 0;
        
    } catch (Exception $e) {
        error_log("Error checking QR Meter Reading access: " . $e->getMessage());
        return false;
    }
}


/**
 * Validate API request permissions
 * Returns JSON error response if no access
 */
function validateAPIPermissions() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Authentication required'
        ]);
        exit();
    }
    
    if (!hasQRMeterReadingAccess()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. You do not have permission to access QR Meter Reading functionality.'
        ]);
        exit();
    }
}

/**
 * Get current user permission status
 * @return array
 */
function getCurrentUserPermissionStatus() {
    $userId = getCurrentUserId();
    
    if (!$userId) {
        return [
            'authenticated' => false,
            'has_access' => false,
            'user_id' => null,
            'message' => 'Not authenticated'
        ];
    }
    
    $hasAccess = hasQRMeterReadingAccess($userId);
    
    return [
        'authenticated' => true,
        'has_access' => $hasAccess,
        'user_id' => $userId,
        'message' => $hasAccess ? 'Access granted' : 'Access denied - insufficient permissions'
    ];
}

/**
 * Get user's group information for QR Meter Reading
 * @param string $userId Optional user ID, defaults to current user
 * @return array|null
 */
function getUserQRMeterReadingGroups($userId = null) {
    if (!$userId) {
        $userId = getCurrentUserId();
    }
    
    if (!$userId) {
        return null;
    }
    
    try {
        $pdo = getDatabaseConnection();
        
        // Get user's groups that have QR Meter Reading access
        $sql = "SELECT ug.group_code, ug.group_desc, m.module_name
                FROM s_users u
                INNER JOIN s_user_group ug ON u.group_code = ug.group_code
                INNER JOIN s_user_group_modules ugm ON ug.group_code = ugm.group_code
                INNER JOIN s_modules m ON ugm.module_id = m.module_id
                WHERE u.user_id = ? AND m.module_name = 'QR METER READING'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetchAll();
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Error getting user QR Meter Reading groups: " . $e->getMessage());
        return null;
    }
}

/**
 * Log permission check activity for audit trail
 * @param string $action
 * @param bool $granted
 * @param string $details
 */
function logPermissionCheck($action, $granted, $details = '') {
    $userId = getCurrentUserId() ? getCurrentUserId() : 'unknown';
    $status = $granted ? 'GRANTED' : 'DENIED';
    $message = "Permission check: {$action} - {$status}";
    
    if ($details) {
        $message .= " - {$details}";
    }
    
    logActivity("Permission Check", $message);
}
?>