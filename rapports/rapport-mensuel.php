<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';

exigerRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$mois     = $_GET['mois'] ?? date('Y-m');
$factures = array_values(obtenirFacturesParMois($mois));

$totalMois = array_sum(array_column($factures, 'total_ttc'));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-titre">
    <h2>Rapport Mensuel</h2>
</div>

<form method="GET" action="" class="form-inline">
    <label for="mois">Mois :</label>
    <input type="month" id="mois" name="mois" value="<?= htmlspecialchars($mois) ?>">
    <button type="submit" class="btn">Afficher</button>
</form>

<p><strong>Nombre de factures :</strong> <?= count($factures) ?></p>
<p><strong>Total encaissé (TTC) :</strong> <?= number_format($totalMois, 0, ',', ' ') ?> CDF</p>

<?php if (empty($factures)): ?>
    <p class="vide">Aucune facture pour ce mois.</p>
<?php else: ?>
    <div class="tableau-wrapper">
    <table class="tableau">
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Date</th>
                <th>Caissier</th>
                <th>Total TTC</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['id_facture']) ?></td>
                <td><?= htmlspecialchars($f['date']) ?></td>
                <td><?= htmlspecialchars($f['caissier']) ?></td>
                <td><?= number_format($f['total_ttc'], 0, ',', ' ') ?> CDF</td>
                <td><a href="<?= BASE_URL ?>/modules/facturation/afficher-facture.php?id=<?= urlencode($f['id_facture']) ?>">Voir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.tableau-wrapper -->
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
