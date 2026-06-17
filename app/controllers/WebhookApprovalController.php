<?php
// app/controllers/WebhookApprovalController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/CreditDecision.php';
require_once ROOT_PATH . '/app/models/DownPayment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class WebhookApprovalController extends Controller
{
    public function process()
    {
        // Set response header to JSON
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

            // Read raw JSON input
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                throw new Exception("Format JSON tidak valid.");
            }

            $id_kredit = isset($dataInput['id_kredit']) ? (int)$dataInput['id_kredit'] : 0;
            $status_approval = isset($dataInput['status_approval']) ? trim($dataInput['status_approval']) : '';
            $catatan = isset($dataInput['catatan']) ? trim($dataInput['catatan']) : '';

            if ($id_kredit <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'id_kredit' wajib diisi dan bernilai positif.");
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

            // Instantiate models
            $creditAppModel = new CreditApplication();
            $creditDecisionModel = new CreditDecision();
            $downPaymentModel = new DownPayment();
            $salesTxModel = new SalesTransaction();

            // 1. Cek apakah pengajuan kredit ada di database
            $creditApp = $creditAppModel->findWithTransactionStatus($id_kredit);
            if (!$creditApp) {
                http_response_code(404);
                throw new Exception("Pengajuan kredit dengan ID tersebut tidak ditemukan.");
            }

            $transaction_id = $creditApp['transaction_id'];
            $current_tx_status = $creditApp['current_tx_status'];

            // Begin database transaction using the models' PDO connection
            $db = Database::getInstance();
            $db->beginTransaction();

            try {
                // 2. Update status pengajuan kredit
                $creditAppModel->update($id_kredit, ['status' => $db_status]);

                // 3. Simpan keputusan kredit di tabel credit_decisions
                $creditDecisionModel->create([
                    'credit_application_id' => $id_kredit,
                    'decision' => $db_status,
                    'notes' => $catatan,
                    'decided_at' => date('Y-m-d H:i:s')
                ]);

                // 4. LOGIKA OTOMATIS GATEWAY KE ANTREAN SERAH TERIMA (PBI-9.6)
                $status_transaksi_baru = null;
                $dp_lunas = false;

                if ($db_status === 'approved') {
                    // Cek pembayaran DP
                    $dpRecord = $downPaymentModel->findByCreditApplicationId($id_kredit);

                    if ($dpRecord && !empty($dpRecord['paid_at'])) {
                        $dp_lunas = true;
                    }

                    if ($dp_lunas) {
                        // Update status transaksi utama menjadi 'lunas'
                        $salesTxModel->update($transaction_id, ['status' => 'lunas']);
                        $status_transaksi_baru = 'lunas';
                    }
                }

                $db->commit();

                $response["status"] = "success";
                $response["message"] = "Webhook approval leasing berhasil diproses.";
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
