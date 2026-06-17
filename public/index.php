<?php

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';

require_once ROOT_PATH . '/config/database.php';
$app = require ROOT_PATH . '/config/app.php';


if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

if (($app['debug'] ?? false) === true) {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

error_reporting(E_ALL);

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Auth.php';

$router = new Router();

require_once ROOT_PATH . '/routes/web.php';

$router->run();
