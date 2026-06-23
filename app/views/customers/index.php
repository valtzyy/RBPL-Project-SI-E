<?php
/**
 * @var array $customers
 */
$customers = $customers ?? [];
$sidebarPath = ROOT_PATH . '/app/views/layouts/SideBar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Pelanggan — DealerLink DMS</title>
  <meta name="description" content="Kelola data pelanggan Anda, lihat riwayat pembelian, dan daftarkan pelanggan baru di DealerLink DMS.">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background: #F3F4F8;
      color: #1A1D29;
      font-size: 14px;
    }

    /* ── Layout ── */
    .shell    { display: flex; min-height: 100vh; flex-direction: column; }
    .body-row { display: flex; flex: 1; }

    /* ── Topbar ── */
    .topbar {
      height: 56px;
      background: #fff;
      border-bottom: 1px solid #E5E7EB;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 28px;
      position: sticky; top: 0; z-index: 30;
    }
    .topbar-left { display: flex; align-items: center; gap: 16px; }
    .hamburger   { display: flex; flex-direction: column; gap: 4px; cursor: pointer; }
    .hamburger span { display: block; width: 20px; height: 2px; background: #374151; border-radius: 2px; }
    .brand      { font-size: 19px; font-weight: 800; letter-spacing: -.02em; }
    .brand span { color: #4F5BD5; }
    .topbar-right { display: flex; align-items: center; gap: 20px; font-size: 13.5px; font-weight: 500; color: #6B7280; }
    .topbar-right a { color: inherit; text-decoration: none; cursor: pointer; }
    .bell {
      width: 34px; height: 34px; border-radius: 50%;
      border: 1px solid #E5E7EB;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; cursor: pointer;
    }
    .user-greeting { font-size: 13.5px; color: #374151; }
    .logout-btn {
      background: none; border: none;
      color: #DC2626; font-family: inherit; font-size: 13.5px; font-weight: 600;
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
      padding: 6px 8px 16px;
      border-bottom: 1px solid #E5E7EB;
      margin-bottom: 10px;
    }
    .avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: #E8EAFB; color: #4F5BD5;
      font-size: 13px; font-weight: 800;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
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
    .sidebar-submenu {
      display: flex; flex-direction: column; gap: 2px;
      padding-left: 28px; margin-top: 4px;
    }
    .sidebar-sublink {
      display: block; padding: 8px 12px; border-radius: 6px;
      font-size: 13px; font-weight: 500; color: #6B7280;
      text-decoration: none; transition: background-color 0.15s, color 0.15s;
    }
    .sidebar-sublink:hover { background: rgba(79,91,213,.04); color: #1A1D29; }
    .sidebar-sublink.active { background: #E8EAFB; color: #4F5BD5; font-weight: 700; }

    /* ── Main ── */
    .main { flex: 1; min-width: 0; padding: 28px 32px 60px; }

    /* ── Page Header ── */
    .page-header {
      display: flex; justify-content: space-between; align-items: flex-start;
      gap: 16px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
    .page-sub   { font-size: 13.5px; color: #6B7280; }
    .btn-row    { display: flex; gap: 10px; }
    .btn {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: 13.5px; font-weight: 600;
      padding: 10px 17px; border-radius: 9px;
      border: 1px solid #E5E7EB; background: #fff;
      cursor: pointer; white-space: nowrap;
      text-decoration: none; color: #1A1D29;
      transition: background 0.15s;
    }
    .btn:hover    { background: #F9FAFB; }
    .btn-primary  { background: #4F5BD5; color: #fff; border-color: #4F5BD5; }
    .btn-primary:hover { background: #4350C4; color: #fff; }

    /* ── Stats Row ── */
    .stats-row {
      display: grid; grid-template-columns: repeat(3, 1fr);
      gap: 16px; margin-bottom: 22px;
    }
    .stat-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 14px; padding: 20px 22px;
    }
    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .stat-icon {
      width: 42px; height: 42px; border-radius: 11px;
      background: #E8EAFB;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px;
    }
    .stat-icon.green { background: #DCFCE7; }
    .stat-icon.amber { background: #FEF3C7; }
    .stat-label { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #9CA3AF; margin-bottom: 4px; }
    .stat-value { font-size: 28px; font-weight: 800; letter-spacing: -.02em; color: #1A1D29; }

    /* ── Search & Filter ── */
    .filter-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 14px; padding: 16px 22px;
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    }
    .search-wrap { position: relative; flex: 1; min-width: 220px; }
    .search-wrap svg {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%); pointer-events: none;
    }
    .search-input {
      width: 100%; padding: 9px 13px 9px 36px;
      border: 1px solid #D1D5DB; border-radius: 9px;
      font-family: inherit; font-size: 13.5px;
      background: #F9FAFB; color: #1A1D29;
      outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .search-input:focus {
      border-color: #4F5BD5; background: #fff;
      box-shadow: 0 0 0 3px rgba(79,91,213,.10);
    }
    .filter-sep { width: 1px; height: 32px; background: #E5E7EB; flex-shrink: 0; }
    .filter-label { font-size: 12px; font-weight: 600; color: #6B7280; white-space: nowrap; }
    .field-select {
      appearance: none;
      font-family: inherit; font-size: 13.5px; font-weight: 500;
      color: #1A1D29; background: #F9FAFB;
      border: 1px solid #D1D5DB; border-radius: 9px;
      padding: 9px 32px 9px 13px; min-width: 150px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      cursor: pointer; outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .field-select:focus {
      border-color: #4F5BD5; background-color: #fff;
      box-shadow: 0 0 0 3px rgba(79,91,213,.10);
    }

    /* ── Table Card ── */
    .table-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 14px; overflow: hidden;
    }
    .table-head {
      display: flex; justify-content: space-between; align-items: center;
      padding: 18px 22px; border-bottom: 1px solid #E5E7EB;
      gap: 14px; flex-wrap: wrap;
    }
    .table-title { font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .count-pill {
      font-size: 12px; font-weight: 700;
      background: #E8EAFB; color: #4F5BD5;
      padding: 2px 9px; border-radius: 999px;
    }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      text-align: left; padding: 13px 22px;
      font-size: 11px; font-weight: 700;
      letter-spacing: .05em; text-transform: uppercase;
      color: #9CA3AF; border-bottom: 1px solid #E5E7EB;
      white-space: nowrap;
    }
    tbody tr { border-bottom: 1px solid #F3F4F6; transition: background .15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FAFBFF; }
    tbody td { padding: 16px 22px; vertical-align: middle; }
    .td-bold  { font-weight: 700; color: #1A1D29; }
    .td-muted { color: #6B7280; font-size: 13px; }
    .td-sub   { font-size: 12px; color: #9CA3AF; margin-top: 2px; }

    /* Avatar Inisial */
    .cust-avatar {
      width: 36px; height: 36px; border-radius: 50%;
      background: #E8EAFB; color: #4F5BD5;
      font-size: 13px; font-weight: 800;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .cust-cell { display: flex; align-items: center; gap: 12px; }
    .cust-name { font-size: 13.5px; font-weight: 600; color: #1A1D29; }
    .cust-id   { font-size: 11.5px; color: #9CA3AF; margin-top: 1px; }

    /* Action buttons */
    .action-btn {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 12.5px; font-weight: 600;
      padding: 6px 12px; border-radius: 7px;
      border: 1px solid #E5E7EB; background: #fff;
      color: #374151; cursor: pointer; text-decoration: none;
      transition: all .15s;
    }
    .action-btn:hover { background: #F3F4F6; }
    .action-btn.primary { background: #E8EAFB; color: #4F5BD5; border-color: #E8EAFB; }
    .action-btn.primary:hover { background: #D3D8F8; }

    /* Empty state */
    .empty-state {
      text-align: center; padding: 60px 20px;
    }
    .empty-icon { font-size: 48px; margin-bottom: 16px; }
    .empty-title { font-size: 18px; font-weight: 700; color: #1A1D29; margin-bottom: 8px; }
    .empty-desc { font-size: 14px; color: #6B7280; margin-bottom: 24px; }

    /* Table footer */
    .table-foot {
      display: flex; justify-content: space-between; align-items: center;
      padding: 14px 22px; border-top: 1px solid #F3F4F6;
      font-size: 13px; color: #6B7280;
    }
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

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Manajemen Pelanggan</h1>
          <p class="page-sub">Kelola seluruh data pelanggan dan riwayat transaksi mereka.</p>
        </div>
        <div class="btn-row">
          <a href="/customers/create" class="btn btn-primary" id="btn-daftar-pelanggan">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Daftarkan Pelanggan
          </a>
        </div>
      </div>

      <!-- Stats Row -->
      <div class="stats-row" style="grid-template-columns: 280px;">
        <div class="stat-card">
          <div class="stat-top">
            <div>
              <div class="stat-label">Total Pelanggan</div>
              <div class="stat-value"><?= count($customers) ?></div>
            </div>
            <div class="stat-icon">👥</div>
          </div>
        </div>
      </div>

      <!-- Filter & Search -->
      <div class="filter-card">
        <div class="search-wrap">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <circle cx="6.5" cy="6.5" r="4.5" stroke="#9CA3AF" stroke-width="1.5"/>
            <path d="M10 10l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <input class="search-input" id="search-customer" type="text" placeholder="Cari nama atau nomor telepon pelanggan...">
        </div>
        <div class="filter-sep"></div>
        <span class="filter-label">Urutkan:</span>
        <select class="field-select" id="sort-customer">
          <option value="name_asc">Nama A–Z</option>
          <option value="name_desc">Nama Z–A</option>
          <option value="newest">Terbaru</option>
        </select>
      </div>

      <!-- Table Card -->
      <div class="table-card">
        <div class="table-head">
          <div class="table-title">
            Daftar Pelanggan
            <span class="count-pill" id="customer-count"><?= count($customers) ?></span>
          </div>
        </div>

        <?php if (empty($customers)): ?>
          <div class="empty-state">
            <div class="empty-icon">👤</div>
            <div class="empty-title">Belum ada pelanggan</div>
            <div class="empty-desc">Mulai dengan mendaftarkan pelanggan pertama Anda.</div>
            <a href="/customers/create" class="btn btn-primary">+ Daftarkan Pelanggan Baru</a>
          </div>
        <?php else: ?>
          <div class="table-wrap">
            <table id="customers-table">
              <thead>
                <tr>
                  <th>Pelanggan</th>
                  <th>No. Telepon</th>
                </tr>
              </thead>
              <tbody id="customers-tbody">
                <?php foreach ($customers as $customer): ?>
                  <?php
                    $words = explode(' ', trim($customer['name'] ?? 'G'));
                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                  ?>
                  <tr data-name="<?= strtolower(htmlspecialchars($customer['name'] ?? '')) ?>" data-phone="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                    <td>
                      <div class="cust-cell">
                        <div class="cust-avatar"><?= $initials ?></div>
                        <div>
                          <div class="cust-name"><?= htmlspecialchars($customer['name']) ?></div>
                          <div class="cust-id">ID #<?= $customer['id'] ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="td-muted"><?= htmlspecialchars($customer['phone'] ?? '—') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="table-foot">
            <span id="table-info">Menampilkan <?= count($customers) ?> pelanggan</span>
          </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<script>
  const tbody   = document.getElementById('customers-tbody');
  const search  = document.getElementById('search-customer');
  const sortSel = document.getElementById('sort-customer');

  // Simpan urutan asli dari server sekali saja saat load
  const originalOrder = tbody
    ? Array.from(tbody.querySelectorAll('tr')).map((row, i) => { row.dataset.origIdx = i; return row; })
    : [];

  function applyFilterSort() {
    if (!tbody) return;

    const q = (search?.value || '').toLowerCase().trim();
    const rows = Array.from(tbody.querySelectorAll('tr'));

    // Filter visibility
    rows.forEach(row => {
      const name  = row.dataset.name  || '';
      const phone = row.dataset.phone || '';
      row.style.display = (!q || name.includes(q) || phone.includes(q)) ? '' : 'none';
    });

    // Sort
    const sortVal = sortSel?.value || 'name_asc';
    const sorted = rows.slice().sort((a, b) => {
      if (sortVal === 'name_asc')  return (a.dataset.name || '').localeCompare(b.dataset.name || '', 'id');
      if (sortVal === 'name_desc') return (b.dataset.name || '').localeCompare(a.dataset.name || '', 'id');
      // 'newest' — gunakan index asli yang sudah disimpan, bukan posisi DOM saat ini
      return parseInt(a.dataset.origIdx) - parseInt(b.dataset.origIdx);
    });

    sorted.forEach(row => tbody.appendChild(row));

    // Update counter
    const visible = rows.filter(r => r.style.display !== 'none').length;
    const info = document.getElementById('table-info');
    const pill = document.getElementById('customer-count');
    if (info) info.textContent = 'Menampilkan ' + visible + ' pelanggan';
    if (pill) pill.textContent = visible;
  }

  search?.addEventListener('input',  applyFilterSort);
  sortSel?.addEventListener('change', applyFilterSort);
</script>
</body>
</html>
