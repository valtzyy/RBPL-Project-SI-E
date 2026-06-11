<?php

return new class {

    public function up(PDO $db): void {
        $db->exec("
           CREATE TABLE IF NOT EXISTS roles (
                id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name        VARCHAR(100) NOT NULL
            )
        ");
    }

    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS roles");
    }
};