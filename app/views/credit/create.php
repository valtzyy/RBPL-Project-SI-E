<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengajuan Kredit</title>

    <link rel="stylesheet" href="/css/upload-document.css">
</head>
<body>

<div class="container">
    <a href="/" class="back-link">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M10 3L5 8l5 5"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"/>
        </svg>
        Kembali
    </a>

    <div class="page-header">
        <h1>Buat Pengajuan Kredit</h1>
        <p>Pilih transaksi dan leasing untuk mengajukan kredit.</p>
    </div>

    <form id="createForm">
        <div class="card mt-20">
            <h3>Form Pengajuan</h3>

            <div class="form-grid">

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="transaction_id">Transaksi (Customer · Kendaraan)</label>
                    <select id="transaction_id" name="transaction_id" required>
                        <option value="">-- Pilih Transaksi --</option>
                        <?php foreach (($transactions ?? []) as $trx): ?>
                            <option value="<?= (int) $trx['id'] ?>">
                                <?= htmlspecialchars($trx['transaction_code']) ?> ·
                                <?= htmlspecialchars($trx['customer_name']) ?> ·
                                <?= htmlspecialchars($trx['brand']) ?> <?= htmlspecialchars($trx['type']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="leasing_name">Nama Leasing</label>
                    <input type="text" id="leasing_name" name="leasing_name" required
                           placeholder="Contoh: BCA Finance">
                </div>

            </div>

            <div class="action-buttons">
                <a href="/" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Buat Pengajuan</button>
            </div>

        </div>
    </form>

</div>

<script src="/js/create-credit.js"></script>

</body>
</html>