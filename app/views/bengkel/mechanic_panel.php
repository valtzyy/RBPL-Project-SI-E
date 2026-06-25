<!-- app/views/bengkel/mechanic_panel.php -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
        margin: 0;
        padding: 0;
    }

    /* .container {
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
    } */

    .header-section {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
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
        color: #94a3b8;
        font-size: 14px;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .alert-danger {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fee2e2;
    }

    .alert-success {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #dcfce7;
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

    .btn-action-inline {
        background-color: #0f172a;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action-inline:hover {
        background-color: #1e293b;
        transform: translateY(-1px);
    }

    .select-inline {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        background-color: #ffffff;
        outline: none;
        transition: border-color 0.2s ease;
        flex-grow: 1;
    }

    .select-inline:focus {
        border-color: #6366f1;
    }

    .btn-log {
        background-color: #ffffff;
        color: #4f46e5;
        border: 1px solid #e0e7ff;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .btn-log:hover {
        background-color: #f5f3ff;
        border-color: #c7d2fe;
        color: #4338ca;
        transform: translateY(-1px);
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

<div class="container">
    <div class="header-section">
        <div class="header-title">
            <h2>🔧 Panel Kerja Mekanik</h2>
            <p>Daftar instruksi kerja aktif berdasarkan penugasan sistem.</p>
        </div>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'failed'): ?>
            <div class="alert alert-danger">
                <span>🚨</span>
                <strong>Gagal:</strong> Terjadi kesalahan dalam memperbarui status kerja ke database.
            </div>
        <?php elseif ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <strong>Berhasil:</strong> Log berhasil disimpan dan status work order diperbarui.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="no-data-card">
            <div class="no-data-icon">📦</div>
            <h3>Tidak ada tugas aktif</h3>
            <p>Belum ada data tugas aktif untuk Anda saat ini.</p>
        </div>
    <?php else: ?>
        <div class="card-table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID WO</th>
                        <th style="width: 20%;">Detail Kendaraan</th>
                        <th style="width: 15%;">Pelanggan</th>
                        <th style="width: 17%;">Keluhan Teknis</th>
                        <th style="width: 12%;">Status Utama</th>
                        <th style="width: 13%;">Status Pengerjaan</th>
                        <th style="width: 20%;">Ubah Status Utama</th>
                        <th style="width: 15%; text-align: center;">Log Pengerjaan</th>
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
                                <div style="max-height: 80px; overflow-y: auto; font-size: 13px; color: #475569;">
                                    <?= htmlspecialchars($order['description'] ?? 'Tidak ada catatan keluhan tambahan') ?>
                                </div>
                            </td>
                            <!-- Status Utama (Tabel work_orders) -->
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
                            <!-- Status Pengerjaan (Tabel work_order_logs) -->
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
                            <!-- Ubah Status Utama (Aksi Cepat) -->
                            <td>
                                <form action="/mechanic/work-order/update-status" method="POST" style="margin: 0; display: flex; gap: 8px;">
                                    <input type="hidden" name="work_order_id" value="<?= $order['id'] ?>">
                                    <select name="status" class="select-inline" id="status-select-<?= $order['id'] ?>">
                                        <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>
                                            In Progress
                                        </option>
                                        <option value="done" <?= $order['status'] === 'done' ? 'selected' : '' ?>>
                                            Done
                                        </option>
                                        <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>
                                            Ready
                                        </option>
                                    </select>
                                    <button type="submit" class="btn-action-inline">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <a href="/work-order/log?id=<?= $order['id'] ?>" class="btn-log">
                                    <span>📝</span> Log & Catatan
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>