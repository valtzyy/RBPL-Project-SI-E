<?php
class DeliverySchedule extends Model
{
    protected string $table = 'delivery_schedules';

    public function allWithTransaction(): array
    {
        $stmt = $this->db->query("
            SELECT ds.*, st.id as trx_id, st.status as trx_status,
                   v.brand, v.type, v.color, v.chassis_number
            FROM delivery_schedules ds
            JOIN sales_transactions st ON ds.transaction_id = st.id
            LEFT JOIN vehicles v ON st.vehicle_id = v.id
            ORDER BY ds.scheduled_date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findWithDetail(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT ds.*, st.id as trx_id, st.status as trx_status,
                   v.brand, v.type, v.color, v.chassis_number, v.id as vehicle_id
            FROM delivery_schedules ds
            JOIN sales_transactions st ON ds.transaction_id = st.id
            LEFT JOIN vehicles v ON st.vehicle_id = v.id
            WHERE ds.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByTransaction(int $transactionId): array|false
    {
        $stmt = $this->db->prepare("
            SELECT * FROM delivery_schedules WHERE transaction_id = ?
        ");
        $stmt->execute([$transactionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function confirmDelivery(int $id, string $signaturePath, string $signatureBase64 = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE delivery_schedules
            SET status           = 'completed',
                signature_path   = ?,
                signature_base64 = ?,
                confirmed_at     = NOW(),
                updated_at       = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$signaturePath, $signatureBase64, $id]);
    }

    public function markVehicleSold(int $vehicleId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE vehicles SET status = 'sold' WHERE id = ?
        ");
        return $stmt->execute([$vehicleId]);
    }

    public function reduceVehicleStock(int $vehicleId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE vehicles_stock
            SET quantity   = quantity - 1,
                updated_at = NOW()
            WHERE vehicle_id = ?
            AND quantity > 0
        ");
        return $stmt->execute([$vehicleId]);
    }

    public function getReadyTransactions(): array
    {
        $stmt = $this->db->query("
            SELECT st.id, st.transaction_code, c.name as customer_name,
                   v.brand, v.type, v.color
            FROM sales_transactions st
            JOIN customers c ON st.customer_id = c.id
            JOIN vehicles v ON st.vehicle_id = v.id
            LEFT JOIN delivery_schedules ds ON ds.transaction_id = st.id
            WHERE st.status = 'lunas'
            AND ds.id IS NULL
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
