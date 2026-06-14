<?php

require_once ROOT_PATH . '/app/models/ServiceBookings.php';
require_once ROOT_PATH . '/app/models/ServiceSummary.php';

class BookingController extends Controller {

    private ServiceBooking $bookingModel;
    private ServiceSummary $summaryModel;

    public function __construct() {
        $this->bookingModel = new ServiceBooking();
        $this->summaryModel = new ServiceSummary();
    }

    // ── CUSTOMER ──────────────────────────────────────

    // GET /booking — form booking untuk customer
    public function index(): void {
        $this->view('booking/index');
    }

    // GET /booking/check-slot?date=YYYY-MM-DD
    // Cek ketersediaan slot — diakses customer & SA
    public function checkSlot(): void {
        $date = $_GET['date'] ?? date('Y-m-d');

        if (!$this->isValidDate($date)) {
            $this->jsonResponse([
                'available' => false,
                'message'   => 'Format tanggal tidak valid.'
            ], 400);
            return;
        }

        if ($date < date('Y-m-d')) {
            $this->jsonResponse([
                'available' => false,
                'message'   => 'Tidak bisa booking tanggal yang sudah lewat.'
            ], 400);
            return;
        }

        $remaining = $this->bookingModel->getRemainingSlot($date);

        $this->jsonResponse([
            'date'      => $date,
            'available' => $remaining > 0,
            'remaining' => $remaining,
            'message'   => $remaining > 0
                ? "Slot tersedia: {$remaining} sisa"
                : 'Slot penuh untuk tanggal ini.'
        ]);
    }

    // POST /booking/store — simpan booking baru
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/booking');
            return;
        }

        $customerId  = (int) ($_POST['customer_id']  ?? 0);
        $vehicleId   = (int) ($_POST['vehicle_id']   ?? 0);
        $bookingDate = trim($_POST['booking_date']    ?? '');

        // Validasi input
        if (!$customerId || !$vehicleId || !$bookingDate) {
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

        // Cek slot sebelum insert
        if (!$this->bookingModel->isSlotAvailable($bookingDate)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Slot untuk tanggal ini sudah penuh.'
            ], 409);
            return;
        }

        $saved = $this->bookingModel->storeBooking([
            'customer_id'  => $customerId,
            'vehicle_id'   => $vehicleId,
            'booking_date' => $bookingDate,
        ]);

        if ($saved) {
            // Update rekap harian service_summary
            $this->summaryModel->updateSummary($bookingDate);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Booking berhasil. Status: queued.'
            ], 201);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Booking gagal. Periksa data customer dan kendaraan.'
            ], 500);
        }
    }

    // ── SERVICE ADVISOR ───────────────────────────────

    // GET /booking/queue?date=YYYY-MM-DD — antrean SA
    public function queue(): void {
        $date     = $_GET['date'] ?? date('Y-m-d');
        $bookings = $this->bookingModel->getQueueForSA($date);

        $this->view('booking/queue', [
            'bookings'  => $bookings,
            'date'      => $date,
            'remaining' => $this->bookingModel->getRemainingSlot($date),
        ]);
    }

    // POST /booking/confirm — SA konfirmasi booking
    public function confirm(): void {
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

        $updated = $this->bookingModel->updateStatus($id, 'confirmed');

        if ($updated) {
            $this->summaryModel->updateSummary($booking['booking_date']);
            $this->jsonResponse(['success' => true, 'message' => 'Booking dikonfirmasi.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal konfirmasi.'], 500);
        }
    }

    // POST /booking/reject — SA tolak booking
    public function reject(): void {
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

        if ($updated) {
            $this->summaryModel->updateSummary($booking['booking_date']);
            $this->jsonResponse(['success' => true, 'message' => 'Booking ditolak.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menolak booking.'], 500);
        }
    }

    // ── HELPER ────────────────────────────────────────

    private function isValidDate(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function jsonResponse(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}