<?php

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/app/models/VehicleStockModel.php';
require_once ROOT_PATH . '/app/models/NotificationModel.php';

class StockService
{
    private PDO $db;
    private VehicleStockModel $stockModel;
    private NotificationModel $notificationModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->stockModel = new VehicleStockModel();
        $this->notificationModel = new NotificationModel();
    }

    public function ensureStockRow(int $vehicleId, int $quantity = 0, int $minStock = 0): int
    {
        if ($vehicleId <= 0) {
            throw new InvalidArgumentException('Kendaraan tidak valid.');
        }

        return $this->stockModel->createIfMissing($vehicleId, $quantity, $minStock);
    }

    public function addStock(int $vehicleId, int $quantity): int
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity stok masuk harus lebih dari 0.');
        }

        $this->ensureStockRow($vehicleId);
        $stock = $this->stockModel->findByVehicleId($vehicleId, true);
        if ($stock === false) {
            throw new RuntimeException('Data stok kendaraan tidak ditemukan.');
        }

        $newQuantity = (int) $stock['quantity'] + $quantity;
        $this->stockModel->updateByVehicleId($vehicleId, ['quantity' => $newQuantity]);
        $this->notifyIfLowStock($vehicleId);

        return $newQuantity;
    }

    public function subtractStock(int $vehicleId, int $quantity): int
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity stok keluar harus lebih dari 0.');
        }

        $this->ensureStockRow($vehicleId);
        $stock = $this->stockModel->findByVehicleId($vehicleId, true);
        if ($stock === false) {
            throw new RuntimeException('Data stok kendaraan tidak ditemukan.');
        }

        $currentQuantity = (int) $stock['quantity'];
        if ($currentQuantity < $quantity) {
            throw new RuntimeException('Stok kendaraan tidak mencukupi. Stok saat ini: ' . $currentQuantity);
        }

        $newQuantity = $currentQuantity - $quantity;
        $this->stockModel->updateByVehicleId($vehicleId, ['quantity' => $newQuantity]);
        $this->notifyIfLowStock($vehicleId);

        return $newQuantity;
    }

    public function updateStockSettings(int $vehicleId, int $quantity, int $minStock): void
    {
        if ($quantity < 0 || $minStock < 0) {
            throw new InvalidArgumentException('Quantity dan minimum stok tidak boleh negatif.');
        }

        $this->ensureStockRow($vehicleId, $quantity, $minStock);
        $this->stockModel->updateByVehicleId($vehicleId, [
            'quantity' => $quantity,
            'min_stock' => $minStock,
        ]);
        $this->notifyIfLowStock($vehicleId);
    }

    public function notifyIfLowStock(int $vehicleId): void
    {
        $stmt = $this->db->prepare("
            SELECT
                v.brand,
                v.type,
                v.color,
                COALESCE(vs.quantity, 0) AS quantity,
                COALESCE(vs.min_stock, 0) AS min_stock
            FROM vehicles v
            LEFT JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            WHERE v.id = ?
            LIMIT 1
        ");
        $stmt->execute([$vehicleId]);
        $vehicle = $stmt->fetch();

        if ($vehicle === false) {
            return;
        }

        $quantity = (int) $vehicle['quantity'];
        $minStock = (int) $vehicle['min_stock'];
        if ($quantity > $minStock) {
            return;
        }

        $title = 'Stok kendaraan minimum';
        $message = sprintf(
            'Stok %s %s %s tersisa %d, minimum %d.',
            $vehicle['brand'],
            $vehicle['type'],
            $vehicle['color'],
            $quantity,
            $minStock
        );

        foreach ($this->notificationModel->getManagerUsers() as $manager) {
            $userId = (int) $manager['id'];
            if ($this->notificationModel->unreadExists($userId, $title, $message)) {
                continue;
            }

            $this->notificationModel->create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'is_read' => 0,
            ]);
        }
    }
}
