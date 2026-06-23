<?php
// app/controllers/VerifikasiDpController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/DownPayment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class VerifikasiDpController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['Finance']);
    }

    public function process()
    {
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

            // Parse inputs (JSON or traditional POST)
            $rawInput = file_get_contents('php://input');
            $dataInput = json_decode($rawInput, true);

            if ($dataInput !== null) {
                $id_kredit = isset($dataInput['id_kredit']) ? (int)$dataInput['id_kredit'] : 0;
                $nominal_dibayar = isset($dataInput['nominal_dibayar']) ? (float)$dataInput['nominal_dibayar'] : 0.0;
                $verified_by_input = isset($dataInput['verified_by']) ? (int)$dataInput['verified_by'] : null;
            } else {
                $id_kredit = isset($_POST['id_kredit']) ? (int)$_POST['id_kredit'] : 0;
                $nominal_dibayar = isset($_POST['nominal_dibayar']) ? (float)$_POST['nominal_dibayar'] : 0.0;
                $verified_by_input = isset($_POST['verified_by']) ? (int)$_POST['verified_by'] : null;
            }

            // Parameter Validation
            if ($id_kredit <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'id_kredit' wajib diisi.");
            }
            if ($nominal_dibayar <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'nominal_dibayar' harus lebih besar dari 0.");
            }

            // Determine verifying user
            $user_finance = null;
            if ($verified_by_input !== null && $verified_by_input > 0) {
                $user_finance = $verified_by_input;
            } elseif (isset($_SESSION['user_id'])) {
                $user_finance = (int)$_SESSION['user_id'];
            }

            // Instantiate models
            $creditAppModel = new CreditApplication();
            $downPaymentModel = new DownPayment();
            $salesTxModel = new SalesTransaction();

            // 1. Cek apakah pengajuan kredit ada di database
            $creditApp = $creditAppModel->findWithTransactionStatus($id_kredit);
            if (!$creditApp) {
                http_response_code(404);
                throw new Exception("Pengajuan kredit dengan ID tersebut tidak ditemukan.");
            }

            $transaction_id = $creditApp['transaction_id'];
            $status_kredit = $creditApp['status'];
            $current_tx_status = $creditApp['current_tx_status'];

            // Begin database transaction using the models' PDO connection
            $db = Database::getInstance();
            $db->beginTransaction();

            try {
                // Check if DP record already exists
                $dpRecord = $downPaymentModel->findByCreditApplicationId($id_kredit);
                $tanggal_sekarang = date('Y-m-d');

                if ($dpRecord) {
                    // Update existing DP
                    $downPaymentModel->update((int)$dpRecord['id'], [
                        'amount' => $nominal_dibayar,
                        'paid_at' => $tanggal_sekarang,
                        'verified_by' => $user_finance
                    ]);
                } else {
                    // Insert new DP
                    $downPaymentModel->create([
                        'credit_application_id' => $id_kredit,
                        'amount' => $nominal_dibayar,
                        'paid_at' => $tanggal_sekarang,
                        'verified_by' => $user_finance
                    ]);
                }

                // 3. LOGIKA OTOMATIS GATEWAY KE ANTREAN SERAH TERIMA (PBI-9.6)
                $status_transaksi_baru = null;
                $kredit_disetujui = ($status_kredit === 'approved');

                if ($kredit_disetujui) {
                    // Kredit disetujui + DP lunas (just paid) = lunas
                    $salesTxModel->update($transaction_id, ['status' => 'lunas']);
                    $status_transaksi_baru = 'lunas';
                }

                $db->commit();

                $response["status"] = "success";
                $response["message"] = "Verifikasi pelunasan Down Payment berhasil dicatat.";
                $response["data"] = [
                    "id_kredit" => $id_kredit,
                    "transaction_id" => $transaction_id,
                    "nominal_dibayar" => $nominal_dibayar,
                    "tanggal_lunas" => $tanggal_sekarang,
                    "verified_by" => $user_finance,
                    "kredit_disetujui" => $kredit_disetujui,
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
