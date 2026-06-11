<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS invoices (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_id  INT UNSIGNED NOT NULL,
                invoice_number  VARCHAR(100) NOT NULL,
                total_amount    DECIMAL(15,2) NOT NULL,
                created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_inv_transaction FOREIGN KEY (transaction_id) REFERENCES sales_transactions(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS invoices");
    }
};
