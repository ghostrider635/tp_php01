<?php
/**
 * Script d'initialisation pour Vercel
 * Copie les fichiers de données dans /tmp au premier lancement
 */

echo "=== Initialisation Vercel pour FacturePro ===\n\n";

$sourceDir = __DIR__ . '/data';
$targetDir = '/tmp';

$filesToCopy = [
    'utilisateurs.json',
    'produits.json', 
    'factures.json',
    'facturation.json'
];

foreach ($filesToCopy as $file) {
    $source = $sourceDir . '/' . $file;
    $target = $targetDir . '/' . $file;
    
    if (file_exists($source)) {
        if (copy($source, $target)) {
            echo "✓ $file copié vers /tmp/\n";
        } else {
            echo "✗ Erreur lors de la copie de $file\n";
        }
    } else {
        echo "⚠ $file n'existe pas dans data/\n";
    }
}

echo "\n=== Vérification des permissions ===\n";

foreach ($filesToCopy as $file) {
    $target = $targetDir . '/' . $file;
    if (file_exists($target)) {
        $perms = substr(sprintf('%o', fileperms($target)), -4);
        $size = filesize($target);
        echo "• $target : permissions $perms, taille $size octets\n";
    }
}

echo "\n=== Test de lecture/écriture ===\n";

$testFile = $targetDir . '/test_vercel.json';
$testData = ['test' => 'Vercel PHP', 'timestamp' => date('Y-m-d H:i:s')];

if (file_put_contents($testFile, json_encode($testData, JSON_PRETTY_PRINT))) {
    echo "✓ Écriture test réussie\n";
    
    $readData = json_decode(file_get_contents($testFile), true);
    if ($readData) {
        echo "✓ Lecture test réussie\n";
        unlink($testFile);
        echo "✓ Fichier test nettoyé\n";
    }
} else {
    echo "✗ Écriture test échouée\n";
}

echo "\n=== Initialisation terminée ===\n";
echo "L'application est prête pour Vercel !\n";
echo "URL d'accès : https://votre-app.vercel.app\n";