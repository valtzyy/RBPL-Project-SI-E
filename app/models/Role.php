<?php

require_once ROOT_PATH . '/core/Model.php';

class Role extends Model
{
    protected string $table = 'roles';

    public function all(): array
    {
        $stmt = $this->db->query("
            SELECT id, name
            FROM roles
            ORDER BY id ASC
        ");

        return $stmt->fetchAll();
    }
}
