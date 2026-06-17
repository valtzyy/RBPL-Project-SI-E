<?php
// database/seeders/WorkOrderSeeder.php

class WorkOrderSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        // Disinkronkan dengan dump-dealer_mobil-202606141824.sql:
        // Parameter: [assigned_mechanic (ID: 5), booking_id (ID: 1/2/3), status, deskripsi]
        $workOrders = [
            [5, 1, 'in_progress', 'Ganti oli mesin Castrol Magnatec dan tune-up filter udara.'],
            [5, 2, 'done',        'Perbaikan kampas rem depan aus dan kuras minyak rem.'],
            [5, 3, 'ready',       'Spooring balancing roda depan dan pengecekan tekanan ban.'],
        ];

        // Kosongkan data lama terlebih dahulu agar fresh dan tidak duplikat
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $this->db->exec("TRUNCATE TABLE work_orders;");
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        $stmt = $this->db->prepare(
            "INSERT INTO work_orders (assigned_mechanic, booking_id, status, description) VALUES (?, ?, ?, ?)"
        );

        foreach ($workOrders as $wo) {
            $stmt->execute($wo);
            echo "  [SEED] Work Order Sukses dimasukkan untuk Mekanik ID: {$wo[0]} [Status: {$wo[2]}]\n";
        }

        echo "\n✅ Seeder Work Order berhasil disinkronkan dengan DB Cloud.\n";
    }
}