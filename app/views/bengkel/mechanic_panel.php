<div style="font-family: Arial, sans-serif; padding: 20px; max-width: 1000px; margin: 0 auto;">

    <h2>🔧 Panel Kerja Mekanik</h2>
    <p>Daftar instruksi kerja aktif berdasarkan penugasan sistem.</p>
    <hr>

    <?php if (empty($orders)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">
            Belum ada data tugas untuk Anda.
        </div>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>ID WO</th>
                    <th>Detail Kendaraan</th>
                    <th>Pelanggan</th>
                    <th>Keluhan Teknis</th>
                    <th>Status Sekarang</th>
                    <th>Aksi Progres</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                        <td>
                            <strong><?= htmlspecialchars($order['vehicle_model']) ?></strong><br>
                            <small><?= htmlspecialchars($order['license_plate']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['description'] ?? 'Tidak ada catatan') ?></td>
                        <td>
                            <?php
                            $badgeColor = '#ffc107'; // Default jingga untuk 'in_progress'
                            if ($order['status'] === 'done')
                                $badgeColor = '#28a745'; // Hijau untuk 'done'
                            if ($order['status'] === 'ready')
                                $badgeColor = '#007bff'; // Biru untuk 'ready'
                            ?>
                            <span
                                style="background-color: <?= $badgeColor ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form action="/mechanic/work-order/update-status" method="POST" style="margin: 0;">
                                <input type="hidden" name="work_order_id" value="<?= $order['id'] ?>">

                                <select name="status" style="padding: 5px;">
                                    <option value="in_progress" <?= $order['status'] === 'in_progress' ? 'selected' : '' ?>>
                                        Dikerjakan (In Progress)</option>

                                    <option value="done" <?= $order['status'] === 'done' ? 'selected' : '' ?>>Selesai Servis (Done)
                                    </option>

                                    <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Siap Diserahkan
                                        (Ready / QC Passed)</option>
                                </select>

                                <button type="submit"
                                    style="background-color: #333; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px;">
                                    Simpan
                                </button>
                            </form>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($order['vehicle_model']) ?></strong><br>
                            <small style="color: #666;">Warna: <?= htmlspecialchars($order['vehicle_color']) ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>