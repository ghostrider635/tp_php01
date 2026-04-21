<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions_donnees.php';

exigerRole([ROLE_SUPER_ADMIN]);

$identifiantCible = trim($_GET['id'] ?? '');
$moi = utilisateurConnecte()['identifiant'];

if (empty($identifiantCible) || $identifiantCible === $moi) {
    $_SESSION['succes_admin'] = "Opération non autorisée.";
    header('Location: ' . BASE_URL . '/modules/admin/gestion-comptes.php');
    exit;
}

$utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);
$utilisateurs = array_values(array_filter($utilisateurs, fn($u) => $u['identifiant'] !== $identifiantCible));
ecrireDonneesJSON(FICHIER_UTILISATEURS, $utilisateurs);

$_SESSION['succes_admin'] = "Compte « $identifiantCible » supprimé.";
header('Location: ' . BASE_URL . '/modules/admin/gestion-comptes.php');
exit;
