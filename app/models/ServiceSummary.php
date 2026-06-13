<?php

class ServiceSummary extends Model {
    protected string $table = 'service_summary';

    // Ambil summary berdasarkan tanggal
    public function getByDate(string $date): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE date = ? LIMIT 1"
        );
        $stmt->execute([$date]);
        return $stmt->fetch() ?: null;
    }

    // Update rekap harian otomatis dari service_bookings
    public function updateSummary(string $date): bool {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (date, total_work_orders, completed)
            SELECT 
                booking_date,
                COUNT(*),
                SUM(status = 'confirmed')
            FROM service_bookings
            WHERE booking_date = ?
            ON DUPLICATE KEY UPDATE
                total_work_orders = VALUES(total_work_orders),
                completed         = VALUES(completed)
        ");
        return $stmt->execute([$date]);
    }

    // Ambil semua summary (untuk laporan manager)
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT * FROM {$this->table} ORDER BY date DESC"
        );
        return $stmt->fetchAll();
    }
}