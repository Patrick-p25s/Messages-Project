<?php
/** @var string $pageTitle */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="bg-shape bg-shape-a"></div>
<div class="bg-shape bg-shape-b"></div>
<header class="topbar">
    <div class="topbar-inner container">
        <a class="brand" href="/index.php"><?= e(APP_NAME) ?></a>
        <?php if (is_logged_in()): ?>
            <?php $current = find_user_by_id((int) current_user_id()); ?>
            <nav class="topnav">
                <span class="user-pill"><?= e($current['name'] ?? 'Utilisateur') ?></span>
                <a class="btn ghost" href="/logout.php">Se déconnecter</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container">
