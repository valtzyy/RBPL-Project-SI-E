<?php
/**
 * @var array $customers
 * @var array $vehicles
 */
$customers = $customers ?? [];
$vehicles  = $vehicles  ?? [];
$sidebarPath = ROOT_PATH . '/app/views/layouts/SideBar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Transaksi — DealerLink DMS</title>
  <meta name="description" content="Form pembuatan transaksi penjualan mobil baru di DealerLink DMS.">
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
    .topbar { height: 56px; background: #fff; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 30; }
    .topbar-left { display: flex; align-items: center; gap: 16px; }
    .hamburger   { display: flex; flex-direction: column; gap: 4px; cursor: pointer; }
    .hamburger span { display: block; width: 20px; height: 2px; background: #374151; border-radius: 2px; }
    .brand      { font-size: 19px; font-weight: 800; letter-spacing: -.02em; }
    .brand span { color: #4F5BD5; }
    .topbar-right { display: flex; align-items: center; gap: 20px; font-size: 13.5px; font-weight: 500; color: #6B7280; }
    .topbar-right a { color: inherit; text-decoration: none; }
    .bell { width: 34px; height: 34px; border-radius: 50%; border: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: center; font-size: 16px; cursor: pointer; }
    .user-greeting { font-size: 13.5px; color: #374151; }
    .logout-btn { background: none; border: none; color: #DC2626; font-family: inherit; font-size: 13.5px; font-weight: 600; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: background-color 0.15s; }
    .logout-btn:hover { background: #FEE2E2; }

    /* ── Sidebar ── */
    .sidebar { width: 232px; flex-shrink: 0; background: #F1F3FB; border-right: 1px solid #E5E7EB; padding: 16px 12px; }
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

    /* ── Breadcrumb ── */
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #9CA3AF; margin-bottom: 20px; }
    .breadcrumb a { color: #6B7280; text-decoration: none; font-weight: 500; }
    .breadcrumb a:hover { color: #4F5BD5; }
    .breadcrumb span { color: #1A1D29; font-weight: 600; }

    /* ── Page title area ── */
    .page-header { margin-bottom: 24px; }
    .page-title { font-size: 24px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
    .page-sub   { font-size: 13.5px; color: #6B7280; }

    /* ── Two-column layout ── */
    .form-layout { display: grid; grid-template-columns: 1fr 380px; gap: 20px; align-items: start; }

    /* ── Section card ── */
    .section-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; overflow: hidden; margin-bottom: 20px; }
    .section-header { padding: 20px 24px 16px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 12px; }
    .section-step { width: 28px; height: 28px; border-radius: 50%; background: #4F5BD5; color: #fff; font-size: 12px; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-title { font-size: 15px; font-weight: 800; color: #1A1D29; }
    .section-desc  { font-size: 12.5px; color: #9CA3AF; margin-top: 1px; }
    .section-body  { padding: 22px 24px; }

    /* ── Form inputs ── */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 18px; }
    .form-group:last-child { margin-bottom: 0; }
    .field-label { display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6B7280; margin-bottom: 8px; }
    .field-label .req { color: #EF4444; margin-left: 2px; }
    .form-input {
      width: 100%; padding: 11px 14px;
      border: 1.5px solid #E5E7EB; border-radius: 10px;
      font-family: inherit; font-size: 14px; color: #1A1D29;
      background: #fff; outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-input:focus { border-color: #4F5BD5; box-shadow: 0 0 0 3px rgba(79,91,213,.10); }
    .form-input::placeholder { color: #C4C7D0; }
    .form-select {
      width: 100%; padding: 11px 36px 11px 14px;
      border: 1.5px solid #E5E7EB; border-radius: 10px;
      font-family: inherit; font-size: 14px; color: #1A1D29;
      background: #fff; outline: none; appearance: none; cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' viewBox='0 0 12 7'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-select:focus { border-color: #4F5BD5; box-shadow: 0 0 0 3px rgba(79,91,213,.10); }
    .form-hint { font-size: 11.5px; color: #9CA3AF; margin-top: 5px; }

    /* ═══════════════════════════════════
       VEHICLE CARD PICKER
    ═══════════════════════════════════ */
    .vehicle-search-wrap { position: relative; margin-bottom: 16px; }
    .vehicle-search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; }
    .vehicle-search-input { width: 100%; padding: 10px 14px 10px 36px; border: 1.5px solid #E5E7EB; border-radius: 10px; font-family: inherit; font-size: 13.5px; color: #1A1D29; background: #F9FAFB; outline: none; transition: border-color .2s, box-shadow .2s; }
    .vehicle-search-input:focus { border-color: #4F5BD5; background: #fff; box-shadow: 0 0 0 3px rgba(79,91,213,.10); }

    .vehicle-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
      max-height: 420px;
      overflow-y: auto;
      padding-right: 2px;
    }
    .vehicle-grid::-webkit-scrollbar { width: 4px; }
    .vehicle-grid::-webkit-scrollbar-track { background: transparent; }
    .vehicle-grid::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 4px; }

    /* Hidden radio input */
    .vehicle-card-radio { display: none; }

    /* Card label */
    .vehicle-card {
      display: flex; flex-direction: column;
      border: 2px solid #E5E7EB; border-radius: 13px;
      padding: 14px; cursor: pointer;
      transition: border-color .2s, box-shadow .2s, transform .15s;
      position: relative; background: #fff;
      user-select: none;
    }
    .vehicle-card:hover {
      border-color: #A5B4FC;
      box-shadow: 0 4px 14px rgba(79,91,213,.12);
      transform: translateY(-1px);
    }
    .vehicle-card-radio:checked + .vehicle-card {
      border-color: #4F5BD5;
      background: #F5F6FE;
      box-shadow: 0 0 0 3px rgba(79,91,213,.14), 0 4px 14px rgba(79,91,213,.10);
    }

    /* Check badge */
    .vehicle-card .check-badge {
      position: absolute; top: 10px; right: 10px;
      width: 20px; height: 20px; border-radius: 50%;
      background: #4F5BD5; color: #fff;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 800;
      opacity: 0; transition: opacity .2s;
      pointer-events: none;
    }
    .vehicle-card-radio:checked + .vehicle-card .check-badge { opacity: 1; }

    /* Car emoji / icon */
    .vehicle-card-icon {
      font-size: 28px; margin-bottom: 10px;
      width: 48px; height: 48px; border-radius: 12px;
      background: #F3F4F8;
      display: flex; align-items: center; justify-content: center;
    }
    .vehicle-card-radio:checked + .vehicle-card .vehicle-card-icon { background: #E8EAFB; }

    .vehicle-card-brand {
      font-size: 13.5px; font-weight: 800; color: #1A1D29;
      line-height: 1.3; margin-bottom: 2px;
    }
    .vehicle-card-type {
      font-size: 12px; color: #6B7280; margin-bottom: 8px;
    }

    /* Color badge */
    .vehicle-card-color {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 11px; font-weight: 600; color: #6B7280;
      background: #F3F4F6; border-radius: 6px; padding: 3px 8px;
      margin-bottom: 10px;
    }
    .color-dot { width: 8px; height: 8px; border-radius: 50%; background: #9CA3AF; flex-shrink: 0; }

    /* Divider in card */
    .vehicle-card-divider { height: 1px; background: #F3F4F6; margin-bottom: 10px; }

    .vehicle-card-price { font-size: 14px; font-weight: 800; color: #1A1D29; }
    .vehicle-card-stock { font-size: 11px; color: #9CA3AF; margin-top: 2px; }
    .vehicle-card-stock.low { color: #F59E0B; }
    .vehicle-card-stock.out { color: #EF4444; }

    .vehicle-empty { text-align: center; padding: 30px 16px; color: #9CA3AF; font-size: 13px; }

    /* ── Selected Summary Card ── */
    .summary-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; overflow: hidden; position: sticky; top: 76px; }
    .summary-header { padding: 18px 22px 16px; border-bottom: 1px solid #F3F4F6; }
    .summary-title { font-size: 14px; font-weight: 800; color: #1A1D29; }
    .summary-body { padding: 20px 22px; }

    .summary-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
    .summary-row:last-child { margin-bottom: 0; }
    .summary-key { font-size: 12px; color: #9CA3AF; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .summary-val { font-size: 13.5px; font-weight: 700; color: #1A1D29; text-align: right; max-width: 180px; }
    .summary-val.placeholder { color: #C4C7D0; font-weight: 500; }
    .summary-divider { height: 1px; background: #F3F4F6; margin: 16px 0; }
    .summary-price { font-size: 22px; font-weight: 800; color: #1A1D29; }
    .summary-price-label { font-size: 11px; color: #9CA3AF; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }

    /* ── Actions ── */
    .form-actions { display: flex; gap: 10px; padding: 18px 22px; border-top: 1px solid #F3F4F6; background: #FAFAFA; }
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; font-family: inherit; font-size: 14px; font-weight: 600; padding: 12px 22px; border-radius: 10px; border: 1.5px solid #E5E7EB; background: #fff; color: #374151; cursor: pointer; text-decoration: none; transition: all .15s; flex: 1; }
    .btn:hover { background: #F9FAFB; }
    .btn-primary { background: #4F5BD5; color: #fff; border-color: #4F5BD5; flex: 2; }
    .btn-primary:hover { background: #4350C4; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }

    /* Payment method pills */
    .payment-options { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .payment-radio { display: none; }
    .payment-pill {
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 12px 14px; border: 2px solid #E5E7EB; border-radius: 10px;
      font-size: 13.5px; font-weight: 600; color: #6B7280;
      cursor: pointer; transition: all .2s;
    }
    .payment-pill:hover { border-color: #A5B4FC; color: #4F5BD5; }
    .payment-radio:checked + .payment-pill { border-color: #4F5BD5; background: #F5F6FE; color: #4F5BD5; }

    /* no-stock overlay */
    .vehicle-card.no-stock { opacity: .5; pointer-events: none; }
  </style>
</head>
<body>
<div class="shell">

  <!-- ═══ TOPBAR ═══ -->
  <?php require ROOT_PATH . '/app/views/layouts/TopBar.php'; ?>

  <div class="body-row">
    <?php require $sidebarPath; ?>

    <main class="main">

      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <a href="/transactions">Penjualan Mobil</a>
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M4 2l4 4-4 4" stroke="#C4C7D0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Buat Transaksi Baru</span>
      </div>

      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">Transaksi Penjualan Baru</h1>
        <p class="page-sub">Isi data pelanggan, pilih kendaraan, dan tentukan metode pembayaran.</p>
      </div>

      <form method="POST" action="/transactions" id="form-transaksi">
        <div class="form-layout">

          <!-- ══ LEFT COLUMN ══ -->
          <div>

            <!-- Step 1: Data Pelanggan -->
            <div class="section-card">
              <div class="section-header">
                <div class="section-step">1</div>
                <div>
                  <div class="section-title">Data Pelanggan</div>
                  <div class="section-desc">Pilih pelanggan yang sudah terdaftar</div>
                </div>
              </div>
              <div class="section-body">

                <div class="form-group">
                  <label class="field-label" for="customer_id">Pilih Pelanggan <span class="req">*</span></label>
                  <select class="form-select" name="customer_id" id="customer_id" required onchange="updateSummary()">
                    <option value="">— Pilih Pelanggan —</option>
                    <?php foreach ($customers as $customer): ?>
                      <option
                        value="<?= $customer['id'] ?>"
                        data-name="<?= htmlspecialchars($customer['name']) ?>"
                        data-phone="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                        data-ktp="<?= htmlspecialchars($customer['ktp_number'] ?? '') ?>"
                        data-address="<?= htmlspecialchars($customer['address'] ?? '') ?>"
                      >
                        <?= htmlspecialchars($customer['name']) ?> — <?= htmlspecialchars($customer['phone'] ?? '') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="form-hint">Belum terdaftar? <a href="/customers/create" style="color:#4F5BD5;font-weight:600;">Daftarkan pelanggan baru</a></div>
                </div>

                <!-- Auto-fill from customer data -->
                <div class="form-row">
                  <div class="form-group">
                    <label class="field-label" for="ktp_number">Nomor KTP / NIK <span class="req">*</span></label>
                    <input class="form-input" id="ktp_number" type="text" name="ktp_number" required placeholder="16 digit NIK" maxlength="16">
                  </div>
                  <div class="form-group">
                    <label class="field-label" for="phone_display">No. Telepon</label>
                    <input class="form-input" id="phone_display" type="text" placeholder="Auto-isi dari pelanggan" disabled>
                  </div>
                </div>

                <div class="form-group">
                  <label class="field-label" for="address">Alamat <span class="req">*</span></label>
                  <textarea class="form-input" id="address" name="address" rows="3" required placeholder="Alamat sesuai KTP" style="resize:vertical;"></textarea>
                </div>

              </div>
            </div>

            <!-- Step 2: Pilih Kendaraan (CARD) -->
            <div class="section-card">
              <div class="section-header">
                <div class="section-step">2</div>
                <div>
                  <div class="section-title">Pilih Kendaraan</div>
                  <div class="section-desc">Klik card untuk memilih unit yang tersedia</div>
                </div>
              </div>
              <div class="section-body">

                <!-- Search vehicles -->
                <div class="vehicle-search-wrap">
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <circle cx="6.5" cy="6.5" r="4.5" stroke="#9CA3AF" stroke-width="1.5"/>
                    <path d="M10 10l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                  <input class="vehicle-search-input" id="vehicle-search" type="text" placeholder="Cari merek, tipe atau warna...">
                </div>

                <!-- Vehicle Cards Grid -->
                <div class="vehicle-grid" id="vehicle-grid">
                  <?php if (empty($vehicles)): ?>
                    <div class="vehicle-empty" style="grid-column:span 2;">
                      <div style="font-size:36px;margin-bottom:10px;">🚗</div>
                      Belum ada kendaraan tersedia.
                    </div>
                  <?php else: ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                      <?php
                        $stock     = intval($vehicle['stock_quantity'] ?? 0);
                        $noStock   = $stock <= 0;
                        $stockClass = $stock <= 0 ? 'out' : ($stock <= 2 ? 'low' : '');
                        $stockLabel = $stock <= 0 ? 'Stok Habis' : ($stock == 1 ? '1 unit tersisa' : $stock . ' unit tersedia');
                        // Color mapping
                        $colorMap = [
                          'putih' => '#E5E7EB', 'hitam' => '#374151', 'silver' => '#9CA3AF',
                          'merah' => '#EF4444', 'biru'  => '#3B82F6', 'hijau'  => '#22C55E',
                          'kuning'=> '#EAB308', 'abu'   => '#6B7280', 'coklat' => '#92400E',
                          'orange'=> '#F97316',
                        ];
                        $colorLower = strtolower($vehicle['color'] ?? '');
                        $dotColor = '#9CA3AF';
                        foreach ($colorMap as $key => $hex) {
                          if (strpos($colorLower, $key) !== false) { $dotColor = $hex; break; }
                        }
                      ?>
                      <input
                        type="radio"
                        class="vehicle-card-radio"
                        name="vehicle_id"
                        id="vehicle-<?= $vehicle['id'] ?>"
                        value="<?= $vehicle['id'] ?>"
                        <?= $noStock ? 'disabled' : '' ?>
                        data-brand="<?= htmlspecialchars($vehicle['brand']) ?>"
                        data-type="<?= htmlspecialchars($vehicle['type']) ?>"
                        data-color="<?= htmlspecialchars($vehicle['color']) ?>"
                        data-price="<?= $vehicle['price'] ?>"
                        data-stock="<?= $stock ?>"
                        onchange="updateSummary()"
                      >
                      <label
                        class="vehicle-card <?= $noStock ? 'no-stock' : '' ?>"
                        for="vehicle-<?= $vehicle['id'] ?>"
                        data-search="<?= strtolower(htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['type'] . ' ' . $vehicle['color'])) ?>"
                      >
                        <div class="check-badge">✓</div>
                        <div class="vehicle-card-icon">🚗</div>
                        <div class="vehicle-card-brand"><?= htmlspecialchars($vehicle['brand']) ?></div>
                        <div class="vehicle-card-type"><?= htmlspecialchars($vehicle['type']) ?></div>
                        <div class="vehicle-card-color">
                          <span class="color-dot" style="background:<?= $dotColor ?>;"></span>
                          <?= htmlspecialchars($vehicle['color']) ?>
                        </div>
                        <div class="vehicle-card-divider"></div>
                        <div class="vehicle-card-price">Rp <?= number_format($vehicle['price'], 0, ',', '.') ?></div>
                        <div class="vehicle-card-stock <?= $stockClass ?>"><?= $stockLabel ?></div>
                      </label>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>

                <!-- No result from search -->
                <div id="vehicle-no-result" style="display:none;" class="vehicle-empty">Tidak ada kendaraan yang sesuai pencarian.</div>

              </div>
            </div>

            <!-- Step 3: Metode Pembayaran -->
            <div class="section-card">
              <div class="section-header">
                <div class="section-step">3</div>
                <div>
                  <div class="section-title">Metode Pembayaran</div>
                  <div class="section-desc">Pilih cara pelanggan membayar</div>
                </div>
              </div>
              <div class="section-body">
                <div class="payment-options">
                  <input type="radio" class="payment-radio" name="payment_type" id="pay-tunai" value="2" onchange="updateSummary()">
                  <label class="payment-pill" for="pay-tunai">
                    💵 Tunai (Cash)
                  </label>
                  <input type="radio" class="payment-radio" name="payment_type" id="pay-kredit" value="1" onchange="updateSummary()">
                  <label class="payment-pill" for="pay-kredit">
                    💳 Kredit (Leasing)
                  </label>
                </div>
              </div>
            </div>

          </div><!-- end LEFT -->

          <!-- ══ RIGHT COLUMN — Summary ══ -->
          <div>
            <div class="summary-card">
              <div class="summary-header">
                <div class="summary-title">📋 Ringkasan Transaksi</div>
              </div>
              <div class="summary-body">

                <div class="summary-row">
                  <div class="summary-key">Pelanggan</div>
                  <div class="summary-val placeholder" id="sum-customer">Belum dipilih</div>
                </div>
                <div class="summary-row">
                  <div class="summary-key">No. KTP</div>
                  <div class="summary-val placeholder" id="sum-ktp">—</div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row">
                  <div class="summary-key">Kendaraan</div>
                  <div class="summary-val placeholder" id="sum-vehicle">Belum dipilih</div>
                </div>
                <div class="summary-row">
                  <div class="summary-key">Warna</div>
                  <div class="summary-val placeholder" id="sum-color">—</div>
                </div>
                <div class="summary-row">
                  <div class="summary-key">Stok</div>
                  <div class="summary-val placeholder" id="sum-stock">—</div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-row">
                  <div class="summary-key">Metode</div>
                  <div class="summary-val placeholder" id="sum-payment">Belum dipilih</div>
                </div>

                <div class="summary-divider"></div>

                <div style="margin-bottom:20px;">
                  <div class="summary-price-label">Total Harga</div>
                  <div class="summary-price" id="sum-price">Rp —</div>
                </div>

              </div>
              <div class="form-actions">
                <a href="/transactions" class="btn">Batal</a>
                <button type="submit" class="btn btn-primary" id="btn-simpan-transaksi" disabled>
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                    <path d="M2 8l4.5 4.5L14 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Simpan Transaksi
                </button>
              </div>
            </div>
          </div><!-- end RIGHT -->

        </div><!-- end form-layout -->
      </form>

    </main>
  </div>
</div>

<script>
  // ── Auto-fill dari dropdown pelanggan ──
  const custSelect = document.getElementById('customer_id');
  custSelect.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('ktp_number').value   = opt.dataset.ktp     || '';
    document.getElementById('address').value       = opt.dataset.address || '';
    document.getElementById('phone_display').value = opt.dataset.phone   || '';
    updateSummary();
  });

  // ── Live search kendaraan ──
  document.getElementById('vehicle-search').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    const labels = document.querySelectorAll('#vehicle-grid .vehicle-card');
    const inputs = document.querySelectorAll('#vehicle-grid .vehicle-card-radio');
    let anyVisible = false;
    labels.forEach((label, i) => {
      const text = label.dataset.search || '';
      const match = !q || text.includes(q);
      // Each radio is before its label in DOM
      const wrap = inputs[i] ? inputs[i].parentNode : null;
      // Radio & label are siblings under .vehicle-grid
      if (inputs[i]) inputs[i].style.display = match ? '' : 'none';
      label.style.display = match ? '' : 'none';
      if (match) anyVisible = true;
    });
    document.getElementById('vehicle-no-result').style.display = anyVisible ? 'none' : 'block';
  });

  // ── Update ringkasan ──
  function updateSummary() {
    // Customer
    const custOpt = custSelect.options[custSelect.selectedIndex];
    const custName = custOpt && custOpt.value ? custOpt.dataset.name : null;
    setText('sum-customer', custName, 'Belum dipilih');

    // KTP
    const ktp = document.getElementById('ktp_number').value.trim();
    setText('sum-ktp', ktp || null, '—');

    // Vehicle
    const checked = document.querySelector('.vehicle-card-radio:checked');
    if (checked) {
      setText('sum-vehicle', checked.dataset.brand + ' ' + checked.dataset.type, 'Belum dipilih');
      setText('sum-color',   checked.dataset.color, '—');
      setText('sum-stock',   checked.dataset.stock + ' unit', '—');
      setText('sum-price',   'Rp ' + Number(checked.dataset.price).toLocaleString('id-ID'), 'Rp —');
    } else {
      setText('sum-vehicle', null, 'Belum dipilih');
      setText('sum-color',   null, '—');
      setText('sum-stock',   null, '—');
      setText('sum-price',   null, 'Rp —');
    }

    // Payment
    const pay = document.querySelector('.payment-radio:checked');
    setText('sum-payment', pay ? (pay.value == 1 ? 'Kredit (Leasing)' : 'Tunai (Cash)') : null, 'Belum dipilih');

    // Enable/disable submit
    const ready = custOpt && custOpt.value && checked && pay && ktp;
    document.getElementById('btn-simpan-transaksi').disabled = !ready;
  }

  function setText(id, value, placeholder) {
    const el = document.getElementById(id);
    if (!el) return;
    if (value) {
      el.textContent = value;
      el.classList.remove('placeholder');
    } else {
      el.textContent = placeholder;
      el.classList.add('placeholder');
    }
  }

  // Update KTP summary on type
  document.getElementById('ktp_number').addEventListener('input', updateSummary);
</script>
</body>
</html>
