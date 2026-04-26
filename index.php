<?php
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/includes/fonctions-factures.php';
require_once __DIR__ . '/includes/fonctions-produits.php';

exigerConnexion();

$user = utilisateurConnecte();
$role = $user['role'] ?? '';

// Stats
$facturesAujourdhui = array_values(obtenirFacturesParDate(date('Y-m-d')));
$facturesMois       = array_values(obtenirFacturesParMois(date('Y-m')));
$totalJour          = array_sum(array_column($facturesAujourdhui, 'total_ttc'));
$totalMois          = array_sum(array_column($facturesMois, 'total_ttc'));
$produits           = obtenirTousProduits();
$stockBas           = array_filter($produits, fn($p) => $p['quantite_stock'] <= 5);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-titre">
    <h2>Dashboard</h2>
    <span style="font-size:0.82rem; color:var(--text-muted);"><?= date('l d F Y') ?></span>
</div>

<!-- STATS CARDS -->
<?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
<div class="dashboard-grid">
    <div class="dash-card blue">
        <span class="dash-card-icon">🧾</span>
        <span class="dash-card-label">Factures aujourd'hui</span>
        <span class="dash-card-value"><?= count($facturesAujourdhui) ?></span>
        <span class="dash-card-sub"><?= number_format($totalJour, 0, ',', ' ') ?> CDF</span>
    </div>
    <div class="dash-card green">
        <span class="dash-card-icon">📅</span>
        <span class="dash-card-label">Factures ce mois</span>
        <span class="dash-card-value"><?= count($facturesMois) ?></span>
        <span class="dash-card-sub"><?= number_format($totalMois, 0, ',', ' ') ?> CDF</span>
    </div>
    <div class="dash-card purple">
        <span class="dash-card-icon">📦</span>
        <span class="dash-card-label">Produits en catalogue</span>
        <span class="dash-card-value"><?= count($produits) ?></span>
        <span class="dash-card-sub"><?= count($stockBas) ?> en stock bas</span>
    </div>
    <div class="dash-card yellow">
        <span class="dash-card-icon">💰</span>
        <span class="dash-card-label">CA mensuel (TTC)</span>
        <span class="dash-card-value" style="font-size:1.1rem;"><?= number_format($totalMois, 0, ',', ' ') ?></span>
        <span class="dash-card-sub">CDF</span>
    </div>
</div>
<?php endif; ?>

<!-- ACTIONS RAPIDES -->
<div class="card">
    <div class="card-title">Actions rapides</div>
    <div class="quick-actions">

        <?php if (in_array($role, [ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
        <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php" class="action-btn">
            <span class="action-icon">🧾</span>
            Nouvelle Facture
        </a>
        <?php endif; ?>

        <?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
        <a href="<?= BASE_URL ?>/modules/produits/liste.php" class="action-btn">
            <span class="action-icon">📦</span>
            Catalogue
        </a>
        <a href="<?= BASE_URL ?>/modules/produits/enregistrer.php" class="action-btn">
            <span class="action-icon">➕</span>
            Ajouter Produit
        </a>
        <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php" class="action-btn">
            <span class="action-icon">📊</span>
            Rapport Jour
        </a>
        <a href="<?= BASE_URL ?>/rapports/rapport-mensuel.php" class="action-btn">
            <span class="action-icon">📈</span>
            Rapport Mois
        </a>
        <?php endif; ?>

        <?php if ($role === ROLE_SUPER_ADMIN): ?>
        <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php" class="action-btn">
            <span class="action-icon">👥</span>
            Comptes
        </a>
        <?php endif; ?>

    </div>
</div>

<!-- DERNIÈRES FACTURES -->
<?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN]) && !empty($facturesAujourdhui)): ?>
<div class="card">
    <div class="card-title">Factures d'aujourd'hui</div>
    <div class="tableau-wrapper">
    <table class="tableau">
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Heure</th>
                <th>Caissier</th>
                <th>Total TTC</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (array_slice(array_reverse($facturesAujourdhui), 0, 8) as $f): ?>
            <tr>
                <td><span class="badge badge-blue"><?= htmlspecialchars($f['id_facture']) ?></span></td>
                <td><?= htmlspecialchars($f['heure']) ?></td>
                <td><?= htmlspecialchars($f['caissier']) ?></td>
                <td><strong><?= number_format($f['total_ttc'], 0, ',', ' ') ?> CDF</strong></td>
                <td>
                    <a href="<?= BASE_URL ?>/modules/facturation/afficher-facture.php?id=<?= urlencode($f['id_facture']) ?>"
                       class="btn btn-secondaire" style="padding:0.25rem 0.6rem; font-size:0.78rem;">Voir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.tableau-wrapper -->
</div>
<?php endif; ?>

<!-- STOCK BAS -->
<?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN]) && !empty($stockBas)): ?>
<div class="card">
    <div class="card-title" style="color:var(--red-light);">&#9888; Stock bas (&le; 5 unités)</div>
    <div class="tableau-wrapper">
    <table class="tableau">
        <thead>
            <tr><th>Produit</th><th>Code-barres</th><th>Stock</th></tr>
        </thead>
        <tbody>
            <?php foreach ($stockBas as $p): ?>
            <tr class="stock-bas">
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= htmlspecialchars($p['code_barre']) ?></td>
                <td><span class="badge badge-red"><?= $p['quantite_stock'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.tableau-wrapper -->
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
