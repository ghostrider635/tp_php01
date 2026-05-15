<?php
// Test simple pour Vercel
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Vercel PHP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>✅ Test Vercel PHP Réussi !</h1>
    
    <h2>Informations :</h2>
    <ul>
        <li>PHP Version: <?php echo phpversion(); ?></li>
        <li>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Vercel'; ?></li>
        <li>Request URI: <?php echo $_SERVER['REQUEST_URI'] ?? '/'; ?></li>
        <li>Router fonctionne: OUI</li>
    </ul>
    
    <h2>Test des fonctionnalités :</h2>
    <?php
    // Test de base
    echo "<p class='success'>✅ PHP fonctionne correctement</p>";
    
    // Test sessions
    session_start();
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo "<p class='success'>✅ Sessions PHP actives</p>";
    } else {
        echo "<p class='error'>❌ Sessions PHP non actives</p>";
    }
    
    // Test JSON
    $test_json = json_encode(['test' => 'ok']);
    if ($test_json !== false) {
        echo "<p class='success'>✅ JSON supporté</p>";
    }
    ?>
    
    <h2>Liens de test :</h2>
    <ul>
        <li><a href="/">Accueil via router</a></li>
        <li><a href="/auth/login.php">Connexion</a></li>
        <li><a href="/api/factures.php">API Factures</a></li>
    </ul>
    
    <hr>
    <p><strong>Note :</strong> Si cette page s'affiche, Vercel PHP fonctionne !</p>
</body>
</html>