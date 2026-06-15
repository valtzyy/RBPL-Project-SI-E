<?php
// ============================================================
//  Semua route aplikasi didefinisikan di sini
//  Format: $router->get('/path', 'NamaController@namaMethod')
// ============================================================

// Halaman Utama
$router->get('/',           'HomeController@index');

// Users - CRUD
$router->get('/users',         'UserController@index');
$router->get('/users/:id',     'UserController@show');
$router->post('/users',        'UserController@store');
$router->post('/users/:id',    'UserController@update');
$router->post('/users/delete', 'UserController@destroy');

//PENGADAAN KENDARAAN
//PBI 2.3
$router->get('/procurement',               'ProcurementController@index');
$router->post('/procurement/store',        'ProcurementController@store');
//PBI 2.4 & 2.5
$router->get('/procurement/receipt',       'ProcurementController@receiptList');
$router->get('/procurement/receipt/:id',   'ProcurementController@receipt');
$router->post('/procurement/receipt/store', 'ProcurementController@storeReceipt');