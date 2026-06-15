<?php
require_once __DIR__ . '/../models/Sparepart.php';

class SparepartController {
    private $sparepartModel;

    public function __construct($db) {
        $this->sparepartModel = new Sparepart($db);
    }

    // Halaman Utama Logistik Gudang
    public function index() {
        $lowStock = $this->sparepartModel->getLowLevelStock(); // PBI-14.1
        $allSpareparts = $this->sparepartModel->getAll();
        $allPO = $this->sparepartModel->getAllPO();

        // Oper data ke view halaman gudang
        require_once __DIR__ . '/../views/sparepart_gudang.php';
    }

    // [PBI-14.2] Handler untuk submit form PO
    public function storePO() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplier = $_POST['supplier_name'];
            $sparepartId = $_POST['sparepart_id'];
            $qty = $_POST['quantity'];

            $this->sparepartModel->createPO($supplier, $sparepartId, $qty);
            header('Location: /sparepart');
            exit();
        }
    }

    // [PBI-14.3] Handler untuk aksi tombol "Terima Batch Suku Cadang"
    public function terimaPO() {
        if (isset($_GET['id'])) {
            $poId = $_GET['id'];
            $this->sparepartModel->terimaBatchSparepart($poId);
        }
        header('Location: /sparepart');
        exit();
    }
}