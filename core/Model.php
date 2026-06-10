<?php

class Model {
    protected PDO $db;
    protected string $table = '';  // Diisi di child class

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /** Ambil semua data */
    public function all(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    /** Cari berdasarkan ID */
    public function find(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Simpan data baru */
    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /** Update data */
    public function update(int $id, array $data): bool {
        $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$set} WHERE id = ?"
        );

        return $stmt->execute([...array_values($data), $id]);
    }

    /** Hapus data */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}