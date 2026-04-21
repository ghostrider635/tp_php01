<?php
require_once __DIR__ . '/fonctions_donnees.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Génère un identifiant unique de facture : FAC-AAAAMMJJ-NNN
 */
function genererIdFacture() {
    $factures = lireDonneesJSON(FICHIER_FACTURES);
    $dateStr  = date('Ymd');
    $compteur = 1;

    foreach ($factures as $f) {
        if (strpos($f['id_facture'], 'FAC-' . $dateStr) === 0) {
            $compteur++;
        }
    }

    return sprintf('FAC-%s-%03d', $dateStr, $compteur);
}

/**
 * Calcule les totaux d'une facture à partir de ses articles.
 * Retourne [total_ht, tva, total_ttc].
 */
function calculerTotaux($articles) {
    $totalHT = 0;
    foreach ($articles as $article) {
        $totalHT += $article['sous_total_ht'];
    }
    $tva      = round($totalHT * TVA);
    $totalTTC = $totalHT + $tva;
    return [$totalHT, $tva, $totalTTC];
}

/**
 * Sauvegarde une facture dans factures.json.
 */
function sauvegarderFacture($articles, $identifiantCaissier) {
    [$totalHT, $tva, $totalTTC] = calculerTotaux($articles);

    $facture = [
        'id_facture' => genererIdFacture(),
        'date'       => date('Y-m-d'),
        'heure'      => date('H:i:s'),
        'caissier'   => $identifiantCaissier,
        'articles'   => $articles,
        'total_ht'   => $totalHT,
        'tva'        => $tva,
        'total_ttc'  => $totalTTC,
    ];

    $factures   = lireDonneesJSON(FICHIER_FACTURES);
    $factures[] = $facture;
    ecrireDonneesJSON(FICHIER_FACTURES, $factures);

    return $facture;
}

/**
 * Retourne toutes les factures.
 */
function obtenirToutesFactures() {
    return lireDonneesJSON(FICHIER_FACTURES);
}

/**
 * Retourne une facture par son identifiant.
 */
function trouverFactureParId($idFacture) {
    $factures = obtenirToutesFactures();
    foreach ($factures as $f) {
        if ($f['id_facture'] === $idFacture) return $f;
    }
    return null;
}

/**
 * Retourne les factures d'une date donnée (format Y-m-d).
 */
function obtenirFacturesParDate($date) {
    $factures = obtenirToutesFactures();
    return array_filter($factures, fn($f) => $f['date'] === $date);
}

/**
 * Retourne les factures d'un mois donné (format Y-m).
 */
function obtenirFacturesParMois($mois) {
    $factures = obtenirToutesFactures();
    return array_filter($factures, fn($f) => str_starts_with($f['date'], $mois));
}
