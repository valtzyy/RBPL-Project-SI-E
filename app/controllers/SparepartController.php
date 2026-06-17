<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/Sparepart.php';

class SparepartController extends Controller
{
    private $sparepartModel;

    public function __construct()
    {
        $this->sparepartModel = new Sparepart();
    }

    // Halaman Utama Logistik Gudang
    public function index()
    {
        $lowStock = $this->sparepartModel->getLowLevelStock(); // PBI-14.1
        $allSpareparts = $this->sparepartModel->getAll();
        $allPO = $this->sparepartModel->getAllPO();

        // Oper data ke view halaman gudang
        $this->view('sparepart_gudang', [
            'lowStock' => $lowStock,
            'allSpareparts' => $allSpareparts,
            'allPO' => $allPO
        ]);
    }

    // [PBI-14.2] Handler untuk submit form PO
    public function storePO()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $supplier = $this->input('supplier_name');
            $sparepartId = $this->input('sparepart_id');
            $qty = $this->input('quantity');

            $this->sparepartModel->createPO($supplier, $sparepartId, $qty);
            $this->redirect('/sparepart');
        }
    }

    // [PBI-14.3] Handler untuk aksi tombol "Terima Batch Suku Cadang"
    public function terimaPO()
    {
        $poId = $this->input('id');
        if (isset($poId)) {
            $this->sparepartModel->terimaBatchSparepart($poId);
        }
        $this->redirect('/sparepart');
    }
}
