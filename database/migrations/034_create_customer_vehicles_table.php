<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS customer_vehicles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                vehicle_id INT UNSIGNED NOT NULL,
                plate_number VARCHAR(20) NOT NULL,

                CONSTRAINT fk_cv_customer
                FOREIGN KEY (customer_id)
                REFERENCES customers(id),

                CONSTRAINT fk_cv_vehicle
                FOREIGN KEY (vehicle_id)
                REFERENCES vehicles(id),

                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->exec("
            ALTER TABLE service_bookings
                DROP COLUMN customer_id,
                ADD COLUMN customer_vehicle_id INT UNSIGNED NULL,
                ADD CONSTRAINT fk_sb_customer_vehicle
                FOREIGN KEY (customer_vehicle_id)
                REFERENCES customer_vehicles(id)
        ");
    }

    public function down(PDO $db): void
    {
    }
};
