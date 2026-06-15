<?php

require_once ROOT_PATH . '/core/Model.php';

class Dashboard extends Model {
    public function __construct() {
        $this->db = Database::getInstance();
    }

    // [PBI-14.6] Ambil data KPI Finansial & hitung persentase konversinya
    public function getKpiMetrics() {
        $stmt = $this->db->prepare("SELECT * FROM view_kpi_dealer");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $data['total_units'] ?? 0;
        if ($total == 0) {
            return ['persen_lunas' => 0, 'persen_ditolak' => 0, 'total_unit' => 0];
        }

        return [
            'total_unit' => (int)$total,
            'persen_lunas' => round(($data['total_lunas'] / $total) * 100, 2),
            'persen_ditolak' => round(($data['total_rejected'] / $total) * 100, 2)
        ];
    }

    // [PBI-14.7] Ambil data tren kedatangan servis bulanan
    public function getTrenServis() {
        $stmt = $this->db->prepare("SELECT month_name AS bulan, total_services AS total FROM view_service_trends");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}