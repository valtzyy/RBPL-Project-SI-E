<?php

class SparepartSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        try {
            // Disable foreign key checks for easy dummy data insertion
            $this->db->exec("SET FOREIGN_KEY_CHECKS=0");

            // Insert dummy work_orders (ID: 101) for testing
            $stmtWo = $this->db->prepare(
                "INSERT IGNORE INTO work_orders (id, assigned_mechanic, booking_id, status) VALUES (?, ?, ?, ?)"
            );
            $stmtWo->execute([101, 1, 1, 'in_progress']);
            echo "  [SEED] Dummy Work Order (ID: 101)\n";

            // Insert dummy spareparts (ID: 1)
            $spareparts = [
                [1, 'Oli Mesin Matic', 'OLI-001', 50, 10, 50000],
                [2, 'Kampas Rem Depan', 'KMP-001', 30, 5, 45000],
                [3, 'Busi Standar', 'BUS-001', 100, 20, 15000]
            ];

            $stmtSp = $this->db->prepare(
                "INSERT IGNORE INTO spareparts (id, name, sku, stock, min_stock, price) VALUES (?, ?, ?, ?, ?, ?)"
            );

            foreach ($spareparts as $sp) {
                $stmtSp->execute($sp);
                echo "  [SEED] Sparepart: {$sp[1]} (Stock: {$sp[3]})\n";
            }

            // Re-enable foreign key checks
            $this->db->exec("SET FOREIGN_KEY_CHECKS=1");

            echo "\n✅ Seeder Sparepart & Dummy Work Order selesai.\n";
        } catch (Exception $e) {
            echo "\n❌ Gagal menjalankan seeder: " . $e->getMessage() . "\n";
        }
    }
}
