<?php
require_once __DIR__ . '/auth/session.php';

exigerConnexion();

$user = utilisateurConnecte();
$role = $user['role'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-titre">
    <h2>Bienvenue, <?= htmlspecialchars($user['nom_complet']) ?></h2>
</div>

<div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:1rem;">

    <?php if (in_array($role, [ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
    <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php" class="btn btn-succes" style="font-size:1rem; padding:1rem 1.5rem;">
        🧾 Nouvelle Facture
    </a>
    <?php endif; ?>

    <?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
    <a href="<?= BASE_URL ?>/modules/produits/liste.php" class="btn" style="font-size:1rem; padding:1rem 1.5rem;">
        📦 Catalogue Produits
    </a>
    <a href="<?= BASE_URL ?>/modules/produits/enregistrer.php" class="btn btn-secondaire" style="font-size:1rem; padding:1rem 1.5rem;">
        ➕ Enregistrer Produit
    </a>
    <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php" class="btn btn-secondaire" style="font-size:1rem; padding:1rem 1.5rem;">
        📊 Rapport Journalier
    </a>
    <a href="<?= BASE_URL ?>/rapports/rapport-mensuel.php" class="btn btn-secondaire" style="font-size:1rem; padding:1rem 1.5rem;">
        📅 Rapport Mensuel
    </a>
    <?php endif; ?>

    <?php if ($role === ROLE_SUPER_ADMIN): ?>
    <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php" class="btn" style="font-size:1rem; padding:1rem 1.5rem; background:#7c3aed;">
        👥 Gestion des Comptes
    </a>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
