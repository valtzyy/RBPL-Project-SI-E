<?php

/**
 * Seeder: PaymentTypeSeeder
 * Mengisi tabel payment_types dengan data tunai dan kredit.
 * Dibutuhkan sebelum SalesTransactionSeeder.
 */
class PaymentTypeSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        $types = ['Tunai', 'Cash', 'Kredit', 'Cicilan'];

        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO payment_types (name) VALUES (?)"
        );

        foreach ($types as $name) {
            $stmt->execute([$name]);
            echo "  [SEED] PaymentType: {$name}\n";
        }

        echo "\n✅ PaymentTypeSeeder selesai.\n";
    }
}
