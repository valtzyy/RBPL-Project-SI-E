<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS delivery_schedules (
                id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                transaction_id  INT UNSIGNED NOT NULL,
                scheduled_date  DATE NOT NULL,
                status          VARCHAR(50) NOT NULL DEFAULT 'scheduled',
                CONSTRAINT fk_ds_transaction FOREIGN KEY (transaction_id) REFERENCES sales_transactions(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS delivery_schedules");
    }
};
