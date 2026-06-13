<?php
return new class {
    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
                ADD COLUMN signature_base64 LONGTEXT
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE delivery_schedules
                DROP COLUMN signature_base64
        ");
    }
};
