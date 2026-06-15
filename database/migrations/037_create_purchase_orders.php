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

        // 2. Buat View untuk Agregasi KPI Dealer (PBI-14.4 & PBI-14.6)
        $db->exec("
            CREATE OR REPLACE VIEW view_kpi_dealer AS
            SELECT 
                COUNT(st.id) AS total_units,
                SUM(CASE WHEN st.status = 'lunas' THEN 1 ELSE 0 END) AS total_lunas,
                SUM(CASE WHEN ca.status = 'rejected' THEN 1 ELSE 0 END) AS total_rejected
            FROM sales_transactions st
            LEFT JOIN credit_applications ca ON st.id = ca.transaction_id
        ");

        // 3. Buat View untuk Agregasi Tren Servis Bulanan (PBI-14.4 & PBI-14.7)
        $db->exec("
            CREATE OR REPLACE VIEW view_service_trends AS
            SELECT 
                MONTHNAME(date) AS month_name,
                MONTH(date) AS month_num,
                SUM(total_work_orders) AS total_services
            FROM service_summary
            GROUP BY MONTH(date), MONTHNAME(date)
            ORDER BY month_num ASC
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP VIEW IF EXISTS view_service_trends");
        $db->exec("DROP VIEW IF EXISTS view_kpi_dealer");
        $db->exec("DROP TABLE IF EXISTS purchase_orders");
    }
};