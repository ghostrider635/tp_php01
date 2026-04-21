<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-produits.php';

exigerConnexion();
header('Content-Type: application/json');

$codeBarre = trim($_GET['code_barre'] ?? '');

if (empty($codeBarre)) {
    echo json_encode(['succes' => false, 'message' => 'Code-barres manquant.']);
    exit;
}

$produit = trouverProduitParCodeBarre($codeBarre);

if ($produit) {
    echo json_encode(['succes' => true, 'produit' => $produit]);
} else {
    echo json_encode(['succes' => false, 'message' => 'Produit inconnu. Veuillez le faire enregistrer par un Manager.']);
}
