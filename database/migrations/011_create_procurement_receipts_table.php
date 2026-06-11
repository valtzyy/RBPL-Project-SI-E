<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS procurement_receipts (
                id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                procurement_id    INT UNSIGNED NOT NULL,
                received_by       VARCHAR(50),    
                inspection_result TEXT,
                created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_pr_procurement FOREIGN KEY (procurement_id) REFERENCES procurements(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS procurement_receipts");
    }
};
