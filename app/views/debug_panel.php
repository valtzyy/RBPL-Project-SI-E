<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Uji Coba / Debug Untuk Sprint 9</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .form-section {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            max-width: 400px;
            padding: 5px;
        }
        .btn {
            padding: 8px 15px;
            cursor: pointer;
        }
        .btn-reset {
            background-color: #f43f5e;
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: bold;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .response-box {
            margin-top: 15px;
            background-color: #f4f4f4;
            padding: 10px;
            border-left: 5px solid #0056b3;
            display: none;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .warning-banner {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border: 1px solid #ffeeba;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="warning-banner">
        <strong>⚠️ Halaman Debug / Pengujian Sementara</strong><br>
        Halaman ini dibuat khusus untuk memverifikasi alur API backend. Dapat dihapus saat website siap dideploy ke server production.
    </div>

    <h1>Panel Pengujian Backend</h1>
    <p>Gunakan tombol reset di bawah untuk mengembalikan data database uji coba ke status awal (Pending / Belum Bayar DP).</p>

    <button id="btnResetData" class="btn-reset">🔄 Reset Data Uji Coba</button>

    <!-- FORM 1: WEBHOOK APPROVAL -->
    <div class="form-section">
        <h2>⚡ Webhook Approval Leasing</h2>
        <p>Simulasi penerimaan status kelayakan kredit dari lembaga leasing luar.</p>
        
        <form id="formWebhook">
            <div class="form-group">
                <label for="w_id_kredit">ID Kredit (credit_applications.id)</label>
                <input type="number" id="w_id_kredit" value="1" required min="1">
            </div>
            
            <div class="form-group">
                <label for="w_status_approval">Status Approval</label>
                <select id="w_status_approval" required>
                    <option value="disetujui">disetujui (approved)</option>
                    <option value="ditolak">ditolak (rejected)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="w_catatan">Catatan Leasing</label>
                <input type="text" id="w_catatan" placeholder="Catatan tambahan keputusan..." value="Lolos verifikasi kelayakan kredit.">
            </div>
            
            <button type="submit" class="btn">Kirim Webhook POST</button>
        </form>

        <div class="response-box" id="resWebhook">
            <strong>Response JSON (<span id="badgeWebhook"></span>):</strong>
            <pre><code id="codeWebhook"></code></pre>
        </div>
    </div>

    <!-- FORM 2: VERIFIKASI DP -->
    <div class="form-section">
        <h2>💰 Verifikasi Down Payment (DP)</h2>
        <p>Simulasi pencatatan pelunasan DP customer oleh staf Finance dealer.</p>

        <form id="formFinance">
            <div class="form-group">
                <label for="f_id_kredit">ID Kredit (credit_applications.id)</label>
                <input type="number" id="f_id_kredit" value="1" required min="1">
            </div>
            
            <div class="form-group">
                <label for="f_nominal_dibayar">Nominal Uang DP yang Dibayar (Rp)</label>
                <input type="number" id="f_nominal_dibayar" value="15000000" required step="0.01">
            </div>

            <div class="form-group">
                <label for="f_verified_by">ID Finance (User ID Staf)</label>
                <input type="number" id="f_verified_by" value="3" placeholder="ID valid: 2=Admin, 3=Finance, 6=Manager">
            </div>
            
            <button type="submit" class="btn">Verifikasi & Lunaskan DP</button>
        </form>

        <div class="response-box" id="resFinance">
            <strong>Response JSON (<span id="badgeFinance"></span>):</strong>
            <pre><code id="codeFinance"></code></pre>
        </div>
    </div>



    <script>
        // Helper function untuk mencetak respon JSON ke kotak hasil
        function showResponse(boxId, badgeId, codeId, status, data) {
            const box = document.getElementById(boxId);
            const badge = document.getElementById(badgeId);
            const code = document.getElementById(codeId);
            
            box.style.display = "block";
            badge.textContent = status;
            code.textContent = JSON.stringify(data, null, 2);
        }

        // A. Handle Form Webhook Approval
        document.getElementById('formWebhook').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id_kredit = parseInt(document.getElementById('w_id_kredit').value);
            const status_approval = document.getElementById('w_status_approval').value;
            const catatan = document.getElementById('w_catatan').value;
            
            try {
                const response = await fetch('/webhook-approval', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_kredit, status_approval, catatan })
                });
                
                const data = await response.json();
                showResponse('resWebhook', 'badgeWebhook', 'codeWebhook', `${response.status} ${response.statusText}`, data);
            } catch (err) {
                showResponse('resWebhook', 'badgeWebhook', 'codeWebhook', "Fetch Error", { status: "error", message: err.message });
            }
        });

        // B. Handle Form Verifikasi DP
        document.getElementById('formFinance').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id_kredit = parseInt(document.getElementById('f_id_kredit').value);
            const nominal_dibayar = parseFloat(document.getElementById('f_nominal_dibayar').value);
            const verified_by_val = document.getElementById('f_verified_by').value;
            const verified_by = verified_by_val ? parseInt(verified_by_val) : null;
            
            try {
                const response = await fetch('/verifikasi-dp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_kredit, nominal_dibayar, verified_by })
                });
                
                const data = await response.json();
                showResponse('resFinance', 'badgeFinance', 'codeFinance', `${response.status} ${response.statusText}`, data);
            } catch (err) {
                showResponse('resFinance', 'badgeFinance', 'codeFinance', "Fetch Error", { status: "error", message: err.message });
            }
        });



        // D. Handle Reset Data Uji Coba
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
