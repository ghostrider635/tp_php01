const cacheName = 'v1-cache-site';
const assetsToCache = [
  '/',
  '/index.php',
  '/css/style.css',
  '/js/script.js',
  '/offline.php' // Une page de secours
];

// Installation : Mise en cache des fichiers
self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open(cacheName).then((cache) => cache.addAll(assetsToCache))
  );
});

// Stratégie : Network first, fallback to Cache
self.addEventListener('fetch', (e) => {
  e.respondWith(
    fetch(e.request).catch(() => caches.match(e.request))
  );
});