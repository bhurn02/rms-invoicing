<?php
/**
 * RMS QR Meter Reading System - Authentication Middleware
 * Provides session management and authentication checks
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['qr_user_id']) && !empty($_SESSION['qr_user_id']);
}

/**
 * Get current user ID
 * @return string|null
 */
function getCurrentUserId() {
    return isset($_SESSION['qr_user_id']) ? $_SESSION['qr_user_id'] : null;
}

/**
 * Get current username
 * @return string|null
 */
function getCurrentUsername() {
    return isset($_SESSION['qr_username']) ? $_SESSION['qr_username'] : null;
}

/**
 * Get current company code
 * @return string|null
 */
function getCurrentCompanyCode() {
    return isset($_SESSION['qr_company_code']) ? $_SESSION['qr_company_code'] : null;
}

/**
 * Check if session has expired (8 hours)
 * @return bool
 */
function isSessionExpired() {
    if (!isset($_SESSION['qr_login_time'])) {
        return true;
    }
    
    $session_lifetime = 8 * 60 * 60; // 8 hours
    return (time() - $_SESSION['qr_login_time']) > $session_lifetime;
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function requireAuth() {
    if (!isAuthenticated() || isSessionExpired()) {
        // Clear expired session
        if (isSessionExpired()) {
            session_destroy();
        }
        
        // Redirect to login
        header('Location: auth/login.php');
        exit();
    }
    
    // Update session time on each request
    $_SESSION['qr_login_time'] = time();
}

/**
 * Logout current user
 */
function logout() {
    // Log the logout
    if (isset($_SESSION['qr_user_id'])) {
        logActivity("QR System Logout", "User {$_SESSION['qr_user_id']} logged out");
    }
    
    // Clear session
    session_destroy();
    
    // Clear cookies
    setcookie("userid", "", time() - 3600, "/");
    setcookie("username", "", time() - 3600, "/");
    setcookie("company_code", "", time() - 3600, "/");
    
    // Redirect to login
    header('Location: auth/login.php');
    exit();
}

/**
 * Get user information from database
 * @param string $userId
 * @return array|null
 */
function getUserInfo($userId = null) {
    if (!$userId) {
        $userId = getCurrentUserId();
    }
    
    if (!$userId) {
        return null;
    }
    
    try {
        // Database class should already be loaded from the calling script
        $db = Database::getInstance();
        
        // Query user information using correct column names from s_users table
        $sql = "SELECT user_id, last_name, first_name, middle_initial, group_code, is_active FROM s_users WHERE user_id = ?";
        $result = $db->querySingle($sql, [$userId]);
        return $result;
    } catch (Exception $e) {
        error_log("Error getting user info: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user has permission for specific action
 * @param string $action
 * @return bool
 */
function hasPermission($action) {
    // For now, return true for authenticated users
    // This can be extended with role-based permissions later
    return isAuthenticated();
}

/**
 * Require authentication and QR Meter Reading access
 * This is the main function to use for QR Meter Reading pages
 * @param string $redirectUrl Optional URL to redirect to after login
 */
function requireQRMeterReadingAccess($redirectUrl = null) {
    // First check if user is authenticated
    if (!isAuthenticated() || isSessionExpired()) {
        // Store the redirect URL in session BEFORE any cleanup
        if ($redirectUrl) {
            $_SESSION['qr_redirect_after_login'] = $redirectUrl;
        } else {
            // Fallback: try to get the current page URL from the calling script
            $currentUrl = $_SERVER['REQUEST_URI'];
            $_SESSION['qr_redirect_after_login'] = $currentUrl;
        }
        
        // If session expired, clear ONLY old user data, preserve session
        if (isSessionExpired()) {
            // Unset old authentication data only
            unset($_SESSION['qr_user_id']);
            unset($_SESSION['qr_username']);
            unset($_SESSION['qr_company_code']);
            unset($_SESSION['qr_login_time']);
            unset($_SESSION['qr_ip_address']);
            // DO NOT call session_destroy() - preserve session and our redirect URL
        }
        
        // Redirect to login page
        header('Location: auth/login.php');
        exit();
    }
    
    // Update session time on each request
    $_SESSION['qr_login_time'] = time();
    
    // Then check QR Meter Reading permissions
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/permission-check.php';
    if (!hasQRMeterReadingAccess()) {
        // User is authenticated but doesn't have QR Meter Reading access
        header('Location: auth/access-denied.php');
        exit();
    }
}

/**
 * Get session information for debugging
 * @return array
 */
function getSessionInfo() {
    return array(
        'user_id' => getCurrentUserId(),
        'username' => getCurrentUsername(),
        'company_code' => getCurrentCompanyCode(),
        'login_time' => isset($_SESSION['qr_login_time']) ? $_SESSION['qr_login_time'] : null,
        'ip_address' => isset($_SESSION['qr_ip_address']) ? $_SESSION['qr_ip_address'] : null,
        'session_age' => isset($_SESSION['qr_login_time']) ? time() - $_SESSION['qr_login_time'] : null
    );
}

/**
 * Log activity for audit trail
 * @param string $action
 * @param string $description
 */
function logActivity($action, $description = '') {
    $logFile = __DIR__ . '/../logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $userId = getCurrentUserId() ? getCurrentUserId() : 'unknown';
    $username = getCurrentUsername() ? getCurrentUsername() : 'unknown';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    
    $logEntry = "[{$timestamp}] [{$userId}/{$username}] [{$ip}] {$action}";
    if ($description) {
        $logEntry .= " - {$description}";
    }
    $logEntry .= PHP_EOL;
    
    // Ensure logs directory exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
