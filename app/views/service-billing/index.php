<?php
// app/views/service-billing/index.php
// Sprint 12 — PBI-12.1: Antarmuka Daftar Tagihan Kasir Bengkel

$title = $title ?? 'Tagihan Kasir Bengkel';

/** Helper: format Rupiah */
function rupiah(float $n): string
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

        <!-- ============ SIDEBAR ============ -->
        <?php
        $activePage   = 'tagihan';
        $pendingCount = count(array_filter($tagihan ?? [], fn($t) => $t['wo_status'] === 'ready'));
        include __DIR__ . '/../kasir/_sidebar.php';
        ?>

        <!-- ============ KONTEN UTAMA ============ -->
        <div class="dl-main">

            <!-- Topbar -->
            <header class="dl-topbar">
                <div>
                    <div class="dl-topbar__title">Kasir Bengkel</div>
                    <div class="dl-topbar__breadcrumb">Workshop › Tagihan Servis</div>
                </div>
                <div class="dl-topbar__right">
                    <span style="font-size:13px;color:var(--clr-text-muted);"><?= date('d M Y') ?></span>
                </div>
            </header>

            <!-- Page body -->
            <main class="dl-page">

                <!-- Header halaman -->
                <div class="dl-page-header">
                    <div>
                        <div class="dl-page-header__title">Daftar Tagihan Kasir Bengkel</div>
                        <div class="dl-page-header__sub">Kalkulasi otomatis biaya jasa servis dan komponen sparepart</div>
                    </div>
                </div>

                <!-- Kartu tabel -->
                <div class="dl-card">

                    <!-- Filter bar -->
                    <div class="dl-filter-bar">
                        <div class="dl-search">
                            <svg class="dl-search__icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input
                                type="text"
                                id="searchInput"
                                class="dl-search__input"
                                placeholder="Cari nama pelanggan atau kendaraan…"
                                oninput="filterTagihan()">
                        </div>
                        <select class="dl-select" id="statusFilter" onchange="filterTagihan()">
                            <option value="">Semua Status</option>
                            <option value="ready">Menunggu Bayar</option>
                            <option value="done">Lunas</option>
                        </select>
                    </div>

                    <!-- Tabel -->
                    <div class="dl-table-wrap">
                        <table class="dl-table" id="tagihan-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pelanggan</th>
                                    <th>Kendaraan</th>
                                    <th>Tanggal Booking</th>
                                    <th class="right">Biaya Jasa</th>
                                    <th class="right">Total Komponen</th>
                                    <th class="right">Grand Total</th>
                                    <th class="center">Status</th>
                                    <th class="center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tagihan)): ?>
                                    <tr>
                                        <td colspan="9" class="dl-table__empty">
                                            Belum ada tagihan yang siap diproses.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tagihan as $i => $t): ?>
                                        <tr
                                            data-name="<?= htmlspecialchars(strtolower($t['customer_name'])) ?>"
                                            data-vehicle="<?= htmlspecialchars(strtolower($t['brand'] . ' ' . $t['vehicle_type'])) ?>"
                                            data-status="<?= htmlspecialchars($t['wo_status']) ?>">
                                            <td><?= $i + 1 ?></td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($t['customer_name']) ?></div>
                                                <div class="td-sub"><?= htmlspecialchars($t['customer_phone'] ?? '-') ?></div>
                                            </td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($t['brand'] . ' ' . $t['vehicle_type']) ?></div>
                                                <div class="td-sub"><?= htmlspecialchars($t['color'] ?? '') ?> • <?= htmlspecialchars(substr($t['chassis_number'] ?? '-', 0, 10)) ?>…</div>
                                            </td>
                                            <td><?= htmlspecialchars($t['booking_date'] ?? '-') ?></td>
                                            <td class="right td-total"><?= rupiah((float)$t['biaya_jasa']) ?></td>
                                            <td class="right td-total"><?= rupiah((float)$t['total_komponen']) ?></td>
                                            <td class="right td-total" style="color:var(--clr-accent-teal);"><?= rupiah((float)$t['grand_total']) ?></td>
                                            <td class="center">
                                                <?php if ($t['wo_status'] === 'done'): ?>
                                                    <span class="dl-badge dl-badge--done">Lunas</span>
                                                <?php else: ?>
                                                    <span class="dl-badge dl-badge--ready">Menunggu Bayar</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="center">
                                                <button
                                                    class="dl-btn dl-btn--detail"
                                                    onclick="bukaDetail(<?= (int)$t['work_order_id'] ?>)">
                                                    Rincian
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div><!-- /dl-card -->

            </main><!-- /dl-page -->
        </div><!-- /dl-main -->
    </div><!-- /dl-shell -->


    <!-- ============ MODAL DETAIL TAGIHAN ============ -->
    <div class="dl-modal-overlay" id="modalOverlay" onclick="tutupModalJikaOverlay(event)">
        <div class="dl-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">

            <div class="dl-modal__head">
                <div>
                    <div class="dl-modal__title" id="modalTitle">Rincian Tagihan</div>
                    <div class="dl-modal__subtitle" id="modalSubtitle"></div>
                </div>
                <button class="dl-modal__close" onclick="tutupModal()" aria-label="Tutup">&times;</button>
            </div>

            <div class="dl-modal__body" id="modalBody">
                <!-- Diisi oleh JS -->
                <p style="color:var(--clr-text-muted);text-align:center;padding:32px 0;">Memuat…</p>
            </div>

            <div class="dl-modal__foot">
                <button class="dl-btn dl-btn--ghost" onclick="tutupModal()">Tutup</button>
                <a id="btnCetakNota" href="#" target="_blank" class="dl-btn dl-btn--primary" style="display:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 6 2 18 2 18 9" />
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                        <rect x="6" y="14" width="12" height="8" />
                    </svg>
                    Cetak Nota
                </a>
                <span id="infoBelumLunas" style="display:none;font-size:12.5px;color:var(--clr-text-muted);align-self:center;">
                    Nota hanya bisa dicetak setelah tagihan lunas.
                </span>
            </div>

        </div>
    </div>


    <script>
        /* ==================================================
   service-billing/index.php — inline JS
   Hanya untuk interaksi UI: filter & modal detail.
   Tidak ada logika bisnis di sini.
================================================== */

        /**
         * Filter baris tabel berdasarkan input search dan dropdown status.
         */
        function filterTagihan() {
            const keyword = document.getElementById('searchInput').value.toLowerCase().trim();
            const status = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#tagihan-table tbody tr');

            rows.forEach(function(row) {
                const name = row.dataset.name || '';
                const vehicle = row.dataset.vehicle || '';
                const rowStat = row.dataset.status || '';

                const cocokKata = keyword === '' || name.includes(keyword) || vehicle.includes(keyword);
                const cocokStatus = status === '' || rowStat === status;

                row.style.display = (cocokKata && cocokStatus) ? '' : 'none';
            });
        }

        /**
         * Buka modal dan load data detail via fetch JSON.
         */
        function bukaDetail(workOrderId) {
            const overlay = document.getElementById('modalOverlay');
            const body = document.getElementById('modalBody');
            const subtitle = document.getElementById('modalSubtitle');

            // Reset & tampilkan modal
            body.innerHTML = '<p style="color:var(--clr-text-muted);text-align:center;padding:40px 0;">Memuat rincian…</p>';
            subtitle.textContent = '';
            overlay.classList.add('open');

            fetch('/service-billing/' + workOrderId)
                .then(function(res) {
                    if (!res.ok) throw new Error('Gagal memuat data tagihan.');
                    return res.json();
                })
                .then(function(d) {
                    renderModal(d);
                })
                .catch(function(err) {
                    body.innerHTML = '<p style="color:#e53e3e;text-align:center;padding:40px 0;">' + err.message + '</p>';
                });
        }

        /**
         * Render isi modal dari data JSON detail tagihan.
         */
        function renderModal(d) {
            const subtitle = document.getElementById('modalSubtitle');
            subtitle.textContent = d.brand + ' ' + d.vehicle_type + ' — ' + d.customer_name;

            // Update link & visibilitas tombol cetak nota
            // Pakai endpoint yang sama dengan menu Nota Servis (KasirController@cetakNota)
            // — bukan endpoint baru, supaya logika nota tidak terduplikasi di 2 tempat.
            const btnCetak = document.getElementById('btnCetakNota');
            const infoBelum = document.getElementById('infoBelumLunas');

            if (d.wo_status === 'done') {
                btnCetak.href = '/kasir/nota/cetak/' + d.work_order_id;
                btnCetak.style.display = '';
                infoBelum.style.display = 'none';
            } else {
                btnCetak.style.display = 'none';
                infoBelum.style.display = '';
            }

            // Helper format Rupiah (di JS)
            function rp(n) {
                return 'Rp ' + Number(n).toLocaleString('id-ID');
            }

            // Baris sparepart
            let barisPart = '';
            if (d.spareparts && d.spareparts.length > 0) {
                d.spareparts.forEach(function(p, i) {
                    barisPart += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + esc(p.nama_sparepart) + '</td>' +
                        '<td style="color:var(--clr-text-muted);font-size:12px;">' + esc(p.sku || '-') + '</td>' +
                        '<td class="right">' + rp(p.harga_satuan) + '</td>' +
                        '<td class="center">' + p.quantity + '</td>' +
                        '<td class="right"><strong>' + rp(p.subtotal) + '</strong></td>' +
                        '</tr>';
                });
            } else {
                barisPart = '<tr><td colspan="6" style="text-align:center;color:var(--clr-text-muted);padding:16px 0;">Tidak ada komponen sparepart.</td></tr>';
            }

            document.getElementById('modalBody').innerHTML =
                /* ----- Info Kendaraan & Pelanggan ----- */
                '<div class="dl-info-grid">' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Pelanggan</div><div class="dl-info-item__value">' + esc(d.customer_name) + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Telepon</div><div class="dl-info-item__value">' + esc(d.customer_phone || '-') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Kendaraan</div><div class="dl-info-item__value">' + esc(d.brand + ' ' + d.vehicle_type) + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Warna</div><div class="dl-info-item__value">' + esc(d.color || '-') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">No. Rangka</div><div class="dl-info-item__value">' + esc(d.chassis_number || '-') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Mekanik</div><div class="dl-info-item__value">' + esc(d.mechanic_name || '-') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Tanggal Booking</div><div class="dl-info-item__value">' + esc(d.booking_date || '-') + '</div></div>' +
                '<div class="dl-info-item"><div class="dl-info-item__label">Catatan WO</div><div class="dl-info-item__value">' + esc(d.wo_description || '-') + '</div></div>' +
                '</div>'

                /* ----- Tabel Sparepart ----- */
                +
                '<div class="dl-section-label">Komponen Sparepart Terpakai</div>' +
                '<table class="dl-part-table">' +
                '<thead><tr>' +
                '<th>#</th><th>Nama Sparepart</th><th>SKU</th>' +
                '<th class="right">Harga Satuan</th><th class="center">Qty</th><th class="right">Subtotal</th>' +
                '</tr></thead>' +
                '<tbody>' + barisPart + '</tbody>' +
                '</table>'

                /* ----- Ringkasan Total ----- */
                +
                '<div class="dl-bill-summary">' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__label">Total Komponen Sparepart</span>' +
                '<span class="dl-bill-summary__value">' + rp(d.total_komponen) + '</span>' +
                '</div>' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__label">Biaya Jasa Servis (' + d.jumlah_log + ' log mekanik)</span>' +
                '<span class="dl-bill-summary__value">' + rp(d.biaya_jasa) + '</span>' +
                '</div>' +
                '<hr class="dl-bill-summary__divider">' +
                '<div class="dl-bill-summary__row">' +
                '<span class="dl-bill-summary__total-label">GRAND TOTAL</span>' +
                '<span class="dl-bill-summary__total-value">' + rp(d.grand_total) + '</span>' +
                '</div>' +
                '</div>';
        }

        /** Sanitize string untuk innerHTML */
        function esc(str) {
            if (str === null || str === undefined) return '-';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function tutupModal() {
            document.getElementById('modalOverlay').classList.remove('open');
        }

        function tutupModalJikaOverlay(e) {
            if (e.target === document.getElementById('modalOverlay')) tutupModal();
        }

        // Tutup modal dengan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tutupModal();
        });
    </script>

</body>

</html>