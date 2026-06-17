<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS procurement_details (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                procurement_id INT UNSIGNED NOT NULL,
                vehicle_id     INT UNSIGNED NOT NULL,
                quantity       INT UNSIGNED NOT NULL DEFAULT 1,

                CONSTRAINT fk_pd_procurement
                    FOREIGN KEY (procurement_id)
                    REFERENCES procurements(id),

                CONSTRAINT fk_pd_vehicle
                    FOREIGN KEY (vehicle_id)
                    REFERENCES vehicles(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec('DROP TABLE IF EXISTS procurement_details');
    }
};