<?php
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/Transaksi.php';

class TransaksiController extends Controller {
    public function history() {
        // [PBI-7.3] MATEMATIKA PAGINATION (10 BARIS DATA PER HALAMAN)
        $limit = 10; 
        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit; 

        // TANGKAP DATA FILTER URL DARI TIM FRONT END
        $status       = $_GET['status'] ?? null;
        $payment_type = $_GET['payment_type'] ?? null;
        $start_date   = $_GET['start_date'] ?? null;
        $end_date     = $_GET['end_date'] ?? null;

        // EKSEKUSI DATA
        $transaksiModel = new Transaksi();
        $dataRiwayat    = $transaksiModel->getHistoryWithPagination($limit, $offset, $status, $payment_type, $start_date, $end_date);

        // [PBI-7.3] HITUNG TOTAL NAVIGATION PAGES
        $totalPages = ceil($dataRiwayat['total'] / $limit);

        // VIEW
        return $this->view('transaksi/history', [
            'transactions' => $dataRiwayat['data'], 
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'filters'      => ['status' => $status, 'payment_type' => $payment_type, 'start_date' => $start_date, 'end_date' => $end_date]
        ]);
    }
}