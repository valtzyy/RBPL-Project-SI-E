<?php

require_once ROOT_PATH . '/core/Model.php';

class NotificationModel extends Model
{
    protected string $table = 'notifications';

    public function getManagerUsers(): array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email
            FROM users u
            INNER JOIN roles r ON r.id = u.role_id
            WHERE LOWER(r.name) = 'manager'
              AND u.status = 'active'
        ");
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function unreadExists(int $userId, string $title, string $message): bool
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM notifications
            WHERE user_id = ?
              AND title = ?
              AND message = ?
              AND is_read = 0
            LIMIT 1
        ");
        $stmt->execute([$userId, $title, $message]);

        return (bool) $stmt->fetch();
    }
}
