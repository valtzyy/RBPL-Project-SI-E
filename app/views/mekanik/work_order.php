<div class="form-container">
    <div class="form-header">
        <h2>Tambah Sparepart ke Work Order</h2>
        <p>Cari part berdasarkan nama atau SKU untuk ditambahkan ke pekerjaan.</p>
    </div>

    <div class="search-wrapper">
        <div class="form-group">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            <input type="text" id="search_sparepart" class="form-input search-input" placeholder=" " autocomplete="off">
            <label for="search_sparepart" class="form-label search-label">Cari Nama / SKU Sparepart...</label>
        </div>

        <ul id="autocomplete-dropdown" class="dropdown-list"></ul>
    </div>

    <div id="stock-indicator" class="stock-card">
        <div class="stock-info">
            <span id="stock-sku" class="stock-sku"></span>
            <h3 id="stock-name" class="stock-name"></h3>
            <p class="stock-qty">Sisa di gudang: <strong id="stock-count"></strong> unit</p>
        </div>
        <div class="stock-status">
            <span id="stock-badge" class="badge"></span>
        </div>
    </div>
</div>