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
// RUTE SPRINT 9 (Kredit & Leasing - Approval & Uang Muka)
// ============================================================
$router->post('/webhook-approval', 'WebhookApprovalController@process');
$router->post('/verifikasi-dp',    'VerifikasiDpController@process');
$router->post('/upload-kontrak',   'UploadKontrakController@process');
$router->post('/reset-sprint9',    'ResetSprint9Controller@process');
$router->get('/test-sprint9',      'TestSprint9Controller@index');