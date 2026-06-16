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
     * GET /service-billing/:plateNumber
     * Tampilkan data tagihan dengan nomor plat kendaraan tertentu (dipanggil oleh JS di halaman index).
     */
    public function findByPlateNumber(string $plateNumber): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $decoded = urldecode($plateNumber);
        $data = $this->model->findByPlateNumber($decoded);

        $this->view('service-billing/test', [
            'title'   => 'Tagihan Kasir Bengkel',
            'tagihan' => $data ? $data : ["Tidak ada plat yg sesuai"],
        ]);

    }

    /**
     * GET /service-billing/detail/:plateNumber
     * Kembalikan JSON detail satu tagihan (dipanggil oleh JS di halaman index).
     */
    public function detail(string $plateNumber): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $decode = urldecode($plateNumber);
        $detail = $this->model->findBillingDetail($decode);

        if (!$detail) {
            http_response_code(404);
            echo json_encode(['error' => 'Tagihan tidak ditemukan.']);
            return;
        }

        $this->view('service-billing/detail/index', [
            'title'   => 'Detail Tagihan Kasir Bengkel',
            'detail' => $detail,
        ]);
    }

    /**
     * GET /service-billing/detail/history/:plateNumber
     * Tampilkan riwayat perubahan tagihan dengan nomor plat kendaraan tertentu (dipanggil oleh JS di halaman index).
     */
    public function detailLog(string $plateNumber): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $decode = urldecode($plateNumber);
        $detailLog = $this->model->getHistoryByPlateNumber($decode);

        $this->view('service-billing/detail/history/index', [
            'title'   => 'Riwayat Service Kendaraan',
            'detailLog' => $detailLog
        ]);
    }

    
}
