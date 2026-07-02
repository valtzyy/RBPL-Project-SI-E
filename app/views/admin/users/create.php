<?php
$title = $title ?? 'Tambah Akun';
?>

<style>
    /* Form Card Panel */
    .panel { 
        background: #fff; 
        border: 1px solid #d8dbe2; 
        border-radius: 12px; 
        padding: 32px; 
        width: 100%; 
        max-width: 600px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.02); 
        margin: 0 auto;
    }
    .panel-header { margin-bottom: 24px; }
    .panel-title { margin: 0 0 6px 0; font-size: 18px; font-weight: 700; color: #0f172a; }
    
    /* Alerts */
    .alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500; }
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
</style>

<!-- Header Row -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">➕ Tambah Akun</h1>
        <p class="text-muted mb-0">Buat akun pengguna baru dengan hak akses spesifik.</p>
    </div>
</div>

<!-- Page Content -->
<div class="d-flex justify-content-center align-items-start">
    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Informasi Akun Baru</h2>
            <p class="text-muted mb-0" style="margin-top: 4px; font-size: 13px;">Isi formulir berikut untuk mendaftarkan akun baru.</p>
        </div>

        <!-- Alert Messages -->
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

        <form method="POST" action="/admin/users">
            <!-- Form Fields -->
            <?php require ROOT_PATH . '/app/views/admin/users/form.php'; ?>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="/admin/users" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Akun</button>
            </div>
        </form>
    </section>
</div>
