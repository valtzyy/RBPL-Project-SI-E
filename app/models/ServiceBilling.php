<?php
// app/models/ServiceBilling.php
//
// CATATAN PERBAIKAN (Juni 2026):
// Versi sebelumnya mengasumsikan tabel `service_customers` dengan kolom
// `plate_number` sebagai jalur ERD. Hasil DESCRIBE terhadap database aktual
// mengonfirmasi tabel itu TIDAK ADA. Struktur sebenarnya:
//
//   service_bookings.service_customer_id  →  customers.id   (langsung, tanpa tabel perantara)
//   service_bookings.vehicle_id           →  vehicles.id    (kolom ini memang ada)
//   service_bookings.vehicle_name / vehicle_color           (fallback bila vehicle_id NULL)
//
// Identifier yang dipakai untuk lookup detail dikembalikan ke work_order_id,
// karena tidak ada plate_number di database dan JS pemanggil (service-billing/index.php)
// memang mengirim work_order_id, bukan nomor plat.

require_once ROOT_PATH . '/core/Model.php';

class ServiceBilling extends Model
{
    protected string $table = 'work_orders';

    /**
     * Semua daftar tagihan siap pakai untuk halaman index.
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
                sb.vehicle_name,
                sb.vehicle_color,

                v.brand,
                v.type                AS vehicle_type,
                v.color,
                v.chassis_number,

                c.name                AS customer_name,
                c.phone               AS customer_phone,

                COALESCE(SUM(sp.price * su.quantity), 0) AS total_komponen,
                (SELECT COUNT(*) FROM work_order_logs wol WHERE wol.work_order_id = wo.id) AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb       ON wo.booking_id          = sb.id
            JOIN customers c                ON sb.service_customer_id = c.id
            LEFT JOIN vehicles v            ON sb.vehicle_id          = v.id
            LEFT JOIN sparepart_usages su   ON su.work_order_id       = wo.id
            LEFT JOIN spareparts sp         ON su.sparepart_id        = sp.id

            WHERE wo.status IN ('ready', 'done')

            GROUP BY
                wo.id, wo.status, wo.description, wo.created_at,
                sb.booking_date, sb.vehicle_name, sb.vehicle_color,
                v.brand, v.type, v.color, v.chassis_number,
                c.name, c.phone

            ORDER BY wo.created_at DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['biaya_jasa']  = $this->hitungBiayaJasaDariLog((int) $row['jumlah_log']);
            $row['grand_total'] = (float) $row['total_komponen'] + $row['biaya_jasa'];

            // Fallback nama kendaraan: utamakan data master vehicles,
            // baru pakai vehicle_name/vehicle_color dari service_bookings jika vehicle_id NULL
            if (!$row['brand'] && !$row['vehicle_type']) {
                $row['brand']        = $row['vehicle_name'] ?? '-';
                $row['vehicle_type'] = '';
                $row['color']        = $row['vehicle_color'] ?? null;
            }
        }
        unset($row);

        return $rows;
    }

    /**
     * Detail satu tagihan berdasarkan work_order_id, lengkap dengan rincian sparepart.
     * Dipanggil oleh ServiceBillingController::detail() yang menerima ID dari fetch() di JS.
     */
    public function findBillingDetail(int $workOrderId): array|false
    {
        $sql = "
            SELECT
                wo.id                 AS work_order_id,
                wo.status             AS wo_status,
                wo.description        AS wo_description,
                wo.created_at         AS wo_created_at,

                sb.id                 AS booking_id,
                sb.booking_date,
                sb.vehicle_name,
                sb.vehicle_color,

                v.brand,
                v.type                AS vehicle_type,
                v.color,
                v.chassis_number,

                c.name                AS customer_name,
                c.phone               AS customer_phone,

                u.name                AS mechanic_name,

                su.sparepart_id       AS sparepart_id,
                sp.name               AS nama_sparepart,
                sp.sku                AS sku,
                su.quantity           AS quantity,
                sp.price              AS harga_satuan,
                (sp.price * su.quantity) AS subtotal_item,

                (SELECT COUNT(*) FROM work_order_logs wol WHERE wol.work_order_id = wo.id AND wol.status = 'rework') AS jumlah_log

            FROM work_orders wo
            JOIN service_bookings sb       ON wo.booking_id          = sb.id
            JOIN customers c                ON sb.service_customer_id = c.id
            LEFT JOIN vehicles v            ON sb.vehicle_id          = v.id
            LEFT JOIN users u               ON wo.assigned_mechanic   = u.id
            LEFT JOIN sparepart_usages su   ON su.work_order_id       = wo.id
            LEFT JOIN spareparts sp         ON su.sparepart_id        = sp.id
            JOIN work_order_logs wol ON wo.id = wol.work_order_id 
            WHERE wo.id = :workOrderId
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':workOrderId', $workOrderId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return false;
        }

        $first = $rows[0];
        $brand       = $first['brand'] ?: ($first['vehicle_name'] ?? '-');
        $vehicleType = $first['brand'] ? $first['vehicle_type'] : '';
        $color       = $first['color'] ?? $first['vehicle_color'] ?? null;

        $detail = [
            'work_order_id'   => $first['work_order_id'],
            'wo_status'       => $first['wo_status'],
            'wo_description'  => $first['wo_description'],
            'wo_created_at'   => $first['wo_created_at'],
            'booking_id'      => $first['booking_id'],
            'booking_date'    => $first['booking_date'],
            'brand'           => $brand,
            'vehicle_type'    => $vehicleType,
            'color'           => $color,
            'chassis_number'  => $first['chassis_number'],
            'customer_name'   => $first['customer_name'],
            'customer_phone'  => $first['customer_phone'],
            'mechanic_name'   => $first['mechanic_name'],
            'jumlah_log'      => (int) $first['jumlah_log'],
            'biaya_jasa'      => $this->hitungBiayaJasaDariLog((int) $first['jumlah_log']),
            'total_komponen'  => 0,
            'grand_total'     => 0,
            'spareparts'      => [],
        ];

        foreach ($rows as $row) {
            if ($row['sparepart_id'] !== null) {
                $detail['spareparts'][] = [
                    'sparepart_id'   => $row['sparepart_id'],
                    'nama_sparepart' => $row['nama_sparepart'],
                    'sku'            => $row['sku'],
                    'quantity'       => (int) $row['quantity'],
                    'harga_satuan'   => (float) $row['harga_satuan'],
                    'subtotal'       => (float) $row['subtotal_item'],
                ];
                $detail['total_komponen'] += (float) $row['subtotal_item'];
            }
        }

        $detail['grand_total'] = $detail['total_komponen'] + $detail['biaya_jasa'];

        return $detail;
    }

    /**
     * Formula biaya jasa:
     *   Base Rp 150.000 + Rp 25.000 per item log mekanik.
     * Tim bisa ganti formula ini sesuai kebijakan dealer.
     */
    public function hitungBiayaJasaDariLog(int $jumlahLog): float
    {
        return 120000 + ($jumlahLog * 25000);
    }

    /**
     * Riwayat servis (log + sparepart) untuk satu work order.
     * Tetap diidentifikasi via plate_number.
     */
    public function getHistoryByPlateNumber(string $plateNumber): array
    {
        try {
            $sql = "
            SELECT 
                wo.id AS work_order_id,
                wo.status AS wo_status,
                wo.description AS wo_description,
                wo.created_at AS wo_created_at,

                wol.id AS log_id,
                wol.status AS log_status,
                wol.notes AS log_notes,
                wol.created_at AS log_created_at,

                su.id AS usage_id,
                sp.name AS sparepart_name,
                su.quantity AS sparepart_qty
            FROM work_orders wo
            LEFT JOIN work_order_logs wol ON wo.id = wol.work_order_id
            LEFT JOIN sparepart_usages su ON wo.id = su.work_order_id
            LEFT JOIN spareparts sp ON su.sparepart_id = sp.id
            JOIN service_bookings sb ON wo.booking_id = sb.id
            JOIN service_customers sc ON sb.service_customer_id = sc.id
            WHERE sc.plate_number = :plateNumber
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':plateNumber', $plateNumber, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Grouping hasil
            $grouped = [];
            foreach ($rows as $row) {
                $woId = $row['work_order_id'];

                if (!isset($grouped[$woId])) {
                    $grouped[$woId] = [
                        'work_order_id' => $row['work_order_id'],
                        'wo_status'     => $row['wo_status'],
                        'wo_description' => $row['wo_description'],
                        'wo_created_at' => $row['wo_created_at'],
                        'logs'          => [],
                        'spareparts'    => []
                    ];
                }

                // Masukkan log
                if ($row['log_id'] !== null) {
                    $grouped[$woId]['logs'][] = [
                        'log_id'      => $row['log_id'],
                        'log_status'  => $row['log_status'],
                        'log_notes'   => $row['log_notes'],
                        'log_created' => $row['log_created_at']
                    ];
                }

                // Masukkan sparepart
                if ($row['usage_id'] !== null) {
                    $grouped[$woId]['spareparts'][] = [
                        'usage_id'       => $row['usage_id'],
                        'sparepart_name' => $row['sparepart_name'],
                        'quantity'       => $row['sparepart_qty']
                    ];
                }
            }

            return array_values($grouped);
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            return [];
        }

    }
}