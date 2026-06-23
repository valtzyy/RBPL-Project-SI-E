<style>
    .detail-container {
        max-width: 850px;
        margin: 0 auto;
        padding: 8px 4px;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #4F5BD5;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
        transition: color 0.15s ease;
    }
    
    .back-link:hover {
        color: #3B47B8;
    }
    
    .detail-header {
        margin-bottom: 24px;
    }
    
    .detail-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin: 0;
        letter-spacing: -0.02em;
    }

    /* Cards */
    .detail-card {
        background: #ffffff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .card-title {
        font-size: 13px;
        font-weight: 700;
        color: #4B5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #F3F4F6;
    }
    
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .info-table td {
        padding: 10px 0;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
    }
    
    .info-label {
        width: 180px;
        color: #6B7280;
        font-weight: 500;
    }
    
    .info-value {
        font-weight: 600;
        color: #111827;
    }
    
    .info-value strong {
        font-weight: 700;
        color: #111827;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }
    
    .badge-pending {
        background-color: #FEF3C7;
        color: #D97706;
        border: 1px solid #FDE68A;
    }
    
    .badge-verified {
        background-color: #D1FAE5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    /* Action Panels */
    .panel-warning {
        border: 1px solid #FDE68A;
        background: #FFFBEB;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .panel-warning h3 {
        margin-top: 0;
        margin-bottom: 8px;
        color: #92400E;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .panel-warning p {
        color: #B45309;
        font-size: 14px;
        margin-top: 0;
        margin-bottom: 16px;
        line-height: 1.5;
    }
    
    .panel-success {
        border: 1px solid #A7F3D0;
        background: #F0FDF4;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .panel-success h3 {
        margin-top: 0;
        margin-bottom: 8px;
        color: #065F46;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .panel-success p {
        color: #047857;
        font-size: 14px;
        margin: 0;
    }

    /* Buttons */
    .btn-verify {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background-color: #10B981;
        color: #ffffff;
        border: none;
        cursor: pointer;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.1);
    }
    
    .btn-verify:hover {
        background-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.15);
    }
    
    .error-banner {
        background: #FEE2E2;
        border: 1px solid #FCA5A5;
        color: #991B1B;
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 500;
    }
</style>

<div class="detail-container">
    <a href="/finance/payments" class="back-link">&larr; Kembali ke Antrean</a>
    
    <div class="detail-header">
        <h2>📄 Detail Pembayaran</h2>
    </div>

    <?php if (!$payment): ?>
        <div class="error-banner">
            ⚠️ Data pembayaran tidak ditemukan atau telah dihapus.
        </div>
    <?php else: ?>

        <!-- Data Transaksi -->
        <div class="detail-card">
            <h3 class="card-title">Data Transaksi</h3>
            <table class="info-table">
                <tr>
                    <td class="info-label">Kode Transaksi</td>
                    <td class="info-value"><strong><?= htmlspecialchars($payment['transaction_code'] ?? '-') ?></strong></td>
                </tr>
                <tr>
                    <td class="info-label">Customer</td>
                    <td class="info-value"><?= htmlspecialchars($payment['customer_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="info-label">No. Telepon</td>
                    <td class="info-value"><?= htmlspecialchars($payment['customer_phone'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Kendaraan</td>
                    <td class="info-value"><?= htmlspecialchars(($payment['brand'] ?? '-') . ' ' . ($payment['type'] ?? '') . ' (' . ($payment['color'] ?? '') . ')') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Harga Kendaraan</td>
                    <td class="info-value" style="color: #0F172A;">Rp <?= number_format($payment['price'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Status Transaksi</td>
                    <td class="info-value"><span style="text-transform: uppercase; font-weight: 700; color: #4F5BD5;"><?= htmlspecialchars($payment['transaction_status'] ?? '-') ?></span></td>
                </tr>
            </table>
        </div>

        <!-- Data Pembayaran -->
        <div class="detail-card">
            <h3 class="card-title">Data Pembayaran</h3>
            <table class="info-table">
                <tr>
                    <td class="info-label">Payment ID</td>
                    <td class="info-value font-mono">#<?= htmlspecialchars($payment['payment_id']) ?></td>
                </tr>
                <tr>
                    <td class="info-label">Jumlah Bayar</td>
                    <td class="info-value" style="color: #10B981; font-size: 16px;"><strong>Rp <?= number_format($payment['payment_amount'] ?? 0, 0, ',', '.') ?></strong></td>
                </tr>
                <tr>
                    <td class="info-label">Tanggal Bayar</td>
                    <td class="info-value"><?= htmlspecialchars($payment['payment_date'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="info-label">Status Pembayaran</td>
                    <td class="info-value">
                        <?php if (($payment['payment_status'] ?? 'pending') === 'verified'): ?>
                            <span class="badge badge-verified">✅ Verified</span>
                        <?php else: ?>
                            <span class="badge badge-pending">⏳ Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tombol Verifikasi -->
        <?php if (($payment['payment_status'] ?? 'pending') === 'pending'): ?>
            <div class="panel-warning">
                <h3>⚠️ Menunggu Verifikasi</h3>
                <p>Pastikan pembayaran telah diterima secara tunai/transfer sebelum menekan tombol verifikasi. Setelah diverifikasi, status transaksi akan otomatis berubah menjadi <strong>LUNAS</strong>.</p>
                <form action="/finance/payments/<?= $payment['payment_id'] ?>/verify" method="POST">
                    <button type="submit" class="btn-verify" onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?')">
                        ✅ Verifikasi Pembayaran
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="panel-success">
                <h3>✅ Pembayaran Sudah Diverifikasi</h3>
                <p>Pembayaran ini telah diverifikasi oleh bagian Finance. Tidak ada aksi lebih lanjut yang perlu dilakukan.</p>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
