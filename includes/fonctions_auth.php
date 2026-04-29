<?php
require_once __DIR__ . '/fonctions_donnees.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Vérifie les identifiants d'un utilisateur.
 * Retourne les infos de l'utilisateur si succès, sinon false.
 */
function authentifierUtilisateur($identifiant, $motDePasseSaisi) {
    $utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);

    // Trouver l'utilisateur d'abord (évite bcrypt sur chaque entrée)
    $userTrouve = null;
    foreach ($utilisateurs as $user) {
        if ($user['identifiant'] === $identifiant && $user['actif'] === true) {
            $userTrouve = $user;
            break;
        }
    }

    if (!$userTrouve) return false;

    if (!password_verify($motDePasseSaisi, $userTrouve['mot_de_passe'])) return false;

    // Réduire le coût bcrypt si trop élevé (> 10 = lent sur petit serveur)
    if (password_needs_rehash($userTrouve['mot_de_passe'], PASSWORD_BCRYPT, ['cost' => 10])) {
        $nouveauHash = password_hash($motDePasseSaisi, PASSWORD_BCRYPT, ['cost' => 10]);
        mettreAJourMotDePasse($userTrouve['identifiant'], $nouveauHash);
    }

    unset($userTrouve['mot_de_passe']);
    return $userTrouve;
}

function mettreAJourMotDePasse($identifiant, $nouveauHash) {
    $utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);
    foreach ($utilisateurs as &$user) {
        if ($user['identifiant'] === $identifiant) {
            $user['mot_de_passe'] = $nouveauHash;
            break;
        }
    }
    ecrireDonneesJSON(FICHIER_UTILISATEURS, $utilisateurs);
}
