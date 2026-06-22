<style>
    @media print {
        @page {
            margin: 0;
            size: A4;
        }
        body {
            margin: 0;
        }
        .no-print {
            display: none;
        }
    }
</style>

<div style="padding:40px; max-width:800px; font-family:Arial,sans-serif; color:#000;">
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print()" style="padding:8px 20px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Cetak Dokumen</button>
        <a href="/delivery/<?= $schedule['id'] ?>" style="margin-left:10px; text-decoration:none; color:#333;">← Kembali</a>
    </div>
    <div style="text-align:center; border-bottom:2px solid #000; padding-bottom:16px; margin-bottom:24px;">
        <h2 style="margin:0;">BERITA ACARA SERAH TERIMA KENDARAAN</h2>
        <h3 style="margin:4px 0 0; font-weight:normal;">Dealer Mobil — Sistem Informasi Manajemen</h3>
    </div>
    <p>Pada hari ini telah dilakukan serah terima kendaraan dengan data sebagai berikut:</p>
    <p style="font-weight:bold; border-bottom:1px solid #ccc; padding-bottom:4px;">Data Customer</p>
    <table style="width:100%; margin-bottom:20px;">
        <tr><td style="padding:8px; font-weight:bold; width:35%;">Nama Customer</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['customer_name']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">No. KTP</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['customer_ktp'] ?? '-') ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">No. HP</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['customer_phone'] ?? '-') ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Alamat</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['customer_address'] ?? '-') ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">No. Transaksi</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['transaction_code']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Metode Pembayaran</td><td style="padding:8px;">: <?= htmlspecialchars(ucfirst($schedule['payment_type'] ?? '-')) ?></td></tr>
    </table>
    <p style="font-weight:bold; border-bottom:1px solid #ccc; padding-bottom:4px;">Data Kendaraan</p>
    <table style="width:100%; margin-bottom:20px;">
        <tr><td style="padding:8px; font-weight:bold; width:35%;">Merek & Tipe</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['brand'] . ' ' . $schedule['type']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Warna</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['color']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">No. Rangka</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['chassis_number']) ?></td></tr>
    </table>
    <p style="font-weight:bold; border-bottom:1px solid #ccc; padding-bottom:4px;">Data Serah Terima</p>
    <table style="width:100%; margin-bottom:20px;">
        <tr><td style="padding:8px; font-weight:bold; width:35%;">Tanggal</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['scheduled_date']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Status</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['status']) ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Catatan</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['notes'] ?? '-') ?></td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Dikonfirmasi</td><td style="padding:8px;">: <?= htmlspecialchars($schedule['confirmed_at'] ?? '-') ?></td></tr>
    </table>
    <?php if (!empty($signatureUrl)): ?>
    <p style="font-weight:bold; border-bottom:1px solid #ccc; padding-bottom:4px;">Tanda Tangan Customer</p>
    <img src="<?= htmlspecialchars($signatureUrl) ?>" style="max-width:300px; border:1px solid #ccc; border-radius:4px; margin-bottom:20px;">
    <?php endif; ?>
    <div style="display:flex; justify-content:space-between; margin-top:30px;">
        <div style="text-align:center; width:200px;">
            <div style="border-top:1px solid #000; margin-top:30px; padding-top:6px; font-size:13px;">Admin Dealer</div>
        </div>
        <div style="text-align:center; width:200px;">
            <div style="border-top:1px solid #000; margin-top:30px; padding-top:6px; font-size:13px;">Customer<br><?= htmlspecialchars($schedule['customer_name']) ?></div>
        </div>
    </div>
</div>