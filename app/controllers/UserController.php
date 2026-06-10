<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/app/models/User.php';

class UserController extends Controller {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /** GET /users — tampilkan semua user */
    public function index(): void {
        $users = $this->userModel->all();
        $this->view('users', ['title' => 'Daftar User', 'users' => $users]);
    }

    /** GET /users/:id — tampilkan detail user */
    public function show(string $id): void {
        $user = $this->userModel->find((int) $id);
        if (!$user) {
            die("User tidak ditemukan.");
        }
        $this->view('users', ['title' => 'Detail User', 'users' => [$user]]);
    }

    /** POST /users — simpan user baru */
    public function store(): void {
        $name  = $this->input('name');
        $email = $this->input('email');
        $pass  = password_hash($this->input('password'), PASSWORD_BCRYPT);

        $this->userModel->create([
            'name'     => $name,
            'email'    => $email,
            'password' => $pass,
        ]);

        $this->redirect('/users');
    }

    /** POST /users/:id — update user */
    public function update(string $id): void {
        $this->userModel->update((int) $id, [
            'name'  => $this->input('name'),
            'email' => $this->input('email'),
        ]);

        $this->redirect('/users');
    }

    /** POST /users/delete — hapus user */
    public function destroy(): void {
        $id = (int) $this->input('id');
        $this->userModel->delete($id);
        $this->redirect('/users');
    }
}