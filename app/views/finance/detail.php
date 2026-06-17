<?php
/**
 * View: Rincian Tagihan Final + Verifikasi + Cetak Kwitansi
 * PBI-5.2 (Rincian tagihan), PBI-5.7 (Tombol cetak kwitansi)
 * Variabel yang diterima dari FinanceController@showTransaction:
 *   $title           string
 *   $transaction     array   — detail lengkap transaksi + customer + vehicle + sales
 *   $invoice         array|false
 *   $invoiceAmount   float
 *   $payments        array   — semua riwayat pembayaran
 *   $totalPaid       float
 *   $remainingBalance float
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Rincian Tagihan') ?> — Finance</title>
    <meta name="description" content="Rincian tagihan final transaksi kendaraan dan riwayat pembayaran untuk verifikasi Finance.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #1e40af;
            --primary-light: #3b82f6;
            --primary-dark:  #1e3a8a;
            --success:       #059669;
            --success-light: #d1fae5;
            --warning:       #d97706;
            --warning-light: #fef3c7;
            --danger:        #dc2626;
            --danger-light:  #fee2e2;
            --surface:       #ffffff;
            --surface-2:     #f8fafc;
            --border:        #e2e8f0;
            --text-main:     #0f172a;
            --text-sub:      #64748b;
            --shadow-sm:     0 1px 3px rgba(0,0,0,.08);
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
        .topnav-brand { font-size: 1rem; font-weight: 700; color: #fff; }
        .topnav-brand span { color: #93c5fd; }
        .topnav-badge {
            background: #1e40af; color: #bfdbfe;
            font-size: .72rem; font-weight: 600;
            padding: 3px 10px; border-radius: 20px;
        }

        /* ── LAYOUT ── */
        .page-wrapper { max-width: 1180px; margin: 0 auto; padding: 32px 24px 60px; }

        .breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: .78rem; color: var(--text-sub); margin-bottom: 12px;
        }
        .breadcrumb a { color: var(--primary-light); text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { text-decoration: underline; }

        .page-title-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
        }
        .page-title-row h1 { font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); }

        /* ── STATUS PILL ── */
        .status-pill {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px; border-radius: 20px; font-size: .8rem; font-weight: 700;
        }
        .s-process { background: var(--warning-light); color: #92400e; }
        .s-lunas   { background: var(--success-light); color: #065f46; }
        .s-cancel  { background: var(--danger-light);  color: #7f1d1d; }
        .s-dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
        .s-process .s-dot { animation: pulse-dot 1.5s infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.35} }

        /* ── GRID 2 COL ── */
        .main-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
        @media (max-width: 900px) { .main-grid { grid-template-columns: 1fr; } }

        /* ── CARD ── */
        .card {
            background: var(--surface); border-radius: var(--radius);
            box-shadow: var(--shadow-md); overflow: hidden; margin-bottom: 20px;
        }
        .card-header {
            padding: 15px 20px; border-bottom: 1px solid var(--border);
            background: linear-gradient(90deg, #eff6ff, #fff);
            display: flex; align-items: center; gap: 8px;
        }
        .card-header h2 { font-size: .95rem; font-weight: 600; color: var(--primary-dark); }
        .card-body { padding: 20px; }

        /* ── INFO LIST ── */
        .info-list { display: grid; gap: 10px; }
        .info-row {
            display: grid; grid-template-columns: 150px 1fr;
            gap: 8px; font-size: .87rem; align-items: start;
        }
        .info-label { color: var(--text-sub); font-weight: 500; }
        .info-value { font-weight: 500; color: var(--text-main); }
        .info-value.bold { font-weight: 700; }

        /* ── BILLING SUMMARY ── */
        .billing-box {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            border-radius: var(--radius);
            padding: 22px 20px;
            color: #fff;
            margin-bottom: 20px;
        }
        .billing-box h2 { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; opacity: .75; margin-bottom: 16px; }
        .billing-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: .875rem; }
        .billing-row .bl { opacity: .8; }
        .billing-row .bv { font-weight: 600; }
        .billing-divider { border: none; border-top: 1px solid rgba(255,255,255,.2); margin: 14px 0; }
        .billing-total  { display: flex; justify-content: space-between; align-items: center; }
        .billing-total .bl { font-size: .9rem; font-weight: 600; }
        .billing-total .bv { font-size: 1.3rem; font-weight: 800; }
        .billing-total.lunas .bv { color: #86efac; }
        .billing-total.hutang .bv { color: #fca5a5; }

        /* ── ACTION BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 18px; border-radius: var(--radius-sm);
            font-size: .82rem; font-weight: 600; cursor: pointer;
            text-decoration: none; border: none; transition: all .2s;
            white-space: nowrap;
        }
        .btn-primary {
            background: var(--primary); color: #fff;
            box-shadow: 0 2px 8px rgba(30,64,175,.35);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(30,64,175,.45); }
        .btn-success {
            background: var(--success); color: #fff;
            box-shadow: 0 2px 8px rgba(5,150,105,.3);
        }
        .btn-success:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(5,150,105,.4); }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 1.5px solid var(--primary);
        }
        .btn-outline:hover { background: #eff6ff; }
        .btn-disabled {
            background: #e2e8f0; color: #94a3b8; cursor: not-allowed; pointer-events: none;
        }
        .btn-receipt {
            background: linear-gradient(135deg, #0f766e, #059669);
            color: #fff; box-shadow: 0 2px 8px rgba(5,150,105,.35);
            width: 100%; justify-content: center; font-size: .87rem; padding: 11px;
        }
        .btn-receipt:hover { background: linear-gradient(135deg, #0d6663, #047857); transform: translateY(-1px); }

        /* ── PAYMENTS TABLE ── */
        table { width: 100%; border-collapse: collapse; font-size: .85rem; }
        thead { background: var(--surface-2); }
        thead th {
            padding: 10px 14px; text-align: left;
            font-size: .7rem; font-weight: 600; color: var(--text-sub);
            text-transform: uppercase; letter-spacing: .5px;
            border-bottom: 1px solid var(--border);
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fbff; }
        td { padding: 12px 14px; vertical-align: middle; }

        .pay-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: .7rem; font-weight: 700;
        }
        .pay-verified { background: var(--success-light); color: #065f46; }
        .pay-pending  { background: var(--warning-light); color: #92400e; }
        .pay-dot { width:5px; height:5px; border-radius:50%; background:currentColor; }

        .action-cell { text-align: center; }
        .btn-verify-sm {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--success); color: #fff;
            border: none; padding: 6px 12px; border-radius: 6px;
            font-size: .72rem; font-weight: 600; cursor: pointer;
            transition: all .2s; text-decoration: none;
        }
        .btn-verify-sm:hover { background: #047857; transform: translateY(-1px); }

        /* ── EMPTY ── */
        .empty-pay {
            padding: 32px; text-align: center;
            font-size: .85rem; color: var(--text-sub);
        }

        /* ── ALERT / NOTICE ── */
        .alert {
            border-radius: var(--radius-sm); padding: 12px 16px;
            font-size: .82rem; margin-bottom: 16px;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .alert-success { background: var(--success-light); color: #065f46; border-left: 4px solid var(--success); }
        .alert-warning { background: var(--warning-light); color: #92400e; border-left: 4px solid var(--warning); }
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
        <a href="/finance/queue">Antrean Tagihan</a>
        &rsaquo;
        <span>Rincian Transaksi #<?= htmlspecialchars($transaction['transaction_code']) ?></span>
    </div>

    <!-- TITLE ROW -->
    <div class="page-title-row">
        <h1>Rincian Tagihan Final</h1>
        <?php
            $status = $transaction['status'] ?? 'process';
            $statusClass = match($status) {
                'lunas'  => 's-lunas',
                'cancel' => 's-cancel',
                default  => 's-process',
            };
            $statusLabel = match($status) {
                'lunas'  => '✓ Lunas',
                'cancel' => '✗ Dibatalkan',
                default  => 'Proses',
            };
        ?>
        <span class="status-pill <?= $statusClass ?>">
            <span class="s-dot"></span>
            <?= $statusLabel ?>
        </span>
    </div>

    <div class="main-grid">

        <!-- ======= KOLOM KIRI ======= -->
        <div>

            <!-- INFO TRANSAKSI -->
            <div class="card">
                <div class="card-header">
                    <h2>📄 Informasi Transaksi</h2>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Kode Transaksi</span>
                            <span class="info-value bold" style="color:var(--primary)"><?= htmlspecialchars($transaction['transaction_code']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Dibuat</span>
                            <span class="info-value"><?= date('d F Y, H:i', strtotime($transaction['created_at'])) ?> WIB</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sales</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['sales_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Metode Bayar</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['payment_type_name']) ?></span>
                        </div>
                        <?php if ($invoice): ?>
                        <div class="info-row">
                            <span class="info-label">No. Invoice</span>
                            <span class="info-value bold"><?= htmlspecialchars($invoice['invoice_number']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- INFO PELANGGAN -->
            <div class="card">
                <div class="card-header">
                    <h2>👤 Data Pelanggan</h2>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value bold"><?= htmlspecialchars($transaction['customer_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['customer_phone'] ?? '-') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. KTP</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['customer_ktp'] ?? '-') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['customer_address'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INFO KENDARAAN -->
            <div class="card">
                <div class="card-header">
                    <h2>🚗 Data Kendaraan</h2>
                </div>
                <div class="card-body">
                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Merek &amp; Tipe</span>
                            <span class="info-value bold"><?= htmlspecialchars($transaction['vehicle_brand']) ?> <?= htmlspecialchars($transaction['vehicle_type']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Warna</span>
                            <span class="info-value"><?= htmlspecialchars($transaction['vehicle_color']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Rangka</span>
                            <span class="info-value" style="font-family:monospace"><?= htmlspecialchars($transaction['vehicle_chassis']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Mesin</span>
                            <span class="info-value" style="font-family:monospace"><?= htmlspecialchars($transaction['vehicle_engine']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Harga OTR</span>
                            <span class="info-value bold" style="color:var(--primary)">
                                Rp <?= number_format((float)$transaction['vehicle_price'], 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIWAYAT PEMBAYARAN -->
            <div class="card">
                <div class="card-header">
                    <h2>💳 Riwayat Pembayaran</h2>
                </div>

                <?php if (empty($payments)): ?>
                <div class="empty-pay">
                    Belum ada data pembayaran yang masuk untuk transaksi ini.
                </div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal Bayar</th>
                            <th style="text-align:right">Jumlah</th>
                            <th style="text-align:center">Status</th>
                            <th>Diverifikasi Oleh</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($payments as $i => $pay): ?>
                        <tr>
                            <td style="color:var(--text-sub);font-weight:500"><?= $i + 1 ?></td>
                            <td><?= date('d M Y', strtotime($pay['payment_date'])) ?></td>
                            <td style="text-align:right;font-weight:700">
                                Rp <?= number_format((float)$pay['amount'], 0, ',', '.') ?>
                            </td>
                            <td style="text-align:center">
                                <?php if ($pay['status'] === 'verified'): ?>
                                    <span class="pay-status pay-verified">
                                        <span class="pay-dot"></span> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="pay-status pay-pending">
                                        <span class="pay-dot"></span> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:.8rem">
                                <?= $pay['status'] === 'verified'
                                    ? htmlspecialchars($pay['verifier_name'] ?? 'Staff Finance')
                                    : '<span style="color:var(--text-sub)">—</span>' ?>
                            </td>
                            <td class="action-cell">
                                <?php if ($pay['status'] === 'pending'): ?>
                                    <!-- PBI-5.3: Tombol Verifikasi Pembayaran -->
                                    <form method="POST"
                                          action="/finance/payments/<?= (int)$pay['id'] ?>/verify"
                                          id="form-verify-<?= (int)$pay['id'] ?>"
                                          onsubmit="return confirm('Verifikasi pembayaran sebesar Rp <?= number_format((float)$pay['amount'], 0, ',', '.') ?>?')">
                                        <button type="submit"
                                                id="btn-verify-<?= (int)$pay['id'] ?>"
                                                class="btn-verify-sm">
                                            <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Verifikasi
                                        </button>
                                    </form>
                                <?php elseif ($pay['status'] === 'verified'): ?>
                                    <!-- PBI-5.7: Tombol Cetak Kwitansi per pembayaran -->
                                    <a href="/finance/payments/<?= (int)$pay['id'] ?>/receipt"
                                       id="btn-receipt-<?= (int)$pay['id'] ?>"
                                       class="btn-verify-sm"
                                       style="background:#0d9488;"
                                       title="Unduh Kwitansi PDF">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                        </svg>
                                        Kwitansi
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div><!-- .card payments -->

        </div><!-- kolom kiri -->


        <!-- ======= KOLOM KANAN (STICKY SUMMARY) ======= -->
        <div>

            <!-- BILLING SUMMARY BOX -->
            <div class="billing-box">
                <h2>📊 Ringkasan Tagihan</h2>

                <div class="billing-row">
                    <span class="bl">Total Tagihan</span>
                    <span class="bv">Rp <?= number_format($invoiceAmount, 0, ',', '.') ?></span>
                </div>
                <div class="billing-row">
                    <span class="bl">Total Terbayar</span>
                    <span class="bv" style="color:#86efac">Rp <?= number_format($totalPaid, 0, ',', '.') ?></span>
                </div>

                <hr class="billing-divider">

                <?php $isLunas = ($remainingBalance <= 0); ?>
                <div class="billing-total <?= $isLunas ? 'lunas' : 'hutang' ?>">
                    <span class="bl"><?= $isLunas ? 'Status' : 'Sisa Tagihan' ?></span>
                    <span class="bv">
                        <?= $isLunas ? '✓ LUNAS' : 'Rp ' . number_format($remainingBalance, 0, ',', '.') ?>
                    </span>
                </div>
            </div><!-- .billing-box -->

            <?php if ($isLunas): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <span>Seluruh tagihan telah terbayar dan lunas. Status transaksi otomatis diperbarui.</span>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <span>⚠️</span>
                <span>Masih terdapat sisa tagihan. Verifikasi pembayaran yang masuk untuk memperbarui status.</span>
            </div>
            <?php endif; ?>

            <!-- TOMBOL AKSI UTAMA -->
            <div class="card">
                <div class="card-header">
                    <h2>⚡ Aksi</h2>
                </div>
                <div class="card-body" style="display:flex;flex-direction:column;gap:10px">

                    <!-- Tombol kembali ke antrean -->
                    <a href="/finance/queue" id="btn-back-queue" class="btn btn-outline" style="justify-content:center">
                        ← Kembali ke Antrean
                    </a>

                    <!-- PBI-5.7: Tombol Cetak Kwitansi untuk pembayaran terverifikasi terakhir -->
                    <?php
                        $lastVerifiedPayment = null;
                        foreach (array_reverse($payments) as $p) {
                            if ($p['status'] === 'verified') {
                                $lastVerifiedPayment = $p;
                                break;
                            }
                        }
                    ?>
                    <?php if ($lastVerifiedPayment): ?>
                    <a href="/finance/payments/<?= (int)$lastVerifiedPayment['id'] ?>/receipt"
                       id="btn-download-last-receipt"
                       class="btn btn-receipt">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h4a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Cetak Kwitansi Terakhir (PDF)
                    </a>
                    <?php else: ?>
                    <span class="btn btn-disabled" style="justify-content:center">
                        Belum Ada Pembayaran Terverifikasi
                    </span>
                    <?php endif; ?>

                </div>
            </div><!-- .card aksi -->

        </div><!-- kolom kanan -->

    </div><!-- .main-grid -->

</div><!-- .page-wrapper -->

</body>
</html>
