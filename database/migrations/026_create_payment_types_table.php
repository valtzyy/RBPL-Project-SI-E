<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS payment_types (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name        VARCHAR(50) NOT NULL,
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS payment_types");
    }
};