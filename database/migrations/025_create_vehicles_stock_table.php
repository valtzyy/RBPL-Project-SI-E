<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS vehicles_stock (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                vehicle_id  INT UNSIGNED NOT NULL,
                quantity    INT NOT NULL DEFAULT 0,
                min_stock   INT NOT NULL DEFAULT 0,
                updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_vs_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS vehicles_stock");
    }
};
