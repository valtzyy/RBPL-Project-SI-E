<div class="container" style="font-family: sans-serif; padding: 20px; max-width: 1100px; margin: 0 auto;">
    <h2 style="margin-bottom: 5px;">Antrean Verifikasi Pembayaran</h2>
    <p style="color: #6c757d; margin-top: 0;">Role: <strong>Finance</strong> — Verifikasi pembayaran tunai dari transaksi Sales.</p>

    <?php if (!empty($_GET['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            ✅ Pembayaran berhasil diverifikasi dan status transaksi telah diubah menjadi <strong>Lunas</strong>.
        </div>
    <?php endif; ?>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <thead style="background-color: #343a40; color: #fff;">
            <tr>
                <th style="padding: 12px 10px;">No</th>
                <th style="padding: 12px 10px;">Kode Transaksi</th>
                <th style="padding: 12px 10px;">Customer</th>
                <th style="padding: 12px 10px;">Kendaraan</th>
                <th style="padding: 12px 10px;">Jumlah Bayar</th>
                <th style="padding: 12px 10px;">Tgl Bayar</th>
                <th style="padding: 12px 10px;">Status</th>
                <th style="padding: 12px 10px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php $no = 1; ?>
                <?php foreach ($transactions as $trx): ?>
                    <?php if (empty($trx['payment_id'])) continue; ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($trx['transaction_code'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($trx['customer_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(($trx['brand'] ?? '') . ' ' . ($trx['type'] ?? '') . ' (' . ($trx['color'] ?? '') . ')') ?></td>
                        <td>Rp <?= number_format($trx['payment_amount'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($trx['payment_date'] ?? '-') ?></td>
                        <td style="text-align: center;">
                            <?php if (($trx['payment_status'] ?? 'pending') === 'verified'): ?>
                                <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: bold;">✅ Verified</span>
                            <?php else: ?>
                                <span style="background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 13px; font-weight: bold;">⏳ Pending</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="/finance/payments/<?= $trx['payment_id'] ?>" 
                               style="padding: 6px 14px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                                Detail
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #6c757d;">Belum ada data pembayaran.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
