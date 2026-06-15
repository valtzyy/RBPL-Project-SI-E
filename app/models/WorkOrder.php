<?php
// app/models/WorkOrder.php

class WorkOrder extends Model 
{
    // =========================================================================
    // [PBI-11.1] DESAIN TABEL DATABASE DETAIL WORK ORDER & STATUS PEKERJAAN
    // Nama properti dicocokkan langsung dengan tabel fisik 'work_orders' di DB cloud Aiven
    // =========================================================================
    protected string $table = 'work_orders'; 

    /**
     * Mengambil instruksi kerja berdasarkan ID Mekanik aktif
     * Query ini mengambil data relasi untuk kebutuhan visualisasi di layar teknisi
     */
public function getByMechanic(int $mechanicId): array 
    {
        // PBI-11.1: Query JOIN multi-level melewati service_customers sesuai skema baru
        $query = "SELECT 
                    wo.*, 
                    c.name AS customer_name, 
                    CONCAT(v.brand, ' ', v.type) AS vehicle_model, 
                    v.color AS vehicle_color,
                    sc.plate_number,
                    sb.booking_date 
                  FROM {$this->table} wo
                  JOIN service_bookings sb ON wo.booking_id = sb.id
                  JOIN service_customers sc ON sb.service_customer_id = sc.id
                  JOIN customers c ON sc.customer_id = c.id
                  JOIN vehicles v ON sc.vehicle_id = v.id
                  WHERE wo.assigned_mechanic = ?
                  ORDER BY wo.created_at DESC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute([$mechanicId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Memperbarui status pengerjaan unit di bengkel
     */
    public function updateWorkOrderStatus(int $woId, string $status): bool 
    {
        // =========================================================================
        // [PBI-11.4] & [PBI-11.6] VALIDASI ENUM STATUS STRUKTUR MEKANIKAL
        // Memastikan inputan dari frontend valid sesuai blueprint ENUM database:
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
}