<link rel="stylesheet" href="/CSS/style.css">
<div class="form-container" style="max-width: 700px; margin: 0 auto; padding-top: 20px;">
    <div class="form-header">
        <h2>Pencarian Suku Cadang Mekanik</h2>
        <p>Cari, periksa stok gudang, dan lakukan request sparepart untuk Work Order aktif.</p>
    </div>

    <div class="form-group" style="margin-bottom: 24px;">
        <input type="number" id="input_wo_id_mekanik" class="form-input" placeholder=" " value="<?= htmlspecialchars($_GET['id'] ?? '101') ?>" required>
        <label for="input_wo_id_mekanik" class="form-label">Nomor Work Order Aktif</label>
    </div>

    <div class="form-group" style="position: relative; margin-bottom: 24px;">
        <input type="text" id="search_sparepart" class="form-input" placeholder=" " autocomplete="off">
        <label for="search_sparepart" class="form-label">🔍 Cari Nama / SKU Sparepart...</label>

        <ul id="autocomplete-dropdown" class="autocomplete-dropdown"></ul>
    </div>

    <div id="stock-indicator" class="stock-card" style="display: none; flex-direction: column; gap: 16px; margin-bottom: 24px;">
        <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="stock-name" style="margin: 0; color: var(--primary-dark); font-size: 18px;">Nama Sparepart</h3>
                <p style="margin: 4px 0 0 0; color: var(--text-muted); font-size: 13px;">SKU: <span id="stock-sku">-</span></p>
            </div>
            <span id="stock-badge" class="badge">Status Stok</span>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: var(--text-main); font-weight: 500;">Stok Tersedia di Gudang:</span>
            <span style="font-size: 20px; font-weight: 700; color: var(--text-main);"><span id="stock-count">0</span> unit</span>
        </div>

        <div style="display: flex; gap: 16px; align-items: flex-end; margin-top: 8px; border-top: 1px dashed var(--border-light); padding-top: 16px;">
            <div class="form-group" style="flex: 1; margin: 0;">
                <input type="number" id="request-qty" class="form-input" value="1" min="1" placeholder=" ">
                <label for="request-qty" class="form-label">Jumlah Yang Diperlukan</label>
            </div>
            <button id="btn-request-part" class="btn-submit" style="margin: 0; padding: 0 28px; height: 48px; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; font-weight: 600; font-size: 15px;">
                Tambahkan ke Work Order
            </button>
        </div>
    </div>
</div>

<div id="toast-overlay" class="toast-overlay">
    <div class="toast-box">
        <div id="toast-icon" class="spinner"></div>
        <span id="toast-text" style="font-weight: 500; color: var(--text-main);">Memproses...</span>
    </div>
</div>