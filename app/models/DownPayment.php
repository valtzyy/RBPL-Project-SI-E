<?php
// app/models/DownPayment.php

class DownPayment extends Model
{
    protected string $table = 'down_payments';

    public function findByCreditApplicationId(int $creditApplicationId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM down_payments 
            WHERE credit_application_id = ? 
            LIMIT 1
        ");
        $stmt->execute([$creditApplicationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
