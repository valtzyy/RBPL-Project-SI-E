<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/DeliverySchedule.php';
require_once ROOT_PATH . '/app/services/CloudinaryService.php';
require_once ROOT_PATH . '/vendor/autoload.php';

class DeliveryScheduleController extends Controller
{
    private DeliverySchedule $deliveryModel;
    private CloudinaryService $cloudinary;

    public function __construct()
    {
        $this->deliveryModel = new DeliverySchedule();
        $this->cloudinary    = new CloudinaryService();
    }

    public function index(): void
    {
        $schedules = $this->deliveryModel->allWithTransaction();
        $this->view('delivery/index', [
            'title'     => 'Jadwal Serah Terima',
            'schedules' => $schedules,
        ]);
    }

    public function create(): void
    {
        $transactions = $this->deliveryModel->getReadyTransactions();
        $this->view('delivery/create', [
            'title'        => 'Buat Jadwal Serah Terima',
            'transactions' => $transactions,
        ]);
    }

    public function show(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }

        $signatureUrl = '';
        if (!empty($schedule['signature_path'])) {
            $signatureUrl = $this->cloudinary->getPrivateImageUrl($schedule['signature_path']);
        }

        $this->view('delivery/show', [
            'title'        => 'Detail Serah Terima',
            'schedule'     => $schedule,
            'signatureUrl' => $signatureUrl,
        ]);
    }

    public function store(): void
    {
        $transactionId = (int) $this->input('transaction_id');
        $customerId    = (int) $this->input('customer_id');
        $scheduledDate = $this->input('scheduled_date');
        $notes         = $this->input('notes');

        $this->deliveryModel->create([
            'transaction_id' => $transactionId,
            'customer_id'    => $customerId,
            'scheduled_date' => $scheduledDate,
            'notes'          => $notes,
            'status'         => 'scheduled',
        ]);

        $this->redirect('/delivery');
    }

    public function confirm(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }

        $signatureData = $this->input('signature_data');
        $signaturePath = '';

        if ($signatureData) {
            $imageData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)
            );
            $tmpFile = ROOT_PATH . '/public/uploads/signatures/tmp_' . $id . '_' . time() . '.png';

            if (!is_dir(dirname($tmpFile))) {
                mkdir(dirname($tmpFile), 0755, true);
            }

            file_put_contents($tmpFile, $imageData);
            $publicId      = $this->cloudinary->uploadPrivateImage($tmpFile, 'signatures');
            $signaturePath = $publicId;
            unlink($tmpFile);
        }

        $this->deliveryModel->confirmDelivery((int) $id, $signaturePath);

        if (!empty($schedule['vehicle_id'])) {
            $this->deliveryModel->markVehicleSold((int) $schedule['vehicle_id']);
            $this->deliveryModel->reduceVehicleStock((int) $schedule['vehicle_id']);
        }

        $this->redirect('/delivery');
    }

    public function fail(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }

        $this->deliveryModel->markFailed((int) $id);
        $this->redirect('/delivery');
    }

   public function document(string $id): void
{
    $schedule = $this->deliveryModel->findWithDetail((int) $id);
    if (!$schedule) {
        die("Jadwal tidak ditemukan.");
    }

    $signatureUrl = '';
    if (!empty($schedule['signature_path'])) {
        $signatureUrl = $this->cloudinary->getPrivateImageUrl($schedule['signature_path']);
    }

    ob_start();
    require ROOT_PATH . '/app/views/delivery/document.php';
    $html = ob_get_clean();

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'Serah-Terima-' . ($schedule['transaction_code'] ?? $id) . '.pdf';
    $dompdf->stream($filename, ['Attachment' => false]);
    exit;
}
}