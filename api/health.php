<?php
header('Content-Type: application/json');

$checks = [
    'php_version' => [
        'status' => version_compare(phpversion(), '7.4', '>=') ? 'ok' : 'warning',
        'value' => phpversion(),
        'message' => version_compare(phpversion(), '7.4', '>=') ? 'Version PHP OK' : 'Version PHP inférieure à 7.4'
    ],
    'session' => [
        'status' => session_status() === PHP_SESSION_ACTIVE ? 'ok' : 'warning',
        'value' => session_status(),
        'message' => session_status() === PHP_SESSION_ACTIVE ? 'Session active' : 'Session non active'
    ],
    'tmp_writable' => [
        'status' => is_writable('/tmp') ? 'ok' : 'error',
        'value' => is_writable('/tmp'),
        'message' => is_writable('/tmp') ? '/tmp est accessible en écriture' : '/tmp non accessible en écriture'
    ],
    'data_files' => [
        'status' => 'ok',
        'value' => [],
        'message' => 'Fichiers de données'
    ],
    'vercel_env' => [
        'status' => isset($_ENV['VERCEL']) ? 'ok' : 'info',
        'value' => isset($_ENV['VERCEL']),
        'message' => isset($_ENV['VERCEL']) ? 'Environnement Vercel détecté' : 'Environnement local'
    ]
];

// Vérifier les fichiers de données
$data_files = ['utilisateurs.json', 'produits.json', 'factures.json'];
foreach ($data_files as $file) {
    $local_path = __DIR__ . '/../data/' . $file;
    $tmp_path = '/tmp/' . $file;
    
    $checks['data_files']['value'][$file] = [
        'local_exists' => file_exists($local_path),
        'tmp_exists' => file_exists($tmp_path),
        'tmp_writable' => is_writable($tmp_path)
    ];
}

$response = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => $checks
];

echo json_encode($response, JSON_PRETTY_PRINT);