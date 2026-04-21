<?php
require_once __DIR__ . '/fonctions_donnees.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Vérifie les identifiants d'un utilisateur.
 * Retourne les infos de l'utilisateur si succès, sinon false.
 */
function authentifierUtilisateur($identifiant, $motDePasseSaisi) {
    $utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);

    foreach ($utilisateurs as $user) {
        if ($user['identifiant'] === $identifiant && $user['actif'] === true) {
            if (password_verify($motDePasseSaisi, $user['mot_de_passe'])) {
                unset($user['mot_de_passe']);
                return $user;
            }
        }
    }
    return false;
}
