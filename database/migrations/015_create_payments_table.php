<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS payments (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_id  INT UNSIGNED NOT NULL,
                amount          DECIMAL(15,2) NOT NULL,
                payment_date    DATE NOT NULL,
                verified_by     INT UNSIGNED,           -- FK users (nullable)
                status          ENUM('pending','verified') NOT NULL DEFAULT 'pending',
                CONSTRAINT fk_pay_transaction FOREIGN KEY (transaction_id) REFERENCES sales_transactions(id),
                CONSTRAINT fk_pay_user        FOREIGN KEY (verified_by)    REFERENCES users(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS payments");
    }
};
