<?php

require_once ROOT_PATH . '/core/Model.php';

class ProcurementReceiptModel extends Model
{
    protected string $table = 'procurement_receipts';

    public function existsForProcurement(int $procurementId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM procurement_receipts WHERE procurement_id = ? LIMIT 1');
        $stmt->execute([$procurementId]);

        return (bool) $stmt->fetch();
    }
}
