<?php
// ============================================================
//  Semua route aplikasi didefinisikan di sini
//  Format: $router->get('/path', 'NamaController@namaMethod')
// ============================================================

// Halaman Utama
$router->get('/',           'HomeController@index');
$router->get('/sprint15',   'ReportController@testingPage');

// Users - CRUD
$router->get('/users',         'UserController@index');
$router->get('/users/:id',     'UserController@show');
$router->post('/users',        'UserController@store');
$router->post('/users/:id',    'UserController@update');
$router->post('/users/delete', 'UserController@destroy');

// Sprint 15 - Reporting, Export, Audit Log
$router->get('/api/reports/:type', 'ReportController@report');
$router->get('/api/reports/:type/export/pdf', 'ReportController@exportPdf');
$router->get('/api/reports/:type/export/excel', 'ReportController@exportExcel');
$router->get('/api/audit-logs', 'ReportController@auditLogs');
