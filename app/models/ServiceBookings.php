<?php

class ServiceBooking extends Model {
    protected string $table = 'service_bookings';

    // Hitung booking per tanggal (untuk auto-reject)
    public function countBookingsByDate(string $date): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM {$this->table}
            WHERE booking_date = ? 
            AND status != 'rejected'
        ");
        $stmt->execute([$date]);
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    // Simpan booking baru
    public function storeBooking(array $data): bool {
        try {
            $this->db->beginTransaction();

            // Validasi customer ada di DB
            $stmt = $this->db->prepare(
                "SELECT id FROM customers WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$data['customer_id']]);
            if (!$stmt->fetch()) {
                $this->db->rollBack();
                return false;
            }

            // Validasi vehicle ada & statusnya available
            $stmt = $this->db->prepare(
                "SELECT id FROM vehicles 
                 WHERE id = ? AND status = 'available' LIMIT 1"
            );
            $stmt->execute([$data['vehicle_id']]);
            if (!$stmt->fetch()) {
                $this->db->rollBack();
                return false;
            }

            // Insert — strict 5 kolom sesuai skema
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table}
                    (customer_id, vehicle_id, booking_date, status)
                VALUES (?, ?, ?, 'queued')
            ");
            $stmt->execute([
                $data['customer_id'],
                $data['vehicle_id'],
                $data['booking_date'],
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("BookingError: " . $e->getMessage());
            return false;
        }
    }

    // Ambil antrean untuk dashboard SA
    public function getQueueForSA(?string $date = null): array {
        $date = $date ?? date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT sb.*,
                   c.name  AS customer_name,
                   c.phone AS customer_phone,
                   v.brand AS vehicle_brand,
                   v.type  AS vehicle_type
            FROM   {$this->table} sb
            JOIN   customers c ON c.id = sb.customer_id
            JOIN   vehicles  v ON v.id = sb.vehicle_id
            WHERE  sb.booking_date = ?
              AND  sb.status IN ('queued', 'confirmed')
            ORDER  BY sb.id ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    // Update status booking
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }

    // Cari booking by ID
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT sb.*,
                   c.name  AS customer_name,
                   c.phone AS customer_phone,
                   v.brand AS vehicle_brand,
                   v.type  AS vehicle_type
            FROM   {$this->table} sb
            JOIN   customers c ON c.id = sb.customer_id
            JOIN   vehicles  v ON v.id = sb.vehicle_id
            WHERE  sb.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}