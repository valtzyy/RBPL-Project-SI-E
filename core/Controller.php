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

        if (!file_exists($layoutPath)) {
            $layoutPath = ROOT_PATH . '/app/views/layout/main.php';
        }

        if (file_exists($layoutPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean();
            require $layoutPath;
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

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    protected function download(string $content, string $filename, string $contentType): void
    {
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo $content;
    }
}
