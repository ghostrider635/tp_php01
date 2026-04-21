<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/session.php';
$user = utilisateurConnecte();
$role = $user['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Facturation</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-brand">🛒 Système de Facturation</div>
    <nav class="header-nav">
        <a href="<?= BASE_URL ?>/index.php">Accueil</a>

        <?php if (in_array($role, [ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
            <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php">Nouvelle Facture</a>
        <?php endif; ?>

        <?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
            <a href="<?= BASE_URL ?>/modules/produits/liste.php">Produits</a>
            <a href="<?= BASE_URL ?>/modules/produits/enregistrer.php">Enregistrer Produit</a>
            <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php">Rapport Journalier</a>
            <a href="<?= BASE_URL ?>/rapports/rapport-mensuel.php">Rapport Mensuel</a>
        <?php endif; ?>

        <?php if ($role === ROLE_SUPER_ADMIN): ?>
            <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php">Comptes</a>
        <?php endif; ?>

        <span class="header-user">
            👤 <?= htmlspecialchars($user['nom_complet'] ?? '') ?>
            (<?= htmlspecialchars($role) ?>)
        </span>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="btn-logout">Déconnexion</a>
    </nav>
</header>

<?php if (!empty($_SESSION['erreur_acces'])): ?>
    <div class="alerte erreur"><?= htmlspecialchars($_SESSION['erreur_acces']) ?></div>
    <?php unset($_SESSION['erreur_acces']); ?>
<?php endif; ?>

<main class="contenu-principal">
