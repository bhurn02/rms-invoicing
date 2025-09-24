<?php
/**
 * RMS QR Meter Reading System - Logout Page
 * Handles user logout and session cleanup
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

// Log the logout before clearing session
if (isset($_SESSION['qr_user_id'])) {
    logActivity("QR System Logout", "User {$_SESSION['qr_user_id']} logged out from {$_SERVER['REMOTE_ADDR']}");
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Clear RMS cookies
setcookie("userid", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("company_code", "", time() - 3600, "/");

// Ensure no output before redirect
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect to login page
header('Location: login.php');
exit();
?>
