<?php
// app/controllers/ServiceBillingController.php

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
     * Tampilkan daftar tagihan kasir bengkel.
     */
    public function index(): void
    {
        $tagihan = $this->model->allWithBillingDetail();

        $this->view('service-billing/test', [
            'title'   => 'Tagihan Kasir Bengkel',
            'tagihan' => $tagihan,
        ]);
    }

    /**
     * GET /service-billing/:id
     * Kembalikan JSON detail satu tagihan (dipanggil oleh JS di halaman index).
     */
    public function detail(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $detail = $this->model->findBillingDetail((int) $id);

        if (!$detail) {
            http_response_code(404);
            echo json_encode(['error' => 'Tagihan tidak ditemukan.']);
            return;
        }

        echo json_encode($detail);
    }
}
