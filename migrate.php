<?php
// ============================================================
//  CLI Migration Tool
//  Cara pakai:
//    php migrate.php           → jalankan semua migrasi baru
//    php migrate.php rollback  → rollback migrasi terakhir
//    php migrate.php seed      → isi data awal (seeder)
// ============================================================

define('ROOT_PATH', __DIR__);

// Load konfigurasi & koneksi
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/database/MigrationRunner.php';

$db = Database::getInstance();
$runner = new MigrationRunner($db, ROOT_PATH . '/database/migrations');

$command = $argv[1] ?? 'migrate';

echo "\n=== RBPL SI E itu mudah!!! ===\n\n";

switch ($command) {

    case 'migrate':
        echo "Melakukan migrasi...\n\n";
        $runner->run();
        break;

    case 'rollback':
        echo "Kembali ke migrasi sebelumnya...\n\n";
        $runner->rollback();
        break;

    case 'seed':
        echo "Menjalankan seeder...\n\n";
        require_once ROOT_PATH . '/database/seeders/UserSeeder.php';
        $seeder = new UserSeeder($db);
        $seeder->run();
        break;

    default:
        echo "Perintah tidak dikenal: {$command}\n";
        echo "Tersedia: migrate | rollback | seed | fresh\n";
}

echo "\n";