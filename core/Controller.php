<?php

class Controller {

    /**
     * Render view dengan data
     * Contoh: $this->view('users', ['users' => $data])
     */
    protected function view(string $name, array $data = []): void {
        // Ekstrak array menjadi variabel (misal $data['users'] → $users)
        extract($data);

        $viewPath = __DIR__ . "/../app/views/{$name}.php";

        if (!file_exists($viewPath)) {
            die("❌ View tidak ditemukan: {$name}.php");
        }

        // Gunakan layout jika ada
        $layoutPath = __DIR__ . '/../app/views/layouts/main.php';
        if (file_exists($layoutPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean(); // Tangkap output view
            require $layoutPath;      // Layout akan pakai $content
        } else {
            require $viewPath;
        }
    }

    /** Redirect ke URL lain */
    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    /** Ambil data POST */
    protected function input(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}