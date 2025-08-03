// Capacitor Initialization and Plugin Setup
// Maharashtra Police Mobile App

// Browser-compatible configuration (no ES6 imports)
// Capacitor plugins will be loaded via script tags in HTML

// Global app configuration
window.AppConfig = {
    // Update this to your actual server URL
    API_BASE_URL: 'http://localhost:8080', // CodeIgniter backend URL
    APP_NAME: 'Maharashtra Police',
    VERSION: '1.0.0',
    
    // Location tracking settings
    LOCATION_TRACKING: {
        enableHighAccuracy: true,
        timeout: 30000,
        maximumAge: 60000,
        updateInterval: 30000 // 30 seconds
    },
    
    // API endpoints
    ENDPOINTS: {
        LOGIN: '/api/auth/mobile-login',
        LOCATION_LOG: '/api/location/log',
        ACTIVE_DUTY: '/api/duties/active',
        COMPLIANCE: '/api/compliance'
    }
};

// Capacitor Platform Detection
window.CapacitorUtils = {
    isNative: () => typeof Capacitor !== 'undefined' ? Capacitor.isNativePlatform() : false,
    isAndroid: () => typeof Capacitor !== 'undefined' ? Capacitor.getPlatform() === 'android' : false,
    isIOS: () => typeof Capacitor !== 'undefined' ? Capacitor.getPlatform() === 'ios' : false,
    isWeb: () => typeof Capacitor !== 'undefined' ? Capacitor.getPlatform() === 'web' : true,
    
    // Device info
    getDeviceInfo: async () => {
        try {
            if (typeof Capacitor === 'undefined') {
                return {
                    platform: 'web',
                    version: 'unknown',
                    model: 'Browser',
                    osVersion: 'unknown'
                };
            }
            const info = await Capacitor.getInfo();
            return {
                platform: info.platform,
                version: info.version,
                model: info.model || 'Unknown',
                osVersion: info.osVersion || 'Unknown'
            };
        } catch (error) {
            console.error('Error getting device info:', error);
            return {
                platform: 'web',
                version: '1.0.0',
                model: 'Browser',
                osVersion: 'Unknown'
            };
        }
    }
};

// Initialize Capacitor plugins
window.CapacitorPlugins = {
    // Geolocation plugin wrapper
    geolocation: {
        async checkPermissions() {
            try {
                return await Geolocation.checkPermissions();
            } catch (error) {
                console.error('Geolocation permission check failed:', error);
                return { location: 'denied' };
            }
        },
        
        async requestPermissions() {
            try {
                return await Geolocation.requestPermissions();
            } catch (error) {
                console.error('Geolocation permission request failed:', error);
                return { location: 'denied' };
            }
        },
        
        async getCurrentPosition(options = {}) {
            try {
                const defaultOptions = {
                    enableHighAccuracy: window.AppConfig.LOCATION_TRACKING.enableHighAccuracy,
                    timeout: window.AppConfig.LOCATION_TRACKING.timeout,
                    maximumAge: window.AppConfig.LOCATION_TRACKING.maximumAge
                };
                
                return await Geolocation.getCurrentPosition({
                    ...defaultOptions,
                    ...options
                });
            } catch (error) {
                console.error('Get current position failed:', error);
                throw error;
            }
        },
        
        async watchPosition(callback, errorCallback, options = {}) {
            try {
                const defaultOptions = {
                    enableHighAccuracy: window.AppConfig.LOCATION_TRACKING.enableHighAccuracy,
                    timeout: window.AppConfig.LOCATION_TRACKING.timeout,
                    maximumAge: window.AppConfig.LOCATION_TRACKING.maximumAge
                };
                
                return await Geolocation.watchPosition({
                    ...defaultOptions,
                    ...options
                }, callback);
            } catch (error) {
                console.error('Watch position failed:', error);
                if (errorCallback) errorCallback(error);
                throw error;
            }
        },
        
        async clearWatch(watchId) {
            try {
                await Geolocation.clearWatch({ id: watchId });
            } catch (error) {
                console.error('Clear watch failed:', error);
            }
        }
    },
    
    // Local notifications wrapper
    notifications: {
        async checkPermissions() {
            try {
                return await LocalNotifications.checkPermissions();
            } catch (error) {
                console.error('Notification permission check failed:', error);
                return { display: 'denied' };
            }
        },
        
        async requestPermissions() {
            try {
                return await LocalNotifications.requestPermissions();
            } catch (error) {
                console.error('Notification permission request failed:', error);
                return { display: 'denied' };
            }
        },
        
        async schedule(notification) {
            try {
                await LocalNotifications.schedule({
                    notifications: [notification]
                });
            } catch (error) {
                console.error('Schedule notification failed:', error);
            }
        }
    },
    
    // Network status wrapper
    network: {
        async getStatus() {
            try {
                // Use browser fallback if Network plugin not available
                if (typeof Network !== 'undefined') {
                    return await Network.getStatus();
                } else {
                    return { connected: navigator.onLine, connectionType: 'unknown' };
                }
            } catch (error) {
                console.warn('Network status check failed, using fallback:', error);
                return { connected: navigator.onLine, connectionType: 'unknown' };
            }
        },
        
        addListener(callback) {
            try {
                // Use browser fallback if Network plugin not available
                if (typeof Network !== 'undefined') {
                    return Network.addListener('networkStatusChange', callback);
                } else {
                    // Browser fallback using online/offline events
                    const onlineHandler = () => callback({ connected: true, connectionType: 'unknown' });
                    const offlineHandler = () => callback({ connected: false, connectionType: 'none' });
                    window.addEventListener('online', onlineHandler);
                    window.addEventListener('offline', offlineHandler);
                    return { 
                        remove: () => {
                            window.removeEventListener('online', onlineHandler);
                            window.removeEventListener('offline', offlineHandler);
                        }
                    };
                }
            } catch (error) {
                console.warn('Network listener failed, using fallback:', error);
                return { remove: () => {} };
            }
        }
    },
    
    // Storage wrapper
    storage: {
        async get(key) {
            try {
                // Use browser fallback if Preferences plugin not available
                if (typeof Preferences !== 'undefined') {
                    const result = await Preferences.get({ key });
                    return result.value ? JSON.parse(result.value) : null;
                } else {
                    // Browser fallback using localStorage
                    const value = localStorage.getItem(key);
                    return value ? JSON.parse(value) : null;
                }
            } catch (error) {
                console.warn('Storage get failed, using fallback:', error);
                // Fallback to localStorage
                try {
                    const value = localStorage.getItem(key);
                    return value ? JSON.parse(value) : null;
                } catch (e) {
                    return null;
                }
            }
        },
        
        async set(key, value) {
            try {
                // Use browser fallback if Preferences plugin not available
                if (typeof Preferences !== 'undefined') {
                    await Preferences.set({
                        key,
                        value: JSON.stringify(value)
                    });
                } else {
                    // Browser fallback using localStorage
                    localStorage.setItem(key, JSON.stringify(value));
                }
            } catch (error) {
                console.warn('Storage set failed, using fallback:', error);
                // Fallback to localStorage
                try {
                    localStorage.setItem(key, JSON.stringify(value));
                } catch (e) {
                    console.error('localStorage fallback failed:', e);
                }
            }
        },
        
        async remove(key) {
            try {
                // Use browser fallback if Preferences plugin not available
                if (typeof Preferences !== 'undefined') {
                    await Preferences.remove({ key });
                } else {
                    // Browser fallback using localStorage
                    localStorage.removeItem(key);
                }
            } catch (error) {
                console.warn('Storage remove failed, using fallback:', error);
                // Fallback to localStorage
                try {
                    localStorage.removeItem(key);
                } catch (e) {
                    console.error('localStorage fallback failed:', e);
                }
            }
        },
        
        async clear() {
            try {
                // Use browser fallback if Preferences plugin not available
                if (typeof Preferences !== 'undefined') {
                    await Preferences.clear();
                } else {
                    // Browser fallback using localStorage
                    localStorage.clear();
                }
            } catch (error) {
                console.error('Storage clear failed:', error);
            }
        }
    }
};

// Initialize app when Capacitor is ready
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Capacitor app initializing...');
    
    // Check if Capacitor is available (mobile app environment)
    if (typeof Capacitor === 'undefined') {
        console.log('⚠️ Capacitor not available - running in web browser mode');
        return;
    }
    
    // Wait for Capacitor to be ready
    await Capacitor.isReady;
    
    // Get device info
    const deviceInfo = await window.CapacitorUtils.getDeviceInfo();
    console.log('📱 Device info:', deviceInfo);
    
    // Check network status
    const networkStatus = await window.CapacitorPlugins.network.getStatus();
    console.log('🌐 Network status:', networkStatus);
    
    // Request necessary permissions
    await requestInitialPermissions();
    
    console.log('✅ Capacitor app initialized successfully');
});

// Request initial permissions
async function requestInitialPermissions() {
    console.log('🔐 Requesting initial permissions...');
    
    // Request location permissions
    const locationPermission = await window.CapacitorPlugins.geolocation.requestPermissions();
    console.log('📍 Location permission:', locationPermission);
    
    // Request notification permissions
    const notificationPermission = await window.CapacitorPlugins.notifications.requestPermissions();
    console.log('🔔 Notification permission:', notificationPermission);
}

console.log('🔧 Capacitor utilities loaded');
