<?php

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function getAvailable(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                v.*,
                vs.quantity AS stock_quantity,
                vs.min_stock AS stock_minimum
            FROM {$this->table} v
            JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            WHERE v.status = 'available'
            AND vs.quantity > 0
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