<?php
// app/models/WorkOrder.php

class WorkOrder extends Model
{
    // =========================================================================
    // [PBI-11.1] DESAIN TABEL DATABASE DETAIL WORK ORDER & STATUS PEKERJAAN
    // Nama properti dicocokkan langsung dengan tabel fisik 'work_orders' di DB cloud
    // =========================================================================
    protected string $table = 'work_orders';

    /**
     * Mengambil instruksi kerja berdasarkan ID Mekanik aktif
     * Query JOIN disesuaikan dengan skema fisik database dealer_mobil termutakhir
     */
    public function getByMechanic(int $mechanicId): array
    {
        // PBI-11.1: Query JOIN multi-level melewati service_customers, customers, dan vehicles
        $query = "SELECT 
                    wo.*, 
                    (SELECT status FROM work_order_logs WHERE work_order_id = wo.id ORDER BY created_at DESC LIMIT 1) AS latest_log_status,
                    c.name AS customer_name, 
                    COALESCE(CONCAT(v.brand, ' ', v.type), sb.vehicle_name, 'Tidak Diketahui') AS vehicle_model, 
                    COALESCE(v.color, '-') AS vehicle_color,
                    sc.plate_number AS license_plate,
                    sb.booking_date 
                  FROM {$this->table} wo
                  LEFT JOIN service_bookings sb ON wo.booking_id = sb.id
                  LEFT JOIN service_customers sc ON sb.service_customer_id = sc.id
                  LEFT JOIN customers c ON sc.customer_id = c.id
                  LEFT JOIN vehicles v ON sb.vehicle_id = v.id
                  ORDER BY wo.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil detail satu work order beserta informasi booking, customer, dan vehicle
     */
    public function getWorkOrderDetail(int $woId): array|false
    {
        $query = "SELECT 
                    wo.*, 
                    (SELECT status FROM work_order_logs WHERE work_order_id = wo.id ORDER BY created_at DESC LIMIT 1) AS latest_log_status,
                    c.name AS customer_name, 
                    COALESCE(CONCAT(v.brand, ' ', v.type), sb.vehicle_name, 'Tidak Diketahui') AS vehicle_model, 
                    COALESCE(v.color, '-') AS vehicle_color,
                    sc.plate_number AS license_plate,
                    sb.booking_date,
                    u.name AS mechanic_name
                  FROM {$this->table} wo
                  LEFT JOIN service_bookings sb ON wo.booking_id = sb.id
                  LEFT JOIN service_customers sc ON sb.service_customer_id = sc.id
                  LEFT JOIN customers c ON sc.customer_id = c.id
                  LEFT JOIN vehicles v ON sb.vehicle_id = v.id
                  LEFT JOIN users u ON wo.assigned_mechanic = u.id
                  WHERE wo.id = ?
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$woId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Memperbarui status pengerjaan unit di bengkel
     */
    public function updateWorkOrderStatus(int $woId, string $status): bool
    {
        // =========================================================================
        // [PBI-11.4] & [PBI-11.6] VALIDASI ENUM STATUS STRUKTUR MEKANIKAL
        // 'in_progress' = Dikerjakan, 'done' = Selesai, 'ready' = Pengecekan Akhir (Siap)
        // =========================================================================
        $allowedStatus = ['in_progress', 'done', 'ready'];
        if (!in_array($status, $allowedStatus)) {
            return false;
        }

        return $this->update($woId, [
            'status' => $status
        ]);
    }

    /**
     * Mengambil seluruh data work order tanpa memfilter mekanik tertentu (untuk Service Advisor)
     */
    public function getAllWorkOrders(): array
    {
        $query = "SELECT 
                    wo.*, 
                    (SELECT status FROM work_order_logs WHERE work_order_id = wo.id ORDER BY created_at DESC LIMIT 1) AS latest_log_status,
                    c.name AS customer_name, 
                    COALESCE(CONCAT(v.brand, ' ', v.type), sb.vehicle_name, 'Tidak Diketahui') AS vehicle_model, 
                    COALESCE(v.color, '-') AS vehicle_color,
                    sc.plate_number AS license_plate,
                    sb.booking_date,
                    u.name AS mechanic_name
                  FROM {$this->table} wo
                  LEFT JOIN service_bookings sb ON wo.booking_id = sb.id
                  LEFT JOIN service_customers sc ON sb.service_customer_id = sc.id
                  LEFT JOIN customers c ON sc.customer_id = c.id
                  LEFT JOIN vehicles v ON sb.vehicle_id = v.id
                  LEFT JOIN users u ON wo.assigned_mechanic = u.id
                  ORDER BY wo.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}