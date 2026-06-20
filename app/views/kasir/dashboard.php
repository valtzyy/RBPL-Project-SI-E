<?php
// app/views/kasir/dashboard.php

$title      = $title      ?? 'Dashboard Kasir';
$activePage = $activePage ?? 'dashboard';
$ringkasan  = $ringkasan  ?? ['pending' => 0, 'lunas_hari_ini' => 0, 'pemasukan_hari_ini' => 0, 'wo_aktif' => 0];
$tagihanTerbaru = $tagihanTerbaru ?? [];

function rupiahDash(float $n): string
{
    if ($n >= 1_000_000) {
        return 'Rp ' . number_format($n / 1_000_000, 1, ',', '.') . ' Jt';
    }
    return 'Rp ' . number_format($n, 0, ',', '.');
}
function rupiahFull(float $n): string
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
    <style>
        /* ---- Stat Cards ---- */
        .dl-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .dl-stat-card {
            background: var(--clr-surface);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-card);
            padding: 20px 22px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dl-stat-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .dl-stat-card__label {
            font-size: 12px;
            font-weight: 600;
            color: var(--clr-text-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .dl-stat-card__icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dl-stat-card__icon--teal {
            background: rgba(45, 212, 167, .12);
            color: var(--clr-accent-teal);
        }

        .dl-stat-card__icon--blue {
            background: rgba(79, 142, 247, .12);
            color: var(--clr-accent-blue);
        }

        .dl-stat-card__icon--amber {
            background: rgba(251, 191, 36, .12);
            color: #f59e0b;
        }

        .dl-stat-card__icon--red {
            background: rgba(239, 68, 68, .12);
            color: #ef4444;
        }

        .dl-stat-card__value {
            font-size: 28px;
            font-weight: 700;
            color: var(--clr-text-primary);
            letter-spacing: -.5px;
            line-height: 1;
        }

        .dl-stat-card__value--teal {
            color: var(--clr-accent-teal);
        }

        .dl-stat-card__value--amber {
            color: #f59e0b;
        }

        .dl-stat-card__value--red {
            color: #ef4444;
        }

        .dl-stat-card__sub {
            font-size: 12px;
            color: var(--clr-text-muted);
            margin-top: 2px;
        }

        /* ---- Section heading ---- */
        .dl-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .dl-section-head__title {
            font-size: 15px;
            font-weight: 700;
            color: var(--clr-text-primary);
        }

        .dl-section-head__link {
            font-size: 12.5px;
            color: var(--clr-accent-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .dl-section-head__link:hover {
            text-decoration: underline;
        }

        /* ---- Greeting bar ---- */
        .dl-greeting {
            margin-bottom: 24px;
        }

        .dl-greeting__title {
            font-size: 22px;
            font-weight: 700;
            color: var(--clr-text-primary);
            letter-spacing: -.3px;
        }

        .dl-greeting__sub {
            font-size: 13px;
            color: var(--clr-text-secondary);
            margin-top: 3px;
        }

        /* ---- Empty state ---- */
        .dl-empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--clr-text-muted);
            font-size: 13.5px;
        }

        @media (max-width: 1024px) {
            .dl-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .dl-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="dl-shell">

        <?php
        $pendingCount = $ringkasan['pending'];
        include __DIR__ . '/_sidebar.php';
        ?>

        <div class="dl-main">

            <!-- Topbar -->
            <header class="dl-topbar">
                <div>
                    <div class="dl-topbar__title">Dashboard</div>
                    <div class="dl-topbar__breadcrumb">Kasir Bengkel</div>
                </div>
                <div class="dl-topbar__right">
                    <span style="font-size:13px;color:var(--clr-text-muted);">
                        <?= date('l, d F Y') ?>
                    </span>
                </div>
            </header>

            <main class="dl-page">

                <!-- Greeting -->
                <div class="dl-greeting">
                    <div class="dl-greeting__title">
                        Selamat datang, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Kasir') ?> 👋
                    </div>
                    <div class="dl-greeting__sub">
                        Berikut ringkasan aktivitas kasir bengkel hari ini,
                        <?= date('d M Y') ?>.
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="dl-stats">

                    <!-- Menunggu Bayar -->
                    <div class="dl-stat-card">
                        <div class="dl-stat-card__top">
                            <span class="dl-stat-card__label">Menunggu Bayar</span>
                            <span class="dl-stat-card__icon dl-stat-card__icon--red">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                            </span>
                        </div>
                        <div class="dl-stat-card__value dl-stat-card__value--red">
                            <?= $ringkasan['pending'] ?>
                        </div>
                        <div class="dl-stat-card__sub">Tagihan belum dilunasi</div>
                    </div>

                    <!-- Lunas Hari Ini -->
                    <div class="dl-stat-card">
                        <div class="dl-stat-card__top">
                            <span class="dl-stat-card__label">Lunas Hari Ini</span>
                            <span class="dl-stat-card__icon dl-stat-card__icon--teal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                        </div>
                        <div class="dl-stat-card__value dl-stat-card__value--teal">
                            <?= $ringkasan['lunas_hari_ini'] ?>
                        </div>
                        <div class="dl-stat-card__sub">Transaksi selesai dibayar</div>
                    </div>

                    <!-- Pemasukan Hari Ini -->
                    <div class="dl-stat-card">
                        <div class="dl-stat-card__top">
                            <span class="dl-stat-card__label">Pemasukan Hari Ini</span>
                            <span class="dl-stat-card__icon dl-stat-card__icon--blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <line x1="12" y1="1" x2="12" y2="23" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                            </span>
                        </div>
                        <div class="dl-stat-card__value" style="font-size:22px;">
                            <?= rupiahDash($ringkasan['pemasukan_hari_ini']) ?>
                        </div>
                        <div class="dl-stat-card__sub">Total pembayaran diterima</div>
                    </div>

                    <!-- WO Aktif -->
                    <div class="dl-stat-card">
                        <div class="dl-stat-card__top">
                            <span class="dl-stat-card__label">WO Aktif</span>
                            <span class="dl-stat-card__icon dl-stat-card__icon--amber">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                                </svg>
                            </span>
                        </div>
                        <div class="dl-stat-card__value dl-stat-card__value--amber">
                            <?= $ringkasan['wo_aktif'] ?>
                        </div>
                        <div class="dl-stat-card__sub">Sedang dikerjakan / menunggu</div>
                    </div>

                </div><!-- /dl-stats -->

                <!-- Tabel Tagihan Pending Terbaru -->
                <div class="dl-section-head">
                    <span class="dl-section-head__title">Tagihan Menunggu Pembayaran</span>
                    <a href="/service-billing" class="dl-section-head__link">Lihat Semua →</a>
                </div>

                <div class="dl-card">
                    <?php if (empty($tagihanTerbaru)): ?>
                        <div class="dl-empty">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:10px;color:var(--clr-text-muted)">
                                <path d="M4 2v20l3-2 3 2 3-2 3 2 3-2V2" />
                                <path d="M8 7h8M8 11h8M8 15h4" />
                            </svg>
                            <div>Tidak ada tagihan yang menunggu pembayaran saat ini.</div>
                        </div>
                    <?php else: ?>
                        <div class="dl-table-wrap">
                            <table class="dl-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pelanggan</th>
                                        <th>Kendaraan</th>
                                        <th>Masuk</th>
                                        <th class="right">Grand Total</th>
                                        <th class="center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tagihanTerbaru as $i => $t): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($t['customer_name']) ?></div>
                                            </td>
                                            <td>
                                                <div class="td-name"><?= htmlspecialchars($t['brand'] . ' ' . $t['vehicle_type']) ?></div>
                                            </td>
                                            <td style="color:var(--clr-text-muted);font-size:13px;">
                                                <?= date('d M, H:i', strtotime($t['wo_created_at'])) ?>
                                            </td>
                                            <td class="right td-total" style="color:var(--clr-accent-teal);">
                                                <?= rupiahFull((float)$t['grand_total']) ?>
                                            </td>
                                            <td class="center">
                                                <a href="/service-billing?highlight=<?= (int)$t['work_order_id'] ?>"
                                                    class="dl-btn dl-btn--detail" style="font-size:12px;padding:7px 14px;">
                                                    Proses
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

</body>

</html>