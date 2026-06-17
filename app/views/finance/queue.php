<?php
/**
 * View: Antrean Tagihan Pembayaran Tunai
 * PBI-5.1 — Khusus Role Finance
 * Variabel yang diterima dari FinanceController@queue:
 *   $title        string
 *   $transactions array  — hasil getPendingCashTransactions()
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Antrean Tagihan') ?> — Finance</title>
    <meta name="description" content="Halaman antrean tagihan pembayaran tunai untuk verifikasi oleh bagian Finance.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #1e40af;
            --primary-light: #3b82f6;
            --primary-dark:  #1e3a8a;
            --success:       #059669;
            --warning:       #d97706;
            --danger:        #dc2626;
            --surface:       #ffffff;
            --surface-2:     #f8fafc;
            --border:        #e2e8f0;
            --text-main:     #0f172a;
            --text-sub:      #64748b;
            --shadow-sm:     0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md:     0 4px 16px rgba(0,0,0,.10);
            --radius:        12px;
            --radius-sm:     8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            min-height: 100vh;
            color: var(--text-main);
        }

        /* ── TOP NAV ── */
        .topnav {
            background: var(--primary-dark);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }
        .topnav-brand {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .5px;
        }
        .topnav-brand span { color: #93c5fd; }
        .topnav-badge {
            background: #1e40af;
            color: #bfdbfe;
            font-size: .72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: .4px;
        }

        /* ── PAGE WRAPPER ── */
        .page-wrapper {
            max-width: 1180px;
            margin: 0 auto;
            padding: 36px 24px 60px;
        }

        /* ── HEADER SECTION ── */
        .page-header {
            margin-bottom: 28px;
        }
        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-dark);
        }
        .page-header p {
            font-size: .875rem;
            color: var(--text-sub);
            margin-top: 5px;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            color: var(--text-sub);
            margin-bottom: 14px;
        }
        .breadcrumb span { color: var(--primary-light); font-weight: 500; }

        /* ── STAT CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-light);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .stat-card.warning { border-left-color: var(--warning); }
        .stat-card.success { border-left-color: var(--success); }
        .stat-label { font-size: .72rem; font-weight: 600; color: var(--text-sub); text-transform: uppercase; letter-spacing: .6px; }
        .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text-main); }

        /* ── CARD / TABLE ── */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, #eff6ff, #fff);
        }
        .card-header h2 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-dark);
        }
        .card-header .count-badge {
            background: #dbeafe;
            color: var(--primary);
            font-size: .78rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .875rem;
        }
        thead { background: var(--surface-2); }
        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: .72rem;
            font-weight: 600;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
        }
        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fbff; }
        td { padding: 14px 16px; vertical-align: middle; }

        .col-no    { width: 50px; text-align: center; color: var(--text-sub); font-weight: 500; }
        .col-code  { font-weight: 600; color: var(--primary); font-size: .83rem; letter-spacing: .3px; }
        .col-name  { font-weight: 500; }
        .col-sub   { font-size: .78rem; color: var(--text-sub); margin-top: 2px; }
        .col-price { font-weight: 700; color: var(--text-main); text-align: right; }
        .col-date  { font-size: .8rem; color: var(--text-sub); }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 600;
        }
        .status-process { background: #fef3c7; color: #92400e; }
        .status-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse-dot 1.5s infinite;
        }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }

        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: .78rem;
            font-weight: 600;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 2px 6px rgba(30,64,175,.3);
        }
        .btn-detail:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30,64,175,.4);
        }
        .btn-detail svg { flex-shrink: 0; }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 60px 24px;
            text-align: center;
        }
        .empty-state .icon {
            width: 64px; height: 64px;
            background: #eff6ff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .empty-state h3 { font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .empty-state p  { font-size: .85rem; color: var(--text-sub); }
    </style>
</head>
<body>

<!-- TOP NAV -->
<nav class="topnav">
    <div class="topnav-brand">RBPL <span>/ Finance</span></div>
    <div class="topnav-badge">💼 Kasir Finance</div>
</nav>

<div class="page-wrapper">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        Dashboard &rsaquo; <span>Antrean Tagihan Tunai</span>
    </div>

    <!-- HEADER -->
    <div class="page-header">
        <h1>Antrean Tagihan Pembayaran Tunai</h1>
        <p>Daftar transaksi kendaraan dengan metode tunai yang menunggu verifikasi pembayaran oleh Finance.</p>
    </div>

    <!-- STAT CARDS -->
    <?php
        $totalTransaksi  = count($transactions);
        $totalNilai      = array_sum(array_column($transactions, 'vehicle_price'));
    ?>
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Total Antrean</div>
            <div class="stat-value"><?= $totalTransaksi ?></div>
        </div>
        <div class="stat-card warning">
            <div class="stat-label">Total Nilai Tagihan</div>
            <div class="stat-value" style="font-size:1.25rem">
                Rp <?= number_format($totalNilai, 0, ',', '.') ?>
            </div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Status</div>
            <div class="stat-value" style="font-size:1rem;color:#059669">Menunggu Verifikasi</div>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card">
        <div class="card-header">
            <h2>📋 Daftar Tagihan Pending</h2>
            <span class="count-badge"><?= $totalTransaksi ?> Tagihan</span>
        </div>

        <?php if (empty($transactions)): ?>
        <div class="empty-state">
            <div class="icon">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <h3>Semua Tagihan Sudah Terselesaikan!</h3>
            <p>Tidak ada transaksi tunai yang menunggu verifikasi saat ini.</p>
        </div>

        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Kode Transaksi</th>
                    <th>Pelanggan</th>
                    <th>Unit Kendaraan</th>
                    <th>Metode Bayar</th>
                    <th style="text-align:right">Nilai Transaksi</th>
                    <th>Status</th>
                    <th>Tgl. Dibuat</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($transactions as $i => $trx): ?>
                <tr>
                    <td class="col-no"><?= $i + 1 ?></td>
                    <td>
                        <div class="col-code"><?= htmlspecialchars($trx['transaction_code']) ?></div>
                    </td>
                    <td>
                        <div class="col-name"><?= htmlspecialchars($trx['customer_name']) ?></div>
                    </td>
                    <td>
                        <div class="col-name"><?= htmlspecialchars($trx['vehicle_brand']) ?> <?= htmlspecialchars($trx['vehicle_type']) ?></div>
                    </td>
                    <td>
                        <div style="font-size:.8rem;font-weight:500;"><?= htmlspecialchars($trx['payment_type_name']) ?></div>
                    </td>
                    <td class="col-price">
                        Rp <?= number_format((float)$trx['vehicle_price'], 0, ',', '.') ?>
                    </td>
                    <td>
                        <span class="status-badge status-process">
                            <span class="status-dot"></span>
                            Pending
                        </span>
                    </td>
                    <td class="col-date">
                        <?= date('d M Y', strtotime($trx['created_at'])) ?>
                    </td>
                    <td style="text-align:center">
                        <a href="/finance/transactions/<?= (int)$trx['id'] ?>"
                           id="btn-detail-<?= (int)$trx['id'] ?>"
                           class="btn-detail">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Rincian
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div><!-- .card -->

</div><!-- .page-wrapper -->

</body>
</html>
