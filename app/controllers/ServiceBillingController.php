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
}