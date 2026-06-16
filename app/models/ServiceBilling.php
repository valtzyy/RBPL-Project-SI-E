<?php
// app/models/ServiceBilling.php

require_once ROOT_PATH . '/core/Model.php';

class ServiceBilling extends Model
{
    protected string $table = 'work_orders';

    /**
     * Semua daftar tagihan all ready to use untuk halaman index.
     */
    public function allWithBillingDetail(): array
    {

        $stmt = $this->db->query("
           SELECT 
                wo.id                 AS work_order_id,
                wo.status             AS wo_status,
                wo.description        AS wo_description,
                wo.created_at         AS wo_created_at,

                sb.booking_date,
                sb.vehicle_name       AS vehicle_name,
                sc.plate_number       AS number_plate,

                c.name                AS customer_name,
                c.phone               AS customer_phone

            FROM work_orders wo
            JOIN service_bookings sb        ON wo.booking_id = sb.id
            JOIN service_customers sc       ON sb.service_customer_id = sc.id  -- Jalur ERD yang benar
            JOIN customers c                ON sc.customer_id = c.id           -- Jalur ERD yang benar
            WHERE wo.status = 'done' 
            ORDER BY wo.created_at DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    'booking_date'    => $row['booking_date'],
                    'vehicle_name'    => $row['vehicle_name'],
                    'plate_number'    => $row['number_plate'],
                    'customer_name'   => $row['customer_name'],
                    'customer_phone'  => $row['customer_phone'],
                ];
            }
        }
        return array_values($grouped);
    }

    /**
     * Semua tagihan bengkel yang siap atau sudah selesai dibayar.
     * Kalkulasi: total_komponen = SUM(harga_satuan × qty sparepart)
     *            biaya_jasa     = Rp 150.000 flat + Rp 25.000 per log mekanik
     *            grand_total    = total_komponen + biaya_jasa
     */
    public function findBillingDetail(int $workOrderId): array|false
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

                sc.plate_number       AS number_plate,

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
            WHERE wo.id = $workOrderId
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
                    'number_plate'     => $row['number_plate'],
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
     * Formula biaya jasa:
     *   Base Rp 150.000 + Rp 25.000 per item log mekanik.
     * Tim bisa ganti formula ini sesuai kebijakan dealer.
     */
    public function hitungBiayaJasaDariLog(int $jumlahLog): float
    {
        return 150000 + ($jumlahLog * 25000);
    }

    /**
     * Get tagihan berdasarkan nomor plat kendaraan.
     */
    public function findByPlateNumber(string $plateNumber): array
    {
        $plateNumber = urldecode($plateNumber);

        $stmt = $this->db->query("
           SELECT 
                wo.id                 AS work_order_id,
                wo.status             AS wo_status,
                wo.description        AS wo_description,
                wo.created_at         AS wo_created_at,

                sb.booking_date,
                sb.vehicle_name       AS vehicle_name,
                sc.plate_number       AS number_plate,

                c.name                AS customer_name,
                c.phone               AS customer_phone

            FROM work_orders wo
            JOIN service_bookings sb        ON wo.booking_id = sb.id
            JOIN service_customers sc       ON sb.service_customer_id = sc.id  -- Jalur ERD yang benar
            JOIN customers c                ON sc.customer_id = c.id           -- Jalur ERD yang benar
            WHERE wo.status = 'done' AND sc.plate_number = '$plateNumber'
            LIMIT 1
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    'booking_date'    => $row['booking_date'],
                    'vehicle_name'    => $row['vehicle_name'],
                    'plate_number'    => $row['number_plate'],
                    'customer_name'   => $row['customer_name'],
                    'customer_phone'  => $row['customer_phone'],
                ];
            }
        }
        return array_values($grouped);
    }
}
