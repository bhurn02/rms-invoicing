<?php
/**
 * RMS QR Meter Reading System - Access Denied Page
 * Simple access denied page for users without QR Meter Reading permissions
 */

// Clear the QR session to prevent loops
session_start();
$currentUser = isset($_SESSION['qr_username']) ? $_SESSION['qr_username'] : null;
$currentUserId = isset($_SESSION['qr_user_id']) ? $_SESSION['qr_user_id'] : null;

// Clear QR session data to prevent loops
unset($_SESSION['qr_user_id']);
unset($_SESSION['qr_username']);
unset($_SESSION['qr_company_code']);
unset($_SESSION['qr_login_time']);
unset($_SESSION['qr_ip_address']);

// Clear QR session cookies
setcookie("userid", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/");
setcookie("company_code", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - QR Meter Reading System</title>
    
    <!-- Local Font Files -->
    <link href="../assets/fonts/varela-round.css" rel="stylesheet">
    <link href="../assets/fonts/poppins.css" rel="stylesheet">
    
    <!-- Local CSS File -->
    <link href="../assets/css/access-denied.css?version=<?= time() ?>" rel="stylesheet">
</head>

<body>
    <!-- Main Message -->
    <div class="message">
        Access Denied
    </div>
    
    <!-- Sub Message -->
    <div class="message2">
        <p>You don't have permission to access the QR Meter Reading System.</p>
        <p>Contact your administrator to request access.</p>
    </div>
    
    <!-- User Info (if logged in) -->
    <?php if ($currentUser): ?>
    <div class="user-info">
        <h6>
            <i class="bi bi-person-circle"></i>
            <?php echo htmlspecialchars($currentUser); ?>
        </h6>
        <small><?php echo htmlspecialchars($currentUserId); ?></small>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="login.php" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right"></i>
            Login to System
        </a>
        <a href="../index.php" class="btn btn-outline">
            <i class="bi bi-house"></i>
            Return to Main System
        </a>
    </div>
    
    <!-- Animated Door -->
    <div class="container">
        <div class="neon">403</div>
        <div class="door-frame">
            <div class="door">
                <div class="rectangle"></div>
                <div class="handle"></div>
                <div class="window">
                    <div class="eye"></div>
                    <div class="eye eye2"></div>
                    <div class="leaf"></div> 
                </div>
            </div>  
        </div>
    </div>
    
    <!-- Local Bootstrap Icons -->
    <link href="../assets/css/bootstrap-icons.css" rel="stylesheet">
</body>
</html>