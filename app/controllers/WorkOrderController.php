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
     * Menerima kiriman request POST dari tombol aksi perubahan status utama
     */
    public function updateStatus(): void 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $woId = (int) $this->input('work_order_id');
            $newStatus = $this->input('status');

            if ($woId > 0 && !empty($newStatus)) {
                // =========================================================================
                // [PBI-11.4] & [PBI-11.6] EKSEKUSI API TRIGGER PENGALIHAN STATUS WORK ORDER
                // Memproses aksi tombol dari web untuk merubah status pengerjaan secara real-time
                // =========================================================================
                $success = $this->workOrderModel->updateWorkOrderStatus($woId, $newStatus);
                
                if ($success) {
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
     * Menyimpan log baru ke dalam tabel log
     */
    public function storeLog(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $woId = (int) $this->input('work_order_id');
            $status = $this->input('status');
            $notes = $this->input('notes', '');

            if ($woId > 0 && !empty($status)) {
                // Mulai pencatatan ke log (hanya log status pengerjaan, tidak memodifikasi status utama)
                $successLog = $this->workOrderLogModel->createLog($woId, $status, $notes);

                if ($successLog) {
                    $this->redirect('/mechanic/work-order/log?id=' . $woId . '&status=success');
                    return;
                }
            }

            $this->redirect('/mechanic/work-order/log?id=' . $woId . '&status=failed');
        }
     }

    /**
     * Menampilkan dashboard utama panel Service Advisor
     */
    public function serviceAdvisorIndex(): void
    {
        $orders = $this->workOrderModel->getAllWorkOrders();
        
        $totalHandled = 0;
        $totalCompleted = 0;
        
        foreach ($orders as $order) {
            if ($order['status'] === 'in_progress') {
                $totalHandled++;
            } elseif ($order['status'] === 'done' || $order['status'] === 'ready') {
                $totalCompleted++;
            }
        }
        
        $this->view('bengkel/service_advisor_panel', [
            'orders' => $orders,
            'totalHandled' => $totalHandled,
            'totalCompleted' => $totalCompleted
        ]);
    }

    /**
     * Menampilkan halaman detail work order untuk Service Advisor (Read-only)
     */
    public function serviceAdvisorDetail(string $id): void
    {
        $woId = (int) $id;
        if ($woId <= 0) {
            $this->redirect('/service-advisor/work-orders');
            return;
        }

        $order = $this->workOrderModel->getWorkOrderDetail($woId);
        if (!$order) {
            $this->redirect('/service-advisor/work-orders');
            return;
        }

        $logs = $this->workOrderLogModel->getLogsByWorkOrderId($woId);

        $this->view('bengkel/service_advisor_detail', [
            'order' => $order,
            'logs'  => $logs
        ]);
    }
}