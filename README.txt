====================================================
  SYSTÈME DE FACTURATION – UPC FSI 2025-2026
====================================================

PRÉREQUIS
---------
- XAMPP (PHP 8.0+, Apache)
- Navigateur moderne avec accès à la caméra (Chrome recommandé)

DÉPLOIEMENT LOCAL
-----------------
1. Copier le dossier tp_php01/ dans : C:\xampp\htdocs\
2. Démarrer Apache via le panneau XAMPP
3. Ouvrir dans le navigateur : http://localhost/tp_php01/

CONNEXION INITIALE
------------------
Identifiant : admin
Mot de passe : admin123
Rôle         : Super Administrateur

PERMISSIONS CAMÉRA
------------------
Le scanner ZXing nécessite l'accès à la caméra.
Autoriser l'accès lorsque le navigateur le demande.
Pour tester en HTTP (non HTTPS), utiliser Chrome avec le flag :
  --unsafely-treat-insecure-origin-as-secure=http://localhost

STRUCTURE DES DONNÉES
---------------------
data/utilisateurs.json  → comptes utilisateurs
data/produits.json      → catalogue produits
data/factures.json      → historique des factures

TVA appliquée : 16%

RÔLES
-----
- super_administrateur : accès complet
- manager             : produits + facturation + rapports
- caissier            : facturation uniquement
