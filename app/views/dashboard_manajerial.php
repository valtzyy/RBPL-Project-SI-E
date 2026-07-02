<?php
/**
 * View: dashboard_manajerial.php
 * DealerLink – Executive Managerial Dashboard
 *
 * Controller: DashboardController@index  →  $this->view('dashboard_manajerial')
 * Tidak ada variabel yang di-pass dari controller.
 * Data user diambil langsung dari Auth:: (sesuai core/Auth.php).
 * Data KPI & chart diambil via fetch() ke endpoint /api/dashboard/*
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard – DealerLink</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-bg:         #1e2235;
            --sidebar-text:       #a0aec0;
            --sidebar-active-bg:  #2d3555;
            --sidebar-hover-bg:   #252b42;
            --accent:             #6c63ff;
            --accent-soft:        rgba(108,99,255,.12);
            --topbar-bg:          #ffffff;
            --topbar-border:      #e8eaf0;
            --page-bg:            #f0f2f7;
            --card-bg:            #ffffff;
            --card-border:        #e8eaf0;
            --card-shadow:        0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
            --text-primary:       #1a202c;
            --text-secondary:     #4a5568;
            --text-muted:         #718096;
            --green:   #38a169;
            --orange:  #dd6b20;
            --red:     #e53e3e;
            --blue:    #3182ce;
            --purple:  #805ad5;
            --sidebar-w: 220px;
            --topbar-h:  56px;
            --radius:    10px;
        }

        html, body { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            background: var(--page-bg);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .sidebar-brand .brand-name { font-size: 20px; font-weight: 700; color: #fff; letter-spacing: -.3px; }
        .sidebar-brand .brand-sub  { font-size: 10px; color: var(--sidebar-text); text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }

        /* ── USER BLOCK (clickable) ── */
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,.06);
            text-decoration: none;
            transition: background .15s;
            cursor: pointer;
        }
        .sidebar-user:hover { background: var(--sidebar-hover-bg); }

        .sidebar-user .avatar {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0;
            text-transform: uppercase;
        }
        .sidebar-user .user-info .uname { font-size: 13px; font-weight: 600; color: #fff; }
        .sidebar-user .user-info .urole  { font-size: 11px; color: var(--sidebar-text); }
        .sidebar-user .user-arrow { margin-left: auto; color: var(--sidebar-text); font-size: 10px; }

        /* ── USER DROPDOWN ── */
        .user-dropdown {
            display: none;
            background: #17192d;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .user-dropdown.open { display: block; }
        .user-dropdown a,
        .user-dropdown button {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            padding: 10px 20px 10px 26px;
            color: var(--sidebar-text);
            font-size: 12.5px;
            font-family: inherit;
            font-weight: 500;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            text-align: left;
            transition: background .12s, color .12s;
        }
        .user-dropdown a:hover,
        .user-dropdown button:hover { background: var(--sidebar-hover-bg); color: #fff; }
        .user-dropdown button { color: #fc8181; }
        .user-dropdown button:hover { color: #fff; background: #7f1d1d; }
        .user-dropdown svg { flex-shrink: 0; opacity: .75; }

        /* ── NAV ── */
        .sidebar-nav { flex: 1; padding: 12px 0; }
        .nav-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 1.2px;
            color: rgba(160,174,192,.5); padding: 12px 20px 6px; font-weight: 600;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px;
            text-decoration: none; color: var(--sidebar-text);
            font-size: 13.5px; font-weight: 500;
            transition: background .15s, color .15s; position: relative;
        }
        .nav-item:hover { background: var(--sidebar-hover-bg); color: #fff; }
        .nav-item.active { background: var(--sidebar-active-bg); color: #fff; }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px; background: var(--accent); border-radius: 0 2px 2px 0;
        }
        .nav-item svg { width: 17px; height: 17px; opacity: .75; flex-shrink: 0; }
        .nav-item.active svg { opacity: 1; }

        /* ── MAIN ── */
        .main-wrapper { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        .topbar {
            height: var(--topbar-h); background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; position: sticky; top: 0; z-index: 50;
        }
        .topbar .page-title { font-size: 16px; font-weight: 600; }
        .topbar .page-sub   { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 7px; font-size: 12.5px;
            font-weight: 600; cursor: pointer; text-decoration: none;
            border: none; font-family: inherit; transition: opacity .15s;
        }
        .btn:active { transform: scale(.97); }
        .btn-success { background: #d4edda; color: #155724; }
        .btn-success:hover { background: #c3e6cb; }
        .btn-outline { background: transparent; color: var(--text-secondary); border: 1.5px solid var(--card-border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        .page-content { flex: 1; padding: 24px 28px; }

        /* ── ALERT ── */
        .alert-critical {
            display: none;
            background: #fff8f0; border-left: 4px solid var(--orange);
            border-radius: 0 var(--radius) var(--radius) 0;
            padding: 14px 18px; margin-bottom: 22px;
        }
        .alert-critical .alert-title {
            font-size: 13px; font-weight: 700; color: var(--orange);
            margin-bottom: 8px; display: flex; align-items: center; gap: 6px;
        }
        .alert-critical ul { padding-left: 18px; color: #7b341e; font-size: 13px; }
        .alert-critical li { margin-bottom: 4px; }

        /* ── SECTION HEADER ── */
        .section-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 14px; }
        .section-title  { font-size: 13.5px; font-weight: 700; display: flex; align-items: center; gap: 7px; }
        .section-sub    { font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ── CARDS ── */
        .card {
            background: var(--card-bg); border: 1px solid var(--card-border);
            border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 18px 20px;
        }

        /* Today strip */
        .today-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        @media (max-width: 1100px) { .today-strip { grid-template-columns: repeat(2,1fr); } }
        .today-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 16px 18px; border-top: 3px solid var(--accent); }
        .today-card.c-blue   { border-top-color: var(--blue); }
        .today-card.c-green  { border-top-color: var(--green); }
        .today-card.c-purple { border-top-color: var(--purple); }
        .today-card.c-orange { border-top-color: var(--orange); }
        .today-card .tc-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .7px; color: var(--text-muted); margin-bottom: 6px; }
        .today-card .tc-value { font-size: 24px; font-weight: 700; line-height: 1.1; }
        .today-card.c-blue   .tc-value { color: var(--blue); }
        .today-card.c-green  .tc-value { color: var(--green); }
        .today-card.c-purple .tc-value { color: var(--purple); }
        .today-card.c-orange .tc-value { color: var(--orange); }

        /* KPI grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        @media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
        .kpi-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 18px 20px; }
        .kpi-card-title { font-size: 12px; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .6px; padding-bottom: 10px; border-bottom: 1px dashed var(--card-border); margin-bottom: 14px; }
        .kpi-big-val { font-size: 22px; font-weight: 700; color: var(--blue); margin-bottom: 12px; line-height: 1.2; }
        .kpi-row { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; padding: 5px 0; border-bottom: 1px solid #f7f8fb; }
        .kpi-row:last-child { border-bottom: none; }
        .kpi-row .kr-label { color: var(--text-muted); }
        .kpi-row .kr-val   { font-weight: 600; color: var(--text-primary); }

        /* Chart grid */
        .chart-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }
        @media (max-width: 900px) { .chart-grid-2 { grid-template-columns: 1fr; } }
        .chart-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 18px 20px; }
        .chart-card-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 6px; }
        .chart-wrap { position: relative; height: 230px; }

        /* Table section */
        .table-section { display: grid; grid-template-columns: 1.8fr 1fr; gap: 18px; margin-bottom: 22px; }
        @media (max-width: 1000px) { .table-section { grid-template-columns: 1fr; } }
        .table-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 18px 20px; overflow: hidden; }
        .table-card-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }
        .overflow-x { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
        .data-table th { padding: 9px 14px; text-align: left; background: #f7f8fb; color: var(--text-secondary); font-weight: 600; font-size: 11.5px; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--card-border); white-space: nowrap; }
        .data-table td { padding: 11px 14px; border-bottom: 1px solid #f2f4f8; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: #fafbff; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
        .badge-lunas   { background: #d1fae5; color: #065f46; }
        .badge-process { background: #fef3c7; color: #92400e; }
        .badge-cancel  { background: #fee2e2; color: #991b1b; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #c1c9d8; border-radius: 10px; }

        @media (max-width: 768px) { .sidebar { width: 0; overflow: hidden; } .main-wrapper { margin-left: 0; } }
    </style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">DealerLink</div>
        <div class="brand-sub">Management System</div>
    </div>

    <?php if (Auth::check()): ?>
    <!-- USER BLOCK — klik buka dropdown -->
    <div class="sidebar-user" onclick="toggleUserMenu()" id="userBlock">
        <div class="avatar"><?= htmlspecialchars(substr(Auth::user()['name'], 0, 1)) ?></div>
        <div class="user-info">
            <div class="uname"><?= htmlspecialchars(Auth::user()['name']) ?></div>
            <div class="urole"><?= htmlspecialchars(Auth::user()['role_name']) ?></div>
        </div>
        <span class="user-arrow" id="userArrow">▼</span>
    </div>

    <!-- DROPDOWN akun -->
    <div class="user-dropdown" id="userDropdown">
        <a href="/change-password">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Ganti Password
        </a>
        <?php if (Auth::role() === 'Admin Dealer'): ?>
        <a href="/admin/users">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Manajemen Akun
        </a>
        <?php endif; ?>
        <form method="POST" action="/logout" style="margin:0;">
            <button type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
    <?php endif; ?>

    <nav class="sidebar-nav">
        <div class="nav-label">Utama</div>
        <a href="/dashboard" class="nav-item active">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Executive Dashboard
        </a>

        <a href="/inventory" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path d="M13 6H6l-3 6v3h3m10-9h2l3 6v3h-3m-4-9l1 9M6 12h13"/>
            </svg>
            Inventory Kendaraan
        </a>

        <a href="/transactions" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>
            </svg>
            Transaksi
        </a>
        
        <a href="/sparepart" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Sparepart
        </a>
     
        <div class="nav-label" style="margin-top:6px;">Layanan</div>
        
        <a href="/finance/payments" class="nav-item">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Pembayaran
        </a>
    </nav>
</aside>


<!-- ══════════ MAIN ══════════ -->
<div class="main-wrapper">

    <header class="topbar">
        <div>
            <div class="page-title">Executive Managerial Dashboard</div>
            <div class="page-sub">Ringkasan performa dealer · <?= date('d M Y') ?></div>
        </div>
        <div class="topbar-right">
            <a href="/dashboard/export" class="btn btn-success">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/>
                </svg>
                Ekspor CSV
            </a>
            <a href="/sparepart" class="btn btn-outline">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
                </svg>
                Gudang Suku Cadang
            </a>
        </div>
    </header>

    <main class="page-content">

        <!-- Low Stock Alert -->
        <div id="alertLowStock" class="alert-critical">
            <div class="alert-title">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Peringatan Stok Kritis – Suku Cadang Menipis
            </div>
            <ul id="alertLowStockList"></ul>
        </div>

        <!-- Today Performance -->
        <div class="section-header">
            <div>
                <div class="section-title">📅 Performa Hari Ini</div>
                <div class="section-sub">Data realtime transaksi & servis · <?= date('d M Y') ?></div>
            </div>
        </div>
        <div class="today-strip">
            <div class="today-card c-blue">
                <div class="tc-label">Unit Terjual Hari Ini</div>
                <div class="tc-value" id="todayUnitsSold">–</div>
            </div>
            <div class="today-card c-green">
                <div class="tc-label">Pendapatan Servis Hari Ini</div>
                <div class="tc-value" id="todayServiceRevenue">–</div>
            </div>
            <div class="today-card c-purple">
                <div class="tc-label">Servis Selesai Hari Ini</div>
                <div class="tc-value" id="todayServicesCompleted">–</div>
            </div>
            <div class="today-card c-orange">
                <div class="tc-label">Booking Aktif Hari Ini</div>
                <div class="tc-value" id="todayBookings">–</div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="section-header">
            <div>
                <div class="section-title">📊 Indikator Performa Kunci (KPI)</div>
                <div class="section-sub">Akumulasi seluruh periode data sistem</div>
            </div>
        </div>
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-card-title">💵 Finansial &amp; Penjualan</div>
                <div class="kpi-big-val" id="kpiTotalRevenue">Memuat...</div>
                <div class="kpi-row"><span class="kr-label">Unit Terjual (Lunas)</span><span class="kr-val" id="kpiTotalVolumeSold">–</span></div>
                <div class="kpi-row"><span class="kr-label">Pendapatan Servis</span><span class="kr-val" id="kpiTotalServiceRevenue" style="color:var(--green)">–</span></div>
                <div class="kpi-row"><span class="kr-label">Volume Armada (Semua)</span><span class="kr-val" id="kpiTotalVolume">–</span></div>
                <div class="kpi-row"><span class="kr-label">Rasio Lunas</span><span class="kr-val" id="kpiPersenLunas" style="color:var(--green)">–</span></div>
                <div class="kpi-row"><span class="kr-label">Kredit Ditolak</span><span class="kr-val" id="kpiPersenDitolak" style="color:var(--red)">–</span></div>
            </div>
            <div class="kpi-card">
                <div class="kpi-card-title">🏢 Nilai Aset Terinventaris</div>
                <div class="kpi-big-val" id="kpiTotalAsset">Memuat...</div>
                <div class="kpi-row"><span class="kr-label">Aset Unit Mobil</span><span class="kr-val" id="kpiVehicleAsset">–</span></div>
                <div class="kpi-row"><span class="kr-label">Aset Suku Cadang</span><span class="kr-val" id="kpiSparepartAsset">–</span></div>
                <div class="kpi-row"><span class="kr-label">Diperbarui</span><span class="kr-val"><?= date('d M Y H:i') ?></span></div>
            </div>
            <div class="kpi-card">
                <div class="kpi-card-title">🔄 Perputaran Suku Cadang</div>
                <div class="kpi-big-val" id="kpiTurnoverRatio">Memuat...</div>
                <div class="kpi-row"><span class="kr-label">COGS Suku Cadang</span><span class="kr-val" id="kpiSparepartCogs">–</span></div>
                <div class="kpi-row"><span class="kr-label">Rata-rata Inventaris</span><span class="kr-val" id="kpiSparepartAvg">–</span></div>
                <div class="kpi-row"><span class="kr-label">Status Efisiensi</span><span class="kr-val" id="kpiSparepartStatus">–</span></div>
            </div>
            <div class="kpi-card">
                <div class="kpi-card-title">🔍 Prospek &amp; Stok Kritis</div>
                <div class="kpi-big-val" id="kpiActiveProspectsTotal" style="color:var(--purple)">–</div>
                <div class="kpi-row"><span class="kr-label">Prospek Penjualan</span><span class="kr-val" id="kpiActiveSalesProspects" style="color:var(--blue)">–</span></div>
                <div class="kpi-row"><span class="kr-label">Booking Servis Aktif</span><span class="kr-val" id="kpiActiveServiceProspects" style="color:var(--blue)">–</span></div>
                <div class="kpi-row"><span class="kr-label">Suku Cadang Kritis</span><span class="kr-val" id="kpiLowStockCount" style="color:var(--red)">–</span></div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-title">📈 Rasio Lunas vs Kredit Ditolak</div>
                <div class="chart-wrap"><canvas id="chartKpi"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-card-title">🛠️ Tren Kunjungan Servis Bulanan</div>
                <div class="chart-wrap"><canvas id="chartServiceTrends"></canvas></div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="chart-card-title">🚗 Volume Penjualan Mobil Bulanan (Unit)</div>
                <div class="chart-wrap"><canvas id="chartSalesVolume"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-card-title">💰 Nominal Pendapatan Penjualan Bulanan</div>
                <div class="chart-wrap"><canvas id="chartSalesRevenue"></canvas></div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="chart-grid-2" style="margin-bottom:22px;">
            <div class="chart-card">
                <div class="chart-card-title">📊 Komposisi Status Stok Unit Mobil</div>
                <div class="chart-wrap"><canvas id="chartStockStatus"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="chart-card-title">🏭 Distribusi Stok Unit per Merek</div>
                <div class="chart-wrap"><canvas id="chartStockAllocation"></canvas></div>
            </div>
        </div>

        <!-- Tables -->
        <div class="table-section">
            <div class="table-card">
                <div class="table-card-title">📋 10 Transaksi Penjualan Terbaru</div>
                <div class="overflow-x">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th><th>Kode</th><th>Pelanggan</th>
                                <th>Mobil</th><th>Tipe Bayar</th><th>Harga</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentTransactionsBody">
                            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table-card">
                <div class="table-card-title">🏆 5 Merek Terlaris (Lunas)</div>
                <div class="overflow-x">
                    <table class="data-table">
                        <thead>
                            <tr><th>Merek</th><th>Terjual</th><th>Revenue</th></tr>
                        </thead>
                        <tbody id="topBrandsBody">
                            <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:24px">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div><!-- end .main-wrapper -->


<script>
/* ── Helpers ── */
const fmt = v => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v);
const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
const gridDefaults = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } };

/* ── Dropdown toggle ── */
function toggleUserMenu() {
    const dd = document.getElementById('userDropdown');
    const ar = document.getElementById('userArrow');
    dd.classList.toggle('open');
    if (ar) ar.textContent = dd.classList.contains('open') ? '▲' : '▼';
}
document.addEventListener('click', function(e) {
    const block = document.getElementById('userBlock');
    const dd    = document.getElementById('userDropdown');
    if (block && dd && !block.contains(e.target) && !dd.contains(e.target)) {
        dd.classList.remove('open');
        const ar = document.getElementById('userArrow');
        if (ar) ar.textContent = '▼';
    }
});

/* ── 1. KPI & Credit Doughnut ── */
fetch('/api/dashboard/kpi').then(r => r.json()).then(d => {
    set('kpiTotalVolume', d.total_unit + ' Unit');
    set('kpiPersenLunas', d.persen_lunas + '%');
    set('kpiPersenDitolak', d.persen_ditolak + '%');
    new Chart(document.getElementById('chartKpi'), {
        type: 'doughnut',
        data: {
            labels: ['Pembayaran Lunas', 'Kredit Ditolak'],
            datasets: [{ data: [d.persen_lunas, d.persen_ditolak], backgroundColor: ['#38a169','#e53e3e'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 12 } } } } }
    });
});

/* ── 2. Today ── */
fetch('/api/dashboard/today').then(r => r.json()).then(d => {
    set('todayUnitsSold', d.units_sold_today + ' Unit');
    set('todayServiceRevenue', fmt(d.service_revenue_today));
    set('todayServicesCompleted', d.services_completed_today + ' Selesai');
    set('todayBookings', d.bookings_today + ' Booking');
});

/* ── 3. Accumulated ── */
fetch('/api/dashboard/accumulated').then(r => r.json()).then(d => {
    set('kpiTotalVolumeSold', d.total_units_sold + ' Unit');
    set('kpiTotalServiceRevenue', fmt(d.total_service_revenue));
    const tot = d.active_sales_prospects + d.active_service_prospects;
    set('kpiActiveProspectsTotal', tot + ' Prospek');
    set('kpiActiveSalesProspects', d.active_sales_prospects + ' Transaksi');
    set('kpiActiveServiceProspects', d.active_service_prospects + ' Booking');
    set('kpiLowStockCount', d.low_stock_count + ' Item');
});

/* ── 4. Inventory KPI ── */
fetch('/api/dashboard/inventory-kpi').then(r => r.json()).then(d => {
    set('kpiTotalAsset', fmt(d.total_asset_value));
    set('kpiVehicleAsset', fmt(d.vehicle_asset_value));
    set('kpiSparepartAsset', fmt(d.sparepart_asset_value));
    const el = document.getElementById('kpiTurnoverRatio');
    if (el) el.innerHTML = d.sparepart_turnover_ratio.toFixed(2) + 'x <span style="font-size:13px;font-weight:400;color:var(--text-muted)">/ thn</span>';
    set('kpiSparepartCogs', fmt(d.sparepart_cogs));
    set('kpiSparepartAvg', fmt(d.sparepart_avg_inventory));
    const ratio = d.sparepart_turnover_ratio;
    const statusEl = document.getElementById('kpiSparepartStatus');
    if (statusEl) {
        if (ratio === 0) { statusEl.textContent = 'Tidak Ada Perputaran'; statusEl.style.color = '#718096'; }
        else if (ratio < 0.5) { statusEl.textContent = 'Lambat (Overstock)'; statusEl.style.color = '#dd6b20'; }
        else { statusEl.textContent = 'Optimal / Cepat'; statusEl.style.color = '#38a169'; }
    }
});

/* ── 5. Service Trends ── */
fetch('/api/dashboard/trends').then(r => r.json()).then(d => {
    new Chart(document.getElementById('chartServiceTrends'), {
        type: 'line',
        data: { labels: d.map(i => i.bulan), datasets: [{ label: 'Servis', data: d.map(i => i.total), borderColor: '#3182ce', backgroundColor: 'rgba(49,130,206,.1)', fill: true, tension: 0.35, borderWidth: 2, pointRadius: 3 }] },
        options: { ...gridDefaults, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0f2f7' } }, x: { grid: { display: false } } } }
    });
});

/* ── 6. Sales Trends ── */
fetch('/api/dashboard/sales-trends').then(r => r.json()).then(d => {
    const labels  = d.map(i => i.bulan);
    const volume  = d.map(i => i.jumlah_terjual);
    const nominal = d.map(i => i.total_nominal);
    set('kpiTotalRevenue', fmt(nominal.reduce((a, c) => a + parseFloat(c || 0), 0)));
    new Chart(document.getElementById('chartSalesVolume'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'Unit', data: volume, backgroundColor: '#6c63ff', borderRadius: 5, borderSkipped: false }] },
        options: { ...gridDefaults, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0f2f7' } }, x: { grid: { display: false } } } }
    });
    new Chart(document.getElementById('chartSalesRevenue'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Nominal', data: nominal, borderColor: '#e53e3e', backgroundColor: 'rgba(229,62,62,.08)', fill: true, tension: 0.25, borderWidth: 2.5, pointRadius: 3 }] },
        options: { ...gridDefaults, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + Intl.NumberFormat('id-ID').format(v) }, grid: { color: '#f0f2f7' } }, x: { grid: { display: false } } } }
    });
});

/* ── 7. Stock Allocation ── */
fetch('/api/dashboard/stock-allocation').then(r => r.json()).then(d => {
    new Chart(document.getElementById('chartStockAllocation'), {
        type: 'bar',
        data: { labels: d.map(i => i.brand), datasets: [{ data: d.map(i => i.total), backgroundColor: '#319795', borderRadius: 4, borderSkipped: false }] },
        options: { ...gridDefaults, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0f2f7' } }, y: { grid: { display: false } } } }
    });
});

/* ── 8. Details (tables + stock pie + low stock) ── */
fetch('/api/dashboard/details').then(r => r.json()).then(d => {
    // Transactions
    const txBody = document.getElementById('recentTransactionsBody');
    txBody.innerHTML = '';
    if (!d.recent_transactions.length) {
        txBody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px">Belum ada transaksi.</td></tr>';
    } else {
        d.recent_transactions.forEach(tx => {
            const dt = new Date(tx.created_at).toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
            const sc = tx.status === 'lunas' ? 'lunas' : tx.status === 'process' ? 'process' : 'cancel';
            txBody.innerHTML += `<tr>
                <td style="color:var(--text-muted);font-size:12px">${dt}</td>
                <td><strong>${tx.transaction_code}</strong></td>
                <td>${tx.customer_name}</td>
                <td>${tx.brand} ${tx.type}</td>
                <td style="color:var(--text-muted)">${tx.payment_type || '–'}</td>
                <td style="font-weight:600">${fmt(tx.price)}</td>
                <td><span class="badge badge-${sc}">${tx.status}</span></td>
            </tr>`;
        });
    }

    // Top brands
    const brandBody = document.getElementById('topBrandsBody');
    brandBody.innerHTML = '';
    if (!d.top_brands.length) {
        brandBody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:24px">Belum ada data.</td></tr>';
    } else {
        d.top_brands.forEach((br, i) => {
            brandBody.innerHTML += `<tr>
                <td><strong>#${i+1} ${br.brand}</strong></td>
                <td>${br.total_sold} Unit</td>
                <td style="color:var(--green);font-weight:600">${fmt(br.total_revenue)}</td>
            </tr>`;
        });
    }

    // Low stock banner
    if (d.low_stock_spareparts && d.low_stock_spareparts.length > 0) {
        const box  = document.getElementById('alertLowStock');
        const list = document.getElementById('alertLowStockList');
        d.low_stock_spareparts.forEach(sp => {
            list.innerHTML += `<li><strong>${sp.name}</strong> (SKU: ${sp.sku}) – Sisa: <strong>${sp.stock} pcs</strong>, min: ${sp.min_stock} pcs.</li>`;
        });
        box.style.display = 'block';
    }

    // Stock status doughnut
    new Chart(document.getElementById('chartStockStatus'), {
        type: 'doughnut',
        data: {
            labels: d.stock_stats.map(i => i.status),
            datasets: [{ data: d.stock_stats.map(i => i.total), backgroundColor: ['#3182ce','#dd6b20','#38a169'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 16, font: { size: 12 } } } } }
    });
});
</script>

</body>
</html>