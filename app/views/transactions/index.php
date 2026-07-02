<?php
/**
 * @var array $transactions
 */
$transactions = $transactions ?? [];
$sidebarPath = ROOT_PATH . '/app/views/layouts/SideBar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penjualan Mobil — DealerLink DMS</title>
  <meta name="description" content="Daftar seluruh transaksi penjualan mobil di DealerLink DMS.">
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
      background: #F1F3FB; border-right: 1px solid #E5E7EB; padding: 16px 12px;
    }
    .profile { display: flex; align-items: center; gap: 10px; padding: 6px 8px 16px; border-bottom: 1px solid #E5E7EB; margin-bottom: 10px; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; background: #E8EAFB; color: #4F5BD5; font-size: 13px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .profile-name { font-size: 13.5px; font-weight: 700; }
    .profile-role { font-size: 11.5px; color: #6B7280; margin-top: 1px; }
    .nav { list-style: none; display: flex; flex-direction: column; gap: 2px; }
    .nav li { display: flex; align-items: center; gap: 11px; padding: 10px 13px; border-radius: 9px; font-size: 13.5px; font-weight: 500; color: #6B7280; cursor: pointer; transition: background-color 0.15s, color 0.15s; }
    .nav li:hover { background: rgba(79,91,213,.07); color: #1A1D29; }
    .nav li.active { background: #E8EAFB; color: #4F5BD5; font-weight: 700; }
    .nav-ic { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .sidebar-details { width: 100%; }
    .sidebar-summary { display: flex; align-items: center; gap: 11px; padding: 10px 13px; border-radius: 9px; font-size: 13.5px; font-weight: 500; color: #6B7280; cursor: pointer; list-style: none; outline: none; user-select: none; transition: background-color 0.15s, color 0.15s; }
    .sidebar-summary::-webkit-details-marker { display: none; }
    .sidebar-summary:hover { background: rgba(79,91,213,.07); color: #1A1D29; }
    .sidebar-submenu { display: flex; flex-direction: column; gap: 2px; padding-left: 28px; margin-top: 4px; }
    .sidebar-sublink { display: block; padding: 8px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; color: #6B7280; text-decoration: none; transition: background-color 0.15s, color 0.15s; }
    .sidebar-sublink:hover { background: rgba(79,91,213,.04); color: #1A1D29; }
    .sidebar-sublink.active { background: #E8EAFB; color: #4F5BD5; font-weight: 700; }

    /* ── Main ── */
    .main { flex: 1; min-width: 0; padding: 28px 32px 60px; }

    /* ── Page Header ── */
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
    .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
    .page-sub   { font-size: 13.5px; color: #6B7280; }
    .btn-row    { display: flex; gap: 10px; }
    .btn {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: 13.5px; font-weight: 600;
      padding: 10px 17px; border-radius: 9px;
      border: 1px solid #E5E7EB; background: #fff;
      cursor: pointer; white-space: nowrap; text-decoration: none; color: #1A1D29;
      transition: background .15s;
    }
    .btn:hover { background: #F9FAFB; }
    .btn-primary { background: #4F5BD5; color: #fff; border-color: #4F5BD5; }
    .btn-primary:hover { background: #4350C4; color: #fff; }

    /* ── KPI ── */
    .kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 22px; }
    .kpi-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 20px 22px; }
    .kpi-top  { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .kpi-icon { width: 42px; height: 42px; border-radius: 11px; background: #E8EAFB; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .kpi-icon.green { background: #DCFCE7; }
    .kpi-icon.amber { background: #FEF3C7; }
    .kpi-icon.rose  { background: #FEE2E2; }
    .kpi-label { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #9CA3AF; margin-bottom: 4px; }
    .kpi-value { font-size: 26px; font-weight: 800; letter-spacing: -.02em; color: #1A1D29; }
    .kpi-sub   { font-size: 12px; color: #9CA3AF; margin-top: 2px; }

    /* ── Filter & Search ── */
    .filter-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; padding: 16px 22px; margin-bottom: 20px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .search-wrap { position: relative; flex: 1; min-width: 220px; }
    .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; }
    .search-input { width: 100%; padding: 9px 13px 9px 36px; border: 1px solid #D1D5DB; border-radius: 9px; font-family: inherit; font-size: 13.5px; background: #F9FAFB; color: #1A1D29; outline: none; transition: border-color .15s, box-shadow .15s; }
    .search-input:focus { border-color: #4F5BD5; background: #fff; box-shadow: 0 0 0 3px rgba(79,91,213,.10); }
    .filter-sep { width: 1px; height: 32px; background: #E5E7EB; flex-shrink: 0; }
    .filter-label { font-size: 12px; font-weight: 600; color: #6B7280; white-space: nowrap; }
    .field-select { appearance: none; font-family: inherit; font-size: 13.5px; font-weight: 500; color: #1A1D29; background: #F9FAFB; border: 1px solid #D1D5DB; border-radius: 9px; padding: 9px 32px 9px 13px; min-width: 140px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; outline: none; transition: border-color .15s, box-shadow .15s; }
    .field-select:focus { border-color: #4F5BD5; background-color: #fff; box-shadow: 0 0 0 3px rgba(79,91,213,.10); }

    /* ── Table Card ── */
    .table-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; }
    .table-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #E5E7EB; gap: 14px; flex-wrap: wrap; }
    .table-title { font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .count-pill { font-size: 12px; font-weight: 700; background: #E8EAFB; color: #4F5BD5; padding: 2px 9px; border-radius: 999px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th { text-align: left; padding: 13px 22px; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #9CA3AF; border-bottom: 1px solid #E5E7EB; white-space: nowrap; }
    thead th.th-r { text-align: right; }
    tbody tr { border-bottom: 1px solid #F3F4F6; transition: background .15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FAFBFF; }
    tbody td { padding: 16px 22px; vertical-align: middle; }
    .td-bold  { font-weight: 700; color: #1A1D29; }
    .td-muted { color: #6B7280; font-size: 13px; }
    .td-r     { text-align: right; font-weight: 700; white-space: nowrap; }
    .badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; letter-spacing: .02em; padding: 5px 11px; border-radius: 999px; white-space: nowrap; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .b-green { background: #DCFCE7; color: #16A34A; }
    .b-blue  { background: #DBEAFE; color: #2563EB; }
    .b-red   { background: #FEE2E2; color: #DC2626; }
    .b-gray  { background: #F3F4F6; color: #6B7280; }

    .action-btn { display: inline-flex; align-items: center; gap: 5px; font-size: 12.5px; font-weight: 600; padding: 6px 12px; border-radius: 7px; border: 1px solid #E5E7EB; background: #fff; color: #374151; cursor: pointer; text-decoration: none; transition: all .15s; }
    .action-btn:hover { background: #F3F4F6; }
    .action-btn.primary { background: #E8EAFB; color: #4F5BD5; border-color: #E8EAFB; }
    .action-btn.primary:hover { background: #D3D8F8; }

    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-icon  { font-size: 48px; margin-bottom: 16px; }
    .empty-title { font-size: 18px; font-weight: 700; color: #1A1D29; margin-bottom: 8px; }
    .empty-desc  { font-size: 14px; color: #6B7280; margin-bottom: 24px; }

    .table-foot { display: flex; justify-content: space-between; align-items: center; padding: 14px 22px; border-top: 1px solid #F3F4F6; font-size: 13px; color: #6B7280; }
  </style>
</head>
<body>
<div class="shell">

  <!-- ═══ TOPBAR ═══ -->
  <?php require ROOT_PATH . '/app/views/layouts/TopBar.php'; ?>

  <div class="body-row">
    <?php require $sidebarPath; ?>

    <main class="main">

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Penjualan Mobil</h1>
          <p class="page-sub">Kelola dan pantau seluruh transaksi penjualan kendaraan.</p>
        </div>
        <div class="btn-row">
          <a href="/transactions/create" class="btn btn-primary" id="btn-buat-transaksi">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Buat Transaksi Baru
          </a>
        </div>
      </div>

      <!-- KPI Row -->
      <?php
        $total = count($transactions);
        $lunas   = count(array_filter($transactions, fn($t) => ($t['status'] ?? '') === 'lunas'));
        $process = count(array_filter($transactions, fn($t) => ($t['status'] ?? '') === 'process'));
        $cancel  = count(array_filter($transactions, fn($t) => ($t['status'] ?? '') === 'cancel'));
        $totalVal = array_sum(array_column($transactions, 'price'));
      ?>
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-top">
            <div>
              <div class="kpi-label">Total Transaksi</div>
              <div class="kpi-value"><?= $total ?></div>
            </div>
            <div class="kpi-icon">🚗</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-top">
            <div>
              <div class="kpi-label">Lunas</div>
              <div class="kpi-value"><?= $lunas ?></div>
            </div>
            <div class="kpi-icon green">✅</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-top">
            <div>
              <div class="kpi-label">Dalam Proses</div>
              <div class="kpi-value"><?= $process ?></div>
            </div>
            <div class="kpi-icon amber">⏳</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-top">
            <div>
              <div class="kpi-label">Dibatalkan</div>
              <div class="kpi-value"><?= $cancel ?></div>
            </div>
            <div class="kpi-icon rose">❌</div>
          </div>
        </div>
      </div>

      <!-- Filter -->
      <div class="filter-card">
        <div class="search-wrap">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
            <circle cx="6.5" cy="6.5" r="4.5" stroke="#9CA3AF" stroke-width="1.5"/>
            <path d="M10 10l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <input class="search-input" id="search-trx" type="text" placeholder="Cari kode, nama pelanggan atau kendaraan...">
        </div>
        <div class="filter-sep"></div>
        <span class="filter-label">Status:</span>
        <select class="field-select" id="filter-status">
          <option value="">Semua Status</option>
          <option value="process">Proses</option>
          <option value="lunas">Lunas</option>
          <option value="cancel">Batal</option>
        </select>
        <span class="filter-label">Pembayaran:</span>
        <select class="field-select" id="filter-payment">
          <option value="">Semua Tipe</option>
          <option value="Tunai">Tunai</option>
          <option value="Kredit">Kredit</option>
        </select>
      </div>

      <!-- Table Card -->
      <div class="table-card">
        <div class="table-head">
          <div class="table-title">
            Daftar Transaksi
            <span class="count-pill" id="trx-count"><?= $total ?></span>
          </div>
        </div>

        <?php if (empty($transactions)): ?>
          <div class="empty-state">
            <div class="empty-icon">🚗</div>
            <div class="empty-title">Belum ada transaksi</div>
            <div class="empty-desc">Mulai buat transaksi penjualan kendaraan pertama Anda.</div>
            <a href="/transactions/create" class="btn btn-primary">+ Buat Transaksi Baru</a>
          </div>
        <?php else: ?>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Kode Transaksi</th>
                  <th>Pelanggan</th>
                  <th>Kendaraan</th>
                  <th>Sales</th>
                  <th>Pembayaran</th>
                  <th class="th-r">Harga</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody id="trx-tbody">
                <?php foreach ($transactions as $trx): ?>
                  <?php
                    $statusClass = 'b-gray';
                    $statusText  = strtoupper($trx['status'] ?? '—');
                    if (($trx['status'] ?? '') === 'lunas')   $statusClass = 'b-green';
                    if (($trx['status'] ?? '') === 'process') $statusClass = 'b-blue';
                    if (($trx['status'] ?? '') === 'cancel')  $statusClass = 'b-red';
                    $payLabel = isset($trx['payment_type']) ? (($trx['payment_type'] == 1) ? 'Kredit' : 'Tunai') : ($trx['payment_type_label'] ?? '—');
                  ?>
                  <tr
                    data-search="<?= strtolower(htmlspecialchars(($trx['transaction_code'] ?? '') . ' ' . ($trx['customer_name'] ?? '') . ' ' . ($trx['brand'] ?? '') . ' ' . ($trx['type'] ?? ''))) ?>"
                    data-status="<?= htmlspecialchars($trx['status'] ?? '') ?>"
                    data-payment="<?= htmlspecialchars($payLabel) ?>"
                  >
                    <td class="td-bold"><?= htmlspecialchars($trx['transaction_code'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($trx['customer_name'] ?? '—') ?></td>
                    <td class="td-muted"><?= htmlspecialchars(($trx['brand'] ?? '') . ' ' . ($trx['type'] ?? '')) ?> <span style="color:#C4C7D0;">(<?= htmlspecialchars($trx['color'] ?? '') ?>)</span></td>
                    <td class="td-muted"><?= htmlspecialchars($trx['sales_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($payLabel) ?></td>
                    <td class="td-r">Rp <?= number_format($trx['price'] ?? 0, 0, ',', '.') ?></td>
                    <td><span class="badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                    <td class="td-muted"><?= isset($trx['created_at']) ? date('d M Y', strtotime($trx['created_at'])) : '—' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="table-foot">
            <span id="trx-info">Menampilkan <?= $total ?> transaksi</span>
          </div>
        <?php endif; ?>
      </div>

    </main>
  </div>
</div>

<script>
  function filterTable() {
    const q       = (document.getElementById('search-trx')?.value || '').toLowerCase();
    const status  = document.getElementById('filter-status')?.value || '';
    const payment = document.getElementById('filter-payment')?.value || '';
    const rows    = document.querySelectorAll('#trx-tbody tr');
    let visible   = 0;
    rows.forEach(row => {
      const matchQ  = !q      || (row.dataset.search || '').includes(q);
      const matchS  = !status || row.dataset.status === status;
      const matchP  = !payment || row.dataset.payment === payment;
      const show    = matchQ && matchS && matchP;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    const info = document.getElementById('trx-info');
    const pill = document.getElementById('trx-count');
    if (info) info.textContent = 'Menampilkan ' + visible + ' transaksi';
    if (pill) pill.textContent = visible;
  }
  ['search-trx','filter-status','filter-payment'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', filterTable);
    document.getElementById(id)?.addEventListener('change', filterTable);
  });
</script>
</body>
</html>
