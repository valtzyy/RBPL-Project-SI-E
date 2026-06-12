<?php

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function getAvailable(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function setHeld(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'held' WHERE id = ?");
        return $stmt->execute([$id]);
    }
}