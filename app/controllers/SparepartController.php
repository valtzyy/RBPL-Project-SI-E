<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/SparepartModel.php';

class SparepartController extends Controller {
    private SparepartModel $sparepartModel;

    public function __construct() {
        $this->sparepartModel = new SparepartModel();
    }

    public function createView(): void {
        $this->view('admin/sparepart_create', ['title' => 'Master Data Sparepart']);
    }

    public function store(): void {
        $sku = $this->input('kode_sparepart');
        $name = $this->input('nama_sparepart');
        $stock = $this->input('stok_awal');
        $price = $this->input('harga_jual');

        if (!$sku || !$name || $stock === null || !$price) {
            http_response_code(400);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
            } else {
                echo "Parameter tidak lengkap.";
            }
            return;
        }

        try {
            $insertId = $this->sparepartModel->create([
                'sku' => $sku,
                'name' => $name,
                'stock' => (int) $stock,
                'price' => (float) $price,
                'min_stock' => 5 // Default
            ]);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Sparepart berhasil disimpan.', 'id' => $insertId]);
            } else {
                $this->redirect('/sparepart/create?success=1');
            }
        } catch (Exception $e) {
            http_response_code(500);
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
            } else {
                echo "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
}
