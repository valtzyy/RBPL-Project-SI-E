<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("ALTER TABLE sales_transactions MODIFY payment_type INT UNSIGNED NOT NULL");
    }

    public function down(PDO $db): void
    {
        $db->exec("ALTER TABLE sales_transactions MODIFY payment_type INT UNSIGNED NULL");
    }
};