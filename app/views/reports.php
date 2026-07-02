<!-- Custom styles for reports panel inside main content area -->
<style>
    .reports-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .reports-header {
        margin-bottom: 8px;
    }
    .reports-header h1 {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.02em;
        color: #1A1D29;
    }
    .reports-header p {
        font-size: 14px;
        color: #6B7280;
        margin-top: 4px;
    }
    .reports-card {
        background: #ffffff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(11, 28, 48, 0.02);
    }
    .reports-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1A1D29;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .filter-input {
        padding: 10px 14px;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-size: 14px;
        color: #1A1D29;
        background: #F9FAFB;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .filter-input:focus {
        border-color: #4F5BD5;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 91, 213, 0.1);
    }
    .btn-row {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    .reports-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid #E5E7EB;
        background: #ffffff;
        color: #374151;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .reports-btn:hover {
        background: #F9FAFB;
        border-color: #D1D5DB;
    }
    .reports-btn-primary {
        background: #4F5BD5;
        color: #ffffff;
        border-color: #4F5BD5;
    }
    .reports-btn-primary:hover {
        background: #4350C4;
        border-color: #4350C4;
    }
    .reports-btn-secondary {
        background: #E8EAFB;
        color: #4F5BD5;
        border-color: #E8EAFB;
    }
    .reports-btn-secondary:hover {
        background: #D3D8F8;
    }
    .response-box {
        background: #1E1E2F;
        color: #A9B2C3;
        border-radius: 8px;
        padding: 18px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        overflow-x: auto;
        max-height: 400px;
        border: 1px solid #2D2D44;
    }
</style>

<div class="reports-container">
    <div class="reports-header">
        <h1>Reports</h1>
        <p>Panel testing internal report, export, dan audit log.</p>
    </div>

    <!-- Filters Section -->
    <div class="reports-card" id="filters">
        <div class="reports-card-title">🔍 Filters</div>
        <div class="filter-grid">
            <div class="filter-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" class="filter-input">
            </div>
            <div class="filter-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" class="filter-input">
            </div>
            <div class="filter-group">
                <label for="status">Status</label>
                <input type="text" id="status" class="filter-input" placeholder="process / lunas / approved">
            </div>
            <div class="filter-group">
                <label for="payment_type">Payment Type</label>
                <input type="text" id="payment_type" class="filter-input" placeholder="cash / credit">
            </div>
            <div class="filter-group">
                <label for="vehicle_type">Vehicle Type</label>
                <input type="text" id="vehicle_type" class="filter-input" placeholder="SUV / Sedan">
            </div>
            <div class="filter-group">
                <label for="sales">Sales (Name/ID)</label>
                <input type="text" id="sales" class="filter-input" placeholder="Nama sales / ID">
            </div>
        </div>
    </div>

    <div class="reports-grid">
        <!-- Sales Report -->
        <div class="reports-card" id="sales-report">
            <div class="reports-card-title">📊 Sales Report</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Lihat dan ekspor laporan transaksi penjualan mobil dealer.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn reports-btn-primary" onclick="loadReport('sales')">View</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('sales', 'pdf')">PDF</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('sales', 'excel')">Excel</button>
            </div>
        </div>

        <!-- Stock Report -->
        <div class="reports-card" id="stock-report">
            <div class="reports-card-title">🚗 Stock Report</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Lihat dan ekspor laporan ketersediaan stok kendaraan.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn reports-btn-primary" onclick="loadReport('stock')">View</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('stock', 'pdf')">PDF</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('stock', 'excel')">Excel</button>
            </div>
        </div>

        <!-- Credit Report -->
        <div class="reports-card" id="credit-report">
            <div class="reports-card-title">💳 Credit Report</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Lihat dan ekspor laporan verifikasi & status pengajuan kredit.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn reports-btn-primary" onclick="loadReport('credit')">View</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('credit', 'pdf')">PDF</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('credit', 'excel')">Excel</button>
            </div>
        </div>

        <!-- Service Report -->
        <div class="reports-card" id="service-report">
            <div class="reports-card-title">🔧 Service Report</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Lihat dan ekspor laporan booking & pengerjaan servis bengkel.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn reports-btn-primary" onclick="loadReport('service')">View</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('service', 'pdf')">PDF</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('service', 'excel')">Excel</button>
            </div>
        </div>

        <!-- Sparepart Report -->
        <div class="reports-card" id="sparepart-report">
            <div class="reports-card-title">⚙ Sparepart Report</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Lihat dan ekspor laporan stok suku cadang & logistik.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn reports-btn-primary" onclick="loadReport('sparepart')">View</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('sparepart', 'pdf')">PDF</button>
                <button type="button" class="reports-btn reports-btn-secondary" onclick="downloadReport('sparepart', 'excel')">Excel</button>
            </div>
        </div>

        <!-- Quick Export PDF -->
        <div class="reports-card" id="export-pdf">
            <div class="reports-card-title">📄 Quick Export PDF</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Ekspor cepat ke format file PDF untuk semua jenis modul.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn" onclick="downloadReport('sales', 'pdf')">Sales</button>
                <button type="button" class="reports-btn" onclick="downloadReport('stock', 'pdf')">Stock</button>
                <button type="button" class="reports-btn" onclick="downloadReport('credit', 'pdf')">Credit</button>
                <button type="button" class="reports-btn" onclick="downloadReport('service', 'pdf')">Service</button>
                <button type="button" class="reports-btn" onclick="downloadReport('sparepart', 'pdf')">Sparepart</button>
            </div>
        </div>

        <!-- Quick Export Excel -->
        <div class="reports-card" id="export-excel">
            <div class="reports-card-title">💚 Quick Export Excel</div>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;">Ekspor cepat ke format file Excel untuk analisis data.</p>
            <div class="btn-row">
                <button type="button" class="reports-btn" onclick="downloadReport('sales', 'excel')">Sales</button>
                <button type="button" class="reports-btn" onclick="downloadReport('stock', 'excel')">Stock</button>
                <button type="button" class="reports-btn" onclick="downloadReport('credit', 'excel')">Credit</button>
                <button type="button" class="reports-btn" onclick="downloadReport('service', 'excel')">Service</button>
                <button type="button" class="reports-btn" onclick="downloadReport('sparepart', 'excel')">Sparepart</button>
            </div>
        </div>
    </div>

    <!-- Response Box Section -->
    <div class="reports-card">
        <div class="reports-card-title">📝 Response Output</div>
        <pre id="response-box" class="response-box">Belum ada response.</pre>
    </div>
</div>

<script>
function currentQueryString() {
    const params = new URLSearchParams();
    ['start_date', 'end_date', 'status', 'payment_type', 'vehicle_type', 'sales'].forEach(function (field) {
        const input = document.getElementById(field);
        if (input) {
            const value = input.value.trim();
            if (value !== '') params.append(field, value);
        }
    });
    const query = params.toString();
    return query ? ('?' + query) : '';
}

function setResponse(payload) {
    document.getElementById('response-box').textContent = JSON.stringify(payload, null, 2);
}

function loadReport(type) {
    fetch('/reports/' + type + currentQueryString())
        .then(function (response) { return response.json(); })
        .then(function (data) { setResponse(data); })
        .catch(function (error) { setResponse({ success: false, message: error.message }); });
}

function downloadReport(type, format) {
    window.open('/reports/' + type + '/export/' + format + currentQueryString(), '_blank');
}
</script>
