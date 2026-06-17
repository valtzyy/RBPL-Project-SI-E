<?php

return new class {

    public function up(PDO $db): void
    {
        // HANYA MENGHAPUS KOLOM signature_base64
        $db->exec("
            ALTER TABLE delivery_schedules
            DROP COLUMN signature_base64
        ");
    }

    public function down(PDO $db): void
    {
        // MEMBALIKKAN PROSES: Menambahkan kembali kolom signature_base64 jika di-rollback
        // Sesuaikan 'LONGTEXT NULL' dengan tipe data asli kolom Anda sebelumnya
        $db->exec("
            ALTER TABLE delivery_schedules
            ADD COLUMN signature_base64 LONGTEXT NULL
        ");
    }
};