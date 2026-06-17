<?php
// config/cloudinary.php

// Load .env langsung di sini
$envFile = ROOT_PATH . '/.env';
require_once dirname(__DIR__) . '/vendor/autoload.php';

if (!file_exists($envFile)) {
    throw new Exception("File .env tidak ditemukan di: $envFile");
}

foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#')) continue; // skip komentar
    if (!str_contains($line, '=')) continue;          // skip baris tanpa '='
    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}

return [
    'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '',
    'api_key'    => $_ENV['CLOUDINARY_API_KEY']    ?? '',
    'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? '',
];