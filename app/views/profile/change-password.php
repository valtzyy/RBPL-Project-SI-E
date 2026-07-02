<?php $sidebarPath = ROOT_PATH . '/app/views/layouts/Sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Ganti Password') ?> — DealerLink DMS</title>
    <style>
        body { margin: 0; font-family: 'Inter', Arial, sans-serif; background: #f4f5f7; color: #1f2937; }
        .app { display: flex; min-height: 100vh; }
        
        /* Sidebar Styling (Consistent with dashboard layout) */
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
        
        /* Main Page */
        .page { padding: 24px; flex: 1; display: flex; justify-content: center; align-items: flex-start; }
        
        /* Form Card Panel */
        .panel { background: #fff; border: 1px solid #d8dbe2; border-radius: 12px; padding: 32px; width: 100%; max-width: 500px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .panel-header { margin-bottom: 24px; }
        .panel-title { margin: 0 0 6px 0; font-size: 18px; font-weight: 700; color: #0f172a; }
        
        /* Alerts */
        .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-danger { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; }
        
        /* Action Buttons */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 20px; font-size: 14px; font-weight: 600; text-decoration: none; border-radius: 8px; transition: all 0.2s; cursor: pointer; border: 1px solid transparent; font-family: inherit; }
        .btn-primary { background: #0f172a; color: #fff; }
        .btn-primary:hover { background: #1e293b; }
        .btn-secondary { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
        .btn-secondary:hover { background: #e2e8f0; }
        
        /* Form Fields Styling */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 11px; font-weight: 700; color: #64748b; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; }
        .form-input { width: 100%; height: 42px; padding: 10px 14px; font-family: inherit; font-size: 14px; font-weight: 500; color: #0f172a; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; transition: all 0.2s; outline: none; }
        .form-input:focus { border-color: #0f172a; box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.06); }
        .form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 28px; }

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
                <h1 class="page-title">Ganti Password</h1>
                <p class="muted">Perbarui kata sandi keamanan akun Anda.</p>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page">
            
            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Keamanan Akun</h2>
                    <p class="muted" style="margin-top: 4px;">Harap isi kolom berikut untuk memperbarui password.</p>
                </div>

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

                <form method="POST" action="/change-password">
                    
                    <div class="form-group">
                        <label for="current_password" class="form-label">Password Lama</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            required 
                            class="form-input"
                            placeholder="Masukkan password saat ini"
                        >
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            required 
                            minlength="8" 
                            class="form-input"
                            placeholder="Minimal 8 karakter"
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required 
                            minlength="8" 
                            class="form-input"
                            placeholder="Ketik ulang password baru"
                        >
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="/" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbarui Password</button>
                    </div>
                </form>
            </section>
            
        </div>
    </main>
</div>
</body>
</html>
