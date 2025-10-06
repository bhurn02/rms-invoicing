<?php
/**
 * RMS QR Meter Reading System - Login Page
 * Integrates with existing RMS authentication system
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

// Redirect if already logged in
if (isset($_SESSION['qr_user_id']) && !empty($_SESSION['qr_user_id'])) {
    // Redirect to original page or main interface
    $redirectUrl = isset($_SESSION['qr_redirect_after_login']) ? $_SESSION['qr_redirect_after_login'] : '../index.php';
    
    // Clear the redirect URL from session
    unset($_SESSION['qr_redirect_after_login']);
    
    // Convert absolute path to relative path from auth directory
    if (strpos($redirectUrl, '/pages/qr-meter-reading/') === 0) {
        $redirectUrl = '..' . substr($redirectUrl, strlen('/pages/qr-meter-reading'));
    }
    
    // Redirect to the original page
    header('Location: ' . $redirectUrl);
    exit();
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');
    $company = trim(isset($_POST['company']) ? $_POST['company'] : '');
    
    if (empty($username) || empty($password) || empty($company)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            $pdo = getDatabaseConnection();
            
            // Use the existing RMS login stored procedure
            $sql = "EXEC sp_s_Login ?, ?, ?, ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $password, $company, $_SERVER['REMOTE_ADDR']]);
            $result = $stmt->fetch();
            
            if ($result && isset($result['x']) && $result['x'] == '1') {
                // Login successful
                $_SESSION['qr_user_id'] = strtoupper($username);
                $_SESSION['qr_username'] = isset($result['username']) ? $result['username'] : $username;
                $_SESSION['qr_company_code'] = $company;
                $_SESSION['qr_login_time'] = time();
                $_SESSION['qr_ip_address'] = $_SERVER['REMOTE_ADDR'];
                
                // Set cookies for compatibility with existing RMS system
                setcookie("userid", strtoupper($username), 0, "/");
                setcookie("username", isset($result['username']) ? $result['username'] : $username, 0, "/");
                setcookie("company_code", $company, 0, "/");
                
                // Log successful login
                logActivity("QR System Login", "User $username logged in successfully from {$_SERVER['REMOTE_ADDR']}");
                
                // Check if user has QR Meter Reading access
                require_once __DIR__ . '/permission-check.php';
                if (hasQRMeterReadingAccess($username)) {
                    // User has access - redirect to original page or main interface
                    $redirectUrl = isset($_SESSION['qr_redirect_after_login']) ? $_SESSION['qr_redirect_after_login'] : '../index.php';
                    
                    // Clear the redirect URL from session
                    unset($_SESSION['qr_redirect_after_login']);
                    
                    // Convert absolute path to relative path from auth directory
                    if (strpos($redirectUrl, '/pages/qr-meter-reading/') === 0) {
                        $redirectUrl = '..' . substr($redirectUrl, strlen('/pages/qr-meter-reading'));
                    }
                    
                    // Redirect to the original page
                    header('Location: ' . $redirectUrl);
                    exit();
                } else {
                    // User doesn't have access - redirect to access denied
                    header('Location: access-denied.php');
                    exit();
                }
            } else {
                $error_message = 'Invalid username, password, or company code.';
                logActivity("QR System Login Failed", "Failed login attempt for user $username from {$_SERVER['REMOTE_ADDR']}");
            }
        } catch (Exception $e) {
            $error_message = 'Login error occurred. Please try again.';
            error_log("QR Login Error: " . $e->getMessage());
        }
    }
}

// Get company list for dropdown
$companies = [];
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT company_code, company_name FROM s_company ORDER BY company_name");
    $companies = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error loading companies: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QR Meter Reading System</title>
    
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
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <div class="mb-3">
                            <i class="bi bi-qr-code" style="font-size: 3rem;"></i>
                        </div>
                        <h2 class="mb-0">QR Meter Reading</h2>
                        <p class="mb-0 opacity-75">System Login</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Subtle Error Message Area -->
                        <div id="login-error-message" class="alert alert-danger d-none mb-3" role="alert" style="font-size: 0.875rem; padding: 0.75rem;">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <span id="error-text"></span>
                        </div>
                        
                        <form method="POST" action="" id="login-form">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-2"></i>Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       required autofocus>
                                <div id="username-error" class="invalid-feedback d-none" style="font-size: 0.8rem;"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="password-error" class="invalid-feedback d-none" style="font-size: 0.8rem;"></div>
                            </div>
                            
                            <div class="mb-4 d-none">
                                <label for="company" class="form-label">
                                    <i class="bi bi-building me-2"></i>Company
                                </label>
                                <select class="form-select" id="company" name="company" required>
                                    <option value="">Select Company</option>
                                    <?php 
                                    $first_company = true;
                                    foreach ($companies as $company): 
                                        $is_selected = false;
                                        
                                        // Check if this company should be selected
                                        if (isset($_POST['company']) && $_POST['company'] === $company['company_code']) {
                                            $is_selected = true;
                                        } elseif (!isset($_POST['company']) && $first_company) {
                                            // Auto-select the first company if no company is already selected
                                            $is_selected = true;
                                        }
                                        
                                        $first_company = false;
                                    ?>
                                        <option value="<?php echo htmlspecialchars($company['company_code']); ?>"
                                                <?php echo $is_selected ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(strtoupper($company['company_name'])); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Secure RMS Authentication
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5.3+ JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Modern UX: Inline error handling instead of SweetAlert
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form');
            const errorMessage = document.getElementById('login-error-message');
            const errorText = document.getElementById('error-text');
            const usernameField = document.getElementById('username');
            const passwordField = document.getElementById('password');
            const usernameError = document.getElementById('username-error');
            const passwordError = document.getElementById('password-error');
            
            // Show server-side error message if present
            <?php if (!empty($error_message)): ?>
            showInlineError('<?php echo addslashes($error_message); ?>');
            <?php endif; ?>
            
            // Real-time validation on blur
            usernameField.addEventListener('blur', function() {
                validateUsername();
            });
            
            passwordField.addEventListener('blur', function() {
                validatePassword();
            });
            
            // Form submission with inline validation
            loginForm.addEventListener('submit', function(e) {
                let hasErrors = false;
                
                // Clear previous errors
                clearAllErrors();
                
                // Validate username
                if (!validateUsername()) {
                    hasErrors = true;
                }
                
                // Validate password
                if (!validatePassword()) {
                    hasErrors = true;
                }
                
                if (hasErrors) {
                    e.preventDefault();
                    return false;
                }
            });
            
            function validateUsername() {
                const username = usernameField.value.trim();
                if (username.length === 0) {
                    showFieldError(usernameField, usernameError, 'Please enter your username');
                    return false;
                } else if (username.length < 3) {
                    showFieldError(usernameField, usernameError, 'Username too short');
                    return false;
                } else {
                    clearFieldError(usernameField, usernameError);
                    return true;
                }
            }
            
            function validatePassword() {
                const password = passwordField.value.trim();
                if (password.length === 0) {
                    showFieldError(passwordField, passwordError, 'Please enter your password');
                    return false;
                } else if (password.length < 4) {
                    showFieldError(passwordField, passwordError, 'Password too short');
                    return false;
                } else {
                    clearFieldError(passwordField, passwordError);
                    return true;
                }
            }
            
            function showFieldError(field, errorElement, message) {
                field.classList.add('is-invalid');
                errorElement.textContent = message;
                errorElement.classList.remove('d-none');
            }
            
            function clearFieldError(field, errorElement) {
                field.classList.remove('is-invalid');
                errorElement.classList.add('d-none');
            }
            
            function showInlineError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('d-none');
                
                // Smooth fade-in animation
                errorMessage.style.opacity = '0';
                errorMessage.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    errorMessage.style.transition = 'all 0.3s ease';
                    errorMessage.style.opacity = '1';
                    errorMessage.style.transform = 'translateY(0)';
                }, 10);
                
                // Auto-hide after 4 seconds with fade-out
                setTimeout(function() {
                    errorMessage.style.transition = 'all 0.3s ease';
                    errorMessage.style.opacity = '0';
                    errorMessage.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        errorMessage.classList.add('d-none');
                        errorMessage.style.transition = '';
                    }, 300);
                }, 4000);
            }
            
            function clearAllErrors() {
                errorMessage.classList.add('d-none');
                clearFieldError(usernameField, usernameError);
                clearFieldError(passwordField, passwordError);
            }
        });
    </script>
</body>
</html>
