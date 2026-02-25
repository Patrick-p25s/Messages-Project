<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

header('Content-Type: application/json');

$currentId = (int) current_user_id();
$groupId = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;

if ($groupId <= 0 || !is_group_member($groupId, $currentId)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid group']);
    exit;
}

$messages = get_group_messages($groupId);
echo json_encode(['messages' => $messages, 'current_user_id' => $currentId]);
