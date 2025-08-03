// Background Location Tracking Integration
// Maharashtra Police Duty Management System

class BackgroundLocationTracker {
    constructor() {
        this.serviceWorker = null;
        this.isRegistered = false;
        this.locationQueue = [];
        this.lastLocationTime = 0;
        this.backgroundSyncSupported = false;
        this.visibilityChangeHandler = null;
        
        this.init();
    }
    
    async init() {
        console.log('🔧 Initializing Background Location Tracker...');
        
        // Register Service Worker
        await this.registerServiceWorker();
        
        // Check for Background Sync support
        this.checkBackgroundSyncSupport();
        
        // Set up visibility change handling
        this.setupVisibilityHandling();
        
        // Set up periodic sync fallback
        this.setupPeriodicSyncFallback();
        
        console.log('✅ Background Location Tracker initialized');
    }
    
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw-location.js');
                console.log('✅ Service Worker registered:', registration);
                
                this.serviceWorker = registration;
                this.isRegistered = true;
                
                // Listen for messages from Service Worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    console.log('📨 Message from Service Worker:', event.data);
                });
                
            } catch (error) {
                console.error('❌ Service Worker registration failed:', error);
            }
        } else {
            console.warn('⚠️ Service Workers not supported');
        }
    }
    
    checkBackgroundSyncSupport() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            this.backgroundSyncSupported = true;
            console.log('✅ Background Sync supported');
        } else {
            console.warn('⚠️ Background Sync not supported - using fallback methods');
        }
    }
    
    setupVisibilityHandling() {
        // Handle page visibility changes
        this.visibilityChangeHandler = () => {
            if (document.hidden) {
                console.log('📱 Page went to background - enabling background sync');
                this.enableBackgroundMode();
            } else {
                console.log('📱 Page came to foreground - resuming normal tracking');
                this.enableForegroundMode();
            }
        };
        
        document.addEventListener('visibilitychange', this.visibilityChangeHandler);
        
        // Handle page unload
        window.addEventListener('beforeunload', () => {
            this.syncQueuedLocations();
        });
    }
    
    setupPeriodicSyncFallback() {
        // Fallback for browsers without Background Sync
        // Use a more aggressive approach when page is visible
        setInterval(() => {
            if (!document.hidden) {
                this.syncQueuedLocations();
            }
        }, 60000); // Every minute when visible
    }
    
    enableBackgroundMode() {
        console.log('🌙 Enabling background location tracking mode');
        
        if (this.backgroundSyncSupported && this.serviceWorker) {
            // Register for background sync
            this.serviceWorker.sync.register('background-location-sync').catch(err => {
                console.error('Failed to register background sync:', err);
            });
        }
        
        // Store current state in localStorage for persistence
        localStorage.setItem('locationTrackingActive', 'true');
        localStorage.setItem('backgroundModeEnabled', 'true');
    }
    
    enableForegroundMode() {
        console.log('☀️ Enabling foreground location tracking mode');
        
        // Sync any queued locations immediately
        this.syncQueuedLocations();
        
        localStorage.setItem('backgroundModeEnabled', 'false');
    }
    
    async queueLocation(locationData) {
        const timestamp = Date.now();
        const queuedLocation = {
            ...locationData,
            timestamp: timestamp,
            queuedAt: timestamp,
            synced: false
        };
        
        console.log('📍 Queuing location for background sync:', queuedLocation);
        
        // Add to local queue
        this.locationQueue.push(queuedLocation);
        
        // Store in localStorage as backup
        this.saveQueueToStorage();
        
        // If Service Worker available, send to it
        if (this.serviceWorker && this.serviceWorker.active) {
            this.serviceWorker.active.postMessage({
                type: 'QUEUE_LOCATION',
                locationData: queuedLocation
            });
        }
        
        // If page is visible, try immediate sync
        if (!document.hidden) {
            await this.syncQueuedLocations();
        }
    }
    
    async syncQueuedLocations() {
        if (this.locationQueue.length === 0) {
            return;
        }
        
        console.log(`🔄 Syncing ${this.locationQueue.length} queued locations...`);
        
        const locationsToSync = [...this.locationQueue];
        
        for (let i = 0; i < locationsToSync.length; i++) {
            const location = locationsToSync[i];
            
            if (location.synced) {
                continue;
            }
            
            try {
                const response = await fetch('/api/location/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        officer_id: location.officer_id,
                        duty_id: location.duty_id,
                        latitude: location.latitude,
                        longitude: location.longitude,
                        timestamp: new Date(location.timestamp).toISOString()
                    })
                });
                
                if (response.ok) {
                    console.log('✅ Successfully synced location:', location.timestamp);
                    location.synced = true;
                    
                    // Remove from queue
                    const index = this.locationQueue.findIndex(l => l.timestamp === location.timestamp);
                    if (index > -1) {
                        this.locationQueue.splice(index, 1);
                    }
                } else {
                    console.error('❌ Failed to sync location:', response.status);
                }
                
            } catch (error) {
                console.error('❌ Error syncing location:', error);
            }
        }
        
        // Update storage
        this.saveQueueToStorage();
        
        console.log(`✅ Location sync completed. ${this.locationQueue.length} locations remaining in queue`);
    }
    
    saveQueueToStorage() {
        try {
            localStorage.setItem('locationQueue', JSON.stringify(this.locationQueue));
        } catch (error) {
            console.error('Failed to save location queue to storage:', error);
        }
    }
    
    loadQueueFromStorage() {
        try {
            const stored = localStorage.getItem('locationQueue');
            if (stored) {
                this.locationQueue = JSON.parse(stored);
                console.log(`📂 Loaded ${this.locationQueue.length} locations from storage`);
            }
        } catch (error) {
            console.error('Failed to load location queue from storage:', error);
            this.locationQueue = [];
        }
    }
    
    // Public method to add location from main tracking
    addLocation(latitude, longitude, officerId, dutyId) {
        const locationData = {
            officer_id: parseInt(officerId),
            duty_id: parseInt(dutyId),
            latitude: parseFloat(latitude),
            longitude: parseFloat(longitude)
        };
        
        return this.queueLocation(locationData);
    }
    
    // Clean up
    destroy() {
        if (this.visibilityChangeHandler) {
            document.removeEventListener('visibilitychange', this.visibilityChangeHandler);
        }
        
        // Sync any remaining locations
        this.syncQueuedLocations();
    }
}

// Global instance
window.backgroundLocationTracker = new BackgroundLocationTracker();

console.log('🔧 Background Location Tracking module loaded');
