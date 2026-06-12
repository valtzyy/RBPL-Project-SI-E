<?php

class CustomerController extends Controller
{
    // GET /customers — tampilkan semua customer
    public function index(): void
    {
        $customers = (new Customer())->all();
        $this->view('customers/index', ['customers' => $customers]);
    }

    // GET /customers/:id — detail customer (untuk auto-fill)
    public function show(int $id): void
    {
        $customer = (new Customer())->find($id);
        $this->view('customers/show', ['customer' => $customer]);
    }
}