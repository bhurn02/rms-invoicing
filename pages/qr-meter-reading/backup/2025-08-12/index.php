<?php
/**
 * RMS QR Meter Reading System - Main Interface
 * Requires authentication before access
 */

// Require authentication
require_once 'auth/auth.php';
requireAuth();

// Get current user information
$currentUser = getCurrentUsername();
$currentCompany = getCurrentCompanyCode();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1e40af">
    <title>QR Meter Reading System - RMS</title>
    
    <!-- Bootstrap 5.3+ CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Executive Professional Theme -->
    <link href="assets/css/custom-theme.css" rel="stylesheet">
    <link href="assets/css/qr-scanner.css" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Mobile-specific styles -->
    <style>
        /* Mobile-specific optimizations */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.1rem !important;
            }
            
            .navbar-text {
                font-size: 0.8rem !important;
            }
            
            .card-professional {
                margin-bottom: 1rem;
                border-radius: 0.75rem;
            }
            
            .card-professional .card-body {
                padding: 1rem;
            }
            
            .btn-lg {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                min-height: 3rem;
            }
            
            .form-field {
                font-size: 16px !important; /* Prevents zoom on iOS */
                padding: 0.75rem;
                min-height: 3rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
            }
            
            /* Hide footer on mobile to save space */
            footer {
                display: none;
            }
            
            /* Optimize scanner viewport for mobile */
            .qr-viewport {
                min-height: 250px;
                max-height: 60vh;
            }
            
            #qr-reader {
                min-height: 250px;
                max-height: 60vh;
            }
        }
        
        /* Touch-friendly buttons */
        .btn {
            min-height: 44px; /* iOS minimum touch target */
            touch-action: manipulation;
        }
        
        /* Prevent text selection on buttons */
        .btn {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Safe area for notched devices */
        @supports (padding: max(0px)) {
            .navbar {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
            }
            
            main {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-qr-code me-2"></i>
                <span class="d-none d-sm-inline">QR Meter Reading System</span>
                <span class="d-inline d-sm-none">QR Scanner</span>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="navbar-text text-light me-2 d-none d-md-block">
                    <i class="bi bi-person-circle me-1"></i>
                    <?php echo htmlspecialchars($currentUser ?? 'Field Technician'); ?>
                </div>
                <div class="navbar-text text-light me-2 d-none d-lg-block">
                    <i class="bi bi-building me-1"></i>
                    <?php echo htmlspecialchars($currentCompany ?? 'Company'); ?>
                </div>
                <!-- Debug link - remove in production -->
                <a href="camera-test.html" class="btn btn-outline-light btn-sm me-2" target="_blank" title="Camera Test">
                    <i class="bi bi-camera-video"></i>
                </a>
                <a href="qr-generator.html" class="btn btn-outline-light btn-sm me-2" target="_blank" title="QR Generator">
                    <i class="bi bi-qr-code"></i>
                </a>
                <a href="qr-generator-simple.html" class="btn btn-outline-light btn-sm me-2" target="_blank" title="Simple QR Generator">
                    <i class="bi bi-qr-code-scan"></i>
                </a>
                <a href="auth/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    <span class="d-none d-sm-inline">Logout</span>
                    <span class="d-inline d-sm-none">Exit</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                
                <!-- Welcome Card -->
                <div class="card card-professional mb-3">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-camera-video-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <h1 class="card-title page-title mb-2">Meter Reading Scanner</h1>
                        <p class="card-text body-text text-muted">
                            Welcome, <?php echo htmlspecialchars($currentUser ?? 'Field Technician'); ?>! 
                            Scan the QR code on the meter to automatically populate the reading form
                        </p>
                    </div>
                </div>

                <!-- Scanner Card -->
                <div class="card card-professional mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-qr-code-scan me-2"></i>
                            QR Code Scanner
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Camera Viewport -->
                        <div id="qr-reader" class="qr-viewport mb-3"></div>
                        
                        <!-- Scanner Controls -->
                        <div class="d-grid gap-2">
                            <button id="start-scanner" class="btn btn-scan-primary btn-lg">
                                <i class="bi bi-camera-fill me-2"></i>
                                Start Scanner
                            </button>
                            <button id="stop-scanner" class="btn btn-secondary btn-lg" style="display: none;">
                                <i class="bi bi-stop-circle me-2"></i>
                                Stop Scanner
                            </button>
                        </div>
                        
                        <!-- Scanner Status -->
                        <div id="scanner-status" class="alert alert-info mt-3" style="display: none;">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="status-text">Ready to scan</span>
                        </div>
                    </div>
                </div>

                <!-- Reading Form Card -->
                <div id="reading-form-card" class="card card-professional" style="display: none;">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Meter Reading Form
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="reading-form">
                            <!-- Property Information -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="property-id" class="form-label field-label">Property ID</label>
                                    <input type="text" class="form-control form-field" id="property-id" readonly>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="unit-number" class="form-label field-label">Unit Number</label>
                                    <input type="text" class="form-control form-field" id="unit-number" readonly>
                                </div>
                            </div>
                            
                            <!-- Meter Information -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="meter-id" class="form-label field-label">Meter ID</label>
                                    <input type="text" class="form-control form-field" id="meter-id" readonly>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="reading-date" class="form-label field-label">Reading Date</label>
                                    <input type="date" class="form-control form-field" id="reading-date" required>
                                </div>
                            </div>
                            
                            <!-- Meter Reading -->
                            <div class="mb-3">
                                <label for="meter-reading" class="form-label field-label">Current Meter Reading</label>
                                <input type="number" class="form-control form-field" id="meter-reading" 
                                       placeholder="Enter current reading" required step="0.01" inputmode="decimal">
                                <div class="form-text helper-text">
                                    Enter the current reading displayed on the meter
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Submit Reading
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Readings Card -->
                <div class="card card-professional mt-3">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Recent Readings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="recent-readings" class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th>Unit</th>
                                        <th>Tenant</th>
                                        <th>Reading</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="readings-table-body">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No recent readings
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5 d-none d-md-block">
        <div class="container text-center">
            <small>&copy; 2025 RMS - QR Meter Reading System | Logged in as: <?php echo htmlspecialchars($currentUser ?? 'Unknown'); ?></small>
        </div>
    </footer>

    <!-- Bootstrap 5.3+ JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>
</body>
</html>
