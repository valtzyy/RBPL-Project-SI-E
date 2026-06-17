<?php

require_once ROOT_PATH . '/core/Model.php';

class SalesTransaction extends Model{
    protected string $table = 'sales_transactions';

    /**
     * Get pending cash/tunai transactions for the finance queue
     */
    public function getPendingCashTransactions(): array {
        $sql = "SELECT st.*, 
                       c_direct.name AS customer_name, 
                       v.brand AS vehicle_brand, 
                       v.type AS vehicle_type, 
                       v.price AS vehicle_price,
                       pt.name AS payment_type_name
                FROM {$this->table} st
                LEFT JOIN customers c_direct ON st.customer_id = c_direct.id
                JOIN vehicles v ON st.vehicle_id = v.id
                JOIN payment_types pt ON st.payment_type = pt.id
                WHERE (LOWER(pt.name) LIKE '%cash%' OR LOWER(pt.name) LIKE '%tunai%')
                  AND st.status != 'lunas'
                ORDER BY st.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get complete details of a specific sales transaction
     */
    public function getTransactionDetails(int $id): array|false {
        $sql = "SELECT st.*, 
                       c_direct.name AS customer_name, 
                       c_direct.phone AS customer_phone,
                       c_direct.address AS customer_address,
                       c_direct.ktp_number AS customer_ktp,
                       v.brand AS vehicle_brand, 
                       v.type AS vehicle_type, 
                       v.color AS vehicle_color,
                       v.chassis_number AS vehicle_chassis,
                       v.engine_number AS vehicle_engine,
                       v.price AS vehicle_price,
                       pt.name AS payment_type_name,
                       u.name AS sales_name
                FROM {$this->table} st
                LEFT JOIN customers c_direct ON st.customer_id = c_direct.id
                JOIN vehicles v ON st.vehicle_id = v.id
                JOIN payment_types pt ON st.payment_type = pt.id
                JOIN users u ON st.sales_user_id = u.id
                WHERE st.id = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Update the status of a sales transaction (e.g. lunas, process, cancel)
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
