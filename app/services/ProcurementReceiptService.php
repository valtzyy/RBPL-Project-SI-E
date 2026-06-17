<?php

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/app/models/ProcurementReceiptModel.php';
require_once ROOT_PATH . '/app/services/StockService.php';

class ProcurementReceiptService
{
    private PDO $db;
    private ProcurementReceiptModel $receiptModel;
    private StockService $stockService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->receiptModel = new ProcurementReceiptModel();
        $this->stockService = new StockService();
    }

    public function createReceipt(array $input): int
    {
        $procurementId = $this->positiveInt($input['procurement_id'] ?? null, 'Procurement');
        $receivedBy = trim((string) ($input['received_by'] ?? ''));
        $inspectionResult = trim((string) ($input['inspection_result'] ?? ''));

        $this->assertProcurementDetailsTableExists();

        $ownsTransaction = !$this->db->inTransaction();
        if ($ownsTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $this->assertProcurementExists($procurementId);

            if ($this->receiptModel->existsForProcurement($procurementId)) {
                throw new RuntimeException('Receipt untuk procurement ini sudah pernah dibuat.');
            }

            $details = $this->getProcurementDetails($procurementId);
            if ($details === []) {
                throw new RuntimeException('Procurement belum memiliki detail kendaraan.');
            }

            $receiptId = $this->receiptModel->create([
                'procurement_id' => $procurementId,
                'received_by' => $receivedBy !== '' ? $receivedBy : null,
                'inspection_result' => $inspectionResult !== '' ? $inspectionResult : null,
            ]);

            foreach ($details as $detail) {
                $this->stockService->addStock((int) $detail['vehicle_id'], (int) $detail['quantity']);
            }

            $stmt = $this->db->prepare("UPDATE procurements SET status = 'received' WHERE id = ?");
            $stmt->execute([$procurementId]);

            if ($ownsTransaction) {
                $this->db->commit();
            }

            return $receiptId;
        } catch (Throwable $e) {
            if ($ownsTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    private function getProcurementDetails(int $procurementId): array
    {
        $stmt = $this->db->prepare("
            SELECT vehicle_id, quantity
            FROM procurement_details
            WHERE procurement_id = ?
        ");
        $stmt->execute([$procurementId]);

        return $stmt->fetchAll();
    }

    private function assertProcurementExists(int $procurementId): void
    {
        $stmt = $this->db->prepare('SELECT id FROM procurements WHERE id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$procurementId]);

        if ($stmt->fetch() === false) {
            throw new RuntimeException('Procurement tidak ditemukan.');
        }
    }

    private function assertProcurementDetailsTableExists(): void
    {
        $stmt = $this->db->query("SHOW TABLES LIKE 'procurement_details'");
        if ($stmt->fetch() === false) {
            throw new RuntimeException('Tabel procurement_details tidak ditemukan di database ini.');
        }
    }

    private function positiveInt(mixed $value, string $label): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value <= 0) {
            throw new InvalidArgumentException($label . ' tidak valid.');
        }

        return (int) $value;
    }
}
