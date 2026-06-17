<div class="container mt-5">
    <h2><?= $title ?? 'Test API Request Sparepart' ?></h2>
    
    <div class="row">
        <!-- Request Sparepart Card -->
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header bg-primary text-white">1. Request Sparepart (PBI-13.5)</div>
                <div class="card-body">
                    <form id="testForm">
                        <div class="mb-3">
                            <label class="form-label">Sparepart ID</label>
                            <input type="number" class="form-control" id="sparepart_id" name="sparepart_id" value="1" required>
                            <small class="text-muted">ID 1 = Oli Mesin Matic (Berdasarkan seeder)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Work Order ID</label>
                            <input type="number" class="form-control" id="work_order_id" name="work_order_id" value="101" required>
                            <small class="text-muted">ID 101 = Dummy Work Order</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Quantity (Jumlah Request)</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Kirim Request Sparepart</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Autocomplete Card -->
        <div class="col-md-6 mt-4">
            <div class="card">
                <div class="card-header bg-success text-white">2. Autocomplete Search API (PBI-13.4)</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cari Nama / SKU Sparepart</label>
                        <input type="text" class="form-control" id="searchQuery" placeholder="Ketik minimal 2 karakter (misal: Oli)...">
                    </div>
                    <div id="searchResults" class="list-group">
                        <p class="text-muted text-center">Hasil pencarian akan muncul di sini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Draft Invoice Card -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">3. Sync Draft Invoice Kasir (PBI-13.6)</div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Work Order ID</label>
                            <input type="number" class="form-control" id="invoice_wo_id" value="101" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button id="btnLoadInvoice" class="btn btn-warning w-100">Load Draft Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation links to other parts -->
    <div class="mt-4 text-center">
        <a href="/sparepart/create" class="btn btn-outline-secondary">Pergi ke Halaman Tambah Master Data Sparepart (PBI-13.2)</a>
    </div>

    <div class="mt-4">
        <h4>Response Output (JSON/Console):</h4>
        <pre id="responseOutput" class="bg-dark text-light p-3 rounded" style="min-height: 150px;">Menunggu aksi...</pre>
    </div>
</div>

<script>
// Handler 1: Request Sparepart
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const output = document.getElementById('responseOutput');
    output.innerHTML = 'Loading...';
    
    const formData = new FormData(this);
    
    fetch('/api/sparepart/request', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        output.innerHTML = "Status: " + response.status + "\n" + JSON.stringify(data, null, 4);
    })
    .catch(error => {
        output.innerHTML = 'Error: ' + error;
    });
});

// Handler 2: Autocomplete Search
document.getElementById('searchQuery').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsContainer = document.getElementById('searchResults');
    const output = document.getElementById('responseOutput');
    
    if (query.length < 2) {
        resultsContainer.innerHTML = '<p class="text-muted text-center">Hasil pencarian akan muncul di sini</p>';
        return;
    }
    
    fetch('/api/sparepart/search?q=' + encodeURIComponent(query))
    .then(response => response.json())
    .then(data => {
        resultsContainer.innerHTML = '';
        output.innerHTML = 'GET /api/sparepart/search?q=' + query + "\n\n" + JSON.stringify(data, null, 4);
        
        if (data.length === 0) {
            resultsContainer.innerHTML = '<div class="list-group-item text-danger">Tidak ditemukan sparepart matching!</div>';
            return;
        }
        
        data.forEach(item => {
            const btn = document.createElement('button');
            btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            btn.innerHTML = `
                <div>
                    <strong>${item.name}</strong><br>
                    <small class="text-muted">SKU: ${item.sku} | ID: ${item.id}</small>
                </div>
                <span class="badge bg-primary rounded-pill">Stok: ${item.stock}</span>
            `;
            resultsContainer.appendChild(btn);
        });
    })
    .catch(err => {
        resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error fetching data</div>';
    });
});

// Handler 3: Load Draft Invoice
document.getElementById('btnLoadInvoice').addEventListener('click', function() {
    const woId = document.getElementById('invoice_wo_id').value;
    const output = document.getElementById('responseOutput');
    output.innerHTML = 'Loading draft invoice...';
    
    fetch('/api/invoice/draft?work_order_id=' + woId)
    .then(async response => {
        const data = await response.json();
        output.innerHTML = "GET /api/invoice/draft?work_order_id=" + woId + "\nStatus: " + response.status + "\n" + JSON.stringify(data, null, 4);
    })
    .catch(error => {
        output.innerHTML = 'Error: ' + error;
    });
});
</script>
