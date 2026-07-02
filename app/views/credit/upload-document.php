<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen Kredit</title>

    <link rel="stylesheet" href="/css/upload-document.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Upload Dokumen Kredit</h1>
        <p>Lengkapi dokumen pelanggan untuk proses verifikasi leasing.</p>
    </div>

    <form id="uploadForm" method="POST" action="/credit/upload">
        <input type="hidden" name="application_id" value="<?= htmlspecialchars($applicationId ?? '') ?>">

        <div class="content-grid">

            <!-- LEFT -->
            <div>

                <div class="card">
                    <h3>Data Pengajuan</h3>

                    <div class="form-grid">

                        <div class="form-group">
                            <label>Nomor Pengajuan</label>
                            <input type="text" value="<?= htmlspecialchars($applicationNo ?? '') ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nama Customer</label>
                            <input type="text" value="<?= htmlspecialchars($customerName ?? '') ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Kendaraan</label>
                            <input type="text" value="<?= htmlspecialchars($vehicle ?? '') ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Leasing</label>
                            <input type="text" value="<?= htmlspecialchars($leasing ?? '') ?>" readonly>
                        </div>

                    </div>
                </div>

                <div class="card mt-20">

                    <h3>Dokumen Persyaratan</h3>

                    <div class="upload-section">

                        <!-- KTP -->
                        <div class="file-upload-card">

                            <div class="upload-header">
                                <div>
                                    <h4>KTP</h4>
                                    <p>PDF / JPG / PNG</p>
                                </div>

                                <span id="ktpStatus" class="status pending">
                                    Belum Upload
                                </span>
                            </div>

                            <input
                                type="file"
                                id="ktpInput"
                                name="ktp"
                                accept=".pdf,.jpg,.jpeg,.png"
                                hidden>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="document.getElementById('ktpInput').click()">

                                Pilih File KTP

                            </button>

                            <div id="ktpFile" class="file-name">
                                Belum ada file
                            </div>

                        </div>

                        <!-- KK -->
                        <div class="file-upload-card">

                            <div class="upload-header">
                                <div>
                                    <h4>Kartu Keluarga</h4>
                                    <p>PDF / JPG / PNG</p>
                                </div>

                                <span id="kkStatus" class="status pending">
                                    Belum Upload
                                </span>
                            </div>

                            <input
                                type="file"
                                id="kkInput"
                                name="kk"
                                accept=".pdf,.jpg,.jpeg,.png"
                                hidden>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="document.getElementById('kkInput').click()">

                                Pilih File KK

                            </button>

                            <div id="kkFile" class="file-name">
                                Belum ada file
                            </div>

                        </div>

                        <!-- Slip -->
                        <div class="file-upload-card">

                            <div class="upload-header">
                                <div>
                                    <h4>Slip Gaji</h4>
                                    <p>PDF / JPG / PNG</p>
                                </div>

                                <span id="slipStatus" class="status pending">
                                    Belum Upload
                                </span>
                            </div>

                            <input
                                type="file"
                                id="slipInput"
                                name="slip"
                                accept=".pdf,.jpg,.jpeg,.png"
                                hidden>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                onclick="document.getElementById('slipInput').click()">

                                Pilih Slip Gaji

                            </button>

                            <div id="slipFile" class="file-name">
                                Belum ada file
                            </div>

                        </div>

                    </div>

                    <div class="action-buttons">
                        <a href="/">
                            <button type="button" class="btn btn-outline">
                            Simpan Draft
                            </button>
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Submit Verifikasi
                        </button>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div>

                <div class="summary-card">

                    <span class="summary-title">
                        KELENGKAPAN DOKUMEN
                    </span>

                    <h2 id="totalFile">
                        0 / 3
                    </h2>

                    <div class="progress">
                        <div
                            class="progress-bar"
                            id="progressBar">
                        </div>
                    </div>

                </div>

                <div class="card mt-20">

                    <h3>Checklist</h3>

                    <div class="check-item">
                        <span>KTP</span>
                        <span id="ktpCheck">○</span>
                    </div>

                    <div class="check-item">
                        <span>Kartu Keluarga</span>
                        <span id="kkCheck">○</span>
                    </div>

                    <div class="check-item">
                        <span>Slip Gaji</span>
                        <span id="slipCheck">○</span>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

<script src="/js/upload-document.js"></script>

</body>
</html>
