<?php
// app/controllers/KasirController.php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/KasirDashboard.php';

class KasirController extends Controller
{
    private KasirDashboard $model;

    public function __construct()
    {
        $this->model = new KasirDashboard();
    }

    /**
     * GET /kasir/dashboard
     */
    public function dashboard(): void
    {
        $ringkasan = $this->model->getRingkasanHarian();
        $tagihanTerbaru = $this->model->getTagihanTerbaru(5);

        $this->view('kasir/dashboard', [
            'title'           => 'Dashboard Kasir Bengkel',
            'activePage'      => 'dashboard',
            'pendingCount'    => $ringkasan['pending'],
            'ringkasan'       => $ringkasan,
            'tagihanTerbaru'  => $tagihanTerbaru,
        ]);
    }

    /**
     * GET /kasir/nota
     * Placeholder — dikerjakan PBI-12.4
     */
    public function nota(): void
    {
        $this->view('kasir/nota', [
            'title'      => 'Nota Servis',
            'activePage' => 'nota',
        ]);
    }

    /**
     * GET /kasir/riwayat
     * Placeholder — dikerjakan PBI-12.6
     */
    public function riwayat(): void
    {
        $this->view('kasir/riwayat', [
            'title'      => 'Riwayat Servis',
            'activePage' => 'riwayat',
        ]);
    }
}
