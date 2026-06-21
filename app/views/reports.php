<?php $sidebarPath = ROOT_PATH . '/app/views/layouts/Sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Reports') ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f5f7; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }
        .sidebar { width: 235px; background: #edf1ff; color: #1f2937; padding: 22px 16px; box-sizing: border-box; border-right: 1px solid #d8dbe2; }
        .sidebar-brand { font-size: 34px; font-weight: 800; line-height: 1; margin-bottom: 28px; }
        .sidebar-profile { display: flex; gap: 12px; align-items: center; margin-bottom: 28px; padding: 8px 6px; }
        .sidebar-avatar { width: 40px; height: 40px; border-radius: 10px; background: #111827; color: #fff; display: grid; place-items: center; font-size: 13px; font-weight: 700; }
        .sidebar-name { font-size: 16px; font-weight: 700; }
        .sidebar-role { font-size: 13px; color: #5f6b7a; }
        .sidebar-nav { display: flex; flex-direction: column; gap: 8px; }
        .sidebar-link, .sidebar-summary, .sidebar-sublink { display: block; text-decoration: none; color: #374151; border-radius: 18px; padding: 14px 14px; font-size: 14px; }
        .sidebar-summary { list-style: none; cursor: pointer; background: transparent; font-weight: 700; }
        .sidebar-summary::-webkit-details-marker { display: none; }
        .sidebar-details { border-radius: 18px; background: transparent; }
        .sidebar-details[open] > .sidebar-summary { background: #dbe2ff; }
        .sidebar-sublink { margin-left: 10px; padding: 10px 14px; font-size: 13px; color: #4b5563; }
        .sidebar-link:hover, .sidebar-summary:hover, .sidebar-sublink:hover { background: #dbe2ff; }
        .sidebar-submenu { display: flex; flex-direction: column; gap: 4px; padding: 8px 0 6px; }
        .content { flex: 1; box-sizing: border-box; }
        .topbar { height: 68px; background: #fff; border-bottom: 1px solid #d8dbe2; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .page-title { margin: 0; font-size: 20px; }
        .topbar-action { width: 36px; height: 36px; display: grid; place-items: center; border: 1px solid #d8dbe2; border-radius: 8px; color: #243042; background: #fff; }
        .page { padding: 24px; }
        .panel { background: #fff; border: 1px solid #d8dbe2; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 1px 2px rgba(15,23,42,0.04); }
        .section-title { margin: 0 0 12px; font-size: 18px; color: #243042; }
        .muted { color: #6b7280; }
        .row { display: flex; flex-wrap: wrap; gap: 12px; }
        .field { display: flex; flex-direction: column; gap: 6px; min-width: 180px; }
        input, button { padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font: inherit; }
        button { cursor: pointer; background: #111827; color: #fff; border: 0; }
        button.secondary { background: #374151; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
        pre { margin: 0; white-space: pre-wrap; word-break: break-word; }
        @media (max-width: 900px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; }
        }
    </style>
</head>
<body>
<div class="app">
    <?php require $sidebarPath; ?>
    <main class="content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Reports</h1>
                <p class="muted">Panel testing internal report, export, dan audit log.</p>
            </div>
            <div class="topbar-action">⚙</div>
        </div>

        <div class="page">
            <section class="panel" id="filters">
                <h2 class="section-title">Filters</h2>
                <div class="row">
                    <label class="field"><span>Start Date</span><input type="date" id="start_date"></label>
                    <label class="field"><span>End Date</span><input type="date" id="end_date"></label>
                    <label class="field"><span>Status</span><input type="text" id="status" placeholder="process / lunas / approved"></label>
                    <label class="field"><span>Payment Type</span><input type="text" id="payment_type" placeholder="cash / credit"></label>
                    <label class="field"><span>Vehicle Type</span><input type="text" id="vehicle_type" placeholder="SUV / Sedan"></label>
                </div>
            </section>

            <section class="panel" id="sales-report">
                <h2 class="section-title">Sales Report</h2>
                <div class="actions">
                    <button type="button" onclick="loadReport('sales')">View</button>
                    <button type="button" class="secondary" onclick="downloadReport('sales', 'pdf')">Export PDF</button>
                    <button type="button" class="secondary" onclick="downloadReport('sales', 'excel')">Export Excel</button>
                </div>
            </section>

            <section class="panel" id="stock-report">
                <h2 class="section-title">Stock Report</h2>
                <div class="actions">
                    <button type="button" onclick="loadReport('stock')">View</button>
                    <button type="button" class="secondary" onclick="downloadReport('stock', 'pdf')">Export PDF</button>
                    <button type="button" class="secondary" onclick="downloadReport('stock', 'excel')">Export Excel</button>
                </div>
            </section>

            <section class="panel" id="credit-report">
                <h2 class="section-title">Credit Report</h2>
                <div class="actions">
                    <button type="button" onclick="loadReport('credit')">View</button>
                    <button type="button" class="secondary" onclick="downloadReport('credit', 'pdf')">Export PDF</button>
                    <button type="button" class="secondary" onclick="downloadReport('credit', 'excel')">Export Excel</button>
                </div>
            </section>

            <section class="panel" id="service-report">
                <h2 class="section-title">Service Report</h2>
                <div class="actions">
                    <button type="button" onclick="loadReport('service')">View</button>
                    <button type="button" class="secondary" onclick="downloadReport('service', 'pdf')">Export PDF</button>
                    <button type="button" class="secondary" onclick="downloadReport('service', 'excel')">Export Excel</button>
                </div>
            </section>

            <section class="panel" id="sparepart-report">
                <h2 class="section-title">Sparepart Report</h2>
                <div class="actions">
                    <button type="button" onclick="loadReport('sparepart')">View</button>
                    <button type="button" class="secondary" onclick="downloadReport('sparepart', 'pdf')">Export PDF</button>
                    <button type="button" class="secondary" onclick="downloadReport('sparepart', 'excel')">Export Excel</button>
                </div>
            </section>

            <section class="panel" id="export-pdf">
                <h2 class="section-title">Quick Export PDF</h2>
                <div class="actions">
                    <button type="button" onclick="downloadReport('sales', 'pdf')">Sales PDF</button>
                    <button type="button" onclick="downloadReport('stock', 'pdf')">Stock PDF</button>
                    <button type="button" onclick="downloadReport('credit', 'pdf')">Credit PDF</button>
                    <button type="button" onclick="downloadReport('service', 'pdf')">Service PDF</button>
                    <button type="button" onclick="downloadReport('sparepart', 'pdf')">Sparepart PDF</button>
                </div>
            </section>

            <section class="panel" id="export-excel">
                <h2 class="section-title">Quick Export Excel</h2>
                <div class="actions">
                    <button type="button" onclick="downloadReport('sales', 'excel')">Sales Excel</button>
                    <button type="button" onclick="downloadReport('stock', 'excel')">Stock Excel</button>
                    <button type="button" onclick="downloadReport('credit', 'excel')">Credit Excel</button>
                    <button type="button" onclick="downloadReport('service', 'excel')">Service Excel</button>
                    <button type="button" onclick="downloadReport('sparepart', 'excel')">Sparepart Excel</button>
                </div>
            </section>

            <section class="panel">
                <h2 class="section-title">Response</h2>
                <pre id="response-box">Belum ada response.</pre>
            </section>
        </div>
    </main>
</div>

<script>
function currentQueryString() {
    const params = new URLSearchParams();
    ['start_date', 'end_date', 'status', 'payment_type', 'vehicle_type'].forEach(function (field) {
        const value = document.getElementById(field).value.trim();
        if (value !== '') params.append(field, value);
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
</body>
</html>
