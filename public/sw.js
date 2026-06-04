/**
 * Service Worker — Dashboard Stok Bibit Indonesia
 * Strategy:
 *   - Static assets (CSS, JS, images, fonts): Cache-First
 *   - HTML pages: Network-First with offline.html fallback
 *   - AJAX / API calls: Network-Only (bypass SW)
 */

const CACHE_VERSION = 'v3';
const CACHE_STATIC  = `seedling-static-${CACHE_VERSION}`;
const CACHE_PAGES   = `seedling-pages-${CACHE_VERSION}`;
const OFFLINE_URL   = '/seedling-dashboard/public/offline.html';

// Static assets to pre-cache on install
const STATIC_ASSETS = [
    // App CSS
    '/seedling-dashboard/public/css/style.css',
    // App JS
    '/seedling-dashboard/public/js/main.js',
    '/seedling-dashboard/public/js/offline-manager.js',
    // Icons & manifest
    '/seedling-dashboard/public/manifest.json',
    '/seedling-dashboard/public/images/icon-192x192.png',
    '/seedling-dashboard/public/images/icon-512x512.png',
    // Offline fallback
    OFFLINE_URL,
];

// ── INSTALL: Pre-cache all static assets ──────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_STATIC)
            .then(cache => {
                console.log('[SW] Pre-caching static assets...');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => self.skipWaiting())
            .catch(err => console.warn('[SW] Pre-cache error:', err))
    );
});

// ── ACTIVATE: Delete old caches ───────────────────────────────────────────────
self.addEventListener('activate', event => {
    const validCaches = [CACHE_STATIC, CACHE_PAGES];
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(name => !validCaches.includes(name))
                        .map(name => {
                            console.log('[SW] Deleting old cache:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => self.clients.claim())
    );
});

// ── FETCH: Smart routing by request type ─────────────────────────────────────
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // ① BYPASS: Non-GET requests (POST, etc.) — never cache form submissions
    if (event.request.method !== 'GET') return;

    // ② BYPASS: AJAX/API endpoints — always go to network
    if (
        url.pathname.includes('-ajax') ||
        url.pathname.includes('/api/') ||
        url.searchParams.has('ajax') ||
        event.request.headers.get('X-Requested-With') === 'XMLHttpRequest'
    ) {
        return;
    }

    // ③ BYPASS: External CDN resources — let browser handle
    if (url.hostname !== self.location.hostname) return;

    // ④ STATIC ASSETS: Cache-First strategy
    const isStaticAsset = /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)(\?.*)?$/.test(url.pathname);
    if (isStaticAsset) {
        event.respondWith(cacheFirst(event.request));
        return;
    }

    // ⑤ HTML PAGES: Network-First, fallback to cache, fallback to offline.html
    event.respondWith(networkFirstWithOfflineFallback(event.request));
});

// ── Strategy: Cache-First ─────────────────────────────────────────────────────
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) return cachedResponse;

    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(CACHE_STATIC);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (err) {
        console.warn('[SW] Network failed for static asset:', request.url);
        return new Response('Asset not available offline.', { status: 503 });
    }
}

// ── Strategy: Network-First with offline fallback ─────────────────────────────
async function networkFirstWithOfflineFallback(request) {
    try {
        const networkResponse = await fetch(request);

        // Cache successful HTML responses
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(CACHE_PAGES);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;

    } catch (err) {
        console.warn('[SW] Network failed for page:', request.url);

        // Try the page cache first
        const cachedPage = await caches.match(request);
        if (cachedPage) return cachedPage;

        // Fall back to offline.html
        const offlinePage = await caches.match(OFFLINE_URL);
        if (offlinePage) return offlinePage;

        return new Response('Offline. Buka aplikasi terlebih dahulu saat ada koneksi.', {
            status: 503,
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
        });
    }
}

// ── Message Handler: Force update from UI ────────────────────────────────────
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
