<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$currentId = (int) current_user_id();
$groupId = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;

$group = $groupId > 0 ? find_group_by_id($groupId) : null;
if (!$group || !is_group_member($groupId, $currentId)) {
    flash('error', 'Groupe introuvable ou accès refusé.');
    header('Location: /index.php');
    exit;
}

$pageTitle = 'Groupe';
require __DIR__ . '/partials/header.php';
?>

<section class="chat-layout">
    <div class="chat-header card">
        <a class="btn ghost" href="/index.php">Retour</a>
        <div>
            <h1><?= e($group['name']) ?></h1>
            <p class="muted">Discussion de groupe</p>
        </div>
    </div>

    <div id="chat-box" class="chat-box card" data-chat-type="group" data-target-id="<?= (int) $groupId ?>"></div>

    <form id="chat-form" class="chat-form card" method="post" action="/send_group.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="group_id" value="<?= (int) $groupId ?>">
        <textarea name="body" rows="3" maxlength="2000" placeholder="Écrivez votre message au groupe..." required></textarea>
        <button class="btn primary" type="submit">Envoyer</button>
    </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
