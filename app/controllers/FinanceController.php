<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';
require_once ROOT_PATH . '/app/models/Invoice.php';
require_once ROOT_PATH . '/app/models/Payment.php';

class FinanceController extends Controller {
    private SalesTransaction $transactionModel;
    private Invoice $invoiceModel;
    private Payment $paymentModel;

    public function __construct() {
        $this->transactionModel = new SalesTransaction();
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
    }

    /**
     * Display the queue of pending cash transactions
     * PBI-5.1
     */
    public function queue(): void {
        $transactions = $this->transactionModel->getPendingCashTransactions();
        $this->view('finance/queue', [
            'title' => 'Antrean Tagihan Pembayaran Tunai',
            'transactions' => $transactions
        ]);
    }

    /**
     * Show final billing details and payment history for a transaction
     * PBI-5.2
     */
    public function showTransaction(string $id): void {
        $transactionId = (int) $id;
        $transaction = $this->transactionModel->getTransactionDetails($transactionId);

        if (!$transaction) {
            die("Transaksi tidak ditemukan.");
        }

        $invoice = $this->invoiceModel->findByTransactionId($transactionId);
        $payments = $this->paymentModel->getPaymentsByTransaction($transactionId);

        // Sum verified payments
        $totalPaid = $this->paymentModel->getSumVerifiedPayments($transactionId);

        // Fallback to vehicle price if invoice is not found
        $invoiceAmount = $invoice ? (float) $invoice['total_amount'] : (float) $transaction['vehicle_price'];
        $remainingBalance = $invoiceAmount - $totalPaid;
        if ($remainingBalance < 0) {
            $remainingBalance = 0.0;
        }

        $this->view('finance/detail', [
            'title' => 'Rincian Tagihan Final',
            'transaction' => $transaction,
            'invoice' => $invoice,
            'invoiceAmount' => $invoiceAmount,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'remainingBalance' => $remainingBalance
        ]);
    }

    /**
     * Verify a payment and automatically check if the total invoice is paid
     * PBI-5.3 & PBI-5.4
     */
    public function verifyPayment(string $id): void {
        $paymentId = (int) $id;

        // Fetch payment record to find transaction
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM payments WHERE id = ? LIMIT 1");
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch();

        if (!$payment) {
            die("Pembayaran tidak ditemukan.");
        }

        $transactionId = (int) $payment['transaction_id'];

        // Guard: jangan verifikasi ulang jika sudah verified
        if ($payment['status'] === 'verified') {
            $this->redirect("/finance/transactions/{$transactionId}");
            return;
        }

        // Get Finance user ID from session — wajib login
        // MOCK FOR DEMO: If not logged in, mock as user 1
        if (empty($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
        }
        $financeUserId = (int) $_SESSION['user_id'];

        // Update payment status to verified
        $success = $this->paymentModel->verify($paymentId, $financeUserId);

        if ($success) {
            // Recalculate total verified payments
            $totalPaid = $this->paymentModel->getSumVerifiedPayments($transactionId);

            // Fetch invoice amount
            $invoice = $this->invoiceModel->findByTransactionId($transactionId);
            if (!$invoice) {
                $transaction = $this->transactionModel->getTransactionDetails($transactionId);
                $invoiceAmount = $transaction ? (float) $transaction['vehicle_price'] : 0.0;
            } else {
                $invoiceAmount = (float) $invoice['total_amount'];
            }

            // PBI-5.4: If total verified payments matches or exceeds the invoice amount, set transaction to 'lunas'
            if ($totalPaid >= $invoiceAmount) {
                $this->transactionModel->updateStatus($transactionId, 'lunas');
            }
        }

        // Redirect back to transaction details page
        $this->redirect("/finance/transactions/{$transactionId}");
    }

    /**
     * Compile receipt details, render to PDF using Dompdf, and trigger attachment download
     * PBI-5.6 & PBI-5.7
     */
    public function downloadReceipt(string $id): void {
        $paymentId = (int) $id;
        $payment = $this->paymentModel->getVerifiedPaymentDetails($paymentId);

        if (!$payment) {
            die("Kwitansi tidak ditemukan atau pembayaran belum terverifikasi oleh Finance.");
        }

        // Require composer autoloader for Dompdf
        $autoloaderPath = ROOT_PATH . '/vendor/autoload.php';
        if (!file_exists($autoloaderPath)) {
            die("Library PDF belum terinstal. Silakan jalankan 'composer install' di terminal proyek terlebih dahulu.");
        }
        require_once $autoloaderPath;

        // Convert payment amount to Indonesian words (Terbilang)
        $amount = (float) $payment['amount'];
        $terbilangText = $this->terbilang($amount) . " Rupiah";

        // Generate dynamic HTML layout for receipt
        $html = $this->getReceiptHtml($payment, $terbilangText);

        // Dompdf configuration & rendering
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'landscape'); // Standard receipt layout format
        $dompdf->render();

        $filename = "kwitansi_pembayaran_" . $payment['transaction_code'] . "_" . $payment['id'] . ".pdf";

        // Clean buffer to avoid corrupted PDF files
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Store output ONCE to avoid double-render bug
        $pdfOutput = $dompdf->output();

        // Send PDF download headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfOutput));
        echo $pdfOutput;
        exit;
    }

    /**
     * Helper to convert numbers to Indonesian words
     */
    private function terbilang(float $number): string {
        $number = abs($number);
        $words = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp = "";

        if ($number < 12) {
            $temp = " " . $words[$number];
        } else if ($number < 20) {
            $temp = $this->terbilang($number - 10) . " Belas";
        } else if ($number < 100) {
            $temp = $this->terbilang(floor($number / 10)) . " Puluh" . $this->terbilang($number % 10);
        } else if ($number < 200) {
            $temp = " Seratus" . $this->terbilang($number - 100);
        } else if ($number < 1000) {
            $temp = $this->terbilang(floor($number / 100)) . " Ratus" . $this->terbilang($number % 100);
        } else if ($number < 2000) {
            $temp = " Seribu" . $this->terbilang($number - 1000);
        } else if ($number < 1000000) {
            $temp = $this->terbilang(floor($number / 1000)) . " Ribu" . $this->terbilang($number % 1000);
        } else if ($number < 1000000000) {
            $temp = $this->terbilang(floor($number / 1000000)) . " Juta" . $this->terbilang($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = $this->terbilang(floor($number / 1000000000)) . " Miliar" . $this->terbilang(fmod($number, 1000000000));
        }

        return trim($temp);
    }

    /**
     * Premium HTML/CSS digital receipt template
     * PBI-5.5
     */
    private function getReceiptHtml(array $payment, string $terbilang): string {
        $amountFormatted = "Rp " . number_format($payment['amount'], 2, ',', '.');
        $dateFormatted = date('d F Y', strtotime($payment['payment_date']));

        return '
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    color: #333;
                    font-size: 12px;
                    margin: 0;
                    padding: 10px;
                }
                .receipt-container {
                    border: 3px double #2b5c8f;
                    padding: 20px;
                    background-color: #fcfdfe;
                    position: relative;
                }
                .header {
                    border-bottom: 2px solid #2b5c8f;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .company-name {
                    font-size: 18px;
                    font-weight: bold;
                    color: #2b5c8f;
                    text-transform: uppercase;
                }
                .company-address {
                    font-size: 10px;
                    color: #666;
                    margin-top: 3px;
                }
                .receipt-title {
                    position: absolute;
                    top: 25px;
                    right: 30px;
                    font-size: 20px;
                    font-weight: bold;
                    color: #2b5c8f;
                    letter-spacing: 1px;
                }
                .row {
                    margin-bottom: 10px;
                    overflow: hidden;
                }
                .label {
                    width: 140px;
                    float: left;
                    font-weight: bold;
                    color: #555;
                }
                .colon {
                    width: 15px;
                    float: left;
                }
                .value {
                    float: left;
                    width: 450px;
                }
                .value-highlight {
                    font-weight: bold;
                    font-size: 13px;
                }
                .amount-box {
                    background-color: #e6f0fa;
                    border: 1px dashed #2b5c8f;
                    padding: 10px 20px;
                    font-size: 18px;
                    font-weight: bold;
                    color: #2b5c8f;
                    display: inline-block;
                    margin-top: 15px;
                }
                .terbilang-box {
                    font-style: italic;
                    background-color: #f9f9f9;
                    padding: 5px 10px;
                    border-left: 3px solid #2b5c8f;
                    display: inline-block;
                    margin-top: 5px;
                    width: 100%;
                    box-sizing: border-box;
                }
                .footer {
                    margin-top: 30px;
                    text-align: right;
                }
                .signature-container {
                    float: right;
                    width: 200px;
                    text-align: center;
                }
                .signature-space {
                    height: 50px;
                }
                .signature-name {
                    font-weight: bold;
                    text-decoration: underline;
                }
                .signature-title {
                    font-size: 10px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="header">
                    <div class="company-name">PT. RAJAWALI BINTANG PRATAMA LESTARI</div>
                    <div class="company-address">Ruko SI-E No. 42, Jl. Raya Otomotif, Surabaya</div>
                    <div class="receipt-title">KWITANSI RESMI</div>
                </div>
                
                <div class="row">
                    <div class="label">Nomor Kwitansi</div>
                    <div class="colon">:</div>
                    <div class="value"><strong>RECP/' . date('Ymd', strtotime($payment['payment_date'])) . '/' . str_pad($payment['id'], 4, '0', STR_PAD_LEFT) . '</strong></div>
                </div>

                <div class="row">
                    <div class="label">Telah Diterima Dari</div>
                    <div class="colon">:</div>
                    <div class="value value-highlight">' . htmlspecialchars($payment['customer_name']) . ' (' . htmlspecialchars($payment['customer_phone']) . ')</div>
                </div>

                <div class="row">
                    <div class="label">Untuk Pembayaran</div>
                    <div class="colon">:</div>
                    <div class="value">Pembayaran Unit Kendaraan ' . htmlspecialchars($payment['vehicle_brand']) . ' ' . htmlspecialchars($payment['vehicle_type']) . ' (No. Rangka: ' . htmlspecialchars($payment['vehicle_chassis']) . ' / No. Mesin: ' . htmlspecialchars($payment['vehicle_engine']) . ')</div>
                </div>

                <div class="row">
                    <div class="label">Uang Sejumlah</div>
                    <div class="colon">:</div>
                    <div class="value">
                        <div class="terbilang-box"># ' . $terbilang . ' #</div>
                    </div>
                </div>

                <div>
                    <div class="amount-box">' . $amountFormatted . '</div>
                    
                    <div class="signature-container">
                        <div>Surabaya, ' . $dateFormatted . '</div>
                        <div class="signature-title">Penerima (Finance Kasir)</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">' . htmlspecialchars($payment['verifier_name'] ?? 'Staff Finance') . '</div>
                        <div class="signature-title">NIP. FIN-' . str_pad($payment['verified_by'] ?? '0', 4, '0', STR_PAD_LEFT) . '</div>
                    </div>
                </div>
                
                <div style="clear: both;"></div>
            </div>
        </body>
        </html>
        ';
    }
}
