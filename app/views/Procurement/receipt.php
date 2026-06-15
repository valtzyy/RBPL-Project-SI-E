<h2>Pencatatan Penerimaan Unit Kendaraan</h2>
<p>Silakan cocokkan jumlah unit fisik yang datang dengan jumlah pesanan di bawah ini.</p>

<p><strong>Kode Permintaan:</strong> <?= htmlspecialchars($procurement['request_code']) ?></p>
<p><strong>Status Saat Ini:</strong> <?= htmlspecialchars($procurement['status']) ?></p>

<form method="POST" action="/procurement/receipt/store">
    <input type="hidden" name="procurement_id" value="<?= htmlspecialchars($procurement['id']) ?>">
    
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Kendaraan</th>
                <th>Jumlah Dipesan (Expected)</th>
                <th>Jumlah Diterima (Actual)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $detail): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($detail['brand']) ?> 
                        <?= htmlspecialchars($detail['type']) ?> 
                        (<?= htmlspecialchars($detail['color']) ?>)
                    </td>
                    <td>
                        <?= htmlspecialchars($detail['quantity']) ?> unit
                    </td>
                    <td>
                        <input 
                            type="number" 
                            name="received_quantities[<?= htmlspecialchars($detail['vehicle_id']) ?>]" 
                            min="0" 
                            value="<?= htmlspecialchars($detail['quantity']) ?>" 
                            required
                        >
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <div>
        <a href="/">Kembali</a>
        <button type="submit" style="margin-left: 20px;">
            Simpan & Validasi Penerimaan
        </button>
    </div>
</form>
