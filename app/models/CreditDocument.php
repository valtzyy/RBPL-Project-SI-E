<?php
// app/models/CreditDocument.php

require_once ROOT_PATH . '/core/Model.php';

class CreditDocument extends Model
{
    protected string $table = 'credit_documents';

    public function findByApplication(int $applicationId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE credit_application_id = ?"
        );
        $stmt->execute([$applicationId]);
        return $stmt->fetchAll();
    }
}
