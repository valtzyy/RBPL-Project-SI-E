<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS daily_sales_summary (
                id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                date               DATE NOT NULL,
                total_sales        DECIMAL(15,2) NOT NULL DEFAULT 0,
                total_transactions INT NOT NULL DEFAULT 0
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS daily_sales_summary");
    }
};
