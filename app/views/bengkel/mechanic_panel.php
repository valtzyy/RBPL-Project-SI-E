<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto;">

    <h2>🔧 Panel Kerja Mekanik</h2>
    <p>Daftar instruksi kerja aktif berdasarkan penugasan sistem.</p>
    <hr>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'failed'): ?>
        <div
            style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
            🚨 <strong>Error:</strong> Gagal memproses pembaruan status kerja ke database cloud.
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
            Belum ada data tugas aktif untuk Anda.
        </div>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="width: 80px;">ID WO</th>
                    <th style="width: 250px;">Detail Kendaraan</th>
                    <th style="width: 180px;">Pelanggan</th>
                    <th>Keluhan Teknis</th>
                    <th style="width: 130px;">Status Sekarang</th>
                    <th style="width: 250px;">Aksi Progres</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td style="text-align: center;"><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($order['vehicle_model']) ?></strong><br>
                            <span style="color: #444; font-size: 13px; font-weight: bold;">Plat:
                                <?= htmlspecialchars($order['license_plate']) ?></span><br>
                            <small style="color: #777;">Warna: <?= htmlspecialchars($order['vehicle_color']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['description'] ?? 'Tidak ada catatan keluhan tambahan') ?></td>
                        <td style="text-align: center;">
                            <?php
                            $badgeColor = '#ffc107'; // Default kuning jingga untuk 'in_progress'
                            if ($order['status'] === 'done')
                                $badgeColor = '#28a745'; // Hijau untuk 'done'
                            if ($order['status'] === 'ready')
                                $badgeColor = '#007bff'; // Biru untuk 'ready'
                            ?>
                            <span
                                style="background-color: <?= $badgeColor ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block;">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form action="/mechanic/work-order/update-status" method="POST"
                                style="margin: 0; display: flex; gap: 5px;">
                                <input type="hidden" name="work_order_id" value="<?= $order['id'] ?>">

                                <select name="status" style="padding: 6px; flex-grow: 1;"
                                    id="status-select-<?= $order['id'] ?>">
                                    <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>
                                        Dikerjakan (In Progress)
                                    </option>
                                    <option value="done" <?= $order['status'] === 'done' ? 'selected' : '' ?>>
                                        Selesai Servis (Done)
                                    </option>
                                    <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>
                                        Siap Diserahkan (Ready / QC Passed)
                                    </option>
                                </select>

                                <button type="submit"
                                    style="background-color: #333; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-weight: bold;">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>