<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/DeliverySchedule.php';

class DeliveryScheduleController extends Controller
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

    // POST /delivery — simpan jadwal baru
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

    // POST /delivery/:id/confirm — simpan tanda tangan
    public function confirm(string $id): void
    {
        $schedule = $this->deliveryModel->findWithDetail((int) $id);
        if (!$schedule) {
            die("Jadwal tidak ditemukan.");
        }

        $signatureData = $this->input('signature_data');
        $signaturePath = '';

        if ($signatureData) {
            // Ubah base64 menjadi file PNG
            $imageData = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)
            );

            // Nama file unik
            $fileName  = 'signature_' . $id . '_' . time() . '.png';

            // Lokasi folder penyimpanan
            $uploadDir = ROOT_PATH . '/public/uploads/signatures/';

            // Buat folder jika belum ada
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Simpan file PNG ke folder
            file_put_contents($uploadDir . $fileName, $imageData);

            // Simpan PATH/lokasi file ke database
            $signaturePath = '/uploads/signatures/' . $fileName;
        }

        // Update database dengan path tanda tangan
        $this->deliveryModel->confirmDelivery((int) $id, $signaturePath);

        // Ubah status kendaraan jadi sold dan kurangi stok
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
