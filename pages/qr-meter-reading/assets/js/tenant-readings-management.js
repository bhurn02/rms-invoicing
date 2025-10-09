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
let isUpdatingFilters = false; // Prevent circular filter updates

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
    // Add a small delay to ensure all elements are fully rendered
    setTimeout(() => {
        initializeSelect2();
        initializeEventListeners();
        loadInitialData();
        setupDateDefaults();
    }, 100);
});

/**
 * Initialize Select2 on all select elements
 */
function initializeSelect2() {
    // Select2 default configuration
    const select2Config = {
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder') || 'Select an option';
        },
        allowClear: true,
        minimumResultsForSearch: 5 // Only show search if more than 5 options
    };
    
    // Initialize all select elements with Select2
    $('.form-select').each(function() {
        const $select = $(this);
        const id = $select.attr('id');
        
        // Preserve existing selection
        const currentValue = $select.val();
        
        // Check if select is inside a modal
        const $modal = $select.closest('.modal');
        const config = { ...select2Config };
        
        // If inside modal, set dropdownParent to prevent z-index issues
        if ($modal.length > 0) {
            config.dropdownParent = $modal;
        }
        
        // Initialize Select2
        $select.select2(config);
        
        // Restore selection after initialization
        if (currentValue) {
            $select.val(currentValue).trigger('change.select2');
        }
    });
}

/**
 * Reinitialize Select2 on specific element (for dynamic data loading)
 */
function reinitializeSelect2(selector) {
    const $select = $(selector);
    if ($select.length === 0) return;
    
    // Preserve current selection
    const currentValue = $select.val();
    
    // Destroy existing Select2 instance if it exists
    if ($select.data('select2')) {
        $select.select2('destroy');
    }
    
    // Check if select is inside a modal
    const $modal = $select.closest('.modal');
    const config = {
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: $select.data('placeholder') || 'Select an option',
        allowClear: true,
        minimumResultsForSearch: 5
    };
    
    // If inside modal, set dropdownParent to prevent z-index issues
    if ($modal.length > 0) {
        config.dropdownParent = $modal;
    }
    
    // Reinitialize
    $select.select2(config);
    
    // Restore selection
    if (currentValue) {
        $select.val(currentValue).trigger('change.select2');
    }
}

/**
 * Initialize all event listeners
 */
function initializeEventListeners() {
    // Helper function to safely add event listeners
    function safeAddEventListener(elementId, event, handler) {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener(event, handler);
        } else {
            console.warn(`Element with ID '${elementId}' not found for event listener`);
        }
    }
    
    // Filter controls
    safeAddEventListener('btnApplyFilters', 'click', applyFilters);
    safeAddEventListener('filterSearch', 'keypress', function(e) {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Selection controls
    safeAddEventListener('selectAllCheckbox', 'change', toggleSelectAll);
    safeAddEventListener('btnSelectAll', 'click', selectAllReadings);
    safeAddEventListener('btnClearSelection', 'click', clearSelection);
    
    // Action buttons
    safeAddEventListener('btnManualEntry', 'click', showManualEntryModal);
    safeAddEventListener('btnBatchOperations', 'click', showBatchOperationsModal);
    
    // Manual entry modal
    safeAddEventListener('btnSaveManualEntry', 'click', saveManualEntryReading);
    
    // Manual entry date auto-population and validation
    safeAddEventListener('dateFrom', 'change', function() {
        autoPopulateDates();
        updateTenantCardDates();
    });
    
    // Validate period when dateTo changes manually (after auto-population)
    safeAddEventListener('dateTo', 'change', function() {
        updateTenantCardDates();
        // Validate period conflict if tenant is already selected
        if (selectedTenant && selectedTenantFromModal && previousReadingCache) {
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            if (dateFromInput && dateToInput && dateFromInput.value && dateToInput.value) {
                const conflictCheck = checkReadingPeriodConflict(dateFromInput.value, dateToInput.value);
                if (conflictCheck && conflictCheck.conflict) {
                    const lastPeriod = `${previousReadingCache.dateFrom?.replace(/\\/g,'')} - ${previousReadingCache.dateTo?.replace(/\\/g,'')}`;
                    const selectedPeriod = conflictCheck.newPeriod;
                    const msg = lastPeriod === selectedPeriod 
                        ? `This period (${selectedPeriod}) already has a reading. Please select a different date range.`
                        : `This period overlaps with last reading (${lastPeriod}). Please select a different date range.`;
                    if (!readingPeriodConflictNoticeId) {
                        readingPeriodConflictNoticeId = showWarning('Reading Period Conflict', msg, true);
                    }
                    setSaveButtonEnabled(false);
                } else {
                    if (readingPeriodConflictNoticeId) {
                        hideNotification(readingPeriodConflictNoticeId);
                        readingPeriodConflictNoticeId = null;
                    }
                    if (!negativeUsageNoticeId) {
                        setSaveButtonEnabled(true);
                    }
                }
            }
        }
    });
    
    // Batch operations modal
    safeAddEventListener('batchOperation', 'change', toggleBatchOperationFields);
    safeAddEventListener('btnExecuteBatchOperation', 'click', function() {
        const operation = document.getElementById('batchOperation').value;
        executeBatchOperation(operation);
    });
    
    // Edit reading modal
    safeAddEventListener('btnSaveEditReading', 'click', saveEditReading);
    
    // Reading value calculations
    safeAddEventListener('currentReading', 'input', calculateConsumption);
    safeAddEventListener('previousReading', 'input', calculateConsumption);
    safeAddEventListener('editCurrentReading', 'input', calculateEditConsumption);
    safeAddEventListener('editPreviousReading', 'input', calculateEditConsumption);
    
    // Property filter change - with bidirectional filtering (Select2 compatible)
    $('#filterProperty').on('change', function() {
        if (!isUpdatingFilters) {
            // Only update units when user changes property (not when auto-updated from unit selection)
        updateMainUnitFilterBasedOnProperty();
        loadUnitsForProperty(this.value);
        }
    });
    
    // Unit filter change - with bidirectional filtering (Select2 compatible)
    $('#filterUnit').on('change', function() {
        if (!isUpdatingFilters) {
        updateMainPropertyFilterBasedOnUnit();
        }
    });
    
    // Initialize tenant selection modal
    initializeTenantSelectionModal();
    
    // Modal close event listeners - clear persistent notifications
    const manualEntryModal = document.getElementById('manualEntryModal');
    if (manualEntryModal) {
        manualEntryModal.addEventListener('hidden.bs.modal', function() {
            // Clear persistent notifications when modal is closed
            if (readingPeriodConflictNoticeId) {
                hideNotification(readingPeriodConflictNoticeId);
                readingPeriodConflictNoticeId = null;
            }
            if (negativeUsageNoticeId) {
                hideNotification(negativeUsageNoticeId);
                negativeUsageNoticeId = null;
            }
        });
    }
}

/**
 * Load initial data
 */
async function loadInitialData() {
    try {
        showLoading();
        
        // Load properties for filter
        await loadProperties();
        
        // Load shared units cache for both main filters and tenant modal
        await loadSharedUnitsCache();
        
        // Populate unit dropdown with all units by default
        await loadUnitsForProperty('');
        
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
            
            // Reinitialize Select2 after loading options
            reinitializeSelect2('#filterProperty');
        }
    } catch (error) {
        console.error('Error loading properties:', error);
    }
}

/**
 * Load units for selected property using shared cache
 */
async function loadUnitsForProperty(propertyId) {
    try {
        const unitSelect = document.getElementById('filterUnit');
        unitSelect.innerHTML = '<option value="">All Units</option>';
        
        // Load shared cache if empty
        if (allUnitsCache.length === 0) {
            await loadSharedUnitsCache();
        }
        
        if (!propertyId) {
            // If no property selected, populate with all units
            allUnitsCache.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit.unit_no.trim();
                option.textContent = `${unit.unit_no.trim()} - ${unit.real_property_name}`;
                option.dataset.propertyCode = unit.real_property_code;
                option.dataset.unitNo = unit.unit_no.trim();
                unitSelect.appendChild(option);
            });
            return;
        }
        
        // Use cached units filtered by property
        const filteredUnits = unitsByPropertyCache[propertyId] || [];
        filteredUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit.unit_no.trim();
            option.textContent = `${unit.unit_no.trim()} - ${unit.real_property_name}`;
            option.dataset.propertyCode = unit.real_property_code;
            option.dataset.unitNo = unit.unit_no.trim();
            unitSelect.appendChild(option);
        });
        
        // Reinitialize Select2 after loading units
        reinitializeSelect2('#filterUnit');
    } catch (error) {
        console.error('Error loading units:', error);
    }
}

/**
 * Update main unit filter based on selected property
 */
function updateMainUnitFilterBasedOnProperty() {
    // Clear current unit selection when property changes (already guarded by caller)
    $('#filterUnit').val('').trigger('change');
}

/**
 * Update main property filter based on selected unit
 */
function updateMainPropertyFilterBasedOnUnit() {
    const unitFilter = document.getElementById('filterUnit');
    const selectedUnitValue = unitFilter.value;
    
    if (selectedUnitValue) {
        // Find the selected option to get its data-property-code attribute
        const selectedOption = unitFilter.options[unitFilter.selectedIndex];
        const propertyCode = selectedOption.dataset.propertyCode;
        
        // Auto-select the corresponding property using Select2's val() and trigger()
        const currentPropertyValue = $('#filterProperty').val();
        if (propertyCode && currentPropertyValue !== propertyCode) {
            isUpdatingFilters = true;
            $('#filterProperty').val(propertyCode).trigger('change');
            isUpdatingFilters = false;
        }
    }
}

/**
 * Load readings with current filters
 */
async function loadReadings(page = 1) {
    let progressNotification = null;
    
    try {
        showLoading();
        progressNotification = showProgress(`Loading readings...`);
        
        const params = new URLSearchParams({
            page: page,
            limit: 20,
            ...currentFilters
        });
        
        const response = await fetch(`${ENDPOINTS.list}?${params}`);
        const data = await response.json();
        
        // Hide loading and progress notification
        hideLoading();
        if (progressNotification) {
            hideNotification(progressNotification);
        }
        
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
        if (progressNotification) {
            hideNotification(progressNotification);
        }
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
            <td>${Math.round(reading.current_reading - reading.prev_reading)}</td>
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
            const row = this.closest('tr');
            if (this.checked) {
                selectedReadings.add(parseInt(this.value));
                row.classList.add('table-primary');
            } else {
                selectedReadings.delete(parseInt(this.value));
                row.classList.remove('table-primary');
            }
            updateSelectionUI();
        });
    });
    
    // Make table rows clickable to toggle checkbox
    tbody.querySelectorAll('tr').forEach((row, index) => {
        const checkbox = row.querySelector('.reading-checkbox');
        
        row.addEventListener('click', function(e) {
            // Don't toggle if clicking on the checkbox itself (to avoid double-toggle)
            if (e.target.type === 'checkbox') {
                return;
            }
            
            // Don't toggle if clicking on action buttons
            if (e.target.closest('.btn-group') || e.target.closest('button')) {
                return;
            }
            
            // Toggle the checkbox
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = !checkbox.checked;
                
                // Trigger the change event to update selection
                const readingId = parseInt(checkbox.value);
                if (checkbox.checked) {
                    selectedReadings.add(readingId);
                    row.classList.add('table-primary');
                } else {
                    selectedReadings.delete(readingId);
                    row.classList.remove('table-primary');
                }
                updateSelectionUI();
            }
        });
        
        // Hover effect is now handled by CSS
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
        unit: document.getElementById('filterUnit').value,
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
        const row = checkbox.closest('tr');
        checkbox.checked = selectAllCheckbox.checked;
        if (selectAllCheckbox.checked) {
            selectedReadings.add(parseInt(checkbox.value));
            row.classList.add('table-primary');
        } else {
            selectedReadings.delete(parseInt(checkbox.value));
            row.classList.remove('table-primary');
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
        const row = checkbox.closest('tr');
        checkbox.checked = true;
        selectedReadings.add(parseInt(checkbox.value));
        row.classList.add('table-primary');
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
        const row = checkbox.closest('tr');
        checkbox.checked = false;
        row.classList.remove('table-primary');
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
    const currentInput = document.getElementById('currentReading');
    const previousInput = document.getElementById('previousReading');
    
    const current = parseFloat(currentInput.value);
    const previous = parseFloat(previousInput.value) || 0;
    const consumption = current - previous;
    const rounded = Math.round(consumption);
    
    const calcEl = document.getElementById('calculatedConsumption');
    if (calcEl) {
        calcEl.textContent = rounded;
    }
    
    // Skip validation during programmatic updates (e.g., auto-population)
    if (isAutoPopulating) {
        return;
    }
    
    // Simple validation: current must be entered and greater than previous
    const isCurrentEmpty = !currentInput.value || currentInput.value.trim() === '';
    const isInvalid = isCurrentEmpty || isNaN(current) || current <= previous;
    
    if (isInvalid && !isCurrentEmpty) {
        // Check if notification actually exists in DOM
        const existingNotification = negativeUsageNoticeId ? document.getElementById(negativeUsageNoticeId) : null;
        
        if (!existingNotification) {
            // Notification doesn't exist in DOM, create it
            negativeUsageNoticeId = showWarning('Invalid Usage', 'Current reading must be greater than previous reading', true);
        }
        setSaveButtonEnabled(false);
    } else if (!isCurrentEmpty && !isInvalid) {
        // Valid consumption: clear invalid usage warning
        if (negativeUsageNoticeId) {
            hideNotification(negativeUsageNoticeId);
            negativeUsageNoticeId = null;
        }
        // Enable save button only if no other validation warnings exist
        if (!hasActiveValidationWarnings()) {
            setSaveButtonEnabled(true);
        }
    } else {
        // Empty: keep button disabled but don't show warning yet
        setSaveButtonEnabled(false);
    }
}

/**
 * Calculate consumption for edit reading
 */
function calculateEditConsumption() {
    const current = parseFloat(document.getElementById('editCurrentReading').value) || 0;
    const previous = parseFloat(document.getElementById('editPreviousReading').value) || 0;
    const consumption = current - previous;
    const rounded = Math.round(consumption);
    
    const calcEl = document.getElementById('editCalculatedConsumption');
    if (calcEl) {
        calcEl.textContent = rounded;
    }
    
    // Note: Edit modal doesn't have save button control like manual entry
    // Just show warning for user awareness (non-persistent, auto-dismiss)
    if (rounded < 0) {
        showWarning('Invalid Usage Detected', 'Current reading is lower than previous. Please verify readings.');
    }
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
            tenant_code: selectedTenant.tenant_code,
            real_property_code: selectedTenant.real_property_code,
            unit_no: selectedTenant.unit_no,
            meter_number: selectedTenant.meter_number,
            date_from: document.getElementById('dateFrom').value,
            date_to: document.getElementById('dateTo').value,
            billing_date_from: document.getElementById('billingDateFrom').value,
            billing_date_to: document.getElementById('billingDateTo').value,
            current_reading: document.getElementById('currentReading').value,
            prev_reading: document.getElementById('previousReading').value,
            remarks: document.getElementById('remarks') ? document.getElementById('remarks').value || '' : ''
        };
        
        // Debug: Log form data to help identify issues
        console.log('Form data being validated:', formData);
        
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
    const requiredFields = ['date_from', 'date_to', 'billing_date_from', 'billing_date_to', 'current_reading', 'prev_reading'];
    
    for (const field of requiredFields) {
        if (!formData[field] || formData[field].toString().trim() === '') {
            const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            showError(`Please fill in ${fieldName}`);
            return false;
        }
    }
    
    // Validate numeric fields
    const current = parseFloat(formData.current_reading);
    const previous = parseFloat(formData.prev_reading);
    
    if (isNaN(current) || isNaN(previous)) {
        showError('Current reading and previous reading must be valid numbers');
        return false;
    }
    
    if (current < previous) {
        showError('Current reading cannot be less than previous reading');
        return false;
    }
    
    // Validate date fields
    const dateFields = ['date_from', 'date_to', 'billing_date_from', 'billing_date_to'];
    for (const field of dateFields) {
        const dateValue = new Date(formData[field]);
        if (isNaN(dateValue.getTime())) {
            const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            showError(`Please enter a valid date for ${fieldName}`);
            return false;
        }
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
            
            // Build modern tenant info card (API returns 'Y'/'N' for is_terminated)
            const isActive = reading.is_terminated !== 'Y';
            const statusBadgeClass = isActive ? 'badge bg-success' : 'badge bg-danger';
            const statusBadgeText = isActive ? 'Active' : 'Terminated';
            
            // Build lease information
            let leaseInfo = '';
            if (reading.actual_move_in_date) {
                // Check if date_terminated is a valid date (not 1900-01-01 default)
                const hasValidTerminationDate = reading.date_terminated && 
                    reading.date_terminated !== '1900-01-01 00:00:00.000' &&
                    new Date(reading.date_terminated).getFullYear() > 1900;
                
                if (isActive) {
                    // Active tenant: show lease start date and current duration
                    const currentDuration = calculateLeaseDuration(reading.actual_move_in_date);
                    leaseInfo = `<div class="text-muted small">Lease Start: ${formatDate(reading.actual_move_in_date)} • Duration: ${currentDuration}</div>`;
                } else if (hasValidTerminationDate) {
                    // Terminated tenant with valid end date: show lease period and total duration (split into 2 lines for readability)
                    const leaseDuration = calculateLeaseDuration(reading.actual_move_in_date, reading.date_terminated);
                    leaseInfo = `<div class="text-muted small">Lease Period: ${formatDate(reading.actual_move_in_date)} - ${formatDate(reading.date_terminated)}<br>Duration: ${leaseDuration}</div>`;
                } else {
                    // Terminated tenant without valid end date: show only lease start
                    leaseInfo = `<div class="text-muted small">Lease Start: ${formatDate(reading.actual_move_in_date)}</div>`;
                }
            }
            
            await Swal.fire({
                title: 'Reading Details',
                html: `
                    <div class="text-start">
                        <!-- Modern Tenant Card -->
                        <div class="card mb-3" style="background-color: #e7f3ff; border: 1px solid #b3d9ff;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="text-primary fw-bold">${escapeHtml(reading.tenant_code || 'N/A')}</span>
                                            <span class="text-dark ms-2">${escapeHtml(reading.tenant_name)}</span>
                                        </h6>
                                        <div class="text-muted small mb-1">
                                            ${escapeHtml(reading.property_name)} • Unit ${escapeHtml(reading.unit_no)} • ${escapeHtml(reading.meter_number || 'N/A')}
                                        </div>
                                        ${leaseInfo}
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="${statusBadgeClass}">${statusBadgeText}</span>
                                        ${renderReadingSource(reading)}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Reading Information -->
                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="text-muted small">Date From</div>
                                        <div class="fw-bold">${formatDate(reading.date_from)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="text-muted small">Date To</div>
                                        <div class="fw-bold">${formatDate(reading.date_to)}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="text-muted small">Current Reading</div>
                                        <div class="fw-bold">${reading.current_reading}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <div class="text-muted small">Previous Reading</div>
                                        <div class="fw-bold">${reading.prev_reading}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card" style="background-color: #d4edda; border: 1px solid #c3e6cb;">
                                    <div class="card-body p-2">
                                        <div class="text-success small fw-bold">Consumption</div>
                                        <div class="fw-bold text-success fs-5">${Math.round(reading.current_reading - reading.prev_reading)}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Close',
                width: '600px',
                customClass: {
                    popup: 'reading-details-modal'
                }
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
            
            // Show tenant details with modern card design (API returns 'Y'/'N' for is_terminated)
            const tenantDetails = document.getElementById('editTenantDetails');
            const isActive = reading.is_terminated !== 'Y';
            const statusBadgeClass = isActive ? 'bg-success' : 'bg-danger';
            const statusBadgeText = isActive ? 'Active' : 'Terminated';
            
            // Build lease information
            let leaseInfo = '';
            if (reading.actual_move_in_date) {
                // Check if date_terminated is a valid date (not 1900-01-01 default)
                const hasValidTerminationDate = reading.date_terminated && 
                    reading.date_terminated !== '1900-01-01 00:00:00.000' &&
                    new Date(reading.date_terminated).getFullYear() > 1900;
                
                if (isActive) {
                    // Active tenant: show lease start date and current duration
                    const currentDuration = calculateLeaseDuration(reading.actual_move_in_date);
                    leaseInfo = `<small class="text-muted d-block">Lease Start: ${formatDate(reading.actual_move_in_date)} • Duration: ${currentDuration}</small>`;
                } else if (hasValidTerminationDate) {
                    // Terminated tenant with valid end date: show lease period and total duration (split into 2 lines for readability)
                    const leaseDuration = calculateLeaseDuration(reading.actual_move_in_date, reading.date_terminated);
                    leaseInfo = `<small class="text-muted d-block">Lease Period: ${formatDate(reading.actual_move_in_date)} - ${formatDate(reading.date_terminated)}<br>Duration: ${leaseDuration}</small>`;
                } else {
                    // Terminated tenant without valid end date: show only lease start
                    leaseInfo = `<small class="text-muted d-block">Lease Start: ${formatDate(reading.actual_move_in_date)}</small>`;
                }
            }
            
            tenantDetails.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="tenant-info flex-grow-1">
                        <h6 class="mb-1">
                            <span class="text-primary fw-bold">${escapeHtml(reading.tenant_code || 'N/A')}</span>
                            <span class="text-dark ms-2">${escapeHtml(reading.tenant_name)}</span>
                        </h6>
                        <small class="text-muted d-block mb-1">
                            ${escapeHtml(reading.property_name)} • Unit ${escapeHtml(reading.unit_no)} • ${escapeHtml(reading.meter_number || 'N/A')}
                        </small>
                        ${leaseInfo}
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="status-badge badge ${statusBadgeClass}">${statusBadgeText}</span>
                        ${renderReadingSource(reading)}
                    </div>
                </div>
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
    const selectedTenantInfo = document.getElementById('selectedTenantInfo');
    selectedTenantInfo.innerHTML = '';
    selectedTenantInfo.style.display = 'block'; // Ensure it's visible
    selectedTenant = null;
    
    // Reset reading fields to default values
    const currentReadingInput = document.getElementById('currentReading');
    const previousReadingInput = document.getElementById('previousReading');
    
    // Set flag to prevent validation during reset
    isAutoPopulating = true;
    
    if (currentReadingInput) {
        currentReadingInput.value = '0';
    }
    
    if (previousReadingInput) {
        previousReadingInput.value = '0';
        previousReadingInput.readOnly = false;
        previousReadingInput.disabled = false;
        previousReadingInput.classList.remove('bg-light');
        previousReadingInput.title = '';
    }
    
    // Reset calculated consumption display
    const calcEl = document.getElementById('calculatedConsumption');
    if (calcEl) {
        calcEl.textContent = '0';
    }
    
    // Reset flag after modal reset complete
    isAutoPopulating = false;
    
    // Hide helper text
    const helper = document.getElementById('previousReadingHelper');
    if (helper) {
        helper.style.display = 'none';
    }
    
    // Clear tenant search results
    const tenantSearchResultsContainer = document.getElementById('tenantSearchResultsContainer');
    if (tenantSearchResultsContainer) {
        tenantSearchResultsContainer.innerHTML = '';
    }
    
    // Reset global tenant search variables
    tenantSearchResults = [];
    selectedTenantFromModal = null;
    
    // Clear previous reading cache when modal is opened fresh
    previousReadingCache = null;
    localStorage.removeItem('previousReadingCache');
    
    // Clear persistent notifications
    if (readingPeriodConflictNoticeId) {
        hideNotification(readingPeriodConflictNoticeId);
        readingPeriodConflictNoticeId = null;
    }
    if (negativeUsageNoticeId) {
        hideNotification(negativeUsageNoticeId);
        negativeUsageNoticeId = null;
    }
    
    // Restore tenant search input visibility (in case it was hidden from previous selection)
    const tenantSearchContainer = document.querySelector('.col-12:has(#tenantSearchDisplay)');
    const tenantSearchDisplay = document.getElementById('tenantSearchDisplay');
    
    if (tenantSearchContainer) {
        tenantSearchContainer.style.display = 'block';
    }
    
    if (tenantSearchDisplay) {
        tenantSearchDisplay.value = '';
        // Update placeholder based on current search criteria
        updateSearchPlaceholder();
    }
    
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
    // Check if there are active validation warnings - prevent save if so
    if (readingPeriodConflictNoticeId) {
        console.log('Save blocked: Period conflict active');
        showWarning('Period Conflict', 'Please resolve the reading period conflict before saving');
        return;
    }
    
    if (negativeUsageNoticeId) {
        console.log('Save blocked: Negative usage active');
        showWarning('Invalid Usage', 'Current reading must be greater than previous reading');
        return;
    }
    
    if (!selectedTenant) {
        showWarning('Tenant Required', 'Please select a tenant first');
        return;
    }
    
    const form = document.getElementById('manualEntryForm');
    const formData = new FormData(form);
    
    // Validate all required fields
    const requiredFields = [
        { key: 'current_reading', label: 'Current Reading', element: 'currentReading' },
        { key: 'previousReading', label: 'Previous Reading', element: 'previousReading' },
        { key: 'date_from', label: 'Date From', element: 'dateFrom' },
        { key: 'date_to', label: 'Date To', element: 'dateTo' },
        { key: 'billing_date_from', label: 'Billing Date From', element: 'billingDateFrom' },
        { key: 'billing_date_to', label: 'Billing Date To', element: 'billingDateTo' }
    ];
    
    for (const field of requiredFields) {
        const value = formData.get(field.key);
        console.log(`Validating ${field.key}:`, value, 'Type:', typeof value, 'Trimmed:', value?.toString().trim());
        if (!value || value.toString().trim() === '') {
            console.log(`❌ Validation failed for ${field.key}`);
            const element = document.getElementById(field.element);
            if (element) {
                showInlineValidationError(element, `${field.label} is required`);
                element.focus();
            }
        return;
    }
    }
    console.log('✅ All required fields validation passed');
    
    // Validate current reading > previous reading
    const currentReading = parseFloat(formData.get('current_reading'));
    const previousReading = parseFloat(formData.get('previousReading'));
    
    console.log('Usage Validation - Current:', currentReading, 'Previous:', previousReading);
    
    // Check for invalid numbers
    if (isNaN(currentReading) || isNaN(previousReading)) {
        console.log('❌ Invalid number detected');
        const currentReadingEl = document.getElementById('currentReading');
        if (currentReadingEl && isNaN(currentReading)) {
            showInlineValidationError(currentReadingEl, 'Current reading must be a valid number');
            currentReadingEl.focus();
        } else {
            const previousReadingEl = document.getElementById('previousReading');
            if (previousReadingEl) {
                showInlineValidationError(previousReadingEl, 'Previous reading must be a valid number');
                previousReadingEl.focus();
            }
        }
        return;
    }
    
    if (currentReading < previousReading) {
        console.log('❌ Current reading less than previous');
        const currentReadingEl = document.getElementById('currentReading');
        if (currentReadingEl) {
            showInlineValidationError(currentReadingEl, 'Current reading must be greater than previous reading');
            currentReadingEl.focus();
        }
        return;
    }
    
    console.log('✅ Usage validation passed');
    
    try {
        const manualEntryData = {
            tenant_code: selectedTenant.tenant_code,
            date_from: formData.get('date_from'),
            date_to: formData.get('date_to'),
            billing_date_from: formData.get('billing_date_from'),
            billing_date_to: formData.get('billing_date_to'),
            current_reading: currentReading,
            prev_reading: previousReading,
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
        showWarning('Save Failed', error.message || 'Error saving manual reading. Please check your inputs and try again.');
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
        if (progressNotification) {
            hideNotification(progressNotification);
        }
        
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
        if (progressNotification) {
            hideNotification(progressNotification);
        }
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
 * Show error message (keep SweetAlert for errors that need acknowledgment)
 */
function showError(message) {
    Swal.fire({
        title: 'Error!',
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc2626'
    });
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    // Format as mm/dd/yyyy
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const year = date.getFullYear();
    return `${month}/${day}/${year}`;
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

/**
 * Update tenant card date display when form dates change
 */
function updateTenantCardDates() {
    // Only update if we have a selected tenant and previous reading data
    if (selectedTenant && selectedTenantFromModal) {
        // Re-fetch and update the tenant card with current date period
        fetchAndPopulatePreviousReading(selectedTenantFromModal);
    }
}

/**
 * Load cached previous reading data from localStorage
 */
function loadCachedPreviousReading() {
    try {
        const cached = localStorage.getItem('previousReadingCache');
        if (cached) {
            previousReadingCache = JSON.parse(cached);
            return previousReadingCache;
        }
    } catch (error) {
        console.error('Error loading cached previous reading:', error);
    }
    return null;
}

/**
 * Check if selected reading period conflicts with previous reading
 */
function checkReadingPeriodConflict(newDateFrom, newDateTo) {
    if (!previousReadingCache) {
        return null; // No previous reading to compare
    }
    
    // Handle escaped slashes from cached data
    const prevDateFromStr = previousReadingCache.dateFrom ? previousReadingCache.dateFrom.replace(/\\/g, '') : null;
    const prevDateToStr = previousReadingCache.dateTo ? previousReadingCache.dateTo.replace(/\\/g, '') : null;
    
    if (!prevDateFromStr || !prevDateToStr) {
        return null; // No valid previous dates
    }
    
    const prevDateFrom = new Date(prevDateFromStr);
    const prevDateTo = new Date(prevDateToStr);
    const newDateFromObj = new Date(newDateFrom);
    const newDateToObj = new Date(newDateTo);
    
    if (isNaN(prevDateFrom.getTime()) || isNaN(prevDateTo.getTime()) || 
        isNaN(newDateFromObj.getTime()) || isNaN(newDateToObj.getTime())) {
        return null; // Invalid dates
    }
    
    // Check for period overlap
    const hasOverlap = (newDateFromObj <= prevDateTo && newDateToObj >= prevDateFrom);
    
    if (hasOverlap) {
        return {
            conflict: true,
            previousPeriod: `${prevDateFrom.toLocaleDateString()} - ${prevDateTo.toLocaleDateString()}`,
            newPeriod: `${newDateFromObj.toLocaleDateString()} - ${newDateToObj.toLocaleDateString()}`,
            previousReading: previousReadingCache.currentReading
        };
    }
    
    return { conflict: false };
}

/**
 * Show inline validation error with proper UX
 */
function showInlineValidationError(inputElement, message) {
    // Remove any existing error styling
    inputElement.classList.remove('is-invalid');
    
    // Remove any existing error message
    const existingError = inputElement.parentNode.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error styling to the input
    inputElement.classList.add('is-invalid');
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-circle me-1"></i>
        ${message}
    `;
    
    // Insert error message after the input
    inputElement.parentNode.appendChild(errorDiv);
    
    // Remove error styling when user starts typing
    inputElement.addEventListener('input', function() {
        clearFieldValidation(this);
    }, { once: true });
}

function clearFieldValidation(inputElement) {
    inputElement.classList.remove('is-invalid');
    const errorMsg = inputElement.parentNode.querySelector('.invalid-feedback');
    if (errorMsg) {
        errorMsg.remove();
    }
}

function setSaveButtonEnabled(enabled) {
    const btn = document.getElementById('btnSaveManualEntry');
    if (btn) {
        btn.disabled = !enabled;
    }
}

/**
 * Helper function to populate date fields
 */
function populateDateFields(dateToFormatted, billingDateFromFormatted, billingDateToFormatted) {
    const dateToInput = document.getElementById('dateTo');
    const billingDateFromInput = document.getElementById('billingDateFrom');
    const billingDateToInput = document.getElementById('billingDateTo');
    
    if (dateToInput) {
        dateToInput.value = dateToFormatted;
    }
    if (billingDateFromInput) {
        billingDateFromInput.value = billingDateFromFormatted;
    }
    if (billingDateToInput) {
        billingDateToInput.value = billingDateToFormatted;
    }
}

/**
 * Validate period conflict if dates are already entered
 * Called after tenant selection when previousReadingCache is populated
 */
function validatePeriodConflictIfDatesEntered() {
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    
    console.log('validatePeriodConflictIfDatesEntered called');
    console.log('Date From:', dateFromInput?.value);
    console.log('Date To:', dateToInput?.value);
    console.log('Previous Reading Cache:', previousReadingCache);
    
    if (!dateFromInput || !dateToInput || !dateFromInput.value || !dateToInput.value) {
        console.log('No dates entered, skipping validation');
        return; // No dates entered yet
    }
    
    if (!previousReadingCache) {
        console.log('No previous reading cache, skipping validation');
        return; // No cache available
    }
    
    const conflictCheck = checkReadingPeriodConflict(dateFromInput.value, dateToInput.value);
    console.log('Conflict check result:', conflictCheck);
    
    if (conflictCheck && conflictCheck.conflict) {
        const lastPeriod = `${previousReadingCache.dateFrom?.replace(/\\/g,'')} - ${previousReadingCache.dateTo?.replace(/\\/g,'')}`;
        const selectedPeriod = conflictCheck.newPeriod;
        const msg = lastPeriod === selectedPeriod 
            ? `This period (${selectedPeriod}) already has a reading. Please select a different date range.`
            : `This period overlaps with last reading (${lastPeriod}). Please select a different date range.`;
        
        console.log('CONFLICT DETECTED! Showing warning:', msg);
        console.log('Current readingPeriodConflictNoticeId:', readingPeriodConflictNoticeId);
        
        // Check if notification actually exists in DOM
        const existingNotification = readingPeriodConflictNoticeId ? document.getElementById(readingPeriodConflictNoticeId) : null;
        
        if (!existingNotification) {
            // Notification doesn't exist in DOM, create it
            readingPeriodConflictNoticeId = showWarning('Reading Period Conflict', msg, true);
            console.log('Warning shown, new ID:', readingPeriodConflictNoticeId);
        }
        setSaveButtonEnabled(false);
    } else {
        console.log('No conflict detected');
        // No conflict - ensure notification is cleared and button is enabled
        if (readingPeriodConflictNoticeId) {
            hideNotification(readingPeriodConflictNoticeId);
            readingPeriodConflictNoticeId = null;
        }
        if (!negativeUsageNoticeId) {
            setSaveButtonEnabled(true);
        }
    }
}

/**
 * Check if any validation warnings are currently active
 * Returns true if there are any ERROR or WARNING level notifications
 */
function hasActiveValidationWarnings() {
    return !!(readingPeriodConflictNoticeId || negativeUsageNoticeId);
}

/**
 * Smart notification manager with priority-based handling
 * Priority: ERROR (4) > WARNING (3) > INFO (2) > SUCCESS (1)
 * 
 * Behavior:
 * - ERROR and WARNING: Stack with badge showing count (can coexist)
 * - INFO and SUCCESS: Suppressed when ERROR or WARNING exist (lower priority)
 */
function showSmartNotification(type, title, message, persistent = false) {
    const priority = NOTIFICATION_PRIORITY[type.toUpperCase()] || NOTIFICATION_PRIORITY.INFO;
    
    // SUCCESS messages are suppressed if any ERROR or WARNING notifications are active
    if (type === 'SUCCESS' && hasActiveValidationWarnings()) {
        console.log('[showSmartNotification] Suppressing SUCCESS - validation warnings active');
        return null;
    }
    
    // INFO messages are suppressed if any ERROR or WARNING notifications are active
    if (type === 'INFO' && hasActiveValidationWarnings()) {
        console.log('[showSmartNotification] Suppressing INFO - validation warnings active');
        return null;
    }
    
    // If same type of persistent notification already exists, don't create duplicate
    if (persistent) {
        if (type === 'WARNING' && title.includes('Period Conflict') && readingPeriodConflictNoticeId) {
            return readingPeriodConflictNoticeId;
        }
        if (type === 'WARNING' && title.includes('Invalid Usage') && negativeUsageNoticeId) {
            return negativeUsageNoticeId;
        }
    }
    
    // Show the notification based on type
    if (type === 'ERROR') {
        // ERROR uses SweetAlert (blocking) for critical issues
        showError(message);
        return null;
    } else if (type === 'WARNING') {
        // WARNING stacks with other warnings (persistent, with badge)
        return showWarning(title, message, persistent);
    } else if (type === 'INFO') {
        // INFO uses success notification styling (non-blocking, suppressed by warnings)
        showSuccess(title, message);
        return null;
    } else if (type === 'SUCCESS') {
        // SUCCESS shows green notification (non-blocking, suppressed by warnings)
        showSuccess(title, message);
        return null;
    }
}

/**
 * Auto-populate date fields in manual entry modal with smart validation
 */
function autoPopulateDates() {
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const billingDateFromInput = document.getElementById('billingDateFrom');
    const billingDateToInput = document.getElementById('billingDateTo');
    
    if (!dateFromInput || !dateFromInput.value) {
        return;
    }
    
    try {
        const dateFrom = new Date(dateFromInput.value);
        if (isNaN(dateFrom.getTime())) {
            return;
        }
        
        // Calculate Date To (end of month for Date From)
        const dateTo = new Date(dateFrom.getFullYear(), dateFrom.getMonth() + 1, 0);
        const dateToFormatted = `${dateTo.getFullYear()}-${String(dateTo.getMonth() + 1).padStart(2, '0')}-${String(dateTo.getDate()).padStart(2, '0')}`;
        
        // Calculate Billing Date From (first day of next month)
        const billingDateFrom = new Date(dateFrom.getFullYear(), dateFrom.getMonth() + 1, 1);
        const billingDateFromFormatted = `${billingDateFrom.getFullYear()}-${String(billingDateFrom.getMonth() + 1).padStart(2, '0')}-${String(billingDateFrom.getDate()).padStart(2, '0')}`;
        
        // Calculate Billing Date To (end of next month)
        const billingDateTo = new Date(dateFrom.getFullYear(), dateFrom.getMonth() + 2, 0);
        const billingDateToFormatted = `${billingDateTo.getFullYear()}-${String(billingDateTo.getMonth() + 1).padStart(2, '0')}-${String(billingDateTo.getDate()).padStart(2, '0')}`;
        
        // Only perform cache validation if we have a selected tenant and cached data
        if (!selectedTenant || !selectedTenantFromModal) {
            // No tenant selected yet - just populate dates without validation
            populateDateFields(dateToFormatted, billingDateFromFormatted, billingDateToFormatted);
            // Use smart notification - will be suppressed if validation warnings exist
            showSmartNotification('SUCCESS', 'Dates Auto-Populated', 'Date To and billing dates have been automatically calculated');
            return;
        }
        
        // Load cached previous reading data if not already loaded
        if (!previousReadingCache) {
            loadCachedPreviousReading();
        }
        
        // Only validate if we have cached data for the current tenant
        if (!previousReadingCache || previousReadingCache.tenant_code !== selectedTenant.tenant_code) {
            // No cached data for this tenant - just populate dates
            populateDateFields(dateToFormatted, billingDateFromFormatted, billingDateToFormatted);
            // Use smart notification - will be suppressed if validation warnings exist
            showSmartNotification('SUCCESS', 'Dates Auto-Populated', 'Date To and billing dates have been automatically calculated');
            return;
        }
        
        // Check for reading period conflict
        const conflictCheck = checkReadingPeriodConflict(dateFromInput.value, dateToFormatted);
        
        if (conflictCheck && conflictCheck.conflict) {
            // Animated, persistent (until resolved/closed) notification using last reading period
            const lastPeriod = previousReadingCache ? `${previousReadingCache.dateFrom?.replace(/\\/g,'')} - ${previousReadingCache.dateTo?.replace(/\\/g,'')}` : conflictCheck.previousPeriod;
            const selectedPeriod = conflictCheck.newPeriod;
            
            // Create user-friendly message
            const msg = lastPeriod === selectedPeriod 
                ? `This period (${selectedPeriod}) already has a reading. Please select a different date range.`
                : `This period overlaps with last reading (${lastPeriod}). Please select a different date range.`;
            
            if (!readingPeriodConflictNoticeId) {
                readingPeriodConflictNoticeId = showWarning('Reading Period Conflict', msg, true); // persistent
            } else {
                // Update existing notification
                const existingNotice = document.getElementById(readingPeriodConflictNoticeId);
                if (existingNotice) {
                    const subtitleEl = existingNotice.querySelector('div:last-child');
                    if (subtitleEl) {
                        subtitleEl.textContent = msg;
                    }
                }
            }
            
            // Auto-populate as requested but prevent submission
            populateDateFields(dateToFormatted, billingDateFromFormatted, billingDateToFormatted);
            setSaveButtonEnabled(false);
            
            // Clear any previous inline validation and focus Date From
            clearFieldValidation(dateFromInput);
            const dateToEl = document.getElementById('dateTo');
            if (dateToEl) clearFieldValidation(dateToEl);
            dateFromInput.focus();
        } else {
            // No conflict - proceed with auto-population; also hide persistent notice
            populateDateFields(dateToFormatted, billingDateFromFormatted, billingDateToFormatted);
            clearFieldValidation(dateFromInput);
            const dateToEl = document.getElementById('dateTo');
            if (dateToEl) clearFieldValidation(dateToEl);
            if (readingPeriodConflictNoticeId) {
                hideNotification(readingPeriodConflictNoticeId);
                readingPeriodConflictNoticeId = null;
            }
            // Enable save button only if no other validation errors exist
            if (!hasActiveValidationWarnings()) {
                setSaveButtonEnabled(true);
            }
            // Use smart notification - will be suppressed if other validation warnings exist
            showSmartNotification('SUCCESS', 'Dates Auto-Populated', 'Date To and billing dates have been automatically calculated');
        }
        
    } catch (error) {
        console.error('Error auto-populating dates:', error);
        showError('Error calculating dates: ' + error.message);
    }
}

// ===== PHASE 17.3.2: ENHANCED TENANT SELECTION MODAL =====

// Global variables for tenant selection
let tenantSearchResults = [];
let selectedTenantFromModal = null;
let tenantSearchCurrentPage = 1;
let tenantSearchTotalPages = 1;
let isUpdatingTenantFilters = false; // Prevent circular filter updates in tenant modal
// Shared cache for both main filters and tenant lookup modal
let allUnitsCache = []; // Cache all units for dynamic filtering
let unitsByPropertyCache = {}; // Cache units organized by property code

// Previous reading data cache for smart validation
let previousReadingCache = null;
// Persistent notification id for period conflict
let readingPeriodConflictNoticeId = null;
// Persistent notification id for negative usage
let negativeUsageNoticeId = null;
// Flag to skip validation during programmatic updates
let isAutoPopulating = false;

// Notification priority system (higher = more important)
const NOTIFICATION_PRIORITY = {
    SUCCESS: 1,
    INFO: 2,
    WARNING: 3,
    ERROR: 4
};

// Current active notification tracking
let currentActiveNotification = null;
let notificationQueue = [];

/**
 * Initialize tenant selection modal event listeners
 */
function initializeTenantSelectionModal() {
    // Helper function to safely add event listeners
    function safeAddEventListener(elementId, event, handler) {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener(event, handler);
        } else {
            console.warn(`Element with ID '${elementId}' not found for tenant selection modal`);
        }
    }
    
    // Open tenant selection modal
    safeAddEventListener('btnOpenTenantSelection', 'click', openTenantSelectionModal);
    
    // Search tenants in modal
    safeAddEventListener('btnSearchTenants', 'click', searchTenantsInModal);
    safeAddEventListener('tenantSearchInput', 'keypress', function(e) {
        if (e.key === 'Enter') searchTenantsInModal();
    });
    
    // Filter changes (Select2 compatible)
    $('#tenantPropertyFilter').on('change', function() {
        if (!isUpdatingTenantFilters) {
            // Only update units when user changes property (not when auto-updated from unit selection)
        updateUnitFilterBasedOnProperty();
        searchTenantsInModal();
        }
    });
    $('#tenantUnitFilter').on('change', function() {
        if (!isUpdatingTenantFilters) {
        updatePropertyFilterBasedOnUnit();
        searchTenantsInModal();
        }
    });
    $('#tenantStatusFilter').on('change', searchTenantsInModal);
    
    // Search criteria change - update placeholder
    safeAddEventListener('tenantSearchCriteria', 'change', updateSearchPlaceholder);
    
    // Confirm tenant selection
    safeAddEventListener('btnConfirmTenantSelection', 'click', confirmTenantSelection);
    
    // Load properties and units for filters
    loadPropertiesForTenantFilter();
    loadSharedUnitsCache().then(() => {
        populateUnitFilter(allUnitsCache);
    });
}

/**
 * Open tenant selection modal
 */
function openTenantSelectionModal() {
    const modal = new bootstrap.Modal(document.getElementById('tenantSelectionModal'));
    modal.show();
    
    // Reset search state
    tenantSearchResults = [];
    selectedTenantFromModal = null;
    tenantSearchCurrentPage = 1;
    
    // Reset UI (Select2 compatible) - use change.select2 to avoid triggering search
    document.getElementById('tenantSearchInput').value = '';
    $('#tenantSearchCriteria').val('tenant_name').trigger('change.select2');
    $('#tenantPropertyFilter').val('').trigger('change.select2');
    $('#tenantStatusFilter').val('active').trigger('change.select2');
    document.getElementById('btnConfirmTenantSelection').disabled = true;
    
    // Reset results container
    document.getElementById('tenantSearchResultsContainer').innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Search for Tenants</h5>
            <p class="text-muted">Enter search criteria above to find tenants for manual entry.</p>
        </div>
    `;
    document.getElementById('tenantSearchPagination').style.display = 'none';
}

/**
 * Search tenants in modal
 */
async function searchTenantsInModal() {
    try {
        const searchTerm = document.getElementById('tenantSearchInput').value.trim();
        const searchCriteria = document.getElementById('tenantSearchCriteria').value;
        const propertyFilter = document.getElementById('tenantPropertyFilter').value;
        const unitFilterValue = document.getElementById('tenantUnitFilter').value;
        const statusFilter = document.getElementById('tenantStatusFilter').value;
        
        // Show loading state
        showTenantSearchLoading();
        
        // Build URL parameters
        const params = new URLSearchParams();
        if (searchTerm) {
            params.append('search', searchTerm);
            params.append('search_criteria', searchCriteria);
        }
        if (propertyFilter) params.append('real_property_code', propertyFilter);
        if (unitFilterValue) {
            // Extract unit number from the "Property Unit" format
            const unitNo = unitFilterValue.split(' ').pop(); // Get last part (unit number)
            params.append('unit_no', unitNo);
        }
        
        // Handle status filter
        if (statusFilter === '') {
            // "All Tenants" - include both active and terminated
            params.append('include_terminated', '1');
        } else if (statusFilter === 'active' || statusFilter === 'terminated') {
            // "Active Only" or "Terminated Only" - use status_filter parameter
            params.append('status_filter', statusFilter);
        }
        
        params.append('page', tenantSearchCurrentPage);
        params.append('limit', 20);
        
        const response = await fetch(`${ENDPOINTS.tenantSearch}?${params.toString()}`);
        const data = await response.json();
        
        if (data.success) {
            tenantSearchResults = data.data.tenants;
            tenantSearchTotalPages = data.data.pagination.total_pages;
            
            if (tenantSearchResults.length > 0) {
                renderTenantSearchResultsModal(tenantSearchResults);
                updateTenantSearchPagination();
            } else {
                showTenantSearchEmpty();
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error searching tenants:', error);
        showTenantSearchError('Failed to search tenants: ' + error.message);
    }
}

/**
 * Show loading state for tenant search
 */
function showTenantSearchLoading() {
    document.getElementById('tenantSearchResultsContainer').innerHTML = `
        <div class="tenant-search-loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Searching tenants...</p>
        </div>
    `;
    document.getElementById('tenantSearchPagination').style.display = 'none';
}

/**
 * Show empty state for tenant search
 */
function showTenantSearchEmpty() {
    document.getElementById('tenantSearchResultsContainer').innerHTML = `
        <div class="tenant-search-empty">
            <i class="fas fa-search"></i>
            <h5>No tenants found</h5>
            <p>Try adjusting your search criteria or filters.</p>
        </div>
    `;
    document.getElementById('tenantSearchPagination').style.display = 'none';
}

/**
 * Show error state for tenant search
 */
function showTenantSearchError(message) {
    document.getElementById('tenantSearchResultsContainer').innerHTML = `
        <div class="tenant-search-empty">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <h5>Search Error</h5>
            <p>${escapeHtml(message)}</p>
        </div>
    `;
    document.getElementById('tenantSearchPagination').style.display = 'none';
}

/**
 * Render tenant search results in modal
 */
function renderTenantSearchResultsModal(tenants) {
    const container = document.getElementById('tenantSearchResultsContainer');
    container.innerHTML = '';
    
    tenants.forEach(tenant => {
        const div = document.createElement('div');
        div.className = 'tenant-search-result';
        div.dataset.tenantCode = tenant.tenant_code;
        
        // Determine status (API returns 1 for terminated, 0 for active)
        const isActive = tenant.is_terminated === 0 || tenant.is_terminated === '0';
        const statusClass = isActive ? 'badge bg-success' : 'badge bg-danger';
        const statusText = isActive ? 'Active' : 'Terminated';
        
        // Build tenant details with lease information
        let tenantDetails = `
            <strong>Property:</strong> ${escapeHtml(tenant.real_property_name)} (${escapeHtml(tenant.real_property_code)})<br>
            <strong>Unit:</strong> ${escapeHtml(tenant.unit_no)}<br>
            <strong>Meter:</strong> ${escapeHtml(tenant.meter_number || 'N/A')} - ${escapeHtml(tenant.unit_type || 'N/A')}<br>
        `;
        
        // Add lease period and duration based on tenant status
        if (isActive) {
            // Active tenant: show lease start date and current duration
            const currentDuration = calculateLeaseDuration(tenant.actual_move_in_date);
            tenantDetails += `<strong>Lease Start Date:</strong> ${formatDate(tenant.actual_move_in_date)}<br><strong>Lease Duration:</strong> ${currentDuration}`;
        } else if (tenant.date_terminated) {
            // Terminated tenant: show lease period (start - end) and total duration
            const leaseDuration = calculateLeaseDuration(tenant.actual_move_in_date, tenant.date_terminated);
            tenantDetails += `<strong>Lease Period:</strong> ${formatDate(tenant.actual_move_in_date)} - ${formatDate(tenant.date_terminated)}<br><strong>Lease Duration:</strong> ${leaseDuration}`;
        } else {
            // Fallback for terminated without end date
            tenantDetails += `<strong>Lease Start Date:</strong> ${formatDate(tenant.actual_move_in_date)}`;
        }
        
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="tenant-code">${escapeHtml(tenant.tenant_code)}</div>
                    <div class="tenant-name">${escapeHtml(tenant.tenant_name)}</div>
                    <div class="tenant-details">
                        ${tenantDetails}
                    </div>
                </div>
                <div class="ms-3">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
            </div>
        `;
        
        // Add click handler
        div.addEventListener('click', () => selectTenantFromModal(tenant, div));
        
        container.appendChild(div);
    });
}

/**
 * Select tenant from modal
 */
function selectTenantFromModal(tenant, element) {
    // Remove previous selection
    document.querySelectorAll('.tenant-search-result').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked element
    element.classList.add('selected');
    selectedTenantFromModal = tenant;
    
    // Enable confirm button
    document.getElementById('btnConfirmTenantSelection').disabled = false;
}

/**
 * Fix modal accessibility issues by removing aria-hidden attributes
 */
function fixModalAccessibility() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (modal.style.display !== 'none' && !modal.classList.contains('show')) {
            modal.removeAttribute('aria-hidden');
        }
    });
}

/**
 * Confirm tenant selection
 */
async function confirmTenantSelection() {
    if (!selectedTenantFromModal) {
        showError('Please select a tenant first');
        return;
    }
    
    // Close tenant selection modal first and fix aria-hidden issue
    const tenantSelectionModalElement = document.getElementById('tenantSelectionModal');
    const tenantSelectionModal = bootstrap.Modal.getInstance(tenantSelectionModalElement);
    
    if (tenantSelectionModal) {
        // Remove aria-hidden before closing to prevent focus issues
        tenantSelectionModalElement.removeAttribute('aria-hidden');
        tenantSelectionModal.hide();
    }
    
    // Wait for modal transition to complete before accessing elements
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Ensure the manual entry modal is accessible
    const manualEntryModalElement = document.getElementById('manualEntryModal');
    if (manualEntryModalElement) {
        manualEntryModalElement.removeAttribute('aria-hidden');
    }
    
    // Fix any remaining modal accessibility issues
    fixModalAccessibility();
    
    // Hide the tenant search input and show the selected tenant info
    const tenantSearchContainer = document.querySelector('.col-12:has(#tenantSearchDisplay)');
    const tenantSearchDisplay = document.getElementById('tenantSearchDisplay');
    
    if (tenantSearchContainer && tenantSearchDisplay) {
        // Hide the search input
        tenantSearchContainer.style.display = 'none';
    }
    
    // Since tenantSearchDisplay works, the manual entry modal is accessible
    // Let's directly access the selectedTenantInfo element the same way
    const selectedTenantInfo = document.getElementById('selectedTenantInfo');
    const selectedTenantResult = document.querySelector('.tenant-search-result.selected');
    
    if (!selectedTenantInfo) {
        console.error('selectedTenantInfo element not found');
        return;
    }
    
    // Show the selected tenant info section
    selectedTenantInfo.style.display = 'block';
    
    // Set selected tenant for form submission
    selectedTenant = selectedTenantFromModal;
    
        // Fetch and populate previous reading for the selected tenant
    // The validation will be triggered inside fetchAndPopulatePreviousReading after cache is set
    await fetchAndPopulatePreviousReading(selectedTenantFromModal);
    
    // Only show success notification if no validation warnings are present
    // Don't overwrite period conflict or negative usage warnings
    if (!readingPeriodConflictNoticeId && !negativeUsageNoticeId) {
        showSuccess('Tenant Selected', `${selectedTenantFromModal.tenant_name} selected for manual entry`);
    }
}

/**
 * Update tenant card with previous reading information
 */
function updateTenantCardWithReadingInfo(tenant, readingData, isLoading) {
    const selectedTenantInfo = document.getElementById('selectedTenantInfo');
    if (!selectedTenantInfo) return;
    
    // Determine status (API returns 1 for terminated, 0 for active)
    const isActive = tenant.is_terminated === 0 || tenant.is_terminated === '0';
    const statusBadgeClass = isActive ? 'bg-success' : 'bg-danger';
    const statusBadgeText = isActive ? 'Active' : 'Terminated';
    
    // Build lease information
    let leaseInfo = '';
    if (tenant.actual_move_in_date) {
        const hasValidTerminationDate = tenant.date_terminated && 
            tenant.date_terminated !== '1900-01-01 00:00:00.000' &&
            tenant.date_terminated !== '1900-01-01' &&
            new Date(tenant.date_terminated).getFullYear() > 1900;
        
    if (isActive) {
            const currentDuration = calculateLeaseDuration(tenant.actual_move_in_date);
            leaseInfo = `<small class="text-muted">Lease Start: ${formatDate(tenant.actual_move_in_date)} • Duration: ${currentDuration}</small>`;
        } else if (hasValidTerminationDate) {
            const leaseDuration = calculateLeaseDuration(tenant.actual_move_in_date, tenant.date_terminated);
            leaseInfo = `<small class="text-muted">Lease Period: ${formatDate(tenant.actual_move_in_date)} - ${formatDate(tenant.date_terminated)}<br>Duration: ${leaseDuration}</small>`;
    } else {
            leaseInfo = `<small class="text-muted">Lease Start: ${formatDate(tenant.actual_move_in_date)}</small>`;
        }
    }
    
    // Build previous reading info section
    let readingInfoHtml = '';
    if (isLoading) {
        readingInfoHtml = `
            <div class="mt-3 pt-3 border-top">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <small class="text-muted">Loading previous reading...</small>
                </div>
            </div>
        `;
    } else if (readingData) {
        // Show previous reading information (compact)
        // Use Date From and Date To from the reading data (handle escaped slashes)
        let periodDisplay = 'N/A';
        
        if (readingData.dateFrom && readingData.dateTo) {
            // Handle escaped slashes from API response
            const dateFromStr = readingData.dateFrom.replace(/\\/g, '');
            const dateToStr = readingData.dateTo.replace(/\\/g, '');
            
            const dateFrom = new Date(dateFromStr);
            const dateTo = new Date(dateToStr);
            
            if (!isNaN(dateFrom.getTime()) && !isNaN(dateTo.getTime())) {
                periodDisplay = `${dateFrom.toLocaleDateString()} - ${dateTo.toLocaleDateString()}`;
            }
        }
        
        readingInfoHtml = `
            <div class="mt-2 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center small">
                    <span class="text-muted"><i class="fas fa-chart-line me-1"></i>Last Reading</span>
                    <span class="fw-bold text-primary">${readingData.currentReading || 'N/A'}</span>
                    <span class="text-muted">•</span>
                    <span class="text-muted">Prev</span>
                    <span class="fw-bold">${readingData.prevReading || 'N/A'}</span>
                    <span class="text-muted">•</span>
                    <span class="text-muted">Usage</span>
                    <span class="fw-bold">${readingData.usage || 'N/A'}</span>
                    <span class="text-muted">•</span>
                    <span class="text-muted"><i class="fas fa-calendar me-1"></i>${periodDisplay}</span>
                </div>
            </div>
        `;
    } else {
        // No previous reading found (compact)
        readingInfoHtml = `
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>No previous reading found</small>
            </div>
        `;
    }

    selectedTenantInfo.innerHTML = `
        <div class="selected-tenant-compact">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="tenant-info flex-grow-1">
                    <h6 class="mb-1">
                        <span class="text-primary fw-bold">${escapeHtml(tenant.tenant_code)}</span>
                        <span class="text-dark ms-2">${escapeHtml(tenant.tenant_name)}</span>
                    </h6>
                    <small class="text-muted d-block mb-1">
                        ${escapeHtml(tenant.real_property_name)} • Unit ${escapeHtml(tenant.unit_no)} • ${escapeHtml(tenant.meter_number || 'N/A')}
                    </small>
                    ${leaseInfo}
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-badge badge ${statusBadgeClass}">${statusBadgeText}</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnChangeTenant">
                        <i class="fas fa-edit me-1"></i>Change
                    </button>
                </div>
            </div>
            ${readingInfoHtml}
        </div>
    `;
    
    // Add event listener for the Change button
    const changeButton = selectedTenantInfo.querySelector('#btnChangeTenant');
    if (changeButton) {
        changeButton.addEventListener('click', function() {
            // Show the tenant search input again
            const tenantSearchContainer = document.querySelector('.col-12:has(#tenantSearchDisplay)');
            if (tenantSearchContainer) {
                tenantSearchContainer.style.display = 'block';
            }
            
            // Hide the selected tenant info
            selectedTenantInfo.style.display = 'none';
            
            // Clear the selected tenant and cache
            selectedTenant = null;
            previousReadingCache = null;
            localStorage.removeItem('previousReadingCache');
            
            // Reset previous reading field when changing tenant
            const previousReadingInput = document.getElementById('previousReading');
            if (previousReadingInput) {
                previousReadingInput.value = '';
                previousReadingInput.readOnly = false;
                previousReadingInput.disabled = false;
                previousReadingInput.classList.remove('bg-light');
                previousReadingInput.title = '';
            }
            
            // Hide helper text
            const helper = document.getElementById('previousReadingHelper');
            if (helper) {
                helper.style.display = 'none';
            }
            
            // Reset current reading and consumption
            const currentReadingInput = document.getElementById('currentReading');
            if (currentReadingInput) {
                currentReadingInput.value = '';
            }
            calculateConsumption();
            
            // Open the tenant selection modal
            const btnOpenTenantSelection = document.getElementById('btnOpenTenantSelection');
            if (btnOpenTenantSelection) {
                btnOpenTenantSelection.click();
            }
        });
    }
}

/**
 * Fetch and populate previous reading for selected tenant
 */
async function fetchAndPopulatePreviousReading(tenant) {
    try {
        // Get property code and unit number
        const propertyCode = tenant.real_property_code;
        const unitNo = tenant.unit_no;
        
        if (!propertyCode || !unitNo) {
            console.warn('Missing property code or unit number');
            return;
        }
        
        // Show loading indicator on previous reading field
        const previousReadingInput = document.getElementById('previousReading');
        if (previousReadingInput) {
            previousReadingInput.value = 'Loading...';
            previousReadingInput.disabled = true;
        }
        
        // Show loading in tenant card
        updateTenantCardWithReadingInfo(tenant, null, true);
        
        // Fetch previous reading from API
        const response = await fetch(`api/get-previous-reading.php?propertyCode=${encodeURIComponent(propertyCode)}&unitNo=${encodeURIComponent(unitNo)}`);
        const data = await response.json();
        
        if (data.success && data.data) {
            const readingData = data.data;
            
            // Cache the previous reading data for smart validation
            previousReadingCache = {
                tenant_code: tenant.tenant_code,
                property_code: propertyCode,
                unit_no: unitNo,
                dateFrom: readingData.dateFrom,
                dateTo: readingData.dateTo,
                currentReading: readingData.currentReading,
                prevReading: readingData.prevReading,
                usage: readingData.usage,
                readingDate: readingData.readingDate
            };
            
            // Store in localStorage for persistence
            localStorage.setItem('previousReadingCache', JSON.stringify(previousReadingCache));
            
            // Update tenant card with reading information
            updateTenantCardWithReadingInfo(tenant, readingData, false);
            
            // Populate previous reading field
            if (previousReadingInput) {
                // Set flag to skip validation during auto-population
                isAutoPopulating = true;
                
                previousReadingInput.value = readingData.currentReading || 0;
                // Allow editing for meter replacement scenarios - do NOT set readOnly
                previousReadingInput.disabled = false;
                previousReadingInput.classList.add('bg-light'); // Visual indicator it's auto-populated
                
                // Add tooltip to show last reading info and indicate it's editable for meter replacement
                previousReadingInput.title = `Last reading on ${readingData.readingDate ? new Date(readingData.readingDate).toLocaleDateString() : 'N/A'} (Editable for meter replacement)`;
                
                // Show helper text
                const helper = document.getElementById('previousReadingHelper');
                if (helper) {
                    helper.style.display = 'block';
                }
                
                // Trigger calculation to update consumption display (validation skipped due to flag)
                calculateConsumption();
                
                // Reset flag after auto-population complete
                isAutoPopulating = false;
            }
            
            // Validate period conflict if dates are already entered (Issue #3 fix)
            validatePeriodConflictIfDatesEntered();
            
        } else {
            // No previous reading found - set to 0 and allow manual entry
            if (previousReadingInput) {
                // Set flag to skip validation during auto-population
                isAutoPopulating = true;
                
                previousReadingInput.value = 0;
                previousReadingInput.readOnly = false;
                previousReadingInput.disabled = false;
                previousReadingInput.classList.remove('bg-light');
                previousReadingInput.title = 'No previous reading found - you can manually enter if needed';
                calculateConsumption();
                
                // Reset flag after auto-population complete
                isAutoPopulating = false;
            }
            
            // Hide helper text
            const helper = document.getElementById('previousReadingHelper');
            if (helper) {
                helper.style.display = 'none';
            }
            
            // Update tenant card showing no previous reading
            updateTenantCardWithReadingInfo(tenant, null, false);
            
            console.log('No previous reading found for this tenant');
        }
    } catch (error) {
        console.error('Error fetching previous reading:', error);
        // Set to 0 on error and allow manual entry
        const previousReadingInput = document.getElementById('previousReading');
        if (previousReadingInput) {
            previousReadingInput.value = 0;
            previousReadingInput.readOnly = false;
            previousReadingInput.disabled = false;
            previousReadingInput.classList.remove('bg-light');
            previousReadingInput.title = 'Error loading previous reading - you can manually enter';
            calculateConsumption();
        }
        
        // Hide helper text on error
        const helper = document.getElementById('previousReadingHelper');
        if (helper) {
            helper.style.display = 'none';
        }
    }
}

/**
 * Update tenant search pagination
 */
function updateTenantSearchPagination() {
    const paginationContainer = document.getElementById('tenantSearchPagination');
    const paginationList = paginationContainer.querySelector('.pagination');
    
    if (tenantSearchTotalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }
    
    paginationContainer.style.display = 'block';
    paginationList.innerHTML = '';
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${tenantSearchCurrentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" data-page="${tenantSearchCurrentPage - 1}">Previous</a>`;
    paginationList.appendChild(prevLi);
    
    // Page numbers
    const startPage = Math.max(1, tenantSearchCurrentPage - 2);
    const endPage = Math.min(tenantSearchTotalPages, tenantSearchCurrentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement('li');
        pageLi.className = `page-item ${i === tenantSearchCurrentPage ? 'active' : ''}`;
        pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        paginationList.appendChild(pageLi);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${tenantSearchCurrentPage === tenantSearchTotalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" data-page="${tenantSearchCurrentPage + 1}">Next</a>`;
    paginationList.appendChild(nextLi);
    
    // Add click handlers
    paginationList.addEventListener('click', function(e) {
        e.preventDefault();
        if (e.target.classList.contains('page-link') && !e.target.parentElement.classList.contains('disabled')) {
            const page = parseInt(e.target.dataset.page);
            if (page >= 1 && page <= tenantSearchTotalPages) {
                tenantSearchCurrentPage = page;
                searchTenantsInModal();
            }
        }
    });
}

/**
 * Update search input placeholder based on selected criteria
 */
function updateSearchPlaceholder() {
    const searchCriteria = document.getElementById('tenantSearchCriteria').value;
    const searchInput = document.getElementById('tenantSearchInput');
    
    let placeholder = 'Enter search term...';
    switch (searchCriteria) {
        case 'tenant_name':
            placeholder = 'Enter tenant name (e.g., John Doe)...';
            break;
        case 'tenant_code':
            placeholder = 'Enter tenant code (e.g., T000001)...';
            break;
    }
    
    searchInput.placeholder = placeholder;
    searchInput.value = ''; // Clear previous search
}

/**
 * Load shared units cache for both main filters and tenant lookup modal
 */
async function loadSharedUnitsCache() {
    try {
        const response = await fetch(`${ENDPOINTS.tenantSearch}?limit=1`);
        const data = await response.json();
        
        if (data.success && data.data.units) {
            // Cache all units for dynamic filtering
            allUnitsCache = data.data.units;
            
            // Organize units by property code for efficient filtering
            unitsByPropertyCache = {};
            data.data.units.forEach(unit => {
                const propertyCode = unit.real_property_code;
                if (!unitsByPropertyCache[propertyCode]) {
                    unitsByPropertyCache[propertyCode] = [];
                }
                unitsByPropertyCache[propertyCode].push(unit);
            });
        }
    } catch (error) {
        console.error('Error loading shared units cache:', error);
    }
}

/**
 * Update unit filter based on selected property
 */
function updateUnitFilterBasedOnProperty() {
    const propertyFilter = document.getElementById('tenantPropertyFilter').value;
    
    // Clear current unit selection when property changes (already guarded by caller)
    $('#tenantUnitFilter').val('').trigger('change');
    
    if (propertyFilter === '') {
        // All properties selected - show all units
        populateUnitFilter(allUnitsCache);
    } else {
        // Specific property selected - get units from organized cache
        const filteredUnits = unitsByPropertyCache[propertyFilter] || [];
        populateUnitFilter(filteredUnits);
    }
}

/**
 * Update property filter based on selected unit
 */
function updatePropertyFilterBasedOnUnit() {
    const unitFilter = document.getElementById('tenantUnitFilter');
    const selectedUnitValue = unitFilter.value;
    
    if (selectedUnitValue) {
        // Find the selected option to get its data-property-code attribute
        const selectedOption = unitFilter.options[unitFilter.selectedIndex];
        const propertyCode = selectedOption.dataset.propertyCode;
        
        // Auto-select the corresponding property using Select2's val() and trigger()
        const currentPropertyValue = $('#tenantPropertyFilter').val();
        if (propertyCode && currentPropertyValue !== propertyCode) {
            isUpdatingTenantFilters = true;
            $('#tenantPropertyFilter').val(propertyCode).trigger('change');
            isUpdatingTenantFilters = false;
        }
    }
}

/**
 * Populate unit filter dropdown with given units
 */
function populateUnitFilter(units) {
    const select = document.getElementById('tenantUnitFilter');
    select.innerHTML = '<option value="">All Units</option>';
    
    // Sort units by property code first, then by unit number
    const sortedUnits = units.sort((a, b) => {
        const propertyCompare = a.real_property_code.localeCompare(b.real_property_code);
        if (propertyCompare !== 0) return propertyCompare;
        
        const unitA = parseInt(a.unit_no) || 0;
        const unitB = parseInt(b.unit_no) || 0;
        return unitA - unitB;
    });
    
    sortedUnits.forEach(unit => {
        const option = document.createElement('option');
        option.value = `${unit.real_property_code} ${unit.unit_no}`;
        option.textContent = `${unit.real_property_code} ${unit.unit_no}`;
        option.dataset.propertyCode = unit.real_property_code;
        option.dataset.unitNo = unit.unit_no;
        select.appendChild(option);
    });
    
    // Reinitialize Select2 after populating
    reinitializeSelect2('#tenantUnitFilter');
}

/**
 * Load properties for tenant filter
 */
async function loadPropertiesForTenantFilter() {
    try {
        const response = await fetch(`${ENDPOINTS.tenantSearch}?limit=1`);
        const data = await response.json();
        
        if (data.success && data.data.properties) {
            const select = document.getElementById('tenantPropertyFilter');
            select.innerHTML = '<option value="">All Properties</option>';
            
            data.data.properties.forEach(property => {
                const option = document.createElement('option');
                option.value = property.real_property_code;
                option.textContent = `${property.real_property_name} (${property.real_property_code})`;
                select.appendChild(option);
            });
            
            // Reinitialize Select2 after loading options
            reinitializeSelect2('#tenantPropertyFilter');
        }
    } catch (error) {
        console.error('Error loading properties for tenant filter:', error);
    }
}

// ===== ANIMATED NOTIFICATION SYSTEM (UX DESIGN STANDARDS) =====

// Global notification management
let currentNotifications = [];
let notificationZIndex = 10000;

/**
 * Clear all existing notifications
 */
function clearAllNotifications() {
    currentNotifications.forEach(notification => {
        if (notification.parentNode) {
            notification.remove();
        }
    });
    currentNotifications = [];
}

/**
 * Show success notification with green gradient
 */
function showSuccess(title, subtitle = '') {
    // Clear any existing notifications to prevent overlap
    clearAllNotifications();
    
    const notification = createNotification(
        title,
        subtitle,
        'linear-gradient(135deg, #4caf50, #45a049)',
        'bi-check-circle-fill',
        4000
    );
    
    // Generate unique ID for dismissal
    const notificationId = `success-${Date.now()}`;
    notification.id = notificationId;
    
    // Set z-index and position
    notification.style.zIndex = notificationZIndex++;
    notification.style.top = '20px';
    
    document.body.appendChild(notification);
    currentNotifications.push(notification);
    
    // Auto-dismiss after 4 seconds with animation
    setTimeout(() => {
        hideNotification(notificationId);
    }, 4000);
}

/**
 * Show warning notification with orange gradient
 * Returns notification element ID for manual dismissal (persistent warnings)
 */
function showWarning(title, subtitle = '', persistent = false) {
    const notification = createNotification(
        title,
        subtitle,
        'linear-gradient(135deg, #ff9800, #f57c00)',
        'bi-exclamation-triangle-fill',
        5000
    );
    
    // Generate unique ID for persistent notifications
    const notificationId = `warning-${Date.now()}`;
    notification.id = notificationId;
    
    // Count existing persistent warning notifications
    const existingWarnings = document.querySelectorAll('[id^="warning-"]');
    const warningCount = existingWarnings.length;
    
    console.log(`[showWarning] Creating warning: "${title}"`);
    console.log(`[showWarning] Existing warnings count: ${warningCount}`);
    console.log(`[showWarning] Existing warning IDs:`, Array.from(existingWarnings).map(w => w.id));
    console.log(`[showWarning] Existing warnings visible:`, Array.from(existingWarnings).map(w => ({
        id: w.id,
        inDOM: document.body.contains(w),
        display: window.getComputedStyle(w).display,
        visibility: window.getComputedStyle(w).visibility
    })));
    
    // Apply stacking classes based on position
    if (warningCount === 0) {
        notification.classList.add('notification-stack-position-1');
        console.log(`[showWarning] Applied position-1 class`);
    } else if (warningCount === 1) {
        notification.classList.add('notification-stack-position-2');
        console.log(`[showWarning] Applied position-2 class`);
    }
    
    // Note: z-index and top position are handled by CSS classes with !important
    
    document.body.appendChild(notification);
    currentNotifications.push(notification);
    
    // Force reflow to ensure styles are applied
    notification.offsetHeight;
    
    console.log(`[showWarning] Added to DOM with ID: ${notificationId}`);
    console.log(`[showWarning] Notification classes:`, notification.className);
    console.log(`[showWarning] Computed styles:`, {
        top: window.getComputedStyle(notification).top,
        zIndex: window.getComputedStyle(notification).zIndex,
        display: window.getComputedStyle(notification).display,
        visibility: window.getComputedStyle(notification).visibility,
        position: window.getComputedStyle(notification).position
    });
    console.log(`[showWarning] Actual position:`, {
        offsetTop: notification.offsetTop,
        offsetLeft: notification.offsetLeft,
        boundingRect: notification.getBoundingClientRect()
    });
    
    // Add count badge to first notification when second one appears
    if (warningCount === 1) {
        console.log(`[showWarning] Adding count badge (2 warnings total)`);
        addWarningCountBadge();
        
        // Verify both notifications are still in DOM after badge is added
        setTimeout(() => {
            const allWarnings = document.querySelectorAll('[id^="warning-"]');
            console.log(`[VERIFICATION] After badge added, warnings in DOM:`, allWarnings.length);
            allWarnings.forEach((w, i) => {
                console.log(`[VERIFICATION] Warning ${i+1}:`, {
                    id: w.id,
                    classes: w.className,
                    inDOM: document.body.contains(w),
                    visible: window.getComputedStyle(w).visibility,
                    display: window.getComputedStyle(w).display,
                    top: window.getComputedStyle(w).top,
                    boundingRect: w.getBoundingClientRect()
                });
            });
        }, 100);
    }
    
    // Only auto-dismiss if not persistent
    if (!persistent) {
        setTimeout(() => {
            hideNotification(notificationId);
        }, 5000);
    }
    
    return notificationId;
}

/**
 * Show progress notification with blue gradient (manual dismiss)
 */
function showProgress(message) {
    // Clear any existing notifications to prevent overlap
    clearAllNotifications();
    
    const notification = createNotification(
        message,
        '',
        'linear-gradient(135deg, #2196f3, #1976d2)',
        'spinner',
        0 // No auto-dismiss
    );
    
    // Set z-index and position
    notification.style.zIndex = notificationZIndex++;
    notification.style.top = '20px';
    
    document.body.appendChild(notification);
    currentNotifications.push(notification);
    
    return notification; // Return for manual dismissal
}

/**
 * Hide notification by element or ID
 */
function hideNotification(notificationElementOrId) {
    let notificationElement = notificationElementOrId;
    let notificationId = null;
    
    // If string ID is passed, get the element and store the ID
    if (typeof notificationElementOrId === 'string') {
        notificationId = notificationElementOrId;
        notificationElement = document.getElementById(notificationElementOrId);
    } else if (notificationElement) {
        notificationId = notificationElement.id;
    }
    
    console.log(`[hideNotification] Called with ID: ${notificationId}`);
    console.log(`[hideNotification] Element found:`, !!notificationElement);
    
    if (notificationElement && notificationElement.parentNode) {
        // Add dismissing animation class
        notificationElement.classList.add('notification-dismissing');
        
        // Wait for animation to complete before removing from DOM
        setTimeout(() => {
            if (notificationElement && notificationElement.parentNode) {
                notificationElement.remove();
                // Remove from tracking array
                currentNotifications = currentNotifications.filter(n => n !== notificationElement);
                
                console.log(`[hideNotification] Removed notification: ${notificationId}`);
                
                // Clear global notification ID variables if this matches
                if (notificationId === readingPeriodConflictNoticeId) {
                    console.log(`[hideNotification] Cleared readingPeriodConflictNoticeId`);
                    readingPeriodConflictNoticeId = null;
                }
                if (notificationId === negativeUsageNoticeId) {
                    console.log(`[hideNotification] Cleared negativeUsageNoticeId`);
                    negativeUsageNoticeId = null;
                }
                
                // Reposition remaining notifications
                updateNotificationStack();
            }
        }, 300); // Match animation duration in CSS
    } else {
        console.log(`[hideNotification] Element not found or not in DOM: ${notificationId}`);
    }
}

/**
 * Update notification stack positions after one is removed
 */
function updateNotificationStack() {
    const remainingWarnings = document.querySelectorAll('[id^="warning-"]');
    
    remainingWarnings.forEach((notification, index) => {
        // Remove old position classes
        notification.classList.remove('notification-stack-position-1', 'notification-stack-position-2');
        
        // Apply new position based on current index (CSS handles z-index and top position)
        if (index === 0) {
            notification.classList.add('notification-stack-position-1');
        } else if (index === 1) {
            notification.classList.add('notification-stack-position-2');
        }
    });
    
    // Update or remove count badge
    if (remainingWarnings.length >= 2) {
        addWarningCountBadge();
    } else {
        removeWarningCountBadge();
    }
}

/**
 * Add warning count badge to first notification
 */
function addWarningCountBadge() {
    const firstWarning = document.querySelector('[id^="warning-"].notification-stack-position-1');
    if (!firstWarning) return;
    
    // Remove existing badge if any
    const existingBadge = firstWarning.querySelector('.notification-warning-count');
    if (existingBadge) {
        existingBadge.remove();
    }
    
    // Count total warnings
    const warningCount = document.querySelectorAll('[id^="warning-"]').length;
    
    // Create and add new badge
    const badge = document.createElement('span');
    badge.className = 'notification-warning-count';
    badge.textContent = `${warningCount} Issues`;
    badge.title = 'Multiple validation errors active';
    
    // Note: Badge is positioned absolute, notification must remain position: fixed
    // Do NOT change the notification's position style
    firstWarning.appendChild(badge);
}

/**
 * Remove warning count badge
 */
function removeWarningCountBadge() {
    const badge = document.querySelector('.notification-warning-count');
    if (badge) {
        badge.remove();
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Calculate lease duration between start and end dates
 */
function calculateLeaseDuration(startDate, endDate = null) {
    if (!startDate) return 'N/A';
    
    const start = new Date(startDate);
    const end = endDate ? new Date(endDate) : new Date(); // Use current date if no end date
    
    if (start > end) return 'Invalid dates';
    
    // Calculate the difference in milliseconds
    const diffMs = end - start;
    
    // Convert to days
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    // Calculate years, months, and remaining days
    const years = Math.floor(diffDays / 365);
    const remainingDaysAfterYears = diffDays % 365;
    const months = Math.floor(remainingDaysAfterYears / 30);
    const days = remainingDaysAfterYears % 30;
    
    // Build duration string
    const parts = [];
    if (years > 0) parts.push(`${years} year${years > 1 ? 's' : ''}`);
    if (months > 0) parts.push(`${months} month${months > 1 ? 's' : ''}`);
    if (days > 0) parts.push(`${days} day${days > 1 ? 's' : ''}`);
    
    return parts.length > 0 ? parts.join(', ') : 'Less than 1 day';
}

/**
 * Create notification element
 */
function createNotification(title, subtitle, gradient, icon, duration) {
    const notification = document.createElement('div');
    notification.className = 'notification-base';
    notification.style.background = gradient;
    notification.style.boxShadow = `0 4px 20px ${gradient.includes('#4caf50') ? 'rgba(76, 175, 80, 0.3)' : 
                                                   gradient.includes('#ff9800') ? 'rgba(255, 152, 0, 0.3)' : 
                                                   'rgba(33, 150, 243, 0.3)'}`;
    
    let iconHtml = '';
    if (icon === 'spinner') {
        iconHtml = '<div class="spinner-border spinner-border-sm me-2" role="status"><span class="visually-hidden">Loading...</span></div>';
    } else {
        iconHtml = `<i class="bi ${icon} me-2"></i>`;
    }
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            ${iconHtml}
            <div class="flex-grow-1">
                <div class="fw-bold">${escapeHtml(title)}</div>
                ${subtitle ? `<div class="small opacity-75">${escapeHtml(subtitle)}</div>` : ''}
            </div>
        </div>
    `;
    
    return notification;
}

/**
 * Show error notification using SweetAlert2 (for critical errors)
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc2626'
    });
}