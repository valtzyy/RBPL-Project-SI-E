<?php
// Hitung jumlah statistik secara dinamis dari array $procurements untuk Summary Cards
$total_count = count($procurements ?? []);
$sent_count = 0;
$received_count = 0;
$draft_count = 0;
$rejected_count = 0;

if (!empty($procurements)) {
    foreach ($procurements as $p) {
        $status = strtolower($p['status'] ?? '');
        if ($status === 'sent') {
            $sent_count++;
        } elseif ($status === 'received') {
            $received_count++;
        } elseif ($status === 'draft') {
            $draft_count++;
        } elseif ($status === 'rejected') {
            $rejected_count++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengadaan Kendaraan - DealerLink</title>
    <!-- Google Font Modern -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            
            /* Warna Badge Status Sesuai Aturan Wajib */
            --color-sent-bg: #eff6ff;     /* Biru bg */
            --color-sent-text: #1e40af;   /* Biru text */
            --color-sent-dot: #3b82f6;
            
            --color-received-bg: #ecfdf5; /* Hijau bg */
            --color-received-text: #065f46; /* Hijau text */
            --color-received-dot: #10b981;
            
            --color-draft-bg: #f3f4f6;    /* Abu-abu bg */
            --color-draft-text: #374151;  /* Abu-abu text */
            --color-draft-dot: #6b7280;
            
            --color-rejected-bg: #fef2f2; /* Merah bg */
            --color-rejected-text: #991b1b; /* Merah text */
            --color-rejected-dot: #ef4444;
        }

        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navigation Bar */
        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-card);
            padding: 16px 32px;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .breadcrumb span.active {
            color: var(--text-main);
            font-weight: 600;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .notification-btn:hover {
            background-color: #f1f5f9;
        }

        .notification-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background-color: var(--color-rejected-dot);
            border-radius: 50%;
            border: 2px solid var(--bg-card);
        }

        .user-profile-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Container Utama */
        .container {
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 32px 24px;
            flex-grow: 1;
        }

        /* Judul & Deskripsi Halaman */
        .header-section {
            margin-bottom: 28px;
        }

        .header-section h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header-section p {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Ringkasan Statistik Grid (Summary Cards) */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

.summary-card {
    background-color: var(--bg-card);
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.02);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 6px;
    transition: all 0.2s ease;
    cursor: default;
}

.summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
        

        .summary-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
        }

        /* Garis Indikator Samping Card */
        .card-total::before { background-color: var(--text-main); }
        .card-sent::before { background-color: var(--color-sent-dot); }
        .card-received::before { background-color: var(--color-received-dot); }
        .card-draft::before { background-color: var(--color-draft-dot); }
        .card-rejected::before { background-color: var(--color-rejected-dot); }

        .summary-card-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .summary-info {
            display: flex;
            flex-direction: column;
        }

        .summary-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.1;
        }

        .summary-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* Card Tabel Utama */
        .content-card {
            background-color: var(--bg-card);
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* Bar Filter & Pencarian */
        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            gap: 16px;
            flex-wrap: wrap;
        }

        .filter-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-grow: 1;
            max-width: 600px;
        }

        .search-wrapper {
            position: relative;
            flex-grow: 1;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            width: 18px;
            height: 18px;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 42px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            outline: none;
            background-color: #f8fafc;
            color: var(--text-main);
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--text-main);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }

        .filter-select-wrapper {
            position: relative;
            min-width: 160px;
        }

        .filter-select {
            width: 100%;
            padding: 10px 36px 10px 14px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            outline: none;
            background-color: #ffffff;
            color: var(--text-main);
            appearance: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-select:focus {
            border-color: var(--text-main);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08);
        }

        .select-chevron {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            width: 16px;
            height: 16px;
        }

        .filter-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Tombol Aksi */
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* Desain Tabel Modern */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .procurement-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        .procurement-table th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
        }

        .procurement-table td {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: #334155;
        }

        .procurement-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .procurement-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .procurement-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Kustomisasi Kolom */
        .col-id {
            font-weight: 500;
            color: var(--text-muted);
        }

        .col-code {
            font-weight: 700;
            color: var(--text-main);
        }

        /* Kolom Creator Avatar */
        .user-avatar-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,0.12);
        }

        .user-name {
            font-weight: 500;
            color: #475569;
        }

        /* Badge Status Bulat (Dot) */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-sent {
            background-color: var(--color-sent-bg);
            color: var(--color-sent-text);
        }
        .status-sent .status-dot {
            background-color: var(--color-sent-dot);
        }

        .status-received {
            background-color: var(--color-received-bg);
            color: var(--color-received-text);
        }
        .status-received .status-dot {
            background-color: var(--color-received-dot);
        }

        .status-draft {
            background-color: var(--color-draft-bg);
            color: var(--color-draft-text);
        }
        .status-draft .status-dot {
            background-color: var(--color-draft-dot);
        }

        .status-rejected {
            background-color: var(--color-rejected-bg);
            color: var(--color-rejected-text);
        }
        .status-rejected .status-dot {
            background-color: var(--color-rejected-dot);
        }

        /* Desain Tombol Aksi */
        .btn-check {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--color-sent-bg);
            color: var(--color-sent-text);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(59, 130, 246, 0.15);
            transition: all 0.2s;
        }

        .btn-check:hover {
            background-color: #dbeafe;
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.05);
        }

        .btn-check svg {
            width: 14px;
            height: 14px;
        }

        .badge-received-check {
            display: inline-flex;
            align-items: center;
            background-color: #f1f5f9;
            color: #64748b;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }

        .text-muted-dash {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Baris Kosong */
        .empty-row td {
            text-align: center;
            padding: 40px 24px;
            color: var(--text-muted);
            font-size: 15px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 24px;
            color: var(--text-muted);
            font-size: 12px;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-card);
            margin-top: auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-left {
                flex-direction: column;
                align-items: stretch;
                max-width: 100%;
            }
            .filter-select-wrapper {
                width: 100%;
            }
            .filter-right {
                justify-content: flex-end;
            }
            .container {
                padding: 16px 12px;
            }
            .top-navbar {
                padding: 12px 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Top Navbar modern (Figma Menu & Profile) -->
    <nav class="top-navbar">
        <div class="navbar-left">
            <button class="menu-toggle" aria-label="Toggle Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="breadcrumb">
                    <strong>DealerLink</strong> /
                    <span>Procurement</span> /
                    <span class="active">Pengadaan Kendaraan</span>
            </div>
        </div>
        <div class="navbar-right">
            <button class="notification-btn" aria-label="Notifications">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span class="notification-badge"></span>
            </button>
            <div class="user-profile-btn">DL</div>
        </div>
    </nav>
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

    <!-- Main Container -->
    <main class="container">
        
        <!-- Header Section -->
        <section class="header-section">
            <h1>Riwayat Pengadaan Kendaraan</h1>
            <p>Berikut adalah seluruh data permintaan pengadaan kendaraan. Silakan pilih pengadaan yang berstatus <strong>sent</strong> untuk merekam penerimaan barang dari pabrik.</p>
        </section>

        <!-- Summary Cards Grid -->
        <section class="summary-grid">
            <!-- Total Card -->
            <div class="summary-card card-total">
                <div class="summary-card-content">
                    <div class="summary-info">
                        <span class="summary-number"><?= $total_count ?></span>
                        <span class="summary-label">Total Pengadaan</span>
                    </div>
                </div>
            </div>

            <!-- Sent Card -->
            <div class="summary-card card-sent">
                <div class="summary-card-content">
                    <div class="summary-info">
                        <span class="summary-number"><?= $sent_count ?></span>
                        <span class="summary-label">Sent</span>
                    </div>
                </div>
            </div>

            <!-- Received Card -->
            <div class="summary-card card-received">
                <div class="summary-card-content">
                    <div class="summary-info">
                        <span class="summary-number"><?= $received_count ?></span>
                        <span class="summary-label">Received</span>
                    </div>
                </div>
            </div>

            <!-- Draft Card -->
            <div class="summary-card card-draft">
                <div class="summary-card-content">
                    <div class="summary-info">
                        <span class="summary-number"><?= $draft_count ?></span>
                        <span class="summary-label">Draft</span>
                    </div>
                </div>
            </div>

            <!-- Rejected Card -->
            <div class="summary-card card-rejected">
                <div class="summary-card-content">
                    <div class="summary-info">
                        <span class="summary-number"><?= $rejected_count ?></span>
                        <span class="summary-label">Rejected</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Table Card Container -->
        <section class="content-card">
            
            <!-- Filters & Controls Bar -->
            <div class="filter-bar">
                <div class="filter-left">
                    <!-- Search Input -->
                    <div class="search-wrapper">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="search-input" class="search-input" placeholder="Cari kode permintaan...">
                    </div>

                    <!-- Dropdown Status Filter -->
                    <div class="filter-select-wrapper">
                        <select id="status-filter" class="filter-select">
                            <option value="semua">Semua Status</option>
                            <option value="sent">Sent</option>
                            <option value="received">Received</option>
                            <option value="draft">Draft</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <svg class="select-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                </div>

                <!-- Back button inside toolbar -->
                <div class="filter-right">
                    <a href="/" class="btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>

            <!-- Table Responsive -->
            <div class="table-responsive">
                <table class="procurement-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode Permintaan</th>
                            <th>ID Pembuat</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($procurements)): ?>
                            <tr class="empty-row">
                                <td colspan="6">
    <div style="padding:20px;">
        📋 Belum ada data pengadaan kendaraan.
    </div>
</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($procurements as $p): ?>
                                <tr>
                                    <td class="col-id"><?= htmlspecialchars($p['id']) ?></td>
                                    <td class="col-code"><?= htmlspecialchars($p['request_code']) ?></td>
                                    <td class="col-creator">
                                        <?php
                                            $requested_by = $p['requested_by'] ?? '';
                                            // Ekstraksi angka & huruf untuk visual inisial avatar (seperti USR-1042 -> U1042)
                                            preg_match('/\d+/', $requested_by, $matches);
                                            $number = isset($matches[0]) ? $matches[0] : '';
                                            $first_letter = strtoupper(substr($requested_by, 0, 1));
                                            if (empty($first_letter)) { $first_letter = 'U'; }
                                            $circle_text = $first_letter . $number;
                                            if (strlen($circle_text) > 5) { $circle_text = substr($circle_text, 0, 5); }
                                            
                                            // Warna avatar dinamis premium yang serasi
                                            $hash_color = '#3b82f6';
                                            if (!empty($number)) {
                                                $color_index = intval($number) % 5;
                                                $avatar_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'];
                                                $hash_color = $avatar_colors[$color_index];
                                            }
                                        ?>
                                        <div class="user-avatar-container">
                                            <span class="user-avatar" style="background-color: <?= $hash_color ?>;"><?= htmlspecialchars($circle_text) ?></span>
                                            <span class="user-name"><?= htmlspecialchars($p['requested_by']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            $status_clean = strtolower($p['status'] ?? 'draft');
                                            $status_label = ucfirst($status_clean);
                                        ?>
                                        <span class="status-badge status-<?= $status_clean ?>">
                                            <span class="status-dot"></span>
                                            <?= $status_label ?>
                                        </span>
                                    </td>
                                    <td class="col-date">
    <?= date('d M Y H:i', strtotime($p['created_at'])) ?>
</td>
                                    <td>
                                        <?php if (($p['status'] ?? '') === 'sent'): ?>
                                            <a href="/procurement/receipt/<?= htmlspecialchars($p['id']) ?>" class="btn-check">
                                                <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                    <polyline points="10 9 9 9 8 9"></polyline>
                                                </svg>
                                                Lakukan Pengecekan
                                            </a>
                                        <?php elseif (($p['status'] ?? '') === 'received'): ?>
                                            <span class="badge-received-check">Sudah Diterima</span>
                                        <?php else: ?>
                                            <span class="text-muted-dash">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Client-side Interactive Filter & Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const tableRows = document.querySelectorAll('.procurement-table tbody tr:not(.empty-row)');
            const emptyRow = document.querySelector('.empty-row');
            
            function filterTable() {
                const query = searchInput.value.toLowerCase().trim();
                const selectedStatus = statusFilter.value.toLowerCase().trim();
                let visibleCount = 0;
                
                tableRows.forEach(row => {
                    const codeText = row.querySelector('.col-code')?.textContent.toLowerCase() || '';
                    const creatorText = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
                    const statusText = row.querySelector('.status-badge')?.className.toLowerCase() || '';
                    
                    const matchesSearch = codeText.includes(query) || creatorText.includes(query);
                    const matchesStatus = (selectedStatus === 'semua' || selectedStatus === '') || statusText.includes('status-' + selectedStatus);
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Tampilkan pesan kosong jika tidak ada baris yang memenuhi kriteria pencarian/filter
                if (emptyRow) {
                    if (visibleCount === 0) {
                        emptyRow.style.display = '';
                        emptyRow.querySelector('td').textContent = 'Tidak ada data pengadaan yang sesuai filter.';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                } else if (visibleCount === 0) {
                    const tbody = document.querySelector('.procurement-table tbody');
                    const newEmptyRow = document.createElement('tr');
                    newEmptyRow.className = 'empty-row';
                    newEmptyRow.innerHTML = '<td colspan="6">Tidak ada data pengadaan yang sesuai filter.</td>';
                    tbody.appendChild(newEmptyRow);
                } else {
                    const tempEmptyRow = document.querySelector('.procurement-table tbody .empty-row');
                    if (tempEmptyRow) tempEmptyRow.remove();
                }
            }
            
            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (statusFilter) statusFilter.addEventListener('change', filterTable);
        });
    </script>
</body>
</html>