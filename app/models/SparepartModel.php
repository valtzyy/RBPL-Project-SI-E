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

    public function getInvoiceDraft(int $work_order_id): array {
        // Ambil data work order
        $stmtWo = $this->db->prepare("SELECT * FROM work_orders WHERE id = ?");
        $stmtWo->execute([$work_order_id]);
        $workOrder = $stmtWo->fetch(PDO::FETCH_ASSOC);

        if (!$workOrder) {
            return ['success' => false, 'message' => 'Work order tidak ditemukan.'];
        }

        // Ambil item sparepart yang digunakan
        $stmtParts = $this->db->prepare("
            SELECT su.id as usage_id, su.quantity, s.id as sparepart_id, s.name, s.sku, s.price, 
                   (su.quantity * s.price) as subtotal
            FROM sparepart_usages su
            JOIN spareparts s ON su.sparepart_id = s.id
            WHERE su.work_order_id = ?
        ");
        $stmtParts->execute([$work_order_id]);
        $parts = $stmtParts->fetchAll(PDO::FETCH_ASSOC);

        $totalPartsAmount = 0;
        foreach ($parts as $part) {
            $totalPartsAmount += $part['subtotal'];
        }

        // Contoh tarif jasa dasar (Flat rate)
        $serviceFee = 100000.00; 
        $totalAmount = $serviceFee + $totalPartsAmount;

        return [
            'success' => true,
            'work_order' => $workOrder,
            'service_fee' => $serviceFee,
            'spareparts' => $parts,
            'total_spareparts_amount' => $totalPartsAmount,
            'total_amount' => $totalAmount
        ];
    }

     public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM spareparts ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [PBI-14.1] Fungsi pendeteksian stok Low-Level
    public function getLowLevelStock() {
        $stmt = $this->db->prepare("SELECT * FROM spareparts WHERE stock <= min_stock");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [PBI-14.2] Simpan formulir pengajuan PO
    public function createPO($supplierName, $sparepartId, $quantity) {
        $stmt = $this->db->prepare("INSERT INTO purchase_orders (supplier_name, sparepart_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$supplierName, $sparepartId, $quantity]);
    }

    // Ambil data List PO untuk Gudang
    public function getAllPO() {
        $stmt = $this->db->prepare("
            SELECT po.*, sp.name AS sparepart_name 
            FROM purchase_orders po 
            JOIN spareparts sp ON po.sparepart_id = sp.id
            ORDER BY po.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // [PBI-14.3] Terima batch sparepart & re-kalkulasi penambahan inventaris gudang
    public function terimaBatchSparepart($poId) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT * FROM purchase_orders WHERE id = ? AND status = 'pending'");
            $stmt->execute([$poId]);
            $po = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($po) {
                // Re-kalkulasi stok: tambahkan kuantitas yang baru datang ke stok gudang utama
                $updateStock = $this->db->prepare("UPDATE spareparts SET stock = stock + ? WHERE id = ?");
                $updateStock->execute([$po['quantity'], $po['sparepart_id']]);

                // Update status PO menjadi received
                $updatePO = $this->db->prepare("UPDATE purchase_orders SET status = 'received' WHERE id = ?");
                $updatePO->execute([$poId]);

                $this->db->commit();
                return true;
            }
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
