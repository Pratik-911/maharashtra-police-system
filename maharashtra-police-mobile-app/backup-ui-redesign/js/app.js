// Main Application Logic
// Maharashtra Police Mobile App

// Hybrid approach: Capacitor Geolocation for native, browser API for web fallback
const { Geolocation } = window.Capacitor?.Plugins || {};

// Helper function to get environment-specific API base URL
function getApiBaseUrl() {
    // Always use LAN IP for proper connectivity from both browser and native app
    return 'http://192.168.1.4:8080';
}

// Fallback configuration in case window.AppConfig is not loaded
if (typeof window.AppConfig === 'undefined') {
    window.AppConfig = {
        API_BASE_URL: getApiBaseUrl(),
        APP_NAME: 'Maharashtra Police',
        VERSION: '1.0.0',
        ENDPOINTS: {
            LOGIN: '/api/auth/mobile-login',
            LOGOUT: '/api/auth/logout',
            DUTIES: '/api/officer/duties',
            LOCATION: '/api/location/log',
            PROFILE: '/api/officer/profile'
        }
    };
}

class MaharashtraPoliceApp {
    constructor() {
        this.isAuthenticated = false;
        this.currentOfficer = null;
        this.activeDuty = null;
        this.locationWatchId = null;
        this.isTracking = false;
        this.locationCount = 0;
        this.lastLocationTime = null;
        this.networkListener = null;
        
        this.init();
    }
    
    async init() {
        console.log('🚀 Maharashtra Police App initializing...');
        
        try {
            // Initialize Capacitor plugins (only if available)
            await this.initializeCapacitor();
            
            // Setup network monitoring (fallback for browser)
            await this.setupNetworkMonitoring();
            
            // Check for existing session
            await this.checkExistingSession();
            
            // Setup event listeners
            this.setupEventListeners();
            
        } catch (error) {
            console.error('❌ App initialization failed:', error);
            this.showLoginScreen();
        }
    }
    
    async initializeCapacitor() {
        // Wait for Capacitor plugins to be available (optional for browser testing)
        let attempts = 0;
        while (!window.CapacitorPlugins && attempts < 10) {
            await new Promise(resolve => setTimeout(resolve, 100));
            attempts++;
        }
        
        if (!window.CapacitorPlugins) {
            console.warn('⚠️ Capacitor plugins not available - using browser fallbacks');
            // Create fallback plugins for browser testing
            window.CapacitorPlugins = {
                storage: {
                    get: async (key) => ({ value: localStorage.getItem(key) }),
                    set: async (key, value) => localStorage.setItem(key, JSON.stringify(value)),
                    remove: async (key) => localStorage.removeItem(key)
                },
                network: {
                    getStatus: async () => ({ connected: navigator.onLine }),
                    addListener: (callback) => {
                        window.addEventListener('online', () => callback({ connected: true }));
                        window.addEventListener('offline', () => callback({ connected: false }));
                    }
                },
                geolocation: {
                    getCurrentPosition: async (options) => {
                        return new Promise((resolve, reject) => {
                            navigator.geolocation.getCurrentPosition(
                                (position) => resolve({
                                    coords: {
                                        latitude: position.coords.latitude,
                                        longitude: position.coords.longitude,
                                        accuracy: position.coords.accuracy
                                    }
                                }),
                                reject,
                                options
                            );
                        });
                    }
                }
            };
        }
    }
    
    async getDeviceInfo() {
        try {
            // Try to get device info from Capacitor if available
            if (window.CapacitorPlugins && window.CapacitorPlugins.device) {
                return await window.CapacitorPlugins.device.getInfo();
            } else {
                // Browser fallback
                return {
                    platform: 'web',
                    model: navigator.userAgent,
                    operatingSystem: navigator.platform,
                    osVersion: 'unknown',
                    manufacturer: 'unknown',
                    isVirtual: false
                };
            }
        } catch (error) {
            console.warn('⚠️ Could not get device info, using fallback:', error);
            return {
                platform: 'web',
                model: 'Browser',
                operatingSystem: 'unknown',
                osVersion: 'unknown',
                manufacturer: 'unknown',
                isVirtual: false
            };
        }
    }
    
    setupEventListeners() {
        // Login form
        document.getElementById('loginForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleLogin();
        });
        
        // Logout button
        document.getElementById('logoutBtn').addEventListener('click', () => {
            this.handleLogout();
        });
        
        // Location tracking buttons
        document.getElementById('startTracking').addEventListener('click', () => {
            this.startLocationTracking();
        });
        
        document.getElementById('stopTracking').addEventListener('click', () => {
            this.stopLocationTracking();
        });
        
        // App lifecycle events
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('📱 App went to background');
                this.handleAppBackground();
            } else {
                console.log('📱 App came to foreground');
                this.handleAppForeground();
            }
        });
    }
    
    async setupNetworkMonitoring() {
        // Monitor network status
        const networkStatus = await window.CapacitorPlugins.network.getStatus();
        this.updateConnectionStatus(networkStatus);
        
        // Listen for network changes
        this.networkListener = window.CapacitorPlugins.network.addListener((status) => {
            this.updateConnectionStatus(status);
        });
    }
    
    updateConnectionStatus(status) {
        const statusElement = document.getElementById('connectionStatus');
        const textElement = document.getElementById('connectionText');
        
        if (status.connected) {
            statusElement.className = 'status-indicator status-active';
            textElement.textContent = 'Online';
        } else {
            statusElement.className = 'status-indicator status-inactive';
            textElement.textContent = 'Offline';
        }
    }
    
    async checkExistingSession() {
        try {
            let savedSession = null;
            
            // Try Capacitor storage first (native app)
            if (typeof window.CapacitorPlugins !== 'undefined' && window.CapacitorPlugins.storage) {
                const result = await window.CapacitorPlugins.storage.get('officer_session');
                if (result && result.value) {
                    try {
                        savedSession = typeof result.value === 'string' ? JSON.parse(result.value) : result.value;
                    } catch (e) {
                        savedSession = result.value;
                    }
                }
            } else {
                // Fallback to localStorage for browser
                const sessionData = localStorage.getItem('officer_session');
                if (sessionData) {
                    try {
                        savedSession = JSON.parse(sessionData);
                    } catch (e) {
                        console.error('Error parsing session data:', e);
                    }
                }
            }
            
            if (savedSession && savedSession.token) {
                console.log('📱 Found existing session');
                this.currentOfficer = savedSession;
                await this.loadDashboard();
            } else {
                console.log('📱 No existing session found');
                this.showLoginScreen();
            }
        } catch (error) {
            console.error('Error checking session:', error);
            this.showLoginScreen();
        }
    }
    
    async handleLogin() {
        // Ensure AppConfig is available with environment-specific API URL
        if (typeof window.AppConfig === 'undefined') {
            // Detect if running in native app (Capacitor) or browser
            const isNativeApp = typeof window.Capacitor !== 'undefined' && window.Capacitor.isNativePlatform();
            const apiBaseUrl = isNativeApp ? 'http://10.0.2.2:8080' : 'http://localhost:8080';
            
            window.AppConfig = {
                API_BASE_URL: apiBaseUrl,
                APP_NAME: 'Maharashtra Police',
                VERSION: '1.0.0',
                ENDPOINTS: {
                    LOGIN: '/api/auth/mobile-login',
                    LOGOUT: '/api/auth/logout',
                    DUTIES: '/api/officer/duties',
                    LOCATION: '/api/location/log',
                    PROFILE: '/api/officer/profile'
                }
            };
        }
        
        const badgeNumber = document.getElementById('badgeNumber').value;
        const password = document.getElementById('password').value;
        const submitBtn = document.querySelector('#loginForm button[type="submit"]');
        const spinner = submitBtn.querySelector('.loading-spinner');
        const errorDiv = document.getElementById('loginError');
        
        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.add('show');
        errorDiv.style.display = 'none';
        
        try {
            // Get device info with fallback for browser
            const deviceInfo = await this.getDeviceInfo();
            
            const response = await fetch(`${getApiBaseUrl()}/api/auth/mobile-login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    badge_number: badgeNumber,
                    password: password,
                    device_info: deviceInfo
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                console.log('✅ Login successful');
                this.currentOfficer = result.data;
                
                // Save session
                // Save session with fallback for browser
                if (typeof window.CapacitorPlugins !== 'undefined' && window.CapacitorPlugins.storage) {
                    await window.CapacitorPlugins.storage.set('officer_session', result.data);
                } else {
                    localStorage.setItem('officer_session', JSON.stringify(result.data));
                }
                
                // Load dashboard
                await this.loadDashboard();
                
            } else {
                throw new Error(result.message || 'Login failed');
            }
            
        } catch (error) {
            console.error('❌ Login error:', error);
            errorDiv.textContent = error.message || 'लॉगिन अयशस्वी. कृपया पुन्हा प्रयत्न करा.';
            errorDiv.style.display = 'block';
        } finally {
            submitBtn.disabled = false;
            spinner.classList.remove('show');
        }
    }
    
    async handleLogout() {
        try {
            // Stop location tracking
            await this.stopLocationTracking();
            
            // Clear session
            // Clear session with fallback for browser
            if (typeof window.CapacitorPlugins !== 'undefined' && window.CapacitorPlugins.storage) {
                await window.CapacitorPlugins.storage.remove('officer_session');
            } else {
                localStorage.removeItem('officer_session');
            }
            
            // Reset state
            this.currentOfficer = null;
            this.activeDuty = null;
            this.isAuthenticated = false;
            
            // Show login screen
            this.showLoginScreen();
            
            console.log('✅ Logout successful');
            
        } catch (error) {
            console.error('❌ Logout error:', error);
        }
    }
    
    showLoginScreen() {
        document.getElementById('loginScreen').style.display = 'block';
        document.getElementById('dashboardScreen').style.display = 'none';
        
        // Hide logout button on login screen
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.style.display = 'none';
        }
        
        // Reset authentication state
        this.isAuthenticated = false;
        this.currentOfficer = null;
    }
    
    async loadDashboard() {
        try {
            // Update UI with officer info
            document.getElementById('officerName').textContent = 
                `${this.currentOfficer.name} (${this.currentOfficer.badge_number})`;
            
            // Load active duty
            await this.loadActiveDuty();
            
            // Show dashboard
            document.getElementById('loginScreen').style.display = 'none';
            document.getElementById('dashboardScreen').style.display = 'block';
            
            // Show logout button on dashboard
            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.style.display = 'block';
            }
            
            this.isAuthenticated = true;
            
            console.log('✅ Dashboard loaded');
            
        } catch (error) {
            console.error('❌ Dashboard load error:', error);
            this.showLoginScreen();
        }
    }
    
    async loadActiveDuty() {
        try {
            // Use the working officer duties API and filter client-side
            const response = await fetch(
                `${getApiBaseUrl()}/api/duties/officer/${this.currentOfficer.id}`,
                {
                    headers: {
                        'Authorization': `Bearer ${this.currentOfficer.token}`,
                        'Content-Type': 'application/json'
                    }
                }
            );
            
            const result = await response.json();
            
            if (result.success && result.data && result.data.length > 0) {
                // Filter for active duty (current date and time)
                const currentDate = new Date().toISOString().split('T')[0]; // YYYY-MM-DD
                const currentTime = new Date().toTimeString().split(' ')[0]; // HH:MM:SS
                
                const activeDuty = result.data.find(duty => {
                    return duty.date === currentDate && 
                           duty.start_time <= currentTime && 
                           duty.end_time >= currentTime;
                });
                
                if (activeDuty) {
                    this.activeDuty = activeDuty;
                    this.updateDutyUI();
                    
                    // Auto-start location tracking if enabled
                    if (this.activeDuty.location_tracking_enabled) {
                        setTimeout(() => this.startLocationTracking(), 2000);
                    }
                } else {
                    this.showNoDuty();
                }
            } else {
                this.showNoDuty();
            }
            
        } catch (error) {
            console.error('Error loading active duty:', error);
            this.showNoDuty();
        }
    }
    
    updateDutyUI() {
        if (this.activeDuty) {
            document.getElementById('dutyInfo').style.display = 'block';
            document.getElementById('noDuty').style.display = 'none';
            
            document.getElementById('dutyLocation').textContent = this.activeDuty.point_name;
            document.getElementById('dutyTime').textContent = 
                `${this.activeDuty.start_time} - ${this.activeDuty.end_time}`;
        }
    }
    
    showNoDuty() {
        document.getElementById('dutyInfo').style.display = 'none';
        document.getElementById('noDuty').style.display = 'block';
    }
    
    async startLocationTracking() {
        if (!this.activeDuty) {
            this.showLocationError('कोणतीही सक्रिय ड्यूटी नाही');
            return;
        }
        
        try {
            console.log('🔍 Starting location tracking...');
            
            // Check if running in Capacitor (native app)
            if (window.Capacitor && window.Capacitor.isNativePlatform() && Geolocation) {
                // Step 1: Request foreground location permission first
                const permissions = await Geolocation.requestPermissions();
                
                if (permissions.location !== 'granted') {
                    throw new Error('स्थान परवानगी नाकारली - कृपया पुन्हा प्रयत्न करा');
                }
                
                // Step 2: Always show background location guidance for Android 10+
                // Since Capacitor cannot programmatically request "Allow all the time"
                this.showBackgroundLocationGuidance();
            }
            
            // Update UI
            document.getElementById('startTracking').classList.add('d-none');
            document.getElementById('stopTracking').classList.remove('d-none');
            
            this.updateLocationStatus('स्थान ट्रॅकिंग सुरू केले...', 'info');
            
            // Start periodic location updates
            this.startPeriodicLocationUpdates();
            
            this.isTracking = true;
            console.log('✅ Location tracking started');
            
        } catch (error) {
            console.error('❌ Start tracking error:', error);
            this.updateLocationStatus('स्थान परवानगी नाकारली', 'danger');
            
            // Reset UI
            document.getElementById('startTracking').classList.remove('d-none');
            document.getElementById('stopTracking').classList.add('d-none');
        }
    }
    
    async stopLocationTracking() {
        try {
            console.log('🛑 Stopping location tracking...');
            
            if (this.locationWatchId) {
                clearInterval(this.locationWatchId);
                this.locationWatchId = null;
            }
            
            // Update UI
            document.getElementById('startTracking').classList.remove('d-none');
            document.getElementById('stopTracking').classList.add('d-none');
            
            this.updateLocationStatus('स्थान ट्रॅकिंग थांबवले', 'warning');
            
            this.isTracking = false;
            console.log('✅ Location tracking stopped');
            
        } catch (error) {
            console.error('❌ Stop tracking error:', error);
        }
    }
    
    startPeriodicLocationUpdates() {
        // Clear any existing interval
        if (this.locationWatchId) {
            clearInterval(this.locationWatchId);
        }
        
        // Start periodic location updates every 30 seconds
        this.locationWatchId = setInterval(async () => {
            try {
                // Use Capacitor Geolocation plugin only
                if (!Geolocation) {
                    throw new Error('Capacitor Geolocation plugin not available');
                }
                
                const position = await Geolocation.getCurrentPosition({
                    enableHighAccuracy: false,
                    timeout: 30000,
                    maximumAge: 60000
                });
                
                await this.handleLocationUpdate(position);
                
            } catch (error) {
                console.error('Location update error:', error);
                this.handleLocationError(error);
            }
        }, 30000); // 30 seconds
        
        console.log('📍 Periodic location updates started (every 30 seconds)');
    }
    
    showBackgroundLocationGuidance() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            बॅकग्राउंड स्थान परवानगी आवश्यक
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Android 10+ वर महत्वाचे:</strong> सतत ड्यूटी ट्रॅकिंगसाठी "नेहमी अनुमती द्या" आवश्यक आहे.
                        </div>
                        
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>का आवश्यक आहे?</strong><br>
                            • ड्यूटी अनुपालन निरीक्षण<br>
                            • अधिकारी सुरक्षा ट्रॅकिंग<br>
                            • फोन लॉक असताना देखील स्थान ट्रॅकिंग
                        </div>
                        
                        <h6 class="fw-bold mb-3 text-primary">📱 सेटिंग्ज बदलण्याचे पायऱ्या:</h6>
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-cog text-muted me-2"></i>
                                <strong>सेटिंग्ज</strong> अॅप उघडा
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-mobile-alt text-muted me-2"></i>
                                <strong>अॅप्स</strong> किंवा <strong>Application Manager</strong> निवडा
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                <strong>Maharashtra Police</strong> अॅप शोधा
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-key text-muted me-2"></i>
                                <strong>परवानग्या (Permissions)</strong> वर टॅप करा
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <strong>स्थान (Location)</strong> निवडा
                            </li>
                            <li class="list-group-item d-flex align-items-center bg-success text-white">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>"नेहमी अनुमती द्या" (Allow all the time)</strong> निवडा
                            </li>
                        </ol>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-times-circle text-danger me-2"></i>
                            <strong>चेतावणी:</strong> "फक्त अॅप वापरताना" पर्याय निवडल्यास फोन लॉक असताना ट्रॅकिंग थांबेल!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-lg" data-bs-dismiss="modal">
                            <i class="fas fa-thumbs-up me-2"></i>समजले, सेटिंग्ज बदलतो
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
        
        // Remove modal from DOM when closed
        modal.addEventListener('hidden.bs.modal', () => {
            document.body.removeChild(modal);
        });
    }
    
    openAppSettings() {
        // Try to open app settings (Capacitor plugin)
        if (window.Capacitor && window.Capacitor.isNativePlatform()) {
            // This would require a custom plugin or native implementation
            console.log('Opening app settings...');
            // For now, show instructions
            alert('कृपया मॅन्युअली सेटिंग्ज > अॅप्स > Maharashtra Police > परवानग्या > स्थान > "नेहमी अनुमती द्या" निवडा');
        }
    }
    
    async handleLocationUpdate(position) {
        try {
            const { latitude, longitude, accuracy } = position.coords;
            const timestamp = new Date().toISOString();
            
            console.log('📍 Location update:', { latitude, longitude, accuracy, timestamp });
            
            // Send to server
            await this.sendLocationToServer(latitude, longitude, timestamp);
            
            // Update UI
            this.locationCount++;
            this.lastLocationTime = new Date();
            this.updateLocationStats();
            
            // Update status
            this.updateLocationStatus(
                `स्थान अपडेट केले (${accuracy.toFixed(0)}m accuracy)`,
                'success'
            );
            
        } catch (error) {
            console.error('❌ Handle location update error:', error);
            this.showLocationError('स्थान अपडेट अयशस्वी');
        }
    }
    
    async sendLocationToServer(latitude, longitude, timestamp) {
        try {
            // Validate required data before sending
            if (!this.currentOfficer || !this.currentOfficer.id) {
                throw new Error('अधिकारी माहिती उपलब्ध नाही');
            }
            
            if (!this.activeDuty || (!this.activeDuty.duty_id && !this.activeDuty.id)) {
                throw new Error('सक्रिय ड्यूटी माहिती उपलब्ध नाही');
            }
            
            // Use duty_id or id field (depending on data structure)
            const dutyId = this.activeDuty.duty_id || this.activeDuty.id;
            
            console.log('📤 Sending location data:', {
                officer_id: this.currentOfficer.id,
                duty_id: dutyId,
                latitude: parseFloat(latitude),
                longitude: parseFloat(longitude)
            });
            
            const response = await fetch(
                `${getApiBaseUrl()}/api/location/log`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${this.currentOfficer.token}`,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        officer_id: this.currentOfficer.id,
                        duty_id: dutyId,
                        latitude: parseFloat(latitude),
                        longitude: parseFloat(longitude),
                        auth_token: this.currentOfficer.token
                    })
                }
            );
            
            const result = await response.json();
            
            // Handle authentication errors (JWT expired/invalid)
            if (response.status === 401) {
                console.warn('🔐 JWT token expired/invalid, clearing session');
                await this.clearSession();
                this.showLoginForm();
                throw new Error('प्रमाणीकरण त्रुटी - कृपया पुन्हा लॉगिन करा');
            }
            
            if (!result.success) {
                throw new Error(result.message || 'Server error');
            }
            
            console.log('✅ Location sent to server successfully');
            
        } catch (error) {
            console.error('❌ Send location error:', error);
            
            // Queue for retry if offline
            const networkStatus = await window.CapacitorPlugins.network.getStatus();
            if (!networkStatus.connected) {
                await this.queueLocationForRetry(latitude, longitude, timestamp);
            }
            
            throw error;
        }
    }
    
    async queueLocationForRetry(latitude, longitude, timestamp) {
        try {
            const queuedLocations = await window.CapacitorPlugins.storage.get('queued_locations') || [];
            queuedLocations.push({
                officer_id: this.currentOfficer.id,
                duty_id: this.activeDuty.id,
                latitude: parseFloat(latitude),
                longitude: parseFloat(longitude),
                timestamp: timestamp,
                queued_at: new Date().toISOString()
            });
            
            await window.CapacitorPlugins.storage.set('queued_locations', queuedLocations);
            console.log('📦 Location queued for retry');
            
        } catch (error) {
            console.error('❌ Queue location error:', error);
        }
    }
    
    handleLocationError(error) {
        console.error('❌ Location error:', error);
        
        let errorMessage = 'स्थान त्रुटी';
        
        switch (error.code) {
            case 1: // PERMISSION_DENIED
                errorMessage = 'स्थान परवानगी नाकारली';
                break;
            case 2: // POSITION_UNAVAILABLE
                errorMessage = 'स्थान उपलब्ध नाही';
                break;
            case 3: // TIMEOUT
                errorMessage = 'स्थान टाइमआउट';
                break;
        }
        
        this.showLocationError(errorMessage);
    }
    
    updateLocationStatus(message, type) {
        const statusDiv = document.getElementById('locationStatus');
        const statusText = document.getElementById('locationStatusText');
        
        statusDiv.className = `location-status alert alert-${type}`;
        statusText.innerHTML = `<i class="fas fa-${this.getStatusIcon(type)} me-2"></i>${message}`;
    }
    
    getStatusIcon(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'danger': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            default: return 'info-circle';
        }
    }
    
    showLocationError(message) {
        this.updateLocationStatus(message, 'danger');
    }
    
    updateLocationStats() {
        document.getElementById('locationCount').textContent = this.locationCount;
        document.getElementById('lastUpdate').textContent = 
            this.lastLocationTime ? this.lastLocationTime.toLocaleTimeString() : 'Never';
    }
    
    handleAppBackground() {
        // App went to background - location tracking continues natively
        console.log('📱 App backgrounded - native location tracking continues');
    }
    
    handleAppForeground() {
        // App came to foreground - sync any queued data
        console.log('📱 App foregrounded - syncing queued data');
        this.syncQueuedLocations();
    }
    
    async syncQueuedLocations() {
        try {
            const queuedLocations = await window.CapacitorPlugins.storage.get('queued_locations') || [];
            
            if (queuedLocations.length === 0) {
                return;
            }
            
            console.log(`🔄 Syncing ${queuedLocations.length} queued locations...`);
            
            for (const location of queuedLocations) {
                try {
                    await this.sendLocationToServer(
                        location.latitude,
                        location.longitude,
                        location.timestamp
                    );
                } catch (error) {
                    console.error('Failed to sync location:', error);
                }
            }
            
            // Clear synced locations
            await window.CapacitorPlugins.storage.remove('queued_locations');
            console.log('✅ Queued locations synced');
            
        } catch (error) {
            console.error('❌ Sync queued locations error:', error);
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new MaharashtraPoliceApp();
});

console.log('🔧 Maharashtra Police App loaded');
