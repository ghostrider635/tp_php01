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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
