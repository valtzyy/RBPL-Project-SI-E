<main style="max-width: 480px; margin: 32px auto; font-family: Arial, sans-serif;">
    <h1>Ganti Password</h1>

    <?php if (!empty($success)): ?>
        <div style="padding: 10px; margin-bottom: 16px; background: #dcfce7; color: #166534;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="padding: 10px; margin-bottom: 16px; background: #fee2e2; color: #991b1b;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/change-password">
        <div style="margin-bottom: 12px;">
            <label for="current_password">Password Lama</label><br>
            <input type="password" id="current_password" name="current_password" required style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 12px;">
            <label for="new_password">Password Baru</label><br>
            <input type="password" id="new_password" name="new_password" required minlength="8" style="width: 100%; padding: 8px;">
        </div>

        <div style="margin-bottom: 16px;">
            <label for="confirm_password">Konfirmasi Password Baru</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" style="width: 100%; padding: 8px;">
        </div>

        <button type="submit">Perbarui Password</button>
        <a href="/">Kembali</a>
    </form>
</main>
