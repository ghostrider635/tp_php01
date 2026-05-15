<?php
// Test simple pour Vercel
echo "<h1>Test Vercel PHP</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Environment: " . (isset($_ENV['VERCEL']) ? 'Vercel' : 'Local') . "</p>";

// Test d'écriture dans /tmp
$testFile = '/tmp/test_vercel.txt';
$content = "Test Vercel - " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $content)) {
    echo "<p style='color:green;'>✓ Écriture dans /tmp réussie</p>";
    echo "<p>Contenu: " . file_get_contents($testFile) . "</p>";
} else {
    echo "<p style='color:red;'>✗ Écriture dans /tmp échouée</p>";
}

// Test JSON
$data = ['test' => 'ok', 'timestamp' => time()];
echo "<p>JSON test: " . json_encode($data) . "</p>";

echo "<hr>";
echo "<a href='index.php'>Accéder à l'application</a>";