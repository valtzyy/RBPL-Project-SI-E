<h2>Daftar Pengadaan Kendaraan</h2>
<p>Berikut adalah seluruh data permintaan pengadaan kendaraan. Silakan pilih pengadaan yang berstatus <strong>sent</strong> untuk merekam penerimaan barang dari pabrik.</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Kode Permintaan</th>
            <th>ID Pembuat</th>
            <th>Status</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($procurements)): ?>
            <tr>
                <td colspan="6" align="center">Tidak ada data pengadaan.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($procurements as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['request_code']) ?></td>
                    <td><?= htmlspecialchars($p['requested_by']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                    <td>
                        <?php if ($p['status'] === 'sent'): ?>
                            <a href="/procurement/receipt/<?= htmlspecialchars($p['id']) ?>">
                                [ Lakukan Pengecekan ]
                            </a>
                        <?php elseif ($p['status'] === 'received'): ?>
                            <span>Sudah Diterima</span>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<br>
<div>
    <a href="/">Kembali ke Beranda</a>
</div>
