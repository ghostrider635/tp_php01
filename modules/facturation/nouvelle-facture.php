<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

exigerRole([ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN]);

// Initialiser le panier en session
if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];

$erreur = '';
$succes = '';

// Ajouter un article au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_article'])) {
    $codeBarre = trim($_POST['code_barre'] ?? '');
    $quantite  = (int) ($_POST['quantite'] ?? 0);

    if (empty($codeBarre) || $quantite <= 0) {
        $erreur = "Code-barres et quantité sont obligatoires.";
    } else {
        $produit = trouverProduitParCodeBarre($codeBarre);
        if (!$produit) {
            $erreur = "Produit inconnu. Veuillez demander à un Manager de l'enregistrer.";
        } elseif ($quantite > $produit['quantite_stock']) {
            $erreur = "Stock insuffisant. Stock disponible : " . $produit['quantite_stock'] . " unités.";
        } else {
            // Vérifier si déjà dans le panier
            $dejaDans = false;
            foreach ($_SESSION['panier'] as &$ligne) {
                if ($ligne['code_barre'] === $codeBarre) {
                    $nouvelleQte = $ligne['quantite'] + $quantite;
                    if ($nouvelleQte > $produit['quantite_stock']) {
                        $erreur = "Stock insuffisant pour cette quantité totale.";
                    } else {
                        $ligne['quantite']     = $nouvelleQte;
                        $ligne['sous_total_ht'] = $ligne['prix_unitaire_ht'] * $nouvelleQte;
                    }
                    $dejaDans = true;
                    break;
                }
            }
            if (!$dejaDans && empty($erreur)) {
                $_SESSION['panier'][] = [
                    'code_barre'      => $produit['code_barre'],
                    'nom'             => $produit['nom'],
                    'prix_unitaire_ht'=> $produit['prix_unitaire_ht'],
                    'quantite'        => $quantite,
                    'sous_total_ht'   => $produit['prix_unitaire_ht'] * $quantite,
                ];
            }
        }
    }
}

// Supprimer un article du panier
if (isset($_GET['supprimer'])) {
    $idx = (int) $_GET['supprimer'];
    array_splice($_SESSION['panier'], $idx, 1);
    header('Location: ' . BASE_URL . '/modules/facturation/nouvelle-facture.php');
    exit;
}

// Vider le panier
if (isset($_GET['vider'])) {
    $_SESSION['panier'] = [];
    header('Location: ' . BASE_URL . '/modules/facturation/nouvelle-facture.php');
    exit;
}

// Valider la facture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_facture'])) {
    if (empty($_SESSION['panier'])) {
        $erreur = "Le panier est vide.";
    } else {
        // Décrémenter les stocks
        foreach ($_SESSION['panier'] as $ligne) {
            decrementerStock($ligne['code_barre'], $ligne['quantite']);
        }
        $user    = utilisateurConnecte();
        $facture = sauvegarderFacture($_SESSION['panier'], $user['identifiant']);
        $_SESSION['panier'] = [];
        header('Location: ' . BASE_URL . '/modules/facturation/afficher-facture.php?id=' . urlencode($facture['id_facture']));
        exit;
    }
}

[$totalHT, $tva, $totalTTC] = calculerTotaux($_SESSION['panier']);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-titre">
    <h2>Nouvelle Facture</h2>
</div>

<?php if ($erreur): ?>
    <div class="alerte erreur"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<!-- Scanner ZXing -->
<div class="scanner-zone">
    <h3>Scanner un article</h3>
    <video id="video" width="300" height="200" style="border:1px solid #ccc;"></video><br>
    <button id="btn-scanner" class="btn">📷 Activer la caméra</button>
    <button id="btn-arreter" class="btn btn-secondaire" style="display:none;">⏹ Arrêter</button>
    <p id="scanner-resultat"></p>
</div>

<!-- Formulaire ajout article -->
<form method="POST" action="" class="form-inline">
    <div class="form-groupe">
        <label for="code_barre">Code-barres</label>
        <input type="text" id="code_barre" name="code_barre"
               value="<?= htmlspecialchars($_POST['code_barre'] ?? '') ?>" required>
    </div>
    <div class="form-groupe">
        <label for="quantite">Quantité</label>
        <input type="number" id="quantite" name="quantite" min="1" value="1" required>
    </div>
    <button type="submit" name="ajouter_article" class="btn">Ajouter</button>
</form>

<!-- Panier -->
<?php if (!empty($_SESSION['panier'])): ?>
<div class="facture-apercu">
    <h3>Articles</h3>
    <div class="tableau-wrapper">
    <table class="tableau">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Prix unit. HT</th>
                <th>Qté</th>
                <th>Sous-total HT</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['panier'] as $i => $ligne): ?>
            <tr>
                <td><?= htmlspecialchars($ligne['nom']) ?></td>
                <td><?= number_format($ligne['prix_unitaire_ht'], 0, ',', ' ') ?> CDF</td>
                <td><?= $ligne['quantite'] ?></td>
                <td><?= number_format($ligne['sous_total_ht'], 0, ',', ' ') ?> CDF</td>
                <td><a href="?supprimer=<?= $i ?>" class="btn-danger">✕</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3"><strong>Total HT</strong></td><td colspan="2"><strong><?= number_format($totalHT, 0, ',', ' ') ?> CDF</strong></td></tr>
            <tr><td colspan="3">TVA (16%)</td><td colspan="2"><?= number_format($tva, 0, ',', ' ') ?> CDF</td></tr>
            <tr><td colspan="3"><strong>Net à payer</strong></td><td colspan="2"><strong><?= number_format($totalTTC, 0, ',', ' ') ?> CDF</strong></td></tr>
        </tfoot>
    </table>
    </div><!-- /.tableau-wrapper -->

    <form method="POST" action="" style="display:inline;">
        <button type="submit" name="valider_facture" class="btn btn-succes">✔ Valider la facture</button>
    </form>
    <a href="?vider=1" class="btn btn-danger">🗑 Vider le panier</a>
</div>
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/scanner.js"></script>
<script>
// Mode hors ligne : utiliser IndexedDB pour trouver les produits
(function () {
    var form       = document.querySelector('form[method="POST"] input[name="ajouter_article"]');
    var inputCode  = document.getElementById('code_barre');
    var inputQte   = document.getElementById('quantite');

    if (!form || !inputCode || !window.PWA) return;

    // Si hors ligne, intercepter la soumission du formulaire
    document.querySelector('form.form-inline').addEventListener('submit', async function (e) {
        if (navigator.onLine) return; // laisser PHP gérer

        e.preventDefault();
        var code = inputCode.value.trim();
        var qte  = parseInt(inputQte.value, 10);

        if (!code || qte <= 0) {
            PWA.afficherBanner('Code-barres et quantité requis.', 'erreur');
            return;
        }

        var produit = await PWA.trouverProduit(code);
        if (!produit) {
            PWA.afficherBanner('Produit inconnu dans le cache local.', 'erreur');
            return;
        }
        if (qte > produit.quantite_stock) {
            PWA.afficherBanner('Stock insuffisant (cache local : ' + produit.quantite_stock + ' unités).', 'erreur');
            return;
        }

        // Stocker dans sessionStorage pour affichage immédiat
        var panier = JSON.parse(sessionStorage.getItem('panier_offline') || '[]');
        var idx    = panier.findIndex(function (l) { return l.code_barre === code; });
        if (idx >= 0) {
            panier[idx].quantite      += qte;
            panier[idx].sous_total_ht  = panier[idx].prix_unitaire_ht * panier[idx].quantite;
        } else {
            panier.push({
                code_barre:       produit.code_barre,
                nom:              produit.nom,
                prix_unitaire_ht: produit.prix_unitaire_ht,
                quantite:         qte,
                sous_total_ht:    produit.prix_unitaire_ht * qte,
            });
        }
        sessionStorage.setItem('panier_offline', JSON.stringify(panier));
        afficherPanierOffline(panier);
        inputCode.value = '';
        inputQte.value  = '1';
        PWA.afficherBanner('Article ajouté (mode hors ligne).', 'info');
    });

    // Valider la facture hors ligne
    document.addEventListener('click', async function (e) {
        if (!e.target.matches('[data-offline-valider]')) return;
        var panier = JSON.parse(sessionStorage.getItem('panier_offline') || '[]');
        if (!panier.length) { PWA.afficherBanner('Panier vide.', 'erreur'); return; }

        await PWA.sauvegarderFactureEnAttente(panier);
        sessionStorage.removeItem('panier_offline');
        afficherPanierOffline([]);

        // Tenter Background Sync
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            navigator.serviceWorker.ready.then(function (reg) {
                reg.sync.register('sync-factures').catch(function () {});
            });
        }
    });

    function afficherPanierOffline(panier) {
        var zone = document.getElementById('panier-offline');
        if (!zone) {
            zone = document.createElement('div');
            zone.id = 'panier-offline';
            zone.className = 'facture-apercu';
            document.querySelector('.page-body').appendChild(zone);
        }

        if (!panier.length) { zone.innerHTML = ''; return; }

        var tva  = 0.16;
        var ht   = panier.reduce(function (s, l) { return s + l.sous_total_ht; }, 0);
        var ttc  = ht + Math.round(ht * tva);

        var lignes = panier.map(function (l, i) {
            return '<tr>' +
                '<td>' + l.nom + '</td>' +
                '<td>' + l.prix_unitaire_ht.toLocaleString('fr') + ' CDF</td>' +
                '<td>' + l.quantite + '</td>' +
                '<td>' + l.sous_total_ht.toLocaleString('fr') + ' CDF</td>' +
                '<td><button onclick="supprimerLigneOffline(' + i + ')" class="btn-danger">✕</button></td>' +
                '</tr>';
        }).join('');

        zone.innerHTML = '<h3>Articles (hors ligne)</h3>' +
            '<div class="tableau-wrapper"><table class="tableau"><thead><tr>' +
            '<th>Désignation</th><th>Prix HT</th><th>Qté</th><th>Sous-total</th><th></th>' +
            '</tr></thead><tbody>' + lignes + '</tbody>' +
            '<tfoot><tr><td colspan="3"><strong>Total HT</strong></td><td colspan="2"><strong>' + ht.toLocaleString('fr') + ' CDF</strong></td></tr>' +
            '<tr><td colspan="3">TVA 16%</td><td colspan="2">' + Math.round(ht * tva).toLocaleString('fr') + ' CDF</td></tr>' +
            '<tr><td colspan="3"><strong>Net à payer</strong></td><td colspan="2"><strong>' + ttc.toLocaleString('fr') + ' CDF</strong></td></tr>' +
            '</tfoot></table></div>' +
            '<button data-offline-valider class="btn btn-succes" style="margin-top:0.75rem">💾 Sauvegarder (hors ligne)</button>';
    }

    window.supprimerLigneOffline = function (idx) {
        var panier = JSON.parse(sessionStorage.getItem('panier_offline') || '[]');
        panier.splice(idx, 1);
        sessionStorage.setItem('panier_offline', JSON.stringify(panier));
        afficherPanierOffline(panier);
    };

    // Restaurer le panier offline au chargement si hors ligne
    if (!navigator.onLine) {
        var saved = JSON.parse(sessionStorage.getItem('panier_offline') || '[]');
        if (saved.length) afficherPanierOffline(saved);
        PWA.afficherBanner('📴 Mode hors ligne actif.', 'warning');
    }
})();
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
