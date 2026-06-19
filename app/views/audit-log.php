<h1><?= htmlspecialchars($title) ?></h1>

<p>Halaman ini dipakai untuk validasi internal Sprint 15 tanpa Postman.</p>

<div>
    <label for="start_date">Start Date</label>
    <input type="date" id="start_date" name="start_date">

    <label for="end_date">End Date</label>
    <input type="date" id="end_date" name="end_date">

    <label for="status">Status</label>
    <input type="text" id="status" name="status" placeholder="process / lunas / approved">

    <label for="payment_type">Payment Type</label>
    <input type="text" id="payment_type" name="payment_type" placeholder="cash / credit">

    <label for="vehicle_type">Vehicle Type</label>
    <input type="text" id="vehicle_type" name="vehicle_type" placeholder="SUV / Sedan">
</div>

<hr>

<div>
    <?php foreach ($reportTypes as $reportKey => $reportLabel): ?>
        <button type="button" onclick="loadReport('<?= htmlspecialchars($reportKey) ?>')">
            Lihat <?= htmlspecialchars($reportLabel) ?>
        </button>
        <button type="button" onclick="downloadReport('<?= htmlspecialchars($reportKey) ?>', 'pdf')">
            Export PDF <?= htmlspecialchars($reportLabel) ?>
        </button>
        <button type="button" onclick="downloadReport('<?= htmlspecialchars($reportKey) ?>', 'excel')">
            Export Excel <?= htmlspecialchars($reportLabel) ?>
        </button>
        <br><br>
    <?php endforeach; ?>

    <button type="button" onclick="loadAuditLogs()">Lihat Audit Log</button>
</div>

<hr>

<h2>Response</h2>
<pre id="response-box">Belum ada response.</pre>

<script>
function currentQueryString() {
    const params = new URLSearchParams();
    const mappings = ['start_date', 'end_date', 'status', 'payment_type', 'vehicle_type'];

    mappings.forEach(function (field) {
        const value = document.getElementById(field).value.trim();
        if (value !== '') {
            params.append(field, value);
        }
    });

    const query = params.toString();
    return query ? ('?' + query) : '';
}

function setResponse(payload) {
    document.getElementById('response-box').textContent = JSON.stringify(payload, null, 2);
}

function loadReport(type) {
    fetch('/api/reports/' + type + currentQueryString())
        .then(function (response) { return response.json(); })
        .then(function (data) { setResponse(data); })
        .catch(function (error) {
            setResponse({ success: false, message: error.message });
        });
}

function loadAuditLogs() {
    fetch('/api/audit-logs')
        .then(function (response) { return response.json(); })
        .then(function (data) { setResponse(data); })
        .catch(function (error) {
            setResponse({ success: false, message: error.message });
        });
}

function downloadReport(type, format) {
    let path = '/api/reports/' + type + '/export/' + format + currentQueryString();
    window.open(path, '_blank');
}
</script>
