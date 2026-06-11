<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS credit_decisions (
                id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                credit_application_id INT UNSIGNED NOT NULL,
                decision              ENUM('approved','rejected') NOT NULL,
                notes                 TEXT,
                decided_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_cds_credit_app FOREIGN KEY (credit_application_id) REFERENCES credit_applications(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS credit_decisions");
    }
};
