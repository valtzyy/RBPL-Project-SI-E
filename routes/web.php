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
$router->get('/delivery',                      'DeliveryScheduleController@index');
$router->get('/delivery/create',               'DeliveryScheduleController@create');
$router->get('/delivery/:id',                  'DeliveryScheduleController@show');
$router->get('/delivery/:id/document',         'DeliveryScheduleController@document');
$router->post('/delivery',                     'DeliveryScheduleController@store');
$router->post('/delivery/:id/confirm',         'DeliveryScheduleController@confirm');