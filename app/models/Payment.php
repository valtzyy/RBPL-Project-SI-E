<?php

require_once ROOT_PATH . '/core/Model.php';

class Payment extends Model {
    protected string $table = 'payments';

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

    public function findByTransactionId(int $transactionId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE transaction_id = ? LIMIT 1");
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }
    
    public function verifyPayment(int $paymentId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'verified', verified_by = ? WHERE id = ?");
        return $stmt->execute([$userId, $paymentId]);
    }
}
