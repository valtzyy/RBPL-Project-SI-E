<?php

require_once ROOT_PATH . '/core/Model.php';

class SalesTransactionModel extends Model
{
    protected string $table = 'sales_transactions';

    public function findForUpdate(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM sales_transactions WHERE id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function getAllowedStatuses(): array
    {
        $stmt = $this->db->query("SHOW COLUMNS FROM sales_transactions LIKE 'status'");
        $column = $stmt->fetch();
        $type = (string) ($column['Type'] ?? '');

        if (preg_match("/^enum\\((.*)\\)$/", $type, $matches) !== 1) {
            return ['process', 'lunas', 'cancel', 'terjual'];
        }

        preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $matches[1], $values);

        return array_map(fn($value) => stripcslashes($value), $values[1]);
    }
}
