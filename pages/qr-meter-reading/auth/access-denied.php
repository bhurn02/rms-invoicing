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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Executive Professional Theme -->
    <link href="../assets/css/custom-theme.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .access-denied-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            width: 100%;
        }
        
        .access-denied-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .access-denied-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid #dc3545;
        }
        
        .contact-info {
            background: #e3f2fd;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 4px solid #2196f3;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-outline-secondary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }
        
        .user-info {
            background: #fff3cd;
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            border-left: 4px solid #ffc107;
        }
    </style>
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
                    
                    <div class="card-body p-4">
                        <!-- User Information -->
                        <?php if ($currentUser): ?>
                        <div class="user-info">
                            <h6 class="mb-2">
                                <i class="bi bi-person-circle me-2"></i>
                                Current User: <?php echo htmlspecialchars($currentUser); ?>
                            </h6>
                            <small class="text-muted">
                                User ID: <?php echo htmlspecialchars($currentUserId); ?>
                            </small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Main Message -->
                        <div class="info-card">
                            <h5 class="mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <?php if ($currentUser): ?>
                                    Insufficient Permissions
                                <?php else: ?>
                                    Access Required
                                <?php endif; ?>
                            </h5>
                            <p class="mb-3">
                                <?php if ($currentUser): ?>
                                    You do not have the required permissions to access the QR Meter Reading System. 
                                    This system is restricted to authorized field technicians and administrators.
                                <?php else: ?>
                                    You need to log in to access the QR Meter Reading System. 
                                    This system is restricted to authorized field technicians and administrators.
                                <?php endif; ?>
                            </p>
                            <p class="mb-0">
                                <strong>Required Access:</strong> Field Technician user group membership with QR Meter Reading module permissions.
                            </p>
                        </div>
                        
                        <!-- Request Access Instructions -->
                        <div class="contact-info">
                            <h6 class="mb-3">
                                <i class="bi bi-telephone me-2"></i>
                                <?php if ($currentUser): ?>
                                    Request Access
                                <?php else: ?>
                                    Get Started
                                <?php endif; ?>
                            </h6>
                            <p class="mb-2">
                                <?php if ($currentUser): ?>
                                    To request access to the QR Meter Reading System, please contact your system administrator:
                                <?php else: ?>
                                    To access the QR Meter Reading System, please log in with your credentials:
                                <?php endif; ?>
                            </p>
                            <ul class="mb-3">
                                <?php if ($currentUser): ?>
                                    <li>Contact your IT Administrator or System Manager</li>
                                    <li>Provide your user ID: <strong><?php echo htmlspecialchars($currentUserId); ?></strong></li>
                                    <li>Request assignment to the "Field Technician" user group</li>
                                    <li>Specify that you need access to the "QR Meter Reading" module</li>
                                <?php else: ?>
                                    <li>Use your existing RMS system credentials</li>
                                    <li>Contact your IT Administrator if you don't have an account</li>
                                    <li>Request assignment to the "Field Technician" user group</li>
                                    <li>Specify that you need access to the "QR Meter Reading" module</li>
                                <?php endif; ?>
                            </ul>
                            <p class="mb-0">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Access requests are typically processed within 1-2 business days.
                                </small>
                            </p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                            <a href="login.php" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Login to System
                            </a>
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house me-2"></i>
                                Return to Main System
                            </a>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                This access control ensures system security and data integrity.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5.3+ JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>