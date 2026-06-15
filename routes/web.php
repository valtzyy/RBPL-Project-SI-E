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

// ============================================================
// [SPRINT 14] - MANAJEMEN SUKU CADANG GUDANG & DASHBOARD
// ============================================================

// Modul Gudang Suku Cadang & Purchase Order (PO)
$router->get('/sparepart',           'SparepartController@index');
$router->post('/sparepart/po/store', 'SparepartController@storePO');
$router->get('/sparepart/po/terima', 'SparepartController@terimaPO'); // Jika menerima pakai query string (?id=)

// Modul Dashboard Eksekutif / Manajerial
$router->get('/dashboard',            'DashboardController@index');

// API Endpoints untuk Data Visualisasi (Chart.js / Recharts)
$router->get('/api/dashboard/kpi',    'DashboardController@apiKpi');
$router->get('/api/dashboard/trends', 'DashboardController@apiTrenServis');