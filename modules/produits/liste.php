<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

exigerRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$produits = obtenirTousProduits();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-titre">
    <h2>Catalogue des Produits</h2>
    <a href="<?= BASE_URL ?>/modules/produits/enregistrer.php" class="btn">+ Nouveau produit</a>
</div>

<?php if (empty($produits)): ?>
    <p class="vide">Aucun produit enregistré.</p>
<?php else: ?>
    <table class="tableau">
        <thead>
            <tr>
                <th>Code-barres</th>
                <th>Nom</th>
                <th>Prix HT (CDF)</th>
                <th>Stock</th>
                <th>Expiration</th>
                <th>Enregistré le</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $p): ?>
            <tr class="<?= $p['quantite_stock'] <= 5 ? 'stock-bas' : '' ?>">
                <td><?= htmlspecialchars($p['code_barre']) ?></td>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= number_format($p['prix_unitaire_ht'], 0, ',', ' ') ?></td>
                <td><?= $p['quantite_stock'] ?></td>
                <td><?= htmlspecialchars($p['date_expiration']) ?></td>
                <td><?= htmlspecialchars($p['date_enregistrement']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
