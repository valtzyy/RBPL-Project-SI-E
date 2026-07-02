<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelacakan Status Pengajuan Kredit</title>


<link rel="stylesheet" href="/css/tracking.css">


</head>
<body>

<div class="container">


<!-- Header -->
<a href="/" class="back-link">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M10 3L5 8l5 5"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"/>
    </svg>
    Kembali
</a>

<div class="page-header">
    <h1>Pelacakan Status Pengajuan Kredit</h1>
    <p>
        Cari dan pantau status pengajuan kredit customer
    </p>
</div>

<!-- Search -->
<form
    class="search-bar"
    method="GET"
    action="/credit/tracking"
    id="searchForm">

    <div class="search-wrap">

        <svg class="search-icon"
             width="18"
             height="18"
             viewBox="0 0 18 18"
             fill="none">
            <circle cx="7.5"
                    cy="7.5"
                    r="5.5"
                    stroke="currentColor"
                    stroke-width="1.8"/>
            <path d="M12 12l3.5 3.5"
                  stroke="currentColor"
                  stroke-width="1.8"
                  stroke-linecap="round"/>
        </svg>

        <input
            type="text"
            name="q"
            id="searchInput"
            class="search-input"
            placeholder="Cari nomor pengajuan, customer, leasing..."
            value="<?= htmlspecialchars($keyword ?? '') ?>"
            autocomplete="off">

        <?php if (!empty($keyword)): ?>
            <button
                type="button"
                class="clear-btn"
                id="clearBtn"
                title="Hapus pencarian">
                ✕
            </button>
        <?php endif; ?>

    </div>

    <button
        type="submit"
        class="btn btn-primary">
        Cari
    </button>

    <?php if (!empty($keyword)): ?>
        <a
            href="/credit/tracking"
            class="btn btn-outline">
            Reset
        </a>
    <?php endif; ?>

</form>

<!-- Info -->
<div class="status-bar">

    <?php if (!empty($keyword)): ?>

        <span class="result-info">
            <strong><?= count($applications ?? []) ?></strong>
            hasil untuk
            "<em><?= htmlspecialchars($keyword) ?></em>"
        </span>

    <?php else: ?>

        <span class="result-info">
            Menampilkan
            <strong><?= count($applications ?? []) ?></strong>
            pengajuan kredit
        </span>

    <?php endif; ?>

</div>

<!-- Empty State -->
<?php if (empty($applications)): ?>

    <div class="card">

        <div class="empty-state">

            <h3 class="empty-title">
                Tidak ada data ditemukan
            </h3>

            <p class="empty-desc">
                Tidak ditemukan pengajuan yang sesuai
                dengan kata kunci pencarian.
            </p>

        </div>

    </div>

<?php else: ?>

    <!-- Table -->
    <div class="card">

        <table class="table">

            <thead>
            <tr>
                <th>No Pengajuan</th>
                <th>Customer</th>
                <th>Kendaraan</th>
                <th>Leasing</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($applications as $app):

                $appNo =
                    'CRD-' .
                    str_pad(
                        $app['application_id'],
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                $tgl =
                    date(
                        'd/m/Y',
                        strtotime($app['created_at'])
                    );

                $kendaraan =
                    trim(
                        ($app['brand'] ?? '') .
                        ' ' .
                        ($app['vehicle_type'] ?? '')
                    );

            ?>

            <tr>

                <td>
                    <span class="app-no">
                        <?= $appNo ?>
                    </span>
                </td>

                <td>
                    <?= htmlspecialchars($app['customer_name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($kendaraan) ?>
                </td>

                <td>
                    <?= htmlspecialchars($app['leasing_name']) ?>
                </td>

                <td>

                    <span
                        class="status-badge status-<?= strtolower($app['status']) ?>">

                        <?= strtoupper($app['status']) ?>

                    </span>

                </td>

                <td>
                    <?= $tgl ?>
                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

<?php endif; ?>
```

</div>

<script src="/js/tracking.js"></script>

</body>
</html>
