<?php
/**
 * Form Registrasi Pelanggan Baru
 */
$sidebarPath = ROOT_PATH . '/app/views/layouts/SideBar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftarkan Pelanggan — DealerLink DMS</title>
  <meta name="description" content="Form pendaftaran pelanggan baru di DealerLink DMS.">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: #F3F4F8;
      color: #1A1D29;
      font-size: 14px;
    }

    .shell    { display: flex; min-height: 100vh; flex-direction: column; }
    .body-row { display: flex; flex: 1; }

    /* ── Topbar ── */
    .topbar {
      height: 56px; background: #fff;
      border-bottom: 1px solid #E5E7EB;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 28px; position: sticky; top: 0; z-index: 30;
    }
    .topbar-left { display: flex; align-items: center; gap: 16px; }
    .hamburger   { display: flex; flex-direction: column; gap: 4px; cursor: pointer; }
    .hamburger span { display: block; width: 20px; height: 2px; background: #374151; border-radius: 2px; }
    .brand      { font-size: 19px; font-weight: 800; letter-spacing: -.02em; }
    .brand span { color: #4F5BD5; }
    .topbar-right { display: flex; align-items: center; gap: 20px; font-size: 13.5px; font-weight: 500; color: #6B7280; }
    .topbar-right a { color: inherit; text-decoration: none; }
    .bell {
      width: 34px; height: 34px; border-radius: 50%;
      border: 1px solid #E5E7EB;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; cursor: pointer;
    }
    .user-greeting { font-size: 13.5px; color: #374151; }
    .logout-btn {
      background: none; border: none; color: #DC2626;
      font-family: inherit; font-size: 13.5px; font-weight: 600;
      cursor: pointer; padding: 4px 8px; border-radius: 6px;
      transition: background-color 0.15s;
    }
    .logout-btn:hover { background: #FEE2E2; }

    /* ── Sidebar ── */
    .sidebar {
      width: 232px; flex-shrink: 0;
      background: #F1F3FB;
      border-right: 1px solid #E5E7EB;
      padding: 16px 12px;
    }
    .profile {
      display: flex; align-items: center; gap: 10px;
      padding: 6px 8px 16px; border-bottom: 1px solid #E5E7EB; margin-bottom: 10px;
    }
    .avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: #E8EAFB; color: #4F5BD5;
      font-size: 13px; font-weight: 800;
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .profile-name { font-size: 13.5px; font-weight: 700; }
    .profile-role { font-size: 11.5px; color: #6B7280; margin-top: 1px; }
    .nav { list-style: none; display: flex; flex-direction: column; gap: 2px; }
    .nav li {
      display: flex; align-items: center; gap: 11px;
      padding: 10px 13px; border-radius: 9px;
      font-size: 13.5px; font-weight: 500; color: #6B7280;
      cursor: pointer; transition: background-color 0.15s, color 0.15s;
    }
    .nav li:hover { background: rgba(79,91,213,.07); color: #1A1D29; }
    .nav li.active{ background: #E8EAFB; color: #4F5BD5; font-weight: 700; }
    .nav-ic { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .sidebar-details { width: 100%; }
    .sidebar-summary {
      display: flex; align-items: center; gap: 11px;
      padding: 10px 13px; border-radius: 9px;
      font-size: 13.5px; font-weight: 500; color: #6B7280;
      cursor: pointer; list-style: none; outline: none; user-select: none;
      transition: background-color 0.15s, color 0.15s;
    }
    .sidebar-summary::-webkit-details-marker { display: none; }
    .sidebar-summary:hover { background: rgba(79,91,213,.07); color: #1A1D29; }
    .sidebar-submenu { display: flex; flex-direction: column; gap: 2px; padding-left: 28px; margin-top: 4px; }
    .sidebar-sublink {
      display: block; padding: 8px 12px; border-radius: 6px;
      font-size: 13px; font-weight: 500; color: #6B7280;
      text-decoration: none; transition: background-color 0.15s, color 0.15s;
    }
    .sidebar-sublink:hover { background: rgba(79,91,213,.04); color: #1A1D29; }
    .sidebar-sublink.active { background: #E8EAFB; color: #4F5BD5; font-weight: 700; }

    /* ── Main ── */
    .main { flex: 1; min-width: 0; padding: 28px 32px 60px; }

    /* ── Breadcrumb ── */
    .breadcrumb {
      display: flex; align-items: center; gap: 8px;
      font-size: 13px; color: #9CA3AF; margin-bottom: 20px;
    }
    .breadcrumb a { color: #6B7280; text-decoration: none; font-weight: 500; }
    .breadcrumb a:hover { color: #4F5BD5; }
    .breadcrumb span { color: #1A1D29; font-weight: 600; }

    /* ── Form Card ── */
    .form-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 16px; overflow: hidden;
      max-width: 680px;
    }
    .form-card-header {
      padding: 24px 28px 20px;
      border-bottom: 1px solid #F3F4F6;
    }
    .form-card-title { font-size: 18px; font-weight: 800; color: #1A1D29; margin-bottom: 4px; }
    .form-card-desc  { font-size: 13.5px; color: #6B7280; }
    .form-body { padding: 28px; }

    /* ── Form Groups ── */
    .form-group { margin-bottom: 22px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .field-label-static {
      display: block; font-size: 12px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em;
      color: #6B7280; margin-bottom: 8px;
    }
    .field-label-static .req { color: #EF4444; margin-left: 2px; }
    .form-input {
      width: 100%; padding: 12px 16px;
      border: 1.5px solid #E5E7EB; border-radius: 10px;
      font-family: inherit; font-size: 14px; color: #1A1D29;
      background: #fff; outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-input:focus {
      border-color: #4F5BD5;
      box-shadow: 0 0 0 3px rgba(79,91,213,.12);
    }
    .form-input::placeholder { color: #C4C7D0; }
    .form-hint { font-size: 12px; color: #9CA3AF; margin-top: 5px; }

    /* ── Divider ── */
    .form-divider {
      display: flex; align-items: center; gap: 12px;
      margin: 24px 0;
    }
    .form-divider::before,
    .form-divider::after {
      content: ''; flex: 1; height: 1px; background: #F3F4F6;
    }
    .form-divider-label { font-size: 11px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }

    /* ── Actions ── */
    .form-actions {
      display: flex; justify-content: flex-end; gap: 10px;
      padding: 20px 28px; border-top: 1px solid #F3F4F6;
      background: #FAFAFA;
    }
    .btn {
      display: inline-flex; align-items: center; gap: 7px;
      font-family: inherit; font-size: 14px; font-weight: 600;
      padding: 11px 22px; border-radius: 10px;
      border: 1.5px solid #E5E7EB; background: #fff;
      color: #374151; cursor: pointer; text-decoration: none;
      transition: all .15s;
    }
    .btn:hover { background: #F9FAFB; }
    .btn-primary { background: #4F5BD5; color: #fff; border-color: #4F5BD5; }
    .btn-primary:hover { background: #4350C4; }
  </style>
</head>
<body>
<div class="shell">

  <!-- ═══ TOPBAR ═══ -->
  <?php require ROOT_PATH . '/app/views/layouts/TopBar.php'; ?>

  <div class="body-row">

    <!-- ═══ SIDEBAR ═══ -->
    <?php require $sidebarPath; ?>

    <!-- ═══ MAIN ═══ -->
    <main class="main">

      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <a href="/customers">Manajemen Pelanggan</a>
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M4 2l4 4-4 4" stroke="#C4C7D0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Daftarkan Pelanggan Baru</span>
      </div>

      <!-- Form Card -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-card-title">Registrasi Pelanggan Baru</div>
          <div class="form-card-desc">Isi data pelanggan di bawah ini. Kolom bertanda <span style="color:#EF4444;">*</span> wajib diisi.</div>
        </div>

        <form method="POST" action="/customers" id="form-registrasi-pelanggan">
          <div class="form-body">

            <!-- Nama & Telepon -->
            <div class="form-row">
              <div class="form-group">
                <label class="field-label-static" for="cust-name">Nama Lengkap <span class="req">*</span></label>
                <input class="form-input" id="cust-name" type="text" name="name" required placeholder="Contoh: Indra Suryanto">
              </div>
              <div class="form-group">
                <label class="field-label-static" for="cust-phone">Nomor Telepon <span class="req">*</span></label>
                <input class="form-input" id="cust-phone" type="tel" name="phone" required placeholder="08xxxxxxxxxx">
              </div>
            </div>
          </div>

          <div class="form-actions">
            <a href="/customers" class="btn">Batal</a>
            <button type="submit" class="btn btn-primary" id="btn-simpan-pelanggan">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M2 8l4.5 4.5L14 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Simpan Pelanggan
            </button>
          </div>
        </form>
      </div>

    </main>
  </div>
</div>
</body>
</html>
