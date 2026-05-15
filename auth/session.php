<?php
require_once __DIR__ . '/../config/config.php';

// Configuration des sessions pour Vercel
if (session_status() === PHP_SESSION_NONE) {
    // Sur Vercel, stocker les sessions dans /tmp
    $isVercel = isset($_ENV['VERCEL']) && $_ENV['VERCEL'] === '1';
    
    if ($isVercel) {
        // Configurer le chemin des sessions pour Vercel
        ini_set('session.save_path', '/tmp/sessions');
        
        // Créer le dossier des sessions s'il n'existe pas
        if (!is_dir('/tmp/sessions')) {
            mkdir('/tmp/sessions', 0755, true);
        }
        
        // Utiliser des cookies plus sécurisés
        session_set_cookie_params([
            'lifetime' => 86400, // 24 heures
            'path' => '/',
            'domain' => '',
            'secure' => true,    // HTTPS seulement sur Vercel
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
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
