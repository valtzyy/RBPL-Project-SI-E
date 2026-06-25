<?php

/**
 * View: sparepart_gudang.php
 * DealerLink – Gudang Logistik & Suku Cadang
 *
 * Variabel dari Controller:
 *   $user         – array ['name', 'role']
 *   $title        – string
 *   $lowStock     – array suku cadang stok kritis  [id, name, stock, min_stock, sku]
 *   $allSpareparts– array semua suku cadang        [id, name, stock]
 *   $allPO        – array log purchase order       [id, supplier_name, sparepart_name, quantity, status, created_at]
 */
?>


<div>

    <!-- TOPBAR -->
    <header class="topbar">
        <div>
            <div class="page-title">Gudang Logistik &amp; Suku Cadang</div>
            <div class="page-breadcrumb">Kelola Purchase Order &amp; pantau stok inventaris</div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="/dashboard" class="btn btn-outline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Executive Dashboard
            </a>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">

        <!-- ── ALERT STOK KRITIS ─────────────────────── -->
        <?php if (!empty($lowStock)): ?>
            <div class="alert-critical">
                <div class="alert-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <?= count($lowStock) ?> Suku Cadang dalam Kondisi Stok Kritis
                </div>
                <ul>
                    <?php foreach ($lowStock as $ls): ?>
                        <li>
                            <strong><?= htmlspecialchars($ls['name']) ?></strong>
                            <?php if (!empty($ls['sku'])): ?>
                                <span style="color:#a0522d;font-size:11px;">(SKU: <?= htmlspecialchars($ls['sku']) ?>)</span>
                            <?php endif; ?>
                            — Sisa stok: <strong><?= $ls['stock'] ?> pcs</strong>,
                            batas minimum: <?= $ls['min_stock'] ?> pcs.
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="alert-safe">
                <div class="alert-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Semua Stok Suku Cadang Dalam Kondisi Aman
                </div>
                <p>Tidak ada item yang berada di bawah batas minimum stok saat ini.</p>
            </div>
        <?php endif; ?>

        <!-- ── STAT STRIP ─────────────────────────────── -->
        <?php
        $totalItems   = count($allSpareparts ?? []);
        $kritisCount  = count($lowStock ?? []);
        $amanCount    = $totalItems - $kritisCount;
        ?>
        <div class="stat-strip">
            <div class="stat-card c-green">
                <div class="stat-label">Total Jenis Suku Cadang</div>
                <div class="stat-value"><?= $totalItems ?> <span style="font-size:14px;font-weight:500;color:var(--text-muted)">item</span></div>
            </div>
            <div class="stat-card c-red">
                <div class="stat-label">Stok Kritis / Menipis</div>
                <div class="stat-value"><?= $kritisCount ?> <span style="font-size:14px;font-weight:500;color:var(--text-muted)">item</span></div>
            </div>
            <div class="stat-card c-orange">
                <div class="stat-label">PO Menunggu Penerimaan</div>
                <div class="stat-value">
                    <?php
                    $pendingPO = count(array_filter($allPO ?? [], fn($p) => strtolower($p['status'] ?? '') === 'pending'));
                    echo $pendingPO;
                    ?>
                    <span style="font-size:14px;font-weight:500;color:var(--text-muted)">PO</span>
                </div>
            </div>
        </div>

        <!-- ── FORM CETAK PO ──────────────────────────── -->
        <div class="section-header" style="margin-bottom:14px;">
            <div>
                <div class="section-title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Purchase Order (PO) Baru
                </div>
                <div class="section-sub">Isi form berikut untuk mencetak surat pesanan ke supplier</div>
            </div>
        </div>

        <div class="card" style="margin-bottom:22px;">
            <div class="card-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Form Surat Pesanan
            </div>

            <form action="/purchase-order/store" method="POST">
                <div class="form-grid">
                    <!-- Nama Supplier -->
                    <div >
                        <label for="supplier_name">Nama Supplier</label>
                        <input
                            type="text"
                            id="supplier_name"
                            name="supplier_name"
                            class="form-control"
                            placeholder="cth. PT Mitra Otomotif"
                            required>
                    </div>

                    <!-- Pilih Suku Cadang -->
                    <div>
                        <label for="sparepart_id">Suku Cadang</label>
                        <select id="sparepart_id" name="sparepart_id" class="form-control" required>
                            <option value="" disabled selected>— Pilih suku cadang —</option>
                            <?php foreach (($allSpareparts ?? []) as $sp): ?>
                                <?php if (isset($sp['id'], $sp['name'], $sp['stock'])): ?>
                                    <option value="<?= $sp['id'] ?>"
                                        <?php if (isset($sp['min_stock']) && $sp['stock'] <= $sp['min_stock']): ?>
                                        style="color:#c53030;font-weight:600;"
                                        <?php endif; ?>>
                                        <?= htmlspecialchars($sp['name']) ?>
                                        (Stok: <?= $sp['stock'] ?> pcs<?= isset($sp['min_stock']) && $sp['stock'] <= $sp['min_stock'] ? ' ⚠' : '' ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Kuantitas -->
                    <div>
                        <label for="quantity">Kuantitas Pesanan</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="form-control"
                            min="1"
                            value="1"
                            placeholder="Jumlah (pcs)"
                            required>
                    </div>

                    <!-- Submit -->
                    <div>
                        <label style="opacity:0">Aksi</label>
                        <button type="submit" class="btn btn-primary" style="height:40px;width:100%;justify-content:center;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M17 17H17.01M17 3H5a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2zM3 13h.01M7 13h.01M11 13H3a2 2 0 00-2 2v4a2 2 0 002 2h18a2 2 0 002-2v-4a2 2 0 00-2-2h-8z" />
                            </svg>
                            Kirim & Cetak PO
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ── LOG PURCHASE ORDER ─────────────────────── -->
        <div class="section-header">
            <div>
                <div class="section-title">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Log Riwayat Purchase Order
                </div>
                <div class="section-sub">
                    <?= count($allPO ?? []) ?> total PO tercatat &nbsp;·&nbsp;
                    <?= $pendingPO ?> menunggu konfirmasi penerimaan
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode PO</th>
                            <th>Supplier</th>
                            <th>Suku Cadang</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allPO)): ?>
                            <tr class="empty-row">
                                <td colspan="7">
                                    <svg width="32" height="32" fill="none" stroke="#cbd5e0" stroke-width="1.5" viewBox="0 0 24 24" style="display:block;margin:0 auto 8px;">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Belum ada log riwayat Purchase Order.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allPO as $po): ?>
                                <?php
                                $status     = strtolower($po['status'] ?? 'pending');
                                $badgeClass = match ($status) {
                                    'received'  => 'badge-received',
                                    'cancelled' => 'badge-cancelled',
                                    default     => 'badge-pending',
                                };
                                $poId       = str_pad($po['id'] ?? '0', 5, '0', STR_PAD_LEFT);
                                $spareName  = htmlspecialchars($po['sparepart_name'] ?? $po['name'] ?? '–');
                                $supplier   = htmlspecialchars($po['supplier_name'] ?? '–');
                                $qty        = $po['quantity'] ?? 0;
                                $createdAt  = isset($po['created_at'])
                                    ? date('d M Y', strtotime($po['created_at']))
                                    : '–';
                                ?>
                                <tr>
                                    <td><span class="po-id">PO-<?= $poId ?></span></td>
                                    <td><?= $supplier ?></td>
                                    <td><?= $spareName ?></td>
                                    <td style="font-weight:600"><?= $qty ?> pcs</td>
                                    <td style="color:var(--text-muted);font-size:12px"><?= $createdAt ?></td>
                                    <td><span class="badge <?= $badgeClass ?>"><?= strtoupper($status) ?></span></td>
                                    <td>
                                        <?php if ($status === 'pending'): ?>
                                            <a href="/purchase-order/receipt?id=<?= $po['id'] ?? '' ?>"
                                                onclick="return confirm('Konfirmasi: item kiriman supplier sudah diterima dan sesuai?')"
                                                class="btn btn-success-solid"
                                                style="font-size:12px;padding:5px 12px;">
                                                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path d="M5 13l4 4L19 7" />
                                                </svg>
                                                Terima &amp; Restock
                                            </a>
                                        <?php elseif ($status === 'received'): ?>
                                            <span style="color:var(--green);font-size:12px;font-weight:600;display:flex;align-items:center;gap:4px;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Selesai di-restock
                                            </span>
                                        <?php else: ?>
                                            <span style="color:var(--red);font-size:12px;">Dibatalkan</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
<style>
    /* ─── TOKENS (sama persis dengan dashboard_manajerial.php) ─── */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --sidebar-bg: #1e2235;
        --sidebar-text: #a0aec0;
        --sidebar-active-bg: #2d3555;
        --sidebar-active-text: #ffffff;
        --sidebar-hover-bg: #252b42;
        --accent: #6c63ff;
        --accent-soft: rgba(108, 99, 255, .12);

        --topbar-bg: #ffffff;
        --topbar-border: #e8eaf0;

        --page-bg: #f0f2f7;
        --card-bg: #ffffff;
        --card-border: #e8eaf0;
        --card-shadow: 0 1px 4px rgba(0, 0, 0, .06), 0 4px 16px rgba(0, 0, 0, .04);

        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;

        --green: #38a169;
        --orange: #dd6b20;
        --red: #e53e3e;
        --blue: #3182ce;
        --purple: #805ad5;

        --sidebar-w: 220px;
        --topbar-h: 56px;
        --radius: 10px;
    }

    .topbar {
        height: var(--topbar-h);
        background: var(--topbar-bg);
        border-bottom: 1px solid var(--topbar-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 28px;
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .topbar .page-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .topbar .page-breadcrumb {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .page-content {
        flex: 1;
        padding: 24px 28px;
    }

    /* ─── BUTTONS ─────────────────────────────── */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: opacity .15s, transform .1s;
        font-family: inherit;
    }

    .btn:active {
        transform: scale(.97);
    }

    .btn-primary {
        background: var(--accent);
        color: #fff;
    }

    .btn-primary:hover {
        opacity: .88;
    }

    .btn-outline {
        background: transparent;
        color: var(--text-secondary);
        border: 1.5px solid var(--card-border);
    }

    .btn-outline:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-danger {
        background: #fed7d7;
        color: #9b2335;
    }

    .btn-danger:hover {
        background: #feb2b2;
    }

    .btn-success-solid {
        background: var(--green);
        color: #fff;
    }

    .btn-success-solid:hover {
        opacity: .88;
    }

    /* ─── CARDS ───────────────────────────────── */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        padding: 20px 22px;
    }

    .card-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--card-border);
    }

    /* ─── ALERT ───────────────────────────────── */
    .alert-critical {
        background: #fff8f0;
        border-left: 4px solid var(--orange);
        border-radius: 0 var(--radius) var(--radius) 0;
        padding: 14px 18px;
        margin-bottom: 22px;
    }

    .alert-safe {
        background: #f0fff4;
        border-left: 4px solid var(--green);
        border-radius: 0 var(--radius) var(--radius) 0;
        padding: 14px 18px;
        margin-bottom: 22px;
    }

    .alert-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .alert-critical .alert-title {
        color: var(--orange);
    }

    .alert-safe .alert-title {
        color: var(--green);
    }

    .alert-critical ul {
        padding-left: 18px;
        color: #7b341e;
        font-size: 13px;
    }

    .alert-safe p {
        font-size: 13px;
        color: #276749;
    }

    .alert-critical li,
    .alert-safe li {
        margin-bottom: 4px;
    }

    /* ─── STOCK STAT STRIP ────────────────────── */
    .stat-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    @media (max-width: 800px) {
        .stat-strip {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        padding: 16px 20px;
        border-top: 3px solid var(--accent);
    }

    .stat-card.c-red {
        border-top-color: var(--red);
    }

    .stat-card.c-green {
        border-top-color: var(--green);
    }

    .stat-card.c-orange {
        border-top-color: var(--orange);
    }

    .stat-label {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 700;
        line-height: 1.1;
    }

    .stat-card.c-red .stat-value {
        color: var(--red);
    }

    .stat-card.c-green .stat-value {
        color: var(--green);
    }

    .stat-card.c-orange .stat-value {
        color: var(--orange);
    }

    /* ─── SECTION HEADER ──────────────────────── */
    .section-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .section-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ─── FORM ────────────────────────────────── */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    @media (max-width: 900px) {
        .form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 560px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-control {
        padding: 9px 12px;
        border: 1.5px solid var(--card-border);
        border-radius: 7px;
        font-size: 13.5px;
        font-family: inherit;
        color: var(--text-primary);
        background: var(--card-bg);
        transition: border-color .15s, box-shadow .15s;
        outline: none;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-soft);
    }

    .form-control option {
        padding: 4px;
    }

    /* ─── TABLE ───────────────────────────────── */
    .table-wrap {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .data-table th {
        padding: 9px 14px;
        text-align: left;
        background: #f7f8fb;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 1px solid var(--card-border);
        white-space: nowrap;
    }

    .data-table td {
        padding: 11px 14px;
        border-bottom: 1px solid #f2f4f8;
        vertical-align: middle;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover td {
        background: #fafbff;
    }

    .empty-row td {
        text-align: center;
        color: var(--text-muted);
        padding: 32px 0 !important;
        font-size: 13px;
    }

    /* ─── BADGES ──────────────────────────────── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        white-space: nowrap;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-received {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .stock-critical {
        color: var(--red);
        font-weight: 700;
    }

    .stock-low {
        color: var(--orange);
        font-weight: 600;
    }

    .stock-ok {
        color: var(--green);
    }

    /* ─── PO ID ───────────────────────────────── */
    .po-id {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        font-weight: 700;
        color: var(--accent);
        background: var(--accent-soft);
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* ─── SCROLLBAR ───────────────────────────── */
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c9d8;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            overflow: hidden;
        }
/* 
        .main-wrapper {
            margin-left: 0;
        } */
    }
</style>