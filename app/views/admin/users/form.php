<div style="margin-bottom: 12px;">
    <label for="name">Nama</label><br>
    <input
        type="text"
        id="name"
        name="name"
        required
        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
        style="width: 100%; padding: 8px;"
    >
</div>

<div style="margin-bottom: 12px;">
    <label for="username">Username</label><br>
    <input
        type="text"
        id="username"
        name="username"
        required
        value="<?= htmlspecialchars($user['username'] ?? '') ?>"
        style="width: 100%; padding: 8px;"
    >
</div>

<div style="margin-bottom: 12px;">
    <label for="email">Email</label><br>
    <input
        type="email"
        id="email"
        name="email"
        required
        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
        style="width: 100%; padding: 8px;"
    >
</div>

<div style="margin-bottom: 12px;">
    <label for="password"><?= empty($user) ? 'Password Awal' : 'Ganti Password (Kosongkan jika tidak diubah)' ?></label><br>
    <input
        type="password"
        id="password"
        name="password"
        <?= empty($user) ? 'required' : '' ?>
        minlength="8"
        style="width: 100%; padding: 8px;"
    >
</div>

<div style="margin-bottom: 12px;">
    <label for="role_id">Role</label><br>
    <select id="role_id" name="role_id" required style="width: 100%; padding: 8px;">
        <option value="">Pilih Role</option>
        <?php foreach ($roles as $role): ?>
            <option
                value="<?= (int) $role['id'] ?>"
                <?= ((int) ($user['role_id'] ?? 0) === (int) $role['id']) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($role['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div style="margin-bottom: 12px;">
    <label for="status">Status</label><br>
    <select id="status" name="status" required style="width: 100%; padding: 8px;">
        <option value="active" <?= (($user['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= (($user['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
    </select>
</div>
