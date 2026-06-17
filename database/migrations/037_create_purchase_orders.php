<?php

return new class {

    public function up(PDO $db): void
    {
        // 1. Buat Tabel purchase_orders untuk mencatat pesanan ke Supplier Luar (PBI-14.2 & PBI-14.3)
        $db->exec("
            CREATE TABLE IF NOT EXISTS purchase_orders (
                id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                supplier_name VARCHAR(150) NOT NULL,
                sparepart_id  INT UNSIGNED NOT NULL,
                quantity      INT NOT NULL DEFAULT 1,
                status        ENUM('pending', 'received') NOT NULL DEFAULT 'pending',
                created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_po_sparepart FOREIGN KEY (sparepart_id) REFERENCES spareparts(id)
            )
        ");

    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS purchase_orders");
    }
};