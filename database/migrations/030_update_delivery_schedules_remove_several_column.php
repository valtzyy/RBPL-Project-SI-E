<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP COLUMN signature_path
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
            ADD COLUMN signature_path VARCHAR(255) NULL
        ");
    }
};