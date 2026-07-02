<?php
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }

    function formatTanggal(string $tanggal): string
    {
        $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
        $ts = strtotime($tanggal);

        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }

    function badgeClassFor(string $status): string
    {
        return match ($status) {
            'disetujui' => 'badge--success',
            'diverifikasi', 'diajukan' => 'badge--warning',
            'ditolak' => 'badge--danger',
            default => 'badge--warning',
        };
    }

    function badgeLabelFor(string $status): string
    {
        return match ($status) {
            'disetujui' => 'Lunas',
            'diverifikasi' => 'Proses',
            'diajukan' => 'Diajukan',
            'ditolak' => 'Batal',
            default => ucfirst($status),
        };
    }

    function docIconClassFor(string $status): string
    {
        return match ($status) {
            'lengkap' => 'doc-row__icon--ok',
            'menunggu' => 'doc-row__icon--wait',
            'kurang' => 'doc-row__icon--miss',
            default => 'doc-row__icon--wait',
        };
    }

    function docIconSymbolFor(string $status): string
    {
        return match ($status) {
            'lengkap' => '&check;',
            'menunggu' => '...',
            'kurang' => '!',
            default => '?',
        };
    }

    function docActionLabelFor(string $status): string
    {
        return match ($status) {
            'lengkap' => 'Lihat File',
            'menunggu' => 'Cek Status',
            'kurang' => 'Minta Susulan',
            default => 'Lihat',
        };
    }
}

if (!isset($profile, $applications, $selected, $stats, $customerId)) {
    require_once __DIR__ . '/../../Models/CreditHistoryModel.php';

    $model = new CreditHistoryModel();
    $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 1;
    $applicationId = isset($_GET['application_id']) ? (int) $_GET['application_id'] : null;
    $profile = $model->getCustomerProfile($customerId);
    $applications = $model->getCreditApplicationsByCustomer($customerId);
    $selected = $model->findSelectedApplication($applications, $applicationId);
    $stats = $model->getApplicationStats($applications);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Kredit - <?= htmlspecialchars($profile['nama']) ?> | DealerLink</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-logo">DealerLink</div>

        <div class="sidebar-user">
            <div class="avatar">FS</div>
            <div class="user-info">
                <span>Finance Staff</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <a href="#">
                <i class="fa-solid fa-table-columns"></i>
                <span>Dashboard</span>
            </a>
            <a href="#">
                <i class="fa-regular fa-credit-card"></i>
                <span>Pembayaran Tunai</span>
            </a>
            <a href="#" class="active">
                <i class="fa-solid fa-building-columns"></i>
                <span>Kredit & Leasing</span>
            </a>
            <a href="#">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>Pembayaran Servis</span>
            </a>
            <a href="#">
                <i class="fa-regular fa-file-lines"></i>
                <span>Riwayat Transaksi</span>
            </a>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <a href="customer.php" class="topbar__back">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2>Histori Kelengkapan File Kredit</h2>
                    <span><?= htmlspecialchars($profile['nama']) ?></span>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="stat-row">
                <div class="stat-card">
                    <span class="badge badge--warning stat-card__badge">Proses</span>
                    <div class="stat-card__number"><?= $stats['total_menunggu'] ?></div>
                    <div class="stat-card__label">Pengajuan perlu diverifikasi</div>
                </div>
                <div class="stat-card">
                    <span class="badge badge--success stat-card__badge">Lunas</span>
                    <div class="stat-card__number"><?= $stats['total_disetujui'] ?></div>
                    <div class="stat-card__label">Kredit disetujui & berjalan</div>
                </div>
                <div class="stat-card stat-card--dark">
                    <div class="stat-card__label">Total Nilai Kredit Aktif</div>
                    <div class="stat-card__number"><?= formatRupiah($stats['total_nilai_aktif']) ?></div>
                    <span class="stat-card__delta">&uarr; <?= $stats['total_pengajuan'] ?> pengajuan tercatat</span>
                </div>
            </div>

            <div class="section-head">
                <h2>Riwayat Pengajuan Kredit</h2>
                <span class="hint">Klik salah satu pengajuan untuk melihat detail kelengkapan dokumen</span>
            </div>

            <div class="credit-layout">
                <div class="timeline-card">
                    <div class="timeline-card__search">
                        <input type="text" placeholder="Cari No. Pengajuan / Kode..." disabled>
                    </div>

                    <?php if (empty($applications)): ?>
                        <div class="empty-state">
                            <div class="ic"><i class="fa-regular fa-folder-open"></i></div>
                            <p>Customer ini belum pernah mengajukan kredit.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline-list">
                            <?php foreach ($applications as $application): ?>
                                <?php
                                    $isSelected = isset($selected) && $selected && $selected['id'] === $application['id'];
                                    $linkUrl = '?customer_id=' . $customerId . '&application_id=' . $application['id'];
                                ?>
                                <a href="<?= htmlspecialchars($linkUrl) ?>">
                                    <div class="timeline-item <?= $isSelected ? 'is-selected' : '' ?>">
                                        <div class="timeline-item__top">
                                            <span class="timeline-item__code"><?= htmlspecialchars($application['kode_pengajuan']) ?></span>
                                            <span class="badge <?= badgeClassFor($application['status']) ?>">
                                                <?= badgeLabelFor($application['status']) ?>
                                            </span>
                                        </div>
                                        <div class="timeline-item__date"><?= formatTanggal($application['tanggal_ajuan']) ?></div>
                                        <div class="timeline-item__veh"><?= htmlspecialchars($application['kendaraan']) ?></div>
                                        <div class="timeline-item__amount"><?= formatRupiah($application['harga_kendaraan']) ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="timeline-card__footer">
                            Menampilkan <?= count($applications) ?> dari <?= count($applications) ?> pengajuan
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <?php if (isset($selected) && $selected): ?>
                        <?php
                            $application = $selected;
                            $totalDoc = count($application['dokumen']);
                            $lengkapCount = count(array_filter($application['dokumen'], fn($document) => $document['status'] === 'lengkap'));
                        ?>
                        <div class="detail-card">
                            <div class="detail-card__back">
                                <span>&larr;</span> <?= htmlspecialchars($application['kode_pengajuan']) ?>
                            </div>

                            <div class="detail-card__header">
                                <div class="detail-card__title">Detail Pengajuan Kredit</div>
                                <span class="badge <?= badgeClassFor($application['status']) ?>"><?= badgeLabelFor($application['status']) ?></span>
                            </div>

                            <div class="total-highlight">
                                <div class="total-highlight__label">Harga Kendaraan</div>
                                <div class="total-highlight__value"><?= formatRupiah($application['harga_kendaraan']) ?></div>
                                <div class="total-highlight__meta">
                                    <div>
                                        Uang Muka (DP)
                                        <b><?= formatRupiah($application['dp']) ?></b>
                                    </div>
                                    <div style="text-align:right;">
                                        Tenor
                                        <b><?= $application['tenor_bulan'] > 0 ? $application['tenor_bulan'] . ' Bulan' : '-' ?></b>
                                    </div>
                                </div>
                            </div>

                            <div class="info-two-col">
                                <div class="info-section">
                                    <div class="info-section__title">Informasi Pengajuan</div>
                                    <div class="info-row"><span class="label">No. Pengajuan</span><span class="value"><?= htmlspecialchars($application['kode_pengajuan']) ?></span></div>
                                    <div class="info-row"><span class="label">Tanggal Ajuan</span><span class="value"><?= formatTanggal($application['tanggal_ajuan']) ?></span></div>
                                    <div class="info-row"><span class="label">Leasing</span><span class="value"><?= htmlspecialchars($application['leasing']) ?></span></div>
                                </div>
                                <div class="info-section">
                                    <div class="info-section__title">Customer &amp; Kendaraan</div>
                                    <div class="info-row"><span class="label">Nama Customer</span><span class="value"><?= htmlspecialchars($profile['nama']) ?></span></div>
                                    <div class="info-row"><span class="label">Kendaraan</span><span class="value"><?= htmlspecialchars($application['kendaraan']) ?></span></div>
                                    <div class="info-row"><span class="label">Sales</span><span class="value"><?= htmlspecialchars($application['sales']) ?></span></div>
                                </div>
                            </div>

                            <div class="doc-panel">
                                <div class="doc-panel__head">
                                    <h3>Kelengkapan Dokumen</h3>
                                    <span class="doc-panel__count"><?= $lengkapCount ?> / <?= $totalDoc ?> lengkap</span>
                                </div>
                                <div class="doc-list">
                                    <?php foreach ($application['dokumen'] as $document): ?>
                                        <div class="doc-row">
                                            <div class="doc-row__icon <?= docIconClassFor($document['status']) ?>">
                                                <?= docIconSymbolFor($document['status']) ?>
                                            </div>
                                            <div class="doc-row__body">
                                                <div class="doc-row__name"><?= htmlspecialchars($document['nama']) ?></div>
                                                <div class="doc-row__note"><?= htmlspecialchars($document['catatan']) ?></div>
                                            </div>
                                            <button 
                                                type="button" 
                                                class="doc-row__action"
                                                data-doc-id="<?= $document['id'] ?>"
                                                data-doc-name="<?= htmlspecialchars($document['nama']) ?>"
                                                data-doc-status="<?= $document['status'] ?>"
                                                data-doc-path="<?= htmlspecialchars($document['file_path'] ?? '') ?>"
                                                data-app-id="<?= $application['id'] ?>">
                                                <?= docActionLabelFor($document['status']) ?>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($application['status'] === 'disetujui'): ?>
                                <div class="decision-note decision-note--success">
                                    <strong>Catatan Keputusan</strong><?= htmlspecialchars($application['catatan_keputusan']) ?>
                                </div>
                            <?php elseif ($application['status'] === 'ditolak'): ?>
                                <div class="decision-note decision-note--danger">
                                    <strong>Catatan Keputusan</strong><?= htmlspecialchars($application['catatan_keputusan']) ?>
                                </div>
                                <button 
                                    type="button" 
                                    class="action-confirm" 
                                    style="background:var(--color-text-soft);"
                                    data-action="resubmit"
                                    data-app-id="<?= $application['id'] ?>"
                                    data-app-code="<?= htmlspecialchars($application['kode_pengajuan']) ?>">
                                    Ajukan Ulang Pengajuan
                                </button>
                            <?php else: ?>
                                <button 
                                    type="button" 
                                    class="action-confirm"
                                    data-action="confirm"
                                    data-app-id="<?= $application['id'] ?>"
                                    data-app-code="<?= htmlspecialchars($application['kode_pengajuan']) ?>">
                                    Konfirmasi Kelengkapan Dokumen
                                </button>
                                <button 
                                    type="button" 
                                    class="action-cancel"
                                    data-action="cancel"
                                    data-app-id="<?= $application['id'] ?>"
                                    data-app-code="<?= htmlspecialchars($application['kode_pengajuan']) ?>">
                                    Batalkan Pengajuan
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="detail-card">
                            <div class="empty-state">
                                <div class="ic"><i class="fa-regular fa-folder-open"></i></div>
                                <p>Pilih salah satu pengajuan di sebelah kiri untuk melihat detailnya.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Dokumen -->
<div id="docModal" class="modal" style="display: none;">
    <div class="modal__overlay"></div>
    <div class="modal__content">
        <div class="modal__header">
            <h2 id="modalTitle">Detail Dokumen</h2>
            <button type="button" class="modal__close" onclick="closeDocModal()">&times;</button>
        </div>
        <div class="modal__body">
            <div id="modalContent"></div>
        </div>
        <div class="modal__footer">
            <button type="button" class="modal__btn-secondary" onclick="closeDocModal()">Tutup</button>
            <button type="button" class="modal__btn-primary" id="modalAction">Lihat File</button>
        </div>
    </div>
</div>

<!-- Modal Aksi Pengajuan -->
<div id="actionModal" class="modal" style="display: none;">
    <div class="modal__overlay"></div>
    <div class="modal__content">
        <div class="modal__header">
            <h2 id="actionModalTitle">Konfirmasi Aksi</h2>
            <button type="button" class="modal__close" onclick="closeActionModal()">&times;</button>
        </div>
        <div class="modal__body">
            <div id="actionModalContent"></div>
        </div>
        <div class="modal__footer">
            <button type="button" class="modal__btn-secondary" onclick="closeActionModal()">Batal</button>
            <button type="button" class="modal__btn-primary" id="actionModalConfirm">Lanjutkan</button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal__overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    cursor: pointer;
}

.modal__content {
    position: relative;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
}

.modal__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
}

.modal__header h2 {
    margin: 0;
    font-size: 18px;
}

.modal__close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #999;
}

.modal__close:hover {
    color: #333;
}

.modal__body {
    padding: 20px;
}

.modal__footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #e5e5e5;
}

.modal__btn-secondary,
.modal__btn-primary {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.modal__btn-secondary {
    background: #e5e5e5;
    color: #333;
}

.modal__btn-secondary:hover {
    background: #d5d5d5;
}

.modal__btn-primary {
    background: #007bff;
    color: white;
}

.modal__btn-primary:hover {
    background: #0056b3;
}

.modal__btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.doc-info {
    margin-bottom: 20px;
}

.doc-info__row {
    display: flex;
    margin-bottom: 10px;
}

.doc-info__label {
    min-width: 120px;
    font-weight: 600;
    color: #555;
}

.doc-info__value {
    flex: 1;
    color: #333;
}

.upload-area {
    border: 2px dashed #007bff;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    background: #f8f9ff;
    transition: all 0.3s;
}

.upload-area:hover {
    background: #eef2ff;
}

.upload-area.dragover {
    background: #e7f0ff;
    border-color: #0056b3;
}

.upload-area__icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.upload-area__text {
    color: #666;
    font-size: 14px;
}

.upload-area__hint {
    font-size: 12px;
    color: #999;
    margin-top: 5px;
}

.action-info {
    font-size: 14px;
    line-height: 1.6;
    color: #333;
}

.action-info p {
    margin: 0 10px 10px 0;
}

.action-info ul {
    list-style-position: inside;
}

.action-info ul li {
    margin-bottom: 8px;
}
</style>

<script>
document.querySelectorAll('.doc-row__action').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const docId = this.getAttribute('data-doc-id');
        const docName = this.getAttribute('data-doc-name');
        const docStatus = this.getAttribute('data-doc-status');
        const docPath = this.getAttribute('data-doc-path');
        const appId = this.getAttribute('data-app-id');
        
        showDocModal(docId, docName, docStatus, docPath, appId);
    });
});

// Event listener untuk tombol aksi pengajuan
document.querySelectorAll('[data-action]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.getAttribute('data-action');
        const appId = this.getAttribute('data-app-id');
        const appCode = this.getAttribute('data-app-code');
        
        handleApplicationAction(action, appId, appCode);
    });
});

function showDocModal(docId, docName, docStatus, docPath, appId) {
    const modal = document.getElementById('docModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    const modalAction = document.getElementById('modalAction');
    
    modalTitle.textContent = docName;
    
    let contentHTML = `
        <div class="doc-info">
            <div class="doc-info__row">
                <div class="doc-info__label">Nama Dokumen</div>
                <div class="doc-info__value">${escapeHtml(docName)}</div>
            </div>
            <div class="doc-info__row">
                <div class="doc-info__label">Status</div>
                <div class="doc-info__value">
                    <span class="badge ${getStatusBadgeClass(docStatus)}">
                        ${getStatusLabel(docStatus)}
                    </span>
                </div>
            </div>
        </div>
    `;
    
    if (docStatus === 'lengkap' && docPath) {
        contentHTML += `
            <div class="doc-info" style="background: #f0f8ff; padding: 15px; border-radius: 4px; text-align: center;">
                <i class="fa-solid fa-check-circle" style="font-size: 32px; color: #28a745; margin-bottom: 10px;"></i>
                <p style="margin: 10px 0 0 0; color: #333;">File sudah lengkap dan terverifikasi</p>
            </div>
        `;
        modalAction.textContent = 'Lihat File';
        modalAction.disabled = false;
        modalAction.onclick = () => {
            window.open(docPath, '_blank');
        };
    } else if (docStatus === 'menunggu') {
        contentHTML += `
            <div class="doc-info" style="background: #fff8f0; padding: 15px; border-radius: 4px; text-align: center;">
                <i class="fa-solid fa-hourglass-half" style="font-size: 32px; color: #ff9800; margin-bottom: 10px;"></i>
                <p style="margin: 10px 0 0 0; color: #333;">Dokumen sedang dalam proses verifikasi. Silakan tunggu.</p>
            </div>
        `;
        modalAction.textContent = 'Cek Status';
        modalAction.disabled = false;
        modalAction.onclick = () => {
            alert('Status dokumen sedang diverifikasi. Kami akan memberikan update dalam 1-2 hari kerja.');
        };
    } else if (docStatus === 'kurang') {
        contentHTML += `
            <div class="doc-info" style="background: #fff5f5; padding: 15px; border-radius: 4px;">
                <p style="margin: 0 0 10px 0; color: #d32f2f; font-weight: 600;">⚠️ Dokumen Tidak Lengkap</p>
                <p style="margin: 0; color: #666; font-size: 14px;">Silakan upload dokumen yang sesuai dengan syarat yang telah ditetapkan.</p>
            </div>
            <div class="upload-area" id="uploadArea" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                <div class="upload-area__icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <div class="upload-area__text">Drag & drop file di sini atau klik untuk memilih</div>
                <div class="upload-area__hint">Format: PDF, JPG, PNG (Max 10MB)</div>
                <input type="file" id="fileInput" style="display: none;" accept=".pdf,.jpg,.jpeg,.png" onchange="handleFileSelect(event)">
            </div>
        `;
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('uploadArea').addEventListener('click', () => {
                document.getElementById('fileInput').click();
            });
        });
        
        modalAction.textContent = 'Minta Susulan';
        modalAction.disabled = false;
        modalAction.onclick = () => {
            alert('Permintaan dokumen susulan telah dikirim ke email Anda. Silakan upload file melalui email atau portal kami.');
        };
    }
    
    modalContent.innerHTML = contentHTML;
    modal.style.display = 'flex';
    
    // Close on overlay click
    document.querySelector('.modal__overlay').onclick = closeDocModal;
}

function closeDocModal() {
    document.getElementById('docModal').style.display = 'none';
}

function closeActionModal() {
    document.getElementById('actionModal').style.display = 'none';
}

function handleApplicationAction(action, appId, appCode) {
    const modal = document.getElementById('actionModal');
    const modalTitle = document.getElementById('actionModalTitle');
    const modalContent = document.getElementById('actionModalContent');
    const modalConfirm = document.getElementById('actionModalConfirm');
    
    let title = '';
    let content = '';
    let confirmText = '';
    let confirmHandler = null;
    
    if (action === 'confirm') {
        title = 'Konfirmasi Kelengkapan Dokumen';
        content = `
            <div class="action-info">
                <p><strong>Kode Pengajuan:</strong> ${escapeHtml(appCode)}</p>
                <p style="margin-top: 15px;">Anda akan mengkonfirmasi bahwa semua dokumen yang diperlukan sudah lengkap dan telah diverifikasi.</p>
                <div style="background: #f0f8ff; padding: 15px; border-radius: 4px; margin-top: 15px;">
                    <p style="margin: 0; color: #333;"><strong>✓ Dokumen akan diproses oleh tim Finance</strong></p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Proses review memerlukan waktu 2-3 hari kerja</p>
                </div>
            </div>
        `;
        confirmText = 'Ya, Konfirmasi Dokumen';
        confirmHandler = () => {
            processAction('confirm', appId);
        };
    } else if (action === 'cancel') {
        title = 'Batalkan Pengajuan Kredit';
        content = `
            <div class="action-info">
                <p><strong>Kode Pengajuan:</strong> ${escapeHtml(appCode)}</p>
                <div style="background: #fff5f5; padding: 15px; border-radius: 4px; margin-top: 15px;">
                    <p style="margin: 0; color: #d32f2f;"><strong>⚠️ PERHATIAN</strong></p>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Tindakan ini tidak dapat dibatalkan. Pengajuan akan ditolak dan Anda dapat mengajukan kembali di kemudian hari.</p>
                </div>
                <div style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 10px;">
                        <input type="checkbox" id="cancelConfirm" style="margin-right: 8px;">
                        <span>Saya memahami dan ingin membatalkan pengajuan ini</span>
                    </label>
                </div>
            </div>
        `;
        confirmText = 'Ya, Batalkan Pengajuan';
        confirmHandler = () => {
            if (!document.getElementById('cancelConfirm').checked) {
                alert('Silakan centang konfirmasi terlebih dahulu');
                return;
            }
            processAction('cancel', appId);
        };
    } else if (action === 'resubmit') {
        title = 'Ajukan Ulang Pengajuan';
        content = `
            <div class="action-info">
                <p><strong>Kode Pengajuan:</strong> ${escapeHtml(appCode)}</p>
                <p style="margin-top: 15px;">Anda dapat mengajukan ulang pengajuan kredit dengan perbaikan yang diperlukan.</p>
                <div style="background: #f0f8ff; padding: 15px; border-radius: 4px; margin-top: 15px;">
                    <p style="margin: 0; color: #333;"><strong>Langkah berikutnya:</strong></p>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px; color: #666; font-size: 13px;">
                        <li>Siapkan dokumen yang telah diperbaiki</li>
                        <li>Verifikasi kesesuaian dengan kriteria leasing</li>
                        <li>Klik tombol "Lanjutkan" untuk memulai pengajuan baru</li>
                    </ul>
                </div>
            </div>
        `;
        confirmText = 'Ya, Ajukan Ulang';
        confirmHandler = () => {
            processAction('resubmit', appId);
        };
    }
    
    modalTitle.textContent = title;
    modalContent.innerHTML = content;
    modalConfirm.textContent = confirmText;
    modalConfirm.onclick = confirmHandler;
    
    modal.style.display = 'flex';
    document.querySelector('#actionModal .modal__overlay').onclick = closeActionModal;
}

function processAction(action, appId) {
    // Simulate API call
    let message = '';
    let redirectUrl = '';
    
    if (action === 'confirm') {
        message = '✓ Dokumen telah dikonfirmasi lengkap.\nTim Finance akan segera memproses pengajuan Anda dalam 2-3 hari kerja.\n\nAnda akan menerima notifikasi melalui email.';
        setTimeout(() => {
            alert(message);
            location.reload();
        }, 500);
    } else if (action === 'cancel') {
        message = '✓ Pengajuan telah dibatalkan.\nAnda dapat mengajukan kembali kapan saja.';
        setTimeout(() => {
            alert(message);
            location.reload();
        }, 500);
    } else if (action === 'resubmit') {
        message = '✓ Pengajuan baru telah dimulai.\nSilakan persiapkan dokumen dan ikuti proses pengajuan.';
        setTimeout(() => {
            alert(message);
            location.href = '?customer_id=' + new URLSearchParams(window.location.search).get('customer_id') + '&action=new_application';
        }, 500);
    }
    
    closeActionModal();
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'lengkap': return 'badge--success';
        case 'menunggu': return 'badge--warning';
        case 'kurang': return 'badge--danger';
        default: return 'badge--warning';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'lengkap': return '✓ Lengkap';
        case 'menunggu': return '⏳ Menunggu Verifikasi';
        case 'kurang': return '✗ Tidak Lengkap';
        default: return status;
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFileSelect({target: {files: files}});
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        const validTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        const validSize = 10 * 1024 * 1024; // 10MB
        
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan PDF, JPG, atau PNG.');
            return;
        }
        
        if (file.size > validSize) {
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            return;
        }
        
        alert(`File ${file.name} siap untuk diupload. Anda akan diarahkan ke halaman upload.`);
        // In a real app, this would upload the file via AJAX
    }
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocModal();
    }
});
</script>

</body>
</html>
