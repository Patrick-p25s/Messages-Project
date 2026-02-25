<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

header('Content-Type: application/json');

$currentId = (int) current_user_id();
$otherId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($otherId <= 0 || $otherId === $currentId || !find_user_by_id($otherId)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid user']);
    exit;
}

$messages = get_private_conversation($currentId, $otherId);
echo json_encode(['messages' => $messages, 'current_user_id' => $currentId]);
