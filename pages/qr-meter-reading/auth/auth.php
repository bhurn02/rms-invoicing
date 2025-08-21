<?php
/**
 * RMS QR Meter Reading System - Authentication Middleware
 * Provides session management and authentication checks
 */

session_start();

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
    return $_SESSION['qr_user_id'] ?? null;
}

/**
 * Get current username
 * @return string|null
 */
function getCurrentUsername() {
    return $_SESSION['qr_username'] ?? null;
}

/**
 * Get current company code
 * @return string|null
 */
function getCurrentCompanyCode() {
    return $_SESSION['qr_company_code'] ?? null;
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
        require_once '../config/config.php';
        $pdo = getDatabaseConnection();
        
        // Query user information (adjust table name as needed)
        $sql = "SELECT userid, username, fullname, email FROM s_users WHERE userid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetch();
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
 * Get session information for debugging
 * @return array
 */
function getSessionInfo() {
    return [
        'user_id' => getCurrentUserId(),
        'username' => getCurrentUsername(),
        'company_code' => getCurrentCompanyCode(),
        'login_time' => $_SESSION['qr_login_time'] ?? null,
        'ip_address' => $_SESSION['qr_ip_address'] ?? null,
        'session_age' => isset($_SESSION['qr_login_time']) ? time() - $_SESSION['qr_login_time'] : null
    ];
}
?>
