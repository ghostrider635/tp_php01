<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirige vers login si l'utilisateur n'est pas connecté.
 */
function exigerConnexion() {
    if (empty($_SESSION['utilisateur'])) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

/**
 * Vérifie que l'utilisateur connecté possède l'un des rôles autorisés.
 * Redirige vers index.php avec un message d'erreur sinon.
 */
function exigerRole(array $rolesAutorises) {
    exigerConnexion();
    $roleActuel = $_SESSION['utilisateur']['role'] ?? '';
    if (!in_array($roleActuel, $rolesAutorises, true)) {
        $_SESSION['erreur_acces'] = "Accès refusé : vous n'avez pas les permissions nécessaires.";
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Retourne l'utilisateur connecté.
 */
function utilisateurConnecte() {
    return $_SESSION['utilisateur'] ?? null;
}
