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

// ===========================
// Sprint 6 - Serah Terima
// ===========================
$router->get('/delivery',                      'DeliveryController@index');
$router->get('/delivery/create',               'DeliveryController@create');
$router->get('/delivery/:id',                  'DeliveryController@show');
$router->get('/delivery/:id/document',         'DeliveryController@document');
$router->post('/delivery',                     'DeliveryController@store');
$router->post('/delivery/:id/confirm',         'DeliveryController@confirm');