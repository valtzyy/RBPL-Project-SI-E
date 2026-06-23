<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Pengajuan – Upload Dokumen</title>
    <link rel="stylesheet" href="/css/upload-search.css">
</head>
<body>

<div class="container">

    <!-- Header -->
    <a href="/credit/createForm" class="back-link">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali
    </a>

    <div class="page-header">
        <h1>Upload Dokumen Kredit</h1>
        <p>Pilih nomor pengajuan yang akan dilengkapi dokumennya</p>
    </div>

    <!-- Search Bar -->
    <form class="search-bar" method="GET" action="/credit/upload-search" id="searchForm">
        <div class="search-wrap">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="7.5" cy="7.5" r="5.5" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 12l3.5 3.5" stroke="currentColor" stroke-width="1.8"
                      stroke-linecap="round"/>
            </svg>
            <input
                type="text"
                name="q"
                id="searchInput"
                class="search-input"
                placeholder="Cari ID pengajuan, nama customer, nama kendaraan atau leasing..."
                value="<?= htmlspecialchars($keyword) ?>"
                autocomplete="off"
            >
            <?php if ($keyword !== ''): ?>
            <button type="button" class="clear-btn" id="clearBtn" title="Hapus pencarian">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                    <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.8"
                          stroke-linecap="round"/>
                </svg>
            </button>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-prima  ry">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="/credit/upload-search" class="btn btn-outline">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Status Bar -->
    <div class="status-bar">   
        <?php if ($keyword !== ''): ?>
            <span class="result-info">
                <strong><?= count($applications) ?></strong> hasil
                untuk "<em><?= htmlspecialchars($keyword) ?></em>"
            </span>
            <a href="/credit/upload-search" class="reset-link">Tampilkan semua</a>
        <?php else: ?>
            <span class="result-info">
                Menampilkan <strong><?= count($applications) ?></strong> pengajuan
                dengan status <strong>submitted</strong>
            </span>
        <?php endif; ?>
    </div>

    <!-- Tabel / Empty State -->
    <?php if (empty($applications)): ?>

    <div class="card">
        <div class="empty-state">
            <svg class="empty-icon" width="52" height="52" viewBox="0 0 52 52" fill="none">
                <circle cx="26" cy="26" r="25" stroke="currentColor" stroke-width="1.5"
                        stroke-dasharray="4 3"/>
                <circle cx="26" cy="26" r="8" stroke="currentColor" stroke-width="1.5"
                        opacity=".4"/>
                <path d="M26 18v8M26 30v2" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" opacity=".4"/>
            </svg>
            <p class="empty-title">Tidak ada pengajuan ditemukan</p>
            <p class="empty-desc">
                <?php if ($keyword !== ''): ?>
                    Tidak ada yang cocok dengan
                    "<strong><?= htmlspecialchars($keyword) ?></strong>".
                    <br>Coba kata kunci lain atau <a href="/credit/upload">tampilkan semua</a>.
                <?php else: ?>
                    Belum ada pengajuan kredit dengan status <em>submitted</em>.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php else: ?>

    <div class="card mt-20">
        <table class="table" id="appTable">
            <thead>
                <tr>
                    <th>No. Pengajuan</th>
                    <th>Customer</th>
                    <th>Kendaraan</th>
                    <th>Leasing</th>
                    <th class="col-center">Dokumen</th>
                    <th>Tanggal</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app):
                $docCount  = (int) $app['doc_count'];
                $docFull   = $docCount >= 3;
                $appNo     = 'CRD-' . str_pad($app['application_id'], 4, '0', STR_PAD_LEFT);
                $tgl       = date('d/m/Y', strtotime($app['created_at']));
                $kendaraan = htmlspecialchars(trim($app['brand'] . ' ' . $app['vehicle_type']));
            ?>
            <tr data-id="<?= $app['application_id'] ?>">
                <td><span class="app-no"><?= $appNo ?></span></td>
                <td><?= htmlspecialchars($app['customer_name']) ?></td>
                <td><?= $kendaraan ?></td>
                <td><?= htmlspecialchars($app['leasing_name']) ?></td>
                <td class="col-center">
                    <span class="doc-badge <?= $docFull ? 'doc-full' : 'doc-partial' ?>">
                        <?= $docCount ?>/3
                    </span>
                </td>
                <td class="date-text"><?= $tgl ?></td>
                <td class="col-center">
                    <a href="/credit/upload?app=<?= $app['application_id'] ?>"
                       class="btn-upload <?= $docFull ? 'btn-upload-done' : '' ?>">
                        <?= $docFull ? 'Lihat' : 'Upload' ?>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>

</div>

<script src="/js/upload-search.js"></script>
</body>
</html>
