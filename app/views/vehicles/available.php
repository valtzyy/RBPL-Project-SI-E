<h1>Kendaraan Tersedia</h1>

<?php if (empty($vehicles)): ?>
    <p>Tidak ada kendaraan yang tersedia saat ini.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Brand</th>
            <th>Tipe</th>
            <th>Warna</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vehicles as $vehicle): ?>
        <tr>
            <td><?= $vehicle['id'] ?></td>
            <td><?= htmlspecialchars($vehicle['brand']) ?></td>
            <td><?= htmlspecialchars($vehicle['type']) ?></td>
            <td><?= htmlspecialchars($vehicle['color']) ?></td>
            <td>Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></td>
            <td><?= $vehicle['stock_quantity'] ?></td>
            <td><?= htmlspecialchars($vehicle['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>