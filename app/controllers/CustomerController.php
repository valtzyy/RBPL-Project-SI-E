<?php
require_once ROOT_PATH . '/app/models/Customer.php';

class CustomerController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['Sales']);
    }

    // GET /customers — tampilkan semua customer
    public function index(): void
    {
        $customers = (new Customer())->all();
        $this->view('customers/index', ['customers' => $customers]);
    }

    // GET /customers/create — form buat customer baru
    public function create(): void
    {
        $this->view('customers/create');
    }

    // POST /customers — simpan customer baru
    public function store(): void
    {
        $name  = $this->input('name');
        $phone = $this->input('phone');

        try {
            (new Customer())->create([
                'name'  => $name,
                'phone' => $phone,
            ]);

            $this->redirect('/customers');
        } catch (Exception $e) {
            echo '<pre>ERROR: ' . $e->getMessage() . '</pre>';
        }
    }

    // GET /customers/:id — detail customer (untuk auto-fill)
    public function show(int $id): void
    {
        $customer = (new Customer())->find($id);
        $this->view('customers/show', ['customer' => $customer]);
    }
}