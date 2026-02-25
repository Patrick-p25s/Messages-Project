<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

verify_csrf_or_fail();

$name = trim((string) ($_POST['name'] ?? ''));
$members = $_POST['members'] ?? [];

if ($name === '' || strlen($name) < 2) {
    flash('error', 'Le nom du groupe doit contenir au moins 2 caractères.');
    header('Location: /index.php');
    exit;
}

$memberIds = [];
if (is_array($members)) {
    foreach ($members as $member) {
        if (is_numeric($member)) {
            $memberIds[] = (int) $member;
        }
    }
}

try {
    $groupId = create_group((int) current_user_id(), $name, $memberIds);
    flash('success', 'Groupe créé avec succès.');
    header('Location: /group.php?group_id=' . $groupId);
} catch (Throwable $e) {
    flash('error', 'Impossible de créer le groupe.');
    header('Location: /index.php');
}
exit;
