<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Role.php';

class UserManagementController extends Controller
{
    private User $userModel;
    private Role $roleModel;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'Admin Dealer']);

        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    public function index(): void
    {
        $this->view('admin/users/index', [
            'title' => 'Manajemen Akun',
            'users' => $this->userModel->allWithRoles(),
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function create(): void
    {
        $this->view('admin/users/create', [
            'title' => 'Tambah Akun',
            'roles' => $this->roleModel->all(),
            'error' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_error']);
    }

    public function store(): void
    {
        $data = $this->validatedInput(true);

        if ($data === null) {
            $this->redirect('/admin/users/create');
        }

        try {
            $this->userModel->createAccount($data);
            $_SESSION['flash_success'] = 'Akun berhasil ditambahkan.';
            $this->redirect('/admin/users');
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Username atau email sudah digunakan.';
            $this->redirect('/admin/users/create');
        }
    }

    public function edit(string $id): void
    {
        $user = $this->userModel->findWithRole((int) $id);

        if (!$user) {
            http_response_code(404);
            exit('User tidak ditemukan.');
        }

        $this->view('admin/users/edit', [
            'title' => 'Edit Akun',
            'user' => $user,
            'roles' => $this->roleModel->all(),
            'error' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_error']);
    }

    public function update(string $id): void
    {
        $data = $this->validatedInput(false);

        if ($data === null) {
            $this->redirect("/admin/users/{$id}/edit");
        }

        try {
            $this->userModel->updateAccount((int) $id, $data);
            if (isset($data['password'])) {
                $this->userModel->updatePassword((int) $id, $data['password']);
            }
            $_SESSION['flash_success'] = 'Akun berhasil diperbarui.';
            $this->redirect('/admin/users');
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Username atau email sudah digunakan.';
            $this->redirect("/admin/users/{$id}/edit");
        }
    }

    public function deactivate(string $id): void
    {
        if ((int) $id === Auth::id()) {
            $_SESSION['flash_error'] = 'Admin tidak boleh menonaktifkan akunnya sendiri.';
            $this->redirect('/admin/users');
        }

        $this->userModel->deactivate((int) $id);
        $_SESSION['flash_success'] = 'Akun berhasil dinonaktifkan.';
        $this->redirect('/admin/users');
    }

    private function validatedInput(bool $isCreate): ?array
    {
        $name = trim((string) $this->input('name', ''));
        $username = trim((string) $this->input('username', ''));
        $email = trim((string) $this->input('email', ''));
        $roleId = (int) $this->input('role_id', 0);
        $status = (string) $this->input('status', 'active');
        $password = (string) $this->input('password', '');

        if ($name === '' || $username === '' || $email === '' || $roleId <= 0) {
            $_SESSION['flash_error'] = 'Nama, username, email, dan role wajib diisi.';
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Format email tidak valid.';
            return null;
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $_SESSION['flash_error'] = 'Status akun tidak valid.';
            return null;
        }

        if ($isCreate && strlen($password) < 8) {
            $_SESSION['flash_error'] = 'Password minimal 8 karakter.';
            return null;
        }

        if (!$isCreate && $password !== '') {
            if (strlen($password) < 8) {
                $_SESSION['flash_error'] = 'Password baru minimal 8 karakter.';
                return null;
            }
        }

        $data = [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'role_id' => $roleId,
            'status' => $status,
        ];

        if ($isCreate) {
            $data['password'] = $password;
        } elseif ($password !== '') {
            $data['password'] = $password;
        }

        return $data;
    }
}
