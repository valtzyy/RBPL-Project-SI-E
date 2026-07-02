<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
ini_set('display_errors', 0);
define('ROOT_PATH', __DIR__ . '/..');

try {
    require_once ROOT_PATH . '/vendor/autoload.php';
    require_once ROOT_PATH . '/config/env.php';
    require_once ROOT_PATH . '/config/database.php';
    $app = require ROOT_PATH . '/config/app.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    ini_set('display_errors', $app['debug'] ? '1' : '0');
    error_reporting(E_ALL);

    require_once ROOT_PATH . '/core/Database.php';
    require_once ROOT_PATH . '/core/Model.php';
    require_once ROOT_PATH . '/core/Controller.php';
    require_once ROOT_PATH . '/core/Router.php';
    require_once ROOT_PATH . '/core/Auth.php';

    $router = new Router();
    require_once ROOT_PATH . '/routes/web.php';
    $router->run();
} catch (Throwable $e) {
    error_log('[FATAL] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
