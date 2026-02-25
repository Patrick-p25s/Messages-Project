<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$userId = (int) current_user_id();
$users = list_other_users($userId);
$groups = list_user_groups($userId);
$pageTitle = 'Accueil';
$flashSuccess = flash('success');
$flashError = flash('error');

require __DIR__ . '/partials/header.php';
?>

<section class="dashboard-grid">
    <div class="card">
        <h1>Bienvenue dans votre messagerie</h1>
        <p class="muted">Choisissez un utilisateur pour discuter en privé, ou créez un groupe.</p>

        <?php if ($flashSuccess): ?>
            <div class="alert success"><?= e($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert error"><?= e($flashError) ?></div>
        <?php endif; ?>

        <h2>Utilisateurs</h2>
        <div class="list">
            <?php if (!$users): ?>
                <p class="muted">Aucun autre utilisateur pour l'instant.</p>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <a class="list-item" href="/chat.php?user_id=<?= (int) $user['id'] ?>">
                    <div>
                        <strong><?= e($user['name']) ?></strong>
                        <span><?= e($user['email']) ?></span>
                    </div>
                    <span class="pill">Message privé</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2>Mes groupes</h2>
        <div class="list">
            <?php if (!$groups): ?>
                <p class="muted">Vous n'êtes membre d'aucun groupe.</p>
            <?php endif; ?>
            <?php foreach ($groups as $group): ?>
                <a class="list-item" href="/group.php?group_id=<?= (int) $group['id'] ?>">
                    <div>
                        <strong><?= e($group['name']) ?></strong>
                        <span>Groupe #<?= (int) $group['id'] ?></span>
                    </div>
                    <span class="pill">Ouvrir</span>
                </a>
            <?php endforeach; ?>
        </div>

        <h3>Créer un groupe</h3>
        <form class="stack" method="post" action="/create_group.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label for="name">Nom du groupe</label>
            <input id="name" name="name" type="text" maxlength="120" required placeholder="Ex: Projet Backend 2026">

            <label>Ajouter des membres</label>
            <div class="members-grid">
                <?php foreach ($users as $user): ?>
                    <label class="checkline">
                        <input type="checkbox" name="members[]" value="<?= (int) $user['id'] ?>">
                        <span><?= e($user['name']) ?> (<?= e($user['email']) ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>

            <button class="btn primary" type="submit">Créer le groupe</button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
