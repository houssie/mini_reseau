<?php
class MessageModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($sender_id, $recipient_id, $content) {
        $stmt = $this->pdo->prepare('INSERT INTO messages (sender_id, recipient_id, content, created_at, is_read) VALUES (:s, :r, :c, :t, 0)');
        $stmt->execute([
            ':s' => $sender_id,
            ':r' => $recipient_id,
            ':c' => $content,
            ':t' => date('Y-m-d H:i:s')
        ]);
        return $this->pdo->lastInsertId();
    }

    public function fetchBetween($userA, $userB, $since = null) {
        $params = [':a' => $userA, ':b' => $userB];
        $sql = 'SELECT m.id, m.sender_id, m.recipient_id, m.content, m.is_read, m.created_at, u.name as sender_name
                FROM messages m
                LEFT JOIN users u ON u.id = m.sender_id
                WHERE (m.sender_id = :a AND m.recipient_id = :b) OR (m.sender_id = :b AND m.recipient_id = :a)';
        if ($since) {
            $sql .= ' AND m.created_at > :since';
            $params[':since'] = $since;
        }
        $sql .= ' ORDER BY m.created_at ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchForUser($userId, $since = null) {
        $params = [':uid' => $userId];
        $sql = 'SELECT m.id, m.sender_id, m.recipient_id, m.content, m.is_read, m.created_at, u.name as sender_name
                FROM messages m
                LEFT JOIN users u ON u.id = m.sender_id
                WHERE m.recipient_id = :uid';
        if ($since) {
            $sql .= ' AND m.created_at > :since';
            $params[':since'] = $since;
        }
        $sql .= ' ORDER BY m.created_at ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead(array $ids) {
        if (empty($ids)) return 0;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("UPDATE messages SET is_read = 1 WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        return $stmt->rowCount();
    }

    /**
     * Marquer tous les messages d'une conversation comme lus
     */
    public function markConversationAsRead(int $userId, int $otherUserId): int {
        $stmt = $this->pdo->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = :other AND recipient_id = :me AND is_read = 0');
        $stmt->execute([':me' => $userId, ':other' => $otherUserId]);
        return $stmt->rowCount();
    }
}
