<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS service_summary (
                id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                date              DATE NOT NULL,
                total_work_orders INT NOT NULL DEFAULT 0,
                completed         INT NOT NULL DEFAULT 0
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS service_summary");
    }
};
