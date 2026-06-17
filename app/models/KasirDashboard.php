<?php
// app/models/KasirDashboard.php

require_once ROOT_PATH . '/core/Model.php';

class KasirDashboard extends Model
{
    protected string $table = 'work_orders';

    /**
     * Ringkasan angka untuk stat cards dashboard.
     *
     * Catatan schema:
     *   - work_orders TIDAK punya updated_at, hanya created_at
     *   - Untuk "selesai hari ini", kita pakai work_order_logs.created_at
     *     dengan status = 'done' sebagai proxy waktu penyelesaian
     */
    public function getRingkasanHarian(): array
    {
        $today = date('Y-m-d');

        // 1. Tagihan menunggu bayar
        $stmtPending = $this->db->query("
            SELECT COUNT(*) FROM work_orders WHERE status = 'ready'
        ");
        $pending = (int) $stmtPending->fetchColumn();

        // 2. WO yang statusnya 'done' dan log terakhirnya hari ini
        //    (work_order_logs.created_at dipakai karena updated_at tidak ada)
        $stmtLunas = $this->db->prepare("
            SELECT COUNT(DISTINCT wo.id)
            FROM work_orders wo
            JOIN work_order_logs wol
                ON wol.work_order_id = wo.id
               AND wol.status = 'done'
               AND DATE(wol.created_at) = ?
            WHERE wo.status = 'done'
        ");
        $stmtLunas->execute([$today]);
        $lunasHariIni = (int) $stmtLunas->fetchColumn();

        // 3. Pemasukan hari ini — WO done dengan log done hari ini
        $stmtPemasukan = $this->db->prepare("
            SELECT COALESCE(SUM(
                COALESCE(komponen.total_komponen, 0)
                + 150000
                + (COALESCE(logs.jumlah_log, 0) * 25000)
            ), 0) AS pemasukan
            FROM work_orders wo
            JOIN (
                SELECT DISTINCT work_order_id
                FROM work_order_logs
                WHERE status = 'done'
                  AND DATE(created_at) = ?
            ) done_today ON done_today.work_order_id = wo.id
            LEFT JOIN (
                SELECT su.work_order_id,
                       SUM(sp.price * su.quantity) AS total_komponen
                FROM sparepart_usages su
                JOIN spareparts sp ON sp.id = su.sparepart_id
                GROUP BY su.work_order_id
            ) komponen ON komponen.work_order_id = wo.id
            LEFT JOIN (
                SELECT work_order_id, COUNT(*) AS jumlah_log
                FROM work_order_logs
                GROUP BY work_order_id
            ) logs ON logs.work_order_id = wo.id
            WHERE wo.status = 'done'
        ");
        $stmtPemasukan->execute([$today]);
        $pemasukanHariIni = (float) $stmtPemasukan->fetchColumn();

        // 4. WO aktif (sedang dikerjakan + menunggu bayar)
        $stmtAktif = $this->db->query("
            SELECT COUNT(*) FROM work_orders
            WHERE status IN ('in_progress', 'ready')
        ");
        $woAktif = (int) $stmtAktif->fetchColumn();

        return [
            'pending'            => $pending,
            'lunas_hari_ini'     => $lunasHariIni,
            'pemasukan_hari_ini' => $pemasukanHariIni,
            'wo_aktif'           => $woAktif,
        ];
    }

    /**
     * 5 tagihan terbaru yang menunggu bayar.
     *
     * Disesuaikan dengan struktur aktual database (hasil DESCRIBE Juni 2026):
     *   - service_bookings TIDAK memiliki vehicle_id → JOIN ke vehicles dihapus
     *   - work_order_logs & sparepart_usages belum terkonfirmasi ada → pakai LEFT JOIN
     *     dengan fallback 0 jika tabel kosong atau belum ada data
     */
    public function getTagihanTerbaru(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
            SELECT
                wo.id               AS work_order_id,
                wo.status           AS wo_status,
                wo.created_at       AS wo_created_at,
                wo.description      AS deskripsi,

                c.name              AS customer_name,
                c.phone             AS customer_phone,

                sb.vehicle_name,
                sb.vehicle_color,
                v.brand,
                v.type              AS vehicle_type,

                COALESCE(SUM(sp.price * su.quantity), 0) AS total_komponen,
                COALESCE(COUNT(DISTINCT wol.id), 0)      AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb ON wo.booking_id          = sb.id
            JOIN customers c         ON sb.service_customer_id = c.id
            LEFT JOIN vehicles v          ON sb.vehicle_id     = v.id
            LEFT JOIN sparepart_usages su ON su.work_order_id  = wo.id
            LEFT JOIN spareparts sp       ON sp.id             = su.sparepart_id
            LEFT JOIN work_order_logs wol ON wol.work_order_id = wo.id

            WHERE wo.status = 'ready'

            GROUP BY
                wo.id, wo.status, wo.created_at, wo.description,
                c.name, c.phone,
                sb.vehicle_name, sb.vehicle_color,
                v.brand, v.type

            ORDER BY wo.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['biaya_jasa']  = 150000 + ((int)$row['jumlah_log'] * 25000);
            $row['grand_total'] = (float)$row['total_komponen'] + $row['biaya_jasa'];
        }
        unset($row);

        return $rows;
    }
}
