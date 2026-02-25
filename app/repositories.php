<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function find_user_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function list_other_users(int $currentUserId): array
{
    $stmt = db()->prepare('SELECT id, name, email FROM users WHERE id != :id ORDER BY name ASC');
    $stmt->execute(['id' => $currentUserId]);
    return $stmt->fetchAll();
}

function create_user(string $name, string $email, string $password): int
{
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    return (int) db()->lastInsertId();
}

function create_private_message(int $senderId, int $receiverId, string $body): void
{
    $stmt = db()->prepare('INSERT INTO private_messages (sender_id, receiver_id, body) VALUES (:sender_id, :receiver_id, :body)');
    $stmt->execute([
        'sender_id' => $senderId,
        'receiver_id' => $receiverId,
        'body' => $body,
    ]);
}

function get_private_conversation(int $userA, int $userB, int $limit = 100): array
{
    $stmt = db()->prepare(
        'SELECT pm.id, pm.sender_id, pm.receiver_id, pm.body, pm.created_at, u.name AS sender_name
         FROM private_messages pm
         JOIN users u ON u.id = pm.sender_id
         WHERE (pm.sender_id = :a AND pm.receiver_id = :b)
            OR (pm.sender_id = :b AND pm.receiver_id = :a)
         ORDER BY pm.created_at ASC
         LIMIT :limit'
    );

    $stmt->bindValue(':a', $userA, PDO::PARAM_INT);
    $stmt->bindValue(':b', $userB, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function create_group(int $ownerId, string $name, array $memberIds): int
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('INSERT INTO groups_chat (name, owner_id) VALUES (:name, :owner_id)');
        $stmt->execute(['name' => $name, 'owner_id' => $ownerId]);
        $groupId = (int) $pdo->lastInsertId();

        $allMembers = array_unique(array_merge([$ownerId], array_map('intval', $memberIds)));
        $memberStmt = $pdo->prepare('INSERT INTO group_members (group_id, user_id) VALUES (:group_id, :user_id)');

        foreach ($allMembers as $memberId) {
            $memberStmt->execute(['group_id' => $groupId, 'user_id' => $memberId]);
        }

        $pdo->commit();
        return $groupId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function list_user_groups(int $userId): array
{
    $stmt = db()->prepare(
        'SELECT g.id, g.name, g.owner_id, g.created_at
         FROM groups_chat g
         JOIN group_members gm ON gm.group_id = g.id
         WHERE gm.user_id = :user_id
         ORDER BY g.name ASC'
    );
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function is_group_member(int $groupId, int $userId): bool
{
    $stmt = db()->prepare('SELECT 1 FROM group_members WHERE group_id = :group_id AND user_id = :user_id LIMIT 1');
    $stmt->execute(['group_id' => $groupId, 'user_id' => $userId]);
    return (bool) $stmt->fetchColumn();
}

function find_group_by_id(int $groupId): ?array
{
    $stmt = db()->prepare('SELECT id, name, owner_id, created_at FROM groups_chat WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $groupId]);
    $group = $stmt->fetch();
    return $group ?: null;
}

function create_group_message(int $groupId, int $senderId, string $body): void
{
    $stmt = db()->prepare('INSERT INTO group_messages (group_id, sender_id, body) VALUES (:group_id, :sender_id, :body)');
    $stmt->execute([
        'group_id' => $groupId,
        'sender_id' => $senderId,
        'body' => $body,
    ]);
}

function get_group_messages(int $groupId, int $limit = 100): array
{
    $stmt = db()->prepare(
        'SELECT gm.id, gm.group_id, gm.sender_id, gm.body, gm.created_at, u.name AS sender_name
         FROM group_messages gm
         JOIN users u ON u.id = gm.sender_id
         WHERE gm.group_id = :group_id
         ORDER BY gm.created_at ASC
         LIMIT :limit'
    );

    $stmt->bindValue(':group_id', $groupId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function delete_message(string $id){
    return "";
}