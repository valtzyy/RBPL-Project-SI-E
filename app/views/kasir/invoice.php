<style>
    /* === SIHIR CSS: Aturan Khusus Saat Masuk Mode Print / Cetak === */
    @media print {

        /* 1. Sembunyikan elemen yang tidak boleh ikut tercetak */
        #toast-overlay,
        .form-header,
        .search-container,
        #btn-print-invoice {
            display: none !important;
        }

        /* 2. Bersihkan background, bayangan, dan margin agar rapi di kertas struk */
        body,
        .main-content,
        .form-container,
        #invoice-card {
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }

        /* 3. Pastikan warna teks tetap hitam tegas untuk tinta printer */
        * {
            color: black !important;
        }
    }
</style>

<div class="form-container" style="max-width: 700px; margin: 0 auto; padding-top: 20px;">
    <div class="form-header">
        <h2>Pusat Pembayaran Tagihan</h2>
        <p>Tarik draft invoice berdasarkan nomor Work Order dari mekanik.</p>
    </div>

    <div class="search-container" style="display: flex; gap: 16px; margin-bottom: 24px; align-items: stretch;">
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="number" id="input_wo_id" class="form-input" placeholder=" " value="1" required>
            <label for="input_wo_id" class="form-label">Nomor Work Order (Contoh: 1)</label>
        </div>
        <button id="btn-load-invoice" class="btn-submit" style="padding: 0 24px; margin: 0;">Cari Tagihan</button>
    </div>

    <div id="invoice-card" class="stock-card" style="flex-direction: column; gap: 16px; display: none;">
        <div style="border-bottom: 1px dashed var(--border-light); padding-bottom: 16px;">
            <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 4px;">Detail Transaksi</p>
            <h3 style="color: var(--primary-dark); font-size: 20px; margin: 0;">Work Order #<span id="inv-wo"></span></h3>
        </div>

        <div id="invoice-items" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 8px;">
        </div>

        <div style="border-top: 2px solid var(--primary-dark); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-weight: 600; color: var(--text-main); font-size: 16px;">TOTAL ESTIMASI</span>
            <span id="inv-total" style="font-weight: 700; color: var(--success-text); font-size: 24px;">Rp 0</span>
        </div>

        <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
            <button id="btn-print-invoice" class="btn-submit" style="background-color: var(--success); color: var(--success-text);">Cetak & Selesaikan Transaksi</button>
        </div>
    </div>
</div>

<div id="toast-overlay" class="toast-overlay">
    <div class="toast-box">
        <div id="toast-icon" class="spinner"></div>
        <span id="toast-text" style="font-weight: 500; color: var(--text-main);">Mencari tagihan...</span>
    </div>
</div>