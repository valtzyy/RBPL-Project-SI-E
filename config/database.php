<?php

// 1. Ambil teks isi sertifikat CA dari Environment Variable Vercel
$ca_content = getenv('DB_SSL_CA_CONTENT');

// 2. Tentukan jalur file sertifikat fisik buatan
// Di Vercel Serverless, hanya folder '/tmp' yang diizinkan untuk menulis file fisik
$is_vercel = getenv('VERCEL') === '1';
$ssl_ca_path = $is_vercel ? '/tmp/aiven_ca.pem' : dirname(__DIR__) . '/config/certs/ca.pem';

// 3. Jika di Vercel dan data env tersedia, buat file ca.pem secara otomatis di folder /tmp
if ($is_vercel && !empty($ca_content) && !file_exists($ssl_ca_path)) {
    // Menghidupkan kembali karakter baris baru (\n) yang sempat merapat di dashboard env
    $formatted_ca = str_replace('\n', "\n", $ca_content);
    file_put_contents($ssl_ca_path, $formatted_ca);
}

return [
    'host'     => envRequired('DB_HOST'),
    'port'     => envRequired('DB_PORT'),
    'dbname'   => envRequired('DB_NAME'),
    'username' => envRequired('DB_USER'),
    'password' => envRequired('DB_PASS'),
    // Isikan dengan path file fisik yang sudah dijamin ada di atas
    'sslcert'  => env('DB_SSL_CA', $ssl_ca_path), 
    'charset'  => 'utf8mb4',

    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        
        // Tetap pasangkan penanda SSL CA fisik menggunakan standard baru PHP 8.5
        \Pdo\Mysql::ATTR_SSL_CA             => $ssl_ca_path,
        \Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT => true, // Set ke true karena file fisik ca.pem sekarang resmi ada!
    ],
];
