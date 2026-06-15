<!-- app/views/home.php -->
<main style="max-width: 600px; margin: 64px auto; font-family: Arial, sans-serif;">
    <h1><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
    <p><?= htmlspecialchars($message ?? '') ?></p>

    <hr style="margin: 24px 0;">

    <?php if (Auth::check()): ?>
        <div style="background: #f3f4f6; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <h3>Status Login Aktif</h3>
            <p><strong>Nama:</strong> <?= htmlspecialchars(Auth::user()['name']) ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars(Auth::user()['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars(Auth::user()['email']) ?></p>
            <p><strong>Role:</strong> <span style="background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.9em;"><?= htmlspecialchars(Auth::role()) ?></span></p>
            
            <div style="margin-top: 20px;">
                <?php if (Auth::role() === 'Admin Dealer'): ?>
                    <a href="/admin/users" style="display: inline-block; padding: 8px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 4px; margin-right: 8px; font-size: 0.9em;">Manajemen Akun (Admin)</a>
                <?php endif; ?>
                <a href="/change-password" style="display: inline-block; padding: 8px 16px; background: #4b5563; color: #fff; text-decoration: none; border-radius: 4px; margin-right: 8px; font-size: 0.9em;">Ganti Password</a>
                
                <form method="POST" action="/logout" style="display: inline;">
                    <button type="submit" style="padding: 8px 16px; background: #dc2626; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em;">Logout</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div style="background: #fffbeb; padding: 16px; border-radius: 8px; border: 1px solid #fef3c7; color: #92400e;">
            <p>Anda belum masuk ke sistem.</p>
            <a href="/login" style="display: inline-block; padding: 8px 16px; background: #d97706; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold;">Ke Halaman Login</a>
        </div>
    <?php endif; ?>
</main>