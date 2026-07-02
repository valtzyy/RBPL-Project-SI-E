<?php
// app/controllers/KasirController.php
//
// PERBAIKAN PBI-12.4 (sebelumnya):
//   - nota() disambungkan ke NotaServis::allDone()
//   - cetakNota() ditambahkan (sebelumnya tidak ada)
//
// PERBAIKAN PBI-12.6 (fokus saat ini):
//   - riwayat() sebelumnya placeholder, sekarang menerima query string ?q=
//     dan memanggil RiwayatServis::cariRiwayat()
//   - Ditambahkan method historicalLogs() yang sebelumnya tidak ada sama sekali,
//     dipanggil oleh modal "Lihat Log" di halaman riwayat.php (endpoint JSON)

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/KasirDashboard.php';
require_once ROOT_PATH . '/app/models/NotaServis.php';
require_once ROOT_PATH . '/app/models/RiwayatServis.php';

class KasirController extends Controller
{
    /** @var KasirDashboard */
    private $model;

    /** @var NotaServis */
    private $notaModel;

    /** @var RiwayatServis */
    private $riwayatModel;

    public function __construct()
    {
        Auth::requireRole(['Finance']);
        $this->model        = new KasirDashboard();
        $this->notaModel    = new NotaServis();
        $this->riwayatModel = new RiwayatServis();
    }

    /**
     * GET /kasir/dashboard
     * (Tidak diubah — di luar fokus PBI-12.4)
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
     * PBI-12.4 — Daftar nota servis yang siap dicetak (WO berstatus done).
     *
     * Sebelumnya method ini placeholder kosong, tidak memanggil model apapun.
     * Sekarang disambungkan ke NotaServis::allDone().
     */
    public function nota(): void
    {
        $notaList = $this->notaModel->allDone();

        $this->view('kasir/nota', [
            'title'        => 'Nota Servis',
            'activePage'   => 'nota',
            'pendingCount' => $this->model->getRingkasanHarian()['pending'],
            'notaList'     => $notaList,
        ]);
    }

    /**
     * GET /kasir/nota/cetak/:id
     * PBI-12.4 — Halaman cetak nota, terbuka di tab baru, siap Ctrl+P / Save as PDF.
     *
     * Method ini SEBELUMNYA TIDAK ADA SAMA SEKALI.
     * Tanpa method ini, file app/views/kasir/nota_cetak.php tidak bisa
     * diakses dari URL manapun walau filenya sudah ada di disk.
     */
    public function cetakNota(string $id): void
    {
        $nota = $this->notaModel->getDataNota((int) $id);

        if (!$nota) {
            http_response_code(404);
            echo '<p style="font-family:sans-serif;padding:40px;">
                    Nota tidak ditemukan, atau work order belum berstatus selesai (done).
                  </p>';
            return;
        }

        $this->view('kasir/nota_cetak', [
            'nota' => $nota,
        ]);
    }

    /**
     * GET /kasir/riwayat
     * GET /kasir/riwayat?q=keyword
     * PBI-12.6 — Halaman pencarian riwayat servis by chassis/mesin/nama pelanggan.
     *
     * Sebelumnya method ini placeholder kosong, tidak memanggil model apapun
     * dan tidak membaca query string ?q=. Sekarang disambungkan ke
     * RiwayatServis::cariRiwayat().
     */
    public function riwayat(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $hasil   = $keyword !== '' ? $this->riwayatModel->cariRiwayat($keyword) : [];

        $this->view('kasir/riwayat', [
            'title'        => 'Riwayat Servis',
            'activePage'   => 'riwayat',
            'pendingCount' => $this->model->getRingkasanHarian()['pending'],
            'keyword'      => $keyword,
            'hasil'        => $hasil,
        ]);
    }

    /**
     * GET /kasir/riwayat/logs/:id
     * PBI-12.7 — Endpoint JSON historical logs, dipanggil fetch() dari
     * modal "Lihat Log" di kasir/riwayat.php.
     *
     * Method ini SEBELUMNYA TIDAK ADA SAMA SEKALI.
     * Tanpa method dan route ini, tombol "Lihat Log" di halaman riwayat
     * akan selalu gagal walau view dan model sudah lengkap.
     */
    public function historicalLogs(string $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $data = $this->riwayatModel->getHistoricalLogs((int) $id);

        if (!$data) {
            http_response_code(404);
            echo json_encode(['error' => 'Work order tidak ditemukan.']);
            return;
        }

        echo json_encode($data);
    }
}
