<?php

class ServiceBooking extends Model {
    protected string $table = 'service_bookings';
    private int $quota;

    public function __construct() {
        parent::__construct();
        $config      = require ROOT_PATH . '/config/app.php';
        $this->quota = $config['booking_quota'];
    }

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

    public function countActiveWorkOrders(): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM work_orders
            WHERE status = 'in_progress'
        ");
        $stmt->execute();
        return (int) ($stmt->fetch()['total'] ?? 0);
    }

    public function isSlotAvailable(string $date): bool {
        if ($this->countActiveWorkOrders() >= 5) {
            return false;
        }
        return $this->countBookingsByDate($date) < $this->quota;
    }

    public function getRemainingSlot(string $date): int {
        if ($this->countActiveWorkOrders() >= 5) {
            return 0;
        }
        return max(0, $this->quota - $this->countBookingsByDate($date));
    }

    // Simpan booking baru — sesuai skema final
    public function storeBooking(array $data): bool {
        $inTransaction = $this->db->inTransaction();
        try {
            if (!$inTransaction) {
                $this->db->beginTransaction();
            }

            // Validasi service_customer ada
            $stmt = $this->db->prepare(
                "SELECT id FROM service_customers WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$data['service_customer_id']]);
            if (!$stmt->fetch()) {
                if (!$inTransaction) {
                    $this->db->rollBack();
                }
                return false;
            }

            // Insert — sesuai 5 kolom skema final
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table}
                    (booking_date, status, service_customer_id, vehicle_name)
                VALUES (?, 'queued', ?, ?)
            ");
            $stmt->execute([
                $data['booking_date'],
                $data['service_customer_id'],
                $data['vehicle_name'],
            ]);

            if (!$inTransaction) {
                $this->db->commit();
            }
            return true;

        } catch (Exception $e) {
            if (!$inTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("BookingError: " . $e->getMessage());
            return false;
        }
    }

    // Ambil antrean untuk dashboard SA
    public function getQueueForSA(?string $date = null): array {
        $date = $date ?? date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT sb.*,
                   c.name         AS customer_name,
                   c.phone        AS customer_phone,
                   sc.plate_number,
                   wo.status      AS wo_status
            FROM   {$this->table} sb
            JOIN   service_customers sc ON sc.id = sb.service_customer_id
            JOIN   customers         c  ON c.id  = sc.customer_id
            LEFT JOIN work_orders    wo ON wo.booking_id = sb.id
            WHERE  sb.booking_date = ?
              AND  sb.status IN ('queued', 'confirmed')
            ORDER  BY 
                   (CASE WHEN wo.status = 'in_progress' THEN 1 ELSE 0 END) ASC,
                   sb.id ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT sb.*,
                   c.name         AS customer_name,
                   c.phone        AS customer_phone,
                   sc.plate_number
            FROM   {$this->table} sb
            JOIN   service_customers sc ON sc.id = sb.service_customer_id
            JOIN   customers         c  ON c.id  = sc.customer_id
            WHERE  sb.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}