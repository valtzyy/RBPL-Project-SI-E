<?php

require_once ROOT_PATH . '/core/Model.php';

class VehicleStockModel extends Model
{
    protected string $table = 'vehicles_stock';

    public function findByVehicleId(int $vehicleId, bool $forUpdate = false): array|false
    {
        $sql = 'SELECT * FROM vehicles_stock WHERE vehicle_id = ? LIMIT 1';
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vehicleId]);

        return $stmt->fetch();
    }

    public function createIfMissing(int $vehicleId, int $quantity = 0, int $minStock = 0): int
    {
        $existing = $this->findByVehicleId($vehicleId);
        if ($existing !== false) {
            return (int) $existing['id'];
        }

        return $this->create([
            'vehicle_id' => $vehicleId,
            'quantity' => max(0, $quantity),
            'min_stock' => max(0, $minStock),
        ]);
    }

    public function updateByVehicleId(int $vehicleId, array $data): bool
    {
        if ($data === []) {
            throw new InvalidArgumentException('Data stok tidak boleh kosong.');
        }

        $set = implode(', ', array_map(
            fn($key) => $this->quoteIdentifier($key) . ' = ?',
            array_keys($data)
        ));

        $stmt = $this->db->prepare("UPDATE vehicles_stock SET {$set} WHERE vehicle_id = ?");

        return $stmt->execute([...array_values($data), $vehicleId]);
    }
}
