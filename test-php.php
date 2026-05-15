<?php
// Test simple pour vérifier que PHP fonctionne sur Vercel
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test PHP Vercel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test PHP sur Vercel</h1>
    
    <h2>Informations système :</h2>
    <ul>
        <li>PHP Version: <?php echo phpversion(); ?></li>
        <li>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Non défini'; ?></li>
        <li>Environment: <?php echo isset($_ENV['VERCEL']) ? 'Vercel' : 'Local'; ?></li>
        <li>Request URI: <?php echo $_SERVER['REQUEST_URI'] ?? '/'; ?></li>
    </ul>
    
    <h2>Test de fonctionnalités :</h2>
    <?php
    $tests = [];
    
    // Test 1: PHP fonctionne
    $tests['PHP fonctionne'] = true;
    
    // Test 2: JSON
    $json_test = json_encode(['test' => 'ok', 'timestamp' => time()]);
    $tests['JSON support'] = $json_test !== false;
    
    // Test 3: Sessions
    session_start();
    $tests['Sessions'] = session_status() === PHP_SESSION_ACTIVE;
    
    // Test 4: Écriture fichier
    $test_file = '/tmp/test_vercel.txt';
    $write_test = file_put_contents($test_file, 'Test Vercel - ' . date('Y-m-d H:i:s'));
    $tests['Écriture /tmp'] = $write_test !== false;
    
    // Test 5: Lecture fichier
    $read_test = file_get_contents($test_file);
    $tests['Lecture /tmp'] = $read_test !== false;
    
    // Afficher les résultats
    foreach ($tests as $name => $result) {
        echo "<p>";
        echo $result ? "✅ " : "❌ ";
        echo htmlspecialchars($name);
        if (!$result) {
            echo " <span class='error'>(ÉCHEC)</span>";
        }
        echo "</p>";
    }
    ?>
    
    <h2>Liens de test :</h2>
    <ul>
        <li><a href="/">Accueil (index.php)</a></li>
        <li><a href="/auth/login.php">Page de connexion</a></li>
        <li><a href="/api/factures.php">API Factures</a></li>
        <li><a href="/api/produits.php">API Produits</a></li>
    </ul>
    
    <hr>
    <p><strong>Note :</strong> Si cette page s'affiche correctement, PHP fonctionne sur Vercel.</p>
</body>
</html>