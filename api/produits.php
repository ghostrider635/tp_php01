<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-produits.php';

exigerConnexion();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode(obtenirTousProduits(), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) { http_response_code(400); echo json_encode(['erreur' => 'Données invalides']); exit; }

    $erreurs = validerDonneesProduit($data);
    if ($erreurs) { http_response_code(422); echo json_encode(['erreurs' => $erreurs]); exit; }

    $ok = enregistrerProduit($data);
    if (!$ok) { http_response_code(409); echo json_encode(['erreur' => 'Code-barres déjà existant']); exit; }

    http_response_code(201);
    echo json_encode(['succes' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['erreur' => 'Méthode non autorisée']);
