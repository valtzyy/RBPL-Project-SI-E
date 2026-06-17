<div class="container" style="max-width: 800px; margin: 0 auto; font-family: sans-serif; padding: 20px;">
    <h2 style="margin-bottom: 5px;">Detail Pembayaran</h2>
    <a href="/finance/payments" style="text-decoration: none; color: #007bff; margin-bottom: 20px; display: inline-block;">&larr; Kembali ke Antrean</a>

    <?php if (!$payment): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
            Data pembayaran tidak ditemukan.
        </div>
    <?php else: ?>

        <!-- Data Transaksi -->
        <div style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; border-bottom: 2px solid #dee2e6; padding-bottom: 8px;">DATA TRANSAKSI</h3>
            <table style="width: 100%; border-spacing: 0;">
                <tr><td style="width: 170px; padding: 6px 0; color: #6c757d;">Kode Transaksi</td><td>: <strong><?= htmlspecialchars($payment['transaction_code'] ?? '-') ?></strong></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Customer</td><td>: <?= htmlspecialchars($payment['customer_name'] ?? '-') ?></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">No. Telepon</td><td>: <?= htmlspecialchars($payment['customer_phone'] ?? '-') ?></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Kendaraan</td><td>: <?= htmlspecialchars(($payment['brand'] ?? '-') . ' ' . ($payment['type'] ?? '') . ' (' . ($payment['color'] ?? '') . ')') ?></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Harga Kendaraan</td><td>: Rp <?= number_format($payment['price'] ?? 0, 0, ',', '.') ?></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Status Transaksi</td><td>: <strong><?= htmlspecialchars(strtoupper($payment['transaction_status'] ?? '-')) ?></strong></td></tr>
            </table>
        </div>

        <!-- Data Pembayaran -->
        <div style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; border-bottom: 2px solid #dee2e6; padding-bottom: 8px;">DATA PEMBAYARAN</h3>
            <table style="width: 100%; border-spacing: 0;">
                <tr><td style="width: 170px; padding: 6px 0; color: #6c757d;">Payment ID</td><td>: #<?= htmlspecialchars($payment['payment_id']) ?></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Jumlah Bayar</td><td>: <strong>Rp <?= number_format($payment['payment_amount'] ?? 0, 0, ',', '.') ?></strong></td></tr>
                <tr><td style="padding: 6px 0; color: #6c757d;">Tanggal Bayar</td><td>: <?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td></tr>
                <tr>
                    <td style="padding: 6px 0; color: #6c757d;">Status Pembayaran</td>
                    <td>: 
                        <?php if (($payment['payment_status'] ?? 'pending') === 'verified'): ?>
                            <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; font-weight: bold;">✅ Verified</span>
                        <?php else: ?>
                            <span style="background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-weight: bold;">⏳ Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tombol Verifikasi -->
        <?php if (($payment['payment_status'] ?? 'pending') === 'pending'): ?>
            <div style="border: 2px solid #ffc107; padding: 20px; border-radius: 5px; background: #fffdf0; margin-bottom: 20px;">
                <h3 style="margin-top: 0; color: #856404;">⚠️ Menunggu Verifikasi</h3>
                <p style="margin-bottom: 15px; color: #555;">Pastikan pembayaran telah diterima sebelum menekan tombol verifikasi. Setelah diverifikasi, status transaksi akan otomatis berubah menjadi <strong>LUNAS</strong>.</p>
                <form action="/finance/payments/<?= $payment['payment_id'] ?>/verify" method="POST">
                    <button type="submit" 
                            style="padding: 10px 25px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 15px; font-weight: bold;"
                            onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?')">
                        ✅ Verifikasi Pembayaran
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div style="border: 2px solid #28a745; padding: 20px; border-radius: 5px; background: #f0fff4; margin-bottom: 20px;">
                <h3 style="margin-top: 0; color: #155724;">✅ Pembayaran Sudah Diverifikasi</h3>
                <p style="margin: 0; color: #555;">Pembayaran ini telah diverifikasi. Tidak ada aksi yang perlu dilakukan.</p>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
