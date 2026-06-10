<?php
// ============================================================
//  ENTRY POINT — semua request masuk ke sini
//  Pastikan web server diarahkan ke folder public/
// ============================================================

define('ROOT_PATH', dirname(__DIR__));

// 1. Load konfigurasi (juga memuat .env)
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/app.php';

// 2. Load core classes
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Router.php';

// 3. Buat instance router
$router = new Router();

// 4. Muat daftar route
require_once ROOT_PATH . '/routes/web.php';

// 5. Jalankan!
$router->run();