<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gudang Logistik & Suku Cadang</title>
</head>

<body style="font-family: Arial, sans-serif; margin: 30px;">
    <h1>📦 Manajemen Suku Cadang Dealer (Sprint 14)</h1>
    <a href="/dashboard">📊 Pergi ke Dashboard Eksekutif</a>
    <hr>

    <div style="background-color: #ffcccc; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <h3 style="color: #cc0000; margin-top: 0;">⚠️ Peringatan Stok Kritis (Low-Level Stock)</h3>
        <ul>
            <?php if (empty($lowStock)): ?>
                <li>Semua stok suku cadang dalam kondisi aman.</li>
            <?php else: ?>
                <?php foreach ($lowStock as $ls): ?>
                    <li><strong><?= $ls['name'] ?></strong> - Sisa Stok: <?= $ls['stock'] ?> (Batas Min: <?= $ls['min_stock'] ?>)</li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <h3>📝 Form Cetak Surat Pesanan (PO) Baru</h3>
    <form action="/sparepart/po/store" method="POST" style="background: #f4f4f4; padding: 15px; border-radius: 5px;">
        <label>Nama Supplier:</label><br>
        <input type="text" name="supplier_name" required style="width: 300px; padding: 5px;"><br><br>

        <label>Pilih Suku Cadang:</label><br>
        <select name="sparepart_id" required style="width: 308px; padding: 5px;">
            <?php foreach (($allSpareparts ?? []) as $sp): ?>
                <?php if (isset($sp['id'], $sp['name'], $sp['stock'])): ?>
                    <option value="<?= $sp['id'] ?>">
                        <?= htmlspecialchars($sp['name']) ?> (Stok saat ini: <?= $sp['stock'] ?>)
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select><br><br>

        <label>Kuantitas Pesanan:</label><br>
        <input type="number" name="quantity" min="1" value="1" required style="width: 300px; padding: 5px;"><br><br>

        <button type="submit" style="background: green; color: white; padding: 8px 15px; border: none; cursor: pointer;">Kirim & Cetak Surat Pesanan</button>
    </form>

    <h3>📋 Log Riwayat Dokumen Purchase Order (PO)</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr style="background-color: #eee;">
            <th>ID PO</th>
            <th>Nama Supplier</th>
            <th>Suku Cadang</th>
            <th>Jumlah Pesanan</th>
            <th>Status</th>
            <th>Aksi Penyesuaian Gudang</th>
        </tr>
        <?php if (empty($allPO)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 15px; color: #777;">
                    Belum ada log riwayat dokumen Purchase Order (PO).
                </td>
            </tr>
        <?php else: ?>
            <?php foreach (($allPO ?? []) as $po): ?>
                <tr>
                    <td>PO-00<?= $po['id'] ?? '?' ?></td>
                    <td><?= htmlspecialchars($po['supplier_name'] ?? '-') ?></td>

                    <td><?= htmlspecialchars($po['sparepart_name'] ?? $po['name'] ?? '-') ?></td>

                    <td><?= $po['quantity'] ?? 0 ?> Pcs</td>
                    <td>
                        <?php
                        $status = strtolower($po['status'] ?? 'pending');
                        ?>
                        <span style="padding: 3px 8px; border-radius: 3px; background: <?= $status === 'received' ? '#d4edda' : '#fff3cd' ?>">
                            <?= strtoupper($status) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($status === 'pending'): ?>
                            <a href="/sparepart/po/terima?id=<?= $po['id'] ?? '' ?>" onclick="return confirm('Apakah item kiriman supplier ini sudah sampai dan sesuai?')" style="background: blue; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;">✔️ Terima & Tambah Inventaris</a>
                        <?php else: ?>
                            <span style="color: green; font-size: 12px;">Selesai di-restock</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>

</html>