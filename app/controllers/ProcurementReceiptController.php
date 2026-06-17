<?php

require_once ROOT_PATH . '/app/services/ProcurementReceiptService.php';

class ProcurementReceiptController
{
    private ProcurementReceiptService $receiptService;

    public function __construct()
    {
        $this->receiptService = new ProcurementReceiptService();
    }

    public function store(): void
    {
        try {
            $receiptId = $this->receiptService->createReceipt($this->requestData());
            $this->json([
                'message' => 'Receipt procurement berhasil dibuat dan stok kendaraan bertambah.',
                'id' => $receiptId,
            ], 201);
        } catch (Throwable $e) {
            $this->json(['message' => $e->getMessage()], 422);
        }
    }

    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
    }

    private function requestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            if ($raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return $_POST;
    }
}
