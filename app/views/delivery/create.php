<div style="padding:20px; max-width:600px;">
    <h1>Jadwalkan Serah Terima Kendaraan</h1>
    <form method="POST" action="/delivery">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Pilih Transaksi</label>
            <select name="transaction_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;" onchange="fillCustomer(this)">
                <option value="">-- Pilih Transaksi --</option>
                <?php foreach ($transactions as $t): ?>
                    <option value="<?= $t['id'] ?>" data-customer-id="<?= $t['customer_id'] ?>" data-customer-name="<?= htmlspecialchars($t['customer_name']) ?>">
                        #<?= htmlspecialchars($t['transaction_code']) ?> — <?= htmlspecialchars($t['customer_name']) ?> — <?= htmlspecialchars($t['brand'] . ' ' . $t['type'] . ' ' . $t['color']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="customer_id" id="customer_id">
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Customer</label>
            <input type="text" id="customer_name_display" readonly style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; background:#f5f5f5;" placeholder="Otomatis terisi saat pilih transaksi">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Tanggal Serah Terima</label>
            <input type="date" name="scheduled_date" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block; font-weight:bold; margin-bottom:4px;">Catatan (opsional)</label>
            <textarea name="notes" rows="3" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"></textarea>
        </div>
        <button type="submit" style="padding:8px 20px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Simpan Jadwal</button>
        <a href="/delivery" style="padding:8px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:4px; margin-left:8px;">Batal</a>
    </form>
    <script>
        function fillCustomer(select) {
            const opt = select.options[select.selectedIndex];
            document.getElementById('customer_id').value = opt.dataset.customerId || '';
            document.getElementById('customer_name_display').value = opt.dataset.customerName || '';
        }
    </script>
</div>
