<?php

require_once ROOT_PATH . '/core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home', [
            'title' => 'Selamat Datang',
            'message' => 'Siap membara',
        ]);
    }
}
