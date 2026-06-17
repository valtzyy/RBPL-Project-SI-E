<?php
require_once ROOT_PATH . '/app/models/Payment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class FinanceController extends Controller
{
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
        
        // Asumsi $_SESSION['user_id'] adalah ID finance yang sedang login
        // $userId = $_SESSION['user_id'] ?? 1;

        // Ambil payment berdasarkan id (atau ambil dari database langsung jika butuh cross-check)
        // Di sini kita langsung update
        try {
            // Update payment status to verified
            $paymentModel->verifyPayment((int) $id, $userId);

            // Karena kita butuh transaction_id untuk mengupdate sales_transactions,
            // mari ambil payment data dulu (tapi Model yang baru dibuat di Payment.php cuma punya getPaymentsByTransaction/findByTransactionId).
            // Tambahkan findById ke Payment model jika belum ada, atau pakai query manual di sini
            $stmt = Database::getInstance()->prepare("SELECT transaction_id FROM payments WHERE id = ?");
            $stmt->execute([$id]);
            $payment = $stmt->fetch();

            if ($payment) {
                // PBI-5.4: Trigger otomatis ubah status transaksi menjadi lunas
                $transactionModel->updateStatus((int) $payment['transaction_id'], 'lunas');
            }

            // Redirect back atau kembalikan response success
            if (isset($_SERVER['HTTP_REFERER'])) {
                $this->redirect($_SERVER['HTTP_REFERER']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Payment verified successfully.']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
