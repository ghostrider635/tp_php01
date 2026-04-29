// pwa.js — Gestion PWA FacturePro
(function () {
    'use strict';

    // ── Enregistrement du Service Worker ─────────────────────────────────────
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker
                .register('/tp_php01/sw.js', { scope: '/tp_php01/' })
                .then((reg) => {
                    console.log('[PWA] Service Worker enregistré', reg.scope);

                    // Écouter les messages du SW (sync réussie)
                    navigator.serviceWorker.addEventListener('message', (e) => {
                        if (e.data?.type === 'SYNC_SUCCESS') {
                            afficherBanner('✅ Facture synchronisée avec le serveur.', 'succes');
                        }
                    });
                })
                .catch((err) => console.warn('[PWA] SW non enregistré :', err));
        });
    }

    // ── Bannière online / offline ─────────────────────────────────────────────
    function afficherBanner(message, type = 'info') {
        let banner = document.getElementById('pwa-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'pwa-banner';
            banner.style.cssText = [
                'position:fixed', 'bottom:1rem', 'left:50%',
                'transform:translateX(-50%)', 'z-index:9999',
                'padding:0.6rem 1.2rem', 'border-radius:8px',
                'font-size:0.88rem', 'font-weight:500',
                'box-shadow:0 4px 16px rgba(0,0,0,0.4)',
                'transition:opacity 0.4s', 'max-width:90vw',
                'text-align:center'
            ].join(';');
            document.body.appendChild(banner);
        }

        const styles = {
            succes:  'background:#238636;color:#fff',
            erreur:  'background:#da3633;color:#fff',
            info:    'background:#1d6fa4;color:#fff',
            warning: 'background:#d29922;color:#111',
        };

        banner.style.cssText += ';' + (styles[type] || styles.info);
        banner.textContent = message;
        banner.style.opacity = '1';

        clearTimeout(banner._timer);
        banner._timer = setTimeout(() => { banner.style.opacity = '0'; }, 4000);
    }

    window.addEventListener('online',  () => {
        afficherBanner('🌐 Connexion rétablie.', 'succes');
        // Déclencher Background Sync si supporté
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.ready.then((reg) =>
                reg.sync.register('sync-factures').catch(() => {})
            );
        }
    });

    window.addEventListener('offline', () => {
        afficherBanner('📴 Vous êtes hors ligne. Les factures seront synchronisées à la reconnexion.', 'warning');
    });

    // ── IndexedDB ─────────────────────────────────────────────────────────────
    let _db = null;

    function ouvrirDB() {
        if (_db) return Promise.resolve(_db);
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
            req.onsuccess  = (e) => { _db = e.target.result; resolve(_db); };
            req.onerror    = (e) => reject(e.target.error);
        });
    }

    function dbPut(storeName, data) {
        return ouvrirDB().then((db) => new Promise((resolve, reject) => {
            const tx  = db.transaction(storeName, 'readwrite');
            const req = tx.objectStore(storeName).put(data);
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror   = (e) => reject(e.target.error);
        }));
    }

    function dbGetAll(storeName) {
        return ouvrirDB().then((db) => new Promise((resolve, reject) => {
            const tx  = db.transaction(storeName, 'readonly');
            const req = tx.objectStore(storeName).getAll();
            req.onsuccess = (e) => resolve(e.target.result);
            req.onerror   = (e) => reject(e.target.error);
        }));
    }

    function dbDelete(storeName, id) {
        return ouvrirDB().then((db) => new Promise((resolve, reject) => {
            const tx  = db.transaction(storeName, 'readwrite');
            const req = tx.objectStore(storeName).delete(id);
            req.onsuccess = () => resolve();
            req.onerror   = (e) => reject(e.target.error);
        }));
    }

    // ── Mise en cache des produits dans IndexedDB ─────────────────────────────
    async function cacherProduits() {
        try {
            const res      = await fetch('/tp_php01/api/produits.php');
            if (!res.ok) return;
            const produits = await res.json();
            for (const p of produits) await dbPut('produits_cache', p);
            console.log('[PWA] Produits mis en cache IndexedDB :', produits.length);
        } catch {
            console.log('[PWA] Impossible de mettre à jour le cache produits (hors ligne ?)');
        }
    }

    // ── API publique exposée sur window.PWA ───────────────────────────────────
    window.PWA = {
        afficherBanner,
        ouvrirDB,
        dbPut,
        dbGetAll,
        dbDelete,
        cacherProduits,

        // Chercher un produit dans IndexedDB (hors ligne)
        async trouverProduit(codeBarre) {
            const produits = await dbGetAll('produits_cache');
            return produits.find((p) => p.code_barre === codeBarre) || null;
        },

        // Sauvegarder une facture en attente (hors ligne)
        async sauvegarderFactureEnAttente(articles) {
            const id = await dbPut('pending_factures', {
                data:      { articles },
                timestamp: Date.now(),
            });
            afficherBanner('💾 Facture sauvegardée localement. Elle sera envoyée à la reconnexion.', 'warning');
            return id;
        },

        // Compter les factures en attente
        async compterFacturesEnAttente() {
            const items = await dbGetAll('pending_factures');
            return items.length;
        },
    };

    // Mettre en cache les produits en idle (ne pas bloquer le chargement)
    if (navigator.onLine) {
        document.addEventListener('DOMContentLoaded', () => {
            // requestIdleCallback si dispo, sinon setTimeout 3s
            if ('requestIdleCallback' in window) {
                requestIdleCallback(cacherProduits, { timeout: 5000 });
            } else {
                setTimeout(cacherProduits, 3000);
            }
        });
    }

    // Afficher le badge de factures en attente
    document.addEventListener('DOMContentLoaded', async () => {
        const count = await window.PWA.compterFacturesEnAttente();
        if (count > 0) {
            afficherBanner(`⏳ ${count} facture(s) en attente de synchronisation.`, 'warning');
        }
    });

})();
