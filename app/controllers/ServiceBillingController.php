<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/ServiceBilling.php';

class ServiceBillingController extends Controller
{
    private ServiceBilling $model;

    public function __construct()
    {
        $this->model = new ServiceBilling();
    }

    /**
     * GET /service-billing
     * Tampilkan daftar tagihan kasir bengkel (PBI-12.1).
     */
    public function index(): void
    {
        $tagihan = $this->model->allWithBillingDetail();

        $this->view('service-billing/index', [
            'title'   => 'Tagihan Kasir Bengkel',
            'tagihan' => $tagihan,
        ]);
    }

    /**
     * GET /service-billing/:id
     * Kembalikan JSON detail satu tagihan berdasarkan work_order_id.
     * Dipanggil oleh fetch() di service-billing/index.php saat modal dibuka.
     */
    public function detail(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        // var_dump($id); // Debugging: pastikan ID diterima dengan benar
        $workOrderId = (int) $id;
        $detail      = $this->model->findBillingDetail($workOrderId);

        if (!$detail) {
            http_response_code(404);
            echo json_encode(['error' => 'Tagihan tidak ditemukan.']);
            return;
        }

        echo json_encode($detail);
    }

    /**
     * GET /service-billing/:id/history
     * Kembalikan JSON riwayat log + sparepart untuk satu work order.
     */
    public function detailLog(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $workOrderId = (int) $id;
        $history     = $this->model->getHistoryByWorkOrderId($workOrderId);

        if (!$history) {
            http_response_code(404);
            echo json_encode(['error' => 'Riwayat tidak ditemukan.']);
            return;
        }

        echo json_encode($history);
    }

    // public function findBillingDetail(string $plateNumber): array|false
    // {
    //     var_dump($plateNumber);
    //     try {
    //         $sql = "
    //        SELECT 
    //             wo.id                 AS work_order_id,
    //             wo.status             AS wo_status,
    //         JOIN customers c                ON sc.customer_id = c.id           -- Jalur ERD yang benar
    //         LEFT JOIN sparepart_usages su   ON su.work_order_id = wo.id        -- Menghubungkan ke banyak su
    //         LEFT JOIN spareparts sp         ON su.sparepart_id = sp.id         -- Menarik nama dari spareparts
    //         WHERE wo.status = 'done' AND sc.plate_number = :plateNumber
    //     ";
    //         $stmt = $this->db->prepare($sql);
    //         $stmt->bindValue(':plateNumber', $plateNumber, PDO::PARAM_STR);
    //         $stmt->execute();
    //         $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         // 2. PROSES GROUPING: Mengelompokkan banyak 'su' ke dalam masing-masing Work Order
    //         $grouped = [];

    //         foreach ($rows as $row) {
    //             $woId = $row['work_order_id'];

    //             // Jika rumah utama Work Order belum dibuat di array, buat sekali saja
    //             if (!isset($grouped[$woId])) {
    //                 $grouped[$woId] = [
    //                     'work_order_id'   => $row['work_order_id'],
    //                     'wo_status'       => $row['wo_status'],
    //                     'wo_description'  => $row['wo_description'],
    //                     'wo_created_at'   => $row['wo_created_at'],
    //                     'booking_id'      => $row['booking_id'],
    //                     'booking_date'    => $row['booking_date'],
    //                     'vehicle_name'    => $row['vehicle_name'],
    //                     'customer_name'   => $row['customer_name'],
    //                     'customer_phone'  => $row['customer_phone'],
    //                     'number_plate'     => $row['number_plate'],
    //                     'jumlah_log'      => $row['jumlah_log'],
    //                     'biaya_jasa'      => $this->hitungBiayaJasaDariLog((int) $row['jumlah_log']),
    //                     'total_komponen'  => 0, // Akan diakumulasikan dari semua su di bawah
    //                     'grand_total'     => 0,
    //                     'spareparts'      => []  // Wadah kosong untuk menampung banyak sparepart
    //                 ];
    //             }

    //             // Jika baris database saat ini mengandung sparepart, masukkan ke sub-array 'spareparts'
    //             if ($row['sparepart_id'] !== null) {
    //                 $grouped[$woId]['spareparts'][] = [
    //                     'sparepart_id'   => $row['sparepart_id'],
    //                     'nama_sparepart' => $row['nama_sparepart'],
    //                     'quantity'       => (int) $row['quantity'],
    //                     'harga_satuan'   => (float) $row['harga_satuan'],
    //                     'subtotal'       => (float) $row['total_komponen_item']
    //                 ];

    //                 // Tambahkan subtotal komponen ini ke total akumulasi komponen Work Order terkait
    //                 $grouped[$woId]['total_komponen'] += (float) $row['total_komponen_item'];
    //             }
    //         }

    //         // 3. FINALISASI: Hitung grand_total untuk setiap Work Order setelah semua sparepart terkumpul
    //         foreach ($grouped as &$wo) {
    //             $wo['grand_total'] = $wo['total_komponen'] + $wo['biaya_jasa'];
    //         }
    //         unset($wo);

    //         // Mengembalikan data berupa array index angka (0, 1, 2, dst) yang siap digunakan di View
    //         return array_values($grouped);
    //     } catch (PDOException $e) {
    //         error_log("DB Error: " . $e->getMessage());
    //         return [];
    //     }
    // }
}
