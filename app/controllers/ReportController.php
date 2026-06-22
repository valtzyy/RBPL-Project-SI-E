<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/Report.php';
require_once ROOT_PATH . '/app/models/AuditLog.php';
require_once ROOT_PATH . '/core/ReportExporter.php';

class ReportController extends Controller
{
    private Report $reportModel;
    private AuditLog $auditLogModel;
    private ReportExporter $exporter;

    public function __construct()
    {
        $this->reportModel = new Report();
        $this->auditLogModel = new AuditLog();
        $this->exporter = new ReportExporter();
    }

    public function testingPage(): void
    {
        $this->view('reports', [
            'title' => 'Reports',
            'reportTypes' => $this->reportModel->supportedTypes(),
        ]);
    }

    public function auditLogPage(): void
    {
        $filters = [
            'limit' => (int) $this->input('limit', 100),
            'module' => trim((string) $this->input('module', '')),
        ];

        $this->view('audit-log', [
            'title' => 'Audit Log',
            'auditLogs' => $this->auditLogModel->latest($filters),
            'filters' => $filters,
        ]);
    }

    public function report(string $type): void
    {
        $normalizedType = $this->reportModel->normalizeType($type);

        if ($normalizedType === null) {
            $this->json([
                'success' => false,
                'message' => 'Jenis report tidak valid.',
                'data' => [],
            ], 404);
            return;
        }

        $filters = $this->reportFilters();
        $data = $this->reportModel->getReportData($normalizedType, $filters);
        $message = $this->isEmptyReportPayload($data) ? 'DATABASE KOSONG' : 'OK';

        $this->auditLogModel->record([
            'action' => 'VIEW_REPORT',
            'module' => strtoupper($normalizedType) . '_REPORT',
            'description' => 'Mengakses endpoint report ' . $normalizedType,
        ]);

        $this->json([
            'success' => true,
            'message' => $message,
            'report_type' => $normalizedType,
            'filters' => $filters,
            'data' => $data,
        ]);
    }

    public function exportPdf(string $type): void
    {
        $normalizedType = $this->reportModel->normalizeType($type);

        if ($normalizedType === null) {
            $this->json([
                'success' => false,
                'message' => 'Jenis report tidak valid.',
                'data' => [],
            ], 404);
            return;
        }

        $filters = $this->reportFilters();
        $data = $this->reportModel->getReportData($normalizedType, $filters);
        $content = $this->exporter->buildPdf($normalizedType, $data, $filters);

        $this->auditLogModel->record([
            'action' => 'EXPORT_PDF',
            'module' => strtoupper($normalizedType) . '_REPORT',
            'description' => 'Export PDF report ' . $normalizedType,
        ]);

        $this->download(
            $content,
            $normalizedType . '-report-' . date('Ymd-His') . '.pdf',
            'application/pdf'
        );
    }

    public function exportExcel(string $type): void
    {
        $normalizedType = $this->reportModel->normalizeType($type);

        if ($normalizedType === null) {
            $this->json([
                'success' => false,
                'message' => 'Jenis report tidak valid.',
                'data' => [],
            ], 404);
            return;
        }

        $filters = $this->reportFilters();
        $data = $this->reportModel->getReportData($normalizedType, $filters);
        [$content, $filename, $contentType] = $this->exporter->buildSpreadsheet(
            $normalizedType,
            $data,
            $filters
        );

        $this->auditLogModel->record([
            'action' => 'EXPORT_EXCEL',
            'module' => strtoupper($normalizedType) . '_REPORT',
            'description' => 'Export Excel report ' . $normalizedType,
        ]);

        $this->download($content, $filename, $contentType);
    }

    public function auditLogs(): void
    {
        $filters = [
            'limit' => (int) $this->input('limit', 100),
            'module' => trim((string) $this->input('module', '')),
        ];

        $data = $this->auditLogModel->latest($filters);
        $message = $data === [] ? 'DATABASE KOSONG' : 'OK';

        $this->auditLogModel->record([
            'action' => 'VIEW_AUDIT_LOG',
            'module' => 'AUDIT_LOG',
            'description' => 'Mengakses endpoint audit log',
        ]);

        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    private function reportFilters(): array
    {
        return [
            'start_date' => trim((string) $this->input('start_date', '')),
            'end_date' => trim((string) $this->input('end_date', '')),
            'status' => trim((string) $this->input('status', '')),
            'payment_type' => trim((string) $this->input('payment_type', '')),
            'vehicle_type' => trim((string) $this->input('vehicle_type', '')),
        ];
    }

    private function isEmptyReportPayload(array $data): bool
    {
        if ($data === []) {
            return true;
        }

        foreach ($data as $value) {
            if (is_array($value) && $value !== []) {
                return false;
            }
        }

        return true;
    }
}
