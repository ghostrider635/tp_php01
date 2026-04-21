<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions_auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Déjà connecté → rediriger
if (!empty($_SESSION['utilisateur'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant     = trim($_POST['identifiant'] ?? '');
    $motDePasse      = $_POST['mot_de_passe'] ?? '';

    if (empty($identifiant) || empty($motDePasse)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $utilisateur = authentifierUtilisateur($identifiant, $motDePasse);
        if ($utilisateur) {
            $_SESSION['utilisateur'] = $utilisateur;
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – Système de Facturation</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="page-login">
    <div class="login-container">
        <h1>Système de Facturation</h1>
        <h2>Connexion</h2>

        <?php if ($erreur): ?>
            <div class="alerte erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="identifiant">Identifiant</label>
            <input type="text" id="identifiant" name="identifiant"
                   value="<?= htmlspecialchars($_POST['identifiant'] ?? '') ?>" required autofocus>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>

            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>
</body>
</html>
