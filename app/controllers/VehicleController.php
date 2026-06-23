<?php
require_once ROOT_PATH . '/app/models/Vehicle.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/services/VehicleInventoryService.php';

class VehicleController extends Controller
{
    // GET /vehicles/available — daftar kendaraan tersedia
    public function available(): void
    {
        $vehicles = (new Vehicle())->getAvailable();
        $this->view('vehicles/available', ['vehicles' => $vehicles]);
    }



    private VehicleInventoryService $inventoryService;

    public function __construct()
    {
        $this->inventoryService = new VehicleInventoryService();
    }

    public function index(): void
    {
        $filters = $this->getFilters();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $inventory = $this->inventoryService->list($filters, $page, 10);
        $this->view('inventory_index', [
            'title' => 'Inventaris Kendaraan',
            'inventory' => $inventory,
            'filters' => $filters,
            'options' => $this->inventoryService->getFilterOptions(),
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null,
        ]);

        unset($_SESSION['success'], $_SESSION['error']);
    }

    public function create(): void
    {
        $this->view('inventory_form', [
            'title' => 'Tambah Kendaraan',
            'mode' => 'create',
            'action' => '/inventory/store',
            'vehicle' => $this->emptyVehicle(),
            'statuses' => $this->inventoryService->getAllowedStatuses(),
            'error' => $_SESSION['error'] ?? null,
        ]);

        unset($_SESSION['error']);
    }

    public function store(): void
    {
        try {
            $id = $this->inventoryService->create($this->requestData());

            if ($this->wantsJson()) {
                $this->respondJson(['message' => 'Kendaraan berhasil dibuat.', 'id' => $id], 201);
                return;
            }

            $_SESSION['success'] = 'Kendaraan berhasil dibuat.';
            $this->redirect('/inventory');
        } catch (Throwable $e) {
            $this->handleError($e, '/inventory/create');
        }
    }

    public function edit(string $id): void
    {
        try {
            $vehicle = $this->inventoryService->find((int) $id);
            $this->view('inventory_form', [
                'title' => 'Edit Kendaraan',
                'mode' => 'edit',
                'action' => '/inventory/update/' . (int) $id,
                'vehicle' => $vehicle,
                'statuses' => $this->inventoryService->getAllowedStatuses(),
                'error' => $_SESSION['error'] ?? null,
            ]);

            unset($_SESSION['error']);
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/inventory');
        }
    }

    public function update(string $id): void
    {
        try {
            $this->inventoryService->update((int) $id, $this->requestData());

            if ($this->wantsJson()) {
                $this->respondJson(['message' => 'Kendaraan berhasil diperbarui.']);
                return;
            }

            $_SESSION['success'] = 'Kendaraan berhasil diperbarui.';
            $this->redirect('/inventory');
        } catch (Throwable $e) {
            $this->handleError($e, '/inventory/edit/' . (int) $id);
        }
    }

    public function delete(string $id): void
    {
        try {
            $this->inventoryService->delete((int) $id);

            if ($this->wantsJson()) {
                $this->respondJson(['message' => 'Kendaraan berhasil dihapus.']);
                return;
            }

            $_SESSION['success'] = 'Kendaraan berhasil dihapus.';
        } catch (Throwable $e) {
            if ($this->wantsJson()) {
                $this->respondJson(['message' => $e->getMessage()], 422);
                return;
            }

            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('/inventory');
    }

    public function apiIndex(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, (int) ($_GET['per_page'] ?? 10));
        $this->respondJson($this->inventoryService->list($this->getFilters(), $page, $perPage));
    }

    public function apiShow(string $id): void
    {
        try {
            $this->respondJson($this->inventoryService->find((int) $id));
        } catch (Throwable $e) {
            $this->respondJson(['message' => $e->getMessage()], 404);
        }
    }

    public function apiStore(): void
    {
        $this->store();
    }

    public function apiUpdate(string $id): void
    {
        $this->update($id);
    }

    public function apiDelete(string $id): void
    {
        $this->delete($id);
    }

    private function getFilters(): array
    {
        return [
            'brand' => trim((string) ($_GET['brand'] ?? '')),
            'type' => trim((string) ($_GET['type'] ?? '')),
            'color' => trim((string) ($_GET['color'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
        ];
    }

    private function handleError(Throwable $e, string $fallbackUrl): void
    {
        if ($this->wantsJson()) {
            $this->respondJson(['message' => $e->getMessage()], 422);
            return;
        }

        $_SESSION['error'] = $e->getMessage();
        $this->redirect($fallbackUrl);
    }

    private function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains($accept, 'application/json')
            || str_contains($contentType, 'application/json')
            || str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    }

    private function requestData(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            if ($raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return $_POST;
    }

    private function respondJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
    }

    private function emptyVehicle(): array
    {
        return [
            'brand' => '',
            'type' => '',
            'color' => '',
            'chassis_number' => '',
            'engine_number' => '',
            'price' => '',
            'status' => 'available',
            'stock_quantity' => 0,
            'min_stock' => 0,
        ];
    }
}
