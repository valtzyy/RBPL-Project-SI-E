<h1>Daftar Pelanggan</h1>

<a href="/customers/create">+ Daftarkan Pelanggan Baru</a>

<hr>

<?php if (empty($customers)): ?>
    <p>Belum ada pelanggan.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>No. Telepon</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= $customer['id'] ?></td>
            <td><?= htmlspecialchars($customer['name']) ?></td>
            <td><?= htmlspecialchars($customer['phone'] ?? '-') ?></td>
            <td><a href="/customers/<?= $customer['id'] ?>">Detail</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>