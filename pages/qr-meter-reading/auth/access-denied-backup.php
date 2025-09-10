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
    
    <!-- Bootstrap 5.3+ CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Executive Professional Theme -->
    <link href="../assets/css/custom-theme.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="../assets/css/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Access Denied Page Styles -->
    <link href="../assets/css/access-denied.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="access-denied-card">
                    <div class="access-denied-header">
                        <div class="access-denied-icon">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <h2 class="mb-2">Access Denied</h2>
                        <p class="mb-0 opacity-75">QR Meter Reading System</p>
                    </div>
                    
                    <div class="card-body">
                        <!-- Simplified User Information -->
                        <?php if ($currentUser): ?>
                        <div class="user-info">
                            <h6>
                                <i class="bi bi-person-circle me-2"></i>
                                <?php echo htmlspecialchars($currentUser); ?>
                            </h6>
                            <small><?php echo htmlspecialchars($currentUserId); ?></small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Concise Main Message -->
                        <div class="main-message">
                            <h5>
                                <?php if ($currentUser): ?>
                                    Insufficient Permissions
                                <?php else: ?>
                                    Access Required
                                <?php endif; ?>
                            </h5>
                            <p>
                                <?php if ($currentUser): ?>
                                    You don't have permission to access the QR Meter Reading System. Contact your administrator to request access.
                                <?php else: ?>
                                    Please log in to access the QR Meter Reading System. This system is available to authorized RMS users.
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="button-container">
                            <a href="login.php" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login to System
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house me-2"></i>
                                Return to Main System
                            </a>
                        </div>
                        
                        <!-- Minimal Footer -->
                        <div class="additional-info">
                            <small>
                                <i class="bi bi-shield-check me-1"></i>
                                System Security Policy v1.2.0
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5.3+ JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>