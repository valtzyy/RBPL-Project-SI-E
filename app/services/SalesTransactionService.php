<?php

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/app/models/SalesTransactionModel.php';
require_once ROOT_PATH . '/app/services/StockService.php';

class SalesTransactionService
{
    private const FINAL_STATUSES = ['lunas', 'terjual'];

    private PDO $db;
    private SalesTransactionModel $salesTransactionModel;
    private StockService $stockService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->salesTransactionModel = new SalesTransactionModel();
        $this->stockService = new StockService();
    }

    public function updateStatus(int $transactionId, string $status): void
    {
        $status = trim(strtolower($status));
        $allowedStatuses = $this->salesTransactionModel->getAllowedStatuses();

        if (!in_array($status, $allowedStatuses, true)) {
            throw new InvalidArgumentException(
                'Status transaksi tidak didukung database. Status tersedia: ' . implode(', ', $allowedStatuses)
            );
        }

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $transaction = $this->salesTransactionModel->findForUpdate($transactionId);
            if ($transaction === false) {
                throw new RuntimeException('Transaksi penjualan tidak ditemukan.');
            }

            $oldStatus = strtolower((string) $transaction['status']);
            $wasFinal = in_array($oldStatus, self::FINAL_STATUSES, true);
            $becomesFinal = in_array($status, self::FINAL_STATUSES, true);

            $this->salesTransactionModel->update($transactionId, ['status' => $status]);

            if (!$wasFinal && $becomesFinal) {
                $vehicleId = (int) $transaction['vehicle_id'];
                $this->stockService->subtractStock($vehicleId, 1);
                $stmt = $this->db->prepare("UPDATE vehicles SET status = 'sold' WHERE id = ?");
                $stmt->execute([$vehicleId]);
            }

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
}
