<?php

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/app/models/User.php';

class ProfileController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        Auth::requireLogin();
        $this->userModel = new User();
    }

    public function editPassword(): void
    {
        $this->view('profile/change-password', [
            'title' => 'Ganti Password',
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);

        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function updatePassword(): void
    {
        $currentPassword = (string) $this->input('current_password', '');
        $newPassword = (string) $this->input('new_password', '');
        $confirmPassword = (string) $this->input('confirm_password', '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['flash_error'] = 'Semua field password wajib diisi.';
            $this->redirect('/change-password');
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['flash_error'] = 'Password baru minimal 8 karakter.';
            $this->redirect('/change-password');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_error'] = 'Konfirmasi password baru tidak cocok.';
            $this->redirect('/change-password');
        }

        $user = $this->userModel->findForLogin(Auth::user()['email']);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $_SESSION['flash_error'] = 'Password lama salah.';
            $this->redirect('/change-password');
        }

        $this->userModel->updatePassword(Auth::id(), $newPassword);
        $_SESSION['flash_success'] = 'Password berhasil diperbarui.';
        $this->redirect('/change-password');
    }
}
