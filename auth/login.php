<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/fonctions_auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['utilisateur'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $motDePasse  = $_POST['mot_de_passe'] ?? '';

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
    <title>Connexion – FacturePro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="page-login">
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <span class="logo-icon">🛒</span>
                <h1>FacturePro</h1>
                <p>Système de Facturation</p>
            </div>

            <?php if ($erreur): ?>
                <div class="alerte erreur"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-groupe">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant"
                           value="<?= htmlspecialchars($_POST['identifiant'] ?? '') ?>"
                           placeholder="Votre identifiant" required autofocus>
                </div>
                <div class="form-groupe">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe"
                           placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn" style="width:100%; justify-content:center; padding:0.65rem;">
                    Se connecter →
                </button>
            </form>
        </div>
        <p style="text-align:center; margin-top:1rem; font-size:0.75rem; color:var(--text-muted);">
            UPC – Faculté des Sciences Informatiques
        </p>
    </div>
</body>
</html>
