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

//PENGADAAN KENDARAAN
//PBI 2.3
$router->get('/procurement',               'ProcurementController@index');
$router->post('/procurement/store',        'ProcurementController@store');
//PBI 2.4 & 2.5
$router->get('/procurement/receipt',       'ProcurementController@receiptList');
$router->get('/procurement/receipt/:id',   'ProcurementController@receipt');
$router->post('/procurement/receipt/store', 'ProcurementController@storeReceipt');