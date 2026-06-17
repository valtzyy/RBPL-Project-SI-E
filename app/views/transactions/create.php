<h2>Transaksi Penjualan Baru</h2>

<h2>Transaksi Penjualan Baru</h2>

<h2>Transaksi Penjualan Baru</h2>

<form method="POST" action="/transactions">

    <h3>Data Pelanggan</h3>
    <label>Pilih Pelanggan</label>
    <select name="customer_id" required>
        <option value="">-- Pilih Pelanggan --</option>
        <?php foreach ($customers as $customer): ?>
            <option value="<?= $customer['id'] ?>">
                <?= $customer['name'] ?> - <?= $customer['phone'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br>
    <label>Nomor KTP</label>
    <input type="text" name="ktp_number" required placeholder="16 digit NIK">

    <br>
    <label>Alamat</label>
    <textarea name="address" required placeholder="Alamat sesuai KTP"></textarea>

    <h3>Pilih Kendaraan</h3>
    <select name="vehicle_id" required>
        <option value="">-- Pilih Kendaraan --</option>
        <?php foreach ($vehicles as $vehicle): ?>
            <option value="<?= $vehicle['id'] ?>">
                <?= $vehicle['brand'] ?> <?= $vehicle['type'] ?> -
                <?= $vehicle['color'] ?> -
                Rp <?= number_format($vehicle['price'], 0, ',', '.') ?> -
                Stok: <?= $vehicle['stock_quantity'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <h3>Metode Pembayaran</h3>
    <select name="payment_type" required>
        <option value="">-- Pilih Metode --</option>
        <option value="1">Kredit (Leasing)</option>
        <option value="2">Tunai (Cash)</option>
    </select>

    <br><br>
    <button type="submit">Simpan Transaksi</button>

</form>