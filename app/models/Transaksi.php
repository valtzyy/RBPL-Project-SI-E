<?php
require_once ROOT_PATH . '/core/Model.php';

class Transaksi extends Model {
    protected string $table = 'sales_transactions';

    public function getHistoryWithPagination($limit, $offset, $status, $payment_type, $start_date, $end_date) {
        // [PBI-7.2] JOIN 4 TABEL DENGAN ALIAS + DYNAMIC FILTERS
        $queryBase = "FROM {$this->table} t 
                      JOIN customers c ON t.customer_id = c.id 
                      JOIN vehicles v ON t.vehicle_id = v.id
                      JOIN users u ON t.sales_user_id = u.id
                      WHERE 1=1";
        
        $params = [];

        // [PENDUKUNG PBI-7.4 s/d 7.6] FILTER SANKSI DINAMIS
        if ($status) { $queryBase .= " AND t.status = :status"; $params[':status'] = $status; }
        if ($payment_type) { $queryBase .= " AND t.payment_type = :payment_type"; $params[':payment_type'] = $payment_type; }
        if ($start_date && $end_date) { 
            $queryBase .= " AND t.created_at BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $start_date.' 00:00:00'; $params[':end_date'] = $end_date.' 23:59:59';
        }

        // [PBI-7.3] HITUNG TOTAL DATA UNTUK TOMBOL PAGINATION
        $stmtCount = $this->db->prepare("SELECT COUNT(*) as total " . $queryBase);
        $stmtCount->execute($params);
        $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // [PBI-7.2] AMBIL DATA ASLI + CASE WHEN UNTUK LABEL PAYMENT TYPE
        $sqlData = "SELECT t.id, t.transaction_code, t.created_at, t.status, t.payment_type,
                    CASE WHEN t.payment_type = 1 THEN 'Kredit' WHEN t.payment_type = 2 THEN 'Tunai' ELSE 'Lain' END as payment_type_label,
                    c.name as customer_name, v.type as vehicle_type, u.name as sales_name
                    " . $queryBase . " 
                    ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset"; // [PBI-7.3]
        
        $stmtData = $this->db->prepare($sqlData);
        foreach ($params as $key => $val) { $stmtData->bindValue($key, $val); }
        $stmtData->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmtData->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmtData->execute();

        return ['total' => $total, 'data' => $stmtData->fetchAll(PDO::FETCH_ASSOC)];
    }
}