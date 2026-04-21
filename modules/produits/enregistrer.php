<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

exigerRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$erreurs  = [];
$succes   = '';
$produitExistant = null;
$codeBarre = trim($_GET['code_barre'] ?? $_POST['code_barre'] ?? '');

// Traitement mise à jour du stock (doit être avant tout affichage)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maj_stock'])) {
    $cb  = trim($_POST['code_barre'] ?? '');
    $qte = (int) ($_POST['nouveau_stock'] ?? 0);
    if ($cb && $qte >= 0) {
        mettreAJourStock($cb, $qte);
        $succes    = "Stock mis à jour avec succès.";
        $codeBarre = $cb;
    }
}

// Traitement du formulaire d'enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer'])) {
    $donnees = [
        'code_barre'       => trim($_POST['code_barre'] ?? ''),
        'nom'              => trim($_POST['nom'] ?? ''),
        'prix_unitaire_ht' => $_POST['prix_unitaire_ht'] ?? '',
        'date_expiration'  => trim($_POST['date_expiration'] ?? ''),
        'quantite_stock'   => $_POST['quantite_stock'] ?? '',
    ];

    $erreurs = validerDonneesProduit($donnees);

    if (empty($erreurs)) {
        if (enregistrerProduit($donnees)) {
            $succes    = "Produit enregistré avec succès.";
            $codeBarre = '';
            $donnees   = [];
        } else {
            $erreurs[] = "Ce code-barres est déjà enregistré.";
        }
    }
}

// Vérification si le produit existe (après traitements pour avoir les données à jour)
if ($codeBarre) {
    $produitExistant = trouverProduitParCodeBarre($codeBarre);
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-titre">
    <h2>Enregistrement d'un Produit</h2>
</div>

<?php if ($succes): ?>
    <div class="alerte succes"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<!-- Scanner ZXing -->
<div class="scanner-zone">
    <h3>Scanner le code-barres</h3>
    <div id="video-container">
        <video id="video" width="300" height="200" style="border:1px solid #ccc;"></video>
    </div>
    <button id="btn-scanner" class="btn">📷 Activer la caméra</button>
    <button id="btn-arreter" class="btn btn-secondaire" style="display:none;">⏹ Arrêter</button>
    <p id="scanner-resultat"></p>
</div>

<?php if ($produitExistant): ?>
    <!-- Produit déjà connu -->
    <div class="alerte info">
        <strong>Produit déjà enregistré :</strong><br>
        Nom : <?= htmlspecialchars($produitExistant['nom']) ?><br>
        Prix HT : <?= number_format($produitExistant['prix_unitaire_ht'], 0, ',', ' ') ?> CDF<br>
        Stock : <?= (int) $produitExistant['quantite_stock'] ?> unités<br>
        Expiration : <?= htmlspecialchars($produitExistant['date_expiration']) ?>
    </div>

    <!-- Mise à jour du stock -->
    <form method="POST" action="">
        <input type="hidden" name="code_barre" value="<?= htmlspecialchars($produitExistant['code_barre']) ?>">
        <div class="form-groupe">
            <label for="nouveau_stock">Nouveau stock</label>
            <input type="number" id="nouveau_stock" name="nouveau_stock" min="0"
                   value="<?= (int) $produitExistant['quantite_stock'] ?>" required>
        </div>
        <button type="submit" name="maj_stock" class="btn">Mettre à jour le stock</button>
    </form>

<?php else: ?>
    <!-- Formulaire d'enregistrement -->
    <?php if (!empty($erreurs)): ?>
        <ul class="erreurs-liste">
            <?php foreach ($erreurs as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="" id="form-produit">
        <div class="form-groupe">
            <label for="code_barre">Code-barres</label>
            <input type="text" id="code_barre" name="code_barre"
                   value="<?= htmlspecialchars($donnees['code_barre'] ?? $codeBarre) ?>" required>
        </div>
        <div class="form-groupe">
            <label for="nom">Nom du produit</label>
            <input type="text" id="nom" name="nom"
                   value="<?= htmlspecialchars($donnees['nom'] ?? '') ?>" required>
        </div>
        <div class="form-groupe">
            <label for="prix_unitaire_ht">Prix unitaire HT (CDF)</label>
            <input type="number" id="prix_unitaire_ht" name="prix_unitaire_ht" min="1" step="0.01"
                   value="<?= htmlspecialchars($donnees['prix_unitaire_ht'] ?? '') ?>" required>
        </div>
        <div class="form-groupe">
            <label for="date_expiration">Date d'expiration (AAAA-MM-JJ)</label>
            <input type="date" id="date_expiration" name="date_expiration"
                   value="<?= htmlspecialchars($donnees['date_expiration'] ?? '') ?>" required>
        </div>
        <div class="form-groupe">
            <label for="quantite_stock">Quantité initiale en stock</label>
            <input type="number" id="quantite_stock" name="quantite_stock" min="0"
                   value="<?= htmlspecialchars($donnees['quantite_stock'] ?? '') ?>" required>
        </div>
        <button type="submit" name="enregistrer" class="btn">Enregistrer le produit</button>
    </form>
<?php endif; ?>

<script src="<?= BASE_URL ?>/assets/js/scanner.js"></script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
