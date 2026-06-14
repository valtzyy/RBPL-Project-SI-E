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
// CREDIT APPLICATION ROUTES (SPRINT 8)
// ============================================================

// 1. Ini rute untuk MEMBUAT PENGAJUAN KREDIT BARU
// Data akan disimpan ke tabel credit_applications
$router->post('/credit/create', 'CreditController@create');

// 2. Ini rute untuk UPLOAD DOKUMEN SYARAT KREDIT (PBI-8.3)
// File dikirim sebagai base64, disimpan di Cloudinary, public_id di tabel credit_documents
$router->post('/credit/upload', 'CreditController@uploadDocument');

// 2. Ini rute untuk MELIHAT STATUS PENGAJUAN KREDIT
// Digunakan untuk menampilkan status submitted/approved/rejected
$router->get('/credit/status', 'CreditController@status');

// 3. Ini rute untuk MENYIMPAN KEPUTUSAN KREDIT
// Digunakan untuk approve atau reject pengajuan kredit
// Data akan disimpan ke tabel credit_decisions dan memperbarui status pada credit_applications
$router->post('/credit/decision', 'CreditController@decision');

// 4. SPRINT 8 - PBI-8.7: Follow-up setelah kredit ditolak
// Customer pilih: batal / ganti tunai / re-apply ke leasing lain
$router->post('/credit/cancel',      'CreditController@cancel');
$router->post('/credit/switch-cash', 'CreditController@switchToCash');
$router->post('/credit/reapply',     'CreditController@reapply');
