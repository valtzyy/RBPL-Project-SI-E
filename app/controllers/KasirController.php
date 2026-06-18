<?php
// app/controllers/KasirController.php
//
// PERBAIKAN PBI-12.4 (fokus saja, bagian dashboard & riwayat tidak diubah):
//   - nota() sebelumnya placeholder, sekarang memanggil NotaServis::allDone()
//   - Ditambahkan method cetakNota() yang sebelumnya tidak ada sama sekali,
//     sehingga app/views/kasir/nota_cetak.php bisa diakses dari browser

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/KasirDashboard.php';
require_once ROOT_PATH . '/app/models/NotaServis.php';

class KasirController extends Controller
{
    /** @var KasirDashboard */
    private $model;

    /** @var NotaServis */
    private $notaModel;

    public function __construct()
    {
        $this->model     = new KasirDashboard();
        $this->notaModel = new NotaServis();
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
     * (Tidak diubah — di luar fokus PBI-12.4, masih placeholder seperti sebelumnya)
     */
    public function riwayat(): void
    {
        $this->view('kasir/riwayat', [
            'title'      => 'Riwayat Servis',
            'activePage' => 'riwayat',
        ]);
    }
}
