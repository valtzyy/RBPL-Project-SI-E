<?php
require_once ROOT_PATH . '/app/models/SalesTransaction.php';
require_once ROOT_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;

class AdminTransactionController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['Admin']);
    }

    public function index(): void
    {
        $transactions = (new SalesTransaction())->getAllWithPaymentDetails();
        $this->view('admin/transactions/index', ['transactions' => $transactions]);
    }

    public function show(string $id): void
    {
        $transactions = (new SalesTransaction())->getAllWithPaymentDetails();
        $data = array_filter($transactions, fn($t) => $t['id'] == $id);
        $transaction = array_values($data)[0] ?? null;

        if (!$transaction) {
            die("Transaksi tidak ditemukan.");
        }

        $this->view('admin/transactions/show', ['transaction' => $transaction]);
    }

    public function downloadReceipt(string $id): void
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $transactions = (new SalesTransaction())->getAllWithPaymentDetails();
        $data = array_filter($transactions, fn($t) => $t['id'] == $id);
        $transaction = array_values($data)[0] ?? null;

        if (!$transaction) {
            die("Transaksi tidak ditemukan.");
        }

        // Render HTML for PDF
        ob_start();
        extract(['transaction' => $transaction]);
        require ROOT_PATH . '/app/views/admin/transactions/receipt.php';
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = 'Kwitansi_' . $transaction['transaction_code'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
