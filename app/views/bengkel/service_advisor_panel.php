<!-- app/views/bengkel/service_advisor_panel.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
        margin: 0;
        padding: 0;
    }

    .header-section {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2), 0 8px 10px -6px rgba(79, 70, 229, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-title h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-title p {
        margin: 5px 0 0 0;
        color: #e0e7ff;
        font-size: 14px;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #ffffff;
        padding: 24px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .icon-yellow {
        background: #fffbeb;
        color: #d97706;
    }

    .icon-green {
        background: #f0fdf4;
        color: #166534;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        margin-top: 2px;
    }

    .card-table-wrapper {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .modern-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.8px;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .modern-table td {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        vertical-align: middle;
    }

    .modern-table tr:last-child td {
        border-bottom: none;
    }

    .modern-table tr:hover {
        background-color: #f8fafc;
    }

    .wo-id {
        font-weight: 700;
        color: #0f172a;
        background: #f1f5f9;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-block;
    }

    .vehicle-info strong {
        font-size: 15px;
        color: #0f172a;
        display: block;
    }

    .plate-badge {
        background: #e0f2fe;
        color: #0369a1;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
        margin-top: 4px;
    }

    .customer-name {
        font-weight: 600;
        color: #334155;
    }

    .mechanic-name {
        font-weight: 600;
        color: #4f46e5;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .badge-started {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .badge-paused {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .badge-checked {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .badge-rework {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .badge-closed {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-in-progress {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-done {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-ready {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-detail {
        background-color: #4f46e5;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
    }

    .btn-detail:hover {
        background-color: #3730a3;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15);
    }

    .no-data-card {
        text-align: center;
        padding: 60px 40px;
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        color: #64748b;
    }

    .no-data-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
</style>

<div>
    <div class="header-section">
        <div class="header-title">
            <h2>🔧 Monitoring Work Orders</h2>
            <p>Daftar seluruh instruksi kerja aktif dan riwayat penanganan unit di bengkel.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">📋</div>
            <div class="stat-info">
                <span class="stat-value"><?= count($orders) ?></span>
                <span class="stat-label">Total Work Order</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-yellow">⏳</div>
            <div class="stat-info">
                <span class="stat-value"><?= $totalHandled ?></span>
                <span class="stat-label">Kendaraan Ditangani</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green">✅</div>
            <div class="stat-info">
                <span class="stat-value"><?= $totalCompleted ?></span>
                <span class="stat-label">Servis Terselesaikan</span>
            </div>
        </div>
    </div>

    <?php if (empty($orders)): ?>
        <div class="no-data-card">
            <div class="no-data-icon">📦</div>
            <h3>Tidak ada data Work Order</h3>
            <p>Belum ada data tugas aktif di bengkel saat ini.</p>
        </div>
    <?php else: ?>
        <div class="card-table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID WO</th>
                        <th style="width: 20%;">Detail Kendaraan</th>
                        <th style="width: 15%;">Pelanggan</th>
                        <th style="width: 15%;">Mekanik</th>
                        <th style="width: 17%;">Keluhan Teknis</th>
                        <th style="width: 10%;">Status Utama</th>
                        <th style="width: 10%;">Status Pengerjaan</th>
                        <th style="width: 5%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <span class="wo-id">#<?= htmlspecialchars($order['id']) ?></span>
                            </td>
                            <td>
                                <div class="vehicle-info">
                                    <strong><?= htmlspecialchars($order['vehicle_model']) ?></strong>
                                    <span class="plate-badge"><?= htmlspecialchars($order['license_plate']) ?></span>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Warna: <?= htmlspecialchars($order['vehicle_color']) ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></span>
                            </td>
                            <td>
                                <span class="mechanic-name"><?= htmlspecialchars($order['mechanic_name'] ?? 'Belum Ditugaskan') ?></span>
                            </td>
                            <td>
                                <div style="max-height: 80px; overflow-y: auto; font-size: 13px; color: #475569;">
                                    <?= htmlspecialchars($order['description'] ?? 'Tidak ada catatan keluhan') ?>
                                </div>
                            </td>
                            <!-- Status Utama -->
                            <td>
                                <?php
                                $mainStatus = $order['status'];
                                $mainBadgeClass = 'badge-in-progress';
                                $mainStatusLabel = 'In Progress';
                                if ($mainStatus === 'done') {
                                    $mainBadgeClass = 'badge-done';
                                    $mainStatusLabel = 'Done';
                                } elseif ($mainStatus === 'ready') {
                                    $mainBadgeClass = 'badge-ready';
                                    $mainStatusLabel = 'Ready';
                                }
                                ?>
                                <span class="badge <?= $mainBadgeClass ?>">
                                    <?= $mainStatusLabel ?>
                                </span>
                            </td>
                            <!-- Status Pengerjaan (Log Terakhir) -->
                            <td>
                                <?php
                                $logStatus = $order['latest_log_status'];
                                $logBadgeClass = 'badge-paused';
                                $logStatusLabel = 'Belum Mulai';

                                switch ($logStatus) {
                                    case 'started':
                                        $logBadgeClass = 'badge-started';
                                        $logStatusLabel = 'Started';
                                        break;
                                    case 'paused':
                                        $logBadgeClass = 'badge-paused';
                                        $logStatusLabel = 'Paused';
                                        break;
                                    case 'checked':
                                        $logBadgeClass = 'badge-checked';
                                        $logStatusLabel = 'Checked';
                                        break;
                                    case 'rework':
                                        $logBadgeClass = 'badge-rework';
                                        $logStatusLabel = 'Rework';
                                        break;
                                    case 'closed':
                                        $logBadgeClass = 'badge-closed';
                                        $logStatusLabel = 'Closed';
                                        break;
                                    default:
                                        $logBadgeClass = 'badge-paused';
                                        $logStatusLabel = 'Belum Mulai';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $logBadgeClass ?>">
                                    <?= $logStatusLabel ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="/work-orders/<?= $order['id'] ?>" class="btn-detail">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
