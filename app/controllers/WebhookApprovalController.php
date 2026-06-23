<?php
// app/controllers/WebhookApprovalController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/CreditDecision.php';
require_once ROOT_PATH . '/app/models/DownPayment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class WebhookApprovalController extends Controller
{
    /**
     * Menampilkan form persetujuan kelayakan kredit dari leasing luar
     */
    public function showForm()
    {
        $creditAppModel = new CreditApplication();
        // Hanya pengajuan dengan status 'submitted' yang tampil di dropdown
        $submittedApps = $creditAppModel->findWithDetailByStatus('submitted');
        $this->view('credit/form_approval', ['submittedApps' => $submittedApps]);
    }

    /**
     * Memproses data keputusan kelayakan kredit (Webhook / Form Submit)
     */
    public function process()
    {
        // Set response header ke JSON
        header("Content-Type: application/json; charset=UTF-8");

        $response = [
            "status" => "error",
            "message" => "Terjadi kesalahan sistem.",
            "data" => null
        ];

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                throw new Exception("Metode HTTP tidak didukung. Harap gunakan POST.");
            }

            // Membaca input (JSON raw atau data POST tradisional)
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true);

            if ($dataInput !== null) {
                $id_kredit = isset($dataInput['id_kredit']) ? (int)$dataInput['id_kredit'] : 0;
                $status_approval = isset($dataInput['status_approval']) ? trim($dataInput['status_approval']) : '';
                $catatan = isset($dataInput['catatan']) ? trim($dataInput['catatan']) : '';
            } else {
                $id_kredit = isset($_POST['id_kredit']) ? (int)$_POST['id_kredit'] : 0;
                $status_approval = isset($_POST['status_approval']) ? trim($_POST['status_approval']) : '';
                $catatan = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
            }

            if ($id_kredit <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'id_kredit' wajib diisi.");
            }
            if (empty($status_approval)) {
                http_response_code(400);
                throw new Exception("Parameter 'status_approval' wajib diisi.");
            }

            $validStatuses = ['disetujui', 'ditolak'];
            if (!in_array($status_approval, $validStatuses)) {
                http_response_code(400);
                throw new Exception("Parameter 'status_approval' tidak valid. Gunakan 'disetujui' atau 'ditolak'.");
            }

            $db_status = ($status_approval === 'disetujui') ? 'approved' : 'rejected';

            // Inisialisasi model
            $creditAppModel = new CreditApplication();
            $creditDecisionModel = new CreditDecision();
            $downPaymentModel = new DownPayment();
            $salesTxModel = new SalesTransaction();

            // 1. Validasi keberadaan pengajuan kredit di database
            $creditApp = $creditAppModel->findWithTransactionStatus($id_kredit);
            if (!$creditApp) {
                http_response_code(404);
                throw new Exception("Pengajuan kredit dengan ID tersebut tidak ditemukan.");
            }

            $transaction_id = $creditApp['transaction_id'];
            $current_tx_status = $creditApp['current_tx_status'];

            // Memulai database transaksi
            $db = Database::getInstance();
            $db->beginTransaction();

            try {
                // 2. Update status pengajuan kredit ke approved / rejected
                $creditAppModel->update($id_kredit, ['status' => $db_status]);

                // 3. Simpan riwayat keputusan kredit
                $creditDecisionModel->create([
                    'credit_application_id' => $id_kredit,
                    'decision' => $db_status,
                    'notes' => $catatan,
                    'decided_at' => date('Y-m-d H:i:s')
                ]);

                // 4. Logika Alur Sekuensial
                // Status transaksi utama belum berubah menjadi 'lunas' pada tahap ini karena DP belum diverifikasi oleh Finance.
                $status_transaksi_baru = null;

                $dpRecord = $downPaymentModel->findByCreditApplicationId($id_kredit);
                $dp_lunas = ($dpRecord && !empty($dpRecord['paid_at'])) ? true : false;

                $db->commit();

                $response["status"] = "success";
                $response["message"] = "Persetujuan kelayakan kredit berhasil dicatat.";
                $response["data"] = [
                    "id_kredit" => $id_kredit,
                    "transaction_id" => $transaction_id,
                    "status_approval" => $db_status,
                    "dp_lunas" => $dp_lunas,
                    "status_transaksi" => $status_transaksi_baru ?? $current_tx_status
                ];

            } catch (Exception $txException) {
                $db->rollBack();
                throw $txException;
            }

        } catch (Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
