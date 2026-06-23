<?php
// app/controllers/DebugResetController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/DownPayment.php';
require_once ROOT_PATH . '/app/models/CreditDecision.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class DebugResetController extends Controller
{
    public function process()
    {
        header("Content-Type: application/json; charset=UTF-8");

        $response = [
            "status" => "error",
            "message" => "Terjadi kesalahan sistem saat mereset data.",
            "data" => null
        ];

        try {
            $creditAppModel = new CreditApplication();
            $downPaymentModel = new DownPayment();
            $creditDecisionModel = new CreditDecision();
            $salesTxModel = new SalesTransaction();

            $db = Database::getInstance();
            $db->beginTransaction();

            try {
                // 1. Reset status credit_applications ke 'submitted'
                $creditAppModel->update(1, ['status' => 'submitted']);

                // 2. Cari & Hapus down_payments untuk credit_application_id = 1
                $dp = $downPaymentModel->findByCreditApplicationId(1);
                if ($dp) {
                    $downPaymentModel->delete((int)$dp['id']);
                }

                // 3. Reset status sales_transactions ke 'process'
                $creditApp = $creditAppModel->find(1);
                $tx_id = $creditApp['transaction_id'] ?? 1;
                $salesTxModel->update($tx_id, ['status' => 'process']);

                // 4. Hapus keputusan kredit
                $db->exec("DELETE FROM credit_decisions WHERE credit_application_id = 1");

                $db->commit();

                $response["status"] = "success";
                $response["message"] = "Data uji coba (Kredit ID 1 & Transaksi ID {$tx_id}) berhasil di-reset ke status awal (Pending / Belum Bayar DP).";

            } catch (Exception $txException) {
                $db->rollBack();
                throw $txException;
            }

        } catch (Exception $e) {
            $response["message"] = $e->getMessage();
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
