<?php

require_once ROOT_PATH . '/core/Model.php';

class VehicleModel extends Model
{
    protected string $table = 'vehicles';

    private array $allowedStatuses = ['available', 'held', 'sold'];

    public function paginate(array $filters, int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $offset = ($page - 1) * $perPage;

        [$whereSql, $params] = $this->buildFilterClause($filters);

        $countStmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM vehicles v
            LEFT JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            {$whereSql}
        ");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT
                v.id,
                v.brand,
                v.type,
                v.color,
                v.chassis_number,
                v.engine_number,
                v.price,
                v.status,
                v.created_at,
                COALESCE(vs.quantity, 0) AS stock_quantity,
                COALESCE(vs.min_stock, 0) AS min_stock
            FROM vehicles v
            LEFT JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            {$whereSql}
            ORDER BY v.created_at DESC, v.id DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function findWithStock(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                v.*,
                COALESCE(vs.quantity, 0) AS stock_quantity,
                COALESCE(vs.min_stock, 0) AS min_stock
            FROM vehicles v
            LEFT JOIN vehicles_stock vs ON vs.vehicle_id = v.id
            WHERE v.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function getFilterOptions(): array
    {
        return [
            'brands' => $this->distinctColumn('brand'),
            'types' => $this->distinctColumn('type'),
            'colors' => $this->distinctColumn('color'),
            'statuses' => $this->allowedStatuses,
        ];
    }

    public function chassisNumberExists(string $chassisNumber, ?int $excludeId = null): bool
    {
        return $this->uniqueValueExists('chassis_number', $chassisNumber, $excludeId);
    }

    public function engineNumberExists(string $engineNumber, ?int $excludeId = null): bool
    {
        return $this->uniqueValueExists('engine_number', $engineNumber, $excludeId);
    }

    public function getAllowedStatuses(): array
    {
        return $this->allowedStatuses;
    }

    private function buildFilterClause(array $filters): array
    {
        $where = [];
        $params = [];

        foreach (['brand', 'type', 'color', 'status'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value === '') {
                continue;
            }

            $where[] = "v.{$field} = ?";
            $params[] = $value;
        }

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $where[] = '(
                v.brand LIKE ?
                OR v.type LIKE ?
                OR v.color LIKE ?
                OR v.chassis_number LIKE ?
                OR v.engine_number LIKE ?
            )';
            $like = '%' . $keyword . '%';
            array_push($params, $like, $like, $like, $like, $like);
        }

        return [$where === [] ? '' : 'WHERE ' . implode(' AND ', $where), $params];
    }

    private function distinctColumn(string $column): array
    {
        $this->validateIdentifier($column);
        $stmt = $this->db->query("
            SELECT DISTINCT {$this->quoteIdentifier($column)} AS value
            FROM vehicles
            WHERE {$this->quoteIdentifier($column)} IS NOT NULL
            ORDER BY {$this->quoteIdentifier($column)} ASC
        ");

        return array_values(array_filter(array_column($stmt->fetchAll(), 'value')));
    }

    private function uniqueValueExists(string $column, string $value, ?int $excludeId): bool
    {
        $this->validateIdentifier($column);
        $sql = "SELECT id FROM vehicles WHERE {$this->quoteIdentifier($column)} = ?";
        $params = [$value];

        if ($excludeId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $excludeId;
        }

        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }
}
