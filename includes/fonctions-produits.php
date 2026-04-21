<?php
require_once __DIR__ . '/fonctions_donnees.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Retourne tous les produits.
 */
function obtenirTousProduits() {
    return lireDonneesJSON(FICHIER_PRODUITS);
}

/**
 * Recherche un produit par son code-barres.
 * Retourne le produit ou null.
 */
function trouverProduitParCodeBarre($codeBarre) {
    $produits = obtenirTousProduits();
    foreach ($produits as $produit) {
        if ($produit['code_barre'] === $codeBarre) {
            return $produit;
        }
    }
    return null;
}

/**
 * Enregistre un nouveau produit dans le fichier JSON.
 * Retourne true si succès, false si le code-barres existe déjà.
 */
function enregistrerProduit($donnees) {
    $produits = obtenirTousProduits();

    foreach ($produits as $p) {
        if ($p['code_barre'] === $donnees['code_barre']) {
            return false;
        }
    }

    $produits[] = [
        'code_barre'         => $donnees['code_barre'],
        'nom'                => $donnees['nom'],
        'prix_unitaire_ht'   => (float) $donnees['prix_unitaire_ht'],
        'date_expiration'    => $donnees['date_expiration'],
        'quantite_stock'     => (int) $donnees['quantite_stock'],
        'date_enregistrement'=> date('Y-m-d'),
    ];

    return ecrireDonneesJSON(FICHIER_PRODUITS, $produits);
}

/**
 * Décrémente le stock d'un produit après une vente.
 */
function decrementerStock($codeBarre, $quantiteVendue) {
    $produits = obtenirTousProduits();
    foreach ($produits as &$produit) {
        if ($produit['code_barre'] === $codeBarre) {
            $produit['quantite_stock'] -= $quantiteVendue;
            break;
        }
    }
    return ecrireDonneesJSON(FICHIER_PRODUITS, $produits);
}

/**
 * Met à jour le stock d'un produit (pour le Manager).
 */
function mettreAJourStock($codeBarre, $nouvelleQuantite) {
    $produits = obtenirTousProduits();
    foreach ($produits as &$produit) {
        if ($produit['code_barre'] === $codeBarre) {
            $produit['quantite_stock'] = (int) $nouvelleQuantite;
            break;
        }
    }
    return ecrireDonneesJSON(FICHIER_PRODUITS, $produits);
}

/**
 * Valide les données d'un produit.
 * Retourne un tableau d'erreurs (vide si tout est valide).
 */
function validerDonneesProduit($donnees) {
    $erreurs = [];

    if (empty($donnees['code_barre']))
        $erreurs[] = "Le code-barres est obligatoire.";

    if (empty($donnees['nom']))
        $erreurs[] = "Le nom du produit est obligatoire.";

    if (!isset($donnees['prix_unitaire_ht']) || !is_numeric($donnees['prix_unitaire_ht']) || $donnees['prix_unitaire_ht'] <= 0)
        $erreurs[] = "Le prix unitaire HT doit être un nombre positif.";

    if (!isset($donnees['quantite_stock']) || !is_numeric($donnees['quantite_stock']) || $donnees['quantite_stock'] < 0)
        $erreurs[] = "La quantité doit être un nombre positif ou nul.";

    if (empty($donnees['date_expiration']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $donnees['date_expiration']))
        $erreurs[] = "La date d'expiration est invalide (format attendu : AAAA-MM-JJ).";

    return $erreurs;
}
