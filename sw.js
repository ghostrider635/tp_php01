// sw.js — Service Worker FacturePro
const CACHE_STATIC  = 'facturepro-static-v1';
const CACHE_DATA    = 'facturepro-data-v1';
const SYNC_TAG      = 'sync-factures';

// Ressources à mettre en cache immédiatement à l'installation
const STATIC_ASSETS = [
    '/tp_php01/',
    '/tp_php01/index.php',
    '/tp_php01/assets/css/style.css',
    '/tp_php01/assets/js/pwa.js',
    '/tp_php01/assets/js/scanner.js',
    '/tp_php01/manifest.json',
    '/tp_php01/assets/icons/icon-192.svg',
    '/tp_php01/assets/icons/icon-512.svg',
    '/tp_php01/modules/facturation/nouvelle-facture.php',
    '/tp_php01/modules/produits/liste.php',
    '/tp_php01/auth/login.php',
];

// ── INSTALL : mise en cache des ressources statiques ──────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_STATIC).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

// ── ACTIVATE : nettoyage des anciens caches ───────────────────────────────────
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

// ── FETCH : stratégies de cache ───────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const url = event.request.url;

    // Ignorer les requêtes non-GET (POST, etc.)
    if (event.request.method !== 'GET') return;

    // Stale-While-Revalidate pour les endpoints JSON (données dynamiques)
    if (url.includes('/tp_php01/api/') || url.includes('.json')) {
        event.respondWith(staleWhileRevalidate(event.request, CACHE_DATA));
        return;
    }

    // Cache-First pour les ressources statiques (CSS, JS, images)
    if (url.includes('/assets/')) {
        event.respondWith(cacheFirst(event.request, CACHE_STATIC));
        return;
    }

    // Network-First pour les pages PHP (avec fallback cache)
    event.respondWith(networkFirstWithFallback(event.request));
});

// ── BACKGROUND SYNC : envoi des factures en attente ──────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === SYNC_TAG) {
        event.waitUntil(syncFacturesEnAttente());
    }
});

// ── STRATÉGIES ────────────────────────────────────────────────────────────────

// Stale-While-Revalidate : répond avec le cache, met à jour en arrière-plan
async function staleWhileRevalidate(request, cacheName) {
    const cache    = await caches.open(cacheName);
    const cached   = await cache.match(request);

    const fetchPromise = fetch(request)
        .then((response) => {
            if (response.ok) cache.put(request, response.clone());
            return response;
        })
        .catch(() => null);

    return cached || fetchPromise;
}

// Cache-First : cache d'abord, réseau si absent
async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;

    const response = await fetch(request);
    if (response.ok) {
        const cache = await caches.open(cacheName);
        cache.put(request, response.clone());
    }
    return response;
}

// Network-First avec fallback sur le cache
async function networkFirstWithFallback(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_STATIC);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response('<h2>Hors ligne</h2><p>Cette page n\'est pas disponible sans connexion.</p>', {
            headers: { 'Content-Type': 'text/html; charset=utf-8' }
        });
    }
}

// Envoyer les factures stockées dans IndexedDB vers le serveur
async function syncFacturesEnAttente() {
    const db       = await ouvrirDB();
    const factures = await getAllFromStore(db, 'pending_factures');

    for (const item of factures) {
        try {
            const response = await fetch('/tp_php01/api/factures.php', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(item.data),
            });

            if (response.ok) {
                await deleteFromStore(db, 'pending_factures', item.id);
                // Notifier les clients
                const clients = await self.clients.matchAll();
                clients.forEach((c) =>
                    c.postMessage({ type: 'SYNC_SUCCESS', id: item.id })
                );
            }
        } catch {
            // Sera réessayé au prochain sync
        }
    }
}

// ── IndexedDB helpers (dans le SW) ───────────────────────────────────────────
function ouvrirDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('facturepro', 1);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('pending_factures')) {
                db.createObjectStore('pending_factures', { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains('produits_cache')) {
                db.createObjectStore('produits_cache', { keyPath: 'code_barre' });
            }
        };
        req.onsuccess  = (e) => resolve(e.target.result);
        req.onerror    = (e) => reject(e.target.error);
    });
}

function getAllFromStore(db, storeName) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(storeName, 'readonly');
        const req = tx.objectStore(storeName).getAll();
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}

function deleteFromStore(db, storeName, id) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(storeName, 'readwrite');
        const req = tx.objectStore(storeName).delete(id);
        req.onsuccess = () => resolve();
        req.onerror   = (e) => reject(e.target.error);
    });
}
