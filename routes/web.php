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

// Booking Servis - Sprint 10
$router->get('/booking',             'BookingController@index');
$router->get('/booking/check-slot',  'BookingController@checkSlot');
$router->post('/booking/store',      'BookingController@store');
$router->get('/booking/queue',       'BookingController@queue');
$router->post('/booking/confirm',    'BookingController@confirm');
$router->post('/booking/reject',     'BookingController@reject');