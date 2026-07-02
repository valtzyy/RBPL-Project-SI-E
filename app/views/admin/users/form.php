<div class="form-group">
    <label for="name" class="form-label">Nama Lengkap</label>
    <input
        type="text"
        id="name"
        name="name"
        required
        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
        class="form-input"
        placeholder="Masukkan nama lengkap"
    >
</div>

<div class="form-group">
    <label for="username" class="form-label">Username</label>
    <input
        type="text"
        id="username"
        name="username"
        required
        value="<?= htmlspecialchars($user['username'] ?? '') ?>"
        class="form-input"
        placeholder="Masukkan username"
    >
</div>

<div class="form-group">
    <label for="email" class="form-label">Email</label>
    <input
        type="email"
        id="email"
        name="email"
        required
        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
        class="form-input"
        placeholder="nama@dealerlink.com"
    >
</div>
<div class="form-group">
    <label for="password" class="form-label"><?= empty($user) ? 'Password Awal' : 'Ganti Password (Kosongkan jika tidak diubah)' ?></label>
    <input
        type="password"
        id="password"
        name="password"
        <?= empty($user) ? 'required' : '' ?>
        minlength="8"
        class="form-input"
        placeholder="<?= empty($user) ? 'Minimal 8 karakter' : 'Kosongkan jika tidak ingin mengubah' ?>"
    >
</div>

<div class="form-group">
    <label for="role_id" class="form-label">Hak Akses (Role)</label>
    <select id="role_id" name="role_id" required class="form-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 14px center; background-size: 16px; padding-right: 40px;">
        <option value="">Pilih Hak Akses</option>
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

<div class="form-group">
    <label for="status" class="form-label">Status Akun</label>
    <select id="status" name="status" required class="form-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 14px center; background-size: 16px; padding-right: 40px;">
        <option value="active" <?= (($user['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Aktif (Active)</option>
        <option value="inactive" <?= (($user['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Nonaktif (Inactive)</option>
    </select>
</div>
