<?php
require_once ROOT_PATH . '/app/models/Payment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class FinanceController extends Controller
{
    private SalesTransaction $transactionModel;

    /**
     * GET /finance/payments
     * Halaman antrean verifikasi pembayaran (khusus role Finance)
     */
    public function __construct() {
        Auth::requireRole(['Finance']);
        $this->transactionModel = new SalesTransaction();
    }

    public function index(): void
    {
        $transactions = (new SalesTransaction())->getAllWithPaymentDetails();
        $this->view('finance/queue', ['transactions' => $transactions]);
    }

    /**
     * GET /finance/payments/:id
     * Halaman detail pembayaran berdasarkan payment ID
     */
    public function show(string $id): void
    {
        $stmt = Database::getInstance()->prepare("
            SELECT 
                p.id AS payment_id,
                p.transaction_id,
                p.amount AS payment_amount,
                p.payment_date,
                p.status AS payment_status,
                p.verified_by,
                st.transaction_code,
                st.status AS transaction_status,
                st.payment_type,
                COALESCE(c.name, '-') AS customer_name,
                COALESCE(c.phone, '-') AS customer_phone,
                COALESCE(v.brand, '-') AS brand,
                COALESCE(v.type, '-') AS type,
                COALESCE(v.color, '-') AS color,
                COALESCE(v.price, 0) AS price
            FROM payments p
            JOIN sales_transactions st ON p.transaction_id = st.id
            LEFT JOIN buyer_customers bc ON st.customer_id = bc.id
            LEFT JOIN customers c ON bc.customer_id = c.id
            LEFT JOIN vehicles v ON st.vehicle_id = v.id
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $payment = $stmt->fetch();

        $this->view('finance/detail', ['payment' => $payment]);
    }

    /**
     * POST /finance/payments/:id/verify
     * Verifikasi pembayaran dan update status transaksi menjadi lunas
     */
    public function verifyPayment(string $id): void
    {
        // Pastikan request adalah POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $paymentModel = new Payment();
        $transactionModel = new SalesTransaction();
        
        // Ambil user ID dari session (finance yang sedang login)
        $userId = Auth::id() ?? ($_SESSION['user_id'] ?? 3);

        try {
            // Update payment status to verified
            $paymentModel->verifyPayment((int) $id, (int) $userId);

            // Ambil transaction_id dari payment untuk update status transaksi
            $stmt = Database::getInstance()->prepare("SELECT transaction_id FROM payments WHERE id = ?");
            $stmt->execute([$id]);
            $payment = $stmt->fetch();

            if ($payment) {
                // PBI-5.4: Trigger otomatis ubah status transaksi menjadi lunas
                $transactionModel->updateStatus((int) $payment['transaction_id'], 'lunas');
            }

            // Redirect ke halaman antrean finance dengan pesan sukses
            $this->redirect('/finance/payments?success=1');

        } catch (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
