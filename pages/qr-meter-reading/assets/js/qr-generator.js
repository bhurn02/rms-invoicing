// QR Generator JavaScript
// Global variables
let qrCodeInstance = null;
let activeTenants = [];
let selectedTenants = [];
let batchQRCodes = [];


// Force focus on search when dropdown is opened
$(document).on('select2:open', function () {
    let searchField = document.querySelector('.select2-container--open .select2-search__field');
    if (searchField) {
        searchField.focus();
    }
});

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for QRCode library to load
    setTimeout(function() {
        // Test QRCode library
        testQRCodeLibrary();
        
        // Initialize event listeners
        initializeEventListeners();
        
        // Load active tenants for batch generation (this will populate the select first)
        loadActiveTenants();
    }, 500);
});

// Test QRCode library functionality
function testQRCodeLibrary() {
    console.log('Testing QRCode library...');
    console.log('QRCode type:', typeof QRCode);
    console.log('QRCode available:', !!QRCode);
    
    if (typeof QRCode === 'undefined') {
        console.error('QRCode library not loaded!');
        showAlert('QRCode library failed to load. Please refresh the page.', 'danger');
        
        // Try to load QRCode library again
        loadQRCodeLibrary();
        return false;
    }
    
    // Test basic QR generation
    try {
        const testContainer = document.createElement('div');
        const testQR = new QRCode(testContainer, {
            text: 'test',
            width: 64,
            height: 64
        });
        console.log('QRCode library test successful');
        return true;
    } catch (error) {
        console.error('QRCode library test failed:', error);
        showAlert('QRCode library test failed: ' + error.message, 'danger');
        return false;
    }
}

// Load QRCode library with multiple fallbacks
function loadQRCodeLibrary() {
    console.log('Attempting to load QRCode library...');
    
    const sources = [
        'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js',
        'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js',
        'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js'
    ];
    
    let currentSource = 0;
    
    function tryNextSource() {
        if (currentSource >= sources.length) {
            console.error('All QRCode library sources failed');
            showAlert('Unable to load QRCode library. Please check your internet connection.', 'danger');
            return;
        }
        
        const script = document.createElement('script');
        script.src = sources[currentSource];
        script.onload = function() {
            console.log('QRCode library loaded from:', sources[currentSource]);
            // Retry initialization
            setTimeout(function() {
                testQRCodeLibrary();
                initializeEventListeners();
                loadActiveTenants();
            }, 100);
        };
        script.onerror = function() {
            console.log('Failed to load from:', sources[currentSource]);
            currentSource++;
            tryNextSource();
        };
        document.head.appendChild(script);
    }
    
    tryNextSource();
}

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
        console.log('Batch QR generation button clicked');
        console.log('Selected tenants:', selectedTenants);
        console.log('QRCode library available:', typeof QRCode !== 'undefined');
        generateBatchQR();
    });

    // Tab switching events
    document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            if (targetTab === '#batch-generator') {
                loadActiveTenants();
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

    // Check if QRCode library is available
    if (typeof QRCode === 'undefined') {
        showAlert('QR Code library not loaded. Attempting to load...', 'warning');
        loadQRCodeLibrary();
        return;
    }

    try {
        // Create QR data in the format expected by the scanning system
        const qrData = {
            propertyId: propertyId,
            unitNumber: unitNumber,
            meterId: meterId || null,
            timestamp: Date.now(), // Add timestamp for uniqueness
            type: 'individual'
        };

        const qrText = JSON.stringify(qrData);
        
        console.log('Individual QR data:', qrData);
        console.log('Individual QR text:', qrText);

        // Clear previous QR code
        const qrcodeDiv = document.getElementById('qrcode');
        qrcodeDiv.innerHTML = '';

        // Generate new QR code with unique identifier
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
            // Remove any duplicate elements (images) and keep only canvas
            const images = qrcodeDiv.querySelectorAll('img');
            images.forEach(img => img.remove());
            
            const canvas = qrcodeDiv.querySelector('canvas');
            if (canvas) {
                canvas.style.display = 'block';
                canvas.style.maxWidth = '100%';
                canvas.style.maxHeight = '320px';
                canvas.style.width = 'auto';
                canvas.style.height = 'auto';
                console.log('Individual QR code generated successfully');
            } else {
                console.error('No canvas found in individual QR generation');
                showAlert('QR Code generation failed - no canvas created', 'danger');
                return;
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
    tr.className = 'tenant-row';
    tr.style.cursor = 'pointer';
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
    checkbox.addEventListener('change', function(e) {
        e.stopPropagation(); // Prevent row click when clicking checkbox
        updateSelectedTenants();
    });

    // Add event listener to entire row for clickability
    tr.addEventListener('click', function(e) {
        // Don't trigger if clicking on the checkbox itself
        if (e.target.type === 'checkbox') {
            return;
        }
        
        // Toggle checkbox state
        checkbox.checked = !checkbox.checked;
        updateSelectedTenants();
        
        // Add visual feedback
        if (checkbox.checked) {
            tr.classList.add('table-active');
        } else {
            tr.classList.remove('table-active');
        }
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
    const visibleRows = document.querySelectorAll('#tenant-list tr:not([style*="display: none"])');
    
    visibleRows.forEach(row => {
        const checkbox = row.querySelector('.tenant-checkbox');
        checkbox.checked = checked;
        
        // Update visual feedback immediately
        if (checked) {
            row.classList.add('table-active');
        } else {
            row.classList.remove('table-active');
        }
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
    
    // Update visual feedback for all rows
    document.querySelectorAll('.tenant-row').forEach(row => {
        const checkbox = row.querySelector('.tenant-checkbox');
        if (checkbox.checked) {
            row.classList.add('table-active');
        } else {
            row.classList.remove('table-active');
        }
    });
    
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

    // Check if QRCode library is available
    if (typeof QRCode === 'undefined') {
        showAlert('QR Code library not loaded. Attempting to load...', 'warning');
        loadQRCodeLibrary();
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

        console.log('Starting batch QR generation for', selectedTenants.length, 'tenants');

        // Prepare the display container first
        const displayContainer = document.getElementById('batch-qr-display');
        displayContainer.innerHTML = '';

        for (let i = 0; i < selectedTenants.length; i++) {
            const tenant = selectedTenants[i];
            currentTenantSpan.textContent = `${tenant.tenant_name} (${tenant.unit_no})`;
            progressCurrentSpan.textContent = i + 1;
            
            const progress = ((i + 1) / selectedTenants.length) * 100;
            progressBar.style.width = progress + '%';
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.textContent = Math.round(progress) + '%';

            // Generate QR code for this tenant with unique data
            const qrData = {
                propertyId: tenant.real_property_code,
                unitNumber: tenant.unit_no,
                meterId: null,
                tenantCode: tenant.tenant_code, // Add tenant code for uniqueness
                timestamp: Date.now() + i, // Add unique timestamp for each tenant
                type: 'batch',
                index: i
            };

            const qrText = JSON.stringify(qrData);
            
            console.log('Batch QR data for tenant:', tenant.tenant_name, ':', qrData);
            console.log('Batch QR text for tenant:', tenant.tenant_name, ':', qrText);
            
            // Create the display column first
            const col = document.createElement('div');
            col.className = 'col-md-4 col-lg-3 mb-4';
            
            col.innerHTML = `
                <div class="professional-qr batch-qr">
                    <div class="qr-code-container" id="batch-qr-${i}">
                        <div class="qr-code-wrapper" style="width: 200px; height: 200px; margin: 0 auto; display: flex; justify-content: center; align-items: center;">
                        </div>
                    </div>
                    <div class="qr-footer">
                        <p class="scan-instruction">Scan for Meter Reading</p>
                        <small class="qr-data">${tenant.real_property_code}|${tenant.unit_no}</small>
                    </div>
                </div>
            `;
            
            displayContainer.appendChild(col);
            
            // Now generate QR code in the wrapper container
            const qrContainer = document.getElementById(`batch-qr-${i}`);
            const qrWrapper = qrContainer.querySelector('.qr-code-wrapper');
            
            try {
                console.log('Creating QR code for tenant:', tenant.tenant_name, 'in container:', qrWrapper.id);
                
                // Clear any existing QR code in the wrapper
                qrWrapper.innerHTML = '';
                
                // Generate QR code in the wrapper container with smaller size
                const qrCode = new QRCode(qrWrapper, {
                    text: qrText,
                    width: 180,
                    height: 180,
                    colorDark: '#1e40af',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });

                console.log('QRCode instance created for:', tenant.tenant_name);

                // Small delay to ensure canvas is rendered
                await new Promise(resolve => setTimeout(resolve, 150));

                // Remove any duplicate elements (images) and keep only canvas
                const images = qrWrapper.querySelectorAll('img');
                images.forEach(img => img.remove());

                // Verify QR code was generated
                const canvas = qrWrapper.querySelector('canvas');
                console.log('Canvas found for tenant:', tenant.tenant_name, 'Canvas:', canvas);
                
                if (!canvas) {
                    console.error('No canvas found in QR container for tenant:', tenant.tenant_name);
                    console.log('QR container contents:', qrWrapper.innerHTML);
                    qrWrapper.innerHTML = '<div style="color: red; padding: 20px;">QR Code generation failed</div>';
                    continue;
                }

                // Ensure canvas is properly contained and sized
                canvas.style.display = 'block';
                canvas.style.maxWidth = '100%';
                canvas.style.maxHeight = '100%';
                canvas.style.width = 'auto';
                canvas.style.height = 'auto';
                canvas.style.margin = '0 auto';

                console.log('QR code generated successfully for:', tenant.tenant_name);
                console.log('Canvas dimensions:', canvas.width, 'x', canvas.height);

                batchQRCodes.push({
                    tenant: tenant,
                    qrData: qrData,
                    qrText: qrText,
                    qrContainer: qrContainer
                });
            } catch (qrError) {
                console.error('Error generating QR for tenant:', tenant.tenant_name, qrError);
                qrWrapper.innerHTML = '<div style="color: red; padding: 20px;">QR Code generation failed</div>';
                continue;
            }

            // Small delay to show progress
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        console.log('Batch generation complete. Generated', batchQRCodes.length, 'QR codes');

        // Hide progress and show results
        document.getElementById('batch-progress').style.display = 'none';
        document.getElementById('results-count').textContent = batchQRCodes.length;
        document.getElementById('batch-results').style.display = 'block';

    } catch (error) {
        console.error('Error generating batch QR codes:', error);
        showAlert('Error generating batch QR codes: ' + error.message, 'danger');
        document.getElementById('batch-progress').style.display = 'none';
    }
}

// Display batch QR results (simplified - QR codes are now generated directly in display containers)
function displayBatchResults() {
    console.log('Displaying batch results for', batchQRCodes.length, 'QR codes');
    document.getElementById('results-count').textContent = batchQRCodes.length;
    document.getElementById('batch-results').style.display = 'block';
}

// Camera test removed

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
    
    // Create print content with QR codes as images
    let printContent = '';
    let pageContent = '';
    let qrCount = 0;
    let totalQRs = batchQRCodes.length;
    
    batchQRCodes.forEach((qrCode, index) => {
        const canvas = qrCode.qrContainer.querySelector('canvas');
        if (canvas) {
            const qrImageDataURL = canvas.toDataURL('image/png');
            const tenant = qrCode.tenant;
            
            pageContent += `
                <div class="professional-qr">
                    <div class="qr-code-container">
                        <img src="${qrImageDataURL}" alt="QR Code">
                    </div>
                    <div class="qr-footer">
                        <p class="scan-instruction">Scan for Meter Reading</p>
                        <small class="qr-data">${tenant.real_property_code}|${tenant.unit_no}</small>
                    </div>
                </div>
            `;
            
            qrCount++;
            
            // Create new page after every 8 QR codes, but only if there are more QR codes to come
            if (qrCount % 8 === 0 && qrCount < totalQRs) {
                printContent += `
                    <div class="page">
                        <div class="qr-grid">
                            ${pageContent}
                        </div>
                    </div>
                `;
                pageContent = '';
            }
        }
    });
    
    // Add remaining QR codes to the last page (only if there's content)
    if (pageContent.trim()) {
        printContent += `
            <div class="page">
                <div class="qr-grid">
                    ${pageContent}
                </div>
            </div>
        `;
    }
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Batch QR Codes</title>
            <style>
                @page {
                    size: landscape;
                    margin: 10mm;
                }
                @media print {
                    html, body { 
                        margin: 0 !important; 
                        padding: 0 !important; 
                        height: auto !important;
                        overflow: visible !important;
                    }
                    .page { 
                        page-break-inside: avoid !important;
                        break-inside: avoid-page !important;
                    }
                    .page:last-child {
                        page-break-after: auto !important;
                        break-after: auto !important;
                    }
                }
                body { 
                    margin: 0; 
                    padding: 0; 
                    font-family: Arial, sans-serif; 
                    background: white;
                }
                .page {
                    width: 100%;
                    height: auto;
                    min-height: 0;
                    page-break-after: always;
                    break-after: page;
                    display: block;
                    overflow: hidden;
                }
                
                /* Ensure last page doesn't create extra blank page */
                .page:last-child {
                    page-break-after: auto !important;
                    break-after: auto !important;
                }
                .qr-grid {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    grid-template-rows: repeat(2, 1fr);
                    gap: 5mm;
                    width: 100%;
                    padding: 5mm;
                }
                .professional-qr { 
                    width: 50mm; 
                    height: 70mm; 
                    border: 2px solid #1e40af; 
                    border-radius: 8px; 
                    padding: 3mm; 
                    background: white; 
                    text-align: center; 
                    page-break-inside: avoid; 
                    break-inside: avoid-page; 
                    display: flex; 
                    flex-direction: column; 
                    justify-content: space-between;
                    margin: 0;
                }
                .qr-code-container { 
                    flex: 1; 
                    display: flex; 
                    justify-content: center; 
                    align-items: center; 
                    margin: 1mm 0;
                }
                .qr-code-container img { 
                    width: 40mm; 
                    height: 40mm; 
                    max-width: 40mm; 
                    max-height: 40mm;
                }
                .qr-footer { 
                    font-size: 8pt; 
                    color: #666; 
                    margin: 1mm 0 0 0; 
                }
                .scan-instruction { 
                    font-size: 9pt; 
                    color: #333; 
                    margin-bottom: 1mm; 
                    font-weight: bold;
                }
                .qr-data { 
                    font-size: 8pt; 
                    color: #666; 
                    font-family: monospace; 
                }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for images to load before printing
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

// Show alert messages using SweetAlert2
function showAlert(message, type = 'info') {
    // Map Bootstrap alert types to SweetAlert types
    const sweetAlertType = {
        'success': 'success',
        'danger': 'error',
        'warning': 'warning',
        'info': 'info'
    }[type] || 'info';
    
    // Map Bootstrap alert types to SweetAlert icons
    const icon = {
        'success': 'success',
        'danger': 'error',
        'warning': 'warning',
        'info': 'info'
    }[type] || 'info';
    
    // Show SweetAlert
    Swal.fire({
        title: type.charAt(0).toUpperCase() + type.slice(1),
        text: message,
        icon: icon,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        customClass: {
            popup: 'swal2-toast',
            title: 'swal2-toast-title',
            content: 'swal2-toast-content'
        }
    });
}

// Initialize QR code fallback if library fails
if (typeof QRCode === 'undefined') {
    console.warn('QRCode library not loaded, using fallback');
    window.QRCode = function(element, options) {
        console.log('Using QRCode fallback for element:', element);
        element.innerHTML = `
            <div style="width: 256px; height: 256px; border: 2px dashed #ccc; 
                 display: flex; align-items: center; justify-content: center; 
                 flex-direction: column; margin: 0 auto;">
                <i class="bi bi-qr-code" style="font-size: 4rem; color: #6b7280;"></i>
                <p style="margin-top: 1rem; color: #6b7280;">QR Library Error</p>
                <small style="color: #9ca3af; margin-top: 0.5rem;">Data: ${options.text || 'No data'}</small>
            </div>
        `;
    };
}

// Camera test removed

// Test function for debugging batch QR generation
window.testBatchQR = function() {
    console.log('Testing batch QR generation...');
    console.log('Active tenants:', activeTenants);
    console.log('Selected tenants:', selectedTenants);
    console.log('QRCode library:', typeof QRCode);
    
    if (activeTenants.length === 0) {
        console.log('No active tenants available');
        return;
    }
    
    // Select first tenant for testing
    selectedTenants = [activeTenants[0]];
    console.log('Selected first tenant for testing:', selectedTenants[0]);
    
    generateBatchQR();
};
