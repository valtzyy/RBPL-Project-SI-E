<?php

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/app/models/VehicleModel.php';
require_once ROOT_PATH . '/app/services/StockService.php';

class VehicleInventoryService
{
    private PDO $db;
    private VehicleModel $vehicleModel;
    private StockService $stockService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->vehicleModel = new VehicleModel();
        $this->stockService = new StockService();
    }

    public function list(array $filters, int $page = 1, int $perPage = 10): array
    {
        return $this->vehicleModel->paginate($filters, $page, $perPage);
    }

    public function find(int $id): array
    {
        $vehicle = $this->vehicleModel->findWithStock($id);
        if ($vehicle === false) {
            throw new RuntimeException('Kendaraan tidak ditemukan.');
        }

        return $vehicle;
    }

    public function create(array $input): int
    {
        $data = $this->validateVehicleData($input);
        $minStock = $this->nonNegativeInt($input['min_stock'] ?? 0, 'Minimum stok');

        if ($this->vehicleModel->chassisNumberExists($data['chassis_number'])) {
            throw new InvalidArgumentException('Nomor rangka sudah digunakan.');
        }

        if ($this->vehicleModel->engineNumberExists($data['engine_number'])) {
            throw new InvalidArgumentException('Nomor mesin sudah digunakan.');
        }

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $vehicleId = $this->vehicleModel->create($data);
            $this->stockService->updateMinimumStock($vehicleId, $minStock);
            if ($ownsTransaction) {
                $this->db->commit();
            }

            return $vehicleId;
        } catch (Throwable $e) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function update(int $id, array $input): void
    {
        $this->find($id);
        $data = $this->validateVehicleData($input);
        $minStock = $this->nonNegativeInt($input['min_stock'] ?? 0, 'Minimum stok');

        if ($this->vehicleModel->chassisNumberExists($data['chassis_number'], $id)) {
            throw new InvalidArgumentException('Nomor rangka sudah digunakan kendaraan lain.');
        }

        if ($this->vehicleModel->engineNumberExists($data['engine_number'], $id)) {
            throw new InvalidArgumentException('Nomor mesin sudah digunakan kendaraan lain.');
        }

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $this->vehicleModel->update($id, $data);
            $this->stockService->updateMinimumStock($id, $minStock);
            if ($ownsTransaction) {
                $this->db->commit();
            }
        } catch (Throwable $e) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        $this->find($id);

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare('DELETE FROM vehicles_stock WHERE vehicle_id = ?');
            $stmt->execute([$id]);
            $this->vehicleModel->delete($id);
            if ($ownsTransaction) {
                $this->db->commit();
            }
        } catch (Throwable $e) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new RuntimeException('Kendaraan tidak dapat dihapus karena sudah dipakai transaksi lain.');
        }
    }

    public function getFilterOptions(): array
    {
        return $this->vehicleModel->getFilterOptions();
    }

    public function getAllowedStatuses(): array
    {
        return $this->vehicleModel->getAllowedStatuses();
    }

    private function validateVehicleData(array $input): array
    {
        $data = [
            'brand' => trim((string) ($input['brand'] ?? '')),
            'type' => trim((string) ($input['type'] ?? '')),
            'color' => trim((string) ($input['color'] ?? '')),
            'chassis_number' => trim((string) ($input['chassis_number'] ?? '')),
            'engine_number' => trim((string) ($input['engine_number'] ?? '')),
            'price' => trim((string) ($input['price'] ?? '')),
            'status' => trim((string) ($input['status'] ?? 'available')),
        ];

        foreach (['brand', 'type', 'color', 'chassis_number', 'engine_number', 'price', 'status'] as $field) {
            if ($data[$field] === '') {
                throw new InvalidArgumentException(ucwords(str_replace('_', ' ', $field)) . ' wajib diisi.');
            }
        }

        if (!in_array($data['status'], $this->vehicleModel->getAllowedStatuses(), true)) {
            throw new InvalidArgumentException('Status kendaraan tidak valid.');
        }

        if (!is_numeric($data['price']) || (float) $data['price'] < 0) {
            throw new InvalidArgumentException('Harga kendaraan harus berupa angka positif.');
        }

        $data['price'] = number_format((float) $data['price'], 2, '.', '');

        return $data;
    }

    private function nonNegativeInt(mixed $value, string $label): int
    {
        if ($value === '' || $value === null) {
            return 0;
        }

        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < 0) {
            throw new InvalidArgumentException($label . ' harus berupa angka 0 atau lebih.');
        }

        return (int) $value;
    }
}
