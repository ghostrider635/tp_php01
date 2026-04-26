<?php
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../includes/fonctions_donnees.php';

exigerRole([ROLE_SUPER_ADMIN]);

$utilisateurs = lireDonneesJSON(FICHIER_UTILISATEURS);
$succes = $_SESSION['succes_admin'] ?? '';
unset($_SESSION['succes_admin']);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-titre">
    <h2>Gestion des Comptes</h2>
    <a href="<?= BASE_URL ?>/modules/admin/ajouter-compte.php" class="btn">+ Ajouter un compte</a>
</div>

<?php if ($succes): ?>
    <div class="alerte succes"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<div class="tableau-wrapper">
<table class="tableau">
    <thead>
        <tr>
            <th>Identifiant</th>
            <th>Nom complet</th>
            <th>Rôle</th>
            <th>Date création</th>
            <th>Actif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['identifiant']) ?></td>
            <td><?= htmlspecialchars($u['nom_complet']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td><?= htmlspecialchars($u['date_creation']) ?></td>
            <td><?= $u['actif'] ? '✔' : '✘' ?></td>
            <td>
                <?php if ($u['identifiant'] !== utilisateurConnecte()['identifiant']): ?>
                    <a href="<?= BASE_URL ?>/modules/admin/supprimer-compte.php?id=<?= urlencode($u['identifiant']) ?>"
                       class="btn-danger"
                       onclick="return confirm('Supprimer ce compte ?')">Supprimer</a>
                <?php else: ?>
                    <em>Compte actuel</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div><!-- /.tableau-wrapper -->

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
