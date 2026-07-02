<?php

require_once ROOT_PATH . '/core/Model.php';

class Report extends Model
{
    protected string $table = 'sales_transactions';

    public function supportedTypes(): array
    {
        return [
            'sales' => 'Sales Report',
            'stock' => 'Stock Report',
            'credit' => 'Credit Report',
            'service' => 'Service Report',
            'sparepart' => 'Sparepart Report',
        ];
    }

    public function normalizeType(string $type): ?string
    {
        $type = strtolower(trim($type));
        return array_key_exists($type, $this->supportedTypes()) ? $type : null;
    }

    public function getReportData(string $type, array $filters = []): array
    {
        return match ($type) {
            'sales' => $this->salesReport($filters),
            'stock' => $this->stockReport($filters),
            'credit' => $this->creditReport($filters),
            'service' => $this->serviceReport($filters),
            'sparepart' => $this->sparepartReport($filters),
            default => [],
        };
    }

    private function salesReport(array $filters): array
    {
        $sql = "
            SELECT
                st.id,
                st.transaction_code,
                st.payment_type,
                st.status,
                st.created_at,
                c.name AS customer_name,
                v.brand,
                v.type AS vehicle_type,
                v.color,
                u.name AS sales_name,
                COALESCE(inv.invoice_number, '-') AS invoice_number,
                COALESCE(inv.total_amount, 0) AS total_amount,
                COALESCE(pay.amount, 0) AS paid_amount,
                COALESCE(pay.status, 'pending') AS payment_status
            FROM sales_transactions st
            LEFT JOIN customers c ON c.id = st.customer_id
            LEFT JOIN vehicles v ON v.id = st.vehicle_id
            LEFT JOIN users u ON u.id = st.sales_user_id
            LEFT JOIN invoices inv ON inv.transaction_id = st.id
            LEFT JOIN payments pay ON pay.transaction_id = st.id
            WHERE 1=1
        ";

        [$sql, $params] = $this->applyDateFilter($sql, [], 'st.created_at', $filters);

        if (($filters['status'] ?? '') !== '') {
            $sql .= " AND st.status = ? ";
            $params[] = $filters['status'];
        }

        if (($filters['payment_type'] ?? '') !== '') {
            $sql .= " AND st.payment_type = ? ";
            $params[] = $filters['payment_type'];
        }

        if (($filters['vehicle_type'] ?? '') !== '') {
            $sql .= " AND v.type = ? ";
            $params[] = $filters['vehicle_type'];
        }

        if (($filters['sales'] ?? '') !== '') {
            if (is_numeric($filters['sales'])) {
                $sql .= " AND st.sales_user_id = ? ";
                $params[] = (int) $filters['sales'];
            } else {
                $sql .= " AND u.name LIKE ? ";
                $params[] = '%' . $filters['sales'] . '%';
            }
        }

        $sql .= " ORDER BY st.created_at DESC, st.id DESC ";

        return $this->fetchAll($sql, $params);
    }

    private function stockReport(array $filters): array
    {
        $stockSql = "
            SELECT
                v.type AS vehicle_type,
                v.status AS vehicle_status,
                COUNT(vs.id) AS item_count,
                COALESCE(SUM(vs.quantity), 0) AS total_stock,
                COALESCE(SUM(CASE WHEN vs.quantity <= vs.min_stock THEN 1 ELSE 0 END), 0) AS low_stock_items
            FROM vehicles_stock vs
            LEFT JOIN vehicles v ON v.id = vs.vehicle_id
            WHERE 1=1
        ";

        $stockParams = [];

        if (($filters['vehicle_type'] ?? '') !== '') {
            $stockSql .= " AND v.type = ? ";
            $stockParams[] = $filters['vehicle_type'];
        }

        if (($filters['status'] ?? '') !== '') {
            $stockSql .= " AND v.status = ? ";
            $stockParams[] = $filters['status'];
        }

        $stockSql .= "
            GROUP BY v.type, v.status
            ORDER BY v.type ASC, v.status ASC
        ";

        $sparepartSql = "
            SELECT
                sp.name AS sparepart_name,
                sp.sku,
                sp.stock AS current_stock,
                COALESCE(SUM(su.quantity), 0) AS total_used,
                CASE
                    WHEN (sp.stock + COALESCE(SUM(su.quantity), 0)) = 0 THEN 0
                    ELSE ROUND(
                        (COALESCE(SUM(su.quantity), 0) / (sp.stock + COALESCE(SUM(su.quantity), 0))) * 100,
                        2
                    )
                END AS usage_ratio_percent
            FROM spareparts sp
            LEFT JOIN sparepart_usages su ON su.sparepart_id = sp.id
            GROUP BY sp.id, sp.name, sp.sku, sp.stock
            ORDER BY usage_ratio_percent DESC, sp.name ASC
        ";

        return [
            'stock_recap' => $this->fetchAll($stockSql, $stockParams),
            'sparepart_usage_ratio' => $this->fetchAll($sparepartSql, []),
        ];
    }

    private function creditReport(array $filters): array
    {
        $statusSql = "
            SELECT
                ca.status AS application_status,
                COALESCE(cd.decision, 'pending') AS decision_status,
                COUNT(ca.id) AS total_applications,
                COALESCE(SUM(dp.amount), 0) AS total_down_payment
            FROM credit_applications ca
            LEFT JOIN credit_decisions cd ON cd.credit_application_id = ca.id
            LEFT JOIN down_payments dp ON dp.credit_application_id = ca.id
            WHERE 1=1
        ";

        [$statusSql, $statusParams] = $this->applyDateFilter($statusSql, [], 'ca.created_at', $filters);

        if (($filters['status'] ?? '') !== '') {
            $statusSql .= " AND ca.status = ? ";
            $statusParams[] = $filters['status'];
        }

        $statusSql .= "
            GROUP BY ca.status, COALESCE(cd.decision, 'pending')
            ORDER BY ca.status ASC, decision_status ASC
        ";

        $detailSql = "
            SELECT
                st.transaction_code,
                c.name AS customer_name,
                ca.leasing_name,
                ca.status AS application_status,
                COALESCE(cd.decision, 'pending') AS decision_status,
                COALESCE(dp.amount, 0) AS down_payment_amount,
                ca.created_at
            FROM credit_applications ca
            LEFT JOIN sales_transactions st ON st.id = ca.transaction_id
            LEFT JOIN customers c ON c.id = st.customer_id
            LEFT JOIN credit_decisions cd ON cd.credit_application_id = ca.id
            LEFT JOIN down_payments dp ON dp.credit_application_id = ca.id
            WHERE 1=1
        ";

        [$detailSql, $detailParams] = $this->applyDateFilter($detailSql, [], 'ca.created_at', $filters);

        if (($filters['status'] ?? '') !== '') {
            $detailSql .= " AND ca.status = ? ";
            $detailParams[] = $filters['status'];
        }

        $detailSql .= " ORDER BY ca.created_at DESC, ca.id DESC ";

        return [
            'credit_conversion_status' => $this->fetchAll($statusSql, $statusParams),
            'credit_conversion_list' => $this->fetchAll($detailSql, $detailParams),
        ];
    }

    private function serviceReport(array $filters): array
    {
        $volumeSql = "
            SELECT
                DATE(wo.created_at) AS service_date,
                COUNT(wo.id) AS total_work_orders,
                COALESCE(SUM(CASE WHEN wo.status = 'done' THEN 1 ELSE 0 END), 0) AS done_count,
                COALESCE(SUM(CASE WHEN wo.status = 'ready' THEN 1 ELSE 0 END), 0) AS ready_count,
                COALESCE(SUM(CASE WHEN wo.status = 'in_progress' THEN 1 ELSE 0 END), 0) AS in_progress_count
            FROM work_orders wo
            LEFT JOIN service_bookings sb ON sb.id = wo.booking_id
            WHERE 1=1
        ";

        [$volumeSql, $volumeParams] = $this->applyDateFilter($volumeSql, [], 'wo.created_at', $filters);

        if (($filters['status'] ?? '') !== '') {
            $volumeSql .= " AND wo.status = ? ";
            $volumeParams[] = $filters['status'];
        }

        if (($filters['vehicle_type'] ?? '') !== '') {
            $volumeSql .= " AND EXISTS (
                SELECT 1
                FROM vehicles v2
                WHERE v2.id = sb.vehicle_id AND v2.type = ?
            ) ";
            $volumeParams[] = $filters['vehicle_type'];
        }

        $volumeSql .= "
            GROUP BY DATE(wo.created_at)
            ORDER BY service_date DESC
        ";

        $detailSql = "
            SELECT
                wo.id,
                DATE(wo.created_at) AS service_date,
                wo.status,
                sb.booking_date,
                c.name AS customer_name,
                v.brand,
                v.type AS vehicle_type,
                mech.name AS mechanic_name
            FROM work_orders wo
            LEFT JOIN service_bookings sb ON sb.id = wo.booking_id
            LEFT JOIN customers c ON c.id = sb.customer_id
            LEFT JOIN vehicles v ON v.id = sb.vehicle_id
            LEFT JOIN users mech ON mech.id = wo.assigned_mechanic
            WHERE 1=1
        ";

        [$detailSql, $detailParams] = $this->applyDateFilter($detailSql, [], 'wo.created_at', $filters);

        if (($filters['status'] ?? '') !== '') {
            $detailSql .= " AND wo.status = ? ";
            $detailParams[] = $filters['status'];
        }

        if (($filters['vehicle_type'] ?? '') !== '') {
            $detailSql .= " AND v.type = ? ";
            $detailParams[] = $filters['vehicle_type'];
        }

        $detailSql .= " ORDER BY wo.created_at DESC, wo.id DESC ";

        return [
            'daily_service_volume' => $this->fetchAll($volumeSql, $volumeParams),
            'service_activity_list' => $this->fetchAll($detailSql, $detailParams),
        ];
    }

    private function sparepartReport(array $filters): array
    {
        $sql = "
            SELECT
                sp.id,
                sp.name,
                sp.sku,
                sp.stock,
                sp.min_stock,
                sp.price,
                COALESCE(SUM(su.quantity), 0) AS used_quantity
            FROM spareparts sp
            LEFT JOIN sparepart_usages su ON su.sparepart_id = sp.id
            GROUP BY sp.id, sp.name, sp.sku, sp.stock, sp.min_stock, sp.price
            ORDER BY sp.name ASC, sp.id DESC
        ";

        return $this->fetchAll($sql, []);
    }

    private function applyDateFilter(string $sql, array $params, string $column, array $filters): array
    {
        if (($filters['start_date'] ?? '') !== '') {
            $sql .= " AND DATE({$column}) >= ? ";
            $params[] = $filters['start_date'];
        }

        if (($filters['end_date'] ?? '') !== '') {
            $sql .= " AND DATE({$column}) <= ? ";
            $params[] = $filters['end_date'];
        }

        return [$sql, $params];
    }

    private function fetchAll(string $sql, array $params): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }
}
