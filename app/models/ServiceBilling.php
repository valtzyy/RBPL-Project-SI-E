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
        $stmt = $this->db->query("
            SELECT
                wo.id                            AS work_order_id,
                wo.status                        AS wo_status,
                wo.description                   AS wo_description,
                wo.created_at                    AS wo_created_at,

                sb.id                            AS booking_id,
                sb.booking_date,

                c.name                           AS customer_name,
                c.phone                          AS customer_phone,

                v.brand,
                v.type                           AS vehicle_type,
                v.color,
                v.chassis_number,

                COALESCE(SUM(sp.price * su.quantity), 0)  AS total_komponen,
                COUNT(su.id)                              AS jumlah_sparepart,
                COALESCE(COUNT(wol.id), 0)                AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb  ON wo.booking_id  = sb.id
            JOIN customers c          ON sb.customer_id = c.id
            JOIN vehicles  v          ON sb.vehicle_id  = v.id
            LEFT JOIN sparepart_usages su   ON su.work_order_id = wo.id
            LEFT JOIN spareparts sp         ON sp.id = su.sparepart_id
            LEFT JOIN work_order_logs wol   ON wol.work_order_id = wo.id

            WHERE wo.status IN ('ready', 'done')

            GROUP BY
                wo.id, wo.status, wo.description, wo.created_at,
                sb.id, sb.booking_date,
                c.name, c.phone,
                v.brand, v.type, v.color, v.chassis_number

            ORDER BY
                FIELD(wo.status, 'ready', 'done'),
                wo.created_at DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kalkulasi biaya jasa & grand total di PHP
        foreach ($rows as &$row) {
            $row['biaya_jasa']  = $this->hitungBiayaJasaDariLog((int) $row['jumlah_log']);
            $row['grand_total'] = (float) $row['total_komponen'] + $row['biaya_jasa'];
        }
        unset($row);

        return $rows;
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
