<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/models/User.php';

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'error' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_error']);
    }

    public function login(): void
    {
        $mockRole = $this->input('mock_role');

        if ($mockRole !== null && $mockRole !== '') {
            $roleNames = [
                'manager' => 'Manager',
                'admin' => 'Admin',
                'sales' => 'Sales',
                'finance' => 'Finance',
                'service advisor' => 'Service Advisor',
                'mekanik' => 'Mekanik'
            ];

            if (array_key_exists($mockRole, $roleNames)) {
                $roleName = $roleNames[$mockRole];
                $mockUser = [
                    'id' => 999,
                    'name' => 'Mock ' . $roleName,
                    'username' => strtolower(str_replace(' ', '_', $roleName)),
                    'email' => strtolower(str_replace(' ', '_', $roleName)) . '@dealerlink.com',
                    'role_id' => 99,
                    'role_name' => $roleName
                ];
                Auth::login($mockUser);
                $this->redirect('/');
            }
        }

        $identity = trim((string) $this->input('identity', ''));
        $password = (string) $this->input('password', '');

        if ($identity === '' || $password === '') {
            $_SESSION['flash_error'] = 'Username/email dan password wajib diisi.';
            $this->redirect('/login');
        }

        $user = $this->userModel->findForLogin($identity);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Username/email atau password salah.';
            $this->redirect('/login');
        }

        if ($user['status'] !== 'active') {
            $_SESSION['flash_error'] = 'Akun tidak aktif. Hubungi Sales.';
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}
