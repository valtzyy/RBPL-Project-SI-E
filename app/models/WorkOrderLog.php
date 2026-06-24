<?php
// app/models/WorkOrderLog.php

class WorkOrderLog extends Model
{
    protected string $table = 'work_order_logs';

    /**
     * Mengambil seluruh catatan log berdasarkan ID Work Order
     */
    public function getLogsByWorkOrderId(int $woId): array
    {
        $query = "SELECT * FROM {$this->table} WHERE work_order_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$woId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menambahkan log baru untuk Work Order
     */
    public function createLog(int $woId, string $status, string $notes): bool
    {
        $allowedStatuses = ['started', 'paused', 'checked', 'rework', 'closed'];

        if (!in_array($status, $allowedStatuses)) {
            throw new InvalidArgumentException(
                "Status log tidak valid."
            );
        }

        // cek data
        $stmt = $this->db->prepare("
        SELECT COUNT(*)
        FROM work_order_logs
        WHERE work_order_id = ?
          AND status = ?
    ");

        $stmt->execute([$woId, $status]);

        if ($stmt->fetchColumn() > 0) {
            return false;
        }

        return $this->create([
            'work_order_id' => $woId,
            'status' => $status,
            'notes' => $notes
        ]) > 0;
    }
}
