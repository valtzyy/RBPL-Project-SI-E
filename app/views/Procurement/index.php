<?php
// Calculate statistics from the procurements array
$total_procurements = 0;
$sent_procurements = 0;
$received_procurements = 0;

if (!empty($procurements)) {
    $total_procurements = count($procurements);
    foreach ($procurements as $p) {
        if (($p['status'] ?? '') === 'sent') {
            $sent_procurements++;
        } elseif (($p['status'] ?? '') === 'received') {
            $received_procurements++;
        }
    }
}
?>

<style>
    .procurement-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4px;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .dashboard-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1A1D29;
        margin: 0 0 4px 0;
        letter-spacing: -0.02em;
    }
    
    .dashboard-header p {
        font-size: 14px;
        color: #6B7280;
        margin: 0;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    
    .stat-card.blue::before { background-color: #4F5BD5; }
    .stat-card.yellow::before { background-color: #FBBF24; }
    .stat-card.green::before { background-color: #10B981; }
    
    .stat-title {
        font-size: 12px;
        font-weight: 700;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    
    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1A1D29;
        margin: 0;
    }

    /* Cards */
    .procurement-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 28px;
        padding: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1A1D29;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-subtitle {
        font-size: 13.5px;
        color: #6B7280;
        margin-bottom: 20px;
    }

    /* Forms */
    .row-vehicle {
        display: flex;
        gap: 16px;
        align-items: center;
        padding: 12px 16px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        margin-bottom: 12px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .row-vehicle:hover {
        border-color: #CBD5E1;
    }

    .form-select {
        flex: 1;
        padding: 10px 14px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        background-color: #ffffff;
        color: #1A1D29;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 18px;
        padding-right: 36px;
    }

    .form-select:focus {
        border-color: #4F5BD5;
        box-shadow: 0 0 0 3px rgba(79, 91, 213, 0.15);
    }

    .qty-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 140px;
        flex-shrink: 0;
    }

    .qty-label {
        font-size: 13px;
        font-weight: 600;
        color: #4B5563;
    }

    .form-input-qty {
        width: 75px;
        padding: 10px 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        outline: none;
        text-align: center;
        transition: border-color 0.15s ease;
    }

    .form-input-qty:focus {
        border-color: #4F5BD5;
        box-shadow: 0 0 0 3px rgba(79, 91, 213, 0.15);
    }

    /* Buttons */
    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background-color: transparent;
        border: 1px dashed #4F5BD5;
        color: #4F5BD5;
        font-weight: 600;
        font-size: 13.5px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none;
    }

    .btn-add:hover {
        background-color: rgba(79, 91, 213, 0.05);
        border-style: solid;
    }

    .btn-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        background-color: #FEE2E2;
        color: #DC2626;
        border: 1px solid #FCA5A5;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        transition: all 0.15s ease;
        flex-shrink: 0;
        outline: none;
    }

    .btn-delete:hover {
        background-color: #FCA5A5;
        color: #B91C1C;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        border-top: 1px solid #E5E7EB;
        padding-top: 20px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background-color: #F3F4F6;
        color: #4B5563;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .btn-back:hover {
        background-color: #E5E7EB;
        color: #1F2937;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px;
        background-color: #4F5BD5;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(79, 91, 213, 0.1);
        transition: all 0.15s ease;
        outline: none;
    }

    .btn-submit:hover {
        background-color: #3B47B8;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(79, 91, 213, 0.15);
    }

    /* Table */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .procurement-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .procurement-table th {
        background-color: #F9FAFB;
        padding: 14px 18px;
        font-weight: 600;
        font-size: 12px;
        color: #4B5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #E5E7EB;
    }

    .procurement-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
        color: #374151;
    }

    .procurement-table tbody tr:last-child td {
        border-bottom: none;
    }

    .procurement-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    .procurement-code {
        background-color: #F3F4F6;
        color: #1F2937;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 13px;
        border: 1px solid #E5E7EB;
        font-family: monospace;
        font-weight: 600;
        display: inline-block;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-sent {
        background-color: #FEF3C7;
        color: #D97706;
        border: 1px solid #FDE68A;
    }

    .badge-received {
        background-color: #D1FAE5;
        color: #059669;
        border: 1px solid #A7F3D0;
    }

    .badge-other {
        background-color: #F3F4F6;
        color: #4B5563;
        border: 1px solid #E5E7EB;
    }

    .btn-action {
        display: inline-block;
        padding: 6px 12px;
        background-color: #4F5BD5;
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        font-size: 12.5px;
        border-radius: 6px;
        transition: all 0.15s ease;
        text-align: center;
    }

    .btn-action:hover {
        background-color: #3B47B8;
        box-shadow: 0 2px 4px rgba(79, 91, 213, 0.15);
    }

    .empty-state {
        color: #6B7280;
        padding: 32px 18px !important;
        font-style: italic;
    }
</style>

<div class="procurement-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>📋 Pengadaan Kendaraan</h2>
            <p>Kelola dan buat permintaan unit kendaraan baru dari pabrik serta rekam penerimaan unit fisik.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <span class="stat-title">Total Permintaan</span>
            <p class="stat-value"><?= $total_procurements ?> Request</p>
        </div>
        <div class="stat-card yellow">
            <span class="stat-title">Menunggu Kedatangan</span>
            <p class="stat-value"><?= $sent_procurements ?> Sent</p>
        </div>
        <div class="stat-card green">
            <span class="stat-title">Selesai Diterima</span>
            <p class="stat-value"><?= $received_procurements ?> Received</p>
        </div>
    </div>

    <!-- Form Procurement Request -->
    <div class="procurement-card">
        <div class="card-title">
            <span>➕ Form Permintaan Baru</span>
        </div>
        <div class="card-subtitle">Buat permintaan pengadaan untuk satu atau beberapa unit kendaraan sekaligus.</div>

        <form method="POST" action="/procurement/store">
            <div id="vehicles-list">
                <!-- Dynamic rows will be inserted here -->
            </div>

            <div style="margin-top: 16px;">
                <button type="button" class="btn-add" onclick="createRow()">
                    + Tambah Kendaraan
                </button>
            </div>

            <div class="form-actions">
                <a href="/" class="btn-back">Kembali</a>
                <button type="submit" class="btn-submit">
                    Kirim Permintaan
                </button>
            </div>
        </form>
    </div>

    <!-- Table Procurement List -->
    <div class="procurement-card">
        <div class="card-title">
            <span>📊 Daftar Permintaan Pengadaan</span>
        </div>
        <div class="card-subtitle">Berikut adalah data permintaan pengadaan kendaraan. Pilih permintaan berstatus <strong>sent</strong> untuk merekam penerimaan barang dari pabrik.</div>

        <div class="table-responsive">
            <table class="procurement-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Kode Permintaan</th>
                        <th>ID Pembuat</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th style="width: 180px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($procurements)): ?>
                        <tr>
                            <td colspan="6" class="text-center empty-state">Tidak ada data pengadaan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($procurements as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['id']) ?></td>
                                <td>
                                    <span class="procurement-code"><?= htmlspecialchars($p['request_code']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($p['requested_by']) ?></td>
                                <td>
                                    <?php if ($p['status'] === 'sent'): ?>
                                        <span class="badge badge-sent">⏳ Sent</span>
                                    <?php elseif ($p['status'] === 'received'): ?>
                                        <span class="badge badge-received">✅ Received</span>
                                    <?php else: ?>
                                        <span class="badge badge-other"><?= htmlspecialchars(ucfirst($p['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['created_at']) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($p['status'] === 'sent'): ?>
                                        <a href="/procurement/receipt/<?= htmlspecialchars($p['id']) ?>" class="btn-action">
                                            Pengecekan Unit
                                        </a>
                                    <?php elseif ($p['status'] === 'received'): ?>
                                        <span style="color: #6B7280; font-size: 13px; font-weight: 500;">Selesai</span>
                                    <?php else: ?>
                                        <span style="color: #9CA3AF;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Data kendaraan dari PHP
    const vehiclesData = <?= json_encode($vehicles); ?>;

    function createRow() {
        const container = document.getElementById('vehicles-list');
        const row = document.createElement('div');
        row.className = 'row-vehicle';
        
        // Select Kendaraan
        const select = document.createElement('select');
        select.name = 'vehicle_ids[]';
        select.className = 'form-select';
        select.required = true;
        
        // Default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Pilih Kendaraan --';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        select.appendChild(defaultOption);

        vehiclesData.forEach(vehicle => {
            const option = document.createElement('option');
            option.value = vehicle.id;
            const priceFormatted = Number(vehicle.price).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            option.textContent = `${vehicle.brand} ${vehicle.type} (${vehicle.color}) - ${priceFormatted}`;
            select.appendChild(option);
        });

        // Input Jumlah
        const qtyWrapper = document.createElement('div');
        qtyWrapper.className = 'qty-input-wrapper';

        const qtyLabel = document.createElement('span');
        qtyLabel.className = 'qty-label';
        qtyLabel.textContent = 'Qty:';

        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.className = 'form-input-qty';
        qtyInput.name = 'quantities[]';
        qtyInput.min = '1';
        qtyInput.value = '1';
        qtyInput.required = true;

        qtyWrapper.appendChild(qtyLabel);
        qtyWrapper.appendChild(qtyInput);

        // Button Hapus
        const btnDelete = document.createElement('button');
        btnDelete.type = 'button';
        btnDelete.className = 'btn-delete';
        btnDelete.innerHTML = '&times;';
        btnDelete.title = 'Hapus baris';
        btnDelete.onclick = function() {
            if (container.children.length > 1) {
                row.remove();
            } else {
                alert('Minimal harus ada 1 baris pengadaan kendaraan.');
            }
        };

        row.appendChild(select);
        row.appendChild(qtyWrapper);
        row.appendChild(btnDelete);
        
        container.appendChild(row);
    }

    // Load first row automatically on page load
    document.addEventListener('DOMContentLoaded', () => {
        createRow();
    });
</script>