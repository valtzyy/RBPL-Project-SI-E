<?php

// Model untuk tabel credit_decisions (simpan keputusan approved/rejected dari leasing)
class CreditDecision extends Model
{
    protected string $table = 'credit_decisions';

    // Ambil keputusan terakhir untuk 1 application (untuk embed di status detail)
    public function findByApplication(int $applicationId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE credit_application_id = ?
             ORDER BY decided_at DESC
             LIMIT 1"
        );
        $stmt->execute([$applicationId]);
        return $stmt->fetch();
    }
}
