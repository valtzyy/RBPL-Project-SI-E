<?php
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {
    private $dashboardModel;

    public function __construct($db) {
        $this->dashboardModel = new Dashboard($db);
    }

    // View halaman utama dashboard executive
    public function index() {
        require_once __DIR__ . '/../views/dashboard_manajerial.php';
    }

    // [PBI-14.6] Endpoint API untuk data KPI Finansial
    public function apiKpi() {
        header('Content-Type: application/json');
        // FIX: Mengubah json_json_encode menjadi json_encode
        echo json_encode($this->dashboardModel->getKpiMetrics());
        exit();
    }

    // [PBI-14.7] Endpoint API untuk data grafik tren servis
    public function apiTrenServis() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getTrenServis());
        exit();
    }
}