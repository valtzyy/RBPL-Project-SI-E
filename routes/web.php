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


// Transactions - Sprint 4
$router->get('/transactions',           'SalesTransactionController@index');
$router->post('/transactions',          'SalesTransactionController@store');
$router->get('/transactions/create',    'SalesTransactionController@create');
$router->get('/transactions/:id',       'SalesTransactionController@show');

// Vehicles - Sprint 4
$router->get('/vehicles/available',     'VehicleController@available');

// Customers - Sprint 4
$router->get('/customers',              'CustomerController@index');
$router->get('/customers/:id',          'CustomerController@show');