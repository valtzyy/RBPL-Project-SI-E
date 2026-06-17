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
$router->post('/transactions',          'SalesTransactionController@store');
$router->get('/transactions/create',    'SalesTransactionController@create');
$router->get('/transactions/:id',       'SalesTransactionController@show');

// Sprint 5 - Pembayaran Tunai (Admin & Finance)
$router->get('/admin/transactions',             'AdminTransactionController@index');
$router->get('/admin/transactions/:id',         'AdminTransactionController@show');
$router->get('/admin/transactions/:id/receipt', 'AdminTransactionController@downloadReceipt');

// Sprint 5 - Finance: Verifikasi Pembayaran
$router->get('/finance/payments',                'FinanceController@index');
$router->get('/finance/payments/:id',            'FinanceController@show');
$router->post('/finance/payments/:id/verify',    'FinanceController@verifyPayment');

// Vehicles - Sprint 4
$router->get('/vehicles/available',     'VehicleController@available');

// Customers - Sprint 4
$router->get('/customers',              'CustomerController@index');
$router->get('/customers/:id',          'CustomerController@show');