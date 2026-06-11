<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS credit_applications (
                id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_id INT UNSIGNED NOT NULL,
                status       ENUM('submitted','approved','rejected') NOT NULL DEFAULT 'submitted',
                leasing_name VARCHAR(150),
                created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_ca_transaction FOREIGN KEY (transaction_id) REFERENCES sales_transactions(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS credit_applications");
    }
};
