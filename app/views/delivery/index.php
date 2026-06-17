<?php $title = $title ?? 'Jadwal Serah Terima'; ?>
<div style="padding: 32px; font-family: 'Segoe UI', Arial, sans-serif;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h1 style="font-size:26px; font-weight:700; color:#1e293b; margin:0;">Jadwal Serah Terima Kendaraan</h1>
        <a href="/delivery/create" style="padding:11px 22px; background:#0f172a; color:#fff; text-decoration:none; border-radius:8px; font-weight:600; font-size:14px;">+ Jadwalkan Serah Terima</a>
    </div>

    <div style="background:#fff; border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,0.05); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">#</th>
                    <th style="text-align:left; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">Nama Customer</th>
                    <th style="text-align:left; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">Kendaraan</th>
                    <th style="text-align:left; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">Tanggal</th>
                    <th style="text-align:left; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">Status</th>
                    <th style="text-align:center; padding:18px 24px; font-size:13px; font-weight:600; color:#475569; border-bottom:1px solid #eef1f5;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:#94a3b8; font-size:14px;">
                            Belum ada jadwal serah terima.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($schedules as $s): ?>
                        <?php if (empty($s['customer_name'])) continue; ?>
                        <tr>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8; color:#334155; font-size:14px;"><?= $no++ ?></td>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8; color:#1e293b; font-weight:600; font-size:14px;"><?= htmlspecialchars($s['customer_name']) ?></td>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8; color:#334155; font-size:14px;"><?= htmlspecialchars($s['brand'] . ' ' . $s['type'] . ' - ' . $s['color']) ?></td>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8; color:#334155; font-size:14px;"><?= htmlspecialchars($s['scheduled_date']) ?></td>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8;">
                                <?php if ($s['status'] === 'completed'): ?>
                                    <span style="background:#1c5e3a; color:#fff; padding:5px 14px; border-radius:6px; font-size:13px; font-weight:600; display:inline-block;">Selesai</span>
                                <?php else: ?>
                                    <span style="background:#f5b942; color:#1e293b; padding:5px 14px; border-radius:6px; font-size:13px; font-weight:600; display:inline-block;">Terjadwal</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:18px 24px; border-bottom:1px solid #f1f4f8; text-align:center; white-space:nowrap;">
                                <a href="/delivery/<?= $s['id'] ?>" style="display:inline-block; padding:7px 16px; background:#1198ab; color:#fff; text-decoration:none; border-radius:6px; font-size:13px; font-weight:600; margin-right:8px;">Detail</a>
                                <a href="/delivery/<?= $s['id'] ?>/document" style="display:inline-block; padding:7px 16px; background:#1198ab; color:#fff; text-decoration:none; border-radius:6px; font-size:13px; font-weight:600;">Dokumen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>