<?php
require_once __DIR__ . '/../Models/CreditHistoryModel.php';

class CreditHistoryController
{
    private CreditHistoryModel $model;

    public function __construct()
    {
        $this->model = new CreditHistoryModel();
    }

    public function histori_kelengkapan(): void
    {
        $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 1;
        $applicationId = isset($_GET['application_id']) ? (int) $_GET['application_id'] : null;

        $profile = $this->model->getCustomerProfile($customerId);
        $applications = $this->model->getCreditApplicationsByCustomer($customerId);
        $selected = $this->model->findSelectedApplication($applications, $applicationId);
        $stats = $this->model->getApplicationStats($applications);

        require __DIR__ . '/../Views/credit_history/histori_kelengkapan.php';
    }

    public function index(): void
    {
        $this->histori_kelengkapan();
    }
}
