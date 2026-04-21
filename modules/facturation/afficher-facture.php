<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

exigerConnexion();

$idFacture = trim($_GET['id'] ?? '');
$facture   = $idFacture ? trouverFactureParId($idFacture) : null;

if (!$facture) {
    $_SESSION['erreur_acces'] = "Facture introuvable.";
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="facture-impression" id="facture">
    <div class="facture-entete">
        <h2>FACTURE</h2>
        <p><strong>N° :</strong> <?= htmlspecialchars($facture['id_facture']) ?></p>
        <p><strong>Date :</strong> <?= htmlspecialchars($facture['date']) ?> à <?= htmlspecialchars($facture['heure']) ?></p>
        <p><strong>Caissier :</strong> <?= htmlspecialchars($facture['caissier']) ?></p>
    </div>

    <table class="tableau">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Prix unit. HT</th>
                <th>Qté</th>
                <th>Sous-total HT</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facture['articles'] as $article): ?>
            <tr>
                <td><?= htmlspecialchars($article['nom']) ?></td>
                <td><?= number_format($article['prix_unitaire_ht'], 0, ',', ' ') ?> CDF</td>
                <td><?= $article['quantite'] ?></td>
                <td><?= number_format($article['sous_total_ht'], 0, ',', ' ') ?> CDF</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3"><strong>Total HT</strong></td><td><strong><?= number_format($facture['total_ht'], 0, ',', ' ') ?> CDF</strong></td></tr>
            <tr><td colspan="3">TVA (16%)</td><td><?= number_format($facture['tva'], 0, ',', ' ') ?> CDF</td></tr>
            <tr><td colspan="3"><strong>Net à payer</strong></td><td><strong><?= number_format($facture['total_ttc'], 0, ',', ' ') ?> CDF</strong></td></tr>
        </tfoot>
    </table>

    <p class="facture-merci">Merci pour votre achat !</p>
</div>

<div class="actions-impression">
    <button onclick="window.print()" class="btn">🖨 Imprimer</button>
    <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php" class="btn btn-secondaire">+ Nouvelle facture</a>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
