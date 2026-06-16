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

    public function testView(): void {
        $this->view('test_sparepart', ['title' => 'Test API Request Sparepart']);
    }

    public function request(): void {
        header('Content-Type: application/json');

        $sparepart_id = $this->input('sparepart_id');
        $work_order_id = $this->input('work_order_id');
        $quantity = $this->input('quantity');

        if (!$sparepart_id || !$work_order_id || !$quantity) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap.']);
            return;
        }

        $result = $this->sparepartModel->requestParts((int) $sparepart_id, (int) $work_order_id, (int) $quantity);

        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }

        echo json_encode($result);
    }

    public function workOrderView(): void {
        $this->view('mekanik/work_order', ['title' => 'Mekanik Work Order']);
    }
}
