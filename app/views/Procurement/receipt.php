<style>
    .procurement-container {
        max-width: 1000px;
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

    /* Cards */
    .procurement-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
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

    .form-input-qty {
        width: 90px;
        padding: 8px 12px;
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

    /* Form Actions */
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

    .vehicle-brand {
        font-weight: 700;
        color: #1A1D29;
    }
    
    .vehicle-meta {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }
</style>

<div class="procurement-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h2>🚚 Pencatatan Penerimaan Unit</h2>
            <p>Verifikasi kecocokan unit kendaraan fisik yang diterima dengan jumlah pesanan pengadaan.</p>
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="procurement-card">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <span style="font-size: 11px; color: #6B7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Kode Permintaan</span>
                <p style="font-size: 16px; font-weight: 800; color: #1A1D29; margin-top: 4px; font-family: monospace; background: #F3F4F6; padding: 4px 8px; border-radius: 6px; display: inline-block; border: 1px solid #E5E7EB;"><?= htmlspecialchars($procurement['request_code']) ?></p>
            </div>
            <div>
                <span style="font-size: 11px; color: #6B7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Status Pengadaan</span>
                <div style="margin-top: 6px;">
                    <?php if ($procurement['status'] === 'sent'): ?>
                        <span class="badge badge-sent">⏳ Sent</span>
                    <?php elseif ($procurement['status'] === 'received'): ?>
                        <span class="badge badge-received">✅ Received</span>
                    <?php else: ?>
                        <span class="badge badge-other"><?= htmlspecialchars(ucfirst($procurement['status'])) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Form & Table -->
    <div class="procurement-card">
        <div class="card-title">
            <span>🔍 Cocokkan Detail Barang</span>
        </div>
        <div class="card-subtitle">Silakan cocokkan jumlah unit fisik yang datang dengan jumlah pesanan di bawah ini.</div>

        <form method="POST" action="/procurement/receipt/store">
            <input type="hidden" name="procurement_id" value="<?= htmlspecialchars($procurement['id']) ?>">
            
            <div class="table-responsive">
                <table class="procurement-table">
                    <thead>
                        <tr>
                            <th>Detail Kendaraan</th>
                            <th style="width: 250px; text-align: center;">Jumlah Dipesan (Expected)</th>
                            <th style="width: 250px; text-align: center;">Jumlah Diterima (Actual)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td>
                                    <div class="vehicle-brand"><?= htmlspecialchars($detail['brand']) ?> <?= htmlspecialchars($detail['type']) ?></div>
                                    <div class="vehicle-meta">Warna: <?= htmlspecialchars($detail['color']) ?></div>
                                </td>
                                <td style="text-align: center; font-weight: 600; color: #374151;">
                                    <?= htmlspecialchars($detail['quantity']) ?> unit
                                </td>
                                <td style="text-align: center;">
                                    <input 
                                        type="number" 
                                        name="received_quantities[<?= htmlspecialchars($detail['vehicle_id']) ?>]" 
                                        class="form-input-qty"
                                        min="0" 
                                        value="<?= htmlspecialchars($detail['quantity']) ?>" 
                                        required
                                    >
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <a href="/procurement" class="btn-back">Kembali</a>
                <button type="submit" class="btn-submit">
                    Simpan & Validasi Penerimaan
                </button>
            </div>
        </form>
    </div>
</div>