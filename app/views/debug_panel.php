<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pengujian Sprint 9 - Kredit & Leasing</title>
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-glow: rgba(99, 102, 241, 0.15);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border-color: rgba(255, 255, 255, 0.08);
            --card-glow: rgba(99, 102, 241, 0.03);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            background-image: 
                radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.12) 0%, transparent 35%),
                radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.06) 0%, transparent 40%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        header p {
            color: var(--text-muted);
            font-size: 1.05rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .warning-banner {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            padding: 16px 20px;
            border-radius: 12px;
            color: #fef08a;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            backdrop-filter: blur(8px);
        }

        .warning-banner span {
            font-size: 1.3rem;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: var(--bg-secondary);
            padding: 16px 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--success);
        }

        .toolbar-title {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-main);
        }

        .btn-reset {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.35);
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        /* Sequential steps visualization */
        .flow-tracker {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }

        .step-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .step-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--border-color);
        }

        .step-card.active::before {
            background: linear-gradient(90deg, var(--accent) 0%, #818cf8 100%);
        }

        .step-card.success::before {
            background: var(--success);
        }

        .step-badge {
            position: absolute;
            top: 24px;
            right: 28px;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .step-badge.pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .step-badge.approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .step-badge.locked {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .step-number {
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 8px;
        }

        .step-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .step-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* Forms styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            width: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background-color: rgba(15, 23, 42, 0.8);
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Locked Card Layer */
        .locked-overlay {
            position: absolute;
            top: 4px;
            left: 0;
            width: 100%;
            height: calc(100% - 4px);
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(4px);
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 12px;
            border-radius: 0 0 20px 20px;
        }

        .locked-icon {
            font-size: 2rem;
        }

        .locked-overlay p {
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 500;
            text-align: center;
            max-width: 280px;
            line-height: 1.4;
        }

        /* JSON responses console style */
        .console-container {
            margin-top: 24px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: #090d16;
        }

        .console-header {
            background: rgba(255, 255, 255, 0.02);
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .console-tab {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .console-status {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .console-status.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .console-status.error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .console-body {
            padding: 16px;
            max-height: 200px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            line-height: 1.4;
            color: #38bdf8;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 768px) {
            .flow-tracker {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <header>
            <h1>Panel Uji Coba Alur Kredit</h1>
            <p>Halaman simulasi pengujian modul Kredit & Leasing (Persetujuan & Uang Muka) secara berurutan.</p>
        </header>

        <div class="warning-banner">
            <span>⚠️</span>
            <div>
                <strong>Alur Kerja Sekuensial:</strong> Pengajuan kredit harus disetujui oleh leasing terlebih dahulu (Langkah 1) agar sistem dapat menerima verifikasi pembayaran uang muka (Langkah 2).
            </div>
        </div>

        <div class="toolbar">
            <div class="toolbar-info">
                <div class="status-dot"></div>
                <div class="toolbar-title">Database Cloud Aiven Terhubung</div>
            </div>
            <button id="btnResetData" class="btn-reset">
                <span>🔄</span> Reset Database
            </button>
        </div>

        <div class="flow-tracker">
            
            <!-- STEP 1: WEBHOOK APPROVAL -->
            <div class="step-card active" id="cardStep1">
                <span class="step-badge pending" id="badgeStep1">Menunggu</span>
                <div class="step-number">Langkah 1</div>
                <div class="step-title">Approval Leasing</div>
                <div class="step-desc">Kirim persetujuan kelayakan kredit dari lembaga leasing luar.</div>

                <form id="formWebhook">
                    <div class="form-group">
                        <label for="w_id_kredit">ID Kredit (credit_applications.id)</label>
                        <input type="number" id="w_id_kredit" class="form-control" value="1" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="w_status_approval">Status Keputusan</label>
                        <select id="w_status_approval" class="form-control" required>
                            <option value="disetujui">disetujui (approved)</option>
                            <option value="ditolak">ditolak (rejected)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="w_catatan">Catatan</label>
                        <input type="text" id="w_catatan" class="form-control" value="Kriteria kredit terpenuhi." required>
                    </div>
                    
                    <button type="submit" class="btn-submit">Kirim Status Approval</button>
                </form>

                <div class="console-container hidden" id="consoleWebhook">
                    <div class="console-header">
                        <div class="console-tab">💻 API Response</div>
                        <div class="console-status success" id="statusBadgeWebhook">200 OK</div>
                    </div>
                    <div class="console-body">
                        <pre><code id="codeWebhook"></code></pre>
                    </div>
                </div>
            </div>

            <!-- STEP 2: VERIFIKASI DP -->
            <div class="step-card" id="cardStep2">
                <!-- Status Badge -->
                <span class="step-badge locked" id="badgeStep2">Terkunci</span>
                <div class="step-number">Langkah 2</div>
                <div class="step-title">Verifikasi Uang Muka</div>
                <div class="step-desc">Finance memproses pencatatan pelunasan uang muka (DP) customer.</div>

                <!-- Overlay to Enforce Sequence -->
                <div class="locked-overlay" id="overlayStep2">
                    <span class="locked-icon">🔒</span>
                    <p>Harap selesaikan dan setujui Langkah 1 (Approval Leasing) terlebih dahulu untuk membuka verifikasi ini.</p>
                </div>

                <form id="formFinance">
                    <div class="form-group">
                        <label for="f_id_kredit">ID Kredit (credit_applications.id)</label>
                        <input type="number" id="f_id_kredit" class="form-control" value="1" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="f_nominal_dibayar">Nominal Dibayar (Rp)</label>
                        <input type="number" id="f_nominal_dibayar" class="form-control" value="15000000" required step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="f_verified_by">ID Staf Finance</label>
                        <input type="number" id="f_verified_by" class="form-control" value="3" required>
                    </div>
                    
                    <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                        Verifikasi Pembayaran DP
                    </button>
                </form>

                <div class="console-container hidden" id="consoleFinance">
                    <div class="console-header">
                        <div class="console-tab">💻 API Response</div>
                        <div class="console-status success" id="statusBadgeFinance">200 OK</div>
                    </div>
                    <div class="console-body">
                        <pre><code id="codeFinance"></code></pre>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Track the current step flow state
        let isLeasingApproved = false;

        function setLeasingApprovedState(approved, responseData = null) {
            isLeasingApproved = approved;
            const card1 = document.getElementById('cardStep1');
            const card2 = document.getElementById('cardStep2');
            const badge1 = document.getElementById('badgeStep1');
            const badge2 = document.getElementById('badgeStep2');
            const overlay2 = document.getElementById('overlayStep2');
            const f_id_kredit = document.getElementById('f_id_kredit');
            const w_id_kredit = document.getElementById('w_id_kredit');

            if (approved) {
                // Update Step 1 card state to success
                card1.classList.remove('active');
                card1.classList.add('success');
                badge1.textContent = "Disetujui";
                badge1.className = "step-badge approved";

                // Unlock Step 2 card
                card2.classList.add('active');
                badge2.textContent = "Terbuka";
                badge2.className = "step-badge pending";
                overlay2.classList.add('hidden');
                
                // Copy ID Kredit automatically to Step 2 input
                f_id_kredit.value = w_id_kredit.value;
            } else {
                // Lock Step 2 card
                card1.classList.remove('success');
                card1.classList.add('active');
                badge1.textContent = "Menunggu";
                badge1.className = "step-badge pending";

                card2.classList.remove('active', 'success');
                badge2.textContent = "Terkunci";
                badge2.className = "step-badge locked";
                overlay2.classList.remove('hidden');
            }
        }

        // A. Handle Form Webhook Approval
        document.getElementById('formWebhook').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id_kredit = parseInt(document.getElementById('w_id_kredit').value);
            const status_approval = document.getElementById('w_status_approval').value;
            const catatan = document.getElementById('w_catatan').value;
            
            const consoleBox = document.getElementById('consoleWebhook');
            const statusBadge = document.getElementById('statusBadgeWebhook');
            const codeOutput = document.getElementById('codeWebhook');

            consoleBox.classList.remove('hidden');
            codeOutput.textContent = "⏳ Memproses request...";

            try {
                const response = await fetch('/webhook-approval', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_kredit, status_approval, catatan })
                });
                
                const data = await response.json();
                
                statusBadge.textContent = `${response.status} ${response.statusText}`;
                if (response.ok && data.status === "success") {
                    statusBadge.className = "console-status success";
                    codeOutput.textContent = JSON.stringify(data, null, 2);
                    
                    if (data.data && data.data.status_approval === 'approved') {
                        setLeasingApprovedState(true);
                    } else {
                        setLeasingApprovedState(false);
                    }
                } else {
                    statusBadge.className = "console-status error";
                    codeOutput.textContent = JSON.stringify(data, null, 2);
                    setLeasingApprovedState(false);
                }
            } catch (err) {
                statusBadge.textContent = "Fetch Error";
                statusBadge.className = "console-status error";
                codeOutput.textContent = JSON.stringify({ status: "error", message: err.message }, null, 2);
                setLeasingApprovedState(false);
            }
        });

        // B. Handle Form Verifikasi DP
        document.getElementById('formFinance').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id_kredit = parseInt(document.getElementById('f_id_kredit').value);
            const nominal_dibayar = parseFloat(document.getElementById('f_nominal_dibayar').value);
            const verified_by_val = document.getElementById('f_verified_by').value;
            const verified_by = verified_by_val ? parseInt(verified_by_val) : null;
            
            const consoleBox = document.getElementById('consoleFinance');
            const statusBadge = document.getElementById('statusBadgeFinance');
            const codeOutput = document.getElementById('codeFinance');

            consoleBox.classList.remove('hidden');
            codeOutput.textContent = "⏳ Memproses request...";

            try {
                const response = await fetch('/verifikasi-dp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_kredit, nominal_dibayar, verified_by })
                });
                
                const data = await response.json();
                
                statusBadge.textContent = `${response.status} ${response.statusText}`;
                if (response.ok && data.status === "success") {
                    statusBadge.className = "console-status success";
                    codeOutput.textContent = JSON.stringify(data, null, 2);
                    
                    // Mark step 2 card as completed/success
                    document.getElementById('cardStep2').classList.remove('active');
                    document.getElementById('cardStep2').classList.add('success');
                    document.getElementById('badgeStep2').textContent = "Lunas";
                    document.getElementById('badgeStep2').className = "step-badge approved";
                } else {
                    statusBadge.className = "console-status error";
                    codeOutput.textContent = JSON.stringify(data, null, 2);
                }
            } catch (err) {
                statusBadge.textContent = "Fetch Error";
                statusBadge.className = "console-status error";
                codeOutput.textContent = JSON.stringify({ status: "error", message: err.message }, null, 2);
            }
        });

        // C. Handle Reset Data Uji Coba
        document.getElementById('btnResetData').addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "⏳ Sedang Mereset...";
            
            try {
                const response = await fetch('/debug-reset', { method: 'POST' });
                const data = await response.json();
                
                if (data.status === "success") {
                    alert("Sukses! " + data.message);
                    // Reset UI states
                    setLeasingApprovedState(false);
                    document.getElementById('consoleWebhook').classList.add('hidden');
                    document.getElementById('consoleFinance').classList.add('hidden');
                } else {
                    alert("Gagal mereset: " + data.message);
                }
            } catch (err) {
                alert("Fetch Error: " + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
</body>
</html>
