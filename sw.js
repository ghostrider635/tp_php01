// sw.js — Service Worker FacturePro
const CACHE_STATIC = 'facturepro-static-v1';
const CACHE_DATA   = 'facturepro-data-v1';
const SYNC_TAG     = 'sync-factures';

// Cache minimal à l'install — ne pas surcharger le premier chargement
const STATIC_ASSETS = [
    '/tp_php01/assets/css/style.css',
    '/tp_php01/assets/js/pwa.js',
    '/tp_php01/manifest.json',
];

// ── INSTALL ───────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_STATIC).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// ── ACTIVATE : nettoyage anciens caches ───────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((k) => k !== CACHE_STATIC && k !== CACHE_DATA)
                    .map((k) => caches.delete(k))
            )
        )
    );
    self.clients.claim();
});

// ── FETCH ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const url = event.request.url;

    if (event.request.method !== 'GET') return;

    // Stale-While-Revalidate pour les API JSON
    if (url.includes('/tp_php01/api/') || url.includes('.json')) {
        event.respondWith(staleWhileRevalidate(event.request, CACHE_DATA));
        return;
    }

    // Cache-First pour CSS/JS/images
    if (url.includes('/assets/')) {
        event.respondWith(cacheFirst(event.request, CACHE_STATIC));
        return;
    }

    // Network-First pour les pages PHP
    event.respondWith(networkFirstWithFallback(event.request));
});

// ── BACKGROUND SYNC ───────────────────────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === SYNC_TAG) {
        event.waitUntil(syncFacturesEnAttente());
    }
});

// ── STRATÉGIES ────────────────────────────────────────────────────────────────
async function staleWhileRevalidate(request, cacheName) {
    const cache  = await caches.open(cacheName);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request)
        .then((res) => { if (res.ok) cache.put(request, res.clone()); return res; })
        .catch(() => null);

    return cached || fetchPromise;
}

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;

    const res = await fetch(request);
    if (res.ok) {
        const cache = await caches.open(cacheName);
        cache.put(request, res.clone());
    }
    return res;
}

async function networkFirstWithFallback(request) {
    try {
        const res = await fetch(request);
        if (res.ok) {
            const cache = await caches.open(CACHE_STATIC);
            cache.put(request, res.clone());
        }
        return res;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response(
            '<h2>Hors ligne</h2><p>Cette page n\'est pas disponible sans connexion.</p>',
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
        );
    }
}

// ── SYNC FACTURES ─────────────────────────────────────────────────────────────
async function syncFacturesEnAttente() {
    const db       = await ouvrirDB();
    const factures = await getAllFromStore(db, 'pending_factures');

    for (const item of factures) {
        try {
            const res = await fetch('/tp_php01/api/factures.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(item.data),
            });
            if (res.ok) {
                await deleteFromStore(db, 'pending_factures', item.id);
                const clients = await self.clients.matchAll();
                clients.forEach((c) => c.postMessage({ type: 'SYNC_SUCCESS', id: item.id }));
            }
        } catch { /* réessayé au prochain sync */ }
    }
}

// ── IndexedDB helpers ─────────────────────────────────────────────────────────
function ouvrirDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('facturepro', 1);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('pending_factures'))
                db.createObjectStore('pending_factures', { keyPath: 'id', autoIncrement: true });
            if (!db.objectStoreNames.contains('produits_cache'))
                db.createObjectStore('produits_cache', { keyPath: 'code_barre' });
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}

function getAllFromStore(db, store) {
    return new Promise((resolve, reject) => {
        const req = db.transaction(store, 'readonly').objectStore(store).getAll();
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}

function deleteFromStore(db, store, id) {
    return new Promise((resolve, reject) => {
        const req = db.transaction(store, 'readwrite').objectStore(store).delete(id);
        req.onsuccess = () => resolve();
        req.onerror   = (e) => reject(e.target.error);
    });
}
