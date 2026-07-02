<!-- Custom styles for audit log panel inside main content area -->
<style>
    .audit-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .audit-header {
        margin-bottom: 8px;
    }
    .audit-header h1 {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.02em;
        color: #1A1D29;
    }
    .audit-header p {
        font-size: 14px;
        color: #6B7280;
        margin-top: 4px;
    }
    .audit-card {
        background: #ffffff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(11, 28, 48, 0.02);
    }
    .audit-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1A1D29;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 200px;
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
        width: 100%;
    }
    .filter-input:focus {
        border-color: #4F5BD5;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 91, 213, 0.1);
    }
    .audit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid #4F5BD5;
        background: #4F5BD5;
        color: #ffffff;
        transition: all 0.15s ease;
        height: 41px;
    }
    .audit-btn:hover {
        background: #4350C4;
        border-color: #4350C4;
    }
    .audit-table-wrap {
        overflow-x: auto;
        margin-top: 10px;
    }
    .audit-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .audit-table th {
        padding: 14px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9CA3AF;
        border-bottom: 1px solid #E5E7EB;
    }
    .audit-table td {
        padding: 14px 16px;
        font-size: 13.5px;
        color: #374151;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    .audit-table tbody tr:hover {
        background: #FAFBFF;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9CA3AF;
        font-weight: 500;
    }
</style>

<div class="audit-container">
    <div class="audit-header">
        <h1>Audit Log</h1>
        <p>Riwayat aktivitas dan operasi sistem pengguna.</p>
    </div>

    <!-- Filter Section -->
    <div class="audit-card">
        <form method="GET" action="/reports/audit-log" class="filter-row">
            <div class="filter-group">
                <label for="module">Module Filter</label>
                <input type="text" id="module" name="module" class="filter-input" value="<?= htmlspecialchars($filters['module'] ?? '') ?>" placeholder="Contoh: AUDIT_LOG">
            </div>
            <div class="filter-group">
                <label for="limit">Limit</label>
                <input type="number" id="limit" name="limit" class="filter-input" value="<?= htmlspecialchars((string) ($filters['limit'] ?? 100)) ?>" min="1" max="500">
            </div>
            <button type="submit" class="audit-btn">Filter Aktivitas</button>
        </form>
    </div>

    <!-- Logs Table Section -->
    <div class="audit-card">
        <div class="audit-card-title">📝 Log Aktivitas Sistem</div>
        <?php if (empty($auditLogs)): ?>
            <div class="empty-state">
                <p>DATABASE KOSONG</p>
            </div>
        <?php else: ?>
            <div class="audit-table-wrap">
                <table class="audit-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Module</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($auditLogs as $log): ?>
                            <tr>
                                <td style="white-space: nowrap; font-weight: 600; color: #1A1D29;"><?= htmlspecialchars($log['created_at'] ?? '-') ?></td>
                                <td><span style="background: #E8EAFB; color: #4F5BD5; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 12px;"><?= htmlspecialchars($log['user_name'] ?? '-') ?></span></td>
                                <td><span style="font-weight: 600;"><?= htmlspecialchars($log['action'] ?? '-') ?></span></td>
                                <td><span style="background: #F3F4F6; color: #4B5563; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;"><?= htmlspecialchars($log['module'] ?? '-') ?></span></td>
                                <td><?= htmlspecialchars($log['description'] ?? '-') ?></td>
                                <td style="font-family: monospace; font-size: 12px; color: #6B7280;"><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
