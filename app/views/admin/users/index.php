<?php $sidebarPath = ROOT_PATH . '/app/views/layouts/Sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Manajemen Akun') ?> — DealerLink DMS</title>
    <style>
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: #f4f5f7; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }
        
        /* Sidebar Styling (Consistent with audit-log.php & reports.php) */
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
        
        /* Layout Content */
        .content { flex: 1; box-sizing: border-box; display: flex; flex-direction: column; }
        
        /* Topbar Styling */
        .topbar { height: 68px; background: #fff; border-bottom: 1px solid #d8dbe2; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .page-title { margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; }
        .muted { color: #64748b; font-size: 13px; margin: 2px 0 0 0; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        
        /* Main Page */
        .page { padding: 24px; flex: 1; }
        
        /* Card Panel */
        .panel { background: #fff; border: 1px solid #d8dbe2; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .panel-title { margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; }
        
        /* Alerts */
        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        
        /* Action Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 16px; font-size: 13.5px; font-weight: 600; text-decoration: none; border-radius: 8px; transition: all 0.2s; cursor: pointer; border: 1px solid transparent; font-family: inherit; }
        .btn-primary { background: #0f172a; color: #fff; }
        .btn-primary:hover { background: #1e293b; }
        .btn-secondary { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #fef2f2; color: #b91c1c; border-color: #fca5a5; }
        .btn-danger:hover { background: #fee2e2; }
        
        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        /* Table Design */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f8fafc; padding: 14px 16px; border-bottom: 2px solid #e2e8f0; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; color: #334155; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }
        
        /* Action cell gap */
        .actions-cell { display: flex; gap: 8px; align-items: center; }
        
        @media (max-width: 900px) {
            .app { flex-direction: column; }
            .sidebar { width: 100%; }
        }
    </style>
</head>
<body>
<div class="app">
    <!-- Include Sidebar -->
    <?php require $sidebarPath; ?>
    
    <main class="content">
        <!-- Topbar -->
        <div class="topbar">
            <div>
                <h1 class="page-title">Manajemen Akun</h1>
                <p class="muted">Kelola akun pengguna operasional sistem DealerLink DMS.</p>
            </div>
            <div class="topbar-right">
                <a href="/change-password" class="btn btn-secondary">🔒 Ganti Password</a>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page">
            
            <!-- Alert Messages -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <div><?= htmlspecialchars($success) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <!-- Table Card -->
            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Daftar Akun Operasional</h2>
                    <a href="/admin/users/create" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Tambah Akun Baru
                    </a>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Hak Akses (Role)</th>
                                <th>Status</th>
                                <th style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span style="background: #eff6ff; color: #1e40af; padding: 4px 8px; border-radius: 6px; font-size: 12.5px; font-weight: 500;">
                                            <?= htmlspecialchars($user['role_name']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions-cell">
                                            <a href="/admin/users/<?= (int) $user['id'] ?>/edit" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12.5px;">Edit</a>

                                            <?php if ($user['status'] === 'active'): ?>
                                                <form method="POST" action="/admin/users/<?= (int) $user['id'] ?>/deactivate" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan akun <?= htmlspecialchars(addslashes($user['name'])) ?>?');">
                                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12.5px;">Nonaktifkan</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            
        </div>
    </main>
</div>
</body>
</html>
