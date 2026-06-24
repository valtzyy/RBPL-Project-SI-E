<?php

return new class {

    public function up(PDO $db): void
    {
        $db->exec("
            ALTER TABLE credit_documents
            MODIFY file_type ENUM('KTP','KK','SlipGaji','SignedContract') NOT NULL
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("
            ALTER TABLE credit_documents
            MODIFY file_type ENUM('KTP','KK','SlipGaji') NOT NULL
        ");
    }
};
