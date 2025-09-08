/**
 * RMS QR Meter Reading System - Service Worker
 * Provides offline functionality and caching for the PWA
 */

const CACHE_NAME = 'qr-meter-reading-v1.0.0';
const STATIC_CACHE = 'qr-meter-reading-static-v1.0.0';
const DYNAMIC_CACHE = 'qr-meter-reading-dynamic-v1.0.0';

// Files to cache for offline functionality
const STATIC_FILES = [
    '/',
    '/index.php',
    '/assets/css/custom-theme.css',
    '/assets/css/qr-scanner.css',
    '/assets/js/app.js',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                console.log('Service Worker: Static files cached');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('Service Worker: Error caching static files:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache or network
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Handle API requests
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(handleApiRequest(request));
        return;
    }
    
    // Handle static files
    if (isStaticFile(url.pathname)) {
        event.respondWith(handleStaticRequest(request));
        return;
    }
    
    // Handle other requests
    event.respondWith(handleDynamicRequest(request));
});

// Handle API requests with offline support
async function handleApiRequest(request) {
    // Skip caching for unsupported schemes
    if (!request.url.startsWith('http://') && !request.url.startsWith('https://')) {
        return fetch(request);
    }
    
    try {
        // Try network first
        const response = await fetch(request);
        
        if (response.ok) {
            // Cache successful API responses
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
            return response;
        }
        
        throw new Error('API request failed');
    } catch (error) {
        console.log('Service Worker: API request failed, checking cache:', error);
        
        // Check cache for API response
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline response for API requests
        return new Response(
            JSON.stringify({
                success: false,
                error: 'Offline mode - data will be synced when online',
                offline: true
            }),
            {
                status: 503,
                statusText: 'Service Unavailable',
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        );
    }
}

// Handle static file requests
async function handleStaticRequest(request) {
    // Skip caching for unsupported schemes
    if (!request.url.startsWith('http://') && !request.url.startsWith('https://')) {
        return fetch(request);
    }
    
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        console.error('Service Worker: Error fetching static file:', error);
        return new Response('Offline - Static file not available', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

// Handle dynamic requests
async function handleDynamicRequest(request) {
    // Skip caching for unsupported schemes
    if (!request.url.startsWith('http://') && !request.url.startsWith('https://')) {
        return fetch(request);
    }
    
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            // Cache successful responses
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('Service Worker: Network request failed, checking cache:', error);
        
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (request.destination === 'document') {
            return caches.match('/offline.html');
        }
        
        return new Response('Offline - Content not available', {
            status: 503,
            statusText: 'Service Unavailable'
        });
    }
}

// Check if file is static
function isStaticFile(pathname) {
    const staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
    return staticExtensions.some(ext => pathname.endsWith(ext)) || 
           pathname.startsWith('/assets/') ||
           pathname === '/' ||
           pathname === '/index.php';
}

// Background sync for offline data
self.addEventListener('sync', (event) => {
    if (event.tag === 'background-sync') {
        console.log('Service Worker: Background sync triggered');
        event.waitUntil(syncOfflineData());
    }
});

// Sync offline data when back online
async function syncOfflineData() {
    try {
        // Get offline readings from IndexedDB or localStorage
        const offlineReadings = await getOfflineReadings();
        
        if (offlineReadings.length > 0) {
            console.log('Service Worker: Syncing offline readings:', offlineReadings.length);
            
            for (const reading of offlineReadings) {
                try {
                    const response = await fetch('/api/save-reading.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(reading)
                    });
                    
                    if (response.ok) {
                        // Remove from offline storage
                        await removeOfflineReading(reading);
                        console.log('Service Worker: Synced reading:', reading);
                    }
                } catch (error) {
                    console.error('Service Worker: Error syncing reading:', error);
                }
            }
        }
    } catch (error) {
        console.error('Service Worker: Error in background sync:', error);
    }
}

// Get offline readings from localStorage
async function getOfflineReadings() {
    // This would typically use IndexedDB, but for simplicity using localStorage
    // In a real implementation, you'd want to use IndexedDB for better performance
    return [];
}

// Remove offline reading
async function removeOfflineReading(reading) {
    // Implementation would remove from IndexedDB
    return true;
}

// Push notification handling
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || 'New meter reading notification',
            icon: '/assets/images/icon-192x192.png',
            badge: '/assets/images/icon-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: 1
            },
            actions: [
                {
                    action: 'explore',
                    title: 'View Reading',
                    icon: '/assets/images/icon-96x96.png'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/assets/images/icon-96x96.png'
                }
            ]
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title || 'QR Meter Reading', options)
        );
    }
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handling for communication with main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    }
});
