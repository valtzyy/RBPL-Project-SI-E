<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Suite Panel Kredit & Leasing (Plain HTML)</title>
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
    </style>
</head>
<body>

    <h1>Testing Panel UI (Plain HTML)</h1>
    <p>Gunakan tombol reset di bawah untuk membersihkan data uji coba (Kredit ID 1 & Transaksi ID 1) sebelum melakukan simulasi baru.</p>

    <button id="btnResetData" class="btn-reset">🔄 Reset Data Uji Coba</button>

    <!-- FORM 1: WEBHOOK APPROVAL -->
    <div class="form-section">
        <h2>⚡ Webhook Approval Leasing</h2>
        <p>Menerima keputusan kredit dari lembaga leasing luar. Men-trigger status transaksi ke antrean serah terima jika DP lunas.</p>
        
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
        <p>Staf Finance mencatat pelunasan DP customer. Men-trigger status transaksi ke antrean serah terima jika kredit disetujui.</p>

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

    <!-- FORM 3: UPLOAD DOKUMEN -->
    <div class="form-section">
        <h2>📄 Unggah Dokumen Kredit</h2>
        <p>Mengunggah dokumen pendukung kredit (PDF, JPG, JPEG, atau PNG) berukuran maksimal 2MB.</p>

        <form id="formUpload" enctype="multipart/form-data">
            <div class="form-group">
                <label for="u_id_kredit">ID Kredit (credit_applications.id)</label>
                <input type="number" id="u_id_kredit" name="id_kredit" value="1" required min="1">
            </div>

            <div class="form-group">
                <label for="file_type">Simpan Sebagai Tipe Dokumen</label>
                <select id="file_type" name="file_type" required>
                    <option value="SlipGaji">SlipGaji (Slip Gaji)</option>
                    <option value="KTP">KTP (Kartu Tanda Penduduk)</option>
                    <option value="KK">KK (Kartu Keluarga)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="file_kontrak">File Dokumen (PDF/Image max 2MB)</label>
                <input type="file" id="file_kontrak" name="file_kontrak" accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            
            <button type="submit" class="btn">Unggah & Simpan Dokumen</button>
        </form>

        <div class="response-box" id="resUpload">
            <strong>Response JSON (<span id="badgeUpload"></span>):</strong>
            <pre><code id="codeUpload"></code></pre>
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

        // C. Handle Form Upload Kontrak PK (Menggunakan FormData)
        document.getElementById('formUpload').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/upload-kontrak', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                showResponse('resUpload', 'badgeUpload', 'codeUpload', `${response.status} ${response.statusText}`, data);
            } catch (err) {
                showResponse('resUpload', 'badgeUpload', 'codeUpload', "Fetch Error", { status: "error", message: err.message });
            }
        });

        // D. Handle Reset Data Uji Coba
        document.getElementById('btnResetData').addEventListener('click', async function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "⏳ Sedang Mereset...";
            
            try {
                const response = await fetch('/reset-sprint9', { method: 'POST' });
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
