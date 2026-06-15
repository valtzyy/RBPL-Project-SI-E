<?php

return new class {

    public function up(PDO $db): void
    {

    // Baru hapus kolom
        $db->exec("
            ALTER TABLE service_bookings
            DROP COLUMN vehicle_id
        ");

        // Buat tabel baru
        $db->exec("
            CREATE TABLE IF NOT EXISTS transaction_categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Tambah kolom dan FK
        $db->exec("
            ALTER TABLE invoices
            ADD COLUMN transaction_category_id INT UNSIGNED NULL,
            ADD CONSTRAINT fk_invoice_transaction_category
            FOREIGN KEY (transaction_category_id)
            REFERENCES transaction_categories(id)
            ON DELETE SET NULL
        ");
    }

    public function down(PDO $db): void
    {
        // Hapus FK invoices dulu
        $db->exec("
            ALTER TABLE invoices
            DROP FOREIGN KEY fk_invoice_transaction_category
        ");

        // Hapus kolom
        $db->exec("
            ALTER TABLE invoices
            DROP COLUMN transaction_category_id
        ");

        // Hapus tabel
        $db->exec("
            DROP TABLE IF EXISTS transaction_categories
        ");

        // Kembalikan kolom vehicle_id
        $db->exec("
            ALTER TABLE service_bookings
            ADD COLUMN vehicle_id INT UNSIGNED NULL
        ");

        // Tambahkan FK lagi
        $db->exec("
            ALTER TABLE service_bookings
            ADD CONSTRAINT fk_sb_vehicle
            FOREIGN KEY (vehicle_id)
            REFERENCES vehicles(id)
            ON DELETE SET NULL
        ");
    }
};