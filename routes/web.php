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

// API - Sparepart
$router->get('/api/sparepart/test', 'SparepartController@testView');
$router->post('/api/sparepart/request', 'SparepartController@request');
$router->get('/sparepart/create', 'SparepartController@createView');
$router->post('/sparepart/store', 'SparepartController@store');
$router->get('/api/sparepart/search', 'SparepartController@search');
$router->get('/api/invoice/draft', 'SparepartController@invoiceDraft');
$router->get('/mekanik/work-order', 'SparepartController@workOrderView');