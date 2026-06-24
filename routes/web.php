<?php
// ============================================================
//  Semua route aplikasi didefinisikan di sini
//  Format: $router->get('/path', 'NamaController@namaMethod')
// ============================================================

// 1. Ini rute untuk MEMBUKA FORM (Halaman sebelum post)
$router->get('/document',       'DocumentController@tampilkanForm');

// 2. Ini rute untuk MEMPROSES UPLOAD (Saat tombol diklik)
$router->post('/document',      'DocumentController@simpanDokumen');

// 3. Ini rute untuk MELIHAT HASIL (Menggunakan query parameter ?id=)
$router->get('/document/view',  'DocumentController@bukaDokumen');

$router->get(
    '/test-notification',
    'NotificationController@test'
);//untuk mengetes notifikasi
$router->get(

'/down-payment',

'DownPaymentController@index'

);

$router->post(

'/down-payment',

'DownPaymentController@store'

); //untuk menyimpan data down payment

$router->post(

'/down-payment/contract',

'DownPaymentController@uploadContract'

); //untuk upload kontrak kredit yang sudah ditandatangani

$router->get('/e2e-testing', 'E2ETestingController@index');
$router->post('/e2e-testing/update', 'E2ETestingController@update');
$router->post('/e2e-testing/reset', 'E2ETestingController@reset');
