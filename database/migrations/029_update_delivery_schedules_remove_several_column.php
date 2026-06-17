<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP COLUMN customer_name,
            ADD COLUMN customer_id INT UNSIGNED NULL AFTER status,
            ADD CONSTRAINT fk_st_customer_id FOREIGN KEY (customer_id) REFERENCES customers(id)
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP FOREIGN KEY fk_st_customer_id,
            DROP COLUMN customer_id,
            ADD COLUMN customer_name VARCHAR(150) AFTER status
        ");
    }
};