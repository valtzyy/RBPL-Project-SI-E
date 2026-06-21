<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement - DealerLink</title>
    
    <style>
        :root {
            --color-bg-main: #f4f6f9;
            --color-sidebar-bg: #eef2ff;
            --color-sidebar-active: #e0e7ff;
            --color-text-primary: #1e293b;
            --color-text-secondary: #64748b;
            --color-primary: #2563eb;
            --color-primary-light: rgba(37, 99, 235, 0.1);
            --color-success: #10b981;
            --color-success-light: #ecfdf5;
            --color-warning: #f59e0b;
            --color-warning-light: #fffbeb;
            --color-border: #e2e8f0;
            
            --radius-sm: 6px;
            --radius-md: 12px;
            --radius-lg: 16px;
            
            --transition-fast: 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            background-color: var(--color-bg-main);
            color: var(--color-text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- SIDEBAR NAVIGATION --- */
        .sidebar {
            width: 260px;
            background-color: var(--color-sidebar-bg);
            border-right: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }

        .logo-container {
            padding: 0 24px;
            margin-bottom: 32px;
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        /* User Profile Card */
        .profile-card {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 16px 24px 16px;
            padding: 12px;
            background-color: transparent;
        }

        .profile-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .profile-role {
            font-size: 11px;
            color: var(--color-text-secondary);
            margin-top: 2px;
        }

        /* Nav Menu */
        .nav-container {
            flex: 1;
        }

        .nav-list {
            list-style: none;
            padding: 0 12px;
        }

        .nav-item {
            margin-bottom: 4px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--color-text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: var(--transition-fast);
        }

        .nav-item a:hover {
            background-color: rgba(0, 0, 0, 0.03);
            color: var(--color-text-primary);
        }

        .nav-item.active a {
            background-color: var(--color-sidebar-active);
            color: var(--color-primary);
        }

        .nav-icon {
            font-size: 16px;
            opacity: 0.8;
        }

        /* --- MAIN CONTENT WRAPPER --- */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Header Bar */
        .header {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
        }

        .page-title-main {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .notification-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
        }

        /* Content Container */
        .content-container {
            padding: 32px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Section Header Block */
        .section-header-block {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-intro h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }

        .page-intro p {
            font-size: 14px;
            color: var(--color-text-secondary);
            margin-top: 4px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .btn-primary {
            background-color: #000000;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #1e293b;
        }

        .btn-secondary {
            background-color: #ffffff;
            border: 1px solid var(--color-border);
            color: var(--color-text-primary);
        }

        .btn-secondary:hover {
            background-color: var(--color-bg-main);
        }

        /* --- DASHBOARD TOP GRID (WIDGETS) --- */
        .dashboard-top {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            margin-bottom: 24px;
        }

        /* Cards Base */
        .card {
            background-color: #ffffff;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .card-badge {
            background-color: #f1f5f9;
            color: var(--color-primary);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .card-footer-link {
            display: block;
            padding: 16px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-primary);
            text-decoration: none;
            border-top: 1px solid var(--color-border);
            background-color: #ffffff;
            transition: var(--transition-fast);
        }

        .card-footer-link:hover {
            background-color: var(--color-bg-main);
        }

        /* Stock Movement Widget */
        .widget-stock {
            background-color: #000000;
            color: #ffffff;
            padding: 24px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 140px;
        }

        .widget-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .widget-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .widget-box-icon {
            font-size: 18px;
            opacity: 0.7;
        }

        .widget-value-row {
            margin-top: 8px;
            display: flex;
            align-items: baseline;
            gap: 6px;
        }

        .widget-value {
            font-size: 42px;
            font-weight: 700;
        }

        .widget-unit {
            font-size: 16px;
            color: #94a3b8;
        }

        .widget-trend {
            font-size: 12px;
            color: var(--color-success);
            background-color: rgba(16, 185, 129, 0.15);
            padding: 4px 10px;
            border-radius: 20px;
            width: fit-content;
            margin-top: auto;
            font-weight: 600;
        }

        /* --- ITEM LISTS RENDERING STYLES --- */
        .item-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--color-border);
        }

        .item-icon-box {
            width: 48px;
            height: 48px;
            background-color: #f1f5f9;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .item-meta {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin-top: 4px;
            display: flex;
            gap: 12px;
        }

        /* Status Badges */
        .badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status-shipping { background-color: #e0f2fe; color: #0369a1; }
        .status-drafting { background-color: #f1f5f9; color: #475569; }
        .status-received { background-color: var(--color-success-light); color: var(--color-success); }

        /* --- DATA TABLE SECTION --- */
        .table-card {
            margin-top: 8px;
        }

        .table-toolbar {
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--color-border);
        }

        .table-toolbar-left h2 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .table-toolbar-left p {
            font-size: 13px;
            color: var(--color-text-secondary);
            margin-top: 2px;
        }

        .table-actions {
            display: flex;
            gap: 12px;
        }

        /* Search Box */
        .search-box-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            color: var(--color-text-secondary);
            font-size: 14px;
        }

        .search-input {
            padding: 10px 16px 10px 40px;
            font-size: 14px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            width: 280px;
            background-color: #f8fafc;
            transition: var(--transition-fast);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--color-primary);
            background-color: #ffffff;
        }

        .filter-btn {
            width: 42px;
            height: 42px;
            border: 1px solid var(--color-border);
            background-color: #ffffff;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Table */
        .data-table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table th {
            background-color: #f8fafc;
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 700;
            color: var(--color-text-secondary);
            text-transform: uppercase;
            border-bottom: 1px solid var(--color-border);
        }

        .data-table td {
            padding: 18px 24px;
            font-size: 14px;
            border-bottom: 1px solid var(--color-border);
            color: #334155;
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* --- MODALS --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-container {
            background-color: #ffffff;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 540px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--color-text-secondary);
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background-color: #f8fafc;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            font-size: 14px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background-color: #ffffff;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo-container">
            <div class="logo-text">DealerLink</div>
        </div>
        
        <div class="profile-card">
            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=faces" alt="Admin Profile" class="profile-img">
            <div class="profile-info">
                <span class="profile-name">Admin Dealer</span>
                <span class="profile-role">Manager Operasional</span>
            </div>
        </div>

        <nav class="nav-container">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="#dashboard"><span class="nav-icon">📊</span> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a href="#inventaris"><span class="nav-icon">🚘</span> Inventaris</a>
                </li>
                <li class="nav-item active">
                    <a href="#procurement"><span class="nav-icon">🛒</span> Procurement</a>
                </li>
                <li class="nav-item">
                    <a href="#sales"><span class="nav-icon">📄</span> Sales SPK</a>
                </li>
                <li class="nav-item">
                    <a href="#servis"><span class="nav-icon">🛠️</span> Layanan Servis</a>
                </li>
                <li class="nav-item">
                    <a href="#laporan"><span class="nav-icon">📈</span> Laporan</a>
                </li>
            </ul>
        </nav>
        
        <div style="margin-top: auto; padding: 12px;">
            <button id="btn-reset-db" class="btn btn-secondary" style="width: 100%; font-size: 11px; padding: 8px;">
                🔄 Reset Mock Data
            </button>
        </div>
    </aside>

    <div class="main-wrapper">
        
        <header class="header">
            <div class="page-title-main">Procurement</div>
            <div class="header-actions">
                <button class="notification-btn">
                    🔔 <span class="notification-badge"></span>
                </button>
            </div>
        </header>

        <main class="content-container">
            
            <div class="section-header-block">
                <div class="page-intro">
                    <h1>Manajemen Pengadaan</h1>
                    <p>Pantau aliran unit dari pabrikan secara real-time.</p>
                </div>
                <button id="btn-open-request" class="btn btn-primary">
                    Buat Permintaan Baru
                </button>
            </div>

            <div class="dashboard-top">
                
                <div class="dashboard-left-col">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">🕒 Estimasi Unit Tiba</span>
                            <span id="active-units-badge" class="card-badge">4 Unit Aktif</span>
                        </div>
                        <div class="estimasi-list" id="estimasi-list">
                            <div class="item-row">
                                <div class="item-icon-box">🚘</div>
                                <div class="item-details">
                                    <div class="item-name">Toyota Raize GR Sport</div>
                                    <div class="item-meta"><span>⚫ Midnight Black</span> <span>⚙️ CVT</span> <span>📅 24 Okt 2023</span></div>
                                </div>
                                <span class="badge-status status-shipping">On Shipping</span>
                            </div>
                            <div class="item-row">
                                <div class="item-icon-box">🚘</div>
                                <div class="item-details">
                                    <div class="item-name">Honda HR-V SE</div>
                                    <div class="item-meta"><span>⚪ Lunar Silver</span> <span>⚙️ CVT</span> <span>📅 02 Nov 2023</span></div>
                                </div>
                                <span class="badge-status status-drafting">Drafting</span>
                            </div>
                        </div>
                        <a href="#data-penerimaan" class="card-footer-link">Lihat Semua Unit Mendatang &rarr;</a>
                    </div>
                </div>

                <div class="dashboard-right-col">
                    <div class="widget-stock">
                        <div class="widget-label-row">
                            <span class="widget-label">Stock Movement</span>
                            <span class="widget-box-icon">📦</span>
                        </div>
                        <div class="widget-value-row">
                            <span class="widget-value" id="stock-value">48</span>
                            <span class="widget-unit">Unit</span>
                        </div>
                        <div class="widget-trend">📈 +12% vs last month</div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">⏳ Riwayat Terbaru</span>
                            <span>🕒</span>
                        </div>
                        <div class="timeline" id="timeline-logs">
                            <div style="padding: 16px; font-size: 13px; border-bottom: 1px solid var(--color-border);">
                                <strong>✅ Unit Diterima Gudang</strong><br>
                                <span style="color:var(--color-text-secondary); font-size:11px;">Mitsubishi Xpander Ultimate (3 Unit) - 1 Jam Lalu</span>
                            </div>
                        </div>
                        <button class="card-footer-link" style="border: none; width: 100%; cursor: pointer;">
                            Lihat Log Lengkap 🔗
                        </button>
                    </div>
                </div>
            </div>

            <div id="data-penerimaan" class="card table-card">
                <div class="table-toolbar">
                    <div class="table-toolbar-left">
                        <h2>Data Penerimaan Unit</h2>
                        <p>Arsip lengkap surat jalan dan kedatangan unit.</p>
                    </div>
                    <div class="table-actions">
                        <div class="search-box-container">
                            <span class="search-icon">🔍</span>
                            <input type="text" id="search-input" class="search-input" placeholder="Cari No. Rangka / Model...">
                        </div>
                        <button class="filter-btn">🎛️</button>
                    </div>
                </div>

                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Surat Jalan</th>
                                <th>Model Kendaraan</th>
                                <th>VIN (Nomor Rangka)</th>
                                <th>Tgl Terima</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr>
                                <td><strong>SJ/2023/1029</strong></td>
                                <td>
                                    <div style="font-weight:700;">Mitsubishi Xpander Ultimate</div>
                                    <div style="font-size:12px; color:var(--color-text-secondary);">White Pearl • MT</div>
                                </td>
                                <td style="font-family:monospace;">MHMXNC12345678</td>
                                <td>24 Okt 2023</td>
                                <td><span class="badge-status status-received">Diterima</span></td>
                                <td><button class="filter-btn" style="width:32px; height:32px; font-size:12px;">👁️</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div class="modal-overlay" id="modal-request">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Buat Permintaan Pengadaan</h2>
                <button class="modal-close-btn">&times;</button>
            </div>
            <form id="form-request">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="distributor">Distributor</label>
                        <select name="distributor" id="distributor" class="form-select" required>
                            <option value="">-- Pilih Distributor Resmi --</option>
                            <option value="TAM">PT Toyota Astra Motor (TAM)</option>
                            <option value="HPM">PT Honda Prospect Motor (HPM)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="model">Model Kendaraan</label>
                        <select name="model" id="model" class="form-select" required>
                            <option value="">-- Pilih Model Mobil --</option>
                            <option value="Toyota Raize GR Sport">Toyota Raize GR Sport</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Surat Permintaan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Opsional: Tempat kamu atau backend menaruh logika javascript aplikasi nanti
        console.log("DealerLink UI Ready.");
    </script>
</body>
</html>