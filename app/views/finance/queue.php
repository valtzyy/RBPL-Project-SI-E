<?php
$total_transactions = 0;
$pending_payments = 0;
$verified_payments = 0;

if (!empty($transactions)) {
    foreach ($transactions as $trx) {
        if (empty($trx['payment_id'])) continue;
        $total_transactions++;
        if (($trx['payment_status'] ?? 'pending') === 'verified') {
            $verified_payments++;
        } else {
            $pending_payments++;
        }
    }
}
?>

<style>
    .finance-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 8px 4px;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .dashboard-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 4px 0;
        letter-spacing: -0.02em;
    }
    
    .dashboard-header p {
        font-size: 14px;
        color: #6B7280;
        margin: 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    
    .stat-card.blue::before { background-color: #4F5BD5; }
    .stat-card.yellow::before { background-color: #FBBF24; }
    .stat-card.green::before { background-color: #10B981; }
    
    .stat-title {
        font-size: 12px;
        font-weight: 700;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #111827;
        margin: 0;
    }

    /* Alert Banner */
    .alert-banner {
        background: #DEF7EC;
        border: 1px solid #BCF0DA;
        color: #03543F;
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Table Container Card */
    .table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Table Styling */
    .finance-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    
    .finance-table th {
        background-color: #F9FAFB;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 12px;
        color: #4B5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #E5E7EB;
    }
    
    .finance-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
        color: #374151;
    }
    
    .finance-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .finance-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    /* Utility Classes */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-mono { font-family: monospace; font-weight: 600; }
    
    .trx-code {
        background-color: #F3F4F6;
        color: #1F2937;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #E5E7EB;
        display: inline-block;
    }

    .vehicle-info {
        display: flex;
        flex-direction: column;
    }
    
    .vehicle-brand {
        font-weight: 700;
        color: #111827;
    }
    
    .vehicle-meta {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }
    
    .badge-pending {
        background-color: #FEF3C7;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    
    .badge-verified {
        background-color: #D1FAE5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    /* Action Button */
    .btn-detail {
        display: inline-block;
        padding: 8px 16px;
        background-color: #4F5BD5;
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        border-radius: 8px;
        transition: all 0.15s ease;
        text-align: center;
        box-shadow: 0 1px 2px rgba(79, 91, 213, 0.1);
    }
    
    .btn-detail:hover {
        background-color: #3B47B8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(79, 91, 213, 0.15);
    }

    .empty-state {
        color: #6B7280;
        padding: 48px 20px !important;
        font-style: italic;
    }
</style>

<div class="finance-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>💵 Antrean Verifikasi Pembayaran</h2>
            <p>Role: <strong>Finance</strong> — Verifikasi pembayaran tunai dari transaksi Sales.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert-banner">
            <span>✅</span> Pembayaran berhasil diverifikasi dan status transaksi telah diubah menjadi <strong>Lunas</strong>.
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <span class="stat-title">Total Pembayaran</span>
            <p class="stat-value"><?= $total_transactions ?> Unit</p>
        </div>
        <div class="stat-card yellow">
            <span class="stat-title">Menunggu Verifikasi</span>
            <p class="stat-value"><?= $pending_payments ?> Unit</p>
        </div>
        <div class="stat-card green">
            <span class="stat-title">Telah Diverifikasi</span>
            <p class="stat-value"><?= $verified_payments ?> Unit</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kode Transaksi</th>
                        <th>Customer</th>
                        <th>Kendaraan</th>
                        <th class="text-right">Jumlah Bayar</th>
                        <th>Tgl Bayar</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($transactions as $trx): ?>
                            <?php if (empty($trx['payment_id'])) continue; ?>
                            <tr>
                                <td class="text-center text-muted"><?= $no++ ?></td>
                                <td>
                                    <span class="trx-code font-mono"><?= htmlspecialchars($trx['transaction_code'] ?? '-') ?></span>
                                </td>
                                <td class="font-semibold"><?= htmlspecialchars($trx['customer_name'] ?? '-') ?></td>
                                <td>
                                    <div class="vehicle-info">
                                        <span class="vehicle-brand"><?= htmlspecialchars(($trx['brand'] ?? '') . ' ' . ($trx['type'] ?? '')) ?></span>
                                        <span class="vehicle-meta">Warna: <?= htmlspecialchars($trx['color'] ?? '-') ?></span>
                                    </div>
                                </td>
                                <td class="text-right font-semibold" style="color: #0F172A;">
                                    Rp <?= number_format($trx['payment_amount'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td><?= htmlspecialchars($trx['payment_date'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?php if (($trx['payment_status'] ?? 'pending') === 'verified'): ?>
                                        <span class="badge badge-verified">✅ Verified</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">⏳ Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="/finance/payments/<?= $trx['payment_id'] ?>" class="btn-detail">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center empty-state">Belum ada data pembayaran.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
