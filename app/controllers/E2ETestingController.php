<?php

require_once ROOT_PATH . '/app/models/E2ETestingModel.php';

class E2ETestingController
{
    private E2ETestingModel $model;

    public function __construct()
    {
        $this->model = new E2ETestingModel();
    }

    public function index(): void
    {
        $scenario = $this->model->getCreditPurchaseScenario();
        $sessionStatuses = $_SESSION['e2e_credit_purchase_statuses'] ?? [];
        $steps = $this->model->mergeStatuses($scenario['steps'], $sessionStatuses);
        $summary = $this->model->calculateSummary($steps);

        require ROOT_PATH . '/app/views/e2e_testing/credit_purchase.php';
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/e2e-testing');
        }

        $stepId = $_POST['step_id'] ?? '';
        $status = $_POST['status'] ?? '';
        $allowedStatuses = ['pending', 'passed', 'failed', 'blocked'];

        if ($this->model->isValidStep($stepId) && in_array($status, $allowedStatuses, true)) {
            $_SESSION['e2e_credit_purchase_statuses'][$stepId] = $status;
            $_SESSION['e2e_credit_purchase_flash'] = 'Status langkah testing berhasil diperbarui.';
        }

        $this->redirect('/e2e-testing');
    }

    public function reset(): void
    {
        unset($_SESSION['e2e_credit_purchase_statuses']);
        $_SESSION['e2e_credit_purchase_flash'] = 'Checklist testing dikembalikan ke status awal.';

        $this->redirect('/e2e-testing');
    }

    private function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}
