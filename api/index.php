<?php
// Fichier de test pour Vercel
header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'message' => 'API FacturePro fonctionne sur Vercel',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => isset($_ENV['VERCEL']) ? 'Vercel' : 'Local',
    'php_version' => phpversion(),
    'session_status' => session_status(),
    'data_dir' => is_writable('/tmp') ? 'writable' : 'not writable'
];

echo json_encode($response, JSON_PRETTY_PRINT);