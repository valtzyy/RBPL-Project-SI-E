<?php

require_once ROOT_PATH . '/core/Model.php';

class Invoice extends Model{
    protected string $table = 'invoices';

    /**
     * Find invoice by sales transaction ID
     */
    public function findByTransactionId(int $transactionId): array|false {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE transaction_id = ? LIMIT 1");
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }
}
