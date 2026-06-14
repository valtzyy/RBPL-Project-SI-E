<?php

require_once ROOT_PATH . '/core/Model.php';

class SalesTransaction extends Model
{
    protected string $table = 'sales_transactions';

    // JOIN dengan payment_types untuk validasi payment_type di PBI-8.4
    public function findWithPaymentType(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT st.*, pt.name AS payment_name
             FROM {$this->table} st
             JOIN payment_types pt ON pt.id = st.payment_type
             WHERE st.id = ?
             LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
