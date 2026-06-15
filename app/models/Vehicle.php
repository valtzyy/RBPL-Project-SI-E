<?php

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function getAvailable(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                v.*,
                COALESCE(vs.quantity, 0) AS stock_quantity,
                COALESCE(vs.min_stock, 0) AS stock_minimum
            FROM {$this->table} v
            LEFT JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            WHERE v.status = 'available' AND vs.quantity > 0
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function setHeld(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'held' WHERE id = ?");
        return $stmt->execute([$id]);
    }
}