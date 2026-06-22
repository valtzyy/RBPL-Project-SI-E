<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP COLUMN signature_base64,
            ADD COLUMN signature_path VARCHAR(255) NULL AFTER status
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP COLUMN signature_path,
            ADD COLUMN signature_base64 LONGTEXT NULL AFTER status
        ");
    }
};