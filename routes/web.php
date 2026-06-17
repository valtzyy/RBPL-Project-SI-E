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

// ============================================================
// SPRINT 11 - Eksekusi & Work Order Servis


// 1. Menampilkan Halaman Panel Kerja Mekanik (UI Ter-assign)
$router->get('/mechanic/panel', 'WorkOrderController@index');

// 2. API/Action untuk Mengubah Progres Item / Status Kerja Mekanik
$router->post('/mechanic/work-order/update-status', 'WorkOrderController@updateStatus');

// 3. Menampilkan Halaman Tambah Log & History Log
$router->get('/mechanic/work-order/log', 'WorkOrderController@addLogForm');

// 4. API/Action untuk Menyimpan Log Baru
$router->post('/mechanic/work-order/log/store', 'WorkOrderController@storeLog');
// ============================================================