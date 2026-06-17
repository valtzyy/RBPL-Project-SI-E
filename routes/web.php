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

// Finance - PBI 5.1, 5.2, 5.3, 5.7
$router->get('/finance/queue',                    'FinanceController@queue');
$router->get('/finance/transactions/:id',         'FinanceController@showTransaction');
$router->post('/finance/payments/:id/verify',     'FinanceController@verifyPayment');
$router->get('/finance/payments/:id/receipt',     'FinanceController@downloadReceipt');
$router->get('/finance/detail',                           'FinanceController@showTransaction');