<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // normalisasi path app jika dijalankan di subfolder atau dengan public/index.php
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($basePath !== '' && $basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
            if ($uri === '') {
                $uri = '/';
            }
        }

        // normalize: buang trailing slash
        $uri = rtrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }


        foreach ($this->routes[$method] ?? [] as $path => $action) {
            $pattern = $this->routePattern($path);

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $this->dispatch($action, array_values($params));
            return;
        }

        http_response_code(404);
        echo '<h1>404 - Halaman tidak ditemukan</h1>';
    }

    private function routePattern(string $path): string
    {
        $pattern = preg_quote($path, '@');
        $pattern = preg_replace('/\\\\:([a-zA-Z_][a-zA-Z0-9_]*)/', '(?P<$1>[^/]+)', $pattern);
        return '@^' . $pattern . '$@';
    }

    private function dispatch(string $action, array $params): void
    {
        if (!str_contains($action, '@')) {
            http_response_code(500);
            exit('Format action route tidak valid.');
        }

        [$controllerName, $methodName] = explode('@', $action, 2);

        if (
            !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $controllerName) ||
            !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $methodName)
        ) {
            http_response_code(500);
            exit('Nama controller atau method tidak valid.');
        }

        $controllerPath = ROOT_PATH . "/app/controllers/{$controllerName}.php";

        if (!file_exists($controllerPath)) {
            http_response_code(500);
            exit("Controller tidak ditemukan: {$controllerName}");
        }

        require_once $controllerPath;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            exit("Class controller tidak ditemukan: {$controllerName}");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            exit("Method controller tidak ditemukan: {$methodName}");
        }

        $controller->$methodName(...$params);
    }
}
