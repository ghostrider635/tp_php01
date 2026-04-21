<?php
// Script à exécuter UNE SEULE FOIS si la connexion admin échoue
// Accès : http://localhost/tp_php01/reset_admin.php
// Supprimer ce fichier après utilisation !

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/fonctions_donnees.php';

$utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);

foreach ($utilisateurs as &$u) {
    if ($u['identifiant'] === 'admin') {
        $u['mot_de_passe'] = password_hash('admin123', PASSWORD_DEFAULT);
        break;
    }
}

ecrireDonneesJSON(FICHIER_UTILISATEURS, $utilisateurs);
echo '✔ Mot de passe admin réinitialisé. <a href="/tp_php01/auth/login.php">Se connecter</a>';
echo '<br><strong>Supprime ce fichier après utilisation !</strong>';
