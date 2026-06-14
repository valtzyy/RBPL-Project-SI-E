<?php

class ProcurementController extends Controller
{
    public function index()
    {
        $this->view('procurement/form');
    }

    public function store()
{
    $procurement = new Procurement();

    $procurement->create([
        'request_code' => $_POST['request_code'],
        'requested_by' => 2, // Admin Dealer
        'status' => 'sent'
    ]);

    echo "Permintaan berhasil dikirim";
}
}