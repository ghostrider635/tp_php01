<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions-factures.php';

exigerConnexion();
header('Content-Type: application/json');

$panier = $_SESSION['panier'] ?? [];
[$totalHT, $tva, $totalTTC] = calculerTotaux($panier);

echo json_encode([
    'total_ht'  => $totalHT,
    'tva'       => $tva,
    'total_ttc' => $totalTTC,
]);
