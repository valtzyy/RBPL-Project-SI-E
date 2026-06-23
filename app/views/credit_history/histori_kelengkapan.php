<?php
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }

    function formatTanggal(string $tanggal): string
    {
        $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $ts = strtotime($tanggal);

        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }

    function badgeClassFor(string $status): string
    {
        return match ($status) {
            'disetujui' => 'badge--success',
            'diverifikasi', 'diajukan' => 'badge--warning',
            'ditolak' => 'badge--danger',
            default => 'badge--warning',
        };
    }

    function badgeLabelFor(string $status): string
    {
        return match ($status) {
            'disetujui' => 'Lunas',
            'diverifikasi' => 'Proses',
            'diajukan' => 'Diajukan',
            'ditolak' => 'Batal',
            default => ucfirst($status),
        };
    }

    function docIconClassFor(string $status): string
    {
        return match ($status) {
            'lengkap' => 'doc-row__icon--ok',
            'menunggu' => 'doc-row__icon--wait',
            'kurang' => 'doc-row__icon--miss',
            default => 'doc-row__icon--wait',
        };
    }

    function docIconSymbolFor(string $status): string
    {
        return match ($status) {
            'lengkap' => '&check;',
            'menunggu' => '...',
            'kurang' => '!',
            default => '?',
        };
    }

    function docActionLabelFor(string $status): string
    {
        return match ($status) {
            'lengkap' => 'Lihat File',
            'menunggu' => 'Cek Status',
            'kurang' => 'Minta Susulan',
            default => 'Lihat',
        };
    }
}

if (!isset($profile, $applications, $selected, $stats, $customerId)) {
    require_once __DIR__ . '/../../Models/CreditHistoryModel.php';

    $model = new CreditHistoryModel();
    $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 1;
    $applicationId = isset($_GET['application_id']) ? (int) $_GET['application_id'] : null;
    $profile = $model->getCustomerProfile($customerId);
    $applications = $model->getCreditApplicationsByCustomer($customerId);
    $selected = $model->findSelectedApplication($applications, $applicationId);
    $stats = $model->getApplicationStats($applications);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Kredit - <?= htmlspecialchars($profile['nama']) ?> | DealerLink</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-logo">DealerLink</div>

        <div class="sidebar-user">
            <div class="avatar">FS</div>
            <div class="user-info">
                <span>Finance Staff</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="#">
                <i class="fa-solid fa-table-columns"></i>
                <span>Dashboard</span>
            </a>
            <a href="#">
                <i class="fa-regular fa-credit-card"></i>
                <span>Pembayaran Tunai</span>
            </a>
            <a href="#" class="active">
                <i class="fa-solid fa-building-columns"></i>
                <span>Kredit & Leasing</span>
            </a>
            <a href="#">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>Pembayaran Servis</span>
            </a>
            <a href="#">
                <i class="fa-regular fa-file-lines"></i>
                <span>Riwayat Transaksi</span>
            </a>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <a href="customer.php" class="topbar__back">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2>Histori Kelengkapan File Kredit</h2>
                    <span><?= htmlspecialchars($profile['nama']) ?></span>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="stat-row">
                <div class="stat-card">
                    <span class="badge badge--warning stat-card__badge">Proses</span>
                    <div class="stat-card__number"><?= $stats['total_menunggu'] ?></div>
                    <div class="stat-card__label">Pengajuan perlu diverifikasi</div>
                </div>
                <div class="stat-card">
                    <span class="badge badge--success stat-card__badge">Lunas</span>
                    <div class="stat-card__number"><?= $stats['total_disetujui'] ?></div>
                    <div class="stat-card__label">Kredit disetujui & berjalan</div>
                </div>
                <div class="stat-card stat-card--dark">
                    <div class="stat-card__label">Total Nilai Kredit Aktif</div>
                    <div class="stat-card__number"><?= formatRupiah($stats['total_nilai_aktif']) ?></div>
                    <span class="stat-card__delta">&uarr; <?= $stats['total_pengajuan'] ?> pengajuan tercatat</span>
                </div>
            </div>

            <div class="section-head">
                <h2>Riwayat Pengajuan Kredit</h2>
                <span class="hint">Klik salah satu pengajuan untuk melihat detail kelengkapan dokumen</span>
            </div>

            <div class="credit-layout">
                <div class="timeline-card">
                    <div class="timeline-card__search">
                        <input type="text" placeholder="Cari No. Pengajuan / Kode..." disabled>
                    </div>

                    <?php if (empty($applications)): ?>
                        <div class="empty-state">
                            <div class="ic"><i class="fa-regular fa-folder-open"></i></div>
                            <p>Customer ini belum pernah mengajukan kredit.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline-list">
                            <?php foreach ($applications as $application): ?>
                                <?php
                                    $isSelected = isset($selected) && $selected && $selected['id'] === $application['id'];
                                    $linkUrl = '?customer_id=' . $customerId . '&application_id=' . $application['id'];
                                ?>
                                <a href="<?= htmlspecialchars($linkUrl) ?>">
                                    <div class="timeline-item <?= $isSelected ? 'is-selected' : '' ?>">
                                        <div class="timeline-item__top">
                                            <span class="timeline-item__code"><?= htmlspecialchars($application['kode_pengajuan']) ?></span>
                                            <span class="badge <?= badgeClassFor($application['status']) ?>">
                                                <?= badgeLabelFor($application['status']) ?>
                                            </span>
                                        </div>
                                        <div class="timeline-item__date"><?= formatTanggal($application['tanggal_ajuan']) ?></div>
                                        <div class="timeline-item__veh"><?= htmlspecialchars($application['kendaraan']) ?></div>
                                        <div class="timeline-item__amount"><?= formatRupiah($application['harga_kendaraan']) ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="timeline-card__footer">
                            Menampilkan <?= count($applications) ?> dari <?= count($applications) ?> pengajuan
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <?php if (isset($selected) && $selected): ?>
                        <?php
                            $application = $selected;
                            $totalDoc = count($application['dokumen']);
                            $lengkapCount = count(array_filter($application['dokumen'], fn($document) => $document['status'] === 'lengkap'));
                        ?>
                        <div class="detail-card">
                            <div class="detail-card__back">
                                <span>&larr;</span> <?= htmlspecialchars($application['kode_pengajuan']) ?>
                            </div>

                            <div class="detail-card__header">
                                <div class="detail-card__title">Detail Pengajuan Kredit</div>
                                <span class="badge <?= badgeClassFor($application['status']) ?>"><?= badgeLabelFor($application['status']) ?></span>
                            </div>

                            <div class="total-highlight">
                                <div class="total-highlight__label">Harga Kendaraan</div>
                                <div class="total-highlight__value"><?= formatRupiah($application['harga_kendaraan']) ?></div>
                                <div class="total-highlight__meta">
                                    <div>
                                        Uang Muka (DP)
                                        <b><?= formatRupiah($application['dp']) ?></b>
                                    </div>
                                    <div style="text-align:right;">
                                        Tenor
                                        <b><?= $application['tenor_bulan'] > 0 ? $application['tenor_bulan'] . ' Bulan' : '-' ?></b>
                                    </div>
                                </div>
                            </div>

                            <div class="info-two-col">
                                <div class="info-section">
                                    <div class="info-section__title">Informasi Pengajuan</div>
                                    <div class="info-row"><span class="label">No. Pengajuan</span><span class="value"><?= htmlspecialchars($application['kode_pengajuan']) ?></span></div>
                                    <div class="info-row"><span class="label">Tanggal Ajuan</span><span class="value"><?= formatTanggal($application['tanggal_ajuan']) ?></span></div>
                                    <div class="info-row"><span class="label">Leasing</span><span class="value"><?= htmlspecialchars($application['leasing']) ?></span></div>
                                </div>
                                <div class="info-section">
                                    <div class="info-section__title">Customer &amp; Kendaraan</div>
                                    <div class="info-row"><span class="label">Nama Customer</span><span class="value"><?= htmlspecialchars($profile['nama']) ?></span></div>
                                    <div class="info-row"><span class="label">Kendaraan</span><span class="value"><?= htmlspecialchars($application['kendaraan']) ?></span></div>
                                    <div class="info-row"><span class="label">Sales</span><span class="value"><?= htmlspecialchars($application['sales']) ?></span></div>
                                </div>
                            </div>

                            <div class="doc-panel">
                                <div class="doc-panel__head">
                                    <h3>Kelengkapan Dokumen</h3>
                                    <span class="doc-panel__count"><?= $lengkapCount ?> / <?= $totalDoc ?> lengkap</span>
                                </div>
                                <div class="doc-list">
                                    <?php foreach ($application['dokumen'] as $document): ?>
                                        <div class="doc-row">
                                            <div class="doc-row__icon <?= docIconClassFor($document['status']) ?>">
                                                <?= docIconSymbolFor($document['status']) ?>
                                            </div>
                                            <div class="doc-row__body">
                                                <div class="doc-row__name"><?= htmlspecialchars($document['nama']) ?></div>
                                                <div class="doc-row__note"><?= htmlspecialchars($document['catatan']) ?></div>
                                            </div>
                                            <button type="button" class="doc-row__action">
                                                <?= docActionLabelFor($document['status']) ?>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($application['status'] === 'disetujui'): ?>
                                <div class="decision-note decision-note--success">
                                    <strong>Catatan Keputusan</strong><?= htmlspecialchars($application['catatan_keputusan']) ?>
                                </div>
                            <?php elseif ($application['status'] === 'ditolak'): ?>
                                <div class="decision-note decision-note--danger">
                                    <strong>Catatan Keputusan</strong><?= htmlspecialchars($application['catatan_keputusan']) ?>
                                </div>
                                <button type="button" class="action-confirm" style="background:var(--color-text-soft);">Ajukan Ulang Pengajuan</button>
                            <?php else: ?>
                                <button type="button" class="action-confirm">Konfirmasi Kelengkapan Dokumen</button>
                                <button type="button" class="action-cancel">Batalkan Pengajuan</button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="detail-card">
                            <div class="empty-state">
                                <div class="ic"><i class="fa-regular fa-folder-open"></i></div>
                                <p>Pilih salah satu pengajuan di sebelah kiri untuk melihat detailnya.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
