<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/DeliverySchedule.php';

class DeliveryController extends Controller
{
    private DeliverySchedule $deliveryModel;

    public function __construct()
    {
        $this->deliveryModel = new DeliverySchedule();
    }

    // GET /delivery
    public function index(): void
    {
        $schedules = $this->deliveryModel->allWithTransaction();
        $this->view('delivery/index', [
            'title'     => 'Jadwal Serah Terima',
            'schedules' => $schedules,
        ]);
    }

    // GET /delivery/create
    public function create(): void
    {
        $transactions = $this->deliveryModel->getReadyTransactions();
        $this->view('delivery/create', [
            'title'        => 'Buat Jadwal Serah Terima',
            'transactions' => $transactions,
        ]);
    }

    // GET /delivery/:id
    public function show(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }
        $this->view('delivery/show', [
            'title'    => 'Detail Serah Terima',
            'schedule' => $schedule,
        ]);
    }

    // POST /delivery
    public function store(): void
    {
        $transactionId = (int) $this->input('transaction_id');
        $scheduledDate = $this->input('scheduled_date');
        $customerName  = $this->input('customer_name');
        $notes         = $this->input('notes');

        $this->deliveryModel->create([
            'transaction_id' => $transactionId,
            'scheduled_date' => $scheduledDate,
            'customer_name'  => $customerName,
            'notes'          => $notes,
            'status'         => 'scheduled',
        ]);

        $this->redirect('/delivery');
    }

    // POST /delivery/:id/confirm
    public function confirm(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }

        $signatureData   = $this->input('signature_data');
        $signaturePath   = '';
        $signatureBase64 = '';

        if ($signatureData) {
            // Simpan base64 ke database
            $signatureBase64 = $signatureData;

            // Simpan juga sebagai file backup
            $imageData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)
            );
            $fileName  = 'signature_' . $id . '_' . time() . '.png';
            $uploadDir = ROOT_PATH . '/public/uploads/signatures/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            file_put_contents($uploadDir . $fileName, $imageData);
            $signaturePath = '/uploads/signatures/' . $fileName;
        }

        $this->deliveryModel->confirmDelivery((int) $id, $signaturePath, $signatureBase64);

        if (!empty($schedule['vehicle_id'])) {
            $this->deliveryModel->markVehicleSold((int) $schedule['vehicle_id']);
            $this->deliveryModel->reduceVehicleStock((int) $schedule['vehicle_id']);
        }

        $this->redirect('/delivery');
    }

    // GET /delivery/:id/document
    public function document(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }
        $this->view('delivery/document', [
            'title'    => 'Dokumen Serah Terima',
            'schedule' => $schedule,
        ]);
    }
}
