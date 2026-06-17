<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE sales_transactions
            DROP COLUMN payment_type,
            ADD COLUMN payment_type INT UNSIGNED NULL AFTER sales_user_id,
            ADD CONSTRAINT fk_st_payment_type FOREIGN KEY (payment_type) REFERENCES payment_types(id)
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE sales_transactions
            DROP FOREIGN KEY fk_st_payment_type,
            DROP COLUMN payment_type,
            ADD COLUMN payment_type VARCHAR(50) AFTER sales_user_id
        ");
    }
};