<main style="font-family: Arial, sans-serif; padding: 24px;">
    <h1>Manajemen Akun</h1>

    <p>
        <a href="/admin/users/create">Tambah Akun</a>
        |
        <a href="/change-password">Ganti Password</a>
    </p>

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

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role_name']) ?></td>
                    <td><?= htmlspecialchars($user['status']) ?></td>
                    <td>
                        <a href="/admin/users/<?= (int) $user['id'] ?>/edit">Edit</a>

                        <?php if ($user['status'] === 'active'): ?>
                            <form method="POST" action="/admin/users/<?= (int) $user['id'] ?>/deactivate" style="display: inline;">
                                <button type="submit">Nonaktifkan</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
