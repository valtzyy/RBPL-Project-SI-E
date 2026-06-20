<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS buyer_customers (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                address     TEXT,
                ktp_number  VARCHAR(30),
                vehicle_id INT UNSIGNED NOT NULL,

                CONSTRAINT fk_bc_customer
                FOREIGN KEY (customer_id)
                REFERENCES customers(id),

                CONSTRAINT fk_bc_vehicle
                FOREIGN KEY (vehicle_id)
                REFERENCES vehicles(id),

                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            DROP TABLE IF EXISTS buyer_customers
        ");
    }
};