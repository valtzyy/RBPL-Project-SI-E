<?php

class ServiceCustomer extends Model {
    protected string $table = 'service_customers';

    // Cari service_customer by customer_id & plate_number
    public function findByCustomerAndPlate(int $customerId, string $plate): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE customer_id = ? AND plate_number = ?
            LIMIT 1
        ");
        $stmt->execute([$customerId, $plate]);
        return $stmt->fetch() ?: null;
    }

    // Cari service_customer by plate_number
    public function findByPlate(string $plate): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE plate_number = ?
            LIMIT 1
        ");
        $stmt->execute([$plate]);
        return $stmt->fetch() ?: null;
    }

    // Update customer_id untuk service_customer tertentu (e.g. transfer kepemilikan/update owner)
    public function updateCustomer(int $id, int $customerId): bool {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET customer_id = ?
            WHERE id = ?
        ");
        return $stmt->execute([$customerId, $id]);
    }

    // Buat service_customer baru
    public function registerCustomer(int $customerId, string $plate): int {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (customer_id, plate_number)
            VALUES (?, ?)
        ");
        $stmt->execute([$customerId, $plate]);
        return (int) $this->db->lastInsertId();
    }

    // Ambil semua service_customer beserta nama customer
    public function getAllWithCustomer(): array {
        $stmt = $this->db->query("
            SELECT sc.*, c.name AS customer_name, c.phone AS customer_phone
            FROM   {$this->table} sc
            JOIN   customers c ON c.id = sc.customer_id
            ORDER  BY sc.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT sc.*, c.name AS customer_name, c.phone AS customer_phone
            FROM   {$this->table} sc
            JOIN   customers c ON c.id = sc.customer_id
            WHERE  sc.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}