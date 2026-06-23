<?php
/** 
 * @var array $transactions 
 * @var int $currentPage 
 * @var int $totalPages 
 * @var array $filters 
 */

// Memaksa inisialisasi variabel lokal 
$transactions = $transactions ?? [];
$currentPage  = $currentPage ?? 1;
$totalPages   = $totalPages ?? 1;
$filters      = $filters ?? [
    'status'       => '',
    'start_date'   => '',
    'end_date'     => '',
    'payment_type' => ''
];

$baseParams = $filters;
$sidebarPath = ROOT_PATH . '/app/views/layouts/SideBar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Transaksi — DealerLink DMS</title>
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
    .shell   { display: flex; min-height: 100vh; flex-direction: column; }
    .body-row{ display: flex; flex: 1; }

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
    .nav          { list-style: none; display: flex; flex-direction: column; gap: 2px; }
    .nav li {
      display: flex; align-items: center; gap: 11px;
      padding: 10px 13px; border-radius: 9px;
      font-size: 13.5px; font-weight: 500; color: #6B7280;
      cursor: pointer;
    }
    .nav li:hover { background: rgba(79,91,213,.07); color: #1A1D29; }
    .nav li.active{ background: #E8EAFB; color: #4F5BD5; font-weight: 700; }
    .nav-ic { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }

    /* ── Main ── */
    .main { flex: 1; min-width: 0; padding: 28px 32px 60px; }

    /* ── Page header ── */
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
    }
    .btn:hover    { background: #F9FAFB; }
    .btn-dark     { background: #111827; color: #fff; border-color: #111827; }
    .btn-dark:hover { background: #1F2937; }

    /* ── Filter card ── */
    .filter-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 14px; padding: 20px 22px;
      margin-bottom: 20px;
    }
    .filter-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr auto;
      gap: 14px; align-items: end;
    }
    .field       { display: flex; flex-direction: column; gap: 7px; }
    .field-label {
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em;
      color: #9CA3AF;
      display: flex; align-items: center; gap: 5px;
    }
    .field-select,
    .field-input {
      appearance: none; width: 100%;
      font-family: inherit; font-size: 13.5px; font-weight: 500;
      color: #1A1D29; background: #fff;
      border: 1px solid #D1D5DB; border-radius: 9px;
      padding: 10px 13px; height: 42px;
      transition: border-color .15s, box-shadow .15s;
    }
    .field-select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      padding-right: 32px; cursor: pointer;
    }
    .field-select:focus,
    .field-input:focus {
      outline: none; border-color: #4F5BD5;
      box-shadow: 0 0 0 3px rgba(79,91,213,.12);
    }
    .btn-filter {
      height: 42px; padding: 0 22px;
      background: #4F5BD5; color: #fff;
      border: none; border-radius: 9px;
      font-family: inherit; font-size: 13.5px; font-weight: 600;
      display: inline-flex; align-items: center; gap: 7px;
      cursor: pointer; white-space: nowrap;
    }
    .btn-filter:hover { background: #4350C4; }
    .btn-filter svg   { flex-shrink: 0; }

    /* ── KPI grid ── */
    .kpi-grid {
      display: grid; grid-template-columns: repeat(3,1fr);
      gap: 16px; margin-bottom: 22px;
    }
    .kpi-card {
      background: #fff; border: 1px solid #E5E7EB;
      border-radius: 14px; padding: 20px 22px;
    }
    .kpi-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .kpi-icon {
      width: 42px; height: 42px; border-radius: 11px;
      background: #E8EAFB;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px;
    }
    .pill {
      display: inline-block;
      font-size: 12px; font-weight: 700;
      padding: 5px 10px; border-radius: 999px;
    }
    .pill-green { background: #DCFCE7; color: #16A34A; }
    .pill-red   { background: #FEE2E2; color: #DC2626; }
    .pill-blue  { background: #DBEAFE; color: #2563EB; }
    .kpi-label  { font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: #9CA3AF; margin-bottom: 5px; }
    .kpi-value  { font-size: 24px; font-weight: 800; letter-spacing: -.02em; }

    /* ── Table card ── */
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
    .search-wrap { position: relative; }
    .search-wrap svg {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%); pointer-events: none;
    }
    .search-input {
      width: 290px; padding: 9px 13px 9px 36px;
      border: 1px solid #D1D5DB; border-radius: 9px;
      font-family: inherit; font-size: 13px;
      background: #F9FAFB; color: #1A1D29;
    }
    .search-input:focus {
      outline: none; border-color: #4F5BD5;
      background: #fff; box-shadow: 0 0 0 3px rgba(79,91,213,.10);
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
    thead th.th-r { text-align: right; }
    tbody tr      { border-bottom: 1px solid #F3F4F6; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #FAFBFF; }
    tbody td { padding: 17px 22px; vertical-align: middle; }
    .td-bold  { font-weight: 700; }
    .td-muted { color: #6B7280; }
    .td-r     { text-align: right; font-weight: 700; white-space: nowrap; }

    /* Tipe tag */
    .tipe { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 600; color: #6B7280; }
    .dot  { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
    .dot-spk { background: #4F5BD5; }
    .dot-wo  { background: #F59E0B; }

    /* Badge */
    .badge {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 11.5px; font-weight: 700; letter-spacing: .02em;
      padding: 5px 11px; border-radius: 999px; white-space: nowrap;
    }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
    .b-green { background: #DCFCE7; color: #16A34A; }
    .b-blue  { background: #DBEAFE; color: #2563EB; }
    .b-red   { background: #FEE2E2; color: #DC2626; }
    .b-gray  { background: #F3F4F6; color: #6B7280; }

    /* Table footer */
    .table-foot {
      display: flex; justify-content: space-between; align-items: center;
      padding: 14px 22px; border-top: 1px solid #F3F4F6;
      font-size: 13px; color: #6B7280;
    }
    .pagi     { display: flex; gap: 5px; }
    .pg-btn {
      width: 32px; height: 32px;
      display: flex; align-items: center; justify-content: center;
      border-radius: 8px; border: 1px solid #E5E7EB;
      background: #fff; font-size: 13px; font-weight: 600;
      color: #374151; cursor: pointer; text-decoration: none;
    }
    .pg-btn:hover { background: #F3F4F6; }
    .pg-btn.active{ background: #4F5BD5; border-color: #4F5BD5; color: #fff; }
    .pg-btn.off   { opacity: .35; pointer-events: none; }
  </style>
</head>
<body>
<div class="shell">

  <!-- ═══ TOPBAR ═══ -->
  <header class="topbar">
    <div class="topbar-left">
      <div class="hamburger">
        <span></span><span></span><span></span>
      </div>
      <div class="brand">DealerLink <span>DMS</span></div>
    </div>
    <div class="topbar-right">
      <a>Notifikasi</a>
      <a>Bantuan</a>
      <div class="bell">🔔</div>
    </div>
  </header>

  <div class="body-row">

    <!-- ═══ SIDEBAR ═══ -->
    <?php require $sidebarPath; ?>

    <!-- ═══ MAIN ═══ -->
    <main class="main">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <h1 class="page-title">Riwayat Transaksi Gabungan</h1>
          <p class="page-sub">Historical record seluruh transaksi penjualan (SPK) dan servis (Work Order) secara terpadu.</p>
        </div>
        <div class="btn-row">
          <button class="btn">Cetak PDF</button>
          <button class="btn btn-dark">Ekspor Excel</button>
        </div>
      </div>

      <!-- ═══ FILTER CARD ═══ -->
      <form method="GET" action="" class="filter-card">
        <div class="filter-grid">

          <div class="field">
            <label class="field-label">Status Transaksi</label>
            <select name="status" class="field-select">
              <option value="">Semua Status</option>
              <option value="process" <?= isset($filters['status']) && $filters['status'] === 'process' ? 'selected' : '' ?>>Proses</option>
              <option value="lunas" <?= isset($filters['status']) && $filters['status'] === 'lunas' ? 'selected' : '' ?>>Lunas</option>
              <option value="cancel" <?= isset($filters['status']) && $filters['status'] === 'cancel' ? 'selected' : '' ?>>Batal</option>
            </select>
          </div>

          <div class="field">
            <label class="field-label">Dari Tanggal</label>
            <input class="field-input" type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date'] ?? '') ?>">
          </div>

          <div class="field">
            <label class="field-label">Sampai Tanggal</label>
            <input class="field-input" type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date'] ?? '') ?>">
          </div>

          <div class="field">
            <label class="field-label">Tipe Pembayaran</label>
            <select name="payment_type" class="field-select">
              <option value="">Semua Tipe</option>
              <option value="2" <?= isset($filters['payment_type']) && $filters['payment_type'] == '2' ? 'selected' : '' ?>>Tunai</option>
              <option value="1" <?= isset($filters['payment_type']) && $filters['payment_type'] == '1' ? 'selected' : '' ?>>Kredit</option>
            </select>
          </div>

          <div class="field">
            <button type="submit" class="btn-filter">
              <svg width="13" height="13" viewBox="0 0 16 16" fill="none">
                <path d="M2 4h12M4 8h8M6 12h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
              Filter
            </button>
          </div>

        </div>
      </form>

      <!-- ═══ TABLE CARD ═══ -->
      <div class="table-card">

        <div class="table-head">
          <div class="table-title">
            Data Tabular Riwayat Transaksi
          </div>
          <div class="search-wrap">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <circle cx="6.5" cy="6.5" r="4.5" stroke="#9CA3AF" stroke-width="1.5"/>
              <path d="M10 10l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <input class="search-input" type="text" placeholder="Cari No. SPK/WO atau Nama Pelanggan...">
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>No. SPK / WO</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Model Unit</th>
                <th>Tipe</th>
                <th>Pembayaran</th>
                <th class="th-r">Nilai Transaksi</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $item): ?>
                  <tr>
                    <td class="td-bold"><?= htmlspecialchars($item['transaction_code']) ?></td>
                    <td class="td-muted"><?= date('d M Y', strtotime($item['created_at'])) ?></td>
                    <td><?= htmlspecialchars($item['customer_name']) ?></td>
                    <td class="td-muted"><?= htmlspecialchars($item['vehicle_type']) ?></td>
                    <td>
                      <span class="tipe">
                        <span class="dot dot-spk"></span>Penjualan (SPK)
                      </span>
                    </td>
                    <td><?= htmlspecialchars($item['payment_type_label']) ?></td>
                    <td class="td-r">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                    <td>
                      <?php 
                        $statusClass = 'b-gray';
                        $statusText = strtoupper($item['status']);
                        if ($item['status'] === 'lunas') $statusClass = 'b-green';
                        if ($item['status'] === 'process') $statusClass = 'b-blue';
                        if ($item['status'] === 'cancel') $statusClass = 'b-red';
                      ?>
                      <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" style="text-align: center; padding: 30px; color: #9CA3AF;">
                    Tidak ada riwayat transaksi yang cocok dengan filter.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

       <div class="table-foot">
          <span>Menampilkan halaman <?= $currentPage ?> dari <?= $totalPages ?> halaman</span>
          <div class="pagi">
            
            <!-- Tombol Previous (‹) -->
            <?php 
              $prevParams = array_merge($baseParams, ['page' => max(1, $currentPage - 1)]); 
            ?>
            <a href="?<?= http_build_query($prevParams) ?>" class="pg-btn <?= $currentPage <= 1 ? 'off' : '' ?>">‹</a>
            
            <!-- Loop Angka Halaman Dinamis -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <?php 
                $pageParams = array_merge($baseParams, ['page' => $i]); 
              ?>
              <a href="?<?= http_build_query($pageParams) ?>" class="pg-btn <?= $currentPage == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <!-- Tombol Next (›) -->
            <?php 
              $nextParams = array_merge($baseParams, ['page' => min($totalPages, $currentPage + 1)]); 
            ?>
            <a href="?<?= http_build_query($nextParams) ?>" class="pg-btn <?= $currentPage >= $totalPages ? 'off' : '' ?>">›</a>
               
          </div>
        </div>

      </div>

    </main>
  </div>
</div>
</body>
</html>
