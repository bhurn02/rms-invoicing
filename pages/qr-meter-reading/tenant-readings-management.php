<?php
/**
 * RMS QR Meter Reading System - Tenant Readings Management Interface
 * Phase 17: Main management interface for tenant readings
 * 
 * Features:
 * - Card-based interface for displaying readings
 * - Search and filter functionality
 * - Modal dialogs for CRUD operations
 * - Batch operations support
 * - Responsive design
 */

// Require authentication and QR Meter Reading access
require_once 'auth/auth.php';
requireQRMeterReadingAccess($_SERVER['REQUEST_URI']);

// Include configuration for environment detection
require_once 'config/config.php';

// Get current user information
$currentUser = getCurrentUsername();
$currentCompany = getCurrentCompanyCode();

// Get page title
$pageTitle = 'Tenant Readings Management';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - RMS</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/tenant-readings-management.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>RMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-qrcode me-1"></i>QR Scanner
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="tenant-readings-management.php">
                            <i class="fas fa-list-alt me-1"></i>Readings Management
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($currentUser); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-edit me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-list-alt me-2 text-primary"></i>
                            Tenant Readings Management
                        </h1>
                        <p class="text-muted mb-0">Manage tenant meter readings with full CRUD operations</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success" id="btnManualEntry">
                            <i class="fas fa-plus me-1"></i>Manual Entry
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnBatchOperations">
                            <i class="fas fa-tasks me-1"></i>Batch Operations
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="filterSearch" class="form-label">Search</label>
                                <input type="text" class="form-control" id="filterSearch" placeholder="Search readings...">
                            </div>
                            <div class="col-md-2">
                                <label for="filterProperty" class="form-label">Property</label>
                                <select class="form-select" id="filterProperty">
                                    <option value="">All Properties</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filterUnit" class="form-label">Unit</label>
                                <select class="form-select" id="filterUnit">
                                    <option value="">All Units</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="filterDateFrom" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="filterDateFrom">
                            </div>
                            <div class="col-md-2">
                                <label for="filterDateTo" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="filterDateTo">
                            </div>
                            <div class="col-md-2">
                                <label for="filterSource" class="form-label">Source</label>
                                <select class="form-select" id="filterSource">
                                    <option value="">All Sources</option>
                                    <option value="Legacy">Legacy</option>
                                    <option value="QR Scanner">QR Scanner</option>
                                    <option value="Manual Entry">Manual Entry</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-outline-primary w-100" id="btnApplyFilters">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Readings Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i>Tenant Readings
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnSelectAll">
                                <i class="fas fa-check-square me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearSelection">
                                <i class="fas fa-square me-1"></i>Clear Selection
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading readings...</p>
                        </div>

                        <!-- Readings Table -->
                        <div id="readingsTableContainer" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-hover" id="readingsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                            </th>
                                            <th>Tenant</th>
                                            <th>Property</th>
                                            <th>Unit</th>
                                            <th>Date From</th>
                                            <th>Date To</th>
                                            <th>Current Reading</th>
                                            <th>Previous Reading</th>
                                            <th>Consumption</th>
                                            <th>Source</th>
                                            <th>Status</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="readingsTableBody">
                                        <!-- Readings will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <nav aria-label="Readings pagination" class="mt-3">
                                <ul class="pagination justify-content-center" id="pagination">
                                    <!-- Pagination will be generated here -->
                                </ul>
                            </nav>
                        </div>

                        <!-- No Data Message -->
                        <div id="noDataMessage" class="text-center py-5" style="display: none;">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No readings found</h5>
                            <p class="text-muted">Try adjusting your filters or create a new reading.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualEntryModalLabel">
                        <i class="fas fa-plus me-2"></i>Manual Reading Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="manualEntryForm">
                        <div class="row g-3">
                            <!-- Tenant Selection -->
                            <div class="col-12">
                                <label class="form-label">Select Tenant *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="tenantSearchDisplay" placeholder="Click to search and select tenant..." readonly>
                                    <button class="btn btn-outline-primary" type="button" id="btnOpenTenantSelection">
                                        <i class="fas fa-search me-1"></i>Search Tenants
                                    </button>
                                </div>
                                <small class="form-text text-muted">Click "Search Tenants" to find and select a tenant for manual entry.</small>
                            </div>

                            <!-- Selected Tenant Info -->
                            <div class="col-12" id="selectedTenantInfo" style="display: none;">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Selected Tenant</h6>
                                    <div id="tenantDetails"></div>
                                </div>
                            </div>

                            <!-- Reading Dates -->
                            <div class="col-md-6">
                                <label for="dateFrom" class="form-label">Date From *</label>
                                <input type="date" class="form-control" id="dateFrom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="dateTo" class="form-label">Date To *</label>
                                <input type="date" class="form-control" id="dateTo" required>
                            </div>

                            <!-- Billing Dates -->
                            <div class="col-md-6">
                                <label for="billingDateFrom" class="form-label">Billing Date From *</label>
                                <input type="date" class="form-control" id="billingDateFrom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="billingDateTo" class="form-label">Billing Date To *</label>
                                <input type="date" class="form-control" id="billingDateTo" required>
                            </div>

                            <!-- Reading Values -->
                            <div class="col-md-6">
                                <label for="currentReading" class="form-label">Current Reading *</label>
                                <input type="number" class="form-control" id="currentReading" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="previousReading" class="form-label">Previous Reading *</label>
                                <input type="number" class="form-control" id="previousReading" step="0.01" min="0" required>
                            </div>

                            <!-- Calculated Consumption -->
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <strong>Calculated Consumption:</strong> <span id="calculatedConsumption">0.00</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSaveManualEntry">
                        <i class="fas fa-save me-1"></i>Save Reading
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Operations Modal -->
    <div class="modal fade" id="batchOperationsModal" tabindex="-1" aria-labelledby="batchOperationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="batchOperationsModalLabel">
                        <i class="fas fa-tasks me-2"></i>Batch Operations
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Selected Readings:</strong> <span id="selectedReadingsCount">0</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="batchOperation" class="form-label">Operation *</label>
                            <select class="form-select" id="batchOperation" required>
                                <option value="">Select operation...</option>
                                <option value="date_correction">Date Correction</option>
                                <option value="bulk_delete">Bulk Delete</option>
                            </select>
                        </div>

                        <!-- Date Correction Fields -->
                        <div id="dateCorrectionFields" style="display: none;">
                            <div class="col-md-6">
                                <label for="batchDateFrom" class="form-label">Date From *</label>
                                <input type="date" class="form-control" id="batchDateFrom">
                            </div>
                            <div class="col-md-6">
                                <label for="batchDateTo" class="form-label">Date To *</label>
                                <input type="date" class="form-control" id="batchDateTo">
                            </div>
                            <div class="col-md-6">
                                <label for="batchBillingDateFrom" class="form-label">Billing Date From *</label>
                                <input type="date" class="form-control" id="batchBillingDateFrom">
                            </div>
                            <div class="col-md-6">
                                <label for="batchBillingDateTo" class="form-label">Billing Date To *</label>
                                <input type="date" class="form-control" id="batchBillingDateTo">
                            </div>
                        </div>

                        <!-- Bulk Delete Confirmation -->
                        <div id="bulkDeleteConfirmation" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Warning:</strong> This action cannot be undone. All selected readings will be permanently deleted.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnExecuteBatchOperation">
                        <i class="fas fa-play me-1"></i>Execute Operation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Reading Modal -->
    <div class="modal fade" id="editReadingModal" tabindex="-1" aria-labelledby="editReadingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReadingModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Reading
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editReadingForm">
                        <input type="hidden" id="editReadingId">
                        
                        <div class="row g-3">
                            <!-- Tenant Info (Read-only) -->
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Tenant Information</h6>
                                    <div id="editTenantDetails"></div>
                                </div>
                            </div>

                            <!-- Reading Dates -->
                            <div class="col-md-6">
                                <label for="editDateFrom" class="form-label">Date From *</label>
                                <input type="date" class="form-control" id="editDateFrom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editDateTo" class="form-label">Date To *</label>
                                <input type="date" class="form-control" id="editDateTo" required>
                            </div>

                            <!-- Billing Dates -->
                            <div class="col-md-6">
                                <label for="editBillingDateFrom" class="form-label">Billing Date From *</label>
                                <input type="date" class="form-control" id="editBillingDateFrom" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editBillingDateTo" class="form-label">Billing Date To *</label>
                                <input type="date" class="form-control" id="editBillingDateTo" required>
                            </div>

                            <!-- Reading Values -->
                            <div class="col-md-6">
                                <label for="editCurrentReading" class="form-label">Current Reading *</label>
                                <input type="number" class="form-control" id="editCurrentReading" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="editPreviousReading" class="form-label">Previous Reading *</label>
                                <input type="number" class="form-control" id="editPreviousReading" step="0.01" min="0" required>
                            </div>

                            <!-- Calculated Consumption -->
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <strong>Calculated Consumption:</strong> <span id="editCalculatedConsumption">0.00</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEditReading">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Selection Modal -->
    <div class="modal fade" id="tenantSelectionModal" tabindex="-1" aria-labelledby="tenantSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenantSelectionModalLabel">
                        <i class="fas fa-users me-2"></i>Select Tenant for Manual Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Criteria -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="tenantSearchCriteria" class="form-label">Search By</label>
                            <select class="form-select" id="tenantSearchCriteria">
                                <option value="tenant_name">Tenant Name</option>
                                <option value="tenant_code">Tenant Code</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tenantSearchInput" class="form-label">Search Term</label>
                            <input type="text" class="form-control" id="tenantSearchInput" placeholder="Enter search term...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="btnSearchTenants">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                    </div>

                    <!-- Additional Filters -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="tenantPropertyFilter" class="form-label">Property Filter</label>
                            <select class="form-select" id="tenantPropertyFilter">
                                <option value="">All Properties</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tenantUnitFilter" class="form-label">Property & Unit Filter</label>
                            <select class="form-select" id="tenantUnitFilter">
                                <option value="">All Units</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tenantStatusFilter" class="form-label">Status Filter</label>
                            <select class="form-select" id="tenantStatusFilter">
                                <option value="">All Tenants</option>
                                <option value="active">Active Only</option>
                                <option value="terminated">Terminated Only</option>
                            </select>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="tenantSearchResultsContainer">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Search for Tenants</h5>
                            <p class="text-muted">Enter search criteria above to find tenants for manual entry.</p>
                            <div class="alert alert-info mt-3">
                                <small>
                                    <strong>💡 Quick Search Tips:</strong><br>
                                    • <strong>Search:</strong> Use Tenant Name or Tenant Code in the search box<br>
                                    • <strong>Property Filter:</strong> Select property to filter units by that property<br>
                                    • <strong>Unit Filter:</strong> Shows all units by default, or only units from selected property<br>
                                    • <strong>Status Filter:</strong> Filter by Active or Terminated tenants<br>
                                    • <strong>Smart Bidirectional Filtering:</strong> Selecting a property updates the unit list; selecting a unit auto-selects its property
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination for Results -->
                    <nav aria-label="Tenant search pagination" class="mt-3" id="tenantSearchPagination" style="display: none;">
                        <ul class="pagination justify-content-center">
                            <!-- Pagination will be generated here -->
                        </ul>
                    </nav>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmTenantSelection" disabled>
                        <i class="fas fa-check me-1"></i>Select Tenant
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/tenant-readings-management.js"></script>
</body>
</html>
