<?php

// Migrasi 001 — Buat tabel users
return new class {

    public function up(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS users (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                name       VARCHAR(100)  NOT NULL,
                email      VARCHAR(150)  NOT NULL UNIQUE,
                password   VARCHAR(255)  NOT NULL,
                created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
    }

    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS users");
    }
};