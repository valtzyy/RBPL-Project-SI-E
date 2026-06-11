<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS service_bookings (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id  INT UNSIGNED NOT NULL,
                vehicle_id   INT UNSIGNED NOT NULL,
                booking_date DATE NOT NULL,
                status       ENUM('queued','confirmed','rejected') NOT NULL DEFAULT 'queued',
                CONSTRAINT fk_sb_customer FOREIGN KEY (customer_id) REFERENCES customers(id),
                CONSTRAINT fk_sb_vehicle  FOREIGN KEY (vehicle_id)  REFERENCES vehicles(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS service_bookings");
    }
};
