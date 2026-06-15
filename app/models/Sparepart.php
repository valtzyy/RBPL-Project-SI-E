<?php

class Sparepart {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Ambil semua data sparepart untuk dropdown di form PO
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