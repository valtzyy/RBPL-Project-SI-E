<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran Uang Muka (DP)</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Link to the styling template -->
    <link rel="stylesheet" href="/css/upload-document.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        .result-card {
            margin-top: 20px;
            display: none;
            padding: 15px;
            border-radius: 8px;
        }
        .result-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .result-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Verifikasi Pembayaran Uang Muka (DP)</h1>
        <p>Halaman pencatatan dan verifikasi pelunasan uang muka oleh divisi Finance.</p>
    </div>

    <div class="content-grid" style="grid-template-columns: 1fr;">
        <div>
            <form id="financeForm">
                <div class="card">
                    <h3>Verifikasi Uang Muka</h3>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                        Verifikasi pembayaran DP yang diserahkan oleh customer untuk meneruskan status transaksi kendaraan.
                    </p>

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="id_kredit">Pengajuan Kredit (Sudah Disetujui)</label>
                            <select id="id_kredit" required>
                                <option value="" disabled selected>-- Pilih Pengajuan Kredit --</option>
                                <?php
                                $selectedId = (int)($_GET['id_kredit'] ?? 0);
                                foreach ($approvedApps as $app):
                                    $isSelected = ($app['id'] == $selectedId) ? 'selected' : '';
                                ?>
                                <option value="<?= $app['id'] ?>" <?= $isSelected ?>>
                                    CRD-<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?> | <?= htmlspecialchars($app['customer_name']) ?> | <?= htmlspecialchars($app['kendaraan']) ?> | <?= htmlspecialchars($app['leasing_name']) ?>
                                </option>
                                <?php endforeach; ?>
                                <?php if (empty($approvedApps)): ?>
                                <option value="" disabled>Tidak ada pengajuan yang sudah disetujui</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nominal_dibayar">Nominal Uang Muka Dibayar (Rp)</label>
                            <input type="number" id="nominal_dibayar" placeholder="Masukkan jumlah nominal..." required step="0.01">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label for="verified_by">Staf Finance (Verifikator)</label>
                            <select id="verified_by" required>
                                <option value="" disabled selected>-- Pilih Staf Finance --</option>
                                <?php foreach ($financeUsers as $user): ?>
                                <option value="<?= $user['id'] ?>">
                                    <?= htmlspecialchars($user['name']) ?> (@<?= htmlspecialchars($user['username']) ?>)
                                </option>
                                <?php endforeach; ?>
                                <?php if (empty($financeUsers)): ?>
                                <option value="" disabled>Tidak ada staf Finance yang aktif</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="action-buttons mt-20">
                        <button type="submit" class="btn btn-primary" style="background: #10b981; width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-receipt"></i> Verifikasi Pembayaran DP
                        </button>
                    </div>
                </div>
            </form>

            <!-- Card Result Message -->
            <div id="resultBox" class="result-card">
                <strong id="resultTitle"></strong>
                <p id="resultMessage" style="margin-top: 5px; font-size: 14px;"></p>
            </div>
        </div>
    </div>

</div>

<!-- Script -->
<script src="/js/verifikasi-dp.js"></script>

</body>
</html>
