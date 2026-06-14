//SPRINT 11 Testing Seeder

<?php
// run_seeder.php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// 1. Ambil koneksi database bawaan timmu
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Database.php';

// 2. Load file seeder yang mau ditest
require_once ROOT_PATH . '/database/seeders/WorkOrderSeeder.php';

try {
    $dbConnection = Database::getInstance();

    // 3. Jalankan Seeder dengan menyuntikkan koneksi $dbConnection
    $seeder = new WorkOrderSeeder($dbConnection);
    $seeder->run();

} catch (PDOException $e) {
    echo "🚨 Gagal menjalankan seeder: " . $e->getMessage() . "\n";
}