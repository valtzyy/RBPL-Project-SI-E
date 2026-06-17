<?php

class Controller
{
    protected function view(string $name, array $data = []): void
    {
        if (!preg_match('/^[a-zA-Z0-9_\/-]+$/', $name)) {
            http_response_code(400);
            exit('Nama view tidak valid.');
        }

        extract($data, EXTR_SKIP);

        $viewPath = ROOT_PATH . "/app/views/{$name}.php";

        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit("View tidak ditemukan: {$name}.php");
        }

        $layoutPath = ROOT_PATH . '/app/views/layout/main.php';

        if (file_exists($layoutPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean();
            
            // Bypass layout if view is already a full HTML document
            if (stripos(trim($content), '<!DOCTYPE') === 0 || stripos(trim($content), '<html') === 0) {
                echo $content;
            } else {
                require $layoutPath;
            }
            return;
        }

        require $viewPath;
    }

    protected function redirect(string $url): void
    {
        if (preg_match('/[\r\n]/', $url)) {
            http_response_code(400);
            exit('URL redirect tidak valid.');
        }

        header("Location: {$url}");
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}
