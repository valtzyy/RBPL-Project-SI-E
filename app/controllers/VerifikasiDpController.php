<?php
// app/controllers/VerifikasiDpController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/DownPayment.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';

class VerifikasiDpController extends Controller
{
    /**
     * Menampilkan form verifikasi pembayaran Down Payment (DP) kendaraan
     */
    public function showForm()
    {
        $creditAppModel = new CreditApplication();

        // Hanya pengajuan dengan status 'approved' yang tampil di dropdown
        $approvedApps = $creditAppModel->findWithDetailByStatus('approved');

        // Ambil semua staf Finance yang aktif untuk dropdown verifikator
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT u.id, u.name, u.username
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE r.name = 'Finance'
              AND u.status = 'active'
            ORDER BY u.name ASC
        ");
        $stmt->execute();
        $financeUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('credit/verifikasi_dp', [
            'approvedApps'  => $approvedApps,
            'financeUsers'  => $financeUsers,
        ]);
    }

    /**
     * Memproses pencatatan verifikasi pelunasan Down Payment (DP)
     */
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

            // Validasi parameter
            if ($id_kredit <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'id_kredit' wajib diisi.");
            }
            if ($nominal_dibayar <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'nominal_dibayar' harus lebih besar dari 0.");
            }

            // Menentukan user finance pemroses
            $user_finance = null;
            if ($verified_by_input !== null && $verified_by_input > 0) {
                $user_finance = $verified_by_input;
            } elseif (isset($_SESSION['user_id'])) {
                $user_finance = (int)$_SESSION['user_id'];
            }

            // Validasi: verifikator wajib ada
            if ($user_finance === null) {
                http_response_code(400);
                throw new Exception("Verifikator tidak ditemukan. Pastikan Anda sudah login atau sertakan 'verified_by' yang valid.");
            }

            // Inisialisasi model
            $creditAppModel = new CreditApplication();
            $downPaymentModel = new DownPayment();
            $salesTxModel = new SalesTransaction();

            // 1. Validasi keberadaan pengajuan kredit di database
            $creditApp = $creditAppModel->findWithTransactionStatus($id_kredit);
            if (!$creditApp) {
                http_response_code(404);
                throw new Exception("Pengajuan kredit dengan ID tersebut tidak ditemukan.");
            }

            $transaction_id = $creditApp['transaction_id'];
            $status_kredit = $creditApp['status'];
            $current_tx_status = $creditApp['current_tx_status'];

            // 2. Validasi alur: Uang muka (DP) hanya dapat diverifikasi setelah status kredit 'approved' (disetujui) oleh leasing
            if ($status_kredit !== 'approved') {
                http_response_code(400);
                throw new Exception("Uang muka tidak dapat diverifikasi sebelum pengajuan kredit disetujui oleh pihak leasing.");
            }

            // Memulai database transaksi
            $db = Database::getInstance();
            $db->beginTransaction();

            try {
                // Cari apakah record DP untuk pengajuan kredit ini sudah ada
                $dpRecord = $downPaymentModel->findByCreditApplicationId($id_kredit);
                $tanggal_sekarang = date('Y-m-d');

                // Guard: Tolak jika DP sudah pernah diverifikasi (paid_at sudah terisi)
                if ($dpRecord && !empty($dpRecord['paid_at'])) {
                    http_response_code(409);
                    throw new Exception("Down Payment untuk pengajuan kredit ini sudah pernah diverifikasi pada " . $dpRecord['paid_at'] . ". Tidak dapat diproses ulang.");
                }

                if ($dpRecord) {
                    // Update nominal DP (record ada tapi belum pernah dibayar)
                    $downPaymentModel->update((int)$dpRecord['id'], [
                        'amount'      => $nominal_dibayar,
                        'paid_at'     => $tanggal_sekarang,
                        'verified_by' => $user_finance
                    ]);
                } else {
                    // Buat record DP baru
                    $downPaymentModel->create([
                        'credit_application_id' => $id_kredit,
                        'amount'      => $nominal_dibayar,
                        'paid_at'     => $tanggal_sekarang,
                        'verified_by' => $user_finance
                    ]);
                }

                // 3. Logika Alur Sekuensial
                // Transaksi otomatis menjadi 'lunas' setelah DP diverifikasi Finance.
                $salesTxModel->update($transaction_id, ['status' => 'lunas']);

                $db->commit();

                $response["status"] = "success";
                $response["message"] = "Verifikasi pelunasan Down Payment berhasil dicatat.";
                $response["data"] = [
                    "id_kredit"        => $id_kredit,
                    "transaction_id"   => $transaction_id,
                    "nominal_dibayar"  => $nominal_dibayar,
                    "tanggal_lunas"    => $tanggal_sekarang,
                    "verified_by"      => $user_finance,
                    "status_transaksi" => 'lunas'
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
