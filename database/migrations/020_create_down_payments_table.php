<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS down_payments (
                id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                credit_application_id INT UNSIGNED NOT NULL,
                amount                DECIMAL(15,2) NOT NULL,
                paid_at               DATE,
                verified_by           INT UNSIGNED,     -- FK users (nullable)
                CONSTRAINT fk_dp_credit_app FOREIGN KEY (credit_application_id) REFERENCES credit_applications(id),
                CONSTRAINT fk_dp_user       FOREIGN KEY (verified_by)           REFERENCES users(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS down_payments");
    }
};
