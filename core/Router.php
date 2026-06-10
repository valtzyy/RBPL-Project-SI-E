<?php

class Router {
    private array $routes = [];

    /** Daftarkan route GET */
    public function get(string $path, string $action): void {
        $this->routes['GET'][$path] = $action;
    }

    /** Daftarkan route POST */
    public function post(string $path, string $action): void {
        $this->routes['POST'][$path] = $action;
    }

    /** Jalankan router berdasarkan URL saat ini */
    public function run(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Hilangkan trailing slash kecuali root
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes[$method] ?? [] as $path => $action) {
            // Ubah :param menjadi regex
            $pattern = preg_replace('/:([a-z]+)/', '(?P<$1>[^/]+)', $path);
            $pattern = "@^{$pattern}$@";

            if (preg_match($pattern, $uri, $matches)) {
                // Ambil hanya named captures
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerName, $methodName] = explode('@', $action);

                require_once ROOT_PATH . "/app/controllers/{$controllerName}.php";

                $controller = new $controllerName();
                $controller->$methodName(...array_values($params));
                return;
            }
        }

        // 404 jika tidak ada yang cocok
        http_response_code(404);
        echo "<h1>404 - Halaman tidak ditemukan</h1>";
    }
}