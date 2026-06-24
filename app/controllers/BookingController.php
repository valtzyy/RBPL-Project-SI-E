<?php

require_once ROOT_PATH . '/app/models/ServiceBookings.php';
require_once ROOT_PATH . '/app/models/ServiceCustomers.php';
require_once ROOT_PATH . '/app/models/WorkOrders.php';

class BookingController extends Controller
{

    private ServiceBooking  $bookingModel;
    private WorkOrders     $workOrderModel;
    private ServiceCustomer $serviceCustomerModel;

    public function __construct()
    {
        $this->bookingModel         = new ServiceBooking();
        $this->workOrderModel       = new WorkOrders();
        $this->serviceCustomerModel = new ServiceCustomer();
    }

    // GET /booking — form booking untuk Admin
    public function queue(): void
    {
        $db        = Database::getInstance();
        $customers = $db->query(
            "SELECT id, name, phone FROM customers ORDER BY name ASC"
        )->fetchAll();

        $this->view('booking/index', [
            'title'     => 'Booking Servis',
            'customers' => $customers,
        ]);
    }

    // GET /booking/check-slot?date=YYYY-MM-DD
    public function checkSlot(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $pekerja = $this->workOrderModel->countActiveWorkOrders();

        if ($pekerja > 5) {
            $this->jsonResponse([
                'date'      => $date,
                'available' => false,
                'message'   => 'Slot tidak tersedia (Bengkel Penuh)',
                'totalAntrean' => $pekerja,
            ], 400);
        } else {
            $this->jsonResponse([
                'date'      => $date,
                'available' => true,
                'message'   => 'Slot tersedia',
                'totalAntrean' => $pekerja,
            ]);
        }
    }

    // POST /booking/create-customer — daftarkan customer baru dan kembalikan ID-nya
    public function createCustomer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed.'], 405);
            return;
        }

        $name  = trim($_POST['new_customer_name'] ?? '');
        $phone = trim($_POST['new_customer_phone'] ?? '');

        if (!$name || !$phone) {
            $this->jsonResponse(['success' => false, 'message' => 'Nama dan Nomor Telepon wajib diisi.'], 422);
            return;
        }

        $db = Database::getInstance();
        try {
            $stmt = $db->prepare("
                INSERT INTO customers (name, phone)
                VALUES (?, ?)
            ");
            $stmt->execute([$name, $phone]);
            $customerId = (int) $db->lastInsertId();

            $this->jsonResponse([
                'success' => true,
                'customer_id' => $customerId,
                'message' => 'Pelanggan baru berhasil didaftarkan.',
                'redirect' => '/booking'
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Gagal mendaftarkan pelanggan: ' . $e->getMessage(),
                'redirect' => '/booking'
            ], 500);
        }
    }

    // POST /booking/store — simpan booking baru oleh Admin
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/booking');
            return;
        }

        $customerId   = (int)   ($_POST['customer_id']   ?? 0);
        $plateNumber  = trim($_POST['plate_number']   ?? '');
        $vehicleName  = trim($_POST['vehicle_name']   ?? '');
        $bookingDate  = trim($_POST['booking_date']   ?? '');

        // Validasi input
        if (!$customerId || !$plateNumber || !$vehicleName || !$bookingDate) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Semua field wajib diisi.'
            ], 422);
            return;
        }

        if (!$this->isValidDate($bookingDate)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Format tanggal tidak valid.'
            ], 422);
            return;
        }

        // Auto-reject jika slot penuh
        if (!$this->bookingModel->isSlotAvailable($bookingDate)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Slot untuk tanggal ini sudah penuh.'
            ], 409);
            return;
        }

        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            // Cari atau buat service_customer berdasarkan plat nomor
            $serviceCustomer = $this->serviceCustomerModel->findByPlate($plateNumber);

            if ($serviceCustomer) {
                $serviceCustomerId = $serviceCustomer['id'];
                // Jika customer_id berbeda, update agar sesuai dengan customer baru
                if ((int)$serviceCustomer['customer_id'] !== $customerId) {
                    $this->serviceCustomerModel->updateCustomer($serviceCustomerId, $customerId);
                }
            } else {
                $serviceCustomerId = $this->serviceCustomerModel->registerCustomer($customerId, $plateNumber);
            }

            // Simpan booking
            $saved = $this->bookingModel->storeBooking([
                'booking_date'        => $bookingDate,
                'service_customer_id' => $serviceCustomerId,
                'vehicle_name'        => $vehicleName,
            ]);

            if ($saved) {
                $db->commit();
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Booking berhasil. Status: queued.',
                    'redirect' => '/booking'
                ], 201);
            } else {
                $db->rollBack();
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Booking gagal. Silakan coba lagi.',
                    'redirect' => '/booking'
                ], 500);
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }
    }

    // GET /booking/queue?date=YYYY-MM-DD — dashboard SA
    public function index(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $bookings = $this->bookingModel->getQueueForSA($date);

        $pekerja = $this->workOrderModel->countActiveWorkOrders();


        $this->view('booking/queue', [
            'title'        => 'Dashboard Antrean Booking',
            'date'         => $date,
            'bookings'     => $bookings,
            'totalAntrean' => $pekerja
        ]);
    }

    // POST /booking/confirm — SA konfirmasi booking
    public function confirm(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'ID tidak valid.'], 422);
            return;
        }

        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
            return;
        }

        if ($booking['status'] !== 'queued') {
            $this->jsonResponse(['success' => false, 'message' => 'Booking sudah diproses.'], 409);
            return;
        }

        // Cek kapasitas bengkel (max 5 mobil in progress)
        if ($this->workOrderModel->countActiveWorkOrders() > 5) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Bengkel sedang penuh (maksimal 5 mobil dalam pengerjaan). Selesaikan salah satu pekerjaan terlebih dahulu.'
            ], 400);
            return;
        }

        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $this->bookingModel->updateStatus($id, 'confirmed');
            $this->workOrderModel->insert($id);


            // Cek apakah setelah konfirmasi, slot kuota untuk tanggal tersebut penuh
            $bookingDate = $booking['booking_date'];
            $remaining = $this->bookingModel->getRemainingSlot($bookingDate);

            if ($remaining <= 0) {
                // Auto-reject semua booking 'queued' di tanggal yang sama (PBI-10.7)
                $stmt = $db->prepare("
                    UPDATE service_bookings 
                    SET status = 'rejected' 
                    WHERE booking_date = ? AND status = 'queued'
                ");
                $stmt->execute([$bookingDate]);
            }

            $db->commit();
            $this->jsonResponse(['success' => true, 'message' => 'Booking dikonfirmasi.']);
        } catch (Exception $e) {
            $db->rollBack();
            $this->jsonResponse(['success' => false, 'message' => 'Gagal konfirmasi.'], 500);
        }
    }

    // POST /booking/reject — SA tolak booking
    public function reject(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'ID tidak valid.'], 422);
            return;
        }

        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
            return;
        }

        if ($booking['status'] !== 'queued') {
            $this->jsonResponse(['success' => false, 'message' => 'Booking sudah diproses.'], 409);
            return;
        }

        $updated = $this->bookingModel->updateStatus($id, 'rejected');
        $updated
            ? $this->jsonResponse(['success' => true,  'message' => 'Booking ditolak.'])
            : $this->jsonResponse(['success' => false, 'message' => 'Gagal menolak.'], 500);
    }

    // GET /booking/inspect/:id — form sheet observasi oleh SA (PBI-10.5)
    public function inspectForm(string $id): void
    {
        $bookingId = (int) $id;
        $booking = $this->bookingModel->findById($bookingId);

        if (!$booking) {
            http_response_code(404);
            exit('Booking tidak ditemukan.');
        }

        if ($booking['status'] !== 'confirmed') {
            http_response_code(400);
            exit('Booking harus dalam status dikonfirmasi untuk melakukan pemeriksaan awal.');
        }

        // Ambil daftar mekanik (users dengan role mekanik)
        $db = Database::getInstance();
        $mechanics = $db->query("
            SELECT u.id, u.name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE r.name LIKE '%mechanic%' OR r.name LIKE '%mekanik%' OR r.name LIKE '%Mechanic%'
        ")->fetchAll();

        if (empty($mechanics)) {
            $mechanics = $db->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll();
        }

        $this->view('booking/inspect', [
            'title'     => 'Lembar Observasi Awal',
            'booking'   => $booking,
            'mechanics' => $mechanics
        ]);
    }

    // POST /booking/inspect/:id/convert — Simpan observasi & buat Work Order resmi (PBI-10.6)
    public function convertToWorkOrder(string $id): void
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/booking');
            return;
        }

        $bookingId        = (int) $id;
        $assignedMechanic = (int) ($_POST['assigned_mechanic'] ?? 0);
        $notes            = trim($_POST['notes'] ?? '');

        if (!$assignedMechanic || !$notes) {
            $this->jsonResponse(['success' => false, 'message' => 'Mekanik dan Catatan Observasi wajib diisi.'], 422);
            return;
        }

        $booking = $this->bookingModel->findById($bookingId);
        if (!$booking) {
            $this->jsonResponse(['success' => false, 'message' => 'Booking tidak ditemukan.'], 404);
            return;
        }

        if ($booking['status'] !== 'confirmed') {
            $this->jsonResponse(['success' => false, 'message' => 'Booking harus dikonfirmasi terlebih dahulu.'], 400);
            return;
        }

        // Cek limit 5 mobil aktif
        if ($this->workOrderModel->countActiveWorkOrders() > 5) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Bengkel penuh (maksimal 5 mobil dalam pengerjaan). Harap selesaikan pekerjaan lain terlebih dahulu.'
            ], 400);
            return;
        }

        // Hasil observasi langsung menggunakan catatan tambahan
        $description = $notes;

        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            // Cek apakah data work order sudah ada untuk booking ini (biasanya dibuat saat konfirmasi awal)
            $stmt = $db->prepare("SELECT id FROM work_orders WHERE booking_id = ?");
            $stmt->execute([$bookingId]);
            $existingWo = $stmt->fetch();

            if ($existingWo) {
                $workOrderId = (int) $existingWo['id'];
                // Update data work order yang sudah ada
                $stmt = $db->prepare("
                    UPDATE work_orders 
                    SET assigned_mechanic = ?, status = 'in_progress', description = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$assignedMechanic, $description, $workOrderId]);
            } else {
                // Buat baru jika belum ada
                $stmt = $db->prepare("
                    INSERT INTO work_orders (assigned_mechanic, booking_id, status, description)
                    VALUES (?, ?, 'in_progress', ?)
                ");
                $stmt->execute([$assignedMechanic, $bookingId, $description]);
                $workOrderId = (int) $db->lastInsertId();
            }

            // Catat log awal ke work_order_logs (status 'started' sesuai ENUM di database)
            $stmt = $db->prepare("
                INSERT INTO work_order_logs (work_order_id, status, notes)
                VALUES (?, 'started', 'Work Order dibuat otomatis dari hasil observasi SA.')
            ");
            $stmt->execute([$workOrderId]);

            $db->commit();
            $this->jsonResponse([
                'success' => true,
                'message' => 'Work Order berhasil dibuat.',
                'work_order_id' => $workOrderId
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            $this->jsonResponse(['success' => false, 'message' => 'Gagal membuat Work Order: ' . $e->getMessage()], 500);
        }
    }

    // ── HELPER ────────────────────────────────────────

    private function isValidDate(string $date): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function jsonResponse(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
