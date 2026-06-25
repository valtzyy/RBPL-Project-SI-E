<!-- app/views/bengkel/service_advisor_detail.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
        margin: 0;
        padding: 0;
    }
/* 
    /* .container {
        padding: 40px 20px;
        max-width: 1000px;
        margin: 0 auto;
    } */ */

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 24px;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #3730a3;
    }

    .card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 30px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 20px;
        color: #0f172a;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media (min-width: 768px) {
        .detail-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .detail-group {
        margin-bottom: 8px;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 500;
        color: #1e293b;
    }

    .plate-badge {
        background: #e0f2fe;
        color: #0369a1;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .history-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .history-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }

    .history-table tr:last-child td {
        border-bottom: none;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
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
</style>

<div class="container">
    <a href="/service-advisor/work-orders" class="back-link">
        <span>←</span> Kembali ke Monitoring Work Orders
    </a>

    <!-- Detail Work Order -->
    <div class="card">
        <h3 class="card-title">📋 Detail Work Order #<?= htmlspecialchars($order['id']) ?></h3>
        
        <div class="detail-grid">
            <div class="detail-group">
                <div class="detail-label">Pelanggan</div>
                <div class="detail-value"><?= htmlspecialchars($order['customer_name']) ?></div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Model Kendaraan</div>
                <div class="detail-value"><?= htmlspecialchars($order['vehicle_model']) ?> (Warna: <?= htmlspecialchars($order['vehicle_color']) ?>)</div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Plat Nomor</div>
                <div class="detail-value">
                    <span class="plate-badge"><?= htmlspecialchars($order['license_plate']) ?></span>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Mekanik Penanggung Jawab</div>
                <div class="detail-value" style="font-weight: 600; color: #4f46e5;">
                    <?= htmlspecialchars($order['mechanic_name'] ?? 'Belum Ditugaskan') ?>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Tanggal Booking / Masuk</div>
                <div class="detail-value"><?= date('d F Y - H:i', strtotime($order['booking_date'] ?? $order['created_at'])) ?></div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Status Utama</div>
                <div class="detail-value">
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
                    <span class="badge <?= $mainBadgeClass ?>"><?= $mainStatusLabel ?></span>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Status Pengerjaan Terakhir</div>
                <div class="detail-value">
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
                    }
                    ?>
                    <span class="badge <?= $logBadgeClass ?>"><?= $logStatusLabel ?></span>
                </div>
            </div>
        </div>

        <div class="detail-group" style="margin-top: 20px;">
            <div class="detail-label">Keluhan Teknis / Deskripsi Pekerjaan</div>
            <div class="detail-value" style="background-color: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; color: #475569; min-height: 80px; white-space: pre-wrap;"><?= htmlspecialchars($order['description'] ?? 'Tidak ada keluhan tambahan.') ?></div>
        </div>
    </div>

    <!-- Tabel Riwayat Log -->
    <div class="card">
        <h3 class="card-title">⏱️ Riwayat Progres Pengerjaan (Log)</h3>
        
        <?php if (empty($logs)): ?>
            <p style="text-align: center; color: #64748b; padding: 20px 0;">Belum ada riwayat aktivitas log untuk work order ini.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 25%;">Waktu Log</th>
                            <th style="width: 20%;">Status Log</th>
                            <th style="width: 50%;">Catatan & Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td style="font-weight: 500; color: #475569;">
                                    <?= date('d M Y - H:i', strtotime($log['created_at'])) ?>
                                </td>
                                <td>
                                    <?php
                                    $logBadgeClass = 'badge-in-progress';
                                    $logStatusLabel = 'Dikerjakan';

                                    switch ($log['status']) {
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
                                        case 'in_progress':
                                            $logBadgeClass = 'badge-in-progress';
                                            $logStatusLabel = 'In Progress';
                                            break;
                                        case 'ready':
                                            $logBadgeClass = 'badge-ready';
                                            $logStatusLabel = 'Ready';
                                            break;
                                        case 'done':
                                            $logBadgeClass = 'badge-done';
                                            $logStatusLabel = 'Done';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $logBadgeClass ?>"><?= $logStatusLabel ?></span>
                                </td>
                                <td style="white-space: pre-wrap; line-height: 1.5; color: #334155;"><?= htmlspecialchars($log['notes']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
