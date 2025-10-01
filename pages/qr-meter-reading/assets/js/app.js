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
        this.isOnline = navigator.onLine; // Track online status
        this.offlineIndicator = null; // Reference to offline indicator element
        this.appConfig = null; // Application configuration
        this.wasOffline = false; // Track if we were previously offline
        this.lastFormInteraction = 0; // Track last form interaction time
        
        // Phase 9: Cache-first tenant resolution system
        this.comprehensiveCache = null; // Cache for all tenant data
        this.tenantResolutionService = null; // Will be initialized after cache
        this.dataValidationPipeline = new DataValidationPipeline();
        this.enhancedOfflineStorage = new EnhancedOfflineStorage();
        
        // Phase 11: Store current tenant data for offline use
        this.currentTenantData = null; // Will be set when fetching tenant info
        
        this.init();
    }

    // Utility function to normalize property codes and unit numbers
    normalizePropertyCode(propertyCode) {
        return propertyCode ? propertyCode.trim() : '';
    }

    normalizeUnitNo(unitNo) {
        return unitNo ? unitNo.trim() : '';
    }

    // Utility function to normalize both property code and unit number
    normalizePropertyAndUnit(propertyCode, unitNo) {
        return {
            propertyCode: this.normalizePropertyCode(propertyCode),
            unitNo: this.normalizeUnitNo(unitNo)
        };
    }

    async init() {
        await this.loadAppConfig();
        this.checkAuthentication();
        this.setupEventListeners();
        
        // Phase 9: Initialize cache-first system
        await this.initializeComprehensiveCache();
        
        this.loadRecentReadings();
        this.setupOfflineSync();
        this.setupOfflineDetection();
        this.setCurrentDate();
        this.updateUserInfo();
        
        // Add testing functionality for screenshots (only in testing mode)
        if (this.appConfig && this.appConfig.isTesting) {
            this.addTestingControls();
        }
    }

    async loadAppConfig() {
        try {
            const response = await fetch('api/get-config.php');
            if (response.ok) {
                this.appConfig = await response.json();
                console.log('App configuration loaded:', this.appConfig);
            } else {
                console.warn('Failed to load app configuration, defaulting to production mode');
                this.appConfig = { isTesting: false, isProduction: true };
            }
        } catch (error) {
            console.warn('Error loading app configuration:', error);
            this.appConfig = { isTesting: false, isProduction: true };
        }
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

        // Track form interactions for offline notification
        const formInputs = ['currentReading', 'remarks'];
        formInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('focus', () => {
                    this.lastFormInteraction = Date.now();
                });
                input.addEventListener('input', () => {
                    this.lastFormInteraction = Date.now();
                });
            }
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
        
        // Normalize property code and unit number
        const normalized = this.normalizePropertyAndUnit(propertyCode, unitNo);
        
        // Clear previous form data
        form.reset();
        
        // Set property and unit information (use normalized values)
        document.getElementById('property-id').value = normalized.propertyCode;
        document.getElementById('unit-number').value = normalized.unitNo;
        document.getElementById('meter-id').value = meterId || '';
        
        // Show the form
        formCard.classList.remove('scanner-hidden');
        formCard.classList.add('scanner-visible');
        
        // Scroll to form
        formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Fetch tenant information and last reading, then auto-focus
        this.fetchTenantAndReadingInfo(normalized.propertyCode, normalized.unitNo);
    }
    
    // Phase 9: Cache-first tenant and reading information fetch
    async fetchTenantAndReadingInfo(propertyCode, unitNo) {
        try {
            console.log(`Fetching tenant and reading info for ${propertyCode}-${unitNo}`);
            
            // Phase 9: Use cache-first tenant resolution
            if (!this.tenantResolutionService) {
                throw new Error('Tenant resolution service not initialized');
            }
            const tenantData = await this.tenantResolutionService.resolveTenantWithFallback(propertyCode, unitNo);
            
            if (tenantData && tenantData.success) {
                // Update tenant information display
                this.updateTenantInfo(tenantData.data);
            } else {
                console.warn('No tenant data found for property/unit');
                this.showStatus('No tenant information found for this property/unit', 'warning');
            }
            
            // Phase 9: Get previous reading from cache or network
            const readingData = await this.getPreviousReadingData(propertyCode, unitNo);
            
            if (readingData && readingData.success) {
                // Update last reading display
                this.updateLastReadingInfo(readingData.data);
                
                // PHASE 11: Check for duplicate reading in the same reading period upon QR scan
                const duplicateCheck = await this.checkDuplicateReading(propertyCode, unitNo);
                if (duplicateCheck.isDuplicate) {
                    // Show duplicate notification (following UX design standards)
                    this.showDuplicateNotification(duplicateCheck.message, duplicateCheck.details);
                    // Hide the form since it's a duplicate
                    const formCard = document.getElementById('reading-form-card');
                    formCard.classList.add('scanner-hidden');
                    formCard.classList.remove('scanner-visible');
                    // Don't proceed further
                    return;
                }
            } else {
                console.warn('No previous reading data found');
                this.showStatus('No previous reading found - this may be the first reading', 'info');
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
        // PHASE 11: Store tenant data for offline use
        this.currentTenantData = tenantData;
        
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
            
                        // Create prominent Last Reading card with Executive Professional styling
            lastReadingDiv.innerHTML = `
                <div class="card border-0 shadow-sm last-reading-card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>Last Reading Information
                        </h6>
                        </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="text-center">
                                    <div class="text-muted small">Last Reading</div>
                                    <div class="h2 mb-0 text-info fw-bold">${currentReading}</div>
                                    <div class="small text-muted">${readingData.readingDate ? new Date(readingData.readingDate).toLocaleDateString() : 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <div class="text-muted small">Previous</div>
                                    <div class="h2 mb-0 text-muted">${prevReading}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <div class="text-muted small">Usage</div>
                                    <div class="h2 mb-0 text-muted">${usage}</div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="row g-2">
                            <div class="col-6 text-center">
                                <small class="text-muted">
                                    <strong>Reading Period:</strong><br>${readingData.dateFrom ? new Date(readingData.dateFrom).toLocaleDateString() : 'N/A'} - ${readingData.dateTo ? new Date(readingData.dateTo).toLocaleDateString() : 'N/A'}
                                </small>
                            </div>
                            <div class="col-6 text-center">
                                <small class="text-muted">
                                    <strong>Billing Period:</strong><br>${readingData.billingDateFrom ? new Date(readingData.billingDateFrom).toLocaleDateString() : 'N/A'} - ${readingData.billingDateTo ? new Date(readingData.billingDateTo).toLocaleDateString() : 'N/A'}
                                </small>
                            </div>
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
            propertyCode: this.normalizePropertyCode(formData.get('propertyCode')),
            unitNo: this.normalizeUnitNo(formData.get('unitNo')),
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
        
        // PHASE 11: Offline-First Duplicate Validation using existing cache system
        // Use existing TenantResolutionService and comprehensiveCache
        console.log('=== Duplicate Validation Debug ===');
        console.log('Property Code:', readingData.propertyCode);
        console.log('Unit No:', readingData.unitNo);
        console.log('Tenant Resolution Service exists:', !!this.tenantResolutionService);
        
        if (this.tenantResolutionService) {
            // Check offline readings first (primary data source in offline-first)
            const offlineReadings = this.tenantResolutionService.getOfflineReadings();
            console.log('Offline readings count:', offlineReadings.length);
            console.log('Offline readings:', offlineReadings);
            
            const offlineDuplicate = offlineReadings.find(reading => 
                reading.propertyCode === readingData.propertyCode && 
                reading.unitNo === readingData.unitNo
            );
            
            console.log('Offline duplicate found:', offlineDuplicate);
            
            if (offlineDuplicate) {
                const duplicateDate = new Date(offlineDuplicate.timestamp).toLocaleDateString();
                const duplicateValue = offlineDuplicate.currentReading;
                const errorMessage = `Duplicate reading detected! A reading for ${readingData.propertyCode}-${readingData.unitNo} is already saved offline on ${duplicateDate} with value ${duplicateValue}. Please verify this is not a duplicate entry.`;
                console.log('Showing offline duplicate error:', errorMessage);
                this.showInlineValidationError('current-meter-reading', errorMessage);
                return;
            }
            
            // Check comprehensive cache for recent readings in the same billing period
            if (this.comprehensiveCache && this.comprehensiveCache.latestReadings) {
                console.log('Cache readings count:', this.comprehensiveCache.latestReadings.length);
                
                const normalized = this.tenantResolutionService.normalizePropertyAndUnit(readingData.propertyCode, readingData.unitNo);
                console.log('Normalized property code:', normalized.propertyCode);
                console.log('Normalized unit no:', normalized.unitNo);
                
                // Get current month range for billing period check
                const now = new Date();
                const currentMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
                const currentMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                console.log('Current month range:', currentMonthStart, 'to', currentMonthEnd);
                
                const cacheDuplicate = this.comprehensiveCache.latestReadings.find(reading => {
                    const normalizedProp = this.tenantResolutionService.normalizePropertyCode(reading.property_code);
                    const normalizedUnit = this.tenantResolutionService.normalizeUnitNo(reading.unit_no);
                    const propertyMatches = normalizedProp === normalized.propertyCode && normalizedUnit === normalized.unitNo;
                    
                    if (propertyMatches) {
                        // Check if reading is in the same reading period (current month)
                        // Use reading_date_from and reading_date_to (not date_from/date_to)
                        const readingDateFrom = new Date(reading.reading_date_from || reading.date_from);
                        const readingDateTo = new Date(reading.reading_date_to || reading.date_to);
                        const inSamePeriod = readingDateFrom >= currentMonthStart && readingDateTo <= currentMonthEnd;
                        
                        console.log('Property match found:', reading);
                        console.log('Reading period:', readingDateFrom, 'to', readingDateTo);
                        console.log('In same reading period:', inSamePeriod);
                        
                        return inSamePeriod;
                    }
                    return false;
                });
                
                console.log('Cache duplicate found:', cacheDuplicate);
                
                if (cacheDuplicate) {
                    const duplicateDate = new Date(cacheDuplicate.reading_date).toLocaleDateString();
                    const duplicateValue = cacheDuplicate.current_reading;
                    const dateFrom = cacheDuplicate.reading_date_from || cacheDuplicate.date_from;
                    const dateTo = cacheDuplicate.reading_date_to || cacheDuplicate.date_to;
                    const readingPeriod = new Date(dateFrom).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}) + ' - ' + new Date(dateTo).toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
                    const errorMessage = `Duplicate reading detected! A reading for ${readingData.propertyCode}-${readingData.unitNo} already exists for reading period ${readingPeriod} on ${duplicateDate} with value ${duplicateValue}. Please verify this is not a duplicate entry.`;
                    console.log('Showing cache duplicate error:', errorMessage);
                    this.showInlineValidationError('current-meter-reading', errorMessage);
                    return;
                }
            }
        }
        console.log('=== No duplicates found, continuing ===');
        
        // Set submitting state
        this.isSubmitting = true;
        
        // PHASE 11: Get ALL submit buttons (mobile and desktop)
        const submitBtns = event.target.querySelectorAll('button[type="submit"]');
        const originalTexts = Array.from(submitBtns).map(btn => btn.innerHTML);
        
        try {
            // Update all submit buttons
            submitBtns.forEach(btn => {
                btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
                btn.disabled = true;
            });
            
            // Check if online
            if (!this.isOnline) {
                // PHASE 11: Show offline-specific progress indicator on ALL buttons
                submitBtns.forEach(btn => {
                    btn.innerHTML = '<i class="bi bi-cloud-download me-2"></i>Saving Offline...';
                });
                
                // PHASE 11: Allow DOM to repaint before storing (makes button update visible)
                await new Promise(resolve => setTimeout(resolve, 100));
                
                // Store offline and wait for completion (now async)
                const saveSuccess = await this.storeOfflineReading(readingData);
                
                if (saveSuccess) {
                this.showSuccessToast(
                    'Reading Saved Offline!',
                    'Will sync when connection is restored'
                );
                
                // Reset form and hide
                event.target.reset();
                const formCard = document.getElementById('reading-form-card');
                formCard.classList.add('scanner-hidden');
                formCard.classList.remove('scanner-visible');
                    
                    // Reset all submit buttons for next scan
                    submitBtns.forEach((btn, index) => {
                        btn.innerHTML = originalTexts[index];
                        btn.disabled = false;
                    });
                
                // Auto-advance: Focus scanner for next reading after brief delay
                setTimeout(() => {
                    this.focusScannerForNext();
                }, 800);
                } else {
                    // If save failed, restore all button states
                    submitBtns.forEach((btn, index) => {
                        btn.innerHTML = originalTexts[index];
                        btn.disabled = false;
                    });
                }
                
                return;
            }
            
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
                // PHASE 7 FIX: Mobile-First Success Toast - Prominent notification for mobile users
                // Following mobile UX best practices for scanning apps
                this.showSuccessToast(
                    'Reading Saved Successfully!',
                    'Ready for next meter scan • Data saved to system'
                );
                
                // Reset form and hide
                event.target.reset();
                const formCard = document.getElementById('reading-form-card');
                formCard.classList.add('scanner-hidden');
                formCard.classList.remove('scanner-visible');
                
                // Auto-advance: Focus scanner for next reading after brief delay
                setTimeout(() => {
                    this.focusScannerForNext();
                }, 800); // Slightly longer delay so user sees toast
                
                // Refresh recent readings table after a short delay with animation
                setTimeout(async () => {
                    await this.loadRecentReadings(true); // Pass true to trigger top row animation
                }, 500);
                
            } else {
                throw new Error(result.message || 'Unknown error occurred');
            }
            
        } catch (error) {
            console.error('Error saving reading:', error);
            
            // Check if it's a network error and we're offline
            if (!this.isOnline || error.name === 'TypeError' || error.message.includes('fetch')) {
                // Store offline and show success
                this.storeOfflineReading(readingData);
                
                this.showSuccessToast(
                    'Reading Saved Offline!',
                    'Will sync when connection is restored'
                );
                
                // Reset form and hide
                event.target.reset();
                const formCard = document.getElementById('reading-form-card');
                formCard.classList.add('scanner-hidden');
                formCard.classList.remove('scanner-visible');
                
                // Auto-advance: Focus scanner for next reading after brief delay
                setTimeout(() => {
                    this.focusScannerForNext();
                }, 800);
                
            } else {
                // PHASE 11: Check if it's a duplicate reading error for inline validation
                if (error.message && error.message.includes('Duplicate reading detected')) {
                    // Show inline validation error for duplicate readings
                    this.showInlineValidationError('current-meter-reading', error.message);
            } else {
                // Show error message with SweetAlert for other errors
            Swal.fire({
                icon: 'error',
                title: 'Error Saving Reading',
                text: error.message || 'An error occurred while saving the reading',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
                }
            }
            
        } finally {
            // Reset submitting state
            this.isSubmitting = false;
            
            // PHASE 11: Restore ALL button states (mobile and desktop)
            const allSubmitBtns = event.target.querySelectorAll('button[type="submit"]');
            allSubmitBtns.forEach((btn, index) => {
                btn.innerHTML = originalTexts[index] || '<i class="bi bi-check-circle me-2"></i>Submit Reading';
                btn.disabled = false;
            });
        }
    }
    
    // Cancel reading form and reset scanner state
    cancelReadingForm() {
        // Reset form
        const form = document.getElementById('reading-form');
        if (form) {
            form.reset();
        }
        
        // Hide the reading form card
        const formCard = document.getElementById('reading-form-card');
        if (formCard) {
            formCard.classList.add('scanner-hidden');
            formCard.classList.remove('scanner-visible');
        }
        
        // Clear current reading data
        this.currentReading = null;
        
        // Show scanner controls again
        this.updateScannerUI();
        
        // Show status message
        this.showStatus('Reading form cancelled. Ready to scan again.', 'info');
        
        // Scroll back to scanner
        const scannerCard = document.querySelector('.card:has(#qr-reader)');
        if (scannerCard) {
            scannerCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
    
    // Populate recent readings table with visual feedback
    populateRecentReadingsTable(readings, isNewReading = false) {
        const tbody = document.getElementById('readings-table-body');
        
        if (readings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent readings</td></tr>';
            return;
        }
        
        tbody.innerHTML = readings.map((reading, index) => {
            // Apply animation class only to the top row if this is a new reading
            const rowClass = (isNewReading && index === 0) ? 'new-reading-row' : '';
            
            return `
                <tr class="${rowClass}">
                    <td>${reading.real_property_name}</td>
                    <td>${reading.unit_no}</td>
                    <td>${reading.tenant_name}</td>
                    <td>${reading.current_reading}</td>
                    <td>${new Date(reading.reading_date).toLocaleDateString()}</td>
                    <td>
                        <span class="badge bg-success">Saved</span>
                    </td>
                </tr>
            `;
        }).join('');

        // PHASE 7 ENHANCEMENT: Highlight only the top row after successful save
        if (isNewReading) {
            const firstRow = tbody.querySelector('tr.new-reading-row');
            if (firstRow) {
                // After the slide-in animation, start the fade-out highlight
                setTimeout(() => {
                    firstRow.classList.add('fade-highlight');
                    
                    // Remove all animation classes after fade-out completes
                    setTimeout(() => {
                        firstRow.classList.remove('new-reading-row', 'fade-highlight');
                    }, 3000);
                }, 800); // Wait for slide-in animation to complete
            }
        }
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

    // Phase 9: Enhanced offline storage with validation
    async storeOfflineReading(readingData) {
        try {
            // PHASE 11: Enhance reading data with tenant and property information
            const enhancedData = {
                ...readingData,
                tenantName: this.currentTenantData?.tenantName || 'N/A',
                propertyName: this.currentTenantData?.realPropertyName || readingData.propertyCode,
                timestamp: new Date().toISOString()
            };
            
            // Phase 9: Validate data before storing offline
            const validationResult = this.dataValidationPipeline.validateOfflineReading(enhancedData);
            
            if (!validationResult.valid) {
                console.error('Offline reading validation failed:', validationResult.errors);
                this.showStatus('Data validation failed - reading not saved offline', 'danger');
                return false;
            }
            
            // PHASE 11: Allow UI to update before heavy operations
            await new Promise(resolve => setTimeout(resolve, 50));
            
            // Phase 9: Add validation metadata
            const enhancedReadingData = this.enhancedOfflineStorage.addValidationMetadata(enhancedData, validationResult);
            
            // Store in offline queue
            this.offlineQueue.push(enhancedReadingData);
            localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
            
            console.log('Reading stored offline with validation metadata:', enhancedReadingData);
            
            // PHASE 11: Allow UI to update
            await new Promise(resolve => setTimeout(resolve, 50));
            
            // Update UI to show offline status
            this.updateOfflineStatus();
            this.updateOfflineIndicator();
            
            // PHASE 11: Refresh Recent QR Readings table to show offline reading
            await this.loadRecentReadings(true);
            
            return true;
        } catch (error) {
            console.error('Error storing offline reading:', error);
            this.showStatus('Error storing reading offline', 'danger');
            return false;
        }
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

    // Phase 9: Initialize comprehensive cache on page load
    // Project specs: Always update localStorage with recent data upon page load
    async initializeComprehensiveCache() {
        try {
            console.log('Initializing comprehensive cache...');
            
            // Always try to refresh cache with recent data on page load
            if (this.isOnline) {
                try {
                    await this.refreshComprehensiveCache();
                    console.log('Cache refreshed with recent data on page load');
                    return;
                } catch (error) {
                    console.warn('Failed to refresh cache on page load, using existing cache:', error);
                }
            }
            
            // Fallback: Use existing cache if refresh failed or offline
            const cachedData = localStorage.getItem('qr_comprehensive_cache');
                if (cachedData) {
                    const parsedCache = JSON.parse(cachedData);
                    this.comprehensiveCache = parsedCache;
                // Phase 9: Initialize tenant resolution service with existing cache
                    this.tenantResolutionService = new TenantResolutionService(this.comprehensiveCache);
                console.log('Using existing cache as fallback');
            } else {
                console.warn('No cache available and cannot refresh');
            }
        } catch (error) {
            console.error('Error initializing comprehensive cache:', error);
        }
    }

    // Phase 9: Refresh comprehensive cache using vw_LatestTenantReadings
    async refreshComprehensiveCache() {
        try {
            console.log('Refreshing comprehensive cache...');
            
            const response = await fetch('api/get-latest-tenant-readings.php');
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to load latest tenant readings');
            }
            
            const updatedCache = {
                latestReadings: data.data || [],
                cachedAt: new Date().toISOString(),
                expiresAt: new Date(Date.now() + 90 * 24 * 60 * 60 * 1000).toISOString(), // 90 days
                source: 'page_load_refresh'
            };
            
            this.comprehensiveCache = updatedCache;
            localStorage.setItem('qr_comprehensive_cache', JSON.stringify(updatedCache));
            
            // Phase 9: Initialize tenant resolution service with comprehensive cache
            this.tenantResolutionService = new TenantResolutionService(this.comprehensiveCache);
            
            console.log(`Cache refreshed with ${updatedCache.latestReadings.length} tenant readings`);
            return updatedCache;
        } catch (error) {
            console.error('Error refreshing comprehensive cache:', error);
            throw error;
        }
    }

    // Phase 9: Check if cache is valid
    isCacheValid(cache) {
        if (!cache || !cache.expiresAt) {
            return false;
        }
        
        const now = new Date();
        const expiresAt = new Date(cache.expiresAt);
        return now < expiresAt;
    }

    // Phase 9: Get previous reading data from cache or network
    async getPreviousReadingData(propertyCode, unitNo) {
        try {
            // First check comprehensive cache
            if (this.comprehensiveCache && this.comprehensiveCache.latestReadings) {
                // Normalize property code and unit number for comparison
                const normalized = this.normalizePropertyAndUnit(propertyCode, unitNo);
                
                const cachedReading = this.comprehensiveCache.latestReadings.find(reading => 
                    this.normalizePropertyCode(reading.property_code) === normalized.propertyCode && 
                    this.normalizeUnitNo(reading.unit_no) === normalized.unitNo
                );
                
                if (cachedReading) {
                    console.log('Previous reading found in cache');
                    return {
                        success: true,
                        data: {
                            prevReading: cachedReading.prev_reading,
                            currentReading: cachedReading.current_reading,
                            usage: cachedReading.usage,
                            readingDate: cachedReading.reading_date,
                            dateFrom: cachedReading.reading_date_from,
                            dateTo: cachedReading.reading_date_to,
                            billingDateFrom: cachedReading.billing_from,
                            billingDateTo: cachedReading.billing_to
                        },
                        source: 'cache'
                    };
                } else {
                    console.log('Previous reading not found in cache for:', propertyCode, unitNo);
                    console.log('Looking for normalized:', normalized.propertyCode, normalized.unitNo);
                }
            }
            
            // Fallback to network if online
            if (this.isOnline) {
                const response = await fetch(`api/get-last-reading.php?propertyCode=${encodeURIComponent(propertyCode)}&unitNo=${encodeURIComponent(unitNo)}`);
                const data = await response.json();
                
                if (data.success) {
                    console.log('Previous reading fetched from network');
                    return data;
                }
            }
            
            // No data found
            return {
                success: false,
                message: 'No previous reading found'
            };
        } catch (error) {
            console.error('Error getting previous reading data:', error);
            return {
                success: false,
                message: error.message
            };
        }
    }

    async syncOfflineReadings(isManual = false) {
        if (this.offlineQueue.length === 0) {
            console.log('No offline readings to sync');
            return;
        }
        
        console.log(`Starting ${isManual ? 'manual' : 'auto'} sync of ${this.offlineQueue.length} offline readings`);
        const readingsToSync = [...this.offlineQueue];
        const totalReadings = readingsToSync.length;
        let syncedCount = 0;
        let failedCount = 0;
        
        // Show sync progress indicator
        this.showSyncProgress(totalReadings, 0, 0, isManual);
        
        for (let i = 0; i < readingsToSync.length; i++) {
            const reading = readingsToSync[i];
            
            try {
                // Check connection before each sync attempt
                if (!this.isOnline) {
                    console.log('Connection lost during sync, stopping sync process');
                    this.hideSyncProgress();
                    break;
                }
                
                // Update progress indicator
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
                console.log(`Syncing reading: ${reading.propertyCode}-${reading.unitNo}`);
                const success = await this.submitReadingOnline(reading);
                
                if (success) {
                    // Remove from offline queue only after confirmed success
                    this.offlineQueue = this.offlineQueue.filter(r => r.syncId !== reading.syncId);
                    localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
                    syncedCount++;
                    console.log(`Successfully synced reading: ${reading.propertyCode}-${reading.unitNo}`);
                } else {
                    failedCount++;
                    console.log(`Failed to sync reading: ${reading.propertyCode}-${reading.unitNo}`);
                }
                
                // Update progress after each reading
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
            } catch (error) {
                console.error(`Error syncing reading ${reading.propertyCode}-${reading.unitNo}:`, error);
                failedCount++;
                
                // If it's a network error, stop syncing to prevent data loss
                if (error.name === 'TypeError' || error.message.includes('fetch')) {
                    console.log('Network error detected, stopping sync to prevent data loss');
                    this.hideSyncProgress();
                    break;
                }
                
                // Update progress even on error
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
            }
        }
        
        // Hide progress indicator
        this.hideSyncProgress();
        
        // Update UI with sync results
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        
        // PHASE 11: Refresh Recent QR Readings table after sync completion
        if (syncedCount > 0) {
            this.showStatus(`${syncedCount} reading(s) synced successfully`, 'success');
            // Refresh the Recent QR Readings table to show updated status badges
            await this.loadRecentReadings(false);
        }
        
        if (failedCount > 0) {
            this.showStatus(`${failedCount} reading(s) failed to sync, will retry later`, 'warning');
        }
        
        console.log(`Sync completed: ${syncedCount} synced, ${failedCount} failed, ${this.offlineQueue.length} remaining`);
    }

    async syncOfflineReadingsWithDelay(isManual = false) {
        if (this.offlineQueue.length === 0) {
            console.log('No offline readings to sync');
            return;
        }
        
        console.log(`Starting ${isManual ? 'manual' : 'auto'} sync with delay of ${this.offlineQueue.length} offline readings`);
        const readingsToSync = [...this.offlineQueue];
        const totalReadings = readingsToSync.length;
        let syncedCount = 0;
        let failedCount = 0;
        
        // Show sync progress indicator
        this.showSyncProgress(totalReadings, 0, 0, isManual);
        
        for (let i = 0; i < readingsToSync.length; i++) {
            const reading = readingsToSync[i];
            
            try {
                // Check connection before each sync attempt
                if (!this.isOnline) {
                    console.log('Connection lost during sync, stopping sync process');
                    this.hideSyncProgress();
                    break;
                }
                
                // Update progress indicator
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
                console.log(`Syncing reading: ${reading.propertyCode}-${reading.unitNo}`);
                
                // Add delay for screenshot purposes (2 seconds per reading)
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                const success = await this.submitReadingOnline(reading);
                
                if (success) {
                    // Remove from offline queue only after confirmed success
                    this.offlineQueue = this.offlineQueue.filter(r => r.syncId !== reading.syncId);
                    localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
                    syncedCount++;
                    console.log(`Successfully synced reading: ${reading.propertyCode}-${reading.unitNo}`);
                } else {
                    failedCount++;
                    console.log(`Failed to sync reading: ${reading.propertyCode}-${reading.unitNo}`);
                }
                
                // Update progress after each reading
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
            } catch (error) {
                console.error(`Error syncing reading ${reading.propertyCode}-${reading.unitNo}:`, error);
                failedCount++;
                
                // If it's a network error, stop syncing to prevent data loss
                if (error.name === 'TypeError' || error.message.includes('fetch')) {
                    console.log('Network error detected, stopping sync to prevent data loss');
                    this.hideSyncProgress();
                    break;
                }
                
                // Update progress even on error
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
            }
        }
        
        // Hide progress indicator
        this.hideSyncProgress();
        
        // Update UI with sync results
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        
        if (syncedCount > 0) {
            this.showStatus(`${syncedCount} reading(s) synced successfully`, 'success');
        }
        
        if (failedCount > 0) {
            this.showStatus(`${failedCount} reading(s) failed to sync, will retry later`, 'warning');
        }
        
        console.log(`Sync with delay completed: ${syncedCount} synced, ${failedCount} failed, ${this.offlineQueue.length} remaining`);
    }

    // Sync progress indicator methods
    showSyncProgress(totalReadings, currentReading, syncedCount, isManual = false) {
        // Remove existing progress indicator if any
        this.hideSyncProgress();
        
        const syncType = isManual ? "Manual sync in progress" : "Auto sync in progress";
        
        const progressIndicator = document.createElement('div');
        progressIndicator.id = 'sync-progress-indicator';
        progressIndicator.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 10000;
            font-family: Arial, sans-serif;
            font-size: 14px;
            min-width: 300px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        `;
        
        progressIndicator.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <div class="spinner" style="width: 20px; height: 20px; border: 2px solid #333; border-top: 2px solid #4caf50; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 10px;"></div>
                <span style="font-weight: bold;">${syncType}</span>
            </div>
            <div style="margin-bottom: 8px;">
                <span id="sync-progress-text">Processing reading 1 of ${totalReadings} offline readings</span>
            </div>
            <div style="background: #333; border-radius: 4px; height: 8px; margin-bottom: 8px;">
                <div id="sync-progress-bar" style="background: linear-gradient(90deg, #4caf50, #8bc34a); height: 100%; border-radius: 4px; width: 0%; transition: width 0.3s ease;"></div>
            </div>
            <div style="font-size: 12px; color: #ccc;">
                <span id="sync-results">Synced: 0 | Failed: 0</span>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        document.body.appendChild(progressIndicator);
    }
    
    updateSyncProgress(totalReadings, currentReading, syncedCount, failedCount) {
        const progressText = document.getElementById('sync-progress-text');
        const progressBar = document.getElementById('sync-progress-bar');
        const results = document.getElementById('sync-results');
        
        if (progressText) {
            progressText.textContent = `Processing reading ${currentReading} of ${totalReadings} offline readings`;
        }
        
        if (progressBar) {
            const percentage = (currentReading / totalReadings) * 100;
            progressBar.style.width = `${percentage}%`;
        }
        
        if (results) {
            results.textContent = `Synced: ${syncedCount} | Failed: ${failedCount}`;
        }
    }
    
    hideSyncProgress() {
        const progressIndicator = document.getElementById('sync-progress-indicator');
        if (progressIndicator) {
            progressIndicator.remove();
        }
    }

    // Offline notification methods
    isFormActive() {
        // Check if user is actively entering data (form has focus or recent activity)
        const currentReadingInput = document.getElementById('currentReading');
        const remarksInput = document.getElementById('remarks');
        
        // Check if any form field has focus
        const activeElement = document.activeElement;
        if (activeElement && (activeElement === currentReadingInput || activeElement === remarksInput)) {
            return true;
        }
        
        // Check if form has been recently interacted with (within last 30 seconds)
        const lastInteraction = this.lastFormInteraction || 0;
        const now = Date.now();
        return (now - lastInteraction) < 30000; // 30 seconds
    }

    showOfflineNotification() {
        // Remove existing notification if any
        this.hideOfflineNotification();
        
        const notification = document.createElement('div');
        notification.id = 'offline-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3);
            z-index: 10000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
            max-width: 400px;
            animation: slideDown 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-wifi-off" style="font-size: 16px;"></i>
                    <span style="font-weight: 600;">Connection Lost</span>
                </div>
                <div style="font-size: 13px; opacity: 0.9;">
                    Reading will be saved offline
                </div>
            </div>
            <style>
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
            </style>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            this.hideOfflineNotification();
        }, 5000);
    }

    hideOfflineNotification() {
        const notification = document.getElementById('offline-notification');
        if (notification) {
            notification.remove();
        }
    }

    showOnlineNotification() {
        // Remove existing notifications if any
        this.hideOfflineNotification();
        this.hideOnlineNotification();
        
        const notification = document.createElement('div');
        notification.id = 'online-notification';
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
            animation: slideDown 0.3s ease-out;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="bi bi-wifi" style="font-size: 16px;"></i>
                <span>Connection Restored</span>
            </div>
            <style>
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
            </style>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            this.hideOnlineNotification();
        }, 3000);
    }

    hideOnlineNotification() {
        const notification = document.getElementById('online-notification');
        if (notification) {
            notification.remove();
        }
    }

    updateOfflineStatus() {
        if (this.offlineQueue.length > 0) {
            this.showStatus(`${this.offlineQueue.length} reading(s) saved offline`, 'info');
        }
    }

    // PHASE 8: Offline Detection System with Intermittent Connection Handling
    setupOfflineDetection() {
        // Create offline indicator element
        this.createOfflineIndicator();
        
        // Enhanced online/offline event handling with connection stability check
        window.addEventListener('online', async () => {
            console.log('Browser online event detected');
            this.isOnline = true;
            this.updateOfflineIndicator();
            
            // Hide offline notification if showing
            this.hideOfflineNotification();
            
            // Phase 9: Refresh cache first when connection is restored
            try {
                await this.refreshComprehensiveCache();
                console.log('Cache refreshed on connection restore');
            } catch (error) {
                console.error('Cache refresh failed on connection restore:', error);
            }
            
            // Show online notification if we were previously offline
            if (this.wasOffline) {
                this.showOnlineNotification();
                this.wasOffline = false;
            }
            
            // Wait for connection stability before auto-sync
            this.waitForStableConnection().then(() => {
                console.log('Connection stable, starting auto-sync');
                this.syncOfflineReadings();
            }).catch(() => {
                console.log('Connection unstable, skipping auto-sync');
            });
        });
        
        window.addEventListener('offline', () => {
            console.log('Browser offline event detected');
            this.isOnline = false;
            this.wasOffline = true; // Track that we went offline
            this.updateOfflineIndicator();
            
            // Show offline notification when connection is lost
            this.showOfflineNotification();
        });
        
        // Initial update
        this.updateOfflineIndicator();
        
        // Add mobile touch event handling for accessibility
        this.setupMobileTouchEvents();
        
        // Initial update to show current status
        this.updateOfflineIndicator();
    }

    // Wait for stable connection before auto-sync
    async waitForStableConnection() {
        const STABILITY_CHECK_DURATION = 3000; // 3 seconds
        const PING_INTERVAL = 500; // Check every 500ms
        const REQUIRED_SUCCESS_COUNT = 3; // Need 3 successful pings
        
        return new Promise((resolve, reject) => {
            let successCount = 0;
            let checkCount = 0;
            const maxChecks = STABILITY_CHECK_DURATION / PING_INTERVAL;
            
            const checkConnection = async () => {
                checkCount++;
                
                try {
                    // Test connection with a lightweight request
                    const response = await fetch('api/ping.php', {
                        method: 'HEAD',
                        cache: 'no-cache',
                        timeout: 2000
                    });
                    
                    if (response.ok) {
                        successCount++;
                        console.log(`Connection check ${checkCount}: Success (${successCount}/${REQUIRED_SUCCESS_COUNT})`);
                        
                        if (successCount >= REQUIRED_SUCCESS_COUNT) {
                            console.log('Connection stable - proceeding with sync');
                            resolve();
                            return;
                        }
                    } else {
                        console.log(`Connection check ${checkCount}: Failed (${response.status})`);
                        successCount = 0; // Reset on failure
                    }
                } catch (error) {
                    console.log(`Connection check ${checkCount}: Error (${error.message})`);
                    successCount = 0; // Reset on error
                }
                
                if (checkCount >= maxChecks) {
                    console.log('Connection stability check timeout');
                    reject(new Error('Connection not stable enough for auto-sync'));
                    return;
                }
                
                // Continue checking
                setTimeout(checkConnection, PING_INTERVAL);
            };
            
            // Start checking after a brief delay
            setTimeout(checkConnection, 1000);
        });
    }

    createOfflineIndicator() {
        // Find the navigation container
        const navContainer = document.querySelector('.navbar .container-fluid');
        if (!navContainer) {
            console.error('Navigation container not found');
            return;
        }
        
        // Find the navigation items container (where user dropdown is located)
        const navItems = navContainer.querySelector('.d-flex.align-items-center.ms-auto');
        if (!navItems) {
            console.error('Navigation items container not found');
            console.log('Available elements in navContainer:', navContainer.children);
            return;
        }
        
        console.log('Found nav items container:', navItems);
        
        // Create offline indicator element
        const offlineIndicator = document.createElement('div');
        offlineIndicator.className = 'offline-indicator';
        offlineIndicator.id = 'offline-indicator';
        
        // Insert at the beginning of nav items (before Tools dropdown)
        navItems.insertBefore(offlineIndicator, navItems.firstChild);
        console.log('Offline indicator created and inserted at beginning (before Tools)');
        
        this.offlineIndicator = offlineIndicator;
    }

    updateOfflineIndicator() {
        if (!this.offlineIndicator) {
            console.log('Offline indicator not found');
            return;
        }
        
        const pendingCount = this.offlineQueue.length;
        console.log('Updating offline indicator:', { isOnline: this.isOnline, pendingCount });
        
        // Update user avatar status badge
        this.updateUserAvatarStatus();
        
        if (!this.isOnline) {
            // Show offline status with clear tooltip
            this.offlineIndicator.innerHTML = `
                <div class="offline-status offline" data-tooltip="You are offline. Readings will be saved locally and synced when connection is restored.">
                    <i class="bi bi-wifi-off me-1"></i>
                    <span class="d-none d-sm-inline">Offline</span>
                </div>
            `;
            this.offlineIndicator.classList.remove('hidden');
            console.log('Showing offline status');
        } else if (pendingCount > 0) {
            // Show pending sync status with clear tooltip
            this.offlineIndicator.innerHTML = `
                <div class="offline-status pending" data-tooltip="${pendingCount} reading(s) saved offline. Click sync to upload to server.">
                    <i class="bi bi-cloud-upload me-1"></i>
                    <span class="d-none d-sm-inline">Sync</span>
                    <span class="badge bg-info ms-1" title="${pendingCount} reading(s) pending sync">${pendingCount}</span>
                    <button class="btn btn-sm ms-2 sync-btn" onclick="qrMeterApp.manualSync()" title="Sync offline readings">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            `;
            this.offlineIndicator.classList.remove('hidden');
            console.log('Showing pending sync status');
        } else {
            // Hide offline indicator when online (status shown in avatar)
            this.offlineIndicator.classList.add('hidden');
            console.log('Hiding offline indicator - status shown in avatar');
        }
    }

    updateUserAvatarStatus() {
        const userAvatar = document.querySelector('.user-avatar');
        if (!userAvatar) {
            console.log('User avatar not found');
            return;
        }
        
        // Remove existing status badge
        const existingBadge = userAvatar.querySelector('.status-badge');
        if (existingBadge) {
            existingBadge.remove();
        }
        
        const pendingCount = this.offlineQueue.length;
        let statusClass = '';
        let statusTitle = '';
        
        if (!this.isOnline) {
            statusClass = 'offline';
            statusTitle = 'You are offline - Readings saved locally';
        } else if (pendingCount > 0) {
            statusClass = 'pending';
            statusTitle = `${pendingCount} reading(s) pending sync - Orange dot indicates sync needed`;
        } else {
            statusClass = 'online';
            statusTitle = 'You are online - All readings saved to server';
        }
        
        // Create status badge
        const statusBadge = document.createElement('div');
        statusBadge.className = `status-badge ${statusClass}`;
        statusBadge.title = statusTitle;
        
        userAvatar.appendChild(statusBadge);
        console.log('Updated user avatar status:', statusClass);
    }

    async manualSync() {
        if (this.offlineQueue.length === 0) return;
        
        // Show sync in progress
        const syncBtn = this.offlineIndicator.querySelector('.sync-btn');
        if (syncBtn) {
            syncBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            syncBtn.disabled = true;
        }
        
        try {
            // Use slow sync only in testing mode, fast sync in production
            if (this.appConfig && this.appConfig.isTesting) {
                await this.syncOfflineReadingsWithDelay(true); // Slow sync for screenshots
            } else {
                await this.syncOfflineReadings(true); // Fast sync for production
            }
            this.showStatus('Offline readings synced successfully', 'success');
        } catch (error) {
            console.error('Manual sync failed:', error);
            this.showStatus('Sync failed. Will retry automatically.', 'warning');
        } finally {
            // Restore sync button
            if (syncBtn) {
                syncBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                syncBtn.disabled = false;
            }
        }
    }

    setupMobileTouchEvents() {
        // Add touch event listeners for mobile accessibility
        document.addEventListener('touchstart', (e) => {
            const offlineStatus = e.target.closest('.offline-status');
            if (offlineStatus) {
                // Add visual feedback for touch
                offlineStatus.style.transform = 'scale(0.98)';
            }
        });
        
        document.addEventListener('touchend', (e) => {
            const offlineStatus = e.target.closest('.offline-status');
            if (offlineStatus) {
                // Remove visual feedback
                offlineStatus.style.transform = '';
                
                // Show detailed information on mobile tap
                this.showOfflineInfo(offlineStatus);
            }
        });
    }

    showOfflineInfo(offlineStatus) {
        const pendingCount = this.offlineQueue.length;
        let message = '';
        
        if (!this.isOnline) {
            message = `You are currently offline. ${pendingCount > 0 ? `${pendingCount} reading(s) have been saved locally and will be synced when your connection is restored.` : 'Your readings will be saved locally until you are back online.'}`;
        } else if (pendingCount > 0) {
            message = `You have ${pendingCount} reading(s) saved offline. Tap the sync button to upload them to the server.`;
        }
        
        if (message) {
            // Use SweetAlert for mobile information display (appropriate use case)
            Swal.fire({
                icon: 'info',
                title: 'Offline Status',
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#1d4ed8',
                showCloseButton: true
            });
        }
    }

    getOfflineReadings() {
        try {
            const offlineData = localStorage.getItem('qr_meter_readings_offline');
            if (offlineData) {
                const offlineReadings = JSON.parse(offlineData);
                console.log('Raw offline data from localStorage:', offlineReadings);
                const mapped = offlineReadings.map(reading => {
                    const mappedReading = {
                        ...reading,
                        isOffline: true,
                        readingDate: reading.timestamp,
                        // Ensure tenant and property data is preserved
                        tenantName: reading.tenantName,
                        propertyName: reading.propertyName
                    };
                    console.log('Mapped offline reading:', mappedReading);
                    return mappedReading;
                });
                return mapped;
            }
        } catch (error) {
            console.error('Error retrieving offline readings:', error);
        }
        return [];
    }

    // PHASE 11: Check for duplicate reading upon QR scan
    async checkDuplicateReading(propertyCode, unitNo) {
        console.log('=== Checking for duplicate reading upon QR scan ===');
        console.log('Property Code:', propertyCode);
        console.log('Unit No:', unitNo);
        
        if (!this.tenantResolutionService) {
            console.warn('Tenant resolution service not available');
            return { isDuplicate: false };
        }
        
        // Check offline readings first (primary data source in offline-first)
        const offlineReadings = this.tenantResolutionService.getOfflineReadings();
        console.log('Offline readings count:', offlineReadings.length);
        
        const offlineDuplicate = offlineReadings.find(reading => 
            reading.propertyCode === propertyCode && 
            reading.unitNo === unitNo
        );
        
        if (offlineDuplicate) {
            const duplicateDate = new Date(offlineDuplicate.timestamp).toLocaleDateString();
            const duplicateValue = offlineDuplicate.currentReading;
            console.log('Offline duplicate found');
            return { 
                isDuplicate: true, 
                details: {
                    propertyCode,
                    unitNo,
                    readingPeriod: 'Saved Offline',
                    duplicateDate,
                    duplicateValue
                }
            };
        }
        
        // Check comprehensive cache for recent readings in the same reading period
        if (this.comprehensiveCache && this.comprehensiveCache.latestReadings) {
            console.log('Cache readings count:', this.comprehensiveCache.latestReadings.length);
            
            const normalized = this.tenantResolutionService.normalizePropertyAndUnit(propertyCode, unitNo);
            
            // Get current month range for reading period check
            const now = new Date();
            const currentMonthStart = new Date(now.getFullYear(), now.getMonth(), 1);
            const currentMonthEnd = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            console.log('Current month range:', currentMonthStart, 'to', currentMonthEnd);
            
            const cacheDuplicate = this.comprehensiveCache.latestReadings.find(reading => {
                const normalizedProp = this.tenantResolutionService.normalizePropertyCode(reading.property_code);
                const normalizedUnit = this.tenantResolutionService.normalizeUnitNo(reading.unit_no);
                const propertyMatches = normalizedProp === normalized.propertyCode && normalizedUnit === normalized.unitNo;
                
                if (propertyMatches) {
                    const readingDateFrom = new Date(reading.reading_date_from || reading.date_from);
                    const readingDateTo = new Date(reading.reading_date_to || reading.date_to);
                    const inSamePeriod = readingDateFrom >= currentMonthStart && readingDateTo <= currentMonthEnd;
                    
                    console.log('Property match found:', reading);
                    console.log('Reading period:', readingDateFrom, 'to', readingDateTo);
                    console.log('In same reading period:', inSamePeriod);
                    
                    return inSamePeriod;
                }
                return false;
            });
            
            if (cacheDuplicate) {
                const duplicateDate = new Date(cacheDuplicate.reading_date).toLocaleDateString();
                const duplicateValue = cacheDuplicate.current_reading;
                const dateFrom = cacheDuplicate.reading_date_from || cacheDuplicate.date_from;
                const dateTo = cacheDuplicate.reading_date_to || cacheDuplicate.date_to;
                const readingPeriod = new Date(dateFrom).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}) + ' - ' + new Date(dateTo).toLocaleDateString('en-US', {month: 'short', day: 'numeric'});
                console.log('Cache duplicate found');
                return { 
                    isDuplicate: true, 
                    details: {
                        propertyCode,
                        unitNo,
                        readingPeriod,
                        duplicateDate,
                        duplicateValue
                    }
                };
            }
        }
        
        console.log('No duplicates found');
        return { isDuplicate: false };
    }

    // PHASE 11: Show duplicate reading notification (following UX design standards)
    showDuplicateNotification(message, details) {
        // Remove existing notification if any
        this.hideDuplicateNotification();
        
        const notification = document.createElement('div');
        notification.id = 'duplicate-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            padding: 20px 28px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(255, 152, 0, 0.4);
            z-index: 10000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            text-align: center;
            max-width: 90%;
            width: 400px;
            animation: slideDown 0.3s ease-out;
            border: 2px solid rgba(255, 255, 255, 0.2);
        `;
        
        notification.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px;"></i>
                    <span style="font-weight: 600; font-size: 16px;">Already Scanned</span>
                </div>
                <div style="font-size: 15px; font-weight: 500; margin-top: 4px; line-height: 1.4;">
                    ${details.propertyCode} - Unit ${details.unitNo.trim()}
                </div>
                <div style="font-size: 14px; opacity: 0.95; margin-top: 2px; line-height: 1.5;">
                    This meter was already read on ${details.duplicateDate}
                </div>
                <div style="font-size: 13px; opacity: 0.9; margin-top: 4px; padding: 8px 12px; background: rgba(255, 255, 255, 0.15); border-radius: 6px;">
                    Last Reading: ${details.duplicateValue}
                </div>
            </div>
            <style>
                @keyframes slideDown {
                    from { transform: translateX(-50%) translateY(-20px); opacity: 0; }
                    to { transform: translateX(-50%) translateY(0); opacity: 1; }
                }
            </style>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-hide after 6 seconds
        setTimeout(() => {
            this.hideDuplicateNotification();
        }, 6000);
    }

    hideDuplicateNotification() {
        const notification = document.getElementById('duplicate-notification');
        if (notification) {
            notification.remove();
        }
    }

    // PHASE 11: Show inline validation error for duplicate readings
    showInlineValidationError(fieldId, errorMessage) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        // Remove existing error styling and message
        this.clearInlineValidationError(fieldId);
        
        // Add error styling to the field
        field.classList.add('is-invalid');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.id = `${fieldId}-error`;
        errorDiv.textContent = errorMessage;
        
        // Insert error message after the field
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
        
        // Focus the field for user attention
        field.focus();
        
        // Auto-clear error after 10 seconds
        setTimeout(() => {
            this.clearInlineValidationError(fieldId);
        }, 10000);
    }

    // PHASE 11: Clear inline validation error
    clearInlineValidationError(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.remove('is-invalid');
        }
        
        const errorDiv = document.getElementById(`${fieldId}-error`);
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // PHASE 11: Get recent readings for duplicate validation
    async getRecentReadingsForValidation() {
        try {
            const response = await fetch('api/get-recent-readings.php');
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    return result.data || [];
                }
            }
        } catch (error) {
            console.error('Error fetching recent readings for validation:', error);
        }
        return [];
    }

    async loadRecentReadings(isNewReading = false) {
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
                    this.displayRecentReadings(result.data, isNewReading);
                }
            } else {
                console.error('Failed to load recent readings:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error loading recent readings:', error);
        }
    }

    displayRecentReadings(readings, isNewReading = false) {
        console.log('Displaying recent readings:', readings);
        const tbody = document.getElementById('readings-table-body');
        
        // Get offline readings from localStorage
        const offlineReadings = this.getOfflineReadings();
        
        // Combine online and offline readings
        const allReadings = [...readings, ...offlineReadings];
        
        if (!allReadings || allReadings.length === 0) {
            console.log('No readings to display');
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent readings</td></tr>';
            return;
        }

        // Sort by date (most recent first)
        allReadings.sort((a, b) => {
            const dateA = new Date(a.readingDate || a.timestamp);
            const dateB = new Date(b.readingDate || b.timestamp);
            return dateB - dateA;
        });

        tbody.innerHTML = allReadings.map((reading, index) => {
            // Apply animation class only to the top row if this is a new reading
            const rowClass = (isNewReading && index === 0) ? 'new-reading-row' : '';
            
            // Determine if this is an offline reading
            const isOffline = reading.isOffline || false;
            const statusBadge = isOffline ? 
                '<span class="badge bg-warning">Saved Offline</span>' : 
                '<span class="badge bg-success">Synced</span>';
            
            // Debug logging for each reading
            if (isOffline) {
                console.log('Rendering offline reading:', {
                    propertyName: reading.propertyName,
                    unitNo: reading.unitNo,
                    tenantName: reading.tenantName,
                    currentReading: reading.currentReading,
                    timestamp: reading.timestamp
                });
            }
            
            return `
                <tr class="${rowClass}">
                    <td>${reading.propertyName || reading.propertyId || reading.propertyCode || 'N/A'}</td>
                    <td>${reading.unitNumber || reading.unitNo || 'N/A'}</td>
                    <td>${reading.tenantName || 'N/A'}</td>
                    <td>${reading.meterReading || reading.currentReading ? (reading.meterReading || reading.currentReading).toLocaleString() : 'N/A'}</td>
                    <td>${this.formatDate(reading.readingDate || reading.timestamp)}</td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        }).join('');

        // PHASE 7 ENHANCEMENT: Highlight only the top row after successful save
        if (isNewReading) {
            const firstRow = tbody.querySelector('tr.new-reading-row');
            if (firstRow) {
                console.log('Applying new reading animation to top row');
                
                // After the slide-in animation, start the fade-out highlight
                setTimeout(() => {
                    firstRow.classList.add('fade-highlight');
                    
                    // Remove all animation classes after fade-out completes
                    setTimeout(() => {
                        firstRow.classList.remove('new-reading-row', 'fade-highlight');
                    }, 3000);
                }, 800); // Wait for slide-in animation to complete
            }
        }
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
        
        // Use 5 seconds for all status messages (not success toasts)
        setTimeout(() => {
            statusDiv.classList.add('scanner-hidden');
            statusDiv.classList.remove('scanner-visible');
        }, 5000);
    }

    // PHASE 7 FIX: Mobile-First Success Toast for prominent mobile feedback
    showSuccessToast(title, subtitle = '') {
        // Remove any existing toast
        const existingToast = document.querySelector('.success-toast');
        if (existingToast) {
            existingToast.remove();
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'success-toast';
        
        toast.innerHTML = `
            <div class="d-flex align-items-center justify-content-center">
                <i class="bi bi-check-circle-fill toast-icon"></i>
                <div>
                    <div class="toast-message">${title}</div>
                    ${subtitle ? `<div class="toast-subtitle">${subtitle}</div>` : ''}
                </div>
            </div>
        `;
        
        // Add to document
        document.body.appendChild(toast);
        
        // Auto-remove after 6 seconds (mobile-friendly duration)
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 400); // Wait for fade-out animation
        }, 6000);
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

    // PHASE 7: Auto-advance functionality - Focus scanner for next reading
    // Following Enhanced UX Flow: "Scanner refocuses for next scan"
    focusScannerForNext() {
        try {
            // Show scanner controls
            const startBtn = document.getElementById('start-scanner');
            const stopBtn = document.getElementById('stop-scanner');
            
            if (startBtn && !this.isScanning) {
                startBtn.classList.remove('scanner-hidden');
                startBtn.classList.add('scanner-visible');
                
                // Auto-start scanner for seamless workflow
                setTimeout(() => {
                    if (!this.isScanning) {
                        this.startScanner();
                    }
                }, 300);
            }
            
            // Show status message encouraging next scan
            this.showStatus('Scanner ready for next meter reading', 'info');
            
        } catch (error) {
            console.error('Error focusing scanner for next reading:', error);
            // Fallback: just show the start button
            const startBtn = document.getElementById('start-scanner');
            if (startBtn) {
                startBtn.classList.remove('scanner-hidden');
                startBtn.classList.add('scanner-visible');
            }
        }
    }

    // Testing functionality for screenshots
    addTestingControls() {
        // Create testing panel
        const testingPanel = document.createElement('div');
        testingPanel.id = 'testing-panel';
        testingPanel.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            z-index: 9999;
            font-family: Arial, sans-serif;
            font-size: 12px;
            min-width: 200px;
            max-width: 250px;
        `;

        testingPanel.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 style="margin: 0; color: #ffeb3b;">📸 Testing Panel</h4>
                <button id="test-toggle" style="padding: 2px 6px; font-size: 10px; background: #666; color: white; border: none; border-radius: 3px;">Hide</button>
            </div>
            <div id="test-controls" style="margin-bottom: 10px;">
                <div style="margin-bottom: 10px;">
                    <button id="test-online" style="margin: 2px; padding: 5px 8px; font-size: 11px;">Online</button>
                    <button id="test-offline" style="margin: 2px; padding: 5px 8px; font-size: 11px;">Offline</button>
                </div>
                <div style="margin-bottom: 10px;">
                    <button id="test-pending-1" style="margin: 2px; padding: 5px 8px; font-size: 11px;">1 Pending</button>
                    <button id="test-pending-3" style="margin: 2px; padding: 5px 8px; font-size: 11px;">3 Pending</button>
                    <button id="test-pending-5" style="margin: 2px; padding: 5px 8px; font-size: 11px;">5 Pending</button>
                </div>
                <div style="margin-bottom: 10px;">
                    <button id="test-cycle" style="margin: 2px; padding: 5px 8px; font-size: 11px; background: #4caf50;">Auto Cycle</button>
                    <button id="test-sync" style="margin: 2px; padding: 5px 8px; font-size: 11px; background: #2196f3;">Test Sync</button>
                    <button id="test-clear" style="margin: 2px; padding: 5px 8px; font-size: 11px; background: #f44336;">Clear</button>
                </div>
                <div style="margin-bottom: 10px;">
                    <button id="test-offline-notification" style="margin: 2px; padding: 5px 8px; font-size: 11px; background: #ff6b6b;">Test Offline</button>
                    <button id="test-online-notification" style="margin: 2px; padding: 5px 8px; font-size: 11px; background: #4caf50;">Test Online</button>
                </div>
                <div style="font-size: 10px; color: #ccc;">
                    Status: <span id="test-status">Online</span>
                </div>
            </div>
        `;

        document.body.appendChild(testingPanel);

        // Add event listeners for testing buttons
        document.getElementById('test-online').addEventListener('click', () => {
            this.testSetOnline();
        });

        document.getElementById('test-offline').addEventListener('click', () => {
            this.testSetOffline();
        });

        document.getElementById('test-pending-1').addEventListener('click', () => {
            this.testSetPending(1);
        });

        document.getElementById('test-pending-3').addEventListener('click', () => {
            this.testSetPending(3);
        });

        document.getElementById('test-pending-5').addEventListener('click', () => {
            this.testSetPending(5);
        });

        document.getElementById('test-cycle').addEventListener('click', () => {
            this.testAutoCycle();
        });

        document.getElementById('test-clear').addEventListener('click', () => {
            this.testClear();
        });

        document.getElementById('test-sync').addEventListener('click', () => {
            this.testSyncProgress();
        });

        document.getElementById('test-offline-notification').addEventListener('click', () => {
            this.testOfflineNotification();
        });

        document.getElementById('test-online-notification').addEventListener('click', () => {
            this.testOnlineNotification();
        });

        // Add toggle functionality
        document.getElementById('test-toggle').addEventListener('click', () => {
            this.toggleTestingPanel();
        });
    }

    // Testing methods
    testSetOnline() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.isOnline = true;
        this.offlineQueue = []; // Clear any pending readings for true online state
        localStorage.removeItem('qr_meter_readings_offline'); // Clear localStorage
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        this.updateUserAvatarStatus();
        this.updateTestStatus('Online');
    }

    testSetOffline() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.isOnline = false;
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        this.updateUserAvatarStatus();
        this.updateTestStatus('Offline');
    }

    testSetPending(count) {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.isOnline = true;
        
        // Clear existing test data
        this.offlineQueue = [];
        
        // Add test readings
        for (let i = 1; i <= count; i++) {
            this.offlineQueue.push({
                propertyCode: `GCA`,
                unitNo: `10${i}`,
                currentReading: 10000 + i,
                remarks: `Test reading ${i}`,
                syncId: `test_${i}_${Date.now()}`,
                timestamp: new Date().toISOString()
            });
        }
        
        // Save to localStorage
        localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
        
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        this.updateUserAvatarStatus();
        this.updateTestStatus(`Online - ${count} Pending`);
    }

    testAutoCycle() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        const states = [
            { name: 'Online', action: () => this.testSetOnline() },
            { name: 'Online - 1 Pending', action: () => this.testSetPending(1) },
            { name: 'Online - 3 Pending', action: () => this.testSetPending(3) },
            { name: 'Offline', action: () => this.testSetOffline() },
            { name: 'Offline - 2 Saved', action: () => {
                this.isOnline = false;
                this.testSetPending(2);
                this.updateTestStatus('Offline - 2 Saved');
            }}
        ];

        let currentIndex = 0;
        
        const cycle = () => {
            const state = states[currentIndex];
            state.action();
            
            currentIndex = (currentIndex + 1) % states.length;
            
            // Continue cycling every 4 seconds
            setTimeout(cycle, 4000);
        };
        
        // Start cycling
        cycle();
        
        // Show cycling indicator
        const cycleBtn = document.getElementById('test-cycle');
        cycleBtn.textContent = 'Cycling...';
        cycleBtn.style.background = '#ff9800';
        
        // Stop cycling after 20 seconds
        setTimeout(() => {
            cycleBtn.textContent = 'Auto Cycle';
            cycleBtn.style.background = '#4caf50';
        }, 20000);
    }

    testClear() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.isOnline = navigator.onLine;
        this.offlineQueue = [];
        
        // Clear localStorage
        localStorage.removeItem('qr_meter_readings_offline');
        
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        this.updateUserAvatarStatus();
        this.updateTestStatus('Cleared');
        
        // Hide testing panel
        const testingPanel = document.getElementById('testing-panel');
        if (testingPanel) {
            testingPanel.style.display = 'none';
        }
    }

    // Test notification methods
    testOfflineNotification() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.showOfflineNotification();
        this.updateTestStatus('Offline Notification Shown');
    }

    testOnlineNotification() {
        // Only allow test functions in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test functions are only available in testing mode');
            return;
        }
        
        this.showOnlineNotification();
        this.updateTestStatus('Online Notification Shown');
    }

    updateTestStatus(status) {
        const statusElement = document.getElementById('test-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
    }

    toggleTestingPanel() {
        const controls = document.getElementById('test-controls');
        const toggleBtn = document.getElementById('test-toggle');
        
        if (controls.style.display === 'none') {
            controls.style.display = 'block';
            toggleBtn.textContent = 'Hide';
        } else {
            controls.style.display = 'none';
            toggleBtn.textContent = 'Show';
        }
    }

    testSyncProgress() {
        // Only allow test sync in testing mode
        if (!this.appConfig || !this.appConfig.isTesting) {
            console.warn('Test sync is only available in testing mode');
            return;
        }
        
        // Create test offline readings if none exist
        if (this.offlineQueue.length === 0) {
            this.testSetPending(5);
        }
        
        // Trigger auto sync simulation with delay for screenshots
        this.testAutoSyncWithDelay();
    }

    async testAutoSyncWithDelay() {
        if (this.offlineQueue.length === 0) {
            console.log('No offline readings to sync');
            return;
        }
        
        console.log(`Starting TEST auto sync of ${this.offlineQueue.length} offline readings`);
        const readingsToSync = [...this.offlineQueue];
        const totalReadings = readingsToSync.length;
        let syncedCount = 0;
        let failedCount = 0;
        
        // Show sync progress indicator (test sync - auto sync simulation)
        this.showSyncProgress(totalReadings, 0, 0, false);
        
        for (let i = 0; i < readingsToSync.length; i++) {
            const reading = readingsToSync[i];
            
            try {
                // Check connection before each sync attempt
                if (!this.isOnline) {
                    console.log('Connection lost during sync, stopping sync process');
                    this.hideSyncProgress();
                    break;
                }
                
                // Update progress indicator
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
                console.log(`TEST Auto syncing reading: ${reading.propertyCode}-${reading.unitNo}`);
                
                // Add delay for screenshot purposes (2 seconds per reading)
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                // Simulate sync attempt (don't actually call server)
                const success = Math.random() > 0.2; // 80% success rate for testing
                
                if (success) {
                    // Remove from offline queue only after confirmed success
                    this.offlineQueue = this.offlineQueue.filter(r => r.syncId !== reading.syncId);
                    localStorage.setItem('qr_meter_readings_offline', JSON.stringify(this.offlineQueue));
                    syncedCount++;
                    console.log(`TEST Auto sync successfully synced reading: ${reading.propertyCode}-${reading.unitNo}`);
                } else {
                    failedCount++;
                    console.log(`TEST Auto sync failed to sync reading: ${reading.propertyCode}-${reading.unitNo}`);
                }
                
                // Update progress after each reading
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
                
            } catch (error) {
                console.error(`TEST Auto sync error syncing reading ${reading.propertyCode}-${reading.unitNo}:`, error);
                failedCount++;
                
                // Update progress even on error
                this.updateSyncProgress(totalReadings, i + 1, syncedCount, failedCount);
            }
        }
        
        // Hide progress indicator
        this.hideSyncProgress();
        
        // Update UI with sync results
        this.updateOfflineStatus();
        this.updateOfflineIndicator();
        
        if (syncedCount > 0) {
            this.showStatus(`TEST Auto sync: ${syncedCount} reading(s) synced successfully`, 'success');
        }
        
        if (failedCount > 0) {
            this.showStatus(`TEST Auto sync: ${failedCount} reading(s) failed to sync`, 'warning');
        }
        
        console.log(`TEST Auto sync completed: ${syncedCount} synced, ${failedCount} failed, ${this.offlineQueue.length} remaining`);
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

// Phase 9: Tenant Resolution Service Class
class TenantResolutionService {
    constructor(comprehensiveCache) {
        this.comprehensiveCache = comprehensiveCache;
        this.cacheExpiry = 90 * 24 * 60 * 60 * 1000; // 90 days as per creative design
    }

    // Local normalization helpers to avoid dependency on outer class
    normalizePropertyCode(propertyCode) {
        return propertyCode ? String(propertyCode).trim() : '';
    }

    normalizeUnitNo(unitNo) {
        return unitNo ? String(unitNo).trim() : '';
    }

    normalizePropertyAndUnit(propertyCode, unitNo) {
        return {
            propertyCode: this.normalizePropertyCode(propertyCode),
            unitNo: this.normalizeUnitNo(unitNo)
        };
    }

    async resolveTenantWithFallback(propertyCode, unitNo) {
        const strategies = [
            this.resolveFromCache.bind(this),
            this.resolveFromOfflineHistory.bind(this),
            this.resolveFromDefaults.bind(this),
            this.resolveFromServer.bind(this)
        ];
        
        for (let i = 0; i < strategies.length; i++) {
            try {
                const result = await strategies[i](propertyCode, unitNo);
                if (result && this.validateTenantResult(result)) {
                    console.log(`Tenant resolved using strategy ${i + 1}: ${result.source}`);
                    return result;
                }
            } catch (error) {
                console.warn(`Strategy ${i + 1} failed:`, error);
                continue;
            }
        }
        
        throw new Error('All tenant resolution strategies failed');
    }

    resolveFromCache(propertyCode, unitNo) {
        // Phase 9: Use comprehensive cache as per creative design
        if (this.comprehensiveCache && this.comprehensiveCache.latestReadings) {
            console.log('Cache lookup for:', propertyCode, unitNo);
            console.log('Cache contains:', this.comprehensiveCache.latestReadings.length, 'readings');
            
            // Normalize property code and unit number for comparison
            const normalized = this.normalizePropertyAndUnit(propertyCode, unitNo);
            
            const cachedReading = this.comprehensiveCache.latestReadings.find(reading => 
                this.normalizePropertyCode(reading.property_code) === normalized.propertyCode && 
                this.normalizeUnitNo(reading.unit_no) === normalized.unitNo
            );
            
            if (cachedReading) {
                console.log('Cache hit found:', cachedReading);
                return {
                    success: true,
                    data: {
                        tenantCode: cachedReading.tenant_code,
                        tenantName: cachedReading.tenant_name,
                        realPropertyName: cachedReading.property_name,
                        unitNo: cachedReading.unit_no,
                        meterNumber: cachedReading.meter_number || null,
                        unitType: cachedReading.unit_desc || 'Unknown'
                    },
                    source: 'cache',
                    confidence: 0.9
                };
            } else {
                console.log('Cache miss - no reading found for:', propertyCode, unitNo);
                console.log('Looking for normalized:', normalized.propertyCode, normalized.unitNo);
                console.log('First 3 cache entries:', this.comprehensiveCache.latestReadings.slice(0, 3));
            }
        } else {
            console.log('No comprehensive cache available');
        }
        
        return null;
    }

    resolveFromOfflineHistory(propertyCode, unitNo) {
        const offlineReadings = this.getOfflineReadings();
        const normalized = this.normalizePropertyAndUnit(propertyCode, unitNo);
        
        const recentReading = offlineReadings
            .filter(r => this.normalizePropertyCode(r.propertyCode) === normalized.propertyCode && 
                        this.normalizeUnitNo(r.unitNo) === normalized.unitNo)
            .sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))[0];
        
        if (recentReading && recentReading.tenantCode) {
            return {
                success: true,
                data: {
                    tenantCode: recentReading.tenantCode,
                    tenantName: recentReading.tenantName || 'Unknown Tenant',
                    realPropertyName: recentReading.realPropertyName || propertyCode,
                    unitNo: unitNo,
                    meterNumber: recentReading.meterNumber,
                    unitType: recentReading.unitType
                },
                source: 'offline_history',
                confidence: 0.7
            };
        }
        
        return null;
    }

    resolveFromDefaults(propertyCode, unitNo) {
        // Phase 9: Return actual defaults as per creative design
        return {
            success: true,
            data: {
                tenantCode: 'DEFAULT',
                tenantName: 'Default Tenant',
                realPropertyName: propertyCode,
                unitNo: unitNo,
                meterNumber: null,
                unitType: 'Unknown'
            },
            source: 'defaults',
            confidence: 0.3
        };
    }

    async resolveFromServer(propertyCode, unitNo) {
        if (!navigator.onLine) {
            throw new Error('Server resolution requires online connection');
        }
        
        // Phase 9: Use correct API endpoint as per creative design
        const response = await fetch(`api/get-tenant-data.php?propertyCode=${encodeURIComponent(propertyCode)}&unitNo=${encodeURIComponent(unitNo)}`);
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                return {
                    success: true,
                    data: data.data,
                    source: 'server',
                    confidence: 1.0
                };
            }
        }
        
        throw new Error('Server resolution failed');
    }

    // Phase 9: Cache management handled by comprehensive cache
    isCacheExpired(cached) {
        if (!cached || !cached.expiresAt) {
            return true;
        }
        return new Date() > new Date(cached.expiresAt);
    }

    validateTenantResult(result) {
        return result && result.data && result.data.tenantCode;
    }

    getOfflineReadings() {
        const offlineData = localStorage.getItem('qr_meter_readings_offline');
        return offlineData ? JSON.parse(offlineData) : [];
    }

    // Phase 9: Property defaults handled by resolveFromDefaults method
}

// Phase 9: Data Validation Pipeline Class
class DataValidationPipeline {
    constructor() {
        this.validationRules = {
            requiredFields: ['propertyCode', 'unitNo', 'currentReading'],
            numericFields: ['currentReading'],
            minValues: { currentReading: 0 },
            maxValues: { currentReading: 999999 }
        };
    }

    validateOfflineReading(readingData) {
        const results = [];
        
        // Validate required fields
        const requiredValidation = this.validateRequiredFields(readingData);
        results.push(requiredValidation);
        
        // Validate data types
        const typeValidation = this.validateDataTypes(readingData);
        results.push(typeValidation);
        
        // Validate business rules
        const businessValidation = this.validateBusinessRules(readingData);
        results.push(businessValidation);
        
        // Check if all validations passed
        const valid = results.every(result => result.valid);
        const errors = results.filter(result => !result.valid);
        
        return {
            valid,
            errors: errors.map(e => e.error),
            checks: results
        };
    }

    validateRequiredFields(readingData) {
        const missing = this.validationRules.requiredFields.filter(field => 
            !readingData[field] || readingData[field] === ''
        );
        
        if (missing.length > 0) {
            return {
                valid: false,
                error: `Missing required fields: ${missing.join(', ')}`
            };
        }
        
        return { valid: true };
    }

    validateDataTypes(readingData) {
        for (const field of this.validationRules.numericFields) {
            if (readingData[field] !== undefined && isNaN(readingData[field])) {
                return {
                    valid: false,
                    error: `Field ${field} must be a number`
                };
            }
        }
        
        return { valid: true };
    }

    validateBusinessRules(readingData) {
        // Validate current reading is positive
        if (readingData.currentReading <= 0) {
            return {
                valid: false,
                error: 'Current reading must be greater than 0'
            };
        }
        
        // Validate current reading is within reasonable range
        if (readingData.currentReading > this.validationRules.maxValues.currentReading) {
            return {
                valid: false,
                error: 'Current reading exceeds maximum allowed value'
            };
        }
        
        return { valid: true };
    }
}

// Phase 9: Enhanced Offline Storage Class
class EnhancedOfflineStorage {
    constructor() {
        this.metadataFields = [
            'validationTimestamp',
            'validationChecks',
            'deviceInfo',
            'locationData',
            'syncId'
        ];
    }

    addValidationMetadata(readingData, validationResult) {
        const enhancedData = {
            ...readingData,
            validationMetadata: {
                validationTimestamp: new Date().toISOString(),
                validationChecks: validationResult.checks,
                deviceInfo: this.getDeviceInfo(),
                locationData: readingData.locationData || null,
                syncId: this.generateSyncId()
            }
        };
        
        return enhancedData;
    }

    getDeviceInfo() {
        return {
            userAgent: navigator.userAgent,
            platform: navigator.platform,
            language: navigator.language,
            timestamp: new Date().toISOString()
        };
    }

    generateSyncId() {
        return `sync_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }

    prepareForSync(offlineReadings) {
        return offlineReadings.map(reading => ({
            ...reading,
            syncPreparedAt: new Date().toISOString(),
            syncAttempts: reading.syncAttempts || 0
        }));
    }

    validateBeforeSync(offlineReadings) {
        return offlineReadings.filter(reading => {
            // Only sync readings that have valid metadata
            return reading.validationMetadata && 
                   reading.validationMetadata.validationChecks &&
                   reading.validationMetadata.validationChecks.every(check => check.valid);
        });
    }
}

// ========================
// PHASE 10: MOBILE GESTURE SUPPORT
// ========================

/**
 * Mobile Gesture Detection System
 * Supports swipe navigation and enhanced touch interactions
 * Compatible with Samsung A15, iPhone 14 Pro Max, and tablets
 */
class MobileGestureHandler {
    constructor() {
        this.touchStartX = null;
        this.touchStartY = null;
        this.touchCurrentX = null;
        this.touchCurrentY = null;
        this.gestureStartTime = null;
        this.minSwipeDistance = 50; // Minimum distance for swipe recognition
        this.maxSwipeTime = 500; // Maximum time for swipe gesture
        this.isGestureActive = false;
        this.swipeThreshold = 100; // Distance threshold for triggering swipe
        
        this.init();
    }

    init() {
        this.setupTouchEventListeners();
        this.addGestureCSS();
        this.log('MobileGestureHandler initialized');
    }

    setupTouchEventListeners() {
        // Add touch event listeners to document for global gesture support
        document.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: false });
        document.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: false });
        document.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: false });
        
        this.log('Touch event listeners setup complete');
    }

    handleTouchStart(event) {
        const touch = event.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartY = touch.clientY;
        this.touchCurrentX = touch.clientX;
        this.touchCurrentY = touch.clientY;
        this.gestureStartTime = Date.now();
        this.isGestureActive = true;
        
        this.log(`Touch start: (${this.touchStartX}, ${this.touchStartY})`);
    }

    handleTouchMove(event) {
        if (!this.isGestureActive) return;
        
        const touch = event.touches[0];
        this.touchCurrentX = touch.clientX;
        this.touchCurrentY = touch.clientY;
        
        // Provide visual feedback during gesture
        this.showGestureFeedback();
    }

    handleTouchEnd(event) {
        if (!this.isGestureActive) return;

        const deltaX = this.touchCurrentX - this.touchStartX;
        const deltaY = this.touchCurrentY - this.touchStartY;
        const deltaTime = Date.now() - this.gestureStartTime;
        const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        
        this.log(`Touch end: delta(${deltaX}, ${deltaY}), distance: ${distance}, time: ${deltaTime}ms`);
        
        // Detect swipe gestures
        if (distance > this.minSwipeDistance && deltaTime < this.maxSwipeTime) {
            this.processSwipeGesture(deltaX, deltaY, distance);
        }
        
        this.clearGestureFeedback();
        this.resetGestureData();
    }

    processSwipeGesture(deltaX, deltaY, distance) {
        const threshold = this.swipeThreshold;
        
        // Determine primary swipe direction
        if (Math.abs(deltaX) > Math.abs(deltaY)) {
            // Horizontal swipe
            if (deltaX > threshold) {
                this.handleSwipeRight(distance);
            } else if (deltaX < -threshold) {
                this.handleSwipeLeft(distance);
            }
        } else {
            // Vertical swipe
            if (deltaY > threshold) {
                this.handleSwipeDown(distance);
            } else if (deltaY < -threshold) {
                this.handleSwipeUp(distance);
            }
        }
    }

    handleSwipeLeft(distance) {
        this.log('Swipe Left detected');
        this.executeSwipeAction('left', distance);
    }

    handleSwipeRight(distance) {
        this.log('Swipe Right detected');
        this.executeSwipeAction('right', distance);
    }

    handleSwipeUp(distance) {
        this.log('Swipe Up detected');
        this.executeSwipeAction('up', distance);
    }

    handleSwipeDown(distance) {
        this.log('Swipe Down detected');
        this.executeSwipeAction('down', distance);
    }

    executeSwipeAction(direction, distance) {
        // Provide haptic feedback if supported
        this.provideHapticFeedback();
        
        // Execute appropriate action based on direction
        switch (direction) {
            case 'left':
                this.navigateToPreviousSection();
                break;
            case 'right':
                this.navigateToNextSection();
                break;
            case 'up':
                this.scrollUp();
                break;
            case 'down':
                this.scrollDown();
                break;
        }
        
        // Show swipe confirmation feedback
        this.showSwipeConfirmation(direction);
    }

    navigateToPreviousSection() {
        // Example: Navigate to previous section (could be previous form, previous page, etc.)
        this.log('Navigating to previous section');
        // Implementation depends on specific navigation requirements
    }

    navigateToNextSection() {
        // Example: Navigate to next section (could be next form, next page, etc.)
        this.log('Navigating to next section');
        // Implementation depends on specific navigation requirements
    }

    scrollUp() {
        // Smooth scroll up
        window.scrollBy({
            top: -200,
            behavior: 'smooth'
        });
        this.log('Scrolling up');
    }

    scrollDown() {
        // Smooth scroll down
        window.scrollBy({
            top: 200,
            behavior: 'smooth'
        });
        this.log('Scrolling down');
    }

    provideHapticFeedback() {
        // Provide haptic feedback if supported by device
        if (navigator.vibrate) {
            navigator.vibrate(50); // Short vibration for feedback
        }
    }

    showSwipeConfirmation(direction) {
        // Show brief visual confirmation of swipe action
        const confirmation = document.createElement('div');
        confirmation.className = 'swipe-confirmation';
        confirmation.textContent = `Swipe ${direction.toUpperCase()} detected`;
        
        confirmation.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(30, 64, 175, 0.9);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            z-index: 9999;
            font-size: 14px;
            transition: all 0.3s ease-in-out;
        `;
        
        document.body.appendChild(confirmation);
        
        // Remove confirmation after 2 seconds
        setTimeout(() => {
            confirmation.style.opacity = '0';
            setTimeout(() => {
                if (confirmation.parentNode) {
                    confirmation.parentNode.removeChild(confirmation);
                }
            }, 300);
        }, 2000);
    }

    showGestureFeedback() {
        // Subtle visual feedback during gesture
        document.body.classList.add('gesture-active');
    }

    clearGestureFeedback() {
        // Remove visual feedback
        document.body.classList.remove('gesture-active');
    }

    resetGestureData() {
        this.touchStartX = null;
        this.touchStartY = null;
        this.touchCurrentX = null;
        this.touchCurrentY = null;
        this.gestureStartTime = null;
        this.isGestureActive = false;
    }

    addGestureCSS() {
        // Add CSS for gesture feedback
        const style = document.createElement('style');
        style.textContent = `
            .gesture-active {
                overflow: hidden;
                touch-action: none;
            }
            
            .swipe-confirmation {
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
            
            /* Enhanced touch targets */
            .touch-target {
                min-height: 44px;
                min-width: 44px;
                touch-action: manipulation;
            }
            
            /* Improve form interaction */
            input, select, textarea {
                touch-action: manipulation;
                -webkit-appearance: none;
                border-radius: 8px;
            }
            
            /* QR Scanner touch area enhancement */
            #qr-reader {
                touch-action: manipulation;
            }
        `;
        document.head.appendChild(style);
    }

    log(message) {
        console.log(`[MobileGesture] ${message}`);
    }
}

// Initialize mobile gesture handler when the app starts
document.addEventListener('DOMContentLoaded', () => {
    if (typeof MobileGestureHandler !== 'undefined') {
        window.mobileGestureHandler = new MobileGestureHandler();
    }
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
