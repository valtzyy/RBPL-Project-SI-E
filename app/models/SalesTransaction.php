<?php
require_once ROOT_PATH . '/core/Model.php';

class SalesTransaction extends Model
{
    protected string $table = 'sales_transactions';

    public function getAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                COALESCE(c.name, '-') AS customer_name,
                COALESCE(c.phone, '-') AS customer_phone,
                COALESCE(v.brand, '-') AS brand,
                COALESCE(v.type, '-') AS type,
                COALESCE(v.color, '-') AS color,                   
                COALESCE(v.price, 0) AS price,
                COALESCE(u.name, '-') AS sales_name
            FROM {$this->table} st
            LEFT JOIN buyer_customers bc ON st.customer_id = bc.id
            LEFT JOIN customers c ON bc.customer_id = c.id
            LEFT JOIN vehicles  v ON st.vehicle_id  = v.id
            LEFT JOIN users     u ON st.sales_user_id = u.id
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        // 1. Ambil data asli dari tabel customers
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$data['customer_id']]);
        $customer = $stmt->fetch();

        if (!$customer) {
            throw new Exception("Data customer tidak ditemukan!");
        }

        // 2. Insert ke tabel bridge buyer_customers sesuai struktur ERD
        $stmt = $this->db->prepare("
            INSERT INTO buyer_customers (customer_id, address, ktp_number)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $customer['id'],
            $customer['address'] ?? '-',
            $customer['ktp_number'] ?? '-'
        ]);
        
        $buyerCustomerId = (int) $this->db->lastInsertId();

        // 3. Timpa customer_id menjadi ID dari buyer_customers untuk tabel sales_transactions
        $data['customer_id'] = $buyerCustomerId;

        // 4. Panggil create bawaan Model untuk menyimpan transaksi
        return parent::create($data);
    }

    public function generateCode(): string
    {
        return 'TRX-' . strtoupper(uniqid());
    }

    public function getAllWithPaymentDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                COALESCE(c.name, '-') AS customer_name,
                COALESCE(c.phone, '-') AS customer_phone,
                COALESCE(v.brand, '-') AS brand,
                COALESCE(v.type, '-') AS type,
                COALESCE(v.color, '-') AS color,
                COALESCE(v.price, 0) AS price,
                COALESCE(v.price, 0) AS total_amount,
                p.id AS payment_id,
                p.amount AS payment_amount,
                p.payment_date,
                p.status AS payment_status
            FROM {$this->table} st
            LEFT JOIN buyer_customers bc ON st.customer_id = bc.id
            LEFT JOIN customers c ON bc.customer_id = c.id
            LEFT JOIN vehicles  v ON st.vehicle_id  = v.id
            LEFT JOIN payments  p ON st.id = p.transaction_id
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }




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
