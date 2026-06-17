<?php

require_once ROOT_PATH . '/core/Model.php';

class AuditLog extends Model
{
    protected string $table = 'audit_logs';

    public function latest(array $filters = []): array
    {
        $limit = (int) ($filters['limit'] ?? 100);
        $limit = $limit > 0 ? min($limit, 500) : 100;

        $sql = "
            SELECT
                al.id,
                al.user_id,
                COALESCE(u.name, 'SYSTEM') AS user_name,
                al.action,
                al.module,
                al.description,
                al.ip_address,
                al.created_at
            FROM audit_logs al
            LEFT JOIN users u ON u.id = al.user_id
            WHERE 1=1
        ";

        $params = [];

        if (($filters['module'] ?? '') !== '') {
            $sql .= " AND al.module = ? ";
            $params[] = $filters['module'];
        }

        $sql .= " ORDER BY al.created_at DESC, al.id DESC LIMIT {$limit}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function record(array $payload): bool
    {
        $userId = $this->resolveUserId();

        if ($userId === null) {
            return false;
        }

        $description = trim((string) ($payload['description'] ?? ''));
        $action = trim((string) ($payload['action'] ?? 'UNKNOWN_ACTION'));
        $module = trim((string) ($payload['module'] ?? 'GENERAL'));
        $ipAddress = $this->resolveIpAddress();

        try {
            $this->create([
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => $ipAddress,
            ]);
            return true;
        } catch (Throwable $e) {
            error_log('Audit log gagal direkam: ' . $e->getMessage());
            return false;
        }
    }

    private function resolveUserId(): ?int
    {
        $sessionUserId = $_SESSION['user_id'] ?? null;
        if (is_numeric($sessionUserId) && (int) $sessionUserId > 0) {
            return (int) $sessionUserId;
        }

        $stmt = $this->db->query("
            SELECT id
            FROM users
            WHERE status = 'active'
            ORDER BY id ASC
            LIMIT 1
        ");

        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : null;
    }

    private function resolveIpAddress(): string
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return substr((string) $ipAddress, 0, 45);
    }
}
