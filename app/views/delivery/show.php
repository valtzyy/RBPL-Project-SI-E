<div style="padding:20px; max-width:700px;">
    <h1>Detail Serah Terima</h1>
    <div style="border:1px solid #ccc; border-radius:8px; padding:20px; margin-bottom:20px;">
        <h2 style="margin-top:0; font-size:16px; color:#555;">Informasi Jadwal</h2>
        <table style="width:100%;">
            <tr><td style="padding:8px; font-weight:bold; width:40%;">Nama Customer</td><td style="padding:8px;"><?= htmlspecialchars($schedule['customer_name'] ?? '-') ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">No. KTP</td><td style="padding:8px;"><?= htmlspecialchars($schedule['customer_ktp'] ?? '-') ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">No. HP</td><td style="padding:8px;"><?= htmlspecialchars($schedule['customer_phone'] ?? '-') ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Alamat</td><td style="padding:8px;"><?= htmlspecialchars($schedule['customer_address'] ?? '-') ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Kendaraan</td><td style="padding:8px;"><?= htmlspecialchars($schedule['brand'] . ' ' . $schedule['type'] . ' - ' . $schedule['color']) ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">No. Rangka</td><td style="padding:8px;"><?= htmlspecialchars($schedule['chassis_number']) ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Metode Pembayaran</td><td style="padding:8px;"><?= htmlspecialchars(ucfirst($schedule['payment_type'] ?? '-')) ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Tanggal Serah Terima</td><td style="padding:8px;"><?= htmlspecialchars($schedule['scheduled_date']) ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Status</td><td style="padding:8px;"><?= htmlspecialchars($schedule['status']) ?></td></tr>
            <tr><td style="padding:8px; font-weight:bold;">Catatan</td><td style="padding:8px;"><?= htmlspecialchars($schedule['notes'] ?? '-') ?></td></tr>
        </table>
    </div>

    <?php if ($schedule['status'] === 'scheduled'): ?>
    <div style="border:1px solid #ccc; border-radius:8px; padding:20px; margin-bottom:20px;">
        <h2 style="margin-top:0; font-size:16px; color:#555;">Tanda Tangan Customer</h2>
        <canvas id="signatureCanvas" width="600" height="200" style="border:2px dashed #aaa; border-radius:8px; cursor:crosshair; display:block;"></canvas>
        <button onclick="clearSignature()" style="margin-top:10px; padding:6px 14px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Hapus</button>
        <form id="confirmForm" method="POST" action="/delivery/<?= $schedule['id'] ?>/confirm" style="margin-top:20px;">
            <input type="hidden" name="signature_data" id="signatureData">
            <a href="/delivery" style="padding:8px 16px; background:#6c757d; color:white; text-decoration:none; border-radius:4px;">Kembali</a>
            <button type="button" onclick="submitConfirm()" style="padding:8px 16px; background:#28a745; color:white; border:none; border-radius:4px; cursor:pointer; margin-left:8px;">Konfirmasi Serah Terima</button>
        </form>
        <form method="POST" action="/delivery/<?= $schedule['id'] ?>/fail" style="margin-top:10px;">
            <button type="submit" onclick="return confirm('Tandai jadwal ini sebagai gagal?')" style="padding:8px 16px; background:#dc3545; color:white; border:none; border-radius:4px; cursor:pointer;">Tandai Gagal</button>
        </form>
    </div>
    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let drawing = false;
        canvas.addEventListener('mousedown', (e) => { drawing = true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
        canvas.addEventListener('mousemove', (e) => { if (!drawing) return; ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke(); });
        canvas.addEventListener('mouseup', () => { drawing = false; });
        canvas.addEventListener('mouseleave', () => { drawing = false; });
        function clearSignature() { ctx.clearRect(0, 0, canvas.width, canvas.height); }
        function submitConfirm() {
            document.getElementById('signatureData').value = canvas.toDataURL('image/png');
            document.getElementById('confirmForm').submit();
        }
    </script>

    <?php elseif ($schedule['status'] === 'confirmed'): ?>
<div style="border:1px solid #ccc; border-radius:8px; padding:20px;">
    <h2 style="margin-top:0; font-size:16px; color:#555;">Serah Terima Sudah Selesai</h2>
    <?php if (!empty($signatureUrl)): ?>
        <p>Tanda tangan customer:</p>
        <img src="<?= htmlspecialchars($signatureUrl) ?>" style="max-width:400px; border:1px solid #ccc; border-radius:4px;">
    <?php endif; ?>
    <p>Dikonfirmasi pada: <?= htmlspecialchars($schedule['confirmed_at'] ?? '-') ?></p>
    <a href="/delivery/<?= $schedule['id'] ?>/document" style="display:inline-block; margin-top:10px; padding:8px 16px; background:#1198ab; color:white; text-decoration:none; border-radius:4px;">Cetak Dokumen</a>
</div>


    <?php elseif ($schedule['status'] === 'failed'): ?>
    <div style="border:1px solid #f5c6cb; border-radius:8px; padding:20px; background:#fff5f5;">
        <h2 style="margin-top:0; font-size:16px; color:#dc3545;">Serah Terima Gagal</h2>
        <p style="color:#555;">Jadwal ini ditandai gagal. Silakan buat jadwal baru untuk customer ini.</p>
        <a href="/delivery/create" style="padding:8px 16px; background:#0f172a; color:white; text-decoration:none; border-radius:4px;">Buat Jadwal Baru</a>
    </div>
    <?php endif; ?>

    <a href="/delivery" style="display:inline-block; margin-top:20px; padding:8px 16px; background:#6c757d; color:white; text-decoration:none; border-radius:4px;">← Kembali ke Daftar</a>
</div>