<?php

require_once ROOT_PATH . '/core/Model.php';

class SparepartModel extends Model {
    protected string $table = 'spareparts';

    public function requestParts(int $sparepart_id, int $work_order_id, int $quantity): array {
        try {
            $this->db->beginTransaction();

            // 1. Cek ketersediaan stok
            $stmt = $this->db->prepare("SELECT stock FROM {$this->table} WHERE id = ? FOR UPDATE");
            $stmt->execute([$sparepart_id]);
            $sparepart = $stmt->fetch();

            if (!$sparepart) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Sparepart tidak ditemukan.'];
            }

            if ($sparepart['stock'] < $quantity) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Stok tidak mencukupi.'];
            }

            // 2. Kurangi nilai di kolom stock
            $stmtUpdate = $this->db->prepare("UPDATE {$this->table} SET stock = stock - ? WHERE id = ?");
            $stmtUpdate->execute([$quantity, $sparepart_id]);

            // 3. Catat riwayat pemakaian ke tabel sparepart_usages
            $stmtInsert = $this->db->prepare("INSERT INTO sparepart_usages (work_order_id, sparepart_id, quantity) VALUES (?, ?, ?)");
            $stmtInsert->execute([$work_order_id, $sparepart_id, $quantity]);

            $this->db->commit();

            return ['success' => true, 'message' => 'Request parts berhasil diproses.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function searchParts(string $query): array {
        $stmt = $this->db->prepare("SELECT id, sku, name, stock, price FROM {$this->table} WHERE name LIKE ? OR sku LIKE ? LIMIT 10");
        $stmt->execute(["%{$query}%", "%{$query}%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
