<?php
// app/models/RiwayatServis.php
// PBI-12.6 — Pencarian riwayat service by chassis/engine/nama pelanggan
// PBI-12.7 — Endpoint historical logs per kendaraan (dipakai modal di halaman PBI-12.6)
//
// PERBAIKAN (sesuai hasil DESCRIBE database real):
//   - service_bookings.customer_id  -> TIDAK ADA, nama kolom aslinya service_customer_id
//     (muncul di 2 query: cariRiwayat() dan getHistoricalLogs())

require_once ROOT_PATH . '/core/Model.php';

class RiwayatServis extends Model
{
    protected string $table = 'work_orders';

    /**
     * PBI-12.6
     * Cari riwayat servis berdasarkan nomor chassis, nomor mesin, atau nama pelanggan.
     *
     * Catatan schema: tabel vehicles tidak punya plate_number.
     * Pencarian by "plat" dialihkan ke chassis_number — satu-satunya
     * identifier unik kendaraan yang tersedia di schema saat ini.
     */
    public function cariRiwayat(string $keyword): array
    {
        $like = '%' . $keyword . '%';

        $stmt = $this->db->prepare("
            SELECT
                wo.id                AS work_order_id,
                wo.status            AS wo_status,
                wo.description       AS wo_description,
                wo.created_at        AS wo_created_at,

                sb.booking_date,

                c.name               AS customer_name,
                c.phone              AS customer_phone,

                v.brand,
                v.type               AS vehicle_type,
                v.color,
                v.chassis_number,
                v.engine_number,

                u.name               AS mechanic_name,

                COALESCE(SUM(sp.price * su.quantity), 0)    AS total_komponen,
                COALESCE(COUNT(DISTINCT wol.id), 0)         AS jumlah_log,
                COALESCE(COUNT(DISTINCT su.id), 0)          AS jumlah_sparepart

            FROM work_orders wo
            JOIN service_bookings sb  ON wo.booking_id          = sb.id
            JOIN customers c          ON sb.service_customer_id = c.id
            JOIN vehicles  v          ON sb.vehicle_id          = v.id
            LEFT JOIN users u         ON wo.assigned_mechanic   = u.id
            LEFT JOIN sparepart_usages su  ON su.work_order_id = wo.id
            LEFT JOIN spareparts sp        ON sp.id = su.sparepart_id
            LEFT JOIN work_order_logs wol  ON wol.work_order_id = wo.id

            WHERE
                v.chassis_number LIKE ?
                OR v.engine_number  LIKE ?
                OR c.name           LIKE ?

            GROUP BY
                wo.id, wo.status, wo.description, wo.created_at,
                sb.booking_date,
                c.name, c.phone,
                v.brand, v.type, v.color, v.chassis_number, v.engine_number,
                u.name

            ORDER BY wo.created_at DESC
        ");

        $stmt->execute([$like, $like, $like]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $biayaJasa          = 150000 + ((int)$row['jumlah_log'] * 25000);
            $row['biaya_jasa']  = $biayaJasa;
            $row['grand_total'] = (float)$row['total_komponen'] + $biayaJasa;
        }
        unset($row);

        return $rows;
    }

    /**
     * PBI-12.7
     * Query historical logs per work order — dipakai oleh modal "Lihat Log"
     * di halaman riwayat (PBI-12.6).
     */
    public function getHistoricalLogs(int $workOrderId): array|false
    {
        $stmtHeader = $this->db->prepare("
            SELECT
                wo.id                AS work_order_id,
                wo.status            AS wo_status,
                wo.description       AS wo_description,
                wo.created_at        AS wo_created_at,

                sb.booking_date,

                c.name               AS customer_name,
                c.phone              AS customer_phone,

                v.brand,
                v.type               AS vehicle_type,
                v.color,
                v.chassis_number,
                v.engine_number,

                u.name               AS mechanic_name

            FROM work_orders wo
            JOIN service_bookings sb  ON wo.booking_id          = sb.id
            JOIN customers c          ON sb.service_customer_id = c.id
            JOIN vehicles  v          ON sb.vehicle_id          = v.id
            LEFT JOIN users u         ON wo.assigned_mechanic   = u.id

            WHERE wo.id = ?
            LIMIT 1
        ");
        $stmtHeader->execute([$workOrderId]);
        $header = $stmtHeader->fetch(PDO::FETCH_ASSOC);

        if (!$header) {
            return false;
        }

        // Timeline log mekanik
        // Status real: started, paused, checked, rework, closed
        $stmtLogs = $this->db->prepare("
            SELECT
                id,
                status,
                notes,
                created_at
            FROM work_order_logs
            WHERE work_order_id = ?
            ORDER BY created_at ASC
        ");
        $stmtLogs->execute([$workOrderId]);
        $header['logs'] = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

        // Sparepart yang dipakai
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

        $header['total_komponen'] = array_sum(array_column($header['spareparts'], 'subtotal'));
        $header['biaya_jasa']     = 150000 + (count($header['logs']) * 25000);
        $header['grand_total']    = $header['total_komponen'] + $header['biaya_jasa'];

        return $header;
    }
}
