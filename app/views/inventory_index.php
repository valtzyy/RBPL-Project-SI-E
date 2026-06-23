<!-- Bootstrap 5 CSS for vehicle inventory component -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    /* Custom styles for dropdown with scroll */
    .dropdown-scroll {
        max-height: 150px;
        overflow-y: auto;
    }
    
    /* Table header light style */
    .table thead th {
        background-color: #f8f9fa !important;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Card shadow */
    .card-shadow {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    /* Filter form compact */
    .filter-form .form-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }
    
    .filter-form .form-control-sm,
    .filter-form .form-select-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Action buttons spacing */
    .action-buttons .btn {
        margin: 0 2px;
    }
    
    /* Stock badge styling - tanpa peringatan */
    .stock-badge {
        font-weight: 500;
        padding: 0.35rem 0.75rem;
        background-color: #e9ecef !important;
        color: #495057 !important;
        border: 1px solid #ced4da;
    }
    
    /* Filter bar - semua dalam satu baris */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.5rem;
    }
    
    .filter-item {
        flex: 1 1 auto;
        min-width: 120px;
    }
    
    .filter-item.keyword {
        flex: 2 1 200px;
    }
    
    .filter-actions {
        flex: 0 0 auto;
        display: flex;
        gap: 0.25rem;
        padding-bottom: 2px;
    }
    
    @media (max-width: 768px) {
        .filter-item {
            flex: 1 1 100%;
            min-width: 100%;
        }
        .filter-item.keyword {
            flex: 1 1 100%;
        }
        .filter-actions {
            flex: 1 1 100%;
            justify-content: flex-start;
        }
    }
</style>

<div class="container-fluid py-3">
    <!-- Header Row -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">Inventaris Kendaraan</h1>
            <p class="text-muted mb-0 small">Master data kendaraan, stok, dan status ketersediaan di dealer</p>
        </div>
        <a class="btn btn-primary btn-sm px-4" href="/inventory/create">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kendaraan
        </a>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <form class="card card-body mb-4 bg-light border-0 card-shadow filter-form" method="get" action="/inventory">
        <div class="filter-bar">
            <!-- Keyword Search -->
            <div class="filter-item keyword">
                <label class="form-label" for="keyword">Cari</label>
                <input class="form-control form-control-sm" id="keyword" name="keyword" 
                       value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                       placeholder="Brand, tipe, rangka...">
            </div>
            
            <!-- Brand -->
            <div class="filter-item">
                <label class="form-label" for="brand">Merek</label>
                <select class="form-select form-select-sm dropdown-scroll" id="brand" name="brand">
                    <option value="">Semua Merek</option>
                    <?php foreach (($options['brands'] ?? []) as $brand): ?>
                        <option value="<?= htmlspecialchars($brand) ?>" <?= ($filters['brand'] ?? '') === $brand ? 'selected' : '' ?>>
                            <?= htmlspecialchars($brand) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Type -->
            <div class="filter-item">
                <label class="form-label" for="type">Tipe</label>
                <select class="form-select form-select-sm dropdown-scroll" id="type" name="type">
                    <option value="">Semua Tipe</option>
                    <?php foreach (($options['types'] ?? []) as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Color -->
            <div class="filter-item">
                <label class="form-label" for="color">Warna</label>
                <select class="form-select form-select-sm dropdown-scroll" id="color" name="color">
                    <option value="">Semua Warna</option>
                    <?php foreach (($options['colors'] ?? []) as $color): ?>
                        <option value="<?= htmlspecialchars($color) ?>" <?= ($filters['color'] ?? '') === $color ? 'selected' : '' ?>>
                            <?= htmlspecialchars($color) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="filter-actions">
                <button class="btn btn-primary btn-sm px-3" type="submit">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="/inventory" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0 card-shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="py-2 px-3">Merek</th>
                            <th class="py-2 px-3">Tipe</th>
                            <th class="py-2 px-3">Warna</th>
                            <th class="py-2 px-3">Nomor Rangka</th>
                            <th class="py-2 px-3">Nomor Mesin</th>
                            <th class="py-2 px-3 text-center">Harga</th>
                            <th class="py-2 px-3 text-center">Stok</th>
                            <th class="py-2 px-3 text-center">Min Stok</th>
                            <th class="py-2 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($inventory['data'] ?? []) as $vehicle): ?>
                            <tr>
                                <td class="py-2 px-3 fw-semibold"><?= htmlspecialchars($vehicle['brand']) ?></td>
                                <td class="py-2 px-3"><?= htmlspecialchars($vehicle['type']) ?></td>
                                <td class="py-2 px-3">
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($vehicle['color']) ?>
                                    </span>
                                </td>
                                <td class="py-2 px-3">
                                    <code class="small bg-light p-1 rounded"><?= htmlspecialchars($vehicle['chassis_number']) ?></code>
                                </td>
                                <td class="py-2 px-3">
                                    <code class="small bg-light p-1 rounded"><?= htmlspecialchars($vehicle['engine_number']) ?></code>
                                </td>
                                <td class="py-2 px-3 text-center fw-semibold">
                                    Rp <?= number_format((float) $vehicle['price'], 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge stock-badge rounded-pill">
                                        <?= (int) $vehicle['stock_quantity'] ?>
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center text-muted small"><?= (int) $vehicle['min_stock'] ?></td>
                                <td class="py-2 px-3 text-center">
                                    <div class="action-buttons d-flex justify-content-center" role="group">
                                        <a class="btn btn-outline-primary btn-sm" href="/inventory/edit/<?= (int) $vehicle['id'] ?>" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form class="d-inline" method="post" action="/inventory/delete/<?= (int) $vehicle['id'] ?>" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kendaraan ini?')">
                                            <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($inventory['data'])): ?>
                            <tr>
                                <td class="text-center text-muted py-5" colspan="9">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <p class="mb-0">Data kendaraan tidak ditemukan</p>
                                    <small>Silakan tambahkan kendaraan baru atau ubah filter pencarian</small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
            <ul class="pagination justify-content-end gap-1">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query(array_merge($query, ['page' => $page - 1]))) ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start = max(1, $page - 2);
                $end = min($lastPage, $page + 2);
                if ($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query(array_merge($query, ['page' => 1]))) ?>">1</a></li>
                    <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query(array_merge($query, ['page' => $i]))) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($end < $lastPage): ?>
                    <?php if ($end < $lastPage - 1): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php endif; ?>
                    <li class="page-item"><a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query(array_merge($query, ['page' => $lastPage]))) ?>"><?= $lastPage ?></a></li>
                <?php endif; ?>
                <li class="page-item <?= $page >= $lastPage ? 'disabled' : '' ?>">
                    <a class="page-link" href="/inventory?<?= htmlspecialchars(http_build_query(array_merge($query, ['page' => $page + 1]))) ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Bootstrap 5 JS for alert dismiss -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>