<main style="max-width: 560px; margin: 32px auto; font-family: Arial, sans-serif;">
    <h1>Tambah Akun</h1>

    <?php if (!empty($error)): ?>
        <div style="padding: 10px; margin-bottom: 16px; background: #fee2e2; color: #991b1b;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/users">
        <?php require ROOT_PATH . '/app/views/admin/users/form.php'; ?>

        <div style="margin-top: 16px;">
            <button type="submit">Simpan</button>
            <a href="/admin/users">Batal</a>
        </div>
    </form>
</main>
