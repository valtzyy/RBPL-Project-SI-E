<?php if ($transaction): ?>
    <p>Kode: <?= $transaction['transaction_code'] ?></p>
    <p>Customer: <?= $transaction['customer_name'] ?></p>
    <p>Kendaraan: <?= $transaction['brand'] ?> <?= $transaction['type'] ?></p>
    <p>Pembayaran: <?= $transaction['payment_type_name'] ?? '-' ?></p>
    <p>Status: <?= $transaction['status'] ?></p>
<?php else: ?>
    <p>Transaksi tidak ditemukan.</p>
<?php endif; ?>