<div class="form-container" style="max-width: 600px; margin: 0 auto; padding-top: 20px;">
    <div class="form-header">
        <h2>Pencarian Suku Cadang</h2>
        <p>Cari dan request sparepart dari gudang untuk Work Order aktif.</p>
    </div>

    <div style="position: relative; margin-bottom: 24px;">
        <div class="form-group" style="margin-bottom: 0;">
            <input type="text" id="search_sparepart" class="form-input" placeholder=" " autocomplete="off">
            <label for="search_sparepart" class="form-label">🔍 Cari Nama / SKU Sparepart...</label>
        </div>

        <ul id="autocomplete-dropdown" class="autocomplete-list"></ul>
    </div>

    <div id="stock-indicator" class="stock-card" style="flex-direction: column; align-items: stretch; gap: 16px;">

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="stock-info">
                <span id="stock-sku" class="stock-sku" style="font-size: 12px; font-weight: 600; color: var(--primary);"></span>
                <h3 id="stock-name" class="stock-name" style="margin: 4px 0; font-size: 18px;"></h3>
                <p class="stock-qty" style="margin: 0; font-size: 14px; color: var(--text-muted);">
                    Sisa di gudang: <strong id="stock-count" style="color: var(--text-main);"></strong> unit
                </p>
            </div>
            <div class="stock-status">
                <span id="stock-badge" class="badge"></span>
            </div>
        </div>

        <div class="stock-action" style="border-top: 1px solid #e2e8f0; padding-top: 16px; display: flex; gap: 12px; align-items: center;">
            <input type="number" id="request-qty" class="form-input" value="1" min="1" style="width: 80px; padding: 10px; margin: 0; border-radius: 8px; text-align: center;" title="Jumlah yang dibutuhkan">
            <button id="btn-request-part" class="btn-submit" style="flex: 1; padding: 10px; margin: 0; border-radius: 8px;">Tambahkan ke Work Order</button>
        </div>

    </div>
    <div id="toast-overlay" class="toast-overlay">
        <div class="toast-box">
            <div id="toast-icon" class="spinner"></div>
            <span id="toast-text" style="font-weight: 500; color: var(--text-main);">Memproses request...</span>
        </div>
    </div>
</div>