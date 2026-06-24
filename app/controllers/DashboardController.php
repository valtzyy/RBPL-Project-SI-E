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
        require_once ROOT_PATH . '/app/models/SparepartModel.php';
        $sparepartModel = new SparepartModel();
        
        $response = [
            'recent_transactions' => $this->dashboardModel->getRecentTransactions($limit),
            'stock_stats' => $this->dashboardModel->getVehicleStockStats(),
            'top_brands' => $this->dashboardModel->getTopSellingBrands(),
            'low_stock_spareparts' => $sparepartModel->getLowLevelStock()
        ];
        echo json_encode($response);
        exit();
    }

    // Endpoint API untuk performa diler hari ini
    public function apiToday() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getTodayPerformance());
        exit();
    }

    // Endpoint API untuk metrik akumulatif keseluruhan
    public function apiAccumulated() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getAccumulatedMetrics());
        exit();
    }

    // Endpoint API untuk alokasi stok mobil berdasarkan merek
    public function apiStockAllocation() {
        header('Content-Type: application/json');
        echo json_encode($this->dashboardModel->getVehicleStockAllocation());
        exit();
    }

    // Ekspor laporan dashboard executive dalam format CSV
    public function exportCsv() {
        // Fetch data
        $today = $this->dashboardModel->getTodayPerformance();
        $accumulated = $this->dashboardModel->getAccumulatedMetrics();
        $inventory = $this->dashboardModel->calculateInventoryMetrics();
        $allocation = $this->dashboardModel->getVehicleStockAllocation();
        $transactions = $this->dashboardModel->getRecentTransactions(10);
        
        require_once ROOT_PATH . '/app/models/Sparepart.php';
        $sparepartModel = new Sparepart();
        $lowStock = $sparepartModel->getLowLevelStock();

        // Filename
        $filename = "laporan_dashboard_executive_" . date('Y-m-d') . ".csv";

        // Headers for download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Title
        fputcsv($output, ["LAPORAN EKSEKUTIF DEALER MOBIL"]);
        fputcsv($output, ["Tanggal Cetak", date('d-m-Y H:i:s')]);
        fputcsv($output, []); // Empty line

        // Section 1: Ringkasan Performa Hari Ini
        fputcsv($output, ["1. RINGKASAN PERFORMA HARI INI"]);
        fputcsv($output, ["Metrik", "Nilai"]);
        fputcsv($output, ["Unit Mobil Terjual (Hari Ini)", $today['units_sold_today'] . " Unit"]);
        fputcsv($output, ["Pendapatan Servis (Hari Ini)", "Rp " . number_format($today['service_revenue_today'], 0, ',', '.')]);
        fputcsv($output, ["Servis Selesai (Hari Ini)", $today['services_completed_today'] . " Kedatangan"]);
        fputcsv($output, ["Booking Servis Aktif (Hari Ini)", $today['bookings_today'] . " Booking"]);
        fputcsv($output, []);

        // Section 2: Ringkasan Performa Akumulatif
        fputcsv($output, ["2. RINGKASAN PERFORMA AKUMULATIF"]);
        fputcsv($output, ["Metrik", "Nilai"]);
        fputcsv($output, ["Total Unit Terjual (Lunas)", $accumulated['total_units_sold'] . " Unit"]);
        fputcsv($output, ["Total Pendapatan Servis (Suku Cadang)", "Rp " . number_format($accumulated['total_service_revenue'], 0, ',', '.')]);
        fputcsv($output, ["Total Prospek Aktif Penjualan", $accumulated['active_sales_prospects'] . " Transaksi Aktif"]);
        fputcsv($output, ["Total Booking Servis Aktif", $accumulated['active_service_prospects'] . " Booking"]);
        fputcsv($output, ["Total Item Suku Cadang Stok Menipis", $accumulated['low_stock_count'] . " Item"]);
        fputcsv($output, []);

        // Section 3: Nilai Aset Terinventaris & Efisiensi Gudang
        fputcsv($output, ["3. NILAI ASET & EFISIENSI GUDANG"]);
        fputcsv($output, ["Metrik", "Nilai"]);
        fputcsv($output, ["Total Nilai Aset Terinventaris", "Rp " . number_format($inventory['total_asset_value'], 0, ',', '.')]);
        fputcsv($output, ["Aset Unit Mobil (Tersedia)", "Rp " . number_format($inventory['vehicle_asset_value'], 0, ',', '.')]);
        fputcsv($output, ["Aset Suku Cadang (Gudang)", "Rp " . number_format($inventory['sparepart_asset_value'], 0, ',', '.')]);
        fputcsv($output, ["Rasio Perputaran Suku Cadang Gudang", number_format($inventory['sparepart_turnover_ratio'], 2) . "x / tahun"]);
        fputcsv($output, ["Nilai Suku Cadang Terpakai (COGS)", "Rp " . number_format($inventory['sparepart_cogs'], 0, ',', '.')]);
        fputcsv($output, []);

        // Section 4: Alokasi Stok Unit Mobil Berdasarkan Merek
        fputcsv($output, ["4. ALOKASI STOK UNIT MOBIL BERDASARKAN MEREK"]);
        fputcsv($output, ["Merek", "Jumlah Unit Tersedia/Held", "Total Nilai Aset"]);
        foreach ($allocation as $alloc) {
            fputcsv($output, [$alloc['brand'], $alloc['total'] . " Unit", "Rp " . number_format($alloc['total_value'], 0, ',', '.')]);
        }
        if (empty($allocation)) {
            fputcsv($output, ["Tidak ada data", 0, "Rp 0"]);
        }
        fputcsv($output, []);

        // Section 5: Log 10 Transaksi Penjualan Mobil Terbaru
        fputcsv($output, ["5. LOG 10 TRANSAKSI PENJUALAN MOBIL TERBARU"]);
        fputcsv($output, ["Tanggal", "Kode Transaksi", "Pelanggan", "Mobil", "Tipe Pembayaran", "Harga", "Status"]);
        foreach ($transactions as $tx) {
            fputcsv($output, [
                $tx['created_at'],
                $tx['transaction_code'],
                $tx['customer_name'],
                $tx['brand'] . " " . $tx['type'],
                $tx['payment_type'] ?? '-',
                "Rp " . number_format($tx['price'], 0, ',', '.'),
                $tx['status']
            ]);
        }
        if (empty($transactions)) {
            fputcsv($output, ["Tidak ada transaksi terbaru", "", "", "", "", "", ""]);
        }
        fputcsv($output, []);

        // Section 6: Suku Cadang Stok Kritis
        fputcsv($output, ["6. SUKU CADANG STOK KRITIS / LOW-LEVEL STOCK"]);
        fputcsv($output, ["Nama Suku Cadang", "SKU", "Sisa Stok", "Batas Minimum"]);
        foreach ($lowStock as $sp) {
            fputcsv($output, [$sp['name'], $sp['sku'], $sp['stock'] . " Pcs", $sp['min_stock'] . " Pcs"]);
        }
        if (empty($lowStock)) {
            fputcsv($output, ["Tidak ada suku cadang dengan stok kritis", "", "", ""]);
        }

        fclose($output);
        exit();
    }
}