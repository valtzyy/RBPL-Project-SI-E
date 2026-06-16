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
            'tagihan' => $tagihan ? $tagihan : ["Tidak ada tagihan yang ditemukan."],
        ]);
    }

     /**
     * GET /service-billing/plate/:plateNumber
     * Tampilkan data tagihan dengan nomor plat kendaraan tertentu (dipanggil oleh JS di halaman index).
     */
    public function findByPlateNumber(string $plateNumber): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->model->findByPlateNumber($plateNumber);

        $this->view('service-billing/test', [
            'title'   => 'Tagihan Kasir Bengkel',
            'tagihan' => $data ? $data : ["Tidak ada plat yg sesuai"],
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

        $this->view('service-billing/test', [
            'title'   => 'Tagihan Kasir Bengkel',
            'detail' => $detail,
        ]);
    }

    
}
