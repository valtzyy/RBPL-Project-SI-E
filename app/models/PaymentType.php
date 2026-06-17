<?php

require_once ROOT_PATH . '/core/Model.php';

class PaymentType extends Model
{
    protected string $table = 'payment_types';

    // Cari payment type berdasarkan nama (untuk switch-cash butuh ID 'tunai')
    public function findByName(string $name): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE name = ? LIMIT 1"
        );
        $stmt->execute([$name]);
        return $stmt->fetch();
    }
}
