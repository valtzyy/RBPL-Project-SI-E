<?php

class SalesTransaction extends Model
{
    protected string $table = 'sales_transactions';

    public function getAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                c.name AS customer_name,
                c.phone AS customer_phone,
                v.brand, v.type, v.color, v.price,
                u.name AS sales_name
            FROM {$this->table} st
            JOIN customers c ON st.customer_id = c.id
            JOIN vehicles  v ON st.vehicle_id  = v.id
            JOIN users     u ON st.sales_user_id = u.id
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function generateCode(): string
    {
        return 'TRX-' . strtoupper(uniqid());
    }
}