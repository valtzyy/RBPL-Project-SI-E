<div class="form-container">
    <?php if (isset($_GET['success'])): ?>
        <div style="background-color: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            ✓ Master Data Sparepart Berhasil Disimpan!
        </div>
    <?php endif; ?>
    <div class="form-header">
        <h2>Master Data Sparepart</h2>
        <p>Masukkan data suku cadang baru ke dalam sistem inventori DealerLink.</p>
    </div>

    <form id="form-sparepart" action="/sparepart/store" method="POST" autocomplete="off">
        <div class="form-grid">

            <div class="form-group">
                <input type="text" id="kode_sparepart" name="kode_sparepart" class="form-input" placeholder=" " required>
                <label for="kode_sparepart" class="form-label">SKU (Kode Part)</label>
                <span class="error-message">Wajib diisi!</span>
            </div>

            <div class="form-group">
                <input type="text" id="nama_sparepart" name="nama_sparepart" class="form-input" placeholder=" " required>
                <label for="nama_sparepart" class="form-label">Nama Sparepart</label>
                <span class="error-message">Wajib diisi!</span>
            </div>

            <div class="form-group">
                <input type="number" id="stok_awal" name="stok_awal" class="form-input" placeholder=" " required>
                <label for="stok_awal" class="form-label">Stok Awal (Pcs)</label>
                <span class="error-message">Wajib diisi!</span>
            </div>

            <div class="form-group">
                <input type="number" id="harga_jual" name="harga_jual" class="form-input input-with-prefix" placeholder=" " required>
                <span class="prefix">Rp</span>
                <label for="harga_jual" class="form-label">Harga Jual</label>
                <span class="error-message">Wajib diisi!</span>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Simpan Master Data</button>
        </div>
    </form>

    <div id="toast-overlay" class="toast-overlay">
        <div class="toast-box">
            <div id="toast-icon" class="spinner"></div>
            <span id="toast-text" style="font-weight: 500; color: var(--text-main);">Menyimpan data...</span>
        </div>
    </div>
</div>