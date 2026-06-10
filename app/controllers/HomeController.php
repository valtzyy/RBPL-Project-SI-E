<?php
// app/controllers/HomeController.php

require_once ROOT_PATH . '/core/Controller.php';

class HomeController extends Controller {

    public function index(): void {
        $this->view('home', [
            'title'   => 'Selamat Datang',
            'message' => 'Aplikasi PHP Native siap digunakan!',
        ]);
    }
}