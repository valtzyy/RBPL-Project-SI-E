<!-- Bootstrap 5 CSS for vehicle inventory form component -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid py-2">
    <!-- Header Row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars($title ?? 'Form Kendaraan') ?></h1>
            <p class="text-muted mb-0">Lengkapi master data kendaraan dan batas minimum stok.</p>
        </div>
        <a class="btn btn-outline-secondary" href="/inventory">🔙 Kembali</a>
    </div>

    <!-- Error Notifications -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Form Card -->
    <form class="card card-body" method="post" action="<?= htmlspecialchars($action ?? '') ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="brand">Merek (Brand)</label>
                <input class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($vehicle['brand'] ?? '') ?>" placeholder="Contoh: Toyota, Honda" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="type">Tipe Kendaraan</label>
                <input class="form-control" id="type" name="type" value="<?= htmlspecialchars($vehicle['type'] ?? '') ?>" placeholder="Contoh: Avanza G 1.5, Civic RS" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="color">Warna</label>
                <input class="form-control" id="color" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" placeholder="Contoh: Hitam Metalik, Putih Mutiara" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="chassis_number">Nomor Rangka</label>
                <input class="form-control" id="chassis_number" name="chassis_number" value="<?= htmlspecialchars($vehicle['chassis_number'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="engine_number">Nomor Mesin</label>
                <input class="form-control" id="engine_number" name="engine_number" value="<?= htmlspecialchars($vehicle['engine_number'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="price">Harga Penjualan (Rp)</label>
                <input class="form-control" id="price" name="price" type="number" min="0" step="0.01" value="<?= htmlspecialchars((string) ($vehicle['price'] ?? '')) ?>" placeholder="Masukkan nominal harga..." required>
            </div>

            <div class="col-md-4">
                <label class="form-label" for="min_stock">Minimum Stok Alert</label>
                <input class="form-control" id="min_stock" name="min_stock" type="number" min="0" value="<?= (int) ($vehicle['min_stock'] ?? 0) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status Awal Unit</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (($statuses ?? []) as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($vehicle['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="/inventory">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan Data Kendaraan</button>
        </div>
    </form>
</div>
