<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS audit_logs (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id     INT UNSIGNED NOT NULL,
                action      VARCHAR(100) NOT NULL,
                module      VARCHAR(100),
                description TEXT,
                ip_address  VARCHAR(45),
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS audit_logs");
    }
};
