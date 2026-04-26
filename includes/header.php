<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/session.php';
$user = utilisateurConnecte();
$role = $user['role'] ?? '';

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
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
<div class="app-layout">

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        🛒 <span>FacturePro</span>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>/index.php"
           class="<?= $currentPage === 'index.php' && $currentDir !== 'facturation' ? 'active' : '' ?>">
            <span class="nav-icon">⊞</span>
            <span>Dashboard</span>
        </a>

        <?php if (in_array($role, [ROLE_CAISSIER, ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
        <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php"
           class="<?= $currentPage === 'nouvelle-facture.php' ? 'active' : '' ?>">
            <span class="nav-icon">🧾</span>
            <span>Nouvelle Facture</span>
        </a>
        <?php endif; ?>

        <?php if (in_array($role, [ROLE_MANAGER, ROLE_SUPER_ADMIN])): ?>
        <div class="sidebar-section">Gestion</div>

        <a href="<?= BASE_URL ?>/modules/produits/liste.php"
           class="<?= in_array($currentPage, ['liste.php','enregistrer.php']) && $currentDir === 'produits' ? 'active' : '' ?>">
            <span class="nav-icon">📦</span>
            <span>Produits</span>
        </a>

        <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php"
           class="<?= in_array($currentPage, ['rapport-journalier.php','rapport-mensuel.php']) ? 'active' : '' ?>">
            <span class="nav-icon">📊</span>
            <span>Rapports</span>
        </a>
        <?php endif; ?>

        <?php if ($role === ROLE_SUPER_ADMIN): ?>
        <div class="sidebar-section">Admin</div>
        <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php"
           class="<?= $currentDir === 'admin' ? 'active' : '' ?>">
            <span class="nav-icon">👥</span>
            <span>Comptes</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            👤 <span><?= htmlspecialchars($user['nom_complet'] ?? '') ?>
            <br><small><?= htmlspecialchars($role) ?></small></span>
        </div>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="btn-logout">
            ⏻ <span>Déconnexion</span>
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main-content">

<div class="topbar">
    <button class="hamburger" id="hamburger-btn" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
    <span class="topbar-title">FacturePro</span>
    <div class="topbar-right">
        <span>👤 <?= htmlspecialchars($user['nom_complet'] ?? '') ?></span>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="topbar-logout">⏻</a>
    </div>
</div>

<?php if (!empty($_SESSION['erreur_acces'])): ?>
    <div class="alerte erreur" style="margin:1rem 1.5rem 0;"><?= htmlspecialchars($_SESSION['erreur_acces']) ?></div>
    <?php unset($_SESSION['erreur_acces']); ?>
<?php endif; ?>

<div class="page-body">
