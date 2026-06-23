<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 mb-1 fw-bold text-dark">
                <?= htmlspecialchars($title ?? 'Form Kendaraan') ?>
            </h1>
            <p class="text-muted mb-0">Lengkapi data detail master kendaraan dan stok operasional dealer secara akurat.</p>
        </div>
        <a class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm" href="/inventory">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <form class="card card-body border-0 shadow-sm p-4 text-secondary" method="post" action="<?= htmlspecialchars($action) ?>">
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark" for="brand">Merek (Brand)</label>
                <input class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($vehicle['brand'] ?? '') ?>" placeholder="Contoh: Toyota, Honda" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark" for="type">Tipe Kendaraan</label>
                <input class="form-control" id="type" name="type" value="<?= htmlspecialchars($vehicle['type'] ?? '') ?>" placeholder="Contoh: Avanza G 1.5, Civic RS" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark" for="color">Warna</label>
                <input class="form-control" id="color" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" placeholder="Contoh: Hitam Metalik, Putih Mutiara" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" for="chassis_number">Nomor Rangka</label>
                <input class="form-control" id="chassis_number" name="chassis_number" value="<?= htmlspecialchars($vehicle['chassis_number'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" for="engine_number">Nomor Mesin</label>
                <input class="form-control" id="engine_number" name="engine_number" value="<?= htmlspecialchars($vehicle['engine_number'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" for="price">Harga Penjualan (Rp)</label>
                <input class="form-control" id="price" name="price" type="number" min="0" step="0.01" value="<?= htmlspecialchars((string) ($vehicle['price'] ?? '')) ?>" placeholder="Masukkan nominal harga..." required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" for="min_stock">Minimum Stok Alert</label>
                <input class="form-control" id="min_stock" name="min_stock" type="number" min="0" value="<?= (int) ($vehicle['min_stock'] ?? 0) ?>">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-5 pt-3 border-top">
            <a class="btn btn-light px-4 border" href="/inventory">Batal</a>
            <button class="btn btn-primary px-4 shadow-sm" type="submit">
                <i class="bi bi-check-circle me-1"></i> Simpan Data Kendaraan
            </button>
        </div>
    </form>
</div>