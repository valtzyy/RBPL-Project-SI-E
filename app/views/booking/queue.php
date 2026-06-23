<!-- app/views/booking/queue.php -->
<div class="queue-container">
    <div class="dashboard-header">
        <div style="width: 100%; justify-content: space-between; align-items: center; display: flex;">
            <!-- Judul & Deskripsi -->
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <h1 style="color: #0B1C30; font-size: 24px; font-family: 'Inter', sans-serif; font-weight: 600; line-height: 32px; margin: 0;">
                    Service Booking
                </h1>
                <p style="color: #45464D; font-size: 14px; font-family: 'Inter', sans-serif; font-weight: 400; line-height: 20px; margin: 0;">
                    Pantauan progress booking
                </p>
            </div>

            <!-- Filter Tanggal & Tombol -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <div class="date-filter">
                    <input type="date" id="filter-date" value="<?= htmlspecialchars($date) ?>">
                </div>

                <!-- Tombol Buat Antrian + Icon -->
                <button style="display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: black; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0px 4px 6px -1px rgba(0, 0, 0, 0.1);"
                    onclick="window.location.href='/booking/queue'">
                    <!-- Icon Tambah (+) SVG Putih -->
                    <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <!-- Teks -->
                    <span style="color: white; font-size: 12px; font-family: 'Inter', sans-serif; font-weight: 600; line-height: 16px; letter-spacing: 0.60px; text-transform: uppercase;">
                        Buat antrian
                    </span>
                </button>
            </div>
        </div>

    </div>

    <div class="stats-row">
        <div class="stat-card">
            <h3>Ketersediaan Slot</h3>
            <p class="stat-value"><?= $totalAntrean  ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Antrean</h3>
            <p class="stat-value"><?= count($bookings) ?> Kendaraan</p>
        </div>
    </div>

    <div id="alert-box" class="alert" style="display: none;"></div>

    <div class="table-card">
        <table class="queue-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>No. Telepon</th>
                    <th>Nama Kendaraan</th>
                    <th>Plat Nomor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="7" class="empty-state">Tidak ada antrean untuk tanggal ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr id="row-<?= $b['id'] ?>">
                            <td><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['customer_name']) ?></td>
                            <td><?= htmlspecialchars($b['customer_phone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($b['vehicle_name']) ?></td>
                            <td><span class="badge badge-plate"><?= htmlspecialchars($b['plate_number']) ?></span></td>
                            <td>
                                <?php if (isset($b['wo_status']) && $b['wo_status'] === 'in_progress'): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-<?= $b['status'] ?>">
                                        <?= $b['status'] === 'queued' ? 'Mengantre' : 'Dikonfirmasi' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($b['status'] === 'queued'): ?>
                                        <button class="btn-action btn-confirm" onclick="processBooking(<?= $b['id'] ?>, 'confirm')">Konfirmasi</button>
                                        <button class="btn-action btn-reject" onclick="processBooking(<?= $b['id'] ?>, 'reject')">Tolak</button>
                                    <?php elseif ($b['status'] === 'confirmed' && (!isset($b['wo_status']) || $b['wo_status'] !== 'in_progress')): ?>
                                        <a href="/booking/inspect/<?= $b['id'] ?>" class="btn-action btn-inspect">Inspeksi / Buat WO</a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 13px; font-weight: 600; padding: 8px 14px; display: inline-block;">Selesai Inspeksi</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
        padding: 40px 20px;
    }

    .queue-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    h2 {
        margin: 0;
        font-size: 26px;
        color: #111;
    }

    .subtitle {
        color: #666;
        font-size: 14px;
        margin: 5px 0 0 0;
    }

    .date-filter {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .date-filter label {
        font-weight: 600;
        font-size: 14px;
    }

    .date-filter input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .stats-row {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        flex: 1;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border-left: 4px solid #0056b3;
    }

    .stat-card h3 {
        margin: 0;
        font-size: 14px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        margin: 10px 0 0 0;
        font-size: 28px;
        font-weight: 700;
        color: #111;
    }

    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .queue-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .queue-table th {
        background-color: #f8f9fa;
        padding: 16px;
        font-weight: 600;
        color: #555;
        border-bottom: 1px solid #eee;
    }

    .queue-table td {
        padding: 16px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }

    .empty-state {
        text-align: center;
        padding: 40px !important;
        color: #999;
        font-style: italic;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-queued {
        background-color: #fff3cd;
        color: #856404;
    }

    .badge-confirmed {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-plate {
        background-color: #e9ecef;
        color: #495057;
        font-family: monospace;
        font-size: 13px;
        border: 1px solid #ced4da;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-confirm {
        background-color: #28a745;
        color: white;
    }

    .btn-confirm:hover {
        background-color: #218838;
    }

    .btn-reject {
        background-color: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background-color: #c82333;
    }

    .btn-inspect {
        background-color: #0056b3;
        color: white;
    }

    .btn-inspect:hover {
        background-color: #004085;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<script>
    document.getElementById('filter-date').addEventListener('change', function() {
        window.location.href = `/booking/queue?date=${this.value}`;
    });

    function processBooking(id, action) {
        if (!confirm(`Apakah Anda yakin ingin melakukan ${action === 'confirm' ? 'konfirmasi' : 'penolakan'} booking ini?`)) {
            return;
        }

        const alertBox = document.getElementById('alert-box');
        alertBox.style.display = 'none';

        const formData = new FormData();
        formData.append('id', id);

        fetch(`/booking/${action}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = data.message;
                    alertBox.style.display = 'block';
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = data.message;
                    alertBox.style.display = 'block';
                }
            })
            .catch(err => {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = 'Terjadi kesalahan sistem.';
                alertBox.style.display = 'block';
            });
    }
</script>