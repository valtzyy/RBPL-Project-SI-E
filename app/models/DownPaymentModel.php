<?php

require_once ROOT_PATH . '/core/Model.php';

class DownPaymentModel extends Model
{
    protected string $table = 'down_payments';

    public function getCreditApplications(): array
    {
        $stmt = $this->db->query("
            SELECT
                ca.id,
                ca.leasing_name,
                ca.status,
                c.name AS customer_name,
                st.transaction_code
            FROM credit_applications ca
            INNER JOIN credit_decisions cd
                ON cd.credit_application_id = ca.id
            INNER JOIN sales_transactions st
                ON st.id = ca.transaction_id
            INNER JOIN customers c
                ON c.id = st.customer_id
            WHERE cd.decision = 'approved'
            ORDER BY ca.id DESC
        ");

        return $stmt->fetchAll();
    }

    public function getApplicationStatus(int $creditApplicationId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                ca.id,
                ca.status,
                cd.decision
            FROM credit_applications ca
            LEFT JOIN credit_decisions cd
                ON cd.credit_application_id = ca.id
            WHERE ca.id = ?
            ORDER BY cd.decided_at DESC
            LIMIT 1
        ");

        $stmt->execute([$creditApplicationId]);

        return $stmt->fetch();
    }

    public function save(array $data): int
    {
        return $this->create([

            'credit_application_id'

            => $data['credit_application_id'],

            'amount'

            => $data['amount'],

            'paid_at'

            => $data['paid_at'],

            'verified_by'

            => null

        ]);
    }

    public function saveContract(int $creditApplicationId, string $filePath): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO credit_documents (
                credit_application_id,
                file_type,
                file_path
            )
            VALUES (?, 'SignedContract', ?)
        ");

        $stmt->execute([$creditApplicationId, $filePath]);

        return (int) $this->db->lastInsertId();
    }

    public function getCustomerHistory(): array
    {
        $stmt = $this->db->query("
            SELECT
                c.id AS customer_id,
                c.name AS customer_name,
                c.phone,
                ca.id AS credit_application_id,
                ca.status AS application_status,
                ca.leasing_name,
                ca.created_at,
                cd.decision,
                cd.notes,
                cd.decided_at,
                dp.id AS down_payment_id,
                dp.amount AS down_payment_amount,
                dp.paid_at AS down_payment_paid_at,
                verifier.name AS verified_by_name,
                COUNT(DISTINCT doc.id) AS document_count,
                SUM(CASE WHEN doc.file_type = 'SignedContract' THEN 1 ELSE 0 END) AS contract_count,
                MAX(CASE WHEN doc.file_type = 'SignedContract' THEN doc.file_path ELSE NULL END) AS contract_file_path
            FROM customers c
            INNER JOIN sales_transactions st
                ON st.customer_id = c.id
            INNER JOIN credit_applications ca
                ON ca.transaction_id = st.id
            LEFT JOIN credit_decisions cd
                ON cd.credit_application_id = ca.id
            LEFT JOIN down_payments dp
                ON dp.credit_application_id = ca.id
            LEFT JOIN users verifier
                ON verifier.id = dp.verified_by
            LEFT JOIN credit_documents doc
                ON doc.credit_application_id = ca.id
            GROUP BY
                c.id,
                c.name,
                c.phone,
                ca.id,
                ca.status,
                ca.leasing_name,
                ca.created_at,
                cd.id,
                dp.id,
                verifier.name
            ORDER BY ca.created_at DESC
        ");

        return $stmt->fetchAll();
    }
}
