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

// sprint-12 pembayaran services
$router->get('/service-billing',      'ServiceBillingController@index');
$router->get('/service-billing/:plateNumber', 'ServiceBillingController@findByPlateNumber');
$router->get('/service-billing/detail/:plateNumber',  'ServiceBillingController@detail');
$router->get('/service-billing/detail/history/:plateNumber',  'ServiceBillingController@detailLog');

$router->get('/kasir/dashboard', 'KasirController@dashboard');
$router->get('/kasir/nota',      'KasirController@nota');
$router->get('/kasir/riwayat',   'KasirController@riwayat');
