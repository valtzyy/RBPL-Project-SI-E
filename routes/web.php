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
$router->get('/service-billing',      'ServiceBillingController@index');
$router->get('/service-billing/:id',  'ServiceBillingController@detail');
$router->get('/service-billing/plate/:plateNumber', 'ServiceBillingController@findByPlateNumber');
$router->get('/kasir/dashboard', 'KasirController@dashboard');
$router->get('/kasir/nota',      'KasirController@nota');
$router->get('/kasir/riwayat',   'KasirController@riwayat');
