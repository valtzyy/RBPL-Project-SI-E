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
}