<?php
// ============================================================
//  Semua route aplikasi didefinisikan di sini
//  Format: $router->get('/path', 'NamaController@namaMethod')
// ============================================================

// Halaman Utama / Dashboard
$router->get('/',               'HomeController@index');

// 1. Ini rute untuk MEMBUKA FORM (Halaman sebelum post)
$router->get('/document',       'DocumentController@tampilkanForm');

// Auth
$router->get('/login',       'AuthController@showLogin');
$router->post('/login',      'AuthController@login');
$router->post('/logout',     'AuthController@logout');

// Admin - Manajemen Akun
$router->get('/admin/users',                'UserManagementController@index');
$router->get('/admin/users/create',         'UserManagementController@create');
$router->post('/admin/users',               'UserManagementController@store');
$router->get('/admin/users/:id/edit',       'UserManagementController@edit');
$router->post('/admin/users/:id',           'UserManagementController@update');
$router->post('/admin/users/:id/deactivate','UserManagementController@deactivate');

// Profile
$router->get('/change-password',  'ProfileController@editPassword');
$router->post('/change-password', 'ProfileController@updatePassword');

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

// 3. Ini rute untuk MENAMPILKAN FORM UPLOAD DOKUMEN (PBI-8.2)
$router->get('/credit/upload', 'CreditController@uploadForm');

// 4. Ini rute untuk MENAMPILKAN FORM CREATE PENGAJUAN KREDIT (PBI-8.4 UI)
$router->get('/credit/create-form', 'CreditController@createForm');

// 5. Ini rute untuk MELIHAT STATUS PENGAJUAN KREDIT
// Digunakan untuk menampilkan status submitted/approved/rejected
$router->get('/credit/status', 'CreditController@status');

// 6. Ini rute untuk MENYIMPAN KEPUTUSAN KREDIT
// Digunakan untuk approve atau reject pengajuan kredit
// Data akan disimpan ke tabel credit_decisions dan memperbarui status pada credit_applications
$router->post('/credit/decision', 'CreditController@decision');

// 7. SPRINT 8 - PBI-8.7: Follow-up setelah kredit ditolak
// Customer pilih: batal / ganti tunai / re-apply ke leasing lain
$router->post('/credit/cancel',      'CreditController@cancel');
$router->post('/credit/switch-cash', 'CreditController@switchToCash');
$router->post('/credit/reapply',     'CreditController@reapply');

//PENGADAAN KENDARAAN
//PBI 2.3
$router->get('/procurement',               'ProcurementController@index');
$router->post('/procurement/store',        'ProcurementController@store');
//PBI 2.4 & 2.5
$router->get('/procurement/receipt',       'ProcurementController@receiptList');
$router->get('/procurement/receipt/:id',   'ProcurementController@receipt');
$router->post('/procurement/receipt/store', 'ProcurementController@storeReceipt');


// Sprint 3 - Manajemen Stok Kendaraan
$router->get('/inventory', 'VehicleController@index');
$router->get('/inventory/create', 'VehicleController@create');
$router->post('/inventory/store', 'VehicleController@store');
$router->get('/inventory/edit/:id', 'VehicleController@edit');
$router->post('/inventory/update/:id', 'VehicleController@update');
$router->post('/inventory/delete/:id', 'VehicleController@delete');

// API/controller CRUD inventaris kendaraan
$router->get('/api/inventory', 'VehicleController@apiIndex');
$router->get('/api/inventory/:id', 'VehicleController@apiShow');
$router->post('/api/inventory', 'VehicleController@apiStore');
$router->post('/api/inventory/update/:id', 'VehicleController@apiUpdate');
$router->post('/api/inventory/delete/:id', 'VehicleController@apiDelete');

// Backend hooks untuk integrasi modul procurement dan sales
$router->post('/procurement-receipts', 'ProcurementReceiptController@store');
$router->post('/sales-transactions/:id/status', 'SalesTransactionController@updateStatus');


// Transactions - Sprint 4
$router->get('/transactions',           'SalesTransactionController@index');
$router->get('/transactions/create',    'SalesTransactionController@create');
$router->get('/transactions/:id',       'SalesTransactionController@show');
$router->post('/transactions',          'SalesTransactionController@store');

// Vehicles - Sprint 4
$router->get('/vehicles/available',     'VehicleController@available');

// Customers - Sprint 4
$router->get('/customers',              'CustomerController@index');
$router->get('/customers/create',       'CustomerController@create');
$router->post('/customers',             'CustomerController@store');
$router->get('/customers/:id',          'CustomerController@show');

// Booking Servis - Sprint 10
$router->get('/booking',             'BookingController@index');
$router->get('/booking/check-slot',  'BookingController@checkSlot');
$router->post('/booking/create-customer', 'BookingController@createCustomer');
$router->post('/booking/store',      'BookingController@store');
$router->get('/booking/queue',       'BookingController@queue');
$router->post('/booking/confirm',    'BookingController@confirm');
$router->post('/booking/reject',     'BookingController@reject');
$router->get('/booking/inspect/:id', 'BookingController@inspectForm');
$router->post('/booking/inspect/:id/convert', 'BookingController@convertToWorkOrder');
