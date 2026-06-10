<?php

class Database {
    private static ?PDO $instance = null;

    // Tidak bisa di-new dari luar
    private function __construct() {}

    /**
     * Ambil koneksi PDO (dibuat sekali, dipakai terus)
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // Tampilkan error yang ramah
                die("❌ Koneksi database gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}