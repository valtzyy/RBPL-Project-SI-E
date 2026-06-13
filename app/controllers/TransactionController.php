<?php

class TransactionController extends Controller
{
    // GET /transactions — tampilkan semua transaksi
    public function index(): void
    {
        $transactions = (new SalesTransaction())->getAllWithDetails();
        $this->view('transactions/index', ['transactions' => $transactions]);
    }

    // GET /transactions/create — form buat transaksi baru
    public function create(): void
    {
        $vehicles  = (new Vehicle())->getAvailable();
        $customers = (new Customer())->all();
        $this->view('transactions/create', [
            'vehicles'  => $vehicles,
            'customers' => $customers,
        ]);
    }

    // POST /transactions — simpan transaksi baru
    public function store(): void
    {
        $customerId  = $this->input('customer_id');
        $vehicleId   = $this->input('vehicle_id');
        $paymentType = (int) $this->input('payment_type');
        $salesUserId = $_SESSION['user_id'] ?? 1; // sementara default 1

        // Simpan transaksi
        $transaction = new SalesTransaction();
        $transaction->create([
            'transaction_code' => $transaction->generateCode(),
            'customer_id'      => $customerId,
            'vehicle_id'       => $vehicleId,
            'sales_user_id'    => $salesUserId,
            'payment_type'     => $paymentType,
            'status'           => 'process',
        ]);

        // Set kendaraan jadi 'held'
        (new Vehicle())->setHeld((int) $vehicleId);

        // Routing berdasarkan metode pembayaran (PBI-4.4.4)
        if ($paymentType === 1) {
            $this->redirect('/credit-applications/create?vehicle_id=' . $vehicleId);
        } else {
            $this->redirect('/payments/create?vehicle_id=' . $vehicleId);
        }
    }

    // GET /transactions/:id — detail transaksi
    public function show(int $id): void
    {
        $transaction = (new SalesTransaction())->getAllWithDetails();
        $data = array_filter($transaction, fn($t) => $t['id'] == $id);
        $this->view('transactions/show', ['transaction' => array_values($data)[0] ?? null]);
    }
}