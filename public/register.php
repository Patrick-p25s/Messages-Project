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

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '' || strlen($name) < 2) {
        $error = 'Le nom doit contenir au moins 2 caractères.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif (find_user_by_email($email)) {
        $error = 'Cet email est déjà utilisé.';
    } else {
        $userId = create_user($name, $email, $password);
        login_user($userId);
        flash('success', 'Compte créé avec succès. Bienvenue !');
        header('Location: /index.php');
        exit;
    }
}

$pageTitle = 'Inscription';
require __DIR__ . '/partials/header.php';
?>

<section class="auth-wrap">
    <div class="auth-card card">
        <h1>Créer un compte</h1>
        <p class="muted">Rejoignez la plateforme et commencez à discuter.</p>

        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="stack" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="name">Nom complet</label>
            <input id="name" name="name" type="text" required maxlength="100" placeholder="Votre nom">

            <label for="email">Email</label>
            <input id="email" name="email" type="email" required placeholder="vous@example.com">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required minlength="6" placeholder="Au moins 6 caractères">

            <button type="submit" class="btn primary">S'inscrire</button>
        </form>

        <p class="inline-link">Déjà un compte ? <a href="/login.php">Se connecter</a></p>
    </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
