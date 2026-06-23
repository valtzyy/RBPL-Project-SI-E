<h2>Transaksi Penjualan Baru</h2>

<form method="POST" action="/transactions">

    <input type="hidden" name="customer_id" value="<?= $customer_id ?>">

    <h3>Data Pelanggan</h3>

    <label>Nama Pelanggan</label><br>
    <input type="text" value="<?= htmlspecialchars($customer['name'] ?? '') ?>" disabled><br><br>

    <label>Nomor Telepon</label><br>
    <input type="text" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" disabled><br><br>

    <label>Nomor KTP</label><br>
    <input type="text" name="ktp_number" required placeholder="16 digit NIK"><br><br>

    <label>Alamat</label><br>
    <textarea name="address" required placeholder="Alamat sesuai KTP"></textarea>

    <h3>Pilih Kendaraan</h3>
    <input type="hidden" name="vehicle_id" id="selected_vehicle_id">
    <p id="selected_vehicle_label" style="color:green;"></p>

    <div style="display:flex; flex-wrap:wrap; gap:16px; margin-top:12px;">
        <?php foreach ($vehicles as $vehicle): ?>
            <div 
                onclick="pilihKendaraan(<?= $vehicle['id'] ?>, '<?= $vehicle['brand'] ?> <?= $vehicle['type'] ?>')"
                id="card-<?= $vehicle['id'] ?>"
                style="border:2px solid #ccc; border-radius:8px; padding:12px; width:200px; cursor:pointer;">
                <strong><?= $vehicle['brand'] ?> <?= $vehicle['type'] ?></strong><br>
                Warna: <?= $vehicle['color'] ?><br>
                Harga: Rp <?= number_format($vehicle['price'], 0, ',', '.') ?><br>
                Stok: <?= $vehicle['stock_quantity'] ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h3>Metode Pembayaran</h3>
    <select name="payment_type" required>
        <option value="">-- Pilih Metode --</option>
        <option value="1">Kredit (Leasing)</option>
        <option value="2">Tunai (Cash)</option>
    </select>

    <br><br>
    <button type="submit">Simpan Transaksi</button>

</form>

<script>
function pilihKendaraan(id, nama) {
    document.querySelectorAll('[id^="card-"]').forEach(card => {
        card.style.borderColor = '#ccc';
        card.style.background = '';
    });
    document.getElementById('card-' + id).style.borderColor = '#4CAF50';
    document.getElementById('card-' + id).style.background = '#f0fff0';
    document.getElementById('selected_vehicle_id').value = id;
    document.getElementById('selected_vehicle_label').textContent = 'Kendaraan dipilih: ' + nama;
}
</script>