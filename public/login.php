<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_fail();

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $user = find_user_by_email($email);

    if (!$user || !password_verify($password, (string) $user['password_hash'])) {
        $error = 'Identifiants invalides.';
    } else {
        login_user((int) $user['id']);
        flash('success', 'Connexion réussie.');
        header('Location: /index.php');
        exit;
    }
}

$pageTitle = 'Connexion';
require __DIR__ . '/partials/header.php';
?>

<section class="auth-wrap">
    <div class="auth-card card">
        <h1>Connexion</h1>
        <p class="muted">Connectez-vous pour accéder à vos conversations.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="stack" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="email">Email</label>
            <input id="email" name="email" type="email" required placeholder="vous@example.com">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required placeholder="Votre mot de passe">

            <button type="submit" class="btn primary">Se connecter</button>
        </form>

        <p class="inline-link">Nouveau ici ? <a href="/register.php">Créer un compte</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
