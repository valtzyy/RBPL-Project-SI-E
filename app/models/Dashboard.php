<?php

require_once ROOT_PATH . '/core/Model.php';

class Dashboard extends Model {
    public function __construct() {
        $this->db = Database::getInstance();
    }

    // [PBI-14.6] Ambil data KPI Finansial & hitung persentase konversinya
    public function getKpiMetrics() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(st.id) AS total_units,
                SUM(CASE WHEN st.status = 'lunas' THEN 1 ELSE 0 END) AS total_lunas,
                SUM(CASE WHEN ca.status = 'rejected' THEN 1 ELSE 0 END) AS total_rejected
            FROM sales_transactions st
            LEFT JOIN credit_applications ca ON st.id = ca.transaction_id
        ");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $data['total_units'] ?? 0;
        if ($total == 0) {
            return ['persen_lunas' => 0, 'persen_ditolak' => 0, 'total_unit' => 0];
        }

        return [
            'total_unit' => (int)$total,
            'persen_lunas' => round((($data['total_lunas'] ?? 0) / $total) * 100, 2),
            'persen_ditolak' => round((($data['total_rejected'] ?? 0) / $total) * 100, 2)
        ];
    }

    // [PBI-14.7] Ambil data tren kedatangan servis bulanan
    public function getTrenServis() {
        $stmt = $this->db->prepare("
            SELECT 
                MONTHNAME(date) AS bulan,
                MONTH(date) AS month_num,
                SUM(total_work_orders) AS total
            FROM service_summary
            GROUP BY MONTH(date), MONTHNAME(date)
            ORDER BY month_num ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kalkulasi Nilai Aset & Rasio Perputaran Suku Cadang Gudang
    public function calculateInventoryMetrics() {
        // 1. Nilai Aset Suku Cadang (Sparepart Asset Value)
        $stmt = $this->db->prepare("SELECT SUM(stock * price) FROM spareparts");
        $stmt->execute();
        $sparepart_asset = (float)($stmt->fetchColumn() ?? 0);

        // 2. Nilai Aset Kendaraan (Vehicle Asset Value)
        // Hanya kendaraan tersedia/tersimpan yang belum terjual (available & held)
        $stmt = $this->db->prepare("SELECT SUM(price) FROM vehicles WHERE status IN ('available', 'held')");
        $stmt->execute();
        $vehicle_asset = (float)($stmt->fetchColumn() ?? 0);

        // 3. Rasio Perputaran Suku Cadang (Inventory Turnover Ratio)
        // COGS = SUM(su.quantity * sp.price)
        $stmt = $this->db->prepare("
            SELECT SUM(su.quantity * sp.price) 
            FROM sparepart_usages su 
            JOIN spareparts sp ON su.sparepart_id = sp.id
        ");
        $stmt->execute();
        $cogs = (float)($stmt->fetchColumn() ?? 0);

        $ending_inventory = $sparepart_asset;
        $avg_inventory = $ending_inventory + ($cogs / 2);

        $turnover_ratio = $avg_inventory > 0 ? round($cogs / $avg_inventory, 4) : 0;

        return [
            'sparepart_asset_value' => $sparepart_asset,
            'vehicle_asset_value' => $vehicle_asset,
            'total_asset_value' => $sparepart_asset + $vehicle_asset,
            'sparepart_cogs' => $cogs,
            'sparepart_avg_inventory' => $avg_inventory,
            'sparepart_turnover_ratio' => $turnover_ratio
        ];
    }

    // Ambil data tren bulanan penjualan mobil
    public function getSalesTrends() {
        $stmt = $this->db->prepare("
            SELECT 
                MONTHNAME(st.created_at) AS bulan,
                MONTH(st.created_at) AS bulan_num,
                COUNT(st.id) AS jumlah_terjual,
                SUM(v.price) AS total_nominal
            FROM sales_transactions st
            JOIN vehicles v ON st.vehicle_id = v.id
            WHERE st.status = 'lunas'
            GROUP BY MONTH(st.created_at), MONTHNAME(st.created_at)
            ORDER BY bulan_num ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil statistik stok kendaraan
    public function getVehicleStockStats() {
        $stmt = $this->db->prepare("
            SELECT 
                status,
                COUNT(*) AS total,
                SUM(price) AS total_price
            FROM vehicles
            GROUP BY status
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil daftar transaksi penjualan terbaru
    public function getRecentTransactions($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT 
                st.id,
                st.transaction_code,
                st.payment_type,
                st.status,
                st.created_at,
                c.name AS customer_name,
                v.brand,
                v.type,
                v.price
            FROM sales_transactions st
            JOIN customers c ON st.customer_id = c.id
            JOIN vehicles v ON st.vehicle_id = v.id
            ORDER BY st.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil statistik merek mobil terlaris
    public function getTopSellingBrands() {
        $stmt = $this->db->prepare("
            SELECT 
                v.brand,
                COUNT(st.id) AS total_sold,
                SUM(v.price) AS total_revenue
            FROM sales_transactions st
            JOIN vehicles v ON st.vehicle_id = v.id
            WHERE st.status = 'lunas'
            GROUP BY v.brand
            ORDER BY total_sold DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil data ringkasan performa hari ini
    public function getTodayPerformance() {
        // Units sold today
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sales_transactions WHERE status = 'lunas' AND DATE(created_at) = CURDATE()");
        $stmt->execute();
        $units_sold = (int)$stmt->fetchColumn();

        // Service revenue today
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(su.quantity * sp.price), 0)
            FROM sparepart_usages su
            JOIN spareparts sp ON su.sparepart_id = sp.id
            JOIN work_orders wo ON su.work_order_id = wo.id
            WHERE DATE(wo.created_at) = CURDATE()
        ");
        $stmt->execute();
        $service_revenue = (float)$stmt->fetchColumn();

        // Completed services today
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM work_orders WHERE status = 'done' AND DATE(created_at) = CURDATE()");
        $stmt->execute();
        $services_completed = (int)$stmt->fetchColumn();

        // Active service prospects/bookings today
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_bookings WHERE status IN ('queued', 'confirmed') AND booking_date = CURDATE()");
        $stmt->execute();
        $bookings_today = (int)$stmt->fetchColumn();

        return [
            'units_sold_today' => $units_sold,
            'service_revenue_today' => $service_revenue,
            'services_completed_today' => $services_completed,
            'bookings_today' => $bookings_today
        ];
    }

    // Ambil data metrik akumulatif keseluruhan
    public function getAccumulatedMetrics() {
        // Total units sold (status = 'lunas')
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sales_transactions WHERE status = 'lunas'");
        $stmt->execute();
        $total_units_sold = (int)$stmt->fetchColumn();

        // Total service revenue (dari semua sparepart yang terpakai)
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(su.quantity * sp.price), 0)
            FROM sparepart_usages su
            JOIN spareparts sp ON su.sparepart_id = sp.id
        ");
        $stmt->execute();
        $total_service_revenue = (float)$stmt->fetchColumn();

        // Active sales prospects (status = 'process')
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sales_transactions WHERE status = 'process'");
        $stmt->execute();
        $active_sales_prospects = (int)$stmt->fetchColumn();

        // Active service prospects (status = 'queued' or 'confirmed')
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM service_bookings WHERE status IN ('queued', 'confirmed')");
        $stmt->execute();
        $active_service_prospects = (int)$stmt->fetchColumn();

        // Low stock spareparts count
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM spareparts WHERE stock <= min_stock");
        $stmt->execute();
        $low_stock_count = (int)$stmt->fetchColumn();

        return [
            'total_units_sold' => $total_units_sold,
            'total_service_revenue' => $total_service_revenue,
            'active_sales_prospects' => $active_sales_prospects,
            'active_service_prospects' => $active_service_prospects,
            'low_stock_count' => $low_stock_count
        ];
    }

    // Ambil alokasi stok unit mobil berdasarkan merek
    public function getVehicleStockAllocation() {
        $stmt = $this->db->prepare("
            SELECT brand, COUNT(*) AS total, SUM(price) AS total_value
            FROM vehicles
            WHERE status IN ('available', 'held')
            GROUP BY brand
            ORDER BY total DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}