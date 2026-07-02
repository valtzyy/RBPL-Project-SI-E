<?php

return [
    'host'     => envRequired('DB_HOST'),
    'port'     => envRequired('DB_PORT'),
    'dbname'   => envRequired('DB_NAME'),
    'username' => envRequired('DB_USER'),
    'password' => envRequired('DB_PASS'),
    // Biarkan bawaan jika di lokal, tapi nanti di Vercel kita bypass lewat 'options'
    'sslcert'  => env('DB_SSL_CA', dirname(__DIR__) . '/config/certs/ca.pem'),
    'charset'  => 'utf8mb4',

    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        
        // Tambahkan 2 baris ini di dalam array options untuk Aiven di Vercel:
        PDO::MYSQL_ATTR_SSL_CA             => '',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ],
];
