<?php

return new class {
    /**
     * Dijalankan saat: php migrate.php
     * Membuat TABEL BARU untuk log pemeriksaan awal Service Advisor
     */
    public function up(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS initial_inspections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                service_summary_id INT UNSIGNED NOT NULL, 
                service_advisor_id INT NOT NULL,
                kondisi_fisik_awal TEXT NOT NULL,
                catatan_sa TEXT NULL,
                estimasi_waktu_menit INT DEFAULT 30,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (service_summary_id) REFERENCES service_summary(id) ON DELETE CASCADE
            ) ENGINE=InnoDB;
        ");
    }

    /**
     * Dijalankan saat rollback: php migrate.php rollback
     */
    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS initial_inspections");
    }
};