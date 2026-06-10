<?php

require_once ROOT_PATH . '/core/Model.php';

class User extends Model {
    protected string $table = 'users';

    /** Cari user berdasarkan email (EXAMPLE) */
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Ambil user terbaru dengan limit (EXAMPLE) */
    public function latest(int $limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}