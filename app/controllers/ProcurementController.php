<?php

require_once ROOT_PATH . '/app/models/Vehicles.php';
require_once ROOT_PATH . '/app/models/procurement.php';
require_once ROOT_PATH . '/app/models/procurementdetail.php';

class ProcurementController extends Controller
{
public function index()
{
    $vehicle = new Vehicle();

    $vehicles = $vehicle->all();

    $this->view('Procurement/form', [
        'vehicles' => $vehicles
    ]);
}

    public function store()
    {
        // 1. Ambil data input
        $vehicleIds = $_POST['vehicle_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        // 2. Validasi input
        if (empty($vehicleIds) || empty($quantities) || count($vehicleIds) !== count($quantities)) {
            http_response_code(400);
            exit("Data permintaan tidak valid atau kosong.");
        }

        $db = Database::getInstance();
        
        try {
            // 3. Mulai Transaksi
            $db->beginTransaction();

            // 4. Simpan Header Procurement
            $procurement = new Procurement();
            $procurementId = $procurement->create([
                'request_code' => 'PR-' . time(),
                'requested_by' => 2, // admin dealer
                'status' => 'sent'
            ]);

            // 5. Simpan Detail Procurement (Looping)
            $detail = new ProcurementDetail();
            for ($i = 0; $i < count($vehicleIds); $i++) {
                $vehicleId = (int)$vehicleIds[$i];
                $qty = (int)$quantities[$i];

                if ($qty < 1) {
                    throw new Exception("Jumlah kendaraan harus minimal 1.");
                }

                $detail->create([
                    'procurement_id' => $procurementId,
                    'vehicle_id' => $vehicleId,
                    'quantity' => $qty
                ]);
            }

            // 6. Commit Transaksi
            $db->commit();

            echo "Permintaan pengadaan berhasil dikirim dengan ID Procurement: " . $procurementId;

        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            exit("Gagal menyimpan permintaan pengadaan: " . $e->getMessage());
        }
    }
}