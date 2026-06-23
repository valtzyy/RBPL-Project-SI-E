<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pengujian Sprint 9 - Kredit & Leasing</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Link to the requested styling template -->
    <link rel="stylesheet" href="/css/upload-document.css">
    
    <style>
        /* Extra styling adjustments for the console output & overlay */
        .step-card {
            position: relative;
        }
        
        .locked-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 16px;
            padding: 20px;
        }

        .locked-overlay span {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .locked-overlay p {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            max-width: 300px;
            line-height: 1.5;
        }

        .console-box {
            margin-top: 15px;
            background: #0f172a;
            padding: 15px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
            color: #38bdf8;
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #1e293b;
        }

        .console-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 5px;
        }

        .console-title {
            color: #94a3b8;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        .console-badge {
            font-weight: 600;
            font-size: 11px;
        }

        pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Panel Pengujian Alur Kredit</h1>
        <p>Halaman simulasi pengujian modul Kredit & Leasing (Persetujuan & Uang Muka) secara berurutan.</p>
    </div>

    <div class="content-grid">

        <!-- LEFT COLUMN: forms -->
        <div>
            <!-- Card: Reset Database -->
            <div class="card">
                <h3>Reset Data Uji Coba</h3>
                <p style="color: #64748b; margin-top: 8px; margin-bottom: 15px; font-size: 14px;">
                    Kembalikan status data uji coba di database ke status awal (Belum Approval & Belum Bayar DP).
                </p>
                <button id="btnResetData" class="btn btn-outline" style="color: #ef4444; border-color: #fca5a5;">
                    🔄 Reset Database
                </button>
            </div>

            <!-- Card: Step 1 (Approval Leasing) -->
            <div class="card mt-20" id="cardStep1">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h3>Langkah 1: Approval Leasing</h3>
                    <span id="badgeStep1" class="status pending">Menunggu</span>
                </div>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                    Kirim persetujuan kelayakan kredit dari lembaga leasing luar.
                </p>

                <form id="formWebhook">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="w_id_kredit">ID Kredit (credit_applications.id)</label>
                            <input type="number" id="w_id_kredit" value="1" required min="1">
                        </div>

                        <div class="form-group">
                            <label for="w_catatan">Catatan</label>
                            <input type="text" id="w_catatan" value="Kriteria kredit terpenuhi." required>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary" id="btnWebhook">
                            Setujui Kredit Leasing (Approve)
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card: Step 2 (Verifikasi DP) -->
            <div class="card mt-20 step-card" id="cardStep2">
                <!-- Lock Overlay -->
                <div class="locked-overlay" id="overlayStep2">
                    <span>🔒</span>
                    <p>Harap selesaikan Langkah 1 (Approval Leasing) terlebih dahulu untuk membuka verifikasi ini.</p>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h3>Langkah 2: Verifikasi Uang Muka</h3>
                    <span id="badgeStep2" class="status pending">Terkunci</span>
                </div>
                <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                    Finance memproses pencatatan pelunasan uang muka (DP) customer.
                </p>

                <form id="formFinance">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="f_id_kredit">ID Kredit (credit_applications.id)</label>
                            <input type="number" id="f_id_kredit" value="1" required min="1">
                        </div>

                        <div class="form-group">
                            <label for="f_nominal_dibayar">Nominal Dibayar (Rp)</label>
                            <input type="number" id="f_nominal_dibayar" value="15000000" required step="0.01">
                        </div>

                        <div class="form-group" style="grid-column: span 2;">
                            <label for="f_verified_by">ID Staf Finance</label>
                            <input type="number" id="f_verified_by" value="3" required>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="btn btn-primary" style="background: #10b981;">
                            Verifikasi Pembayaran DP
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: summary & check list -->
        <div>
            <!-- Summary card (dark theme slate from template) -->
            <div class="summary-card">
                <span class="summary-title">ALUR PENGAJUAN KREDIT</span>
                <h2 id="totalFile">0 / 2</h2>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Steps Checklist Card -->
            <div class="card mt-20">
                <h3>Checklist Alur</h3>
                
                <div class="check-item">
                    <span>1. Approval Leasing</span>
                    <span id="checkStep1">○</span>
                </div>

                <div class="check-item">
                    <span>2. Verifikasi Uang Muka</span>
                    <span id="checkStep2">○</span>
                </div>
            </div>

            <!-- API Console Card -->
            <div class="card mt-20">
                <h3>Response API Console</h3>
                
                <div class="console-box">
                    <div class="console-header">
                        <span class="console-title">Console Log</span>
                        <span id="consoleStatus" class="console-badge" style="color: #64748b;">IDLE</span>
                    </div>
                    <pre><code id="consoleOutput">Belum ada aktivitas API. Silakan kirim form untuk melihat response.</code></pre>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Link to the requested JS logic file -->
<script src="/js/debug-panel.js"></script>

</body>
</html>
