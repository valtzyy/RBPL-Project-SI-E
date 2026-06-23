<?php

require_once ROOT_PATH . '/core/Model.php';

class CreditApplication extends Model
{
    protected string $table = 'credit_applications';

    // Cari pengajuan berdasarkan transaction_id (untuk cek duplikat PBI-8.4)
    public function findByTransactionId(int $transactionId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE transaction_id = ? LIMIT 1"
        );
        $stmt->execute([$transactionId]);
        return $stmt->fetch();
    }

    // Ambil semua pengajuan dengan status tertentu (untuk PBI-8.5 Kanban)
    public function findByStatus(string $status): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE status = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    // List semua pengajuan + count dokumen uploaded (untuk Kanban PBI-8.5)
    public function findAllWithDocCount(): array
    {
        $stmt = $this->db->query(
            "SELECT ca.*,
                    (SELECT COUNT(*) FROM credit_documents
                     WHERE credit_application_id = ca.id) AS documents_uploaded
             FROM {$this->table} ca
             ORDER BY ca.created_at DESC"
        );
        return $stmt->fetchAll();
    }
}
