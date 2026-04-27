<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/../includes/fonctions-factures.php';
require_once __DIR__ . '/../includes/fonctions-produits.php';

exigerConnexion();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $date = $_GET['date'] ?? null;
    $mois = $_GET['mois'] ?? null;

    if ($date)      $data = array_values(obtenirFacturesParDate($date));
    elseif ($mois)  $data = array_values(obtenirFacturesParMois($mois));
    else            $data = obtenirToutesFactures();

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (empty($body['articles'])) {
        http_response_code(400);
        echo json_encode(['erreur' => 'Articles manquants']);
        exit;
    }

    $user = utilisateurConnecte();

    // Décrémenter les stocks
    foreach ($body['articles'] as $ligne) {
        decrementerStock($ligne['code_barre'], $ligne['quantite']);
    }

    $facture = sauvegarderFacture($body['articles'], $user['identifiant']);
    http_response_code(201);
    echo json_encode($facture, JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode(['erreur' => 'Méthode non autorisée']);
