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

<div class="container">

    <div class="page-header">
        <h1>Persetujuan Kredit Leasing</h1>
        <p>Prosedur persetujuan kelayakan kredit dari lembaga leasing eksternal.</p>
    </div>

    <div class="content-grid" style="grid-template-columns: 1fr;">
        <div>
            <form id="approvalForm">
                <input type="hidden" id="status_approval" value="disetujui">
                
                <div class="card">
                    <h3>Persetujuan Pengajuan Kredit</h3>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                        Kirim data persetujuan kelayakan kredit untuk melanjutkan alur transaksi.
                    </p>

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="id_kredit">Pengajuan Kredit</label>
                            <select id="id_kredit" required style="height: 46px; border: 1px solid #dbe1ea; border-radius: 10px; padding: 10px; background: white;">
                                <option value="" disabled selected>-- Pilih Pengajuan Kredit --</option>
                                <?php foreach ($submittedApps as $app): ?>
                                <option value="<?= $app['id'] ?>">
                                    CRD-<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?> | <?= htmlspecialchars($app['customer_name']) ?> | <?= htmlspecialchars($app['kendaraan']) ?> | <?= htmlspecialchars($app['leasing_name']) ?>
                                </option>
                                <?php endforeach; ?>
                                <?php if (empty($submittedApps)): ?>
                                <option value="" disabled>Tidak ada pengajuan yang menunggu approval</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group" style="grid-column: span 2; margin-top: 15px;">
                            <label for="catatan">Catatan / Keterangan Keputusan</label>
                            <input type="text" id="catatan" placeholder="Masukkan catatan kelayakan kredit..." required>
                        </div>
                    </div>

                    <div class="action-buttons mt-20">
                        <button type="submit" class="btn btn-primary" style="width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check"></i> Setujui Kredit Leasing (Approve)
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
<script src="/js/form-approval.js"></script>
