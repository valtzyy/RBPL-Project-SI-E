<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/Dashboard.php';

class DashboardController extends Controller {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new Dashboard();
    }

    // View halaman utama dashboard executive
    public function index() {
        $this->view('dashboard_manajerial');
    }

    // [PBI-14.6] Endpoint API untuk data KPI Finansial
    public function apiKpi() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getKpiMetrics());
        exit();
    }

    // [PBI-14.7] Endpoint API untuk data grafik tren servis
    public function apiTrenServis() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getTrenServis());
        exit();
    }

    // Endpoint API untuk tren penjualan mobil bulanan
    public function apiSalesTrends() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getSalesTrends());
        exit();
    }

    // Endpoint API untuk metrik aset & rasio perputaran suku cadang
    public function apiInventoryKpi() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->calculateInventoryMetrics());
        exit();
    }

    // Endpoint API untuk detail transaksi, stok kendaraan, dan merek terlaris
    public function apiDetails() {
        header('Content-Type: application/json');
        $limit = (int)$this->input('limit', 10);
        require_once ROOT_PATH . '/app/models/Sparepart.php';
        $sparepartModel = new Sparepart();
        
        $response = [
            'recent_transactions' => $this->dashboardModel->getRecentTransactions($limit),
            'stock_stats' => $this->dashboardModel->getVehicleStockStats(),
            'top_brands' => $this->dashboardModel->getTopSellingBrands(),
            'low_stock_spareparts' => $sparepartModel->getLowLevelStock()
        ];
        echo json_encode($response);
        exit();
    }
}