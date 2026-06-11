<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS vehicles (
                id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                brand            VARCHAR(100) NOT NULL,
                type             VARCHAR(100) NOT NULL,
                color            VARCHAR(50)  NOT NULL,
                chassis_number   VARCHAR(100) NOT NULL UNIQUE,
                engine_number    VARCHAR(100) NOT NULL UNIQUE,
                price            DECIMAL(15,2) NOT NULL,
                status           ENUM('available','held','sold') NOT NULL DEFAULT 'available',
                created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS vehicles");
    }
};
