# Documentation académique — Projet TP_php01

## Résumé
Ce document présente une description structurée et académique du projet "TP_php01". Il explique le contexte, les objectifs, l'architecture, les composants principaux, le modèle de données, les API, les fonctionnalités, l'installation, les usages et les limitations.

## Table des matières
1. Contexte et objectifs
2. Architecture générale
3. Description des modules et fichiers clés
4. Modèle de données
5. API et endpoints
6. Fonctionnalités utilisateur
7. Installation et exécution
8. Scénarios d'utilisation
9. Limitations et pistes d'amélioration
10. Annexes (fichiers et ressources)

---

## 1. Contexte et objectifs
Le projet "TP_php01" est une application de gestion de facturation légère écrite en PHP. Elle vise à fournir un cadre pédagogique pour apprendre la manipulation de fichiers JSON en tant que source de données, la gestion d'authentification simple, et la génération/consultation de factures via une interface web minimale.

Objectifs pédagogiques:
- Illustrer la structuration d'un petit projet PHP sans base de données relationnelle.
- Montrer la séparation modulaire du code (includes, modules, api).
- Fournir des endpoints API simples pour consommation par du JS côté client (scanner PWA, etc.).

## 2. Architecture générale
Le projet suit une architecture file-based (données en JSON dans `data/`) et une organisation MVC-lite :
- `index.php`, pages publiques et UI.
- `includes/` : fonctions utilitaires (authentification, gestion des données, factures, produits).
- `modules/` : pages par fonctionnalité (facturation, produits, administration).
- `api/` : endpoints REST-like en PHP servant JSON (factures, produits).
- `auth/` : pages d'authentification et sessions.
- `assets/` : ressources front (CSS, JS, icônes).
- `data/` : fichiers JSON persistants (`produits.json`, `factures.json`, `users.json`, etc.).

L'application est conçue pour être déployée sur un serveur web local (ex. XAMPP) et ne requiert pas de serveur de base de données externe.

## 3. Description des modules et fichiers clés
- [index.php](index.php) : point d'entrée, tableau de bord ou redirection.
- [manifest.json](manifest.json), [sw.js](sw.js), `assets/js/pwa.js` : PWA (mode hors-ligne, installable) et scripts service worker.
- `api/factures.php` : endpoint pour consulter/créer/mettre à jour les factures (JSON).
- `api/produits.php` : endpoint pour opérations CRUD simples sur les produits.
- `includes/fonctions_auth.php` : fonctions d'authentification (login/logout/session management).
- `includes/fonctions_donnees.php` : lecture/écriture sécurisée des fichiers JSON.
- `includes/fonctions-factures.php` : fonctions métiers pour créer, lire et formater les factures.
- `modules/facturation/nouvelle-facture.php` : interface pour créer une nouvelle facture.
- `assets/js/scanner.js` : logique côté client pour scanner ou interagir avec l'API (ex. QR code ou PWA features).

## 4. Modèle de données
Les données sont stockées sous forme de JSON dans `data/`.
Exemples de fichiers clés:
- `data/produits.json` : liste d'objets produit {id, nom, description, prix_unitaire, stock}
- `data/factures.json` : liste d'objets facture {id, date, client, lignes:[{produit_id, quantite, prix_unitaire}], total, statut}
- `data/users.json` / `data/utilisateurs.json` : comptes utilisateurs pour accès admin.

Les fonctions d'accès aux données encapsulent la sérialisation/désérialisation et la gestion des verrous (si implémentée) pour éviter la corruption des fichiers.

## 5. API et endpoints
Les endpoints exposés dans `api/` renvoient du JSON et acceptent typiquement des méthodes POST/GET. Exemple de routes:
- `api/produits.php` :
  - GET -> liste ou détail d'un produit
  - POST -> création ou mise à jour d'un produit
- `api/factures.php` :
  - GET -> liste ou détail d'une facture
  - POST -> création d'une facture

Chaque endpoint doit valider les entrées et renvoyer des objets JSON avec code d'état HTTP approprié.

## 6. Fonctionnalités utilisateur
- Gestion des produits : créer, lire, modifier, lister.
- Création et consultation des factures : nouvelle facture, calcul des totaux, affichage.
- Authentification : pages de login/logout pour protéger les pages d'administration.
- PWA et scanner : intégration d'un script `scanner.js` pour usage mobile (ex. scanner QR ou interaction hors-ligne).
- Génération d'icônes et ressources statiques pour la PWA.

## 7. Installation et exécution
Prérequis:
- PHP 7.4+ (ou version compatible avec le code)
- Serveur local (XAMPP, WampServer, ou PHP built-in)

Étapes d'installation (local):
1. Placer le dossier `tp_php01` dans le répertoire web du serveur (ex. `C:/xampp/htdocs/`).
2. Vérifier les permissions d'écriture pour le dossier `data/` afin que PHP puisse modifier les fichiers JSON.
3. Ouvrir le navigateur et accéder à `http://localhost/tp_php01/`.
4. Utiliser `auth/login.php` pour accéder aux pages protégées.

## 8. Scénarios d'utilisation
- Scénario 1 — Création d'une facture :
  1. Authentifier l'utilisateur si nécessaire.
  2. Aller dans `modules/facturation/nouvelle-facture.php`.
  3. Sélectionner les produits et quantités.
  4. Soumettre -> l'API enregistre la facture dans `data/factures.json` et calcule le total.

- Scénario 2 — Consulter rapport journalier :
  1. Accéder à `rapports/rapport-journalier.php`.
  2. Spécifier la date -> page filtre `data/factures.json` et présente le total.

## 9. Limitations et pistes d'amélioration
Limitations actuelles:
- Pas de base de données relationnelle (limites de scalabilité et de concurrence).
- Authentification basique (peut manquer d'algorithmes modernes, hashing salé, etc.).
- Gestion d'accès et permissions fines non implémentées.
- Tests unitaires et couverture inexistants.

Pistes d'amélioration:
- Migrer vers MySQL ou SQLite pour gestion transactionnelle.
- Renforcer l'authentification (hash bcrypt, tokens JWT pour API).
- Ajouter tests automatisés (PHPUnit) et CI.
- Ajouter gestion des erreurs et journalisation centralisée.

## 10. Annexes — Fichiers et ressources
- Points d'entrée principaux : [index.php](index.php), [generateur_user.php](generateur_user.php)
- Endpoints : `api/factures.php`, `api/produits.php`
- Utilitaires : `includes/fonctions_donnees.php`, `includes/fonctions-factures.php`, `includes/fonctions_auth.php`
- Données exemples : `data/produits.json`, `data/factures.json`, `data/users.json`
- Assets front : `assets/css/style.css`, `assets/js/scanner.js`, `assets/js/pwa.js`

---

### Contact
Pour toute question sur cette documentation ou pour demander des transformations (format LaTeX, ajout d'illustrations, export PDF prêt à l'emploi), indiquez vos préférences et je m'en occupe.
