<?php

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $config = require ROOT_PATH . '/config/database.php';

            $certPath = $config['sslcert'] ?? null;

            // DEBUG WAJIB (hapus nanti kalau sudah jalan)
            if (!file_exists($certPath)) {
                die("CA CERT NOT FOUND: " . $certPath);
            }

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset=utf8mb4";

            $options = $config['options'] ?? [];

            // SSL Aiven
            $options[PDO::MYSQL_ATTR_SSL_CA] = $certPath;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $options
                );

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("❌ Koneksi Aiven MySQL gagal: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}