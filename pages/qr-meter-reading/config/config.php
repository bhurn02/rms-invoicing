<?php
/**
 * RMS QR Meter Reading System - Database Configuration
 * Configuration for connecting to the RMS MSSQL database
 */

// Database configuration
$db_server = '172.16.57.10'; // Update with your MSSQL server
$db_name = 'RMS'; // Update with your database name
$db_user = 'web_app'; // Update with your database user
$db_password = '@webapp123'; // Update with your database password

// Application configuration
define('APP_NAME', 'RMS QR Meter Reading System');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'production'); // production, development, testing

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Pacific/Saipan'); // CNMI timezone

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Database connection function
function getDatabaseConnection() {
    global $db_server, $db_name, $db_user, $db_password;
    
    try {
        $pdo = new PDO(
            "sqlsrv:Server=$db_server;Database=$db_name",
            $db_user,
            $db_password,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8
            )
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception('Database connection failed');
    }
}

// Utility functions
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function formatCurrency($amount) {
    return number_format($amount, 2);
}

function logActivity($action, $details = '') {
    $logEntry = date('Y-m-d H:i:s') . " - $action - $details\n";
    error_log($logEntry, 3, '../logs/activity.log');
}

// Check if database connection is working
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query('SELECT 1 as test');
        $result = $stmt->fetch();
        return $result && $result['test'] == 1;
    } catch (Exception $e) {
        return false;
    }
}

// Initialize logging directory
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
?>
