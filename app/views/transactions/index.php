<h1>Daftar Transaksi Penjualan</h1>

<a href="/transactions/create">+ Buat Transaksi Baru</a>

<hr>

<?php if (empty($transactions)): ?>
    <p>Belum ada transaksi.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Customer</th>
            <th>Kendaraan</th>
            <th>Harga</th>
            <th>Sales</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $trx): ?>
        <tr>
            <td><?= htmlspecialchars($trx['transaction_code']) ?></td>
            <td><?= htmlspecialchars($trx['customer_name']) ?></td>
            <td><?= htmlspecialchars($trx['brand'] . ' ' . $trx['type']) ?> (<?= htmlspecialchars($trx['color']) ?>)</td>
            <td>Rp <?= number_format($trx['price'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($trx['sales_name']) ?></td>
            <td><?= htmlspecialchars($trx['status']) ?></td>
            <td><?= $trx['created_at'] ?></td>
            <td><a href="/transactions/<?= $trx['id'] ?>">Detail</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>