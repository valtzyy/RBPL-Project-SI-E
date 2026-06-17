<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Form Kendaraan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= htmlspecialchars($title ?? 'Form Kendaraan') ?></h1>
            <p class="text-muted mb-0">Lengkapi master data kendaraan dan stok awal.</p>
        </div>
        <a class="btn btn-outline-secondary" href="/inventory">Kembali</a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form class="card card-body" method="post" action="<?= htmlspecialchars($action) ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="brand">Brand</label>
                <input class="form-control" id="brand" name="brand" value="<?= htmlspecialchars($vehicle['brand'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="type">Type</label>
                <input class="form-control" id="type" name="type" value="<?= htmlspecialchars($vehicle['type'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="color">Color</label>
                <input class="form-control" id="color" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" required>
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
                <label class="form-label" for="price">Harga</label>
                <input class="form-control" id="price" name="price" type="number" min="0" step="0.01" value="<?= htmlspecialchars((string) ($vehicle['price'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="quantity">Quantity Stok</label>
                <input class="form-control" id="quantity" name="quantity" type="number" min="0" value="<?= (int) ($vehicle['stock_quantity'] ?? 0) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="min_stock">Minimum Stok</label>
                <input class="form-control" id="min_stock" name="min_stock" type="number" min="0" value="<?= (int) ($vehicle['min_stock'] ?? 0) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <?php foreach (($statuses ?? []) as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($vehicle['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a class="btn btn-outline-secondary" href="/inventory">Batal</a>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
    </form>
</main>
</body>
</html>
