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
<style>
   /* ============================================================
   service-billing.css
   Sprint 12 — Pembayaran Servis (Kasir Bengkel)
   Mengikuti palet DealerLink DMS dari Image.pdf
   ============================================================ */

/* ----- Token Warna ----- */
:root {
    --clr-bg:           #f0f2f7;
    --clr-sidebar:      #1a1f2e;
    --clr-sidebar-text: #c8cfe0;
    --clr-sidebar-active-bg: #2a3147;
    --clr-sidebar-active-text: #2dd4a7;

    --clr-surface:      #ffffff;
    --clr-border:       #e2e8f0;

    --clr-text-primary: #1e2a3b;
    --clr-text-secondary: #6b7a90;
    --clr-text-muted:   #9aaaba;

    --clr-accent-teal:  #2dd4a7;
    --clr-accent-blue:  #4f8ef7;

    --clr-status-ready-bg:   #ecfdf5;
    --clr-status-ready-text: #0d8a5f;
    --clr-status-done-bg:    #f0f4ff;
    --clr-status-done-text:  #3b5bdb;

    --clr-total-bg:     #1a1f2e;
    --clr-total-text:   #2dd4a7;

    --radius-card:   12px;
    --radius-badge:  6px;
    --radius-btn:    8px;

    --shadow-card:   0 1px 4px rgba(30,42,59,.07), 0 4px 16px rgba(30,42,59,.05);
}

/* ----- Reset minimal ----- */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Segoe UI', system-ui, Arial, sans-serif; background: var(--clr-bg); color: var(--clr-text-primary); }

/* ----- Layout shell ----- */
.dl-shell {
    display: flex;
    min-height: 100vh;
}

/* ===================== SIDEBAR ===================== */
.dl-sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--clr-sidebar);
    display: flex;
    flex-direction: column;
    padding: 0;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
}

.dl-sidebar__logo {
    padding: 24px 20px 20px;
    font-size: 17px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -.3px;
    border-bottom: 1px solid rgba(255,255,255,.07);
}

.dl-sidebar__logo span {
    color: var(--clr-accent-teal);
}

.dl-sidebar__nav {
    padding: 12px 0;
    flex: 1;
}

.dl-sidebar__item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    color: var(--clr-sidebar-text);
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 500;
    border-radius: 0;
    transition: background .15s, color .15s;
    cursor: pointer;
}

.dl-sidebar__item:hover {
    background: rgba(255,255,255,.06);
    color: #fff;
}

.dl-sidebar__item.active {
    background: var(--clr-sidebar-active-bg);
    color: var(--clr-sidebar-active-text);
    font-weight: 600;
}

.dl-sidebar__item svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    opacity: .8;
}

.dl-sidebar__item.active svg {
    opacity: 1;
}

/* ----- User footer sidebar ----- */
.dl-sidebar__user {
    padding: 16px 20px;
    border-top: 1px solid rgba(255,255,255,.07);
    display: flex;
    align-items: center;
    gap: 10px;
}

.dl-sidebar__avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--clr-accent-teal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: var(--clr-sidebar);
    flex-shrink: 0;
}

.dl-sidebar__user-info {
    min-width: 0;
}

.dl-sidebar__user-name {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dl-sidebar__user-role {
    font-size: 11px;
    color: var(--clr-text-muted);
    margin-top: 1px;
}

/* ===================== MAIN CONTENT ===================== */
.dl-main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* ----- Topbar ----- */
.dl-topbar {
    height: 60px;
    background: var(--clr-surface);
    border-bottom: 1px solid var(--clr-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.dl-topbar__title {
    font-size: 15px;
    font-weight: 600;
    color: var(--clr-text-primary);
}

.dl-topbar__breadcrumb {
    font-size: 12px;
    color: var(--clr-text-muted);
    margin-top: 2px;
}

.dl-topbar__right {
    display: flex;
    align-items: center;
    gap: 14px;
}

/* ----- Page body ----- */
.dl-page {
    padding: 28px;
    flex: 1;
}

/* ===================== KOMPONEN ===================== */

/* Kartu generik */
.dl-card {
    background: var(--clr-surface);
    border-radius: var(--radius-card);
    border: 1px solid var(--clr-border);
    box-shadow: var(--shadow-card);
}

/* Header halaman */
.dl-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.dl-page-header__title {
    font-size: 22px;
    font-weight: 700;
    color: var(--clr-text-primary);
    letter-spacing: -.3px;
}

.dl-page-header__sub {
    font-size: 13px;
    color: var(--clr-text-secondary);
    margin-top: 3px;
}

/* Tombol */
.dl-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: var(--radius-btn);
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: filter .15s, transform .1s;
    line-height: 1;
    white-space: nowrap;
}

.dl-btn:active { transform: translateY(1px); }

.dl-btn--primary {
    background: var(--clr-accent-teal);
    color: var(--clr-sidebar);
}

.dl-btn--primary:hover { filter: brightness(1.08); }

.dl-btn--ghost {
    background: transparent;
    color: var(--clr-text-secondary);
    border: 1px solid var(--clr-border);
}

.dl-btn--ghost:hover {
    background: var(--clr-bg);
    color: var(--clr-text-primary);
}

.dl-btn--detail {
    background: #eef3ff;
    color: var(--clr-accent-blue);
}

.dl-btn--detail:hover { background: #dce7ff; }

/* Badge status */
.dl-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: var(--radius-badge);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
}

.dl-badge--ready {
    background: var(--clr-status-ready-bg);
    color: var(--clr-status-ready-text);
}

.dl-badge--done {
    background: var(--clr-status-done-bg);
    color: var(--clr-status-done-text);
}

/* ===================== TABEL DAFTAR TAGIHAN ===================== */
.dl-table-wrap {
    overflow-x: auto;
}

.dl-table {
    width: 100%;
    border-collapse: collapse;
}

.dl-table thead tr {
    background: #f8fafc;
    border-bottom: 2px solid var(--clr-border);
}

.dl-table th {
    padding: 13px 18px;
    font-size: 12px;
    font-weight: 600;
    color: var(--clr-text-secondary);
    text-align: left;
    text-transform: uppercase;
    letter-spacing: .5px;
    white-space: nowrap;
}

.dl-table th.right { text-align: right; }
.dl-table th.center { text-align: center; }

.dl-table tbody tr {
    border-bottom: 1px solid var(--clr-border);
    transition: background .12s;
}

.dl-table tbody tr:last-child { border-bottom: none; }
.dl-table tbody tr:hover { background: #f8fafc; }

.dl-table td {
    padding: 16px 18px;
    font-size: 13.5px;
    color: var(--clr-text-primary);
    vertical-align: middle;
}

.dl-table td.right { text-align: right; }
.dl-table td.center { text-align: center; }

/* Sel nama pelanggan */
.dl-table td .td-name {
    font-weight: 600;
    color: var(--clr-text-primary);
}

.dl-table td .td-sub {
    font-size: 12px;
    color: var(--clr-text-muted);
    margin-top: 2px;
}

/* Total pada tabel */
.td-total {
    font-weight: 700;
    color: var(--clr-text-primary);
    font-size: 14px;
}

/* Baris empty */
.dl-table__empty {
    text-align: center;
    padding: 40px 18px;
    color: var(--clr-text-muted);
    font-size: 13.5px;
}

/* ===================== PANEL FILTER / SEARCH ===================== */
.dl-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--clr-border);
    flex-wrap: wrap;
}

.dl-search {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 340px;
}

.dl-search__icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--clr-text-muted);
    pointer-events: none;
}

.dl-search__input {
    width: 100%;
    padding: 8px 12px 8px 34px;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-btn);
    font-size: 13.5px;
    color: var(--clr-text-primary);
    background: var(--clr-bg);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}

.dl-search__input:focus {
    border-color: var(--clr-accent-teal);
    box-shadow: 0 0 0 3px rgba(45,212,167,.15);
    background: #fff;
}

.dl-select {
    padding: 8px 12px;
    border: 1px solid var(--clr-border);
    border-radius: var(--radius-btn);
    font-size: 13.5px;
    color: var(--clr-text-primary);
    background: var(--clr-bg);
    outline: none;
    cursor: pointer;
    transition: border-color .15s;
}

.dl-select:focus {
    border-color: var(--clr-accent-teal);
}

/* ===================== MODAL DETAIL TAGIHAN ===================== */
.dl-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,20,30,.55);
    z-index: 100;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.dl-modal-overlay.open {
    display: flex;
}

.dl-modal {
    background: var(--clr-surface);
    border-radius: 14px;
    width: 100%;
    max-width: 680px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(15,20,30,.25);
    display: flex;
    flex-direction: column;
}

.dl-modal__head {
    padding: 22px 24px 16px;
    border-bottom: 1px solid var(--clr-border);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    position: sticky;
    top: 0;
    background: var(--clr-surface);
    z-index: 1;
}

.dl-modal__title {
    font-size: 17px;
    font-weight: 700;
    color: var(--clr-text-primary);
}

.dl-modal__subtitle {
    font-size: 12.5px;
    color: var(--clr-text-secondary);
    margin-top: 3px;
}

.dl-modal__close {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: var(--clr-bg);
    color: var(--clr-text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    transition: background .15s;
}

.dl-modal__close:hover { background: #e2e8f0; }

.dl-modal__body {
    padding: 20px 24px;
    flex: 1;
}

/* Info grid: 2 kolom */
.dl-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 20px;
    margin-bottom: 20px;
}

.dl-info-item__label {
    font-size: 11px;
    font-weight: 600;
    color: var(--clr-text-muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 3px;
}

.dl-info-item__value {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--clr-text-primary);
}

/* Section heading dalam modal */
.dl-section-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--clr-text-secondary);
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--clr-border);
}

/* Tabel sparepart dalam modal */
.dl-part-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.dl-part-table th {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--clr-text-muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 8px 10px;
    border-bottom: 2px solid var(--clr-border);
    text-align: left;
}

.dl-part-table th.right { text-align: right; }

.dl-part-table td {
    font-size: 13px;
    padding: 10px 10px;
    border-bottom: 1px solid #f1f4f8;
    color: var(--clr-text-primary);
    vertical-align: middle;
}

.dl-part-table td.right { text-align: right; }

.dl-part-table tfoot td {
    font-size: 13px;
    font-weight: 600;
    padding: 10px 10px;
    border-top: 2px solid var(--clr-border);
}

.dl-part-table tfoot td.right { text-align: right; }

/* ---- Ringkasan total di bawah modal ---- */
.dl-bill-summary {
    background: var(--clr-total-bg);
    border-radius: 10px;
    padding: 18px 20px;
    margin-top: 4px;
}

.dl-bill-summary__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
}

.dl-bill-summary__label {
    font-size: 13px;
    color: var(--clr-sidebar-text);
}

.dl-bill-summary__value {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
}

.dl-bill-summary__divider {
    border: none;
    border-top: 1px solid rgba(255,255,255,.12);
    margin: 10px 0;
}

.dl-bill-summary__total-label {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--clr-accent-teal);
}

.dl-bill-summary__total-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--clr-accent-teal);
    letter-spacing: -.3px;
}

.dl-modal__foot {
    padding: 16px 24px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid var(--clr-border);
    background: var(--clr-surface);
}

/* ===================== RESPONSIF ===================== */
@media (max-width: 900px) {
    .dl-sidebar { display: none; }
    .dl-page { padding: 16px; }
    .dl-info-grid { grid-template-columns: 1fr; }
}

@media (max-width: 600px) {
    .dl-filter-bar { flex-direction: column; align-items: stretch; }
    .dl-search { max-width: 100%; }
}

/* ---- Sidebar section label ---- */
.dl-sidebar__section-label {
    padding: 16px 20px 6px;
    font-size: 10px;
    font-weight: 700;
    color: rgba(200,207,224,.4);
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* ---- Sidebar badge (pending count) ---- */
.dl-sidebar__badge {
    margin-left: auto;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    line-height: 1.4;
}
</style>

    <div>
        <div class="dl-shell">
           
            <div class="dl-main">
               
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
                                                        onclick="bukaDetail(<?= $t['work_order_id'] ?>)">
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
                    <a id="btnCetakNota" href="#" class="dl-btn dl-btn--primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 6 2 18 2 18 9" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Cetak Nota
                    </a>
                </div>
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
         function bukaDetail(id) {
            const overlay = document.getElementById('modalOverlay');
            const body = document.getElementById('modalBody');
            const subtitle = document.getElementById('modalSubtitle');

            // Reset & tampilkan modal
            body.innerHTML = '<p style="color:var(--clr-text-muted);text-align:center;padding:40px 0;">Memuat rincian…</p>';
            subtitle.textContent = '';
            overlay.classList.add('open');

            fetch('/service-billing/' + id)
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

            // Update link cetak nota
            document.getElementById('btnCetakNota').href = '/service-billing/' + d.work_order_id + '/nota';

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