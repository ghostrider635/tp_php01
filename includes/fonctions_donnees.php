<?php
/**
 * Lit un fichier JSON et retourne un tableau PHP.
 * Retourne un tableau vide si le fichier est absent ou invalide.
 */
function lireDonneesJSON($chemin) {
    if (!file_exists($chemin)) return [];
    $contenu = file_get_contents($chemin);
    $donnees = json_decode($contenu, true);
    return is_array($donnees) ? $donnees : [];
}

/**
 * Écrit un tableau PHP dans un fichier JSON.
 * Retourne true en cas de succès, false sinon.
 */
function ecrireDonneesJSON($chemin, $donnees) {
    $json = json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($chemin, $json) !== false;
}
