<h1><?= htmlspecialchars($title) ?></h1>

<!-- Form Tambah User -->
<form method="POST" action="/users">
    <input type="text"  name="name"     placeholder="Nama"     required>
    <input type="email" name="email"    placeholder="Email"    required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Tambah User</button>
</form>

<hr>

<!-- Tabel Daftar User -->
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['created_at'] ?></td>
            <td>
                <!-- Hapus -->
                <form method="POST" action="/users/delete" style="display:inline">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <button type="submit" onclick="return confirm('Hapus user ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>