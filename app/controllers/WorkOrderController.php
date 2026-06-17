<?php
// app/controllers/WorkOrderController.php

require_once ROOT_PATH . '/app/models/WorkOrder.php';
require_once ROOT_PATH . '/app/models/WorkOrderLog.php';

class WorkOrderController extends Controller 
{
    private WorkOrder $workOrderModel;
    private WorkOrderLog $workOrderLogModel;

    public function __construct() 
    {
        $this->workOrderModel = new WorkOrder();
        $this->workOrderLogModel = new WorkOrderLog();
    }

    /**
     * Menampilkan dashboard utama panel teknisi
     */
    public function index() 
    {
        // Mendapatkan ID mekanik dari session jika sistem login sudah terintegrasi, 
        // fallback ke ID 5 untuk keperluan testing mandiri / sinkronisasi awal.
        $mechanicId = (int) ($_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? $_SESSION['id'] ?? 5); 
        
        $orders = $this->workOrderModel->getByMechanic($mechanicId);
        $this->view('bengkel/mechanic_panel', ['orders' => $orders]);
    }

    /**
     * Menerima kiriman request POST dari tombol aksi perubahan status
     */
    public function updateStatus(): void 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $woId = (int) $this->input('work_order_id');
            $newStatus = $this->input('status');

            if ($woId > 0 && !empty($newStatus)) {
                $mappedStatus = $this->mapLogStatusToWorkOrderStatus($newStatus);
                // =========================================================================
                // [PBI-11.4] & [PBI-11.6] EKSEKUSI API TRIGGER PENGALIHAN STATUS WORK ORDER
                // Memproses aksi tombol dari web untuk merubah status pengerjaan secara real-time
                // =========================================================================
                $success = $this->workOrderModel->updateWorkOrderStatus($woId, $mappedStatus);
                
                if ($success) {
                    // Masukkan ke log juga agar history tercatat otomatis saat status diubah langsung dari panel
                    $this->workOrderLogModel->createLog($woId, $newStatus, 'Perubahan status langsung dari panel kerja.');
                    $this->redirect('/mechanic/panel');
                    return;
                }
            }
            
            // Jika gagal, kembalikan ke halaman utama dengan membawa informasi gagal tanpa merusak halaman
            $this->redirect('/mechanic/panel?status=failed');
        }
    }

    /**
     * Halaman form untuk menambahkan work order log beserta history log nya
     */
    public function addLogForm(): void
    {
        $woId = (int) $this->input('id');
        if ($woId <= 0) {
            $this->redirect('/mechanic/panel');
            return;
        }

        $order = $this->workOrderModel->getWorkOrderDetail($woId);
        if (!$order) {
            $this->redirect('/mechanic/panel');
            return;
        }

        $logs = $this->workOrderLogModel->getLogsByWorkOrderId($woId);

        $this->view('bengkel/add_log', [
            'order' => $order,
            'logs'  => $logs
        ]);
    }

    /**
     * Menyimpan log baru ke dalam tabel log dan memperbarui status work order utama
     */
    public function storeLog(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $woId = (int) $this->input('work_order_id');
            $status = $this->input('status');
            $notes = $this->input('notes', '');

            if ($woId > 0 && !empty($status)) {
                $mappedStatus = $this->mapLogStatusToWorkOrderStatus($status);
                // Mulai pencatatan ke log
                $successLog = $this->workOrderLogModel->createLog($woId, $status, $notes);
                // Update status di work order utama
                $successWO = $this->workOrderModel->updateWorkOrderStatus($woId, $mappedStatus);

                if ($successLog && $successWO) {
                    $this->redirect('/mechanic/work-order/log?id=' . $woId . '&status=success');
                    return;
                }
            }

            $this->redirect('/mechanic/work-order/log?id=' . $woId . '&status=failed');
        }
    }

    /**
     * Memetakan status log ke status kolom enum tabel work_orders
     */
    private function mapLogStatusToWorkOrderStatus(string $logStatus): string
    {
        switch ($logStatus) {
            case 'started':
            case 'paused':
            case 'rework':
                return 'in_progress';
            case 'checked':
                return 'ready';
            case 'closed':
                return 'done';
            default:
                return 'in_progress';
        }
    }
}