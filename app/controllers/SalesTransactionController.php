<?php
require_once ROOT_PATH . '/app/models/SalesTransaction.php';
require_once ROOT_PATH . '/app/models/Customer.php';
require_once ROOT_PATH . '/app/models/Vehicle.php';
require_once ROOT_PATH . '/app/services/SalesTransactionService.php';

class SalesTransactionController extends Controller
{
    // GET /transactions — tampilkan semua transaksi
    public function index(): void
    {
        $transactions = (new SalesTransaction())->getAllWithDetails();
        $this->view('transactions/index', ['transactions' => $transactions]);
    }

    // GET /transactions/create — form buat transaksi baru
    public function create(): void
    {
        $vehicles  = (new Vehicle())->getAvailable();
        $customers = (new Customer())->all();
        $this->view('transactions/create', [
            'vehicles'  => $vehicles,
            'customers' => $customers,
        ]);
    }

    // POST /transactions — simpan transaksi baru
    public function store(): void
    {
        $customerId  = $this->input('customer_id');
        $vehicleId   = $this->input('vehicle_id');
        $paymentType = (int) $this->input('payment_type');
        $salesUserId = $_SESSION['user_id'] ?? 2;

        try {
            $transaction = new SalesTransaction();
            $transaction->create([
                'transaction_code' => $transaction->generateCode(),
                'customer_id'      => $customerId,
                'vehicle_id'       => $vehicleId,
                'sales_user_id'    => $salesUserId,
                'payment_type'     => $paymentType,
                'status'           => 'process',
            ]);

            (new Vehicle())->setHeld((int) $vehicleId);

            // Routing berdasarkan metode pembayaran (PBI-4.4.4)
            if ($paymentType === 1) {
                $this->redirect('/credit-applications/create?vehicle_id=' . $vehicleId);
            } else {
                $this->redirect('/payments/create?vehicle_id=' . $vehicleId);
            }

        } catch (Exception $e) {
            echo '<pre>ERROR: ' . $e->getMessage() . '</pre>';
        }
    }

    // GET /transactions/:id — detail transaksi
    public function show(int $id): void
    {
        $transaction = (new SalesTransaction())->getAllWithDetails();
        $data = array_filter($transaction, fn($t) => $t['id'] == $id);
        $this->view('transactions/show', ['transaction' => array_values($data)[0] ?? null]);
    }

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
