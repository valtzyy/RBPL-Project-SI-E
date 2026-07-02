<?php
// app/views/kasir/nota.php
// PBI-12.4 — Daftar nota servis yang siap dicetak

$title        = $title        ?? 'Nota Servis';
$activePage   = $activePage   ?? 'nota';
$notaList     = $notaList     ?? [];
$pendingCount = $pendingCount ?? 0;

function rupiahN(float $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — DealerLink DMS</title>
    <link rel="stylesheet" href="/css/service-billing.css">
</head>

<body>

    <div class="dl-shell">

        <?php include __DIR__ . '/_sidebar.php'; ?>

        <div class="dl-main">

            <header class="dl-topbar">
                <div>
                    <div class="dl-topbar__title">Nota Servis</div>
                    <div class="dl-topbar__breadcrumb">Kasir Bengkel › Nota</div>
                </div>
                <div class="dl-topbar__right">
                    <span style="font-size:13px;color:var(--clr-text-muted);"><?= date('d M Y') ?></span>
                </div>
            </header>

            <main class="dl-page">

                <div class="dl-page-header">
                    <div>
                        <div class="dl-page-header__title">Nota Servis Resmi</div>
                        <div class="dl-page-header__sub">
                            Daftar transaksi servis yang sudah lunas — klik Cetak untuk membuka nota
                        </div>
                    </div>
                </div>

                <div class="dl-card">

                    <!-- Filter search client-side -->
                    <div class="dl-filter-bar">
                        <div class="dl-search">
                            <svg class="dl-search__icon" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input
                                type="text"
                                class="dl-search__input"
                                placeholder="Cari nama pelanggan atau kendaraan…"
                                oninput="filterNota(this.value)">
                        </div>
                    </div>

                    <?php if (empty($notaList)): ?>
                        <div class="dl-table__empty">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5"
                                style="margin-bottom:10px;color:var(--clr-text-muted)">
                                <polyline points="6 9 6 2 18 2 18 9" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                <rect x="6" y="14" width="12" height="8" />
                            </svg>
                            <div>Belum ada transaksi servis yang lunas.</div>
                        </div>
                    <?php else: ?>
                        <div class="dl-table-wrap">
                            <table class="dl-table" id="nota-table">
                                <thead>
                                    <tr>
                                        <th>No. Nota</th>
                                        <th>Pelanggan</th>
                                        <th>Kendaraan</th>
                                        <th>No. Rangka</th>
                                        <th>Tanggal Masuk</th>
                                        <th class="right">Grand Total</th>
                                        <th class="center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notaList as $n): ?>
                                        <?php
                                        $nomorNota = 'NS-'
                                            . date('Ym', strtotime($n['wo_created_at']))
                                            . '-' . str_pad($n['work_order_id'], 4, '0', STR_PAD_LEFT);
                                        ?>
                                        <tr
                                            data-name="<?= htmlspecialchars(strtolower($n['customer_name'])) ?>"
                                            data-vehicle="<?= htmlspecialchars(strtolower($n['brand'] . ' ' . $n['vehicle_type'])) ?>">
                                            <td style="font-family:monospace;font-weight:600;color:var(--clr-accent-blue);">
                                                <?= htmlspecialchars($nomorNota) ?>
                                            </td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($n['customer_name']) ?></div>
                                                <div class="td-sub"><?= htmlspecialchars($n['customer_phone'] ?? '—') ?></div>
                                            </td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($n['brand'] . ' ' . $n['vehicle_type']) ?></div>
                                                <div class="td-sub"><?= htmlspecialchars($n['color']) ?></div>
                                            </td>
                                            <td style="font-family:monospace;font-size:12.5px;">
                                                <?= htmlspecialchars($n['chassis_number']) ?>
                                            </td>
                                            <td style="color:var(--clr-text-muted);font-size:13px;">
                                                <?= date('d M Y', strtotime($n['wo_created_at'])) ?>
                                            </td>
                                            <td class="right td-total" style="color:var(--clr-accent-teal);">
                                                <?= rupiahN((float)$n['grand_total']) ?>
                                            </td>
                                            <td class="center">
                                                <a
                                                    href="/kasir/nota/cetak/<?= (int)$n['work_order_id'] ?>"
                                                    target="_blank"
                                                    class="dl-btn dl-btn--primary"
                                                    style="font-size:12px;padding:7px 14px;">
                                                    <svg width="12" height="12" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <polyline points="6 9 6 2 18 2 18 9" />
                                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                                        <rect x="6" y="14" width="12" height="8" />
                                                    </svg>
                                                    Cetak
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>
    </div>

    <script>
        function filterNota(keyword) {
            keyword = keyword.toLowerCase().trim();
            document.querySelectorAll('#nota-table tbody tr').forEach(function(row) {
                var name = row.dataset.name || '';
                var vehicle = row.dataset.vehicle || '';
                row.style.display =
                    (!keyword || name.includes(keyword) || vehicle.includes(keyword)) ? '' : 'none';
            });
        }
    </script>

</body>

</html>