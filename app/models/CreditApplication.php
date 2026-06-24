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

    public function findForUploadSearch(string $keyword = ''): array
    {
        $sql = "
            SELECT
                ca.id AS application_id,
                ca.leasing_name,
                ca.created_at,

                c.name AS customer_name,

                v.brand,
                v.type AS vehicle_type,

                (
                    SELECT COUNT(*)
                    FROM credit_documents cd
                    WHERE cd.credit_application_id = ca.id
                ) AS doc_count

            FROM credit_applications ca

            JOIN sales_transactions st
                ON st.id = ca.transaction_id

            JOIN buyer_customers bc
                ON bc.id = st.customer_id

            JOIN customers c
                ON c.id = bc.customer_id

            JOIN vehicles v
                ON v.id = st.vehicle_id

            WHERE ca.status = 'submitted'
        ";

        $params = [];

        if ($keyword !== '') {
            $sql .= "
                AND (
                    CONCAT('CRD-', LPAD(ca.id,4,'0')) LIKE ?
                    OR c.name LIKE ?
                    OR CONCAT(v.brand,' ',v.type) LIKE ?
                    OR v.brand LIKE ?
                    OR v.type LIKE ?
                    OR ca.leasing_name LIKE ?
                )
            ";

            $search = "%{$keyword}%";

            $params = [
                $search, // No Pengajuan
                $search, // Customer
                $search, // Kendaraan lengkap
                $search, // Brand
                $search, // Type
                $search  // Leasing
            ];
        }

        $sql .= " ORDER BY ca.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

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

    public function findForTracking(string $keyword = ''): array
    {
        $sql = "
            SELECT
                ca.id AS application_id,
                ca.status,
                ca.leasing_name,
                ca.created_at,

                c.name AS customer_name,

                v.brand,
                v.type AS vehicle_type

            FROM credit_applications ca

            JOIN sales_transactions st
                ON st.id = ca.transaction_id

            JOIN buyer_customers bc
                ON bc.id = st.customer_id

            JOIN customers c
                ON c.id = bc.customer_id

            JOIN vehicles v
                ON v.id = st.vehicle_id
        ";

        $params = [];

        if ($keyword !== '') {

            $sql .= "
            WHERE
                CONCAT('CRD-', LPAD(ca.id,4,'0')) LIKE ?
                OR c.name LIKE ?
                OR ca.leasing_name LIKE ?
                OR v.brand LIKE ?
                OR v.type LIKE ?
                OR ca.status LIKE ?
            ";

            $search = "%{$keyword}%";

            $params = [
                $search,
                $search,
                $search,
                $search,
                $search,
                $search
            ];
        }

        $sql .= " ORDER BY ca.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
