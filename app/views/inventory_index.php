<!-- Bootstrap 5 CSS for vehicle inventory component -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid py-2">
    <!-- Header Row -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">🚗 Inventaris Kendaraan</h1>
            <p class="text-muted mb-0">Master data kendaraan, stok, dan status ketersediaan di dealer.</p>
        </div>
        <a class="btn btn-primary" href="/inventory/create">➕ Tambah Kendaraan Baru</a>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Filter Card -->
    <form class="card card-body mb-4" method="get" action="/inventory">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label" for="keyword">Pencarian Kata Kunci</label>
                <input class="form-control" id="keyword" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Cari Brand, tipe, atau no rangka...">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="brand">Merek (Brand)</label>
                <select class="form-select" id="brand" name="brand">
                    <option value="">Semua</option>
                    <?php foreach (($options['brands'] ?? []) as $brand): ?>
                        <option value="<?= htmlspecialchars($brand) ?>" <?= ($filters['brand'] ?? '') === $brand ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="type">Tipe</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Semua</option>
                    <?php foreach (($options['types'] ?? []) as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="color">Warna</label>
                <select class="form-select" id="color" name="color">
                    <option value="">Semua</option>
                    <?php foreach (($options['colors'] ?? []) as $color): ?>
                        <option value="<?= htmlspecialchars($color) ?>" <?= ($filters['color'] ?? '') === $color ? 'selected' : '' ?>>
                            <?= htmlspecialchars($color) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="status">Status Unit</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Semua</option>
                    <?php foreach (($options['statuses'] ?? []) as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($status)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-grid align-items-end">
                <button class="btn btn-outline-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <!-- Data Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Merek</th>
                    <th>Tipe</th>
                    <th>Warna</th>
                    <th>Nomor Rangka</th>
                    <th>Nomor Mesin</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Min Stok</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($inventory['data'] ?? []) as $vehicle): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($vehicle['brand']) ?></strong></td>
                        <td><?= htmlspecialchars($vehicle['type']) ?></td>
                        <td><?= htmlspecialchars($vehicle['color']) ?></td>
                        <td><code><?= htmlspecialchars($vehicle['chassis_number']) ?></code></td>
                        <td><code><?= htmlspecialchars($vehicle['engine_number']) ?></code></td>
                        <td class="text-end" style="font-weight: 600;">Rp <?= number_format((float) $vehicle['price'], 0, ',', '.') ?></td>
                        <td class="text-center"><?= (int) $vehicle['stock_quantity'] ?></td>
                        <td class="text-center text-muted"><?= (int) $vehicle['min_stock'] ?></td>
                        <td>
                            <span class="badge text-bg-<?= $vehicle['status'] === 'sold' ? 'secondary' : ($vehicle['status'] === 'held' ? 'warning' : 'success') ?>">
                                <?= htmlspecialchars(ucfirst($vehicle['status'])) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-secondary" href="/inventory/edit/<?= (int) $vehicle['id'] ?>">Edit</a>
                            <form class="d-inline" method="post" action="/inventory/delete/<?= (int) $vehicle['id'] ?>" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini dari inventaris?')">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($inventory['data'])): ?>
                    <tr>
                        <td class="text-center text-muted py-4" colspan="10">Data kendaraan tidak ditemukan atau belum tersedia.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php
    $page = (int) ($inventory['page'] ?? 1);
    $lastPage = (int) ($inventory['last_page'] ?? 1);
    $query = $filters;
    ?>
    <?php if ($lastPage > 1): ?>
        <nav class="mt-4" aria-label="Pagination">
            <ul class="pagination justify-content-end">
                <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                    <?php $query['page'] = $i; ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query($query)) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
