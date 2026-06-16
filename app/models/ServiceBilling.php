<?php
// app/models/ServiceBilling.php

require_once ROOT_PATH . '/core/Model.php';

class ServiceBilling extends Model
{
    protected string $table = 'work_orders';

    /**
     * Semua tagihan bengkel yang siap atau sudah selesai dibayar.
     * Kalkulasi: total_komponen = SUM(harga_satuan × qty sparepart)
     *            biaya_jasa     = Rp 150.000 flat + Rp 25.000 per log mekanik
     *            grand_total    = total_komponen + biaya_jasa
     */
    public function allWithBillingDetail(): array
    {
        // 1. SELECT dengan LEFT JOIN tanpa GROUP BY agar semua baris 'su' ditarik dari database
        $stmt = $this->db->query("
           SELECT 
                wo.id                 AS work_order_id,
                wo.status             AS wo_status,
                wo.description        AS wo_description,
                wo.created_at         AS wo_created_at,

                sb.id                 AS booking_id,
                sb.booking_date,
                sb.vehicle_name       AS vehicle_name,

                c.name                AS customer_name,
                c.phone               AS customer_phone,

                -- Menarik data sparepart per item pemakaian
                su.sparepart_id       AS sparepart_id,
                sp.name               AS nama_sparepart,
                su.quantity           AS quantity,
                sp.price              AS harga_satuan,
                (sp.price * su.quantity) AS total_komponen_item,
                
                -- SUBQUERY: Menghitung total log mekanik per Work Order untuk biaya jasa
                (SELECT COUNT(*) FROM work_order_logs wol WHERE wol.work_order_id = wo.id) AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb        ON wo.booking_id = sb.id
            JOIN service_customers sc       ON sb.service_customer_id = sc.id  -- Jalur ERD yang benar
            JOIN customers c                ON sc.customer_id = c.id           -- Jalur ERD yang benar
            LEFT JOIN sparepart_usages su   ON su.work_order_id = wo.id        -- Menghubungkan ke banyak su
            LEFT JOIN spareparts sp         ON su.sparepart_id = sp.id         -- Menarik nama dari spareparts
            WHERE wo.status = 'done'
            ORDER BY wo.created_at DESC, sp.name ASC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. PROSES GROUPING: Mengelompokkan banyak 'su' ke dalam masing-masing Work Order
        $grouped = [];

        foreach ($rows as $row) {
            $woId = $row['work_order_id'];

            // Jika rumah utama Work Order belum dibuat di array, buat sekali saja
            if (!isset($grouped[$woId])) {
                $grouped[$woId] = [
                    'work_order_id'   => $row['work_order_id'],
                    'wo_status'       => $row['wo_status'],
                    'wo_description'  => $row['wo_description'],
                    'wo_created_at'   => $row['wo_created_at'],
                    'booking_id'      => $row['booking_id'],
                    'booking_date'    => $row['booking_date'],
                    'vehicle_name'    => $row['vehicle_name'],
                    'customer_name'   => $row['customer_name'],
                    'customer_phone'  => $row['customer_phone'],
                    'jumlah_log'      => $row['jumlah_log'],
                    'biaya_jasa'      => $this->hitungBiayaJasaDariLog((int) $row['jumlah_log']),
                    'total_komponen'  => 0, // Akan diakumulasikan dari semua su di bawah
                    'grand_total'     => 0,
                    'spareparts'      => []  // Wadah kosong untuk menampung banyak sparepart
                ];
            }

            // Jika baris database saat ini mengandung sparepart, masukkan ke sub-array 'spareparts'
            if ($row['sparepart_id'] !== null) {
                $grouped[$woId]['spareparts'][] = [
                    'sparepart_id'   => $row['sparepart_id'],
                    'nama_sparepart' => $row['nama_sparepart'],
                    'quantity'       => (int) $row['quantity'],
                    'harga_satuan'   => (float) $row['harga_satuan'],
                    'subtotal'       => (float) $row['total_komponen_item']
                ];
                
                // Tambahkan subtotal komponen ini ke total akumulasi komponen Work Order terkait
                $grouped[$woId]['total_komponen'] += (float) $row['total_komponen_item'];
            }
        }

        // 3. FINALISASI: Hitung grand_total untuk setiap Work Order setelah semua sparepart terkumpul
        foreach ($grouped as &$wo) {
            $wo['grand_total'] = $wo['total_komponen'] + $wo['biaya_jasa'];
        }
        unset($wo);

        // Mengembalikan data berupa array index angka (0, 1, 2, dst) yang siap digunakan di View
        return array_values($grouped);
    }

    /**
     * Detail satu tagihan: header + baris sparepart + kalkulasi final.
     */
    public function findBillingDetail(int $workOrderId): array|false
    {
        // Header
        $stmt = $this->db->prepare("
            SELECT
                wo.id                 AS work_order_id,
                wo.status             AS wo_status,
                wo.description        AS wo_description,
                wo.created_at         AS wo_created_at,

                sb.id                 AS booking_id,
                sb.booking_date,

                c.name                AS customer_name,
                c.phone               AS customer_phone,

                v.brand,
                v.type                AS vehicle_type,
                v.color,
                v.chassis_number,

                u.name                AS mechanic_name

            FROM work_orders wo
            JOIN service_bookings sb  ON wo.booking_id        = sb.id
            JOIN customers c          ON sb.customer_id       = c.id
            JOIN vehicles  v          ON sb.vehicle_id        = v.id
            LEFT JOIN users u         ON wo.assigned_mechanic = u.id

            WHERE wo.id = ?
            LIMIT 1
        ");
        $stmt->execute([$workOrderId]);
        $header = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$header) {
            return false;
        }

        // Baris sparepart
        $stmtParts = $this->db->prepare("
            SELECT
                sp.name         AS nama_sparepart,
                sp.sku,
                sp.price        AS harga_satuan,
                su.quantity,
                (sp.price * su.quantity) AS subtotal
            FROM sparepart_usages su
            JOIN spareparts sp ON sp.id = su.sparepart_id
            WHERE su.work_order_id = ?
            ORDER BY sp.name
        ");
        $stmtParts->execute([$workOrderId]);
        $header['spareparts'] = $stmtParts->fetchAll(PDO::FETCH_ASSOC);

        // Jumlah log untuk kalkulasi jasa
        $stmtLog = $this->db->prepare("
            SELECT COUNT(*) AS jumlah_log
            FROM work_order_logs
            WHERE work_order_id = ?
        ");
        $stmtLog->execute([$workOrderId]);
        $header['jumlah_log'] = (int) $stmtLog->fetchColumn();

        // Kalkulasi
        $header['total_komponen'] = array_sum(array_column($header['spareparts'], 'subtotal'));
        $header['biaya_jasa']     = $this->hitungBiayaJasaDariLog($header['jumlah_log']);
        $header['grand_total']    = $header['total_komponen'] + $header['biaya_jasa'];

        return $header;
    }

    /**
     * Formula biaya jasa:
     *   Base Rp 150.000 + Rp 25.000 per item log mekanik.
     * Tim bisa ganti formula ini sesuai kebijakan dealer.
     */
    public function hitungBiayaJasaDariLog(int $jumlahLog): float
    {
        return 150000 + ($jumlahLog * 25000);
    }
}
