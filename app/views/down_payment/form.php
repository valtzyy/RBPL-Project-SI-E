<section class="page-heading">
    <div>
        <h1>Manajemen Down Payment</h1>
        <p>Catat DP customer, simpan kontrak kredit, dan pantau riwayat pengajuan.</p>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="flash-message <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<section class="content-grid">
    <div class="dp-card">
        <div class="card-header">
            <div>
                <h2>Catat Pembayaran DP</h2>
                <p>Hanya pengajuan kredit dengan keputusan approved yang dapat dipilih.</p>
            </div>
        </div>

        <form id="downPaymentForm" method="POST" action="/down-payment">
            <div class="form-group">
                <label for="credit_application_id">Pengajuan Kredit Approved</label>
                <select id="credit_application_id" name="credit_application_id" required>
                    <option value="">Pilih pengajuan</option>
                    <?php foreach ($applications as $app): ?>
                        <option value="<?= $app['id'] ?>">
                            #<?= $app['id'] ?>
                            - <?= htmlspecialchars($app['customer_name']) ?>
                            - <?= htmlspecialchars($app['leasing_name'] ?? 'Leasing') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="amount">Nominal DP</label>
                    <input id="amount" type="number" step="0.01" name="amount" placeholder="Masukkan nominal" required>
                </div>

                <div class="form-group">
                    <label for="paid_at">Tanggal Bayar</label>
                    <input id="paid_at" type="date" name="paid_at" required>
                </div>
            </div>

            <div class="button-container">
                <button class="create-button" type="submit">
                    <span class="button-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                    </span>
                    Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>

    <div class="dp-card">
        <div class="card-header">
            <div>
                <h2>Upload Kontrak Kredit</h2>
                <p>Simpan dokumen kontrak yang sudah ditandatangani agar bisa diakses kembali.</p>
            </div>
        </div>

        <form method="POST" action="/down-payment/contract" enctype="multipart/form-data">
            <div class="form-group">
                <label for="contract_credit_application_id">Pengajuan Kredit Approved</label>
                <select id="contract_credit_application_id" name="credit_application_id" required>
                    <option value="">Pilih pengajuan</option>
                    <?php foreach ($applications as $app): ?>
                        <option value="<?= $app['id'] ?>">
                            #<?= $app['id'] ?>
                            - <?= htmlspecialchars($app['customer_name']) ?>
                            - <?= htmlspecialchars($app['leasing_name'] ?? 'Leasing') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="contract_file">File Kontrak</label>
                <input id="contract_file" type="file" name="contract_file" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>

            <div class="button-container">
                <button class="secondary-button" type="submit">Upload Kontrak</button>
            </div>
        </form>
    </div>
</section>

<section class="history-section">
    <div class="section-header">
        <div>
            <h2>Riwayat Pengajuan Kredit Customer</h2>
            <p>Status kredit, pembayaran DP, dan kelengkapan dokumen per customer.</p>
        </div>
    </div>

    <div class="table-wrap">
        <table class="history-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Pengajuan</th>
                    <th>Status Kredit</th>
                    <th>DP</th>
                    <th>Dokumen</th>
                    <th>Kontrak</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($histories)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada riwayat pengajuan kredit.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($histories as $history): ?>
                    <?php
                        $decision = $history['decision'] ?? 'pending';
                        $documentCount = (int) ($history['document_count'] ?? 0);
                        $hasContract = (int) ($history['contract_count'] ?? 0) > 0;
                        $hasDownPayment = !empty($history['down_payment_id']);
                    ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($history['customer_name']) ?></strong>
                            <span><?= htmlspecialchars($history['phone'] ?? '-') ?></span>
                        </td>
                        <td>
                            <strong>#<?= htmlspecialchars($history['credit_application_id']) ?></strong>
                            <span><?= htmlspecialchars($history['leasing_name'] ?? 'Leasing') ?></span>
                        </td>
                        <td>
                            <span class="status-badge <?= htmlspecialchars($decision) ?>">
                                <?= htmlspecialchars(ucfirst($decision)) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($hasDownPayment): ?>
                                <strong>Rp <?= number_format((float) $history['down_payment_amount'], 0, ',', '.') ?></strong>
                                <span><?= htmlspecialchars($history['down_payment_paid_at'] ?? '-') ?></span>
                            <?php else: ?>
                                <span class="muted">Belum dicatat</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= $documentCount ?> dokumen</strong>
                            <span><?= $documentCount > 0 ? 'Ada dokumen' : 'Belum lengkap' ?></span>
                        </td>
                        <td>
                            <?php if ($hasContract): ?>
                                <span class="status-badge approved">Tersimpan</span>
                                <a class="document-link" href="<?= htmlspecialchars($history['contract_file_path']) ?>" target="_blank" rel="noopener">
                                    Lihat kontrak
                                </a>
                            <?php else: ?>
                                <span class="status-badge pending">Belum ada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
