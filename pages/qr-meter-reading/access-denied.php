<?php
/**
 * QR Meter Reading System - Access Denied Page
 * Displays when user does not have permission to access QR Meter Reading modules
 */

// Include authentication and database configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/auth/auth.php';

// Check if user is logged in
if (!isAuthenticated()) {
    header('Location: auth/login.php');
    exit;
}

// Get current user information
$currentUser = getCurrentUsername();
$currentUserId = getCurrentUserId();

// Log access attempt
logActivity($currentUserId, 'ACCESS_DENIED', 'QR Meter Reading - Insufficient permissions', $_SERVER['REMOTE_ADDR']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - QR Meter Reading System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .access-denied-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .access-denied-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            border: 1px solid rgba(243, 244, 246, 0.8);
        }
        
        .access-denied-icon {
            font-size: 4rem;
            color: #dc2626;
            margin-bottom: 1.5rem;
        }
        
        .access-denied-title {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        
        .access-denied-message {
            font-size: 1.125rem;
            color: #374151;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .user-info {
            background: #f3f4f6;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #1e40af;
        }
        
        .user-info h6 {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .user-info p {
            color: #374151;
            margin: 0;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 14px 0 rgba(30, 64, 175, 0.2);
            transition: all 0.2s ease-in-out;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(30, 64, 175, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #374151;
            border: 2px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-secondary:hover {
            border-color: #1e40af;
            color: #1e40af;
            box-shadow: 0 4px 14px 0 rgba(30, 64, 175, 0.1);
        }
        
        .contact-info {
            background: #dbeafe;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
            border-left: 4px solid #1e40af;
        }
        
        .contact-info h6 {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .contact-info p {
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .access-denied-card {
                padding: 2rem;
                margin: 1rem;
            }
            
            .access-denied-title {
                font-size: 1.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="access-denied-container">
        <div class="access-denied-card">
            <!-- Access Denied Icon -->
            <div class="access-denied-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            
            <!-- Title -->
            <h1 class="access-denied-title">Access Denied</h1>
            
            <!-- Message -->
            <div class="access-denied-message">
                <p>You do not have permission to access the QR Meter Reading System.</p>
                <p>This system is restricted to authorized field technicians and administrators.</p>
            </div>
            
            <!-- User Information -->
            <div class="user-info">
                <h6><i class="bi bi-person-circle me-2"></i>Current User</h6>
                <p><strong>User ID:</strong> <?= htmlspecialchars($currentUserId) ?></p>
                <p><strong>Username:</strong> <?= htmlspecialchars($currentUser) ?></p>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="requestAccess()">
                    <i class="bi bi-envelope me-2"></i>Request Access
                </button>
                <button class="btn btn-secondary" onclick="returnToMain()">
                    <i class="bi bi-house me-2"></i>Return to Main System
                </button>
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                <h6><i class="bi bi-info-circle me-2"></i>Need Help?</h6>
                <p><strong>To request access to QR Meter Reading:</strong></p>
                <p>1. Contact your system administrator</p>
                <p>2. Request to be added to the "Field Technician" user group</p>
                <p>3. Ensure you have the "QR Meter Reading" module permission</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function requestAccess() {
            Swal.fire({
                title: 'Request Access',
                html: `
                    <div class="text-start">
                        <p><strong>To request access to QR Meter Reading System:</strong></p>
                        <ol>
                            <li>Contact your system administrator</li>
                            <li>Request to be added to the "Field Technician" user group</li>
                            <li>Ensure you have the "QR Meter Reading" module permission</li>
                        </ol>
                        <p><strong>Required Permissions:</strong></p>
                        <ul>
                            <li>User Group: Field Technician (Group Code: 12)</li>
                            <li>Module: QR Meter Reading (Module ID: 25)</li>
                        </ul>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'I Understand',
                confirmButtonColor: '#1e40af',
                customClass: {
                    popup: 'swal-access-request'
                }
            });
        }
        
        function returnToMain() {
            Swal.fire({
                title: 'Return to Main System',
                text: 'Are you sure you want to return to the main RMS system?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Return',
                cancelButtonText: 'Stay Here',
                confirmButtonColor: '#1e40af',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to main RMS system
                    window.location.href = '../../index.php';
                }
            });
        }
        
        // Show access denied notification on page load
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Access Denied',
                text: 'You do not have permission to access QR Meter Reading System.',
                icon: 'error',
                confirmButtonText: 'I Understand',
                confirmButtonColor: '#dc2626',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        });
    </script>
</body>
</html>
