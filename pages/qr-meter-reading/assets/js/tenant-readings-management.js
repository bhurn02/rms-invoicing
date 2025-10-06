/**
 * RMS QR Meter Reading System - Tenant Readings Management JavaScript
 * Phase 17: JavaScript functionality for the tenant readings management interface
 */

// Global variables
let currentPage = 1;
let totalPages = 1;
let currentFilters = {};
let selectedReadings = new Set();
let allReadings = [];
let selectedTenant = null;

// API endpoints
const API_BASE = 'api/readings';
const ENDPOINTS = {
    list: `${API_BASE}.php`,
    create: `${API_BASE}/manual-entry.php`,
    read: `${API_BASE}.php`,
    update: `${API_BASE}.php`,
    delete: `${API_BASE}.php`,
    batchUpdate: `${API_BASE}/batch-update.php`,
    manualEntry: `${API_BASE}/manual-entry.php`,
    tenantSearch: `${API_BASE}/tenants.php`
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
    loadInitialData();
    setupDateDefaults();
});

/**
 * Initialize all event listeners
 */
function initializeEventListeners() {
    // Filter controls
    document.getElementById('btnApplyFilters').addEventListener('click', applyFilters);
    document.getElementById('filterSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Selection controls
    document.getElementById('selectAllCheckbox').addEventListener('change', toggleSelectAll);
    document.getElementById('btnSelectAll').addEventListener('click', selectAllReadings);
    document.getElementById('btnClearSelection').addEventListener('click', clearSelection);
    
    // Action buttons
    document.getElementById('btnManualEntry').addEventListener('click', showManualEntryModal);
    document.getElementById('btnBatchOperations').addEventListener('click', showBatchOperationsModal);
    
    // Manual entry modal
    document.getElementById('btnTenantSearch').addEventListener('click', searchTenants);
    document.getElementById('tenantSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') searchTenants();
    });
    document.getElementById('btnSaveManualEntry').addEventListener('click', saveManualEntryReading);
    
    // Batch operations modal
    document.getElementById('batchOperation').addEventListener('change', toggleBatchOperationFields);
    document.getElementById('btnExecuteBatchOperation').addEventListener('click', function() {
        const operation = document.getElementById('batchOperation').value;
        executeBatchOperation(operation);
    });
    
    // Edit reading modal
    document.getElementById('btnSaveEditReading').addEventListener('click', saveEditReading);
    
    // Reading value calculations
    document.getElementById('currentReading').addEventListener('input', calculateConsumption);
    document.getElementById('previousReading').addEventListener('input', calculateConsumption);
    document.getElementById('editCurrentReading').addEventListener('input', calculateEditConsumption);
    document.getElementById('editPreviousReading').addEventListener('input', calculateEditConsumption);
    
    // Property filter change
    document.getElementById('filterProperty').addEventListener('change', function() {
        loadUnitsForProperty(this.value);
    });
}

/**
 * Load initial data
 */
async function loadInitialData() {
    try {
        showLoading();
        
        // Load properties for filter
        await loadProperties();
        
        // Load initial readings
        await loadReadings();
        
        hideLoading();
    } catch (error) {
        console.error('Error loading initial data:', error);
        showError('Failed to load initial data');
        hideLoading();
    }
}

/**
 * Setup default dates
 */
function setupDateDefaults() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    // Set default filter dates to current month
    document.getElementById('filterDateFrom').value = formatDate(firstDay);
    document.getElementById('filterDateTo').value = formatDate(lastDay);
    
    // Set default manual entry dates
    document.getElementById('dateFrom').value = formatDate(firstDay);
    document.getElementById('dateTo').value = formatDate(lastDay);
    document.getElementById('billingDateFrom').value = formatDate(firstDay);
    document.getElementById('billingDateTo').value = formatDate(lastDay);
}

/**
 * Load properties for filter dropdown
 */
async function loadProperties() {
    try {
        const response = await fetch(ENDPOINTS.tenantSearch);
        const data = await response.json();
        
        if (data.success) {
            const propertySelect = document.getElementById('filterProperty');
            propertySelect.innerHTML = '<option value="">All Properties</option>';
            
            data.data.properties.forEach(property => {
                const option = document.createElement('option');
                option.value = property.real_property_code;
                option.textContent = property.real_property_name;
                propertySelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading properties:', error);
    }
}

/**
 * Load units for selected property
 */
async function loadUnitsForProperty(propertyId) {
    try {
        const unitSelect = document.getElementById('filterUnit');
        unitSelect.innerHTML = '<option value="">All Units</option>';
        
        if (!propertyId) return;
        
        const response = await fetch(`${ENDPOINTS.tenantSearch}?property=${propertyId}`);
        const data = await response.json();
        
        if (data.success) {
            data.data.units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.unit_no.trim();
                option.textContent = `${unit.unit_no.trim()} - ${unit.real_property_name}`;
                unitSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading units:', error);
    }
}

/**
 * Load readings with current filters
 */
async function loadReadings(page = 1) {
    try {
        showLoading();
        const progressNotification = showProgress(`Loading readings...`);
        
        const params = new URLSearchParams({
            page: page,
            limit: 20,
            ...currentFilters
        });
        
        const response = await fetch(`${ENDPOINTS.list}?${params}`);
        const data = await response.json();
        
        // Hide notifications
        hideLoading();
        hideNotification('progress-notification');
        
        if (data.success) {
            allReadings = data.data;
            currentPage = data.pagination.current_page;
            totalPages = data.pagination.total_pages;
            
            renderReadingsTable();
            renderPagination();
            
            if (allReadings.length === 0) {
                showNoDataMessage();
                showWarning('No Readings Found', 'Try adjusting your search filters');
            } else {
                hideNoDataMessage();
                showSuccess('Readings Loaded!', `Found ${allReadings.length} readings`);
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error loading readings:', error);
        hideNotification('progress-notification');
        showError('Failed to load readings: ' + error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Render readings table
 */
function renderReadingsTable() {
    const tbody = document.getElementById('readingsTableBody');
    tbody.innerHTML = '';
    
    allReadings.forEach(reading => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input reading-checkbox" 
                       value="${reading.reading_id}" 
                       ${selectedReadings.has(reading.reading_id) ? 'checked' : ''}>
            </td>
            <td>${escapeHtml(reading.tenant_name)}</td>
            <td>${escapeHtml(reading.property_name)}</td>
            <td>${escapeHtml(reading.unit_no)}</td>
            <td>${formatDate(reading.date_from)}</td>
            <td>${formatDate(reading.date_to)}</td>
            <td>${reading.current_reading}</td>
            <td>${reading.prev_reading}</td>
            <td>${(reading.current_reading - reading.prev_reading).toFixed(2)}</td>
            <td>${renderReadingSource(reading)}</td>
            <td>${renderStatusBadge(reading)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" 
                            onclick="viewReading(${reading.reading_id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" 
                            onclick="editReading(${reading.reading_id})" title="Edit"
                            ${reading.is_invoiced == '1' ? 'disabled' : ''}>
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="deleteReading(${reading.reading_id})" title="Delete"
                            ${reading.is_invoiced == '1' ? 'disabled' : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    // Add event listeners to checkboxes
    tbody.querySelectorAll('.reading-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedReadings.add(parseInt(this.value));
            } else {
                selectedReadings.delete(parseInt(this.value));
            }
            updateSelectionUI();
        });
    });
}

/**
 * Render status badge
 */
function renderReadingSource(reading) {
    const source = reading.reading_source;
    let badgeClass, iconClass;
    
    switch(source) {
        case 'Legacy':
            badgeClass = 'badge bg-secondary';
            iconClass = 'fas fa-history';
            break;
        case 'QR Scanner':
            badgeClass = 'badge bg-primary';
            iconClass = 'fas fa-qrcode';
            break;
        case 'Manual Entry':
            badgeClass = 'badge bg-info';
            iconClass = 'fas fa-edit';
            break;
        default:
            badgeClass = 'badge bg-light text-dark';
            iconClass = 'fas fa-question';
            break;
    }
    
    return `<span class="badge ${badgeClass}"><i class="${iconClass} me-1"></i>${source}</span>`;
}

function renderStatusBadge(reading) {
    // is_invoiced returns "1" or "0" as strings from API
    if (reading.is_invoiced === 1 || reading.is_invoiced === "1") {
        return '<span class="badge bg-success">Invoiced</span>';
    } else {
        return '<span class="badge bg-info">Active</span>';
    }
}

/**
 * Render pagination
 */
function renderPagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadReadings(${currentPage - 1})">Previous</a>`;
    pagination.appendChild(prevLi);
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="loadReadings(${i})">${i}</a>`;
        pagination.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadReadings(${currentPage + 1})">Next</a>`;
    pagination.appendChild(nextLi);
}

/**
 * Apply filters
 */
function applyFilters() {
    currentFilters = {
        search: document.getElementById('filterSearch').value,
        property: document.getElementById('filterProperty').value,
        tenant: document.getElementById('filterUnit').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value,
        source: document.getElementById('filterSource').value
    };
    
    // Remove empty filters
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    loadReadings(1);
}

/**
 * Toggle select all checkbox
 */
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.reading-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
        if (selectAllCheckbox.checked) {
            selectedReadings.add(parseInt(checkbox.value));
        } else {
            selectedReadings.delete(parseInt(checkbox.value));
        }
    });
    
    updateSelectionUI();
}

/**
 * Select all readings
 */
function selectAllReadings() {
    const checkboxes = document.querySelectorAll('.reading-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        selectedReadings.add(parseInt(checkbox.value));
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelectionUI();
}

/**
 * Clear selection
 */
function clearSelection() {
    const checkboxes = document.querySelectorAll('.reading-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    selectedReadings.clear();
    updateSelectionUI();
}

/**
 * Update selection UI
 */
function updateSelectionUI() {
    const count = selectedReadings.size;
    document.getElementById('selectedReadingsCount').textContent = count;
    
    // Update batch operations button state
    const batchBtn = document.getElementById('btnBatchOperations');
    batchBtn.disabled = count === 0;
}

/**
 * Show manual entry modal
 */
function showManualEntryModal() {
    const modal = new bootstrap.Modal(document.getElementById('manualEntryModal'));
    modal.show();
    
    // Reset form
    document.getElementById('manualEntryForm').reset();
    document.getElementById('selectedTenantInfo').style.display = 'none';
    document.getElementById('tenantSearchResults').style.display = 'none';
    selectedTenant = null;
    setupDateDefaults();
}

/**
 * Search tenants
 */
async function searchTenants() {
    try {
        const searchTerm = document.getElementById('tenantSearch').value;
        if (!searchTerm.trim()) {
            document.getElementById('tenantSearchResults').style.display = 'none';
            return;
        }
        
        const response = await fetch(`${ENDPOINTS.tenantSearch}?search=${encodeURIComponent(searchTerm)}&limit=10`);
        const data = await response.json();
        
        if (data.success) {
            renderTenantSearchResults(data.data.tenants);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error searching tenants:', error);
        showError('Failed to search tenants: ' + error.message);
    }
}

/**
 * Render tenant search results
 */
function renderTenantSearchResults(tenants) {
    const container = document.getElementById('tenantSearchResults');
    container.innerHTML = '';
    
    if (tenants.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No tenants found</div>';
    } else {
        tenants.forEach(tenant => {
            const div = document.createElement('div');
            div.className = 'tenant-search-result';
            div.innerHTML = `
                <h6>${escapeHtml(tenant.tenant_name)}</h6>
                <p>${escapeHtml(tenant.property_name)} - ${escapeHtml(tenant.unit_name)}<br>
                Meter: ${escapeHtml(tenant.meter_name)} (${escapeHtml(tenant.meter_type)})</p>
            `;
            div.addEventListener('click', () => selectTenant(tenant));
            container.appendChild(div);
        });
    }
    
    container.style.display = 'block';
}

/**
 * Select tenant
 */
function selectTenant(tenant) {
    selectedTenant = tenant;
    
    // Update UI
    document.getElementById('tenantSearch').value = tenant.tenant_name;
    document.getElementById('tenantSearchResults').style.display = 'none';
    
    // Show tenant details
    const tenantDetails = document.getElementById('tenantDetails');
    tenantDetails.innerHTML = `
        <strong>${escapeHtml(tenant.tenant_name)}</strong><br>
        Property: ${escapeHtml(tenant.property_name)}<br>
        Unit: ${escapeHtml(tenant.unit_name)}<br>
        Meter: ${escapeHtml(tenant.meter_name)} (${escapeHtml(tenant.meter_type)})<br>
        ${tenant.latest_reading ? `Latest Reading: ${tenant.latest_reading} (${formatDate(tenant.latest_reading_date)})` : 'No previous readings'}
    `;
    document.getElementById('selectedTenantInfo').style.display = 'block';
    
    // Set previous reading if available
    if (tenant.latest_reading) {
        document.getElementById('previousReading').value = tenant.latest_reading;
    }
}

/**
 * Calculate consumption for manual entry
 */
function calculateConsumption() {
    const current = parseFloat(document.getElementById('currentReading').value) || 0;
    const previous = parseFloat(document.getElementById('previousReading').value) || 0;
    const consumption = current - previous;
    
    document.getElementById('calculatedConsumption').textContent = consumption.toFixed(2);
}

/**
 * Calculate consumption for edit reading
 */
function calculateEditConsumption() {
    const current = parseFloat(document.getElementById('editCurrentReading').value) || 0;
    const previous = parseFloat(document.getElementById('editPreviousReading').value) || 0;
    const consumption = current - previous;
    
    document.getElementById('editCalculatedConsumption').textContent = consumption.toFixed(2);
}

/**
 * Save manual entry
 */
async function saveManualEntry() {
    try {
        if (!selectedTenant) {
            showError('Please select a tenant');
            return;
        }
        
        const formData = {
            tenant_id: selectedTenant.tenant_id,
            property_id: selectedTenant.property_id,
            unit_id: selectedTenant.unit_id,
            meter_id: selectedTenant.meter_id,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value,
            billing_date_from: document.getElementById('billingDateFrom').value,
            billing_date_to: document.getElementById('billingDateTo').value,
            current_reading: document.getElementById('currentReading').value,
            previous_reading: document.getElementById('previousReading').value
        };
        
        // Validate form
        if (!validateManualEntryForm(formData)) {
            return;
        }
        
        const response = await fetch(ENDPOINTS.manualEntry, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Reading Created Successfully!', 'Manual entry has been saved to the system');
            bootstrap.Modal.getInstance(document.getElementById('manualEntryModal')).hide();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error saving manual entry:', error);
        showError('Failed to save reading: ' + error.message);
    }
}

/**
 * Validate manual entry form
 */
function validateManualEntryForm(formData) {
    const requiredFields = ['date_from', 'date_to', 'billing_date_from', 'billing_date_to', 'current_reading', 'previous_reading'];
    
    for (const field of requiredFields) {
        if (!formData[field]) {
            showError(`Please fill in ${field.replace('_', ' ')}`);
            return false;
        }
    }
    
    const current = parseFloat(formData.current_reading);
    const previous = parseFloat(formData.previous_reading);
    
    if (current < previous) {
        showError('Current reading cannot be less than previous reading');
        return false;
    }
    
    return true;
}

/**
 * Show batch operations modal
 */
function showBatchOperationsModal() {
    if (selectedReadings.size === 0) {
        showError('Please select readings to perform batch operations');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('batchOperationsModal'));
    modal.show();
    
    // Reset form
    document.getElementById('batchOperation').value = '';
    document.getElementById('dateCorrectionFields').style.display = 'none';
    document.getElementById('bulkDeleteConfirmation').style.display = 'none';
}

/**
 * Toggle batch operation fields
 */
function toggleBatchOperationFields() {
    const operation = document.getElementById('batchOperation').value;
    
    document.getElementById('dateCorrectionFields').style.display = operation === 'date_correction' ? 'block' : 'none';
    document.getElementById('bulkDeleteConfirmation').style.display = operation === 'bulk_delete' ? 'block' : 'none';
}

/**
 * Execute batch operation
 */
async function executeBatchOperation() {
    try {
        const operation = document.getElementById('batchOperation').value;
        
        if (!operation) {
            showError('Please select an operation');
            return;
        }
        
        if (selectedReadings.size === 0) {
            showError('Please select readings to perform batch operations');
            return;
        }
        
        const formData = {
            reading_ids: Array.from(selectedReadings),
            operation: operation
        };
        
        // Add operation-specific data
        if (operation === 'date_correction') {
            formData.date_from = document.getElementById('batchDateFrom').value;
            formData.date_to = document.getElementById('batchDateTo').value;
            formData.billing_date_from = document.getElementById('batchBillingDateFrom').value;
            formData.billing_date_to = document.getElementById('batchBillingDateTo').value;
            
            if (!formData.date_from || !formData.date_to || !formData.billing_date_from || !formData.billing_date_to) {
                showError('Please fill in all date fields for date correction');
                return;
            }
        }
        
        // Confirm destructive operations
        if (operation === 'bulk_delete') {
            const confirmed = await Swal.fire({
                title: 'Confirm Delete',
                text: `Are you sure you want to delete ${selectedReadings.size} readings? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete them!'
            });
            
            if (!confirmed.isConfirmed) {
                return;
            }
        }
        
        const response = await fetch(ENDPOINTS.batchUpdate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Batch Operation Completed!', `Success: ${data.data.success_count}, Errors: ${data.data.error_count}`);
            bootstrap.Modal.getInstance(document.getElementById('batchOperationsModal')).hide();
            clearSelection();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error executing batch operation:', error);
        showError('Failed to execute batch operation: ' + error.message);
    }
}

/**
 * View reading
 */
async function viewReading(readingId) {
    try {
        const response = await fetch(`${ENDPOINTS.list}?id=${readingId}`);
        const data = await response.json();
        
        if (data.success) {
            const reading = data.data;
            
            await Swal.fire({
                title: 'Reading Details',
                html: `
                    <div class="text-start">
                        <p><strong>Tenant:</strong> ${escapeHtml(reading.tenant_name)}</p>
                        <p><strong>Property:</strong> ${escapeHtml(reading.property_name)}</p>
                        <p><strong>Unit:</strong> ${escapeHtml(reading.unit_no)}</p>
                        <p><strong>Source:</strong> ${renderReadingSource(reading)}</p>
                        <p><strong>Date From:</strong> ${formatDate(reading.date_from)}</p>
                        <p><strong>Date To:</strong> ${formatDate(reading.date_to)}</p>
                        <p><strong>Current Reading:</strong> ${reading.current_reading}</p>
                        <p><strong>Previous Reading:</strong> ${reading.prev_reading}</p>
                        <p><strong>Consumption:</strong> ${(reading.current_reading - reading.prev_reading).toFixed(2)}</p>
                        <p><strong>Status:</strong> ${renderStatusBadge(reading)}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Close'
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error viewing reading:', error);
        showError('Failed to load reading details: ' + error.message);
    }
}

/**
 * Edit reading
 */
async function editReading(readingId) {
    try {
        const response = await fetch(`${ENDPOINTS.list}?id=${readingId}`);
        const data = await response.json();
        
        if (data.success) {
            const reading = data.data;
            
            // Check if reading is invoiced (explicit check for "1")
            if (reading.is_invoiced === '1' || reading.is_invoiced === 1) {
                Swal.fire({
                    title: 'Cannot Edit',
                    text: 'This reading has been invoiced and cannot be edited.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6c757d'
                });
                return;
            }
            
            // Populate edit form
            document.getElementById('editReadingId').value = reading.reading_id;
            document.getElementById('editDateFrom').value = formatDateForInput(reading.date_from);
            document.getElementById('editDateTo').value = formatDateForInput(reading.date_to);
            document.getElementById('editBillingDateFrom').value = formatDateForInput(reading.billing_date_from);
            document.getElementById('editBillingDateTo').value = formatDateForInput(reading.billing_date_to);
            document.getElementById('editCurrentReading').value = reading.current_reading;
            document.getElementById('editPreviousReading').value = reading.prev_reading;
            
            // Show tenant details
            const tenantDetails = document.getElementById('editTenantDetails');
            tenantDetails.innerHTML = `
                <strong>${escapeHtml(reading.tenant_name)}</strong><br>
                Property: ${escapeHtml(reading.property_name)}<br>
                Unit: ${escapeHtml(reading.unit_no)}<br>
                Source: ${renderReadingSource(reading)}
            `;
            
            // Calculate consumption
            calculateEditConsumption();
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editReadingModal'));
            modal.show();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error editing reading:', error);
        showError('Failed to load reading for editing: ' + error.message);
    }
}

/**
 * Save edit reading
 */
async function saveEditReading() {
    try {
        const readingId = document.getElementById('editReadingId').value;
        
        const formData = {
            reading_id: readingId,
            date_from: document.getElementById('editDateFrom').value,
            date_to: document.getElementById('editDateTo').value,
            billing_date_from: document.getElementById('editBillingDateFrom').value,
            billing_date_to: document.getElementById('editBillingDateTo').value,
            current_reading: document.getElementById('editCurrentReading').value,
            previous_reading: document.getElementById('editPreviousReading').value
        };
        
        // Validate form
        if (!validateEditReadingForm(formData)) {
            return;
        }
        
        const response = await fetch(`${ENDPOINTS.list}?id=${formData.reading_id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Reading Updated Successfully!', 'Changes have been saved to the system');
            bootstrap.Modal.getInstance(document.getElementById('editReadingModal')).hide();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error saving edit reading:', error);
        showError('Failed to update reading: ' + error.message);
    }
}

/**
 * Validate edit reading form
 */
function validateEditReadingForm(formData) {
    const requiredFields = ['date_from', 'date_to', 'billing_date_from', 'billing_date_to', 'current_reading', 'previous_reading'];
    
    for (const field of requiredFields) {
        if (!formData[field]) {
            showError(`Please fill in ${field.replace('_', ' ')}`);
            return false;
        }
    }
    
    const current = parseFloat(formData.current_reading);
    const previous = parseFloat(formData.previous_reading);
    
    if (current < previous) {
        showError('Current reading cannot be less than previous reading');
        return false;
    }
    
    return true;
}

/**
 * Delete reading
 */
async function deleteReading(readingId) {
    try {
        // First check if reading is invoiced
        const getReadingResponse = await fetch(`${ENDPOINTS.list}?id=${readingId}`);
        const getReadingData = await getReadingResponse.json();
        
        if (getReadingData.success) {
            const reading = getReadingData.data;
            
            // Check if reading is invoiced (explicit check for "1")
            if (reading.is_invoiced === '1' || reading.is_invoiced === 1) {
                Swal.fire({
                    title: 'Cannot Delete',
                    text: 'This reading has been invoiced and cannot be deleted.',
            icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6c757d'
                });
                return;
            }
        }
        
        const confirmed = await Swal.fire({
            title: '🚨 Delete Reading',
            text: 'Are you sure you want to delete this reading? This is a critical action and cannot be undone.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        
        if (!confirmed.isConfirmed) {
            return;
        }
        
        const response = await fetch(`${ENDPOINTS.list}?id=${readingId}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showSuccess('Reading Deleted Successfully!', 'Record has been removed from the system');
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error deleting reading:', error);
        showError('Failed to delete reading: ' + error.message);
    }
}

/**
 * Show loading spinner
 */
function showLoading() {
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('readingsTableContainer').style.display = 'none';
    document.getElementById('noDataMessage').style.display = 'none';
}

/**
 * Hide loading spinner
 */
function hideLoading() {
    document.getElementById('loadingSpinner').style.display = 'none';
    document.getElementById('readingsTableContainer').style.display = 'block';
}

/**
 * Show no data message
 */
function showNoDataMessage() {
    document.getElementById('noDataMessage').style.display = 'block';
    document.getElementById('readingsTableContainer').style.display = 'none';
}

/**
 * Hide no data message
 */
function hideNoDataMessage() {
    document.getElementById('noDataMessage').style.display = 'none';
    document.getElementById('readingsTableContainer').style.display = 'block';
}

/**
 * Show success message
 */
function showSuccess(message) {
    Swal.fire({
        title: 'Success!',
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

/**
 * Show Manual Entry Modal
 */
function showManualEntryModal() {
    // Clear form
    document.getElementById('manualEntryForm').reset();
    document.getElementById('selectedTenantInfo').innerHTML = '';
    selectedTenant = null;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('manualEntryModal'));
    modal.show();
}

/**
 * Show Batch Operations Modal
 */
function showBatchOperationsModal() {
    if (selectedReadings.size === 0) {
        showError('Please select readings first');
        return;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('batchOperationsModal'));
    modal.show();
}

/**
 * Save Manual Entry Reading
 */
async function saveManualEntryReading() {
    if (!selectedTenant) {
        showError('Please select a tenant first');
        return;
    }
    
    const form = document.getElementById('manualEntryForm');
    const formData = new FormData(form);
    
    // Validate required fields
    if (!formData.get('current_reading') || !formData.get('date_from') || !formData.get('date_to')) {
        showError('Please fill in all required fields');
        return;
    }
    
    try {
        const manualEntryData = {
            tenant_code: selectedTenant.tenant_code,
            date_from: formData.get('date_from'),
            date_to: formData.get('date_to'),
            billing_date_from: formData.get('billing_date_from'),
            billing_date_to: formData.get('billing_date_to'),
            current_reading: parseFloat(formData.get('current_reading')),
            remarks: formData.get('remarks') || ''
        };
        
        const response = await fetch(ENDPOINTS.manualEntry, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(manualEntryData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Manual reading saved successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            
            // Close modal and refresh data
            const modal = bootstrap.Modal.getInstance(document.getElementById('manualEntryModal'));
            modal.hide();
            clearSelection();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error saving manual reading:', error);
        showError('Error saving manual reading: ' + error.message);
    }
}

/**
 * Save Edit Reading
 */
async function saveEditReading() {
    try {
        const readingId = document.getElementById('editReadingId').value;
        const editData = {
            reading_id: readingId,
            date_from: document.getElementById('editDateFrom').value,
            date_to: document.getElementById('editDateTo').value,
            billing_date_from: document.getElementById('editBillingDateFrom').value,
            billing_date_to: document.getElementById('editBillingDateTo').value,
            current_reading: parseFloat(document.getElementById('editCurrentReading').value),
            prev_reading: parseFloat(document.getElementById('editPreviousReading').value)
        };
        
        const response = await fetch(`${ENDPOINTS.list}?id=${readingId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(editData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Reading updated successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            
            // Close modal and refresh data
            const modal = bootstrap.Modal.getInstance(document.getElementById('editReadingModal'));
            modal.hide();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error saving edited reading:', error);
        showError('Failed to update reading: ' + error.message);
    }
}


/**
 * Toggle batch operation fields based on selected operation
 */
function toggleBatchOperationFields() {
    const operation = document.getElementById('batchOperation').value;
    const dateFields = document.querySelector('#batchOperationsModal .date-fields');
    
    if (operation === 'updating_date' && dateFields) {
        dateFields.style.display = 'block';
    } else if (dateFields) {
        dateFields.style.display = 'none';
    }
}

/**
 * Execute batch operations
 */
async function executeBatchOperation(operation) {
    if (selectedReadings.size === 0) {
        showError('Please select readings first');
        return;
    }
    
    let confirmMessage = `Are you sure you want to ${operation} ${selectedReadings.size} reading(s)?`;
    if (operation === 'delete') {
        confirmMessage = `Are you sure you want to DELETE ${selectedReadings.size} reading(s)? This action cannot be undone.`;
    }
    
    const result = await Swal.fire({
        title: 'Confirm Action',
        text: confirmMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Yes, ${operation}`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: operation === 'delete' ? '#dc3545' : '#0d6efd'
    });
    
    if (!result.isConfirmed) return;
    
    // Show progress notification
    const progressNotification = showProgress(`Processing ${selectedReadings.size} readings...`);
    
    const readingIds = Array.from(selectedReadings);
    const operationData = {
        operation: operation,
        reading_ids: readingIds
    };
    
    try {
        if (operation === 'updating_date') {
            // Handle date correction
            operationData.new_date_from = document.getElementById('batchDateFrom')?.value;
            operationData.new_date_to = document.getElementById('batchDateTo')?.value;
            operationData.new_billing_date_from = document.getElementById('batchBillingDateFrom')?.value;
            operationData.new_billing_date_to = document.getElementById('batchBillingDateTo')?.value;
        }
        
        const response = await fetch(ENDPOINTS.batchUpdate, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(operationData)
        });
        
        const data = await response.json();
        
        // Hide progress notification
        hideNotification('progress-notification');
        
        if (data.success) {
            showSuccess('Batch Operation Completed!', `${operation} completed successfully`);
            
            // Close modal and refresh data
            const modal = bootstrap.Modal.getInstance(document.getElementById('batchOperationsModal'));
            modal.hide();
            clearSelection();
            loadReadings(currentPage);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error(`Error ${operation}:`, error);
        hideNotification('progress-notification');
        showError(`Failed to ${operation}: ${error.message}`);
    }
}

/**
 * Show animated success notification (using QR scanner's beautiful design)
 */
function showSuccess(title, subtitle = '') {
    // Remove any existing success notification
    const existingNotification = document.getElementById('success-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.id = 'success-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #4caf50, #45a049);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(76, 175, 80, 0.3);
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        max-width: 400px;
        animation: slideDownBadge 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-check-circle-fill" style="font-size: 16px;"></i>
                <span style="font-weight: 600;">${title}</span>
            </div>
            ${subtitle ? `<div style="font-size: 12px; opacity: 0.9;">${subtitle}</div>` : ''}
        </div>
        <style>
            @keyframes slideDownBadge {
                from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                to { transform: translateX(-50%) translateY(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-hide after 4 seconds (longer than offline notification for success)
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

/**
 * Show animated warning notification for invoice constraints
 */
function showWarning(title, subtitle = '') {
    const notification = document.createElement('div');
    notification.id = 'warning-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #ff9800, #f57c00);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(255, 152, 0, 0.3);
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        max-width: 400px;
        animation: slideDownWarning 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 16px;"></i>
            <span>${title}</span>
        </div>
        ${subtitle ? `<div style="font-size: 12px; opacity: 0.9; margin-top: 4px;">${subtitle}</div>` : ''}
        <style>
            @keyframes slideDownWarning {
                from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                to { transform: translateX(-50%) translateY(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 5000); // Longer for warnings
}

/**
 * Show animated progress notification for batch operations
 */
function showProgress(message) {
    const notification = document.createElement('div');
    notification.id = 'progress-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, #2196f3, #1976d2);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(33, 150, 243, 0.3);
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        max-width: 400px;
        animation: slideDownProgress 0.3s ease-out;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            <div class="spinner-border spinner-border-sm" role="status" style="width: 16px; height: 16px;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <span>${message}</span>
        </div>
        <style>
            @keyframes slideDownProgress {
                from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                to { transform: translateX(-50%) translateY(0); opacity: 1; }
            }
        </style>
    `;
    
    document.body.appendChild(notification);
    return notification; // Return so we can hide it manually
}

/**
 * Hide notification by ID
 */
function hideNotification(id) {
    const notification = document.getElementById(id);
    if (notification) {
        notification.remove();
    }
}

/**
 * Show error message (keep SweetAlert for errors that need acknowledgment)
 */
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

/**
 * Format API datetime (or date) string to yyyy-mm-dd for input[type="date"]
 */
function formatDateForInput(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
