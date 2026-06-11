<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS procurements (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                request_code   VARCHAR(100) NOT NULL,
                requested_by   INT UNSIGNED NOT NULL,   -- FK users
                status         ENUM('sent','received','approved') NOT NULL DEFAULT 'sent',
                created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_proc_user FOREIGN KEY (requested_by) REFERENCES users(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS procurements");
    }
};
