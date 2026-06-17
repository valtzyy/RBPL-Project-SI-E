<?php
// app/models/CreditApplication.php

class CreditApplication extends Model
{
    protected string $table = 'credit_applications';

    public function findWithTransactionStatus(int $id)
    {
        $stmt = $this->db->prepare("
            SELECT ca.*, st.status AS current_tx_status 
            FROM credit_applications ca
            LEFT JOIN sales_transactions st ON ca.transaction_id = st.id
            WHERE ca.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
