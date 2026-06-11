<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS spareparts (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name        VARCHAR(150) NOT NULL,
                sku         VARCHAR(100) NOT NULL UNIQUE,
                stock       INT NOT NULL DEFAULT 0,
                min_stock   INT NOT NULL DEFAULT 0,
                price       DECIMAL(15,2) NOT NULL
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS spareparts");
    }
};
