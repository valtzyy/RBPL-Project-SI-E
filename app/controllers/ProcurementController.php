<?php

require_once ROOT_PATH . '/app/models/Vehicle.php';
require_once ROOT_PATH . '/app/models/procurement.php';
require_once ROOT_PATH . '/app/models/procurementdetail.php';
require_once ROOT_PATH . '/app/models/procurementreceipt.php';

class ProcurementController extends Controller
{
public function index()
{
     $procurementModel = new Procurement();
        $procurements = $procurementModel->all();
         $vehicle = new Vehicle();

    $vehicles = $vehicle->all();


        $this->view('Procurement/index', [
            'procurements' => $procurements,
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

    public function receipt($id)
    {
        $procurementModel = new Procurement();
        $procurement = $procurementModel->find((int)$id);

        if (!$procurement) {
            http_response_code(404);
            exit("Data pengadaan tidak ditemukan.");
        }

        // Ambil detail kendaraan yang dipesan
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT pd.*, v.brand, v.type, v.color 
            FROM procurement_details pd
            JOIN vehicles v ON pd.vehicle_id = v.id
            WHERE pd.procurement_id = ?
        ");
        $stmt->execute([$id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('Procurement/receipt', [
            'procurement' => $procurement,
            'details' => $details
        ]);
    }

    public function storeReceipt()
    {
        $procurementId = (int)($_POST['procurement_id'] ?? 0);
        $receivedQuantities = $_POST['received_quantities'] ?? []; // Array [vehicle_id => qty]

        if ($procurementId === 0) {
            http_response_code(400);
            exit("ID Pengadaan tidak valid.");
        }

        $db = Database::getInstance();

        try {
            $db->beginTransaction();

            // 1. Ambil detail pesanan asli
            $stmt = $db->prepare("SELECT vehicle_id, quantity FROM procurement_details WHERE procurement_id = ?");
            $stmt->execute([$procurementId]);
            $orderedItems = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [vehicle_id => quantity]

            $mismatches = [];
            $isMatch = true;

            // 2. Bandingkan pesanan dengan fisik yang datang
            foreach ($orderedItems as $vehicleId => $orderedQty) {
                $receivedQty = (int)($receivedQuantities[$vehicleId] ?? 0);

                if ($receivedQty !== $orderedQty) {
                    $isMatch = false;
                    $mismatches[] = [
                        'vehicle_id' => $vehicleId,
                        'expected' => $orderedQty,
                        'received' => $receivedQty,
                        'status' => $receivedQty < $orderedQty ? 'Kurang' : 'Lebih'
                    ];
                }
            }

            // 3. Format hasil inspeksi sebagai JSON
            $inspectionData = [
                'is_compatible' => $isMatch,
                'checked_at' => date('Y-m-d H:i:s'),
                'mismatches' => $mismatches
            ];

            // 4. Simpan ke procurement_receipts
            $receipt = new ProcurementReceipt();
            $receipt->create([
                'procurement_id' => $procurementId,
                'received_by' => 'Staff Gudang', // Default staff
                'inspection_result' => json_encode($inspectionData)
            ]);

            // 5. Update status procurement menjadi 'received'
            $procurement = new Procurement();
            $procurement->update($procurementId, [
                'status' => 'received'
            ]);

            $db->commit();
header("Location: /procurement");
            exit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            exit("Gagal memproses penerimaan: " . $e->getMessage());
        }
    }

    
}