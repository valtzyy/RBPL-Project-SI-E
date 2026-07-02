<?php

// 1. Ambil teks path relatif dari .env (nilainya akan berisi "./config/certs/ca.pem")
$env_path = getenv('DB_SSL_CA') ?: './config/certs/ca.pem';

// 2. Bersihkan teks './' di depan jika ada, agar tidak merusak penggabungan path
$clean_path = ltrim($env_path, './');

// 3. Gabungkan dengan folder root proyek menggunakan dirname(__DIR__) karena file ini ada di folder /config
// Hasil akhirnya akan menjadi path absolut yang valid di server Vercel maupun Lokal
$absolute_ssl_path = dirname(__DIR__) . '/' . $clean_path;

return [
    'host'     => envRequired('DB_HOST'),
    'port'     => envRequired('DB_PORT'),
    'dbname'   => envRequired('DB_NAME'),
    'username' => envRequired('DB_USER'),
    'password' => envRequired('DB_PASS'),
    // Panggil variabel path absolut yang sudah diolah dengan aman di atas
    'sslcert'  => $absolute_ssl_path, 
    'charset'  => 'utf8mb4',

    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        
        // Masukkan path absolut ke setelan driver PDO PHP 8.5+
        \Pdo\Mysql::ATTR_SSL_CA             => $absolute_ssl_path,
        \Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT => true, 
    ],
];
