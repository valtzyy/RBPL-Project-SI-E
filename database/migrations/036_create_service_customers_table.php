<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
        CREATE TABLE IF NOT EXISTS service_customers (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

            customer_id INT UNSIGNED NOT NULL,

            vehicle_id INT UNSIGNED NOT NULL,

            plate_number VARCHAR(20) NOT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            CONSTRAINT fk_sc_customer
            FOREIGN KEY (customer_id)
            REFERENCES customers(id),

            CONSTRAINT fk_sc_vehicle
            FOREIGN KEY (vehicle_id)
            REFERENCES vehicles(id)
        )
    ");

       
    }
};
