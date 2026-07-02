<?php
// app/controllers/UploadDokumenController.php

require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/CreditDocument.php';

class UploadDokumenController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['Sales']);
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

            $id_kredit = isset($_POST['id_kredit']) ? (int)$_POST['id_kredit'] : 0;
            $file_type = isset($_POST['file_type']) ? trim($_POST['file_type']) : 'SlipGaji';

            if ($id_kredit <= 0) {
                http_response_code(400);
                throw new Exception("Parameter 'id_kredit' wajib diisi.");
            }

            $allowedFileTypes = ['KTP', 'KK', 'SlipGaji'];
            if (!in_array($file_type, $allowedFileTypes)) {
                http_response_code(400);
                throw new Exception("Parameter 'file_type' tidak valid. Gunakan: KTP, KK, atau SlipGaji.");
            }

            // Inisialisasi model database
            $creditAppModel = new CreditApplication();
            $creditDocModel = new CreditDocument();

            // 1. Cek apakah pengajuan kredit tersedia
            $creditApp = $creditAppModel->find($id_kredit);
            if (!$creditApp) {
                http_response_code(404);
                throw new Exception("Pengajuan kredit dengan ID tersebut tidak ditemukan.");
            }

            // 2. Cek unggahan file
            if (!isset($_FILES['file_kontrak']) || $_FILES['file_kontrak']['error'] === UPLOAD_ERR_NO_FILE) {
                http_response_code(400);
                throw new Exception("File dokumen ('file_kontrak') wajib diunggah.");
            }

            $file = $_FILES['file_kontrak'];

            // Cek error bawaan PHP upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                switch ($file['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception("Ukuran file melebihi batas maksimal konfigurasi PHP.");
                    case UPLOAD_ERR_PARTIAL:
                        throw new Exception("File hanya terunggah sebagian. Silakan coba lagi.");
                    default:
                        throw new Exception("Gagal mengunggah file ke server temp. Error code: " . $file['error']);
                }
            }

            // 3. Validasi Ukuran File (Maksimal 2MB = 2.097.152 bytes)
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                http_response_code(400);
                throw new Exception("Ukuran file terlalu besar. Batas maksimal adalah 2MB.");
            }

            // 4. Validasi Ekstensi File
            $filename = $file['name'];
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($extension, $allowedExtensions)) {
                http_response_code(400);
                throw new Exception("Ekstensi file tidak valid. Hanya diperbolehkan: .pdf, .jpg, .jpeg, atau .png.");
            }

            // 5. Validasi MIME Type secara riil
            $tmpPath = $file['tmp_name'];
            $mimeType = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
            } elseif (function_exists('mime_content_type')) {
                $mimeType = mime_content_type($tmpPath);
            } else {
                $mimeType = $file['type'] ?? '';
            }

            $allowedMimeTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png'
            ];

            if (!in_array($mimeType, $allowedMimeTypes)) {
                http_response_code(400);
                throw new Exception("Konten file (MIME type) tidak aman atau tidak sesuai.");
            }

            // 6. Tentukan folder penyimpanan berkas
            $uploadDir = ROOT_PATH . '/storage/uploads/contracts/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    throw new Exception("Gagal membuat direktori penyimpanan berkas.");
                }
            }

            // 7. Rename file secara unik
            $newFilename = time() . '_' . uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;

            // Path relatif untuk disimpan di database
            $dbPath = 'storage/uploads/contracts/' . $newFilename;

            // Pindahkan file dari temp directory ke folder upload
            if (!move_uploaded_file($tmpPath, $targetPath)) {
                throw new Exception("Gagal memindahkan file ke direktori tujuan.");
            }

            // 8. Simpan info file ke database menggunakan Model
            try {
                $creditDocModel->create([
                    'credit_application_id' => $id_kredit,
                    'file_type' => $file_type,
                    'file_path' => $dbPath
                ]);
            } catch (Exception $dbEx) {
                // Hapus file fisik jika simpan database gagal
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                throw $dbEx;
            }

            $response["status"] = "success";
            $response["message"] = "Dokumen (" . $file_type . ") berhasil diunggah dan disimpan.";
            $response["data"] = [
                "id_kredit" => $id_kredit,
                "file_name" => $newFilename,
                "file_path" => $dbPath,
                "file_type" => $file_type,
                "file_size_bytes" => $file['size']
            ];

        } catch (Exception $e) {
            $response["status"] = "error";
            $response["message"] = $e->getMessage();
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
