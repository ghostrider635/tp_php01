<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions_donnees.php';

exigerRole([ROLE_SUPER_ADMIN]);

$erreurs = [];
$succes  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant  = trim($_POST['identifiant'] ?? '');
    $nomComplet   = trim($_POST['nom_complet'] ?? '');
    $role         = trim($_POST['role'] ?? '');
    $motDePasse   = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    $rolesAutorises = [ROLE_CAISSIER, ROLE_MANAGER];

    if (empty($identifiant))
        $erreurs[] = "L'identifiant est obligatoire.";
    if (empty($nomComplet))
        $erreurs[] = "Le nom complet est obligatoire.";
    if (!in_array($role, $rolesAutorises))
        $erreurs[] = "Rôle invalide.";
    if (strlen($motDePasse) < 6)
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    if ($motDePasse !== $confirmation)
        $erreurs[] = "Les mots de passe ne correspondent pas.";

    if (empty($erreurs)) {
        $utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);
        foreach ($utilisateurs as $u) {
            if ($u['identifiant'] === $identifiant) {
                $erreurs[] = "Cet identifiant est déjà utilisé.";
                break;
            }
        }
    }

    if (empty($erreurs)) {
        $utilisateurs[] = [
            'identifiant'   => $identifiant,
            'mot_de_passe'  => password_hash($motDePasse, PASSWORD_DEFAULT),
            'role'          => $role,
            'nom_complet'   => $nomComplet,
            'date_creation' => date('Y-m-d'),
            'actif'         => true,
        ];
        ecrireDonneesJSON(FICHIER_UTILISATEURS, $utilisateurs);
        $_SESSION['succes_admin'] = "Compte « $identifiant » créé avec succès.";
        header('Location: ' . BASE_URL . '/modules/admin/gestion-comptes.php');
        exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-titre">
    <h2>Ajouter un Compte</h2>
</div>

<?php if (!empty($erreurs)): ?>
    <ul class="erreurs-liste">
        <?php foreach ($erreurs as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-groupe">
        <label for="identifiant">Identifiant</label>
        <input type="text" id="identifiant" name="identifiant"
               value="<?= htmlspecialchars($_POST['identifiant'] ?? '') ?>" required>
    </div>
    <div class="form-groupe">
        <label for="nom_complet">Nom complet</label>
        <input type="text" id="nom_complet" name="nom_complet"
               value="<?= htmlspecialchars($_POST['nom_complet'] ?? '') ?>" required>
    </div>
    <div class="form-groupe">
        <label for="role">Rôle</label>
        <select id="role" name="role" required>
            <option value="">-- Choisir --</option>
            <option value="caissier"  <?= ($_POST['role'] ?? '') === 'caissier'  ? 'selected' : '' ?>>Caissier</option>
            <option value="manager"   <?= ($_POST['role'] ?? '') === 'manager'   ? 'selected' : '' ?>>Manager</option>
        </select>
    </div>
    <div class="form-groupe">
        <label for="mot_de_passe">Mot de passe</label>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
    </div>
    <div class="form-groupe">
        <label for="confirmation">Confirmer le mot de passe</label>
        <input type="password" id="confirmation" name="confirmation" required>
    </div>
    <button type="submit" class="btn">Créer le compte</button>
    <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php" class="btn btn-secondaire">Annuler</a>
</form>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
