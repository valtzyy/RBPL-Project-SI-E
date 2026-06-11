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
            $certPath = $config['sslcert'] ?? '';

            if ($certPath !== '' && !file_exists($certPath)) {
                self::fail('Sertifikat CA database tidak ditemukan.');
            }

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            $options = $config['options'] ?? [];

            if ($certPath !== '') {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $certPath;
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
            }

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $options
                );
            } catch (PDOException $e) {
                error_log('Koneksi database gagal: ' . $e->getMessage());
                self::fail('Koneksi database gagal. Periksa konfigurasi .env dan akses Aiven.');
            }
        }

        return self::$instance;
    }

    private static function fail(string $message): never
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }

        exit($message . PHP_EOL);
    }
}
