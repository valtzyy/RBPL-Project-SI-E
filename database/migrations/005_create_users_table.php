<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS users (
                id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(150) NOT NULL,
                username   VARCHAR(100) NOT NULL UNIQUE,
                email      VARCHAR(150) NOT NULL UNIQUE,
                password   VARCHAR(255) NOT NULL,
                role_id    INT UNSIGNED NOT NULL,
                status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS users");
    }
};
