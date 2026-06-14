<?php

require_once ROOT_PATH . '/app/services/CloudinaryService.php';
require_once ROOT_PATH . '/app/models/CreditDocument.php';

class CreditController extends Controller
{
    private CloudinaryService $cloudinary;
    private CreditDocument $documentModel;

    public function __construct()
    {
        // Inisialisasi 1x per request — hemat memory vs bikin ulang tiap method
        $this->cloudinary     = new CloudinaryService();
        $this->documentModel  = new CreditDocument();
    }

    public function create()
    {
        // TODO: PBI-8.4 — buat pengajuan baru
    }

    public function uploadDocument()
    {
        // 1. Cek login — $_SESSION['user_id'] di-set oleh tim login (bukan job kita)
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }

        // 2. Ambil input dari POST
        $base64Data    = $_POST['file_base64']   ?? null;
        $fileType      = $_POST['file_type']     ?? null;
        $applicationId = (int) ($_POST['application_id'] ?? 0);

        // 3a. Validasi kelengkapan input
        if (empty($base64Data) || empty($fileType) || $applicationId <= 0) {
            http_response_code(400);
            exit('Data tidak lengkap');
        }

        // 3b. Validasi tipe dokumen (whitelist, defense in depth)
        $allowedType = ['KTP', 'KK', 'SlipGaji'];
        if (!in_array($fileType, $allowedType, true)) {
            http_response_code(400);
            exit('Tipe dokumen tidak valid');
        }

        // 4a. Parse string base64: "data:<mime>;base64,<data>"
        if (!preg_match('/^data:([^;]+);base64,(.+)$/', $base64Data, $m)) {
            http_response_code(400);
            exit('Format file base64 tidak valid');
        }
        $mime = $m[1];
        $data = base64_decode($m[2], true);
        if ($data === false) {
            http_response_code(400);
            exit('Gagal decode base64');
        }

        // 4b. Validasi mime & ukuran
        $allowedMime = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxSize     = 5 * 1024 * 1024; // 5 MB dalam byte
        if (!in_array($mime, $allowedMime, true)) {
            http_response_code(415);
            exit('Tipe file tidak diizinkan');
        }
        if (strlen($data) > $maxSize) {
            http_response_code(413);
            exit('Ukuran file melebihi 5 MB');
        }

        // 5. Simpan binary ke file temporary (Cloudinary butuh path, bukan raw data)
        $tmpPath = tempnam(sys_get_temp_dir(), 'credit_');
        file_put_contents($tmpPath, $data);

        // 6+7. Upload ke Cloudinary & simpan public_id ke tabel credit_documents
        try {
            $publicId = $this->cloudinary->uploadCreditDocument($tmpPath, $fileType);
            $this->documentModel->create([
                'credit_application_id' => $applicationId,
                'file_type'             => $fileType,
                'file_path'             => $publicId,
            ]);
        } catch (Throwable $e) {
            // Gagal? Cleanup temp file dulu, baru kembalikan error
            @unlink($tmpPath);
            http_response_code(500);
            exit('Upload gagal: ' . $e->getMessage());
        }

        // 8. Cleanup temp file (wajib — supaya tidak menumpuk di /tmp)
        @unlink($tmpPath);

        // 9. Redirect ke halaman status pengajuan
        header('Location: /credit/status?app=' . $applicationId);
        exit;
    }

    public function getStatus()
    {
        // TODO: PBI-8.5 — lihat status pengajuan + progress dokumen
    }

    public function createDecision()
    {
        // TODO: PBI-8.6/8.7 — approve/reject pengajuan
    }
}
