<?php
// ============================================================
//  Semua route aplikasi didefinisikan di sini
//  Format: $router->get('/path', 'NamaController@namaMethod')
// ============================================================

// Halaman Utama
$router->get('/',           'HomeController@index');

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

// Users - CRUD
$router->get('/users',         'UserController@index');
$router->get('/users/:id',     'UserController@show');
$router->post('/users',        'UserController@store');
$router->post('/users/:id',    'UserController@update');
$router->post('/users/delete', 'UserController@destroy');
