<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS sales_transactions (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_code VARCHAR(100) NOT NULL,
                customer_id     INT UNSIGNED NOT NULL,
                vehicle_id      INT UNSIGNED NOT NULL,
                sales_user_id   INT UNSIGNED NOT NULL,
                payment_type    VARCHAR(50),
                status          ENUM('process','lunas','cancel') NOT NULL DEFAULT 'process',
                created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_st_customer FOREIGN KEY (customer_id)   REFERENCES customers(id),
                CONSTRAINT fk_st_vehicle  FOREIGN KEY (vehicle_id)    REFERENCES vehicles(id),
                CONSTRAINT fk_st_user     FOREIGN KEY (sales_user_id) REFERENCES users(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS sales_transactions");
    }
};
