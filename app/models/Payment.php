<?php

require_once ROOT_PATH . '/core/Model.php';

class Payment extends Model {
    protected string $table = 'payments';

    /**
     * Get all payments for a sales transaction, including verifier's name
     */
    public function getPaymentsByTransaction(int $transactionId): array {
        $sql = "SELECT p.*, u.name AS verifier_name 
                FROM {$this->table} p
                LEFT JOIN users u ON p.verified_by = u.id
                WHERE p.transaction_id = ?
                ORDER BY p.payment_date DESC, p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$transactionId]);
        return $stmt->fetchAll();
    }

    /**
     * Verify a specific payment by a finance user
     */
    public function verify(int $paymentId, int $financeUserId): bool {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET status = 'verified', verified_by = ? WHERE id = ?"
        );
        return $stmt->execute([$financeUserId, $paymentId]);
    }

    /**
     * Sum the total verified payments for a sales transaction
     */
    public function getSumVerifiedPayments(int $transactionId): float {
        $stmt = $this->db->prepare(
            "SELECT SUM(amount) AS total FROM {$this->table} WHERE transaction_id = ? AND status = 'verified'"
        );
        $stmt->execute([$transactionId]);
        $result = $stmt->fetch();
        return (float) ($result['total'] ?? 0.0);
    }

    /**
     * Get details of a verified payment for the digital receipt (kwitansi)
     */
    public function getVerifiedPaymentDetails(int $paymentId): array|false {
        $sql = "SELECT p.*, 
                       st.transaction_code, 
                       c_direct.name AS customer_name, 
                       c_direct.phone AS customer_phone,
                       v.brand AS vehicle_brand, 
                       v.type AS vehicle_type, 
                       v.chassis_number AS vehicle_chassis, 
                       v.engine_number AS vehicle_engine,
                       u.name AS verifier_name
                FROM {$this->table} p
                JOIN sales_transactions st ON p.transaction_id = st.id
                LEFT JOIN customers c_direct ON st.customer_id = c_direct.id
                JOIN vehicles v ON st.vehicle_id = v.id
                LEFT JOIN users u ON p.verified_by = u.id
                WHERE p.id = ? AND p.status = 'verified'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$paymentId]);
        return $stmt->fetch();
    }
}
