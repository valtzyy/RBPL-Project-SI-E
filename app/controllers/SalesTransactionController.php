<?php

require_once ROOT_PATH . '/app/services/SalesTransactionService.php';

class SalesTransactionController
{
    private SalesTransactionService $salesTransactionService;

    public function __construct()
    {
        $this->salesTransactionService = new SalesTransactionService();
    }

    public function updateStatus(string $id): void
    {
        try {
            $data = $this->requestData();
            $status = (string) ($data['status'] ?? '');
            $this->salesTransactionService->updateStatus((int) $id, $status);
            $this->json(['message' => 'Status transaksi berhasil diperbarui.']);
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
