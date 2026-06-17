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

    case 'rollback:all':
        echo "Rollback semua migrasi...\n\n";
        $runner->rollback(999);       // semua
        break;

    case 'seed':
        echo "Menjalankan semua seeder...\n\n";

        // 1. Payment Types (dependency untuk transaksi)
        require_once ROOT_PATH . '/database/seeders/PaymentTypeSeeder.php';
        (new PaymentTypeSeeder($db))->run();

        // 2. Users
        require_once ROOT_PATH . '/database/seeders/UserSeeder.php';
        (new UserSeeder($db))->run();

        // 3. Sales Transactions Tunai (perlu customer, vehicle, user, payment_type)
        require_once ROOT_PATH . '/database/seeders/SalesTransactionSeeder.php';
        (new SalesTransactionSeeder($db))->run();

        // 4. Payments dummy (perlu transaksi)
        require_once ROOT_PATH . '/database/seeders/PaymentSeeder.php';
        (new PaymentSeeder($db))->run();
        break;

    // Jalankan hanya seeder khusus PBI-5.8 (payment flow)
    case 'seed:finance':
        echo "Menjalankan seeder Finance (PBI-5.8)...\n\n";

        require_once ROOT_PATH . '/database/seeders/PaymentTypeSeeder.php';
        (new PaymentTypeSeeder($db))->run();

        require_once ROOT_PATH . '/database/seeders/SalesTransactionSeeder.php';
        (new SalesTransactionSeeder($db))->run();

        require_once ROOT_PATH . '/database/seeders/PaymentSeeder.php';
        (new PaymentSeeder($db))->run();
        break;

    default:
        echo "Perintah tidak dikenal: {$command}\n";
        echo "Tersedia: migrate | rollback | seed | fresh\n";
}

echo "\n";
