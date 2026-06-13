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

// Transactions - Sprint 4
$router->get('/transactions',           'SalesTransactionController@index');
$router->get('/transactions/create',    'SalesTransactionController@create');
$router->post('/transactions',          'SalesTransactionController@store');
$router->get('/transactions/:id',       'SalesTransactionController@show');

// Vehicles - Sprint 4
$router->get('/vehicles/available',     'VehicleController@available');

// Customers - Sprint 4
$router->get('/customers',              'CustomerController@index');
$router->get('/customers/:id',          'CustomerController@show');