<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #333; }
        .receipt-container { border: 2px dashed #333; padding: 40px; margin: 0 auto; width: 90%; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0 0 10px 0; font-size: 26px; text-transform: uppercase; letter-spacing: 2px;}
        .header p { margin: 0; font-size: 16px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .details-table td { padding: 10px 0; vertical-align: top; }
        .details-table td.label { width: 35%; font-weight: bold; }
        .details-table td.colon { width: 5%; text-align: center; font-weight: bold; }
        .details-table td.value { width: 60%; }
        .footer { width: 100%; margin-top: 60px; }
        .footer-left { float: left; width: 50%; }
        .footer-right { float: right; width: 40%; text-align: center; }
        .stamp { display: inline-block; border: 3px solid #28a745; color: #28a745; font-size: 24px; font-weight: bold; padding: 10px 20px; text-align: center; letter-spacing: 2px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>KWITANSI PEMBAYARAN</h1>
        </div>
        
        <table class="details-table">
            <tr>
                <td class="label">Kode Transaksi</td>
                <td class="colon">:</td>
                <td class="value"><?= htmlspecialchars($transaction['transaction_code'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Telah Terima Dari</td>
                <td class="colon">:</td>
                <td class="value" style="text-transform: capitalize;"><?= htmlspecialchars($transaction['customer_name'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="colon">:</td>
                <td class="value">Pembelian Kendaraan <?= htmlspecialchars($transaction['brand'] . ' ' . $transaction['type'] . ' (' . $transaction['color'] . ')') ?></td>
            </tr>
            <tr>
                <td class="label">Tanggal Pembayaran</td>
                <td class="colon">:</td>
                <td class="value"><?= htmlspecialchars($transaction['payment_date'] ?? date('Y-m-d')) ?></td>
            </tr>
            <tr>
                <td class="label">Nominal Pembayaran</td>
                <td class="colon">:</td>
                <td class="value" style="font-size: 16px; font-weight: bold;">Rp <?= number_format($transaction['payment_amount'] ?? 0, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td class="label">Status Pembayaran</td>
                <td class="colon">:</td>
                <td class="value" style="text-transform: uppercase; font-weight: bold;">
                    <?= htmlspecialchars($transaction['payment_status'] ?? '-') ?>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div class="footer-left">
                <?php if(isset($transaction['payment_status']) && strtolower($transaction['payment_status']) === 'verified'): ?>
                <div class="stamp">LUNAS / VERIFIED</div>
                <?php endif; ?>
            </div>
            <div class="footer-right">
                <p>Mengetahui,</p>
                <p style="margin-bottom: 70px;">Admin / Finance</p>
                <p style="border-top: 1px solid #333; padding-top: 10px; width: 80%; margin: 0 auto;">( Tanda Tangan & Nama Terang )</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>
