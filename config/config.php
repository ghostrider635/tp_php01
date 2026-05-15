<?php
// Taux de TVA
define('TVA', 0.16);

// Détecter l'environnement Vercel
$isVercel = isset($_ENV['VERCEL']) && $_ENV['VERCEL'] === '1';

// Chemins des fichiers de données
if ($isVercel) {
    // Sur Vercel, utiliser /tmp pour les fichiers écritures
    define('FICHIER_PRODUITS',     '/tmp/produits.json');
    define('FICHIER_FACTURES',     '/tmp/factures.json');
    define('FICHIER_UTILISATEURS', '/tmp/utilisateurs.json');
    
    // Copier les fichiers initiaux si nécessaire
    if (!file_exists('/tmp/utilisateurs.json')) {
        copy(__DIR__ . '/../data/utilisateurs.json', '/tmp/utilisateurs.json');
    }
    if (!file_exists('/tmp/produits.json')) {
        copy(__DIR__ . '/../data/produits.json', '/tmp/produits.json');
    }
    if (!file_exists('/tmp/factures.json')) {
        copy(__DIR__ . '/../data/factures.json', '/tmp/factures.json');
    }
} else {
    // En local
    define('FICHIER_PRODUITS',     __DIR__ . '/../data/produits.json');
    define('FICHIER_FACTURES',     __DIR__ . '/../data/factures.json');
    define('FICHIER_UTILISATEURS', __DIR__ . '/../data/utilisateurs.json');
}

// Rôles disponibles
define('ROLE_CAISSIER',       'caissier');
define('ROLE_MANAGER',        'manager');
define('ROLE_SUPER_ADMIN',    'super_administrateur');

// URL de base
if ($isVercel) {
    define('BASE_URL', ''); // Sur Vercel, pas de sous-dossier
} else {
    define('BASE_URL', '/tp_php01');
}
