<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$currentId = (int) current_user_id();
$otherId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

$otherUser = $otherId > 0 ? find_user_by_id($otherId) : null;
if (!$otherUser || $otherId === $currentId) {
    flash('error', 'Utilisateur invalide pour un chat privé.');
    header('Location: /index.php');
    exit;
}

$pageTitle = 'Chat privé';
require __DIR__ . '/partials/header.php';
?>

<section class="chat-layout">
    <div class="chat-header card">
        <a class="btn ghost" href="/index.php">Retour</a>
        <div>
            <h1>Conversation privée</h1>
            <p class="muted">Avec <?= e($otherUser['name']) ?> (<?= e($otherUser['email']) ?>)</p>
        </div>
    </div>

    <div id="chat-box" class="chat-box card" data-chat-type="private" data-target-id="<?= (int) $otherId ?>"></div>

    <form id="chat-form" class="chat-form card" method="post" action="/send_private.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="receiver_id" value="<?= (int) $otherId ?>">
        <textarea name="body" rows="3" maxlength="2000" placeholder="Écrivez votre message..." required></textarea>
        <button class="btn primary" type="submit">Envoyer</button>
    </form>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
