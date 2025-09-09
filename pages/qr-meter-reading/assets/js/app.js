/**
 * RMS QR Meter Reading System - Main Application
 * Executive Professional Design for Non-Technical Users
 * Includes authentication and session management
 */

class QRMeterReadingApp {
    constructor() {
        this.html5QrcodeScanner = null;
        this.isScanning = false;
        this.currentReading = null;
        this.offlineQueue = [];
        this.isAuthenticated = true; // Will be checked on initialization
        this.isSubmitting = false; // Prevent double form submission
        this.activeCameraStream = null; // Track active camera stream
        
        this.init();
    }

    init() {
        this.checkAuthentication();
        this.setupEventListeners();
        this.loadRecentReadings();
        this.setupOfflineSync();
        this.setCurrentDate();
        this.updateUserInfo();
    }

    async checkAuthentication() {
        try {
            // Check if we're authenticated by trying to load recent readings
            const response = await fetch('api/get-recent-readings.php');
            if (response.status === 401 || response.status === 403) {
                // Not authenticated, redirect to login
                window.location.href = 'auth/login.php';
                return;
            }
            this.isAuthenticated = true;
        } catch (error) {
            console.error('Authentication check failed:', error);
            // If there's a network error, we'll assume we're authenticated for now
            // The server-side will handle authentication properly
        }
    }

    updateUserInfo() {
        // Update user information in the UI if available
        const userInfoElements = document.querySelectorAll('[data-user-info]');
        userInfoElements.forEach(element => {
            const infoType = element.dataset.userInfo;
            // This will be populated by PHP on the server side
        });
    }

    setupEventListeners() {
        // Scanner controls
        document.getElementById('start-scanner').addEventListener('click', () => {
            this.startScanner();
        });

        document.getElementById('stop-scanner').addEventListener('click', () => {
            this.stopScanner();
        });

        // Form submission
        document.getElementById('reading-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitReadingForm(e);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isScanning) {
                this.stopScanner();
            }
        });

        // Logout button
        const logoutBtn = document.querySelector('a[href*="logout.php"]');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        }

        // Cleanup camera when page is unloaded
        window.addEventListener('beforeunload', () => {
            this.cleanupCameraStream();
        });

        // Cleanup camera when page becomes hidden (mobile browsers)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && this.isScanning) {
                this.stopScanner();
            }
        });
    }

    async logout() {
        try {
            // Clear any stored data
            localStorage.removeItem('qr_meter_readings_offline');
            
            // Immediate logout - no confirmation dialog (modern UX standard)
            window.location.href = 'auth/logout.php';
        } catch (error) {
            console.error('Logout error:', error);
            // Fallback to direct logout
            window.location.href = 'auth/logout.php';
        }
    }

    async startScanner() {
        try {
            this.showStatus('Initializing camera...', 'info');
            
            // Check if we're on HTTPS or localhost (required for camera access)
            if (!this.isSecureContext()) {
                this.showStatus('Camera access requires HTTPS or localhost. Using alternative method...', 'warning');
                // Try alternative approach for HTTP environments
                await this.startScannerHTTP();
                return;
            }
            
            // Check camera permissions
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Camera not supported on this device');
            }

            // Request camera permission with better error handling
            let stream;
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment', // Use back camera on mobile
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                // Store reference to the stream for cleanup
                this.activeCameraStream = stream;
            } catch (cameraError) {
                console.error('Camera permission denied:', cameraError);
                if (cameraError.name === 'NotAllowedError') {
                    this.showStatus('Camera permission denied. Please allow camera access and try again.', 'error');
                    this.showCameraPermissionUI('Camera permission denied');
                } else if (cameraError.name === 'NotFoundError') {
                    this.showStatus('No camera found on this device.', 'error');
                    this.showCameraPermissionUI('No camera found');
                } else {
                    this.showStatus(`Camera error: ${cameraError.message}`, 'error');
                    this.showCameraPermissionUI('Camera initialization failed');
                }
                return;
            }

            // Initialize QR scanner
            this.html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                // Mobile-specific configurations
                disableFlip: false,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            await this.html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                this.onScanSuccess.bind(this),
                this.onScanFailure.bind(this)
            );

            this.isScanning = true;
            this.updateScannerUI();
            this.showStatus('Scanner active - Point camera at QR code', 'success');
            
            // Add scanning animation
            this.addScanningAnimation();

        } catch (error) {
            console.error('Scanner initialization failed:', error);
            this.showStatus(`Scanner error: ${error.message}`, 'error');
            this.showCameraPermissionUI('Scanner initialization failed');
        }
    }

    // Alternative scanner method for HTTP environments
    async startScannerHTTP() {
        try {
            // Show HTTP-specific UI
            this.showHTTPCameraUI();
            
            // Try to use a different approach for HTTP
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Some browsers might allow camera access on HTTP for localhost or trusted sites
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        } 
                    });
                    
                    // If we get here, camera access was granted
                    this.initializeQRScanner(stream);
                    return;
                } catch (httpCameraError) {
                    console.log('HTTP camera access failed:', httpCameraError);
                    // Continue to show HTTP-specific UI
                }
            }
            
        } catch (error) {
            console.error('HTTP scanner initialization failed:', error);
        }
    }

    // Initialize QR scanner with stream
    async initializeQRScanner(stream) {
        try {
            this.html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                disableFlip: false,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true
                }
            };

            await this.html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                this.onScanSuccess.bind(this),
                this.onScanFailure.bind(this)
            );

            this.isScanning = true;
            this.updateScannerUI();
            this.showStatus('Scanner active - Point camera at QR code', 'success');
            this.addScanningAnimation();
            
        } catch (error) {
            console.error('QR scanner initialization failed:', error);
            this.showStatus('Failed to initialize QR scanner', 'error');
        }
    }

    // Show HTTP-specific camera UI
    showHTTPCameraUI() {
        const qrReader = document.getElementById('qr-reader');
        qrReader.innerHTML = `
            <div class="camera-permission">
                <i class="bi bi-shield-lock" style="font-size: 3rem; color: var(--warning-amber);"></i>
                <h3>HTTP Environment Detected</h3>
                <p>Camera access is limited in HTTP environments. You can:</p>
                <ul class="text-start">
                    <li>Use the manual entry form below</li>
                    <li>Access via HTTPS when available</li>
                    <li>Use localhost for testing</li>
                </ul>
                <div class="d-grid gap-2">
                    <button class="btn btn-scan-primary" onclick="qrMeterApp.showManualEntryForm()">
                        <i class="bi bi-pencil-square me-2"></i>
                        Manual Entry
                    </button>
                    <button class="btn btn-secondary" onclick="qrMeterApp.tryCameraAnyway()">
                        <i class="bi bi-camera me-2"></i>
                        Try Camera Anyway
                    </button>
                </div>
            </div>
        `;
    }

    // Show manual entry form
    showManualEntryForm() {
        const qrReader = document.getElementById('qr-reader');
        qrReader.innerHTML = `
            <div class="camera-permission">
                <i class="bi bi-pencil-square" style="font-size: 3rem; color: var(--primary-blue);"></i>
                <h3>Manual Entry Mode</h3>
                <p>Enter meter information manually:</p>
                <div class="row g-3">
                    <div class="col-12">
                        <label for="manual-property-id" class="form-label">Property ID</label>
                        <input type="text" class="form-control" id="manual-property-id" placeholder="Enter Property ID">
                    </div>
                    <div class="col-12">
                        <label for="manual-unit-number" class="form-label">Unit Number</label>
                        <input type="text" class="form-control" id="manual-unit-number" placeholder="Enter Unit Number">
                    </div>
                    <div class="col-12">
                        <label for="manual-meter-id" class="form-label">Meter ID</label>
                        <input type="text" class="form-control" id="manual-meter-id" placeholder="Enter Meter ID">
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-scan-primary" onclick="qrMeterApp.submitManualEntry()">
                        <i class="bi bi-check-circle me-2"></i>
                        Continue to Reading
                    </button>
                    <button class="btn btn-secondary" onclick="qrMeterApp.showHTTPCameraUI()">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back
                    </button>
                </div>
            </div>
        `;
    }

    // Submit manual entry
    submitManualEntry() {
        const propertyId = document.getElementById('manual-property-id').value.trim();
        const unitNumber = document.getElementById('manual-unit-number').value.trim();
        const meterId = document.getElementById('manual-meter-id').value.trim();
        
        if (!propertyId || !unitNumber) {
            this.showStatus('Please enter Property ID and Unit Number', 'error');
            return;
        }
        
        const qrData = {
            propertyId: propertyId,
            unitNumber: unitNumber,
            meterId: meterId,
            timestamp: Date.now()
        };
        
        this.populateForm(qrData);
        this.showStatus('Manual entry completed!', 'success');
        // Call enhanced reading form with manual entry data
        this.showReadingForm(propertyId, unitNumber, meterId);
    }

    // Try camera anyway (for some browsers that might allow it)
    async tryCameraAnyway() {
        try {
            this.showStatus('Attempting camera access...', 'info');
            
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    } 
                });
                
                this.initializeQRScanner(stream);
            } else {
                this.showStatus('Camera not supported', 'error');
                this.showHTTPCameraUI();
            }
        } catch (error) {
            console.error('Camera access failed:', error);
            this.showStatus('Camera access not available in HTTP environment', 'error');
            this.showHTTPCameraUI();
        }
    }

    stopScanner() {
        if (this.html5QrcodeScanner && this.isScanning) {
            this.html5QrcodeScanner.stop().then(() => {
                this.isScanning = false;
                this.updateScannerUI();
                this.showStatus('Scanner stopped', 'info');
                this.removeScanningAnimation();
                
                // Additional cleanup to ensure camera is fully released
                this.cleanupCameraStream();
            }).catch(error => {
                console.error('Error stopping scanner:', error);
                // Even if stop fails, try to cleanup
                this.isScanning = false;
                this.updateScannerUI();
                this.cleanupCameraStream();
            });
        }
    }

    cleanupCameraStream() {
        // Stop the tracked camera stream
        if (this.activeCameraStream) {
            this.activeCameraStream.getTracks().forEach(track => {
                track.stop();
                console.log('Camera track stopped:', track.kind);
            });
            this.activeCameraStream = null;
        }
        
        // Also try to stop any other active video tracks as a fallback
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    stream.getTracks().forEach(track => {
                        if (track.readyState === 'live') {
                            track.stop();
                            console.log('Additional camera track stopped:', track.kind);
                        }
                    });
                })
                .catch(error => {
                    // This is expected if no camera is active
                    console.log('No additional camera streams to cleanup');
                });
        }
    }

    onScanSuccess(decodedText, decodedResult) {
        try {
            // Stop scanner on successful scan
            this.stopScanner();
            
            // Parse QR code data
            const qrData = this.parseQRData(decodedText);
            
            if (qrData) {
                this.populateForm(qrData);
                this.showStatus('QR code scanned successfully!', 'success');
                // Call enhanced reading form with parsed data
                this.showReadingForm(qrData.propertyId, qrData.unitNumber, qrData.meterId);
            } else {
                this.showStatus('Invalid QR code format', 'error');
            }
            
        } catch (error) {
            console.error('Error processing QR code:', error);
            this.showStatus('Error processing QR code', 'error');
        }
    }

    onScanFailure(error) {
        // Ignore scan failures - they're normal during scanning
        // Only log for debugging
        if (error && error.name !== 'NotFoundException') {
            console.debug('Scan failure:', error);
        }
    }

    parseQRData(qrText) {
        try {
            // Try to parse as JSON first
            if (qrText.startsWith('{')) {
                const data = JSON.parse(qrText);
                return {
                    propertyId: data.p || data.propertyId,
                    unitNumber: data.u || data.unitNumber,
                    meterId: data.m || data.meterId,
                    timestamp: data.t || data.timestamp
                };
            }
            
            // Fallback to pipe-separated format
            const parts = qrText.split('|');
            if (parts.length >= 3) {
                return {
                    propertyId: parts[0],
                    unitNumber: parts[1],
                    meterId: parts[2] || '',
                    timestamp: parts[3] || Date.now()
                };
            }
            
            return null;
        } catch (error) {
            console.error('Error parsing QR data:', error);
            return null;
        }
    }

    populateForm(qrData) {
        document.getElementById('property-id').value = qrData.propertyId || '';
        document.getElementById('unit-number').value = qrData.unitNumber || '';
        document.getElementById('meter-id').value = qrData.meterId || '';
        
        // Store current reading data
        this.currentReading = qrData;
    }

    // Enhanced reading form with period display and API integration
    showReadingForm(propertyCode, unitNo, meterId = null) {
        const formCard = document.getElementById('reading-form-card');
        const form = document.getElementById('reading-form');
        
        // Clear previous form data
        form.reset();
        
        // Set property and unit information
        document.getElementById('property-id').value = propertyCode;
        document.getElementById('unit-number').value = unitNo;
        document.getElementById('meter-id').value = meterId || '';
        
        // Show the form
        formCard.classList.remove('scanner-hidden');
        formCard.classList.add('scanner-visible');
        
        // Scroll to form
        formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Fetch tenant information and last reading, then auto-focus
        this.fetchTenantAndReadingInfo(propertyCode, unitNo);
    }
    
    // Fetch tenant information and last reading data
    async fetchTenantAndReadingInfo(propertyCode, unitNo) {
        try {
            // Fetch tenant information
            const tenantResponse = await fetch(`api/get-tenant-by-unit.php?propertyCode=${encodeURIComponent(propertyCode)}&unitNo=${encodeURIComponent(unitNo)}`);
            const tenantData = await tenantResponse.json();
            
            if (tenantData.success && tenantData.data) {
                // Update tenant information display
                this.updateTenantInfo(tenantData.data);
            }
            
            // Fetch last reading
            const readingResponse = await fetch(`api/get-last-reading.php?propertyCode=${encodeURIComponent(propertyCode)}&unitNo=${encodeURIComponent(unitNo)}`);
            const readingData = await readingResponse.json();
            
            if (readingData.success && readingData.data) {
                // Update last reading display
                this.updateLastReadingInfo(readingData.data);
            }
            
            // Auto-focus on current meter reading input after data is loaded
            setTimeout(() => {
                const currentReadingInput = document.getElementById('current-meter-reading');
                if (currentReadingInput) {
                    currentReadingInput.focus();
                }
            }, 200);
            
        } catch (error) {
            console.error('Error fetching tenant/reading info:', error);
            this.showStatus('Error fetching property information', 'danger');
            
            // Still try to focus even if there's an error
            setTimeout(() => {
                const currentReadingInput = document.getElementById('current-meter-reading');
                if (currentReadingInput) {
                    currentReadingInput.focus();
                }
            }, 200);
        }
    }
    
    // Update tenant information display
    updateTenantInfo(tenantData) {
        const tenantInfoDiv = document.getElementById('tenant-info');
        if (tenantInfoDiv) {
            tenantInfoDiv.innerHTML = `
                <div class="alert alert-info border-0">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tenant:</strong> ${tenantData.tenantName}<br>
                            <strong>Property:</strong> ${tenantData.realPropertyName}<br>
                            <strong>Unit:</strong> ${tenantData.unitNo}
                        </div>
                        <div class="col-md-6">
                            <strong>Meter:</strong> ${tenantData.meterNumber || 'N/A'}<br>
                            <strong>Type:</strong> ${tenantData.unitType || 'N/A'}<br>
                            <strong>Status:</strong> <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // Update last reading information display
    updateLastReadingInfo(readingData) {
        const lastReadingDiv = document.getElementById('last-reading-info');
        if (lastReadingDiv) {
            const prevReading = readingData.prevReading || 'N/A';
            const currentReading = readingData.currentReading || 'N/A';
            const usage = readingData.usage || 'N/A';
            
            lastReadingDiv.innerHTML = `
                <div class="alert alert-warning border-0">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Last Reading:</strong> ${currentReading}<br>
                            <strong>Previous Reading:</strong> ${prevReading}<br>
                            <strong>Last Reading Date:</strong> ${readingData.readingDate ? new Date(readingData.readingDate).toLocaleDateString() : 'N/A'}<br>
                            <strong>Usage:</strong> ${usage}
                        </div>
                        <div class="col-md-6">
                            <strong>Reading Period:</strong> ${readingData.dateFrom ? new Date(readingData.dateFrom).toLocaleDateString() : 'N/A'} - ${readingData.dateTo ? new Date(readingData.dateTo).toLocaleDateString() : 'N/A'}<br>
                            <strong>Billing Period:</strong> ${readingData.billingDateFrom ? new Date(readingData.billingDateFrom).toLocaleDateString() : 'N/A'} - ${readingData.billingDateTo ? new Date(readingData.billingDateTo).toLocaleDateString() : 'N/A'}
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // Enhanced form submission with API integration
    async submitReadingForm(event) {
        event.preventDefault();
        
        // Prevent double submission
        if (this.isSubmitting) {
            return;
        }
        
        const formData = new FormData(event.target);
        const readingData = {
            propertyCode: formData.get('propertyCode'),
            unitNo: formData.get('unitNo'),
            currentReading: parseFloat(formData.get('currentReading')),
            remarks: formData.get('remarks') || '',
            locationData: await this.getLocationData()
        };
        
        // Enhanced validation
        if (!readingData.propertyCode || !readingData.unitNo) {
            this.showStatus('Property code and unit number are required', 'danger');
            return;
        }
        
        if (!readingData.currentReading || readingData.currentReading <= 0) {
            this.showStatus('Please enter a valid current reading greater than 0', 'danger');
            return;
        }
        
        // Set submitting state
        this.isSubmitting = true;
        
        // Show loading state
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
            submitBtn.disabled = true;
            
            // Submit reading to API
            const response = await fetch('api/save-reading.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(readingData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Show simple success message
                Swal.fire({
                    icon: 'success',
                    title: 'Reading Saved Successfully!',
                    text: 'Your meter reading has been recorded and will appear in the Recent Readings table below.',
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#198754',
                    width: '500px',
                    customClass: {
                        popup: 'swal2-popup-custom'
                    }
                });
                
                // Reset form and hide
                event.target.reset();
                const formCard = document.getElementById('reading-form-card');
                formCard.classList.add('scanner-hidden');
                formCard.classList.remove('scanner-visible');
                
                // Refresh recent readings table after a short delay
                setTimeout(async () => {
                    await this.loadRecentReadings();
                }, 500);
                
            } else {
                throw new Error(result.message || 'Unknown error occurred');
            }
            
        } catch (error) {
            console.error('Error saving reading:', error);
            
            // Show error message with SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Error Saving Reading',
                text: error.message || 'An error occurred while saving the reading',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            
        } finally {
            // Reset submitting state
            this.isSubmitting = false;
            
            // Restore button state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }
    
    // Update recent readings table
    async updateRecentReadings() {
        try {
            const response = await fetch('api/meter-reading-report.php?startDate=' + 
                encodeURIComponent(new Date().toISOString().split('T')[0]) + 
                '&endDate=' + encodeURIComponent(new Date().toISOString().split('T')[0]) + 
                '&limit=10');
            
            const data = await response.json();
            
            if (data.success && data.data.readings) {
                this.populateRecentReadingsTable(data.data.readings);
            }
        } catch (error) {
            console.error('Error updating recent readings:', error);
        }
    }
    
    // Populate recent readings table
    populateRecentReadingsTable(readings) {
        const tbody = document.getElementById('readings-table-body');
        
        if (readings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent readings</td></tr>';
            return;
        }
        
        tbody.innerHTML = readings.map(reading => `
            <tr>
                <td>${reading.real_property_name}</td>
                <td>${reading.unit_no}</td>
                <td>${reading.tenant_name}</td>
                <td>${reading.current_reading}</td>
                <td>${new Date(reading.reading_date).toLocaleDateString()}</td>
                <td>
                    <span class="badge bg-success">Saved</span>
                </td>
            </tr>
        `).join('');
    }





    async submitReadingOnline(readingData) {
        try {
            const response = await fetch('api/save-reading.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(readingData)
            });

            if (response.status === 401 || response.status === 403) {
                // Not authenticated, redirect to login
                window.location.href = 'auth/login.php';
                return false;
            }

            if (response.ok) {
                const result = await response.json();
                return result.success;
            }
            
            return false;
        } catch (error) {
            console.error('Network error:', error);
            return false;
        }
    }

    storeOfflineReading(readingData) {
        this.offlineQueue.push(readingData);
        localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
        
        // Update UI to show offline status
        this.updateOfflineStatus();
    }

    async setupOfflineSync() {
        // Load offline queue
        const offlineData = localStorage.getItem('qr_meter_readings_offline');
        if (offlineData) {
            this.offlineQueue = JSON.parse(offlineData);
            this.updateOfflineStatus();
        }

        // Try to sync offline readings
        if (this.offlineQueue.length > 0) {
            await this.syncOfflineReadings();
        }
    }

    async syncOfflineReadings() {
        const readingsToSync = [...this.offlineQueue];
        
        for (const reading of readingsToSync) {
            try {
                const success = await this.submitReadingOnline(reading);
                if (success) {
                    // Remove from offline queue
                    this.offlineQueue = this.offlineQueue.filter(r => r !== reading);
                    localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
                }
            } catch (error) {
                console.error('Error syncing offline reading:', error);
            }
        }
        
        this.updateOfflineStatus();
    }

    updateOfflineStatus() {
        if (this.offlineQueue.length > 0) {
            this.showStatus(`${this.offlineQueue.length} reading(s) saved offline`, 'info');
        }
    }

    async loadRecentReadings() {
        try {
            console.log('Loading recent readings...');
            const response = await fetch('api/get-recent-readings.php');
            
            if (response.status === 401 || response.status === 403) {
                // Not authenticated, redirect to login
                window.location.href = 'auth/login.php';
                return;
            }
            
            if (response.ok) {
                const result = await response.json();
                console.log('Recent readings response:', result);
                if (result.success) {
                    this.displayRecentReadings(result.data);
                }
            } else {
                console.error('Failed to load recent readings:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error loading recent readings:', error);
        }
    }

    displayRecentReadings(readings) {
        console.log('Displaying recent readings:', readings);
        const tbody = document.getElementById('readings-table-body');
        
        if (!readings || readings.length === 0) {
            console.log('No readings to display');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent readings</td></tr>';
            return;
        }

        tbody.innerHTML = readings.map(reading => `
            <tr>
                <td>${reading.propertyName || reading.propertyId || 'N/A'}</td>
                <td>${reading.unitNumber || 'N/A'}</td>
                <td>${reading.tenantName || 'N/A'}</td>
                <td>${reading.meterReading ? reading.meterReading.toLocaleString() : 'N/A'}</td>
                <td>${this.formatDate(reading.readingDate)}</td>
                <td>
                    <span class="badge bg-success">Submitted</span>
                </td>
            </tr>
        `).join('');
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

    setCurrentDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('reading-date').value = today;
    }

    resetForm() {
        document.getElementById('reading-form').reset();
        this.setCurrentDate();
        this.currentReading = null;
    }

    hideReadingForm() {
        const formCard = document.getElementById('reading-form-card');
        formCard.classList.add('scanner-hidden');
        formCard.classList.remove('scanner-visible');
    }

    updateScannerUI() {
        const startBtn = document.getElementById('start-scanner');
        const stopBtn = document.getElementById('stop-scanner');
        const viewport = document.querySelector('.qr-viewport');

        if (this.isScanning) {
            startBtn.classList.add('scanner-hidden');
            startBtn.classList.remove('scanner-visible');
            stopBtn.classList.remove('scanner-hidden');
            stopBtn.classList.add('scanner-visible');
            viewport.classList.add('active', 'scanning');
        } else {
            startBtn.classList.remove('scanner-hidden');
            startBtn.classList.add('scanner-visible');
            stopBtn.classList.add('scanner-hidden');
            stopBtn.classList.remove('scanner-visible');
            viewport.classList.remove('active', 'scanning');
        }
    }

    async getLocationData() {
        try {
            if (!navigator.geolocation) {
                return null;
            }
            
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 300000 // 5 minutes
                });
            });
            
            return JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                timestamp: new Date(position.timestamp).toISOString()
            });
        } catch (error) {
            console.log('Location access denied or unavailable:', error.message);
            return null;
        }
    }

    showStatus(message, type = 'info') {
        const statusDiv = document.getElementById('scanner-status');
        const statusText = document.getElementById('status-text');
        
        statusText.textContent = message;
        statusDiv.className = `alert alert-${type} mt-3`;
        statusDiv.classList.remove('scanner-hidden');
        statusDiv.classList.add('scanner-visible');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            statusDiv.classList.add('scanner-hidden');
            statusDiv.classList.remove('scanner-visible');
        }, 5000);
    }

    showCameraPermissionUI(message = 'Camera access required') {
        const qrReader = document.getElementById('qr-reader');
        
        let title = 'Camera Access Required';
        let description = 'This app needs camera access to scan QR codes. Please allow camera permissions and try again.';
        let icon = 'bi-camera-video-off';
        
        // Customize message based on error type
        if (message.includes('HTTPS')) {
            title = 'Secure Connection Required';
            description = 'Camera access requires HTTPS or localhost. Please access this page using a secure connection (https://) or from localhost.';
            icon = 'bi-shield-lock';
        } else if (message.includes('permission denied')) {
            title = 'Camera Permission Denied';
            description = 'Camera access was denied. Please allow camera permissions in your browser settings and try again.';
            icon = 'bi-camera-video-off';
        } else if (message.includes('No camera found')) {
            title = 'No Camera Found';
            description = 'No camera was found on this device. Please use a device with a camera.';
            icon = 'bi-camera-video-off';
        } else if (message.includes('initialization failed')) {
            title = 'Scanner Initialization Failed';
            description = 'Failed to initialize the camera scanner. Please refresh the page and try again.';
            icon = 'bi-exclamation-triangle';
        }
        
        qrReader.innerHTML = `
            <div class="camera-permission">
                <i class="bi ${icon}" style="font-size: 3rem; color: var(--text-tertiary);"></i>
                <h3>${title}</h3>
                <p>${description}</p>
                <div class="d-grid gap-2">
                <button class="btn btn-scan-primary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i>
                    Retry
                </button>
                    <button class="btn btn-secondary" onclick="qrMeterApp.showManualEntryForm()">
                        <i class="bi bi-pencil-square me-2"></i>
                        Manual Entry
                    </button>
                    <button class="btn btn-outline-secondary" onclick="qrMeterApp.hideCameraPermissionUI()">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancel
                    </button>
                </div>
            </div>
        `;
    }

    hideCameraPermissionUI() {
        const cameraPermission = document.querySelector('.camera-permission');
        if (cameraPermission) {
            cameraPermission.remove();
        }
        // Show start scanner button
        const startBtn = document.getElementById('start-scanner');
        startBtn.classList.remove('scanner-hidden');
        startBtn.classList.add('scanner-visible');
    }

    addScanningAnimation() {
        const viewport = document.querySelector('.qr-viewport');
        const scanningLine = document.createElement('div');
        scanningLine.className = 'scanning-line';
        scanningLine.id = 'scanning-line';
        viewport.appendChild(scanningLine);
    }

    removeScanningAnimation() {
        const scanningLine = document.getElementById('scanning-line');
        if (scanningLine) {
            scanningLine.remove();
        }
    }

    // Check if context is secure (HTTPS or localhost)
    isSecureContext() {
        return window.isSecureContext || location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Add Bootstrap Icons
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css';
    document.head.appendChild(link);
    
    // Initialize the app
    window.qrMeterApp = new QRMeterReadingApp();
});

// Service Worker for offline functionality
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('service-worker.js')
            .then(registration => {
                console.log('ServiceWorker registered successfully');
            })
            .catch(error => {
                console.log('ServiceWorker registration failed:', error);
            });
    });
}
