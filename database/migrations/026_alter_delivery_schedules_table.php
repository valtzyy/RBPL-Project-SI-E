<?php
return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
                ADD COLUMN customer_name   VARCHAR(150) NOT NULL DEFAULT '',
                ADD COLUMN notes           TEXT,
                ADD COLUMN signature_path  VARCHAR(255),
                ADD COLUMN confirmed_at    DATETIME,
                ADD COLUMN created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
                ADD COLUMN updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
                DROP COLUMN customer_name,
                DROP COLUMN notes,
                DROP COLUMN signature_path,
                DROP COLUMN confirmed_at,
                DROP COLUMN created_at,
                DROP COLUMN updated_at
        ");
    }
};
