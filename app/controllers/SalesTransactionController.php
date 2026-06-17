<?php
require_once ROOT_PATH . '/app/models/SalesTransaction.php';
require_once ROOT_PATH . '/app/models/Customer.php';
require_once ROOT_PATH . '/app/models/Vehicle.php';
require_once ROOT_PATH . '/app/models/BuyerCustomer.php';

class SalesTransactionController extends Controller
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
        $customerId  = (int) $this->input('customer_id');
        $ktpNumber   = $this->input('ktp_number');
        $address     = $this->input('address');
        $vehicleId   = $this->input('vehicle_id');
        $paymentType = (int) $this->input('payment_type');
        $salesUserId = $_SESSION['user_id'] ?? 2;

        try {
            // Simpan ke tabel buyer_customers
            (new BuyerCustomer())->create([
                'customer_id' => $customerId,
                'ktp_number'  => $ktpNumber,
                'address'     => $address,
            ]);

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

            // Set kendaraan jadi held
            (new Vehicle())->setHeld((int) $vehicleId);

            if ($paymentType === 1) {
                $this->redirect('/credit-applications/create?vehicle_id=' . $vehicleId);
            } else {
                $this->redirect('/payments/create?vehicle_id=' . $vehicleId);
            }

        } catch (Exception $e) {
            echo '<pre>ERROR: ' . $e->getMessage() . '</pre>';
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