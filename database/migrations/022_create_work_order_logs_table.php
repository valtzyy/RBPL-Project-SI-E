<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS work_order_logs (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                work_order_id  INT UNSIGNED NOT NULL,
                status         VARCHAR(50),
                notes          TEXT,
                created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_wol_wo FOREIGN KEY (work_order_id) REFERENCES work_orders(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS work_order_logs");
    }
};
