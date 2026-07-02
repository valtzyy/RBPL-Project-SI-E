<?php
// app/models/NotaServis.php
// PBI-12.4 — Pembentuk nota servis resmi untuk pelanggan
//
// PERBAIKAN (sesuai hasil DESCRIBE database real):
//   - service_bookings.customer_id  -> TIDAK ADA, nama kolom aslinya service_customer_id
//   - customers.address             -> TIDAK ADA, dihapus dari query
//   - work_order_logs.status        -> enum('started','paused','checked','rework','closed')
//                                       tidak ada value 'done' di enum ini

require_once ROOT_PATH . '/core/Model.php';

class NotaServis extends Model
{
    protected string $table = 'work_orders';

    /**
     * Semua WO berstatus 'done' — sudah lunas, siap dicetak notanya.
     */
    public function allDone(): array
    {
        $stmt = $this->db->query("
            SELECT
                wo.id                AS work_order_id,
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

                COALESCE(SUM(sp.price * su.quantity), 0)  AS total_komponen,
                COALESCE(COUNT(DISTINCT wol.id), 0)       AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb  ON wo.booking_id          = sb.id
            JOIN customers c          ON sb.service_customer_id = c.id
            JOIN vehicles  v          ON sb.vehicle_id          = v.id
            LEFT JOIN users u         ON wo.assigned_mechanic   = u.id
            LEFT JOIN sparepart_usages su  ON su.work_order_id = wo.id
            LEFT JOIN spareparts sp        ON sp.id = su.sparepart_id
            LEFT JOIN work_order_logs wol  ON wol.work_order_id = wo.id

            WHERE wo.status = 'done'

            GROUP BY
                wo.id, wo.created_at,
                sb.booking_date,
                c.name, c.phone,
                v.brand, v.type, v.color, v.chassis_number, v.engine_number,
                u.name

            ORDER BY wo.created_at DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['biaya_jasa']  = 150000 + ((int)$row['jumlah_log'] * 25000);
            $row['grand_total'] = (float)$row['total_komponen'] + $row['biaya_jasa'];
        }
        unset($row);

        return $rows;
    }

    /**
     * Data lengkap satu WO untuk generate nota cetak.
     */
    public function getDataNota(int $workOrderId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                wo.id                AS work_order_id,
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

            WHERE wo.id = ? AND wo.status = 'done'
            LIMIT 1
        ");
        $stmt->execute([$workOrderId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return false;
        }

        // Sparepart
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
        $data['spareparts'] = $stmtParts->fetchAll(PDO::FETCH_ASSOC);

        // Jumlah log untuk kalkulasi jasa
        $stmtLog = $this->db->prepare("
            SELECT COUNT(*) FROM work_order_logs WHERE work_order_id = ?
        ");
        $stmtLog->execute([$workOrderId]);
        $jumlahLog = (int) $stmtLog->fetchColumn();

        $data['total_komponen'] = array_sum(array_column($data['spareparts'], 'subtotal'));
        $data['biaya_jasa']     = 150000 + ($jumlahLog * 25000);
        $data['grand_total']    = $data['total_komponen'] + $data['biaya_jasa'];

        // Nomor nota: format NS-YYYYMM-{id}
        $data['nomor_nota'] = 'NS-'
            . date('Ym', strtotime($data['wo_created_at']))
            . '-' . str_pad((string)$workOrderId, 4, '0', STR_PAD_LEFT);

        return $data;
    }
}
