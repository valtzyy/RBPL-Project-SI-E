<!-- app/views/bengkel/add_log.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
        margin: 0;
        padding: 0;
    }

    .container {
        padding: 40px 20px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #6366f1;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 24px;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #4338ca;
    }

    .grid-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }

    @media (min-width: 768px) {
        .grid-layout {
            grid-template-columns: 1fr 1fr;
        }
    }

    .card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        padding: 24px;
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

    .detail-group {
        margin-bottom: 16px;
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

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-select, .form-textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        font-family: inherit;
        background-color: #ffffff;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-select:focus, .form-textarea:focus {
        border-color: #6366f1;
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .btn-submit {
        background-color: #6366f1;
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
    }

    .btn-submit:hover {
        background-color: #4f46e5;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #dcfce7;
    }

    .alert-danger {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fee2e2;
    }

    .history-card {
        margin-top: 30px;
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
    <a href="/mechanic/panel" class="back-link">
        <span>←</span> Kembali ke Panel Kerja
    </a>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success">
                ✅ Log berhasil ditambahkan dan status diperbarui secara real-time!
            </div>
        <?php elseif ($_GET['status'] === 'failed'): ?>
            <div class="alert alert-danger">
                🚨 Gagal menambahkan log. Silakan periksa kembali isian Anda.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="grid-layout">
        <!-- Detail Work Order -->
        <div class="card">
            <h3 class="card-title">📋 Detail Work Order #<?= htmlspecialchars($order['id']) ?></h3>
            
            <div class="detail-group">
                <div class="detail-label">Pelanggan</div>
                <div class="detail-value"><?= htmlspecialchars($order['customer_name']) ?></div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Model Kendaraan</div>
                <div class="detail-value"><?= htmlspecialchars($order['vehicle_model']) ?> (<?= htmlspecialchars($order['vehicle_color']) ?>)</div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Plat Nomor</div>
                <div class="detail-value">
                    <span class="plate-badge"><?= htmlspecialchars($order['license_plate']) ?></span>
                </div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Keluhan Teknis</div>
                <div class="detail-value" style="background-color: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13px; color: #475569; min-height: 60px; white-space: pre-wrap;"><?= htmlspecialchars($order['description'] ?? 'Tidak ada keluhan tambahan.') ?></div>
            </div>

            <div class="detail-group">
                <div class="detail-label">Status Saat Ini</div>
                <div class="detail-value">
                    <?php
                    $currentStatus = $order['latest_log_status'] ?: $order['status'];
                    $badgeClass = 'badge-in-progress';
                    $statusLabel = 'Dikerjakan';

                    switch ($currentStatus) {
                        case 'started':
                            $badgeClass = 'badge-started';
                            $statusLabel = 'Started';
                            break;
                        case 'paused':
                            $badgeClass = 'badge-paused';
                            $statusLabel = 'Paused';
                            break;
                        case 'checked':
                            $badgeClass = 'badge-checked';
                            $statusLabel = 'Checked';
                            break;
                        case 'rework':
                            $badgeClass = 'badge-rework';
                            $statusLabel = 'Rework';
                            break;
                        case 'closed':
                            $badgeClass = 'badge-closed';
                            $statusLabel = 'Closed';
                            break;
                        case 'in_progress':
                            $badgeClass = 'badge-in-progress';
                            $statusLabel = 'In Progress';
                            break;
                        case 'ready':
                            $badgeClass = 'badge-ready';
                            $statusLabel = 'Ready';
                            break;
                        case 'done':
                            $badgeClass = 'badge-done';
                            $statusLabel = 'Done';
                            break;
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= $statusLabel ?></span>
                </div>
            </div>
        </div>

        <!-- Form Tambah Log -->
        <div class="card">
            <h3 class="card-title">📝 Tambah Log & Perbarui Status</h3>
            <form action="/mechanic/work-order/log/store" method="POST">
                <input type="hidden" name="work_order_id" value="<?= $order['id'] ?>">

                <div class="form-group">
                    <label class="form-label" for="status">Status Pekerjaan Baru</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="started" <?= $currentStatus === 'started' ? 'selected' : '' ?>>Started</option>
                        <option value="paused" <?= $currentStatus === 'paused' ? 'selected' : '' ?>>Paused</option>
                        <option value="checked" <?= $currentStatus === 'checked' ? 'selected' : '' ?>>Checked</option>
                        <option value="rework" <?= $currentStatus === 'rework' ? 'selected' : '' ?>>Rework</option>
                        <option value="closed" <?= $currentStatus === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Catatan / Keterangan Progres</label>
                    <textarea name="notes" id="notes" class="form-textarea" placeholder="Tuliskan detail perbaikan, sparepart yang diganti, atau kendala..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">Simpan Log Pekerjaan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat Log -->
    <div class="card history-card">
        <h3 class="card-title">⏱️ Riwayat Work Order Log</h3>
        
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
