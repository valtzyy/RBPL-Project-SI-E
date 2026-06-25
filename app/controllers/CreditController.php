<?php

require_once ROOT_PATH . '/app/services/CloudinaryService.php';
require_once ROOT_PATH . '/app/services/LeasingService.php';
require_once ROOT_PATH . '/app/models/CreditDocument.php';
require_once ROOT_PATH . '/app/models/CreditApplication.php';
require_once ROOT_PATH . '/app/models/CreditDecision.php';
require_once ROOT_PATH . '/app/models/SalesTransaction.php';
require_once ROOT_PATH . '/app/models/PaymentType.php';

class CreditController extends Controller
{
    // Service untuk upload file (PBI-8.3) & simulasi leasing (PBI-8.4)
    private CloudinaryService $cloudinary;
    private LeasingService $leasing;

    // Model untuk akses DB tiap tabel terkait
    private CreditDocument $documentModel;
    private CreditApplication $applicationModel;
    private CreditDecision $decisionModel;
    private SalesTransaction $transactionModel;
    private PaymentType $paymentTypeModel;

    public function __construct()
    {
        // Inisialisasi 1x per request — hemat memory vs bikin ulang tiap method
        $this->cloudinary       = new CloudinaryService();
        $this->leasing          = new LeasingService();
        $this->documentModel    = new CreditDocument();
        $this->applicationModel = new CreditApplication();
        $this->decisionModel    = new CreditDecision();
        $this->transactionModel = new SalesTransaction();
        $this->paymentTypeModel = new PaymentType();
    }

    public function create()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
            header('Content-Type: application/json'); 
            http_response_code(401); 
            echo json_encode(['status'=>'error','message'=>'Login dulu']); 
            exit;
        }*/

        // 2. Ambil input
        $transactionId = (int) ($_POST['transaction_id'] ?? 0);
        $leasingName   = trim($_POST['leasing_name'] ?? '');

        // 3. Validasi kelengkapan input
        if ($transactionId <= 0 || $leasingName === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            http_response_code(400); echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
            exit;
        }

        // 4. Validasi transaction: ada, payment_type=kredit, status=process
        $transaction = $this->transactionModel->findWithPaymentType($transactionId);
        if (!$transaction) {
            http_response_code(404);
            header('Content-Type: application/json'); http_response_code(404);
            echo json_encode(['status'=>'error','message'=>'Transaksi tidak ditemukan']);
            exit;
        }
        if (strtolower($transaction['payment_name']) !== 'kredit') {
            http_response_code(400);
            header('Content-Type: application/json'); http_response_code(400);
            echo json_encode(['status'=>'error','message'=>'Transaksi bukan tipe kredit']);
            exit;
        }
        if ($transaction['status'] !== 'process') {
            http_response_code(400);
            header('Content-Type: application/json'); http_response_code(400);
            echo json_encode(['status'=>'error','message'=>'Transaksi tidak dalam status process']);
            exit;
        }

        // 5. Cek duplikat pengajuan
        $existing = $this->applicationModel->findByTransactionId($transactionId);
        if ($existing) {
            http_response_code(400);
            header('Content-Type: application/json'); http_response_code(400);
            echo json_encode(['status'=>'error','message'=>'Pengajuan kredit untuk transaksi ini sudah ada']);
            exit;
        }

        // 6. Insert credit_application
        $applicationId = $this->applicationModel->create([
            'transaction_id' => $transactionId,
            'leasing_name'   => $leasingName,
        ]);

        // 7. Simulasi kirim ke leasing → dapat ref
        $leasingRef = $this->leasing->simulateSend($applicationId, $leasingName);

        // 8. Response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status'         => 'ok',
            'application_id' => $applicationId,
            'leasing_ref'    => $leasingRef,
        ]);
        exit;
    }

    public function cancel()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil input
        $applicationId = (int) ($_POST['application_id'] ?? 0);

        // 3. Validasi kelengkapan
        if ($applicationId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            http_response_code(400); echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
            exit;
        }

        // 4. Validasi: application ada & status = 'rejected'
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }
        if ($application['status'] !== 'rejected') {
            http_response_code(400);
            exit('Hanya pengajuan rejected yang bisa dibatalkan');
        }

        // 5. Update sales_transactions.status = 'cancel'
        $this->transactionModel->update($application['transaction_id'], [
            'status' => 'cancel',
        ]);

        // 6. Response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status'         => 'ok',
            'action'         => 'cancel',
            'transaction_id' => $application['transaction_id'],
        ]);
        exit;
    }

    public function switchToCash()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil input
        $applicationId = (int) ($_POST['application_id'] ?? 0);

        // 3. Validasi kelengkapan
        if ($applicationId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            http_response_code(400); echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
            exit;
        }

        // 4. Validasi: application ada & status = 'rejected'
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }
        if ($application['status'] !== 'rejected') {
            http_response_code(400);
            exit('Hanya pengajuan rejected yang bisa dialihkan ke tunai');
        }

        // 5. Cari payment_type 'tunai' secara dinamis
        $tunai = $this->paymentTypeModel->findByName('tunai');
        if (!$tunai) {
            http_response_code(500);
            exit('Payment type tunai tidak ditemukan di database');
        }

        // 6. Update sales_transactions.payment_type = tunai.id
        $this->transactionModel->update($application['transaction_id'], [
            'payment_type' => $tunai['id'],
        ]);

        // 7. Response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status'           => 'ok',
            'action'           => 'switch_cash',
            'transaction_id'   => $application['transaction_id'],
            'new_payment_type' => 'tunai',
        ]);
        exit;
    }

    public function reapply()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil input
        $applicationId  = (int) ($_POST['application_id'] ?? 0);
        $newLeasingName = trim($_POST['leasing_name'] ?? '');

        // 3. Validasi kelengkapan
        if ($applicationId <= 0 || $newLeasingName === '') {
            http_response_code(400);
            header('Content-Type: application/json');
            http_response_code(400); echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
            exit;
        }

        // 4. Validasi: application lama ada & status = 'rejected'
        $oldApplication = $this->applicationModel->find($applicationId);
        if (!$oldApplication) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }
        if ($oldApplication['status'] !== 'rejected') {
            http_response_code(400);
            exit('Hanya pengajuan rejected yang bisa di-reapply');
        }

        // 5. Buat application baru (start fresh, tidak copy dokumen)
        $newApplicationId = $this->applicationModel->create([
            'transaction_id' => $oldApplication['transaction_id'],
            'leasing_name'   => $newLeasingName,
        ]);

        // 6. Simulasi kirim ke leasing (sama alur seperti create)
        $leasingRef = $this->leasing->simulateSend($newApplicationId, $newLeasingName);

        // 7. Response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status'         => 'ok',
            'action'         => 'reapply',
            'application_id' => $newApplicationId,
            'leasing_ref'    => $leasingRef,
            'message'        => 'Pengajuan baru dibuat, silakan upload 3 dokumen',
        ]);
        exit;
    }

    public function status()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil param: ?app=ID untuk detail, tanpa param untuk list
        $applicationId = (int) ($_GET['app'] ?? 0);

        header('Content-Type: application/json');

        // 3a. Mode DETAIL: ?app=ID
        if ($applicationId > 0) {
            $application = $this->applicationModel->find($applicationId);
            if (!$application) {
                http_response_code(404);
                exit('Pengajuan tidak ditemukan');
            }

            $documents = $this->documentModel->findByApplication($applicationId);
            $decision  = $this->decisionModel->findByApplication($applicationId);

            echo json_encode([
                'status'      => 'ok',
                'type'        => 'detail',
                'application' => $application,
                'documents'   => $documents,
                'decision'    => $decision ?: null,
            ]);
            exit;
        }

        // 3b. Mode LIST: tanpa param
        $applications = $this->applicationModel->findAllWithDocCount();
        echo json_encode([
            'status'       => 'ok',
            'type'         => 'list',
            'applications' => $applications,
        ]);
        exit;
    }

    public function decision()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil input
        $applicationId = (int) ($_POST['application_id'] ?? 0);
        $decision      = $_POST['decision'] ?? '';
        $notes         = trim($_POST['notes'] ?? '');

        // 3. Validasi kelengkapan & nilai decision
        if ($applicationId <= 0 || !in_array($decision, ['approved', 'rejected'], true)) {
            http_response_code(400);
            exit('Data tidak lengkap atau nilai decision tidak valid');
        }

        // 4. Validasi application: ada & status = 'submitted'
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }
        if ($application['status'] !== 'submitted') {
            http_response_code(400);
            exit('Hanya pengajuan submitted yang bisa di-decision');
        }

        // 5. Business rule: harus ada 3 dokumen (KTP, KK, SlipGaji)
        $documents = $this->documentModel->findByApplication($applicationId);
        if (count($documents) < 3) {
            http_response_code(400);
            exit('Pengajuan belum memiliki 3 dokumen lengkap');
        }

        // 6. Insert ke credit_decisions
        $this->decisionModel->create([
            'credit_application_id' => $applicationId,
            'decision'              => $decision,
            'notes'                 => $notes,
        ]);

        // 7. Update status credit_applications → approved/rejected
        $this->applicationModel->update($applicationId, [
            'status' => $decision,
        ]);

        // 8. Response JSON
        header('Content-Type: application/json');
        echo json_encode([
            'status'         => 'ok',
            'action'         => 'decision',
            'application_id' => $applicationId,
            'new_status'     => $decision,
        ]);
        exit;
    }

    public function uploadDocument()
    {
        // 1. Cek login — $_SESSION['user_id'] di-set oleh tim login (bukan job kita)
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil input dari POST
        $base64Data    = $_POST['file_base64']   ?? null;
        $fileType      = $_POST['file_type']     ?? null;
        $applicationId = (int) ($_POST['application_id'] ?? 0);

        // 3a. Validasi kelengkapan input
        if (empty($base64Data) || empty($fileType) || $applicationId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            http_response_code(400); echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
            exit;
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
            $publicId = $this->cloudinary->uploadPrivateImage($tmpPath, $fileType);
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

    public function uploadForm()
    {
        // 1. Cek login
        // if (!isset($_SESSION['user_id'])) {
        //     http_response_code(401);
        //     exit('Login dulu');
        // }

        // 2. Ambil application_id dari query string
        $applicationId = (int) ($_GET['app'] ?? 0);
        if ($applicationId <= 0) {
            http_response_code(400);
            exit('Application ID tidak valid');
        }

        // 3. Ambil data pengajuan
        $application = $this->applicationModel->find($applicationId);
        if (!$application) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }

        // 4. Ambil data customer & vehicle dari tabel terkait
        $customerName = '';
        $vehiclename = '';
        $transaction = $this->transactionModel->findWithPaymentType($application['transaction_id']);
        if ($transaction) {
            $db = \Database::getInstance();

            $stmt = $db->prepare('
                SELECT c.name 
                FROM customers c 
                JOIN buyer_customers bc ON bc.customer_id = c.id 
                WHERE bc.id = ?
            ');
            $stmt->execute([$transaction['customer_id']]);
            $customerName = $stmt->fetchColumn() ?: '';

            $stmt = $db->prepare("SELECT type FROM vehicles WHERE id = ?");
            $stmt->execute([$transaction['vehicle_id']]);
            $vehiclename = $stmt->fetchColumn();
        }

        // 5. Render view dengan data
        $this->view('credit/upload-document', [
            'applicationId'  => $application['id'],
            'applicationNo'  => 'CRD-' . $application['id'],
            'customerName' => $customerName,
            'vehicle'  => $vehiclename,
            'leasing' => $application['leasing_name'] ?? '',
        ]);
    }

        public function createForm()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit('Login dulu');
        }*/

        // 2. Ambil transaksi yang eligible: payment_type=kredit, status=process
        $db = \Database::getInstance();
        $stmt = $db->prepare("
            SELECT st.id, st.transaction_code, c.name AS customer_name,
                   v.brand, v.type
            FROM sales_transactions st
            JOIN payment_types pt    ON pt.id = st.payment_type
            JOIN buyer_customers bc   ON bc.id = st.customer_id
            JOIN customers c          ON c.id  = bc.customer_id
            JOIN vehicles v           ON v.id  = st.vehicle_id
            WHERE pt.name = 'kredit' AND st.status = 'process'
            AND NOT EXISTS (
                  SELECT 1 FROM credit_applications ca 
                  WHERE ca.transaction_id = st.id
            )
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        $transactions = $stmt->fetchAll();

        // 3. Render view
        $this->view('credit/create', [
            'transactions' => $transactions,
        ]);
    }

    public function uploadSearch()
    {
        // 1. Login check
        /*if (!isset($_SESSION['user_id'])) {
            http_response_code(401);s
            exit('Login dulu');
        }*/

        // 2. Ambil transaksi yang eligible: payment_type=kredit, status=process
        $keyword = trim($_GET['q'] ?? '');

        $applications = $this->applicationModel->findForUploadSearch($keyword);

        $this->view('credit/upload-search', [
            'keyword' => $keyword,
            'applications' => $applications
        ]);
    }

    public function statusView()
    {
        $applicationId = (int) ($_GET['app'] ?? 0);

        if ($applicationId <= 0) {
            http_response_code(400);
            exit('Application ID tidak valid');
        }

        $application = $this->applicationModel->find($applicationId);

        if (!$application) {
            http_response_code(404);
            exit('Pengajuan tidak ditemukan');
        }

        $documents = $this->documentModel
            ->findByApplication($applicationId);

        $decision = $this->decisionModel
            ->findByApplication($applicationId);

        $this->view('credit/status', [
            'application' => $application,
            'documents'   => $documents,
            'decision'    => $decision
        ]);
    }

    public function tracking()
    {
        $keyword = trim($_GET['q'] ?? '');

        $applications =
            $this->applicationModel
                ->findForTracking($keyword);

        $this->view('credit/tracking', [
            'keyword'     => $keyword,
            'applications'=> $applications
        ]);
    }
}
