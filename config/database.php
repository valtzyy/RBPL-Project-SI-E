<?php

return [
    'host'     => envRequired('DB_HOST'),
    'port'     => envRequired('DB_PORT'),
    'dbname'   => envRequired('DB_NAME'),
    'username' => envRequired('DB_USER'),
    'password' => envRequired('DB_PASS'),
    'sslcert'  => env('DB_SSL_CA', ''),
    'charset'  => 'utf8mb4',

    'options'  => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
