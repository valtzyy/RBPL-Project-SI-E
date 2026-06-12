<div style="padding:20px; max-width:600px;">
    <h1>Jadwalkan Serah Terima Kendaraan</h1>
    <form method="POST" action="/delivery">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Pilih Transaksi (Status: Lunas)</label>
            <select name="transaction_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                <option value="">-- Pilih Transaksi --</option>
                <?php foreach ($transactions as $t): ?>
                    <option value="<?= $t['id'] ?>">
                        #<?= htmlspecialchars($t['transaction_code']) ?> — <?= htmlspecialchars($t['customer_name']) ?> — <?= htmlspecialchars($t['brand'] . ' ' . $t['type'] . ' ' . $t['color']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Nama Customer</label>
            <input type="text" name="customer_name" required placeholder="Nama lengkap customer" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Tanggal Serah Terima</label>
            <input type="date" name="scheduled_date" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Catatan (opsional)</label>
            <textarea name="notes" rows="3" placeholder="Catatan tambahan..." style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"></textarea>
        </div>
        <button type="submit" style="padding:8px 20px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Simpan Jadwal</button>
        <a href="/delivery" style="padding:8px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:4px; margin-left:8px;">Batal</a>
    </form>
</div>
