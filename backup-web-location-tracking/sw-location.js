// Service Worker for Background Location Tracking
// Maharashtra Police Duty Management System

const CACHE_NAME = 'location-tracker-v1';
const LOCATION_SYNC_TAG = 'background-location-sync';

// Install event
self.addEventListener('install', function(event) {
    console.log('Location Service Worker installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', function(event) {
    console.log('Location Service Worker activating...');
    event.waitUntil(self.clients.claim());
});

// Background Sync for location updates
self.addEventListener('sync', function(event) {
    console.log('Background sync triggered:', event.tag);
    
    if (event.tag === LOCATION_SYNC_TAG) {
        event.waitUntil(syncLocationData());
    }
});

// Sync queued location data
async function syncLocationData() {
    try {
        console.log('Syncing queued location data...');
        
        // Get queued location data from IndexedDB
        const queuedLocations = await getQueuedLocations();
        
        if (queuedLocations.length === 0) {
            console.log('No queued locations to sync');
            return;
        }
        
        console.log(`Syncing ${queuedLocations.length} queued locations`);
        
        // Send each queued location
        for (const locationData of queuedLocations) {
            try {
                const response = await fetch('/api/location/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(locationData)
                });
                
                if (response.ok) {
                    console.log('Successfully synced location:', locationData.timestamp);
                    await removeQueuedLocation(locationData.id);
                } else {
                    console.error('Failed to sync location:', response.status);
                }
            } catch (error) {
                console.error('Error syncing location:', error);
            }
        }
        
        console.log('Background location sync completed');
        
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

// IndexedDB operations for queuing location data
async function getQueuedLocations() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('LocationQueue', 1);
        
        request.onerror = () => reject(request.error);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['locations'], 'readonly');
            const store = transaction.objectStore('locations');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = () => resolve(getAllRequest.result);
            getAllRequest.onerror = () => reject(getAllRequest.error);
        };
        
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains('locations')) {
                const store = db.createObjectStore('locations', { keyPath: 'id', autoIncrement: true });
                store.createIndex('timestamp', 'timestamp', { unique: false });
            }
        };
    });
}

async function removeQueuedLocation(id) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('LocationQueue', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['locations'], 'readwrite');
            const store = transaction.objectStore('locations');
            const deleteRequest = store.delete(id);
            
            deleteRequest.onsuccess = () => resolve();
            deleteRequest.onerror = () => reject(deleteRequest.error);
        };
    });
}

// Handle messages from main thread
self.addEventListener('message', function(event) {
    console.log('Service Worker received message:', event.data);
    
    if (event.data.type === 'QUEUE_LOCATION') {
        // Queue location data for background sync
        queueLocationForSync(event.data.locationData);
    } else if (event.data.type === 'START_BACKGROUND_TRACKING') {
        // Register for background sync
        self.registration.sync.register(LOCATION_SYNC_TAG);
    }
});

// Queue location data in IndexedDB
async function queueLocationForSync(locationData) {
    try {
        const request = indexedDB.open('LocationQueue', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['locations'], 'readwrite');
            const store = transaction.objectStore('locations');
            
            const queuedLocation = {
                ...locationData,
                queuedAt: Date.now(),
                synced: false
            };
            
            store.add(queuedLocation);
            console.log('Location queued for background sync:', queuedLocation);
        };
        
        request.onupgradeneeded = () => {
            const db = request.result;
            if (!db.objectStoreNames.contains('locations')) {
                const store = db.createObjectStore('locations', { keyPath: 'id', autoIncrement: true });
                store.createIndex('timestamp', 'timestamp', { unique: false });
            }
        };
    } catch (error) {
        console.error('Failed to queue location:', error);
    }
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', function(event) {
    if (event.tag === 'location-sync') {
        event.waitUntil(syncLocationData());
    }
});

console.log('Location Service Worker loaded');
