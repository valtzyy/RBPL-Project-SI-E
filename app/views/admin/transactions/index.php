<div class="container" style="font-family: sans-serif; padding: 20px;">
    <h2>Antrean Tagihan Pembayaran Tunai</h2>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>Transaction Code</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $trx): ?>
                    <tr>
                        <td><?= htmlspecialchars($trx['transaction_code'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($trx['customer_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($trx['brand'] . ' ' . $trx['type'] . ' (' . $trx['color'] . ')') ?></td>
                        <td>Rp <?= number_format($trx['total_amount'] ?? 0, 0, ',', '.') ?></td>
                        <td>
                            <?php if (($trx['payment_status'] ?? 'pending') === 'verified'): ?>
                                <span style="color: green; font-weight: bold;">Verified</span>
                            <?php else: ?>
                                <span style="color: #d39e00; font-weight: bold;">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="/admin/transactions/<?= $trx['id'] ?>" style="padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; font-size: 14px;">Rincian</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Belum ada antrean tagihan pembayaran tunai.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
