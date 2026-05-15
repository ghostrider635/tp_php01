<?php
/**
 * Routeur pour Vercel PHP
 * Redirige toutes les requêtes vers les fichiers PHP correspondants
 */

// Récupérer le chemin demandé
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($request_uri, PHP_URL_PATH);

// Nettoyer le chemin
$path = trim($path, '/');

// Route par défaut
if (empty($path) || $path === '/') {
    $path = 'index.php';
}

// Mapper les routes vers les fichiers
$route_map = [
    // Pages principales
    'index.php' => '../index.php',
    'auth/login.php' => '../auth/login.php',
    'auth/logout.php' => '../auth/logout.php',
    
    // Modules
    'modules/facturation/nouvelle-facture.php' => '../modules/facturation/nouvelle-facture.php',
    'modules/facturation/afficher-facture.php' => '../modules/facturation/afficher-facture.php',
    'modules/facturation/calcul.php' => '../modules/facturation/calcul.php',
    'modules/produits/liste.php' => '../modules/produits/liste.php',
    'modules/produits/enregistrer.php' => '../modules/produits/enregistrer.php',
    'modules/produits/lire.php' => '../modules/produits/lire.php',
    'modules/admin/gestion-comptes.php' => '../modules/admin/gestion-comptes.php',
    'modules/admin/ajouter-compte.php' => '../modules/admin/ajouter-compte.php',
    'modules/admin/supprimer-compte.php' => '../modules/admin/supprimer-compte.php',
    
    // Rapports
    'rapports/rapport-journalier.php' => '../rapports/rapport-journalier.php',
    'rapports/rapport-mensuel.php' => '../rapports/rapport-mensuel.php',
    
    // API (restent dans api/)
    'api/factures.php' => 'factures.php',
    'api/produits.php' => 'produits.php',
    
    // Assets (fichiers statiques)
    'assets/css/style.css' => '../assets/css/style.css',
    'assets/js/pwa.js' => '../assets/js/pwa.js',
    'assets/js/scanner.js' => '../assets/js/scanner.js',
    'manifest.json' => '../manifest.json',
    'sw.js' => '../sw.js',
];

// Vérifier si la route existe dans le mapping
if (isset($route_map[$path])) {
    $target_file = $route_map[$path];
    
    // Vérifier si le fichier existe
    if (file_exists(__DIR__ . '/' . $target_file)) {
        // Inclure le fichier PHP
        require_once __DIR__ . '/' . $target_file;
        exit;
    }
}

// Si c'est un fichier PHP direct, essayer de l'inclure
if (preg_match('/\.php$/', $path)) {
    $php_file = '../' . $path;
    if (file_exists(__DIR__ . '/' . $php_file)) {
        require_once __DIR__ . '/' . $php_file;
        exit;
    }
}

// Si c'est un fichier statique, essayer de le servir
$static_file = '../' . $path;
if (file_exists(__DIR__ . '/' . $static_file)) {
    $extension = pathinfo($static_file, PATHINFO_EXTENSION);
    
    // Définir les headers appropriés
    $mime_types = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'txt' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
    ];
    
    if (isset($mime_types[$extension])) {
        header('Content-Type: ' . $mime_types[$extension]);
    }
    
    readfile(__DIR__ . '/' . $static_file);
    exit;
}

// Page 404
http_response_code(404);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>404 - Page non trouvée</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #666; }
    </style>
</head>
<body>
    <h1>404 - Page non trouvée</h1>
    <p>La page demandée n'existe pas : <?php echo htmlspecialchars($path); ?></p>
    <p><a href="/">Retour à l'accueil</a></p>
</body>
</html>