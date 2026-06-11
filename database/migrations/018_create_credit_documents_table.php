<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS credit_documents (
                id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                credit_application_id INT UNSIGNED NOT NULL,
                file_type             ENUM('KTP','KK','SlipGaji') NOT NULL,
                file_path             VARCHAR(255) NOT NULL,
                CONSTRAINT fk_cd_credit_app FOREIGN KEY (credit_application_id) REFERENCES credit_applications(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS credit_documents");
    }
};
