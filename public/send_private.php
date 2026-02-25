<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

verify_csrf_or_fail();

$senderId = (int) current_user_id();
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$body = trim((string) ($_POST['body'] ?? ''));

if ($receiverId <= 0 || $body === '' || $receiverId === $senderId || !find_user_by_id($receiverId)) {
    http_response_code(422);
    exit('Invalid data');
}

create_private_message($senderId, $receiverId, $body);

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

header('Location: /chat.php?user_id=' . $receiverId);
exit;
