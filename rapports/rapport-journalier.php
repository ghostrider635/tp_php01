<?php
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';

exigerRole([ROLE_MANAGER, ROLE_SUPER_ADMIN]);

$date     = $_GET['date'] ?? date('Y-m-d');
$factures = array_values(obtenirFacturesParDate($date));

$totalJour = array_sum(array_column($factures, 'total_ttc'));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-titre">
    <h2>Rapport Journalier</h2>
</div>

<form method="GET" action="" class="form-inline">
    <label for="date">Date :</label>
    <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>">
    <button type="submit" class="btn">Afficher</button>
</form>

<p><strong>Nombre de factures :</strong> <?= count($factures) ?></p>
<p><strong>Total encaissé (TTC) :</strong> <?= number_format($totalJour, 0, ',', ' ') ?> CDF</p>

<?php if (empty($factures)): ?>
    <p class="vide">Aucune facture pour cette date.</p>
<?php else: ?>
    <table class="tableau">
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Heure</th>
                <th>Caissier</th>
                <th>Total HT</th>
                <th>TVA</th>
                <th>Total TTC</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($factures as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['id_facture']) ?></td>
                <td><?= htmlspecialchars($f['heure']) ?></td>
                <td><?= htmlspecialchars($f['caissier']) ?></td>
                <td><?= number_format($f['total_ht'], 0, ',', ' ') ?> CDF</td>
                <td><?= number_format($f['tva'], 0, ',', ' ') ?> CDF</td>
                <td><?= number_format($f['total_ttc'], 0, ',', ' ') ?> CDF</td>
                <td><a href="<?= BASE_URL ?>/modules/facturation/afficher-facture.php?id=<?= urlencode($f['id_facture']) ?>">Voir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
