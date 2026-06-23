<?php

require_once ROOT_PATH . '/core/Controller.php';

require_once ROOT_PATH . '/app/models/DownPaymentModel.php';

class DownPaymentController extends Controller
{
    private DownPaymentModel $model;

    public function __construct()
    {
        $this->model = new DownPaymentModel();
    }

    public function index()
    {
        $applications =
            $this->model
            ->getCreditApplications();

        $histories =
            $this->model
            ->getCustomerHistory();

        $flash = $_SESSION['flash'] ?? null;

        unset($_SESSION['flash']);

        $this->view(

            'down_payment/form',

            [

                'title'

                => 'Down Payment',

                'applications'

                => $applications,

                'histories'

                => $histories,

                'flash'

                => $flash

            ]

        );
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            $this->redirect('/down-payment');

        }

        $creditApplicationId = (int) $this->input(
            'credit_application_id'
        );

        if (!$this->isApprovedApplication($creditApplicationId)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'DP hanya bisa dicatat untuk pengajuan kredit yang sudah approved.'
            ];

            $this->redirect('/down-payment');
        }

        $this->model->save([

            'credit_application_id'

            => $creditApplicationId,

            'amount'

            => $this->input(
                'amount'
            ),

            'paid_at'

            => $this->input(
                'paid_at'
            )

        ]);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Pembayaran DP berhasil dicatat.'
        ];

        $this->redirect('/down-payment');
    }

    public function uploadContract()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/down-payment');
        }

        $creditApplicationId = (int) $this->input(
            'credit_application_id'
        );

        if (!$this->isApprovedApplication($creditApplicationId)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Kontrak hanya bisa disimpan untuk pengajuan kredit yang sudah approved.'
            ];

            $this->redirect('/down-payment');
        }

        $file = $_FILES['contract_file'] ?? null;

        if (
            !$file ||
            ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        ) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'File kontrak belum dipilih atau gagal diupload.'
            ];

            $this->redirect('/down-payment');
        }

        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Format kontrak harus PDF, JPG, JPEG, atau PNG.'
            ];

            $this->redirect('/down-payment');
        }

        $uploadDir = ROOT_PATH . '/public/uploads/contracts';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = sprintf(
            'contract_%d_%s.%s',
            $creditApplicationId,
            bin2hex(random_bytes(8)),
            $extension
        );

        $targetPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'File kontrak gagal disimpan.'
            ];

            $this->redirect('/down-payment');
        }

        $this->model->saveContract(
            $creditApplicationId,
            '/uploads/contracts/' . $fileName
        );

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Kontrak kredit digital berhasil disimpan.'
        ];

        $this->redirect('/down-payment');
    }

    private function isApprovedApplication(int $creditApplicationId): bool
    {
        if ($creditApplicationId <= 0) {
            return false;
        }

        $application = $this->model->getApplicationStatus(
            $creditApplicationId
        );

        return $application &&
            ($application['decision'] ?? null) === 'approved';
    }
}
