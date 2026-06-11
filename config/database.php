<?php

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim(trim($value), "\"'");
        }
    }
}

if (!function_exists('envRequired')) {
    function envRequired(string $key): string
    {
        $value = $_ENV[$key] ?? '';

        if ($value === '') {
            throw new RuntimeException("Konfigurasi {$key} belum diisi di file .env");
        }

        return $value;
    }
}

loadEnv(__DIR__ . '/../.env');

return [
    'host'     => envRequired('DB_HOST'),
    'port'     => envRequired('DB_PORT'),
    'dbname'   => envRequired('DB_NAME'),
    'username' => envRequired('DB_USER'),
    'password' => envRequired('DB_PASS'),
    'sslcert'  => $_ENV['DB_SSL_CA'] ?? '',
    'charset'  => 'utf8mb4',

    'options'  => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
