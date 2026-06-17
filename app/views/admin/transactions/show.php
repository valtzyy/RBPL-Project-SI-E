<div class="container" style="max-width: 800px; margin: 0 auto; font-family: sans-serif; padding: 20px;">
    <h2>Rincian Tagihan Final</h2>
    <a href="/admin/transactions" style="text-decoration: none; color: #007bff; margin-bottom: 20px; display: inline-block;">&larr; Kembali</a>

    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3>DATA TRANSAKSI</h3>
        <table style="width: 100%; border-spacing: 0;">
            <tr><td style="width: 150px; padding: 5px 0;">Transaction Code</td><td>: <?= htmlspecialchars($transaction['transaction_code'] ?? '-') ?></td></tr>
            <tr><td style="padding: 5px 0;">Customer</td><td>: <?= htmlspecialchars($transaction['customer_name'] ?? '-') ?></td></tr>
            <tr><td style="padding: 5px 0;">Vehicle</td><td>: <?= htmlspecialchars($transaction['brand'] . ' ' . $transaction['type']) ?></td></tr>
            <tr><td style="padding: 5px 0;">Payment Type</td><td>: <?= htmlspecialchars($transaction['payment_type'] == 1 ? 'Kredit' : 'Tunai') ?></td></tr>
            <tr><td style="padding: 5px 0;">Status</td><td>: <?= htmlspecialchars(strtoupper($transaction['status'] ?? '-')) ?></td></tr>
        </table>
    </div>

    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3>DATA TOTAL AMOUNT</h3>
        <table style="width: 100%; border-spacing: 0;">
            <tr><td style="width: 150px; padding: 5px 0;">Total Amount</td><td>: Rp <?= number_format($transaction['total_amount'] ?? 0, 0, ',', '.') ?></td></tr>
        </table>
    </div>

    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3>DATA PAYMENT</h3>
        <?php if (!empty($transaction['payment_id'])): ?>
            <table style="width: 100%; border-spacing: 0;">
                <tr><td style="width: 150px; padding: 5px 0;">Amount</td><td>: Rp <?= number_format($transaction['payment_amount'] ?? 0, 0, ',', '.') ?></td></tr>
                <tr><td style="padding: 5px 0;">Payment Date</td><td>: <?= htmlspecialchars($transaction['payment_date'] ?? '-') ?></td></tr>
                <tr><td style="padding: 5px 0;">Status</td><td>: 
                    <?php if (($transaction['payment_status'] ?? 'pending') === 'verified'): ?>
                        <span style="color: green; font-weight: bold;">Verified</span>
                    <?php else: ?>
                        <span style="color: #d39e00; font-weight: bold;">Pending</span>
                    <?php endif; ?>
                </td></tr>
            </table>
            
            <!-- SIMULASI UNTUK ROLE FINANCE: Tombol Verify via POST Endpoint -->
            <?php if ($transaction['payment_status'] === 'pending'): ?>
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 5px solid #ffecb5;">
                    <p style="margin-top: 0;"><strong>[Simulasi Role Finance]</strong> Tekan tombol ini untuk memverifikasi pembayaran (Menjalankan POST /finance/payments/<?= $transaction['payment_id'] ?>/verify)</p>
                    <form action="/finance/payments/<?= $transaction['payment_id'] ?>/verify" method="POST" style="margin: 0;">
                        <button type="submit" style="padding: 8px 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 3px; font-size: 14px;">Verifikasi Pembayaran</button>
                    </form>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <p style="color: #dc3545; margin: 0;">Payment belum dibuat untuk transaksi ini.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: right; padding-top: 10px;">
        <?php if (!empty($transaction['payment_id']) && $transaction['payment_status'] === 'verified'): ?>
            <a href="/admin/transactions/<?= $transaction['id'] ?>/receipt" style="padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">Download Kwitansi</a>
        <?php else: ?>
            <button disabled style="padding: 10px 20px; background: #e9ecef; color: #6c757d; border: 1px solid #ced4da; border-radius: 5px; font-weight: bold; cursor: not-allowed;">Download Kwitansi</button>
            <p style="font-size: 13px; color: #6c757d; margin-top: 8px;">Kwitansi baru dapat didownload jika pembayaran sudah diverifikasi.</p>
        <?php endif; ?>
    </div>
</div>
