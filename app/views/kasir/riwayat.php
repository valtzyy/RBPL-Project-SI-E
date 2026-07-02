<?php
// app/views/kasir/riwayat.php
// PBI-12.6 — Halaman pencarian riwayat service by chassis/engine/nama pelanggan
// PBI-12.7 — Modal historical logs per WO (data dari endpoint JSON)

$title      = $title      ?? 'Riwayat Servis';
$activePage = $activePage ?? 'riwayat';
$hasil      = $hasil      ?? [];
$keyword    = $keyword    ?? '';
$pendingCount = $pendingCount ?? 0;

function rupiahR(float $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}

$statusLabel = [
    'in_progress' => ['label' => 'Dikerjakan', 'class' => 'dl-badge--amber'],
    'ready'       => ['label' => 'Menunggu Bayar', 'class' => 'dl-badge--ready'],
    'done'        => ['label' => 'Lunas', 'class' => 'dl-badge--done'],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — DealerLink DMS</title>
    <link rel="stylesheet" href="/css/service-billing.css">
    <style>
        /* Badge amber untuk status in_progress */
        .dl-badge--amber {
            background: #fffbeb;
            color: #b45309;
        }

        /* Search bar halaman riwayat */
        .dl-search-section {
            background: var(--clr-surface);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-card);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-card);
        }

        .dl-search-section__title {
            font-size: 14px;
            font-weight: 600;
            color: var(--clr-text-secondary);
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .dl-search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .dl-search-form .dl-search {
            flex: 1;
            max-width: 100%;
        }

        .dl-search-form .dl-search__input {
            background: var(--clr-bg);
            font-size: 14px;
            padding: 10px 12px 10px 36px;
        }

        /* Timeline logs dalam modal */
        .dl-timeline {
            position: relative;
            padding-left: 20px;
            margin: 4px 0 16px;
        }

        .dl-timeline::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: var(--clr-border);
        }

        .dl-timeline-item {
            position: relative;
            margin-bottom: 16px;
            padding-left: 16px;
        }

        .dl-timeline-item::before {
            content: '';
            position: absolute;
            left: -14px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--clr-accent-teal);
            border: 2px solid var(--clr-surface);
            box-shadow: 0 0 0 2px var(--clr-accent-teal);
        }

        .dl-timeline-item:last-child::before {
            background: var(--clr-accent-blue);
            box-shadow: 0 0 0 2px var(--clr-accent-blue);
        }

        .dl-timeline-item__time {
            font-size: 11px;
            color: var(--clr-text-muted);
            margin-bottom: 3px;
        }

        .dl-timeline-item__status {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--clr-text-primary);
        }

        .dl-timeline-item__notes {
            font-size: 12.5px;
            color: var(--clr-text-secondary);
            margin-top: 3px;
        }

        .dl-timeline-empty {
            font-size: 13px;
            color: var(--clr-text-muted);
            padding: 12px 0;
            font-style: italic;
        }

        /* Hasil pencarian count */
        .dl-result-meta {
            font-size: 13px;
            color: var(--clr-text-muted);
            margin-bottom: 14px;
        }

        .dl-result-meta strong {
            color: var(--clr-text-primary);
        }
    </style>
</head>

<body>

    <div class="dl-shell">

        <?php include __DIR__ . '/_sidebar.php'; ?>

        <div class="dl-main">

            <header class="dl-topbar">
                <div>
                    <div class="dl-topbar__title">Riwayat Servis</div>
                    <div class="dl-topbar__breadcrumb">Kasir Bengkel › Riwayat</div>
                </div>
                <div class="dl-topbar__right">
                    <span style="font-size:13px;color:var(--clr-text-muted);"><?= date('d M Y') ?></span>
                </div>
            </header>

            <main class="dl-page">

                <div class="dl-page-header">
                    <div>
                        <div class="dl-page-header__title">Riwayat Servis Kendaraan</div>
                        <div class="dl-page-header__sub">
                            Cari berdasarkan nomor rangka, nomor mesin, atau nama pelanggan
                        </div>
                    </div>
                </div>

                <!-- Form pencarian -->
                <div class="dl-search-section">
                    <div class="dl-search-section__title">Pencarian</div>
                    <form method="GET" action="/kasir/riwayat" class="dl-search-form">
                        <div class="dl-search">
                            <svg class="dl-search__icon" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input
                                type="text"
                                name="q"
                                class="dl-search__input"
                                placeholder="Contoh: MHF123ABC, Toyota Avanza, atau nama pelanggan…"
                                value="<?= htmlspecialchars($keyword) ?>"
                                autofocus>
                        </div>
                        <button type="submit" class="dl-btn dl-btn--primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            Cari
                        </button>
                        <?php if ($keyword): ?>
                            <a href="/kasir/riwayat" class="dl-btn dl-btn--ghost">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Hasil pencarian -->
                <?php if ($keyword !== ''): ?>

                    <div class="dl-result-meta">
                        Menampilkan <strong><?= count($hasil) ?></strong> hasil untuk
                        "<strong><?= htmlspecialchars($keyword) ?></strong>"
                    </div>

                    <div class="dl-card">
                        <?php if (empty($hasil)): ?>
                            <div class="dl-table__empty">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.5"
                                    style="margin-bottom:10px;color:var(--clr-text-muted)">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                </svg>
                                <div>Tidak ditemukan riwayat servis untuk kata kunci tersebut.</div>
                                <div style="margin-top:6px;font-size:12.5px;">
                                    Coba gunakan nomor rangka lengkap atau nama pelanggan.
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="dl-table-wrap">
                                <table class="dl-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kendaraan</th>
                                            <th>No. Rangka</th>
                                            <th>Pelanggan</th>
                                            <th>Tanggal Masuk</th>
                                            <th class="right">Grand Total</th>
                                            <th class="center">Status</th>
                                            <th class="center">Log</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($hasil as $i => $r): ?>
                                            <?php
                                            $st = $statusLabel[$r['wo_status']]
                                                ?? ['label' => $r['wo_status'], 'class' => ''];
                                            ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td>
                                                    <div class="td-name">
                                                        <?= htmlspecialchars($r['brand'] . ' ' . $r['vehicle_type']) ?>
                                                    </div>
                                                    <div class="td-sub">
                                                        <?= htmlspecialchars($r['color']) ?>
                                                    </div>
                                                </td>
                                                <td style="font-family:monospace;font-size:12.5px;">
                                                    <?= htmlspecialchars($r['chassis_number']) ?>
                                                </td>
                                                <td>
                                                    <div class="td-name">
                                                        <?= htmlspecialchars($r['customer_name']) ?>
                                                    </div>
                                                    <div class="td-sub">
                                                        <?= htmlspecialchars($r['customer_phone'] ?? '-') ?>
                                                    </div>
                                                </td>
                                                <td style="color:var(--clr-text-muted);font-size:13px;">
                                                    <?= date('d M Y', strtotime($r['wo_created_at'])) ?>
                                                </td>
                                                <td class="right td-total">
                                                    <?= rupiahR((float)$r['grand_total']) ?>
                                                </td>
                                                <td class="center">
                                                    <span class="dl-badge <?= $st['class'] ?>">
                                                        <?= $st['label'] ?>
                                                    </span>
                                                </td>
                                                <td class="center">
                                                    <button
                                                        class="dl-btn dl-btn--detail"
                                                        style="font-size:12px;padding:6px 12px;"
                                                        onclick="bukaPanelLog(<?= (int)$r['work_order_id'] ?>)">
                                                        Lihat Log
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($keyword === ''): ?>
                    <!-- State awal — belum ada pencarian -->
                    <div class="dl-card" style="padding:48px 20px;text-align:center;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.2"
                            style="color:var(--clr-text-muted);margin-bottom:14px;">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <div style="font-size:15px;font-weight:600;color:var(--clr-text-primary);
                                margin-bottom:6px;">
                            Masukkan kata kunci pencarian
                        </div>
                        <div style="font-size:13px;color:var(--clr-text-muted);">
                            Gunakan nomor rangka, nomor mesin, atau nama pelanggan<br>
                            untuk mencari riwayat servis kendaraan.
                        </div>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>


    <!-- ===== MODAL HISTORICAL LOGS (PBI-12.7) ===== -->
    <div class="dl-modal-overlay" id="modalLogOverlay" onclick="tutupModalLogJikaOverlay(event)">
        <div class="dl-modal" role="dialog" aria-modal="true">

            <div class="dl-modal__head">
                <div>
                    <div class="dl-modal__title">Log Pekerjaan Mekanik</div>
                    <div class="dl-modal__subtitle" id="modalLogSubtitle">—</div>
                </div>
                <button class="dl-modal__close" onclick="tutupModalLog()">&times;</button>
            </div>

            <div class="dl-modal__body" id="modalLogBody">
                <p style="text-align:center;color:var(--clr-text-muted);padding:32px 0;">
                    Memuat…
                </p>
            </div>

            <div class="dl-modal__foot">
                <button class="dl-btn dl-btn--ghost" onclick="tutupModalLog()">Tutup</button>
                <a id="btnLogCetakNota" href="#" class="dl-btn dl-btn--primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 6 2 18 2 18 9" />
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                        <rect x="6" y="14" width="12" height="8" />
                    </svg>
                    Cetak Nota
                </a>
            </div>

        </div>
    </div>


    <script>
        /* =============================================
   riwayat.php — JS untuk modal historical logs
   PBI-12.7: GET /kasir/riwayat/logs/:id
   ============================================= */

        function bukaPanelLog(workOrderId) {
            const overlay = document.getElementById('modalLogOverlay');
            const body = document.getElementById('modalLogBody');
            const subtitle = document.getElementById('modalLogSubtitle');

            body.innerHTML = '<p style="text-align:center;color:var(--clr-text-muted);padding:40px 0;">Memuat log…</p>';
            subtitle.textContent = '—';
            overlay.classList.add('open');

            fetch('/kasir/riwayat/logs/' + workOrderId)
                .then(function(res) {
                    if (!res.ok) throw new Error('Gagal memuat log.');
                    return res.json();
                })
                .then(function(d) {
                    renderModalLog(d);
                })
                .catch(function(err) {
                    body.innerHTML = '<p style="color:#e53e3e;text-align:center;padding:40px 0;">' +
                        esc(err.message) + '</p>';
                });
        }

        function renderModalLog(d) {
            document.getElementById('modalLogSubtitle').textContent =
                d.brand + ' ' + d.vehicle_type + ' — ' + d.customer_name;

            document.getElementById('btnLogCetakNota').href =
                '/kasir/nota/cetak/' + d.work_order_id;

            function rp(n) {
                return 'Rp ' + Number(n).toLocaleString('id-ID');
            }

            // Timeline logs
            let timelineHtml = '';
            if (d.logs && d.logs.length > 0) {
                timelineHtml = '<div class="dl-timeline">';
                d.logs.forEach(function(log) {
                    timelineHtml +=
                        '<div class="dl-timeline-item">' +
                        '<div class="dl-timeline-item__time">' +
                        new Date(log.created_at).toLocaleString('id-ID') +
                        '</div>' +
                        '<div class="dl-timeline-item__status">' + esc(log.status || '—') + '</div>' +
                        (log.notes ?
                            '<div class="dl-timeline-item__notes">' + esc(log.notes) + '</div>' :
                            '') +
                        '</div>';
                });
                timelineHtml += '</div>';
            } else {
                timelineHtml = '<div class="dl-timeline-empty">Belum ada log dicatat oleh mekanik.</div>';
            }

            // Sparepart rows
            let partRows = '';
            if (d.spareparts && d.spareparts.length > 0) {
                d.spareparts.forEach(function(p, i) {
                    partRows +=
                        '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + esc(p.nama_sparepart) + '</td>' +
                        '<td style="color:var(--clr-text-muted);font-size:12px;">' + esc(p.sku || '—') + '</td>' +
                        '<td class="right">' + rp(p.harga_satuan) + '</td>' +
                        '<td class="center">' + p.quantity + '</td>' +
                        '<td class="right"><strong>' + rp(p.subtotal) + '</strong></td>' +
                        '</tr>';
                });
            } else {
                partRows = '<tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted);' +
                    'padding:14px 0;">Tidak ada sparepart tercatat.</td></tr>';
            }

            document.getElementById('modalLogBody').innerHTML =

                // Info kendaraan & pelanggan
                '<div class="dl-info-grid">' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Pelanggan</div>' +
                '<div class="dl-info-item__value">' + esc(d.customer_name) + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Telepon</div>' +
                '<div class="dl-info-item__value">' + esc(d.customer_phone || '—') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Kendaraan</div>' +
                '<div class="dl-info-item__value">' + esc(d.brand + ' ' + d.vehicle_type) + ' — ' + esc(d.color) + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">No. Rangka</div>' +
                '<div class="dl-info-item__value" style="font-family:monospace;font-size:12.5px;">' +
                esc(d.chassis_number) + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Mekanik</div>' +
                '<div class="dl-info-item__value">' + esc(d.mechanic_name || '—') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Tanggal Booking</div>' +
                '<div class="dl-info-item__value">' + esc(d.booking_date || '—') + '</div></div>' +
                '</div>'

                // Timeline
                +
                '<div class="dl-section-label">Kronologi Pekerjaan Mekanik</div>' +
                timelineHtml

                // Sparepart
                +
                '<div class="dl-section-label">Komponen Terpakai</div>' +
                '<table class="dl-part-table">' +
                '<thead><tr>' +
                '<th>#</th><th>Nama</th><th>SKU</th>' +
                '<th class="right">Harga</th><th class="center">Qty</th><th class="right">Subtotal</th>' +
                '</tr></thead>' +
                '<tbody>' + partRows + '</tbody>' +
                '</table>'

                // Total
                +
                '<div class="dl-bill-summary">' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__label">Total Komponen</span>' +
                '<span class="dl-bill-summary__value">' + rp(d.total_komponen) + '</span>' +
                '</div>' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__label">Biaya Jasa (' + d.logs.length + ' log)</span>' +
                '<span class="dl-bill-summary__value">' + rp(d.biaya_jasa) + '</span>' +
                '</div>' +
                '<hr class="dl-bill-summary__divider">' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__total-label">GRAND TOTAL</span>' +
                '<span class="dl-bill-summary__total-value">' + rp(d.grand_total) + '</span>' +
                '</div>' +
                '</div>';
        }

        function esc(str) {
            if (str == null) return '—';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function tutupModalLog() {
            document.getElementById('modalLogOverlay').classList.remove('open');
        }

        function tutupModalLogJikaOverlay(e) {
            if (e.target === document.getElementById('modalLogOverlay')) tutupModalLog();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tutupModalLog();
        });
    </script>

</body>

</html>