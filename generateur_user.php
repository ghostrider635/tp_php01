<?php

$users = [];

$users[] = [
    "identifiant" => "admin",
    "mot_de_passe" => password_hash("admin123", PASSWORD_DEFAULT),
    "role" => "super_administrateur",
    "nom_complet" => "Super Admin",
    "date_creation" => date("Y-m-d"),
    "actif" => true
];

$users[] = [
    "identifiant" => "user1",
    "mot_de_passe" => password_hash("user123", PASSWORD_DEFAULT),
    "role" => "utilisateur",
    "nom_complet" => "Utilisateur Test",
    "date_creation" => date("Y-m-d"),
    "actif" => true
];

// Sauvegarde JSON
file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));

echo "Fichier JSON généré avec succès ";

?>