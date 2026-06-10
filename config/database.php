<?php

// Baca file .env secara manual
function loadEnv(string $path): void {
    if (!file_exists($path)) return;

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue; // skip komentar
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

return [
    'host'     => $_ENV['DB_HOST']   ?? 'localhost',
    'port'     => $_ENV['DB_PORT']   ?? '3306',
    'dbname'   => $_ENV['DB_NAME']   ?? 'myapp_db',
    'username' => $_ENV['DB_USER']   ?? 'root',
    'password' => $_ENV['DB_PASS']   ?? '',
    'charset'  => 'utf8mb4',

    // Opsi PDO tambahan
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];