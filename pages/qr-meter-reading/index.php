<?php
/**
 * RMS QR Meter Reading System - Main Interface
 * Requires authentication before access
 */

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Require authentication and QR Meter Reading access
require_once 'auth/auth.php';
requireQRMeterReadingAccess();

// Get current user information
$currentUser = getCurrentUsername();
$currentCompany = getCurrentCompanyCode();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Mobile-specific meta tags for maximum compatibility -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="QR Scanner">
    <meta name="theme-color" content="#1e40af">
    <meta name="msapplication-navbutton-color" content="#1e40af">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- Disable automatic phone number detection -->
    <meta name="format-detection" content="telephone=no">
    
    <!-- Cache Control Headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>QR Meter Reading System - RMS</title>
    
    <!-- Bootstrap 5.3+ CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="assets/css/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Executive Professional Theme -->
    <link href="assets/css/custom-theme.css" rel="stylesheet">
    
    <!-- QR Scanner Specific Styles with Cache-Busting -->
    <link href="assets/css/qr-scanner.css?version=<?= time() ?>" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
</head>
<body class="bg-light">
    <!-- Professional Navigation with Dropdown Menus -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow">
        <div class="container-fluid px-3">
            <!-- Brand -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="bi bi-qr-code me-2 fs-5"></i>
                <span class="d-none d-sm-inline">QR Meter Reading System</span>
                <span class="d-sm-none">QR Scanner</span>
            </a>
            
            <!-- Navigation Items -->
            <div class="d-flex align-items-center ms-auto">
                
                <!-- Tools Dropdown -->
                <div class="dropdown tools-dropdown me-3">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-tools me-1"></i>
                        <span class="d-none d-md-inline">Tools</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Development Tools</h6></li>
                        <li>
                            <a class="dropdown-item" href="camera-test.html" target="_blank">
                                <i class="bi bi-camera-video"></i>
                                Camera Test
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="qr-generator.html" target="_blank">
                                <i class="bi bi-qr-code"></i>
                                QR Generator
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="qr-generator-simple.html" target="_blank">
                                <i class="bi bi-qr-code-scan"></i>
                                Simple QR Generator
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="qr-test.html" target="_blank">
                                <i class="bi bi-bug"></i>
                                QR Test Utility
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($currentUser ?? 'U', 0, 1)); ?>
                        </div>
                        <div class="d-flex flex-column align-items-start">
                            <span class="fw-semibold user-info-text"></span>
                                <?php echo htmlspecialchars($currentUser ?? 'Field Technician'); ?>
                            </span>                            
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Account Management</h6></li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person"></i>
                                Edit Account
                            </a>
                        </li>                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-receipt"></i>
                                Reports
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content - Bootstrap responsive -->
    <main class="container-fluid p-3 p-md-4">
        <div class="row justify-content-center g-3">
            <div class="col-12 col-lg-8 col-xl-6">
                
                <!-- Welcome Card - Bootstrap responsive -->
                <div class="card card-professional shadow-sm border-0 mb-3">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="bi bi-camera-video-fill text-primary display-4"></i>
                        </div>
                        <h1 class="card-title page-title mb-2">Meter Reading Scanner</h1>
                        <p class="card-text body-text text-muted">
                            Welcome, <?php echo htmlspecialchars($currentUser ?? 'Field Technician'); ?>! 
                            Scan the QR code on the meter to automatically populate the reading form
                        </p>
                    </div>
                </div>

                <!-- Scanner Card - Bootstrap responsive -->
                <div class="card card-professional shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white border-0 py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="bi bi-qr-code-scan me-2 fs-5"></i>
                            QR Code Scanner
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Camera Viewport - proper QR scanner styling -->
                        <div id="qr-reader" class="qr-viewport mb-3"></div>
                        
                        <!-- Scanner Controls - Bootstrap grid -->
                        <div class="d-grid gap-2">
                            <button id="start-scanner" class="btn btn-scan-primary btn-lg shadow-sm scanner-visible">
                                <i class="bi bi-camera-fill me-2"></i>
                                Start Scanner
                            </button>
                            <button id="stop-scanner" class="btn btn-secondary btn-lg shadow-sm scanner-hidden">
                                <i class="bi bi-stop-circle me-2"></i>
                                Stop Scanner
                            </button>
                        </div>
                        
                        <!-- Scanner Status -->
                        <div id="scanner-status" class="alert alert-info border-0 mt-3 shadow-sm scanner-hidden">
                            <i class="bi bi-info-circle me-2"></i>
                            <span id="status-text">Ready to scan</span>
                        </div>
                    </div>
                </div>

                <!-- Reading Form Card - Bootstrap responsive -->
                <div id="reading-form-card" class="card card-professional shadow-sm border-0 scanner-hidden">
                    <div class="card-header bg-success text-white border-0 py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="bi bi-check-circle me-2 fs-5"></i>
                            Meter Reading Form
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <!-- Tenant Information Display -->
                        <div id="tenant-info" class="mb-4">
                            <!-- Tenant info will be populated dynamically -->
                        </div>
                        
                        <!-- Last Reading Information Display -->
                        <div id="last-reading-info" class="mb-4">
                            <!-- Last reading info will be populated dynamically -->
                        </div>
                        
                        <form id="reading-form">
                            <!-- Property Information - Bootstrap responsive grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label for="property-id" class="form-label field-label">Property ID</label>
                                    <input type="text" class="form-control form-field" 
                                           id="property-id" name="propertyCode" readonly>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="unit-number" class="form-label field-label">Unit Number</label>
                                    <input type="text" class="form-control form-field" 
                                           id="unit-number" name="unitNo" readonly>
                                </div>
                            </div>
                            
                            <!-- Meter Information - Bootstrap responsive grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label for="meter-id" class="form-label field-label">Meter ID</label>
                                    <input type="text" class="form-control form-field" 
                                           id="meter-id" name="meterId" readonly>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="reading-date" class="form-label field-label">Reading Date</label>
                                    <input type="text" class="form-control form-field" 
                                           id="reading-date" value="<?php echo date('Y-m-d H:i:s'); ?>" readonly>
                                    <small class="text-muted">Set automatically by system</small>
                                </div>
                            </div>
                            
                            <!-- Reading Information - Bootstrap responsive grid -->
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label for="current-meter-reading" class="form-label field-label">
                                        <i class="bi bi-speedometer2 me-2"></i>Current Meter Reading
                                    </label>
                                    <input type="number" class="form-control form-field" 
                                           id="current-meter-reading" name="currentReading" 
                                           step="0.01" min="0.01" required>
                                    <small class="text-muted">Enter the current meter reading value</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="remarks" class="form-label field-label">
                                        <i class="bi bi-chat-text me-2"></i>Remarks
                                    </label>
                                    <textarea class="form-control form-field" 
                                              id="remarks" name="remarks" 
                                              rows="3" placeholder="Optional notes about the reading..."></textarea>
                                    <small class="text-muted">Add any relevant notes or observations</small>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary btn-lg" 
                                        onclick="document.getElementById('reading-form-card').style.display='none'">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Submit Reading
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

    <!-- Bootstrap 5 JavaScript Bundle with Popper -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 for modern alerts -->
    <script src="assets/js/sweetalert2.min.js"></script>
    
    <!-- QR Code Library -->
    <script src="assets/js/html5-qrcode.min.js"></script>
    
    <!-- Custom JavaScript with Cache-Busting -->
    <script src="assets/js/app.js?version=<?= time() ?>"></script>
    
    <!-- Force reload of app.js to prevent caching issues -->
    <script>
        // Clear any cached version of app.js
        if (window.qrMeterApp) {
            console.log('Clearing cached app instance');
            delete window.qrMeterApp;
        }
    </script>
    
    <!-- Initialize Bootstrap Dropdowns -->
    <script>
        // Initialize Bootstrap dropdowns when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Initialize tooltips if any
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            console.log('Bootstrap dropdowns initialized:', dropdownList.length);
        });
    </script>
    
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
