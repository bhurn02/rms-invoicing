// QR Generator JavaScript
// Global variables
let qrCodeInstance = null;
let activeTenants = [];
let selectedTenants = [];
let batchQRCodes = [];
let html5QrcodeScanner = null;

// Force focus on search when dropdown is opened
$(document).on('select2:open', function () {
    let searchField = document.querySelector('.select2-container--open .select2-search__field');
    if (searchField) {
        searchField.focus();
    }
});

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
    // Initialize event listeners
    initializeEventListeners();
    
    // Load active tenants for batch generation (this will populate the select first)
    loadActiveTenants();
});

// Initialize event listeners
function initializeEventListeners() {
    // Form submission for individual QR generation
    document.getElementById('qr-form').addEventListener('submit', function(e) {
        e.preventDefault();
        generateQR();
    });

    // Tenant search functionality (exactly like index.php)
    document.getElementById('tenant-search').addEventListener('keyup', function() {
        filterTenants();
    });

    // Property filter functionality
    $('#property-filter').on('change', function() {
        filterTenants();
    });

    // Select all tenants checkbox
    document.getElementById('select-all-tenants').addEventListener('change', function() {
        toggleAllTenants(this.checked);
    });

    // Batch QR generation button
    document.getElementById('generate-batch-qr').addEventListener('click', function() {
        generateBatchQR();
    });

    // Tab switching events
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            if (targetTab === '#batch-generator') {
                loadActiveTenants();
            } else if (targetTab === '#scanner-test') {
                // Don't automatically stop scanner when switching to scanner tab
                // Only stop if scanner is already running
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    stopScanner();
                }
            } else {
                // Stop scanner when switching to other tabs
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    stopScanner();
                }
            }
        });
    });
}

// Individual QR Code Generation
function generateQR() {
    const propertyId = document.getElementById('propertyId').value.trim();
    const unitNumber = document.getElementById('unitNumber').value.trim();
    const meterId = document.getElementById('meterId').value.trim();
    const propertyName = document.getElementById('propertyName').value.trim();

    if (!propertyId || !unitNumber) {
        showAlert('Please enter Property ID and Unit Number', 'danger');
        return;
    }

    try {
        // Create QR data in the format expected by the scanning system
        const qrData = {
            propertyId: propertyId,
            unitNumber: unitNumber,
            meterId: meterId || null
        };

        const qrText = JSON.stringify(qrData);

        // Clear previous QR code
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = '';

        // Generate new QR code
        qrCodeInstance = new QRCode(qrcodeDiv, {
            text: qrText,
            width: 320,
            height: 320,
            colorDark: '#1e40af',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });

        // Ensure QR code canvas is properly sized and positioned
        setTimeout(() => {
            const canvas = qrcodeDiv.querySelector('canvas');
            if (canvas) {
                canvas.style.maxWidth = '100%';
                canvas.style.maxHeight = '320px';
                canvas.style.width = 'auto';
                canvas.style.height = 'auto';
            }
        }, 100);

        // Update display information
        updateQRDisplay(propertyId, unitNumber, meterId, propertyName, qrText);

        showAlert('QR Code generated successfully!', 'success');

    } catch (error) {
        console.error('Error generating QR code:', error);
        showAlert('Error generating QR code. Please try again.', 'danger');
    }
}

// Update QR code display with property information
function updateQRDisplay(propertyId, unitNumber, meterId, propertyName, qrText) {
    // Only show meter ID if it exists and is not empty
    const qrDataText = meterId && meterId.trim() !== '' 
        ? `${propertyId}|${unitNumber}|${meterId}` 
        : `${propertyId}|${unitNumber}`;
    document.getElementById('display-qr-data').textContent = qrDataText;
    
    // Store QR data for download/copy functions
    window.currentQRData = {
        propertyId: propertyId,
        unitNumber: unitNumber,
        meterId: meterId,
        propertyName: propertyName,
        qrText: qrText
    };
    
    document.getElementById('qr-result').style.display = 'block';
}

// Clear form
function clearForm() {
    document.getElementById('qr-form').reset();
    document.getElementById('qr-result').style.display = 'none';
    const qrcodeDiv = document.getElementById('qrcode');
    qrcodeDiv.innerHTML = '';
    qrCodeInstance = null;
}

// Load active tenants from database
async function loadActiveTenants() {
    try {
        document.getElementById('loading-tenants').style.display = 'block';
        document.getElementById('tenant-table-container').style.display = 'none';
        document.getElementById('tenant-error').style.display = 'none';

        const response = await fetch('api/get-active-tenants.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            activeTenants = data.tenants || [];
            populatePropertyFilter(data.properties || []);
            renderTenantTable();
            
            document.getElementById('loading-tenants').style.display = 'none';
            document.getElementById('tenant-table-container').style.display = 'block';
        } else {
            throw new Error(data.message || 'Failed to load tenants');
        }
        
    } catch (error) {
        console.error('Error loading tenants:', error);
        document.getElementById('loading-tenants').style.display = 'none';
        document.getElementById('tenant-error').style.display = 'block';
        
        // Show demo data for development
        loadDemoTenants();
    }
}

// Load demo tenants for development/testing
function loadDemoTenants() {
    activeTenants = [
        {
            tenant_code: 'T001',
            tenant_name: 'John Doe',
            real_property_code: 'PROP1',
            real_property_name: 'Main Building Complex',
            unit_no: 'UNIT001'
        },
        {
            tenant_code: 'T002',
            tenant_name: 'Jane Smith',
            real_property_code: 'PROP1',
            real_property_name: 'Main Building Complex',
            unit_no: 'UNIT002'
        },
        {
            tenant_code: 'T003',
            tenant_name: 'Bob Johnson',
            real_property_code: 'PROP2',
            real_property_name: 'Secondary Complex',
            unit_no: 'UNIT101'
        }
    ];

    const properties = [
        { real_property_code: 'PROP1', real_property_name: 'Main Building Complex' },
        { real_property_code: 'PROP2', real_property_name: 'Secondary Complex' }
    ];

    populatePropertyFilter(properties);
    renderTenantTable();
    
    document.getElementById('loading-tenants').style.display = 'none';
    document.getElementById('tenant-table-container').style.display = 'block';
}

// Populate property filter dropdown
function populatePropertyFilter(properties) {
    const $filterSelect = $('#property-filter');
    
    // Clear existing options
    $filterSelect.empty();
    
    // Add "All Properties" option
    $filterSelect.append(new Option('All Properties', '', true, true));
    
    // Add property options
    properties.forEach(property => {
        const optionText = `${property.real_property_code} - ${property.real_property_name}`;
        $filterSelect.append(new Option(optionText, property.real_property_code));
    });
    
    // Now initialize Select2 after populating the select element
    $filterSelect.select2({
        theme: 'bootstrap-5',
        placeholder: 'All Properties',
        allowClear: true,
        width: '100%',
        closeOnSelect: true,
        selectOnClose: false,
        dropdownParent: $('body'),
        minimumResultsForSearch: 0
    });
    

    
    console.log('Select2 initialized with', properties.length, 'properties');
}

// Render tenant table
function renderTenantTable() {
    const tbody = document.getElementById('tenant-list');
    tbody.innerHTML = '';

    activeTenants.forEach(tenant => {
        const row = createTenantRow(tenant);
        tbody.appendChild(row);
    });

    updateSelectedCount();
}

// Create tenant table row
function createTenantRow(tenant) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <div class="form-check">
                <input class="form-check-input tenant-checkbox" type="checkbox" 
                       value="${tenant.tenant_code}" data-tenant='${JSON.stringify(tenant)}'>
            </div>
        </td>
        <td>
            <strong>${tenant.real_property_code}</strong><br>
            <small class="text-muted">${tenant.real_property_name}</small>
        </td>
        <td><span class="badge bg-primary">${tenant.unit_no}</span></td>
        <td><span class="badge bg-info">${tenant.tenant_code || 'N/A'}</span></td>
        <td>${tenant.tenant_name}</td>
    `;

    // Add event listener to checkbox
    const checkbox = tr.querySelector('.tenant-checkbox');
    checkbox.addEventListener('change', function() {
        updateSelectedTenants();
    });

    return tr;
}

// Filter tenants based on search and property filter (exactly like index.php)
function filterTenants() {
    const searchTerm = document.getElementById('tenant-search').value.toLowerCase();
    const propertyFilter = $('#property-filter').val();

    // First, show all rows
    $('#tenant-list tr').show();
    
    // Apply search filter exactly like index.php
    if (searchTerm) {
        $('#tenant-list tr').filter(function() {
            return !$(this).text().toLowerCase().includes(searchTerm);
        }).hide();
    }
    
    // Apply property filter
    if (propertyFilter) {
        $('#tenant-list tr').filter(function() {
            const checkbox = $(this).find('.tenant-checkbox');
            const tenant = checkbox.data('tenant'); // jQuery data() already returns object
            return tenant.real_property_code !== propertyFilter;
        }).hide();
    }
    
    // Uncheck hidden rows
    $('#tenant-list tr:hidden .tenant-checkbox').prop('checked', false);
    
    updateSelectedTenants();
}

// Toggle all visible tenants
function toggleAllTenants(checked) {
    const visibleCheckboxes = document.querySelectorAll('#tenant-list tr:not([style*="display: none"]) .tenant-checkbox');
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });

    updateSelectedTenants();
}

// Update selected tenants array
function updateSelectedTenants() {
    const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
    selectedTenants = Array.from(checkedBoxes).map(checkbox => 
        $(checkbox).data('tenant') // jQuery data() already returns object
    );

    updateSelectedCount();
    
    // Update select all checkbox state
    const selectAllCheckbox = document.getElementById('select-all-tenants');
    const visibleCheckboxes = document.querySelectorAll('#tenant-list tr:not([style*="display: none"]) .tenant-checkbox');
    const visibleCheckedBoxes = document.querySelectorAll('#tenant-list tr:not([style*="display: none"]) .tenant-checkbox:checked');
    
    if (visibleCheckboxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (visibleCheckedBoxes.length === visibleCheckboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else if (visibleCheckedBoxes.length > 0) {
        selectAllCheckbox.indeterminate = true;
        selectAllCheckbox.checked = false;
    } else {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    }
}

// Update selected count display
function updateSelectedCount() {
    const count = selectedTenants.length;
    document.getElementById('selected-count').textContent = count;
    document.getElementById('selected-count-btn').textContent = count;
    
    const generateBtn = document.getElementById('generate-batch-qr');
    generateBtn.disabled = count === 0;
}

// Generate batch QR codes
async function generateBatchQR() {
    if (selectedTenants.length === 0) {
        showAlert('Please select at least one tenant', 'warning');
        return;
    }

    try {
        // Show progress
        document.getElementById('batch-progress').style.display = 'block';
        document.getElementById('batch-results').style.display = 'none';
        
        const progressBar = document.querySelector('#batch-progress .progress-bar');
        const currentTenantSpan = document.getElementById('current-tenant');
        const progressCurrentSpan = document.getElementById('progress-current');
        const progressTotalSpan = document.getElementById('progress-total');
        
        progressTotalSpan.textContent = selectedTenants.length;
        batchQRCodes = [];

        for (let i = 0; i < selectedTenants.length; i++) {
            const tenant = selectedTenants[i];
            currentTenantSpan.textContent = `${tenant.tenant_name} (${tenant.unit_no})`;
            progressCurrentSpan.textContent = i + 1;
            
            const progress = ((i + 1) / selectedTenants.length) * 100;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.textContent = Math.round(progress) + '%';

            // Generate QR code for this tenant
            const qrData = {
                propertyId: tenant.real_property_code,
                unitNumber: tenant.unit_no,
                meterId: null
            };

            const qrText = JSON.stringify(qrData);
            
            // Create QR code canvas
            const canvas = document.createElement('canvas');
            const qrCode = new QRCode(canvas, {
                text: qrText,
                width: 256,
                height: 256,
                colorDark: '#1e40af',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });

            batchQRCodes.push({
                tenant: tenant,
                qrData: qrData,
                qrText: qrText,
                canvas: canvas
            });

            // Small delay to show progress
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        // Hide progress and show results
        document.getElementById('batch-progress').style.display = 'none';
        displayBatchResults();

    } catch (error) {
        console.error('Error generating batch QR codes:', error);
        showAlert('Error generating batch QR codes. Please try again.', 'danger');
        document.getElementById('batch-progress').style.display = 'none';
    }
}

// Display batch QR results
function displayBatchResults() {
    document.getElementById('results-count').textContent = batchQRCodes.length;
    
    const displayContainer = document.getElementById('batch-qr-display');
    displayContainer.innerHTML = '';

    batchQRCodes.forEach((qrCode, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-lg-3 mb-4';
        
        col.innerHTML = `
            <div class="professional-qr batch-qr">
                <div class="batch-qr-header">
                    <h5>${qrCode.tenant.real_property_name}</h5>
                    <p>Unit: ${qrCode.tenant.unit_no}</p>
                </div>
                <div class="qr-code-container" id="batch-qr-${index}">
                </div>
                <div class="qr-footer">
                    <p class="scan-instruction">Scan for Meter Reading</p>
                    <small class="qr-data">${qrCode.tenant.real_property_code}|${qrCode.tenant.unit_no}${qrCode.tenant.meter_id ? '|' + qrCode.tenant.meter_id : ''}</small>
                </div>
            </div>
        `;
        
        displayContainer.appendChild(col);
        
        // Add QR code to the container
        const qrContainer = document.getElementById(`batch-qr-${index}`);
        const canvas = qrCode.canvas.querySelector('canvas');
        if (canvas) {
            qrContainer.appendChild(canvas);
        }
    });

    document.getElementById('batch-results').style.display = 'block';
}

// QR Scanner Test Functions
function startScanner() {
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrcodeScanner = new Html5Qrcode("qr-reader");
    
    document.getElementById('qr-reader').style.display = 'block';
    document.getElementById('scanner-controls').style.display = 'none';
    document.getElementById('stop-btn').style.display = 'inline-block';
    document.getElementById('scanner-result').style.display = 'none';
    document.getElementById('scanner-error').style.display = 'none';

    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).then(() => {
        // Scanner started successfully
        console.log('Scanner started successfully');
    }).catch(err => {
        console.error('Unable to start scanning:', err);
        showScannerError('Unable to start camera. Please check permissions and try again.');
        // Reset UI on error
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('scanner-controls').style.display = 'block';
        document.getElementById('stop-btn').style.display = 'none';
        html5QrcodeScanner = null;
    });
}

function stopScanner() {
    if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
        html5QrcodeScanner.stop().then(() => {
            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('scanner-controls').style.display = 'block';
            document.getElementById('stop-btn').style.display = 'none';
            html5QrcodeScanner = null;
        }).catch(err => {
            console.error('Error stopping scanner:', err);
            // Reset UI even if stop fails
            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('scanner-controls').style.display = 'block';
            document.getElementById('stop-btn').style.display = 'none';
            html5QrcodeScanner = null;
        });
    } else {
        // Reset UI if scanner is not running
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('scanner-controls').style.display = 'block';
        document.getElementById('stop-btn').style.display = 'none';
        html5QrcodeScanner = null;
    }
}

function onScanSuccess(decodedText) {
    try {
        // Try to parse as JSON first
        const qrData = JSON.parse(decodedText);
        
        document.getElementById('raw-data').textContent = decodedText;
        document.getElementById('parsed-property').textContent = qrData.propertyId || 'Not found';
        document.getElementById('parsed-unit').textContent = qrData.unitNumber || 'Not found';
        document.getElementById('parsed-meter').textContent = qrData.meterId || 'Not specified';
        
    } catch (error) {
        // If JSON parsing fails, try pipe-delimited format
        const parts = decodedText.split('|');
        
        document.getElementById('raw-data').textContent = decodedText;
        document.getElementById('parsed-property').textContent = parts[0] || 'Not found';
        document.getElementById('parsed-unit').textContent = parts[1] || 'Not found';
        document.getElementById('parsed-meter').textContent = parts[2] || 'Not specified';
    }

    document.getElementById('scanner-result').style.display = 'block';
    document.getElementById('scanner-error').style.display = 'none';
}

function onScanFailure(error) {
    // This is normal - scanner will keep trying
}

function showScannerError(message) {
    document.getElementById('error-message').textContent = message;
    document.getElementById('scanner-error').style.display = 'block';
    document.getElementById('scanner-result').style.display = 'none';
}

// Utility Functions
function downloadQR() {
    if (!qrCodeInstance) {
        showAlert('Please generate a QR code first', 'warning');
        return;
    }

    try {
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.download = `QR-${window.currentQRData.propertyId}-${window.currentQRData.unitNumber}.png`;
            link.href = canvas.toDataURL();
            link.click();
        }
    } catch (error) {
        console.error('Error downloading QR code:', error);
        showAlert('Error downloading QR code', 'danger');
    }
}

function printQR() {
    if (!qrCodeInstance) {
        showAlert('Please generate a QR code first', 'warning');
        return;
    }

    // Get the canvas and convert to image data URL
    const canvas = document.querySelector('#qrcode canvas');
    if (!canvas) {
        showAlert('QR code not properly generated. Please try generating again.', 'warning');
        return;
    }

    const qrImageDataURL = canvas.toDataURL('image/png');
    const qrData = document.getElementById('display-qr-data').textContent;

    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print QR Code</title>
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                .professional-qr { 
                    width: 60mm; height: 80mm; border: 2px solid #1e40af; 
                    border-radius: 8px; padding: 3mm; background: white; 
                    text-align: center; page-break-inside: avoid; 
                    display: flex; flex-direction: column; justify-content: space-between;
                }
                .qr-code-container { 
                    flex: 1; display: flex; justify-content: center; 
                    align-items: center; margin: 1mm 0;
                }
                .qr-code-container img { 
                    width: 50mm; height: 50mm; 
                    max-width: 50mm; max-height: 50mm;
                }
                .qr-footer { 
                    font-size: 8pt; color: #666; margin: 1mm 0 0 0; 
                }
                .scan-instruction { 
                    font-size: 9pt; color: #333; margin-bottom: 1mm; 
                    font-weight: bold;
                }
                .qr-data { 
                    font-size: 8pt; color: #666; font-family: monospace; 
                }
            </style>
        </head>
        <body>
            <div class="professional-qr">
                <div class="qr-code-container">
                    <img src="${qrImageDataURL}" alt="QR Code">
                </div>
                <div class="qr-footer">
                    <p class="scan-instruction">Scan for Meter Reading</p>
                    <small class="qr-data">${qrData}</small>
                </div>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for image to load before printing
    setTimeout(() => {
        printWindow.print();
    }, 100);
}

function copyQRData() {
    if (!window.currentQRData) {
        showAlert('Please generate a QR code first', 'warning');
        return;
    }

    const dataText = window.currentQRData.qrText;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(dataText).then(() => {
            showAlert('QR data copied to clipboard!', 'success');
        }).catch(err => {
            console.error('Error copying to clipboard:', err);
            fallbackCopyTextToClipboard(dataText);
        });
    } else {
        fallbackCopyTextToClipboard(dataText);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showAlert('QR data copied to clipboard!', 'success');
        } else {
            showAlert('Unable to copy QR data', 'danger');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showAlert('Unable to copy QR data', 'danger');
    }

    document.body.removeChild(textArea);
}

// Batch download functions
function downloadBatchPDF() {
    showAlert('PDF download functionality will be implemented in the next phase', 'info');
}

function downloadBatchZIP() {
    showAlert('ZIP download functionality will be implemented in the next phase', 'info');
}

function printBatchQR() {
    if (batchQRCodes.length === 0) {
        showAlert('Please generate batch QR codes first', 'warning');
        return;
    }

    const printWindow = window.open('', '_blank');
    const qrDisplay = document.getElementById('batch-qr-display').cloneNode(true);
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Batch QR Codes</title>
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                .row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10mm; }
                .professional-qr { 
                    width: 60mm; height: 80mm; border: 2px solid #1e40af; 
                    border-radius: 8px; padding: 5mm; background: white; 
                    text-align: center; page-break-inside: avoid; margin-bottom: 10mm;
                }
                .qr-header h4 { font-size: 10pt; color: #1e40af; margin-bottom: 2mm; }
                .qr-header p { font-size: 8pt; color: #666; margin-bottom: 3mm; }
                .qr-code-container canvas { width: 35mm !important; height: 35mm !important; }
                .qr-footer { font-size: 8pt; color: #666; margin-top: 2mm; }
            </style>
        </head>
        <body>
            ${qrDisplay.outerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Show alert messages
function showAlert(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    const icon = {
        'success': 'bi-check-circle',
        'danger': 'bi-exclamation-triangle',
        'warning': 'bi-exclamation-triangle',
        'info': 'bi-info-circle'
    }[type] || 'bi-info-circle';
    
    alertDiv.innerHTML = `
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize QR code fallback if library fails
if (typeof QRCode === 'undefined') {
    console.warn('QRCode library not loaded, using fallback');
    window.QRCode = function(element, options) {
        element.innerHTML = `
            <div style="width: 256px; height: 256px; border: 2px dashed #ccc; 
                 display: flex; align-items: center; justify-content: center; 
                 flex-direction: column; margin: 0 auto;">
                <i class="bi bi-qr-code" style="font-size: 4rem; color: #6b7280;"></i>
                <p style="margin-top: 1rem; color: #6b7280;">QR Library Error</p>
            </div>
        `;
    };
}

// Stop scanner when page is unloaded
window.addEventListener('beforeunload', () => {
    if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
        html5QrcodeScanner.stop().catch(err => {
            console.error('Error stopping scanner on page unload:', err);
        });
    }
});
