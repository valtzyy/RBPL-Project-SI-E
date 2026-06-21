<?php $sidebarPath = ROOT_PATH . '/app/views/layouts/Sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Audit Log') ?></title>
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
        .field { display: flex; flex-direction: column; gap: 6px; min-width: 220px; }
        input, button { padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font: inherit; }
        button { cursor: pointer; background: #111827; color: #fff; border: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
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
                <h1 class="page-title">Audit Log</h1>
                <p class="muted">Riwayat aktivitas pengguna.</p>
            </div>
            <div class="topbar-action">⚙</div>
        </div>

        <div class="page">
            <section class="panel">
                <form method="GET" action="/reports/audit-log" class="row">
                    <label class="field">
                        <span>Module Filter</span>
                        <input type="text" name="module" value="<?= htmlspecialchars($filters['module'] ?? '') ?>" placeholder="AUDIT_LOG">
                    </label>
                    <label class="field">
                        <span>Limit</span>
                        <input type="number" name="limit" value="<?= htmlspecialchars((string) ($filters['limit'] ?? 100)) ?>" min="1" max="500">
                    </label>
                    <button type="submit">Filter</button>
                </form>
            </section>

            <section class="panel">
                <?php if (empty($auditLogs)): ?>
                    <p class="muted">DATABASE KOSONG</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Module</th>
                                <th>Deskripsi</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($auditLogs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['created_at'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['user_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['action'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['module'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['description'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($log['ip_address'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>
</body>
</html>
