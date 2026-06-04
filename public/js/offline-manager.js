/**
 * Offline Manager — Dashboard Stok Bibit Indonesia
 * Handles: IndexedDB queue, AJAX cache, and sync logic
 *
 * Stores:
 *   offlineQueue  — pending form submissions
 *   ajaxCache     — cached dropdown/master data for offline form use
 */

const OfflineManager = (function () {
    'use strict';

    const DB_NAME    = 'SeedlingOfflineDB';
    const DB_VERSION = 1;
    const STORE_QUEUE = 'offlineQueue';
    const STORE_CACHE = 'ajaxCache';

    let db = null;

    // ─────────────────────────────────────────────────────────────────────────
    // CORE: Open / Initialize IndexedDB
    // ─────────────────────────────────────────────────────────────────────────
    function openDB() {
        return new Promise((resolve, reject) => {
            if (db) return resolve(db);

            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (e) {
                const database = e.target.result;

                // Store for pending form submissions
                if (!database.objectStoreNames.contains(STORE_QUEUE)) {
                    const queueStore = database.createObjectStore(STORE_QUEUE, {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    queueStore.createIndex('status', 'status', { unique: false });
                    queueStore.createIndex('form_type', 'form_type', { unique: false });
                }

                // Store for AJAX/dropdown cache
                if (!database.objectStoreNames.contains(STORE_CACHE)) {
                    database.createObjectStore(STORE_CACHE, { keyPath: 'cache_key' });
                }
            };

            request.onsuccess = function (e) {
                db = e.target.result;
                resolve(db);
            };

            request.onerror = function (e) {
                console.error('[OfflineManager] IndexedDB error:', e.target.error);
                reject(e.target.error);
            };
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUEUE: Add a pending form submission
    // ─────────────────────────────────────────────────────────────────────────
    async function addToQueue(formType, endpoint, payload, displayLabel) {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_QUEUE, 'readwrite');
            const store = tx.objectStore(STORE_QUEUE);
            const item = {
                form_type:     formType,
                endpoint:      endpoint,
                payload:       payload,
                display_label: displayLabel || formType,
                status:        'pending',
                created_at:    new Date().toISOString(),
                error_msg:     null,
                retry_count:   0
            };
            const req = store.add(item);
            req.onsuccess = () => resolve(req.result);
            req.onerror   = () => reject(req.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUEUE: Get all pending items
    // ─────────────────────────────────────────────────────────────────────────
    async function getPendingQueue() {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_QUEUE, 'readonly');
            const store = tx.objectStore(STORE_QUEUE);
            const items = [];
            const cursor = store.openCursor();
            cursor.onsuccess = function (e) {
                const c = e.target.result;
                if (c) {
                    if (['pending', 'failed'].includes(c.value.status)) {
                        items.push(c.value);
                    }
                    c.continue();
                } else {
                    resolve(items);
                }
            };
            cursor.onerror = () => reject(cursor.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUEUE: Count pending items
    // ─────────────────────────────────────────────────────────────────────────
    async function getPendingCount() {
        const items = await getPendingQueue();
        return items.length;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUEUE: Update item status
    // ─────────────────────────────────────────────────────────────────────────
    async function updateItemStatus(id, status, errorMsg) {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_QUEUE, 'readwrite');
            const store = tx.objectStore(STORE_QUEUE);
            const getReq = store.get(id);
            getReq.onsuccess = function () {
                const item = getReq.result;
                if (!item) return reject(new Error('Item not found'));
                item.status    = status;
                item.error_msg = errorMsg || null;
                item.retry_count = (item.retry_count || 0) + 1;
                const putReq = store.put(item);
                putReq.onsuccess = () => resolve();
                putReq.onerror   = () => reject(putReq.error);
            };
            getReq.onerror = () => reject(getReq.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUEUE: Delete synced item
    // ─────────────────────────────────────────────────────────────────────────
    async function deleteItem(id) {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_QUEUE, 'readwrite');
            const store = tx.objectStore(STORE_QUEUE);
            const req = store.delete(id);
            req.onsuccess = () => resolve();
            req.onerror   = () => reject(req.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CACHE: Save AJAX response to IndexedDB for offline use
    // ─────────────────────────────────────────────────────────────────────────
    async function saveAjaxCache(cacheKey, data) {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_CACHE, 'readwrite');
            const store = tx.objectStore(STORE_CACHE);
            const item = {
                cache_key:  cacheKey,
                data:       data,
                cached_at:  new Date().toISOString()
            };
            const req = store.put(item);
            req.onsuccess = () => resolve();
            req.onerror   = () => reject(req.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CACHE: Get cached AJAX data
    // ─────────────────────────────────────────────────────────────────────────
    async function getAjaxCache(cacheKey) {
        const database = await openDB();
        return new Promise((resolve, reject) => {
            const tx = database.transaction(STORE_CACHE, 'readonly');
            const store = tx.objectStore(STORE_CACHE);
            const req = store.get(cacheKey);
            req.onsuccess = () => resolve(req.result || null);
            req.onerror   = () => reject(req.error);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FETCH WITH CACHE FALLBACK: Used by forms to load dropdown data
    // ─────────────────────────────────────────────────────────────────────────
    async function fetchWithCache(url, cacheKey) {
        if (navigator.onLine) {
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    await saveAjaxCache(cacheKey, data);
                }
                return { data: data, fromCache: false };
            } catch (err) {
                // Network error but we said we're online — fallback to cache
            }
        }

        // Offline or fetch failed: use cache
        const cached = await getAjaxCache(cacheKey);
        if (cached) {
            return { data: cached.data, fromCache: true, cachedAt: cached.cached_at };
        }
        return { data: null, fromCache: true, cachedAt: null };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CSRF: Get a fresh CSRF token from server
    // ─────────────────────────────────────────────────────────────────────────
    async function getFreshCsrfToken() {
        try {
            const resp = await fetch('/seedling-dashboard/public/seedling-admin/get-fresh-csrf', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            const json = await resp.json();
            if (json.success && json.csrf_token) {
                return json.csrf_token;
            }
        } catch (err) {
            console.error('[OfflineManager] Failed to get fresh CSRF:', err);
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SYNC: Main sync loop — called when user clicks "Sinkronkan"
    // ─────────────────────────────────────────────────────────────────────────
    async function syncAll(progressCallback) {
        if (!navigator.onLine) {
            return { success: false, message: 'Tidak ada koneksi internet.', results: [] };
        }

        const pendingItems = await getPendingQueue();
        if (pendingItems.length === 0) {
            return { success: true, message: 'Tidak ada data yang perlu disinkronisasi.', results: [] };
        }

        // Get a fresh CSRF token ONCE for all requests in this batch
        const csrfToken = await getFreshCsrfToken();
        if (!csrfToken) {
            return { success: false, message: 'Gagal mendapatkan token keamanan. Silakan refresh halaman.', results: [] };
        }

        const results = [];
        let successCount = 0;
        let failCount = 0;

        for (let i = 0; i < pendingItems.length; i++) {
            const item = pendingItems[i];

            if (progressCallback) {
                progressCallback(i + 1, pendingItems.length, item.display_label);
            }

            try {
                // Build form data
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('_offline_sync', '1'); // Flag so backend knows it's a sync

                // Append all payload fields
                const payload = item.payload;
                Object.keys(payload).forEach(key => {
                    const val = payload[key];
                    if (Array.isArray(val)) {
                        val.forEach(v => formData.append(key + '[]', v));
                    } else {
                        formData.append(key, val !== null && val !== undefined ? val : '');
                    }
                });

                const response = await fetch(item.endpoint, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                // Backend untuk form-form ini me-redirect setelah sukses.
                // Kita anggap sukses jika status code adalah redirect (302) atau 200
                // Cek jika response ada body JSON (untuk endpoint yang support JSON response)
                const responseText = await response.text();
                let isSuccess = true;
                let errorMessage = null;

                // Cek apakah response adalah JSON error
                try {
                    const json = JSON.parse(responseText);
                    if (json.success === false) {
                        isSuccess = false;
                        errorMessage = json.message || 'Gagal disimpan oleh server.';
                    }
                } catch (e) {
                    // Response bukan JSON (redirect page) — anggap berhasil
                    isSuccess = response.redirected || response.ok;
                }

                if (isSuccess) {
                    await deleteItem(item.id);
                    successCount++;
                    results.push({ id: item.id, label: item.display_label, status: 'ok' });
                } else {
                    await updateItemStatus(item.id, 'failed', errorMessage);
                    failCount++;
                    results.push({ id: item.id, label: item.display_label, status: 'error', message: errorMessage });
                }

            } catch (err) {
                const errMsg = err.message || 'Gagal terhubung ke server.';
                await updateItemStatus(item.id, 'failed', errMsg);
                failCount++;
                results.push({ id: item.id, label: item.display_label, status: 'error', message: errMsg });
                console.error('[OfflineManager] Sync error for item', item.id, err);
            }
        }

        return {
            success: failCount === 0,
            message: `Sinkronisasi selesai: ${successCount} berhasil, ${failCount} gagal.`,
            successCount,
            failCount,
            results
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UI HELPERS: Toast notification
    // ─────────────────────────────────────────────────────────────────────────
    function showToast(message, type) {
        // Remove existing toasts
        const existing = document.querySelector('.offline-toast');
        if (existing) existing.remove();

        const colors = {
            success: { bg: '#d1fae5', border: '#34d399', text: '#065f46', icon: '✅' },
            warning: { bg: '#fef3c7', border: '#fbbf24', text: '#92400e', icon: '⚠️' },
            error:   { bg: '#fee2e2', border: '#f87171', text: '#991b1b', icon: '❌' },
            info:    { bg: '#dbeafe', border: '#60a5fa', text: '#1e3a5f', icon: 'ℹ️' }
        };
        const c = colors[type] || colors.info;

        const toast = document.createElement('div');
        toast.className = 'offline-toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: ${c.bg};
            color: ${c.text};
            border: 1.5px solid ${c.border};
            border-radius: 14px;
            padding: 12px 20px;
            font-size: 0.875rem;
            font-weight: 600;
            z-index: 9999;
            max-width: 90vw;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            animation: slideUp 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        toast.innerHTML = `<span>${c.icon}</span><span>${message}</span>`;

        // Add animation
        const style = document.createElement('style');
        style.textContent = '@keyframes slideUp { from { transform: translateX(-50%) translateY(20px); opacity: 0; } to { transform: translateX(-50%) translateY(0); opacity: 1; } }';
        document.head.appendChild(style);

        document.body.appendChild(toast);
        setTimeout(() => { if (toast.parentNode) toast.remove(); }, 4000);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UI HELPERS: Update badge counters in nav
    // ─────────────────────────────────────────────────────────────────────────
    async function updateBadge() {
        const count = await getPendingCount();
        const badges = document.querySelectorAll('.offline-badge');
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        });

        // Update sync banner in dashboard
        const syncBanner = document.getElementById('offlineSyncBanner');
        if (syncBanner) {
            if (count > 0) {
                syncBanner.style.display = 'flex';
                const countEl = syncBanner.querySelector('.pending-count');
                if (countEl) countEl.textContent = count;
            } else {
                syncBanner.style.display = 'none';
            }
        }

        return count;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INIT: Setup connectivity listeners
    // ─────────────────────────────────────────────────────────────────────────
    function init() {
        openDB().then(() => {
            updateBadge();

            // Show/hide offline indicator
            updateConnectivityUI();

            window.addEventListener('online', () => {
                updateConnectivityUI();
                updateBadge().then(count => {
                    if (count > 0) {
                        showToast(`Koneksi pulih! Ada ${count} data yang belum tersinkronisasi.`, 'warning');
                    } else {
                        showToast('Koneksi internet tersambung kembali.', 'success');
                    }
                });
            });

            window.addEventListener('offline', () => {
                updateConnectivityUI();
                showToast('Koneksi terputus. Mode Offline aktif — data tetap bisa diinput.', 'warning');
            });
        }).catch(err => {
            console.warn('[OfflineManager] IndexedDB init failed:', err);
        });
    }

    function updateConnectivityUI() {
        const indicator = document.getElementById('offlineIndicator');
        if (indicator) {
            if (!navigator.onLine) {
                indicator.style.display = 'flex';
            } else {
                indicator.style.display = 'none';
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SERIALIZE FORM: Collect all form field values into a plain object
    // ─────────────────────────────────────────────────────────────────────────
    function serializeForm(form) {
        const payload = {};
        const data = new FormData(form);

        // We need to handle array fields specially
        for (const [key, value] of data.entries()) {
            const cleanKey = key.replace(/\[\]$/, '');
            if (key.endsWith('[]')) {
                if (!payload[cleanKey]) payload[cleanKey] = [];
                payload[cleanKey].push(value);
            } else {
                payload[cleanKey] = value;
            }
        }

        // Remove CSRF from stored payload (will be added fresh at sync time)
        delete payload['csrf_token'];
        return payload;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────
    return {
        init,
        openDB,
        addToQueue,
        getPendingQueue,
        getPendingCount,
        updateItemStatus,
        deleteItem,
        syncAll,
        saveAjaxCache,
        getAjaxCache,
        fetchWithCache,
        showToast,
        updateBadge,
        serializeForm
    };
})();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => OfflineManager.init());
} else {
    OfflineManager.init();
}
