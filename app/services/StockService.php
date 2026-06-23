<?php

require_once ROOT_PATH . '/app/models/VehicleStockModel.php';

class StockService
{
    private VehicleStockModel $stockModel;

    public function __construct()
    {
        $this->stockModel = new VehicleStockModel();
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

        return $newQuantity;
    }

    public function updateMinimumStock(int $vehicleId, int $minStock): void
    {
        if ($minStock < 0) {
            throw new InvalidArgumentException('Minimum stok tidak boleh negatif.');
        }

        $this->ensureStockRow($vehicleId, 0, $minStock);
        $this->stockModel->updateByVehicleId($vehicleId, [
            'min_stock' => $minStock,
        ]);
    }
}
