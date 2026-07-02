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

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('envRequired')) {
    function envRequired(string $key): string
    {
        $value = env($key, '');

        if ($value === '') {
            throw new RuntimeException("Konfigurasi {$key} belum diisi di file .env");
        }

        return $value;
    }
}

loadEnv(dirname(__DIR__) . '/.env');
