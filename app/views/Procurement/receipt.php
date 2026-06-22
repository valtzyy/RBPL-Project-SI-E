<?php
/**
 * receipt.php - Halaman Pencatatan Penerimaan Unit Kendaraan
 * Desain diselaraskan 100% dengan form.php Procurement DealerLink
 */

// Fallback data untuk kemudahan testing jika halaman diakses secara langsung
$procurement = $procurement ?? [
    'id' => '25',
    'request_code' => 'PRQ-2024-001',
    'status' => 'Sent',
    'shipping_date' => '01 Juni 2024',
    'supplier' => 'PT Astra International'
];

$details = $details ?? [
    [
        'vehicle_id' => '1',
        'brand' => 'Toyota',
        'type' => 'Raize GR Sport',
        'color' => 'Midnight Black',
        'quantity' => '10'
    ],
    [
        'vehicle_id' => '2',
        'brand' => 'Honda',
        'type' => 'HR-V SE',
        'color' => 'Lunar Silver',
        'quantity' => '5'
    ],
    [
        'vehicle_id' => '3',
        'brand' => 'Hyundai',
        'type' => 'Stargazer',
        'color' => 'Abu-Abu',
        'quantity' => '3'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Penerimaan Unit - DealerLink</title>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Design System & Color Tokens form.php */
        :root {
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --color-primary: #2563eb;
            --color-primary-hover: #1d4ed8;
            --color-border: #e2e8f0;
            --color-bg-main: #f4f6f9;
            --color-bg-card: #ffffff;
            --color-text-main: #1e293b;
            --color-text-muted: #64748b;
            
            /* Sidebar Tokens */
            --sidebar-bg: #eef2ff;
            --sidebar-active-bg: #e0e7ff;
            --sidebar-active-color: #2563eb;
            --sidebar-width: 260px;
            
            /* Badge Status Colors */
            --status-sent-bg: #fef3c7;
            --status-sent-text: #d97706;
            --status-sent-dot: #f59e0b;
            
            --status-received-bg: #d1fae5;
            --status-received-text: #059669;
            --status-received-dot: #10b981;
            
            --status-rejected-bg: #fee2e2;
            --status-rejected-text: #dc2626;
            --status-rejected-dot: #ef4444;
            
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        }

        /* Basic Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--color-bg-main);
            color: var(--color-text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar - Identik dengan form.php */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--color-border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 24px 16px;
            transition: transform 0.3s ease;
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
            flex-grow: 1;
            min-width: 0;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-role {
            font-size: 12px;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: var(--radius-lg);
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .menu-item a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .menu-item a:hover {
            background-color: rgba(255, 255, 255, 0.5);
            color: var(--color-text-main);
        }

        .menu-item.active a {
            background-color: var(--sidebar-active-bg);
            color: var(--sidebar-active-color);
        }

        .sidebar-footer {
            font-size: 11px;
            color: #94a3b8;
            padding-top: 16px;
            border-top: 1px solid var(--color-border);
            text-align: center;
            font-weight: 500;
        }

        /* Layout Container */
        .layout-container {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-bottom: 120px;
            transition: margin-left 0.3s ease;
        }

        /* Header - Identik dengan form.php */
.header {
    height: 70px;
    background-color: #ffffff;
    border-bottom: 1px solid var(--color-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 32px;
}

        .header-title-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--color-text-main);
            cursor: pointer;
        }

        .header-module-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        .notification-btn {
            background: none;
            border: 1px solid var(--color-border);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            cursor: pointer;
            position: relative;
            transition: background-color 0.2s ease;
        }

        .notification-btn:hover {
            background-color: #f1f5f9;
        }

        .notification-dot {
            position: absolute;
            top: 10px;
            right: 11px;
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }

        /* Main Workspace Content */
        .main-content {
            padding: 32px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Breadcrumbs */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--color-text-muted);
            margin-bottom: 16px;
            font-weight: 600;
        }

        .breadcrumb a {
            color: var(--color-text-muted);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: var(--color-primary);
        }

        .breadcrumb-separator {
            color: #cbd5e1;
        }

        .breadcrumb-active {
            color: var(--color-text-main);
        }

        /* Page Headers */
        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-text-main);
            margin-bottom: 6px;
        }
        
        .page-title-main {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
}

        .page-description {
            font-size: 14px;
            color: var(--color-text-muted);
        }

        /* Information Card */
        .info-card {
            background-color: var(--color-bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-card-icon {
            color: var(--color-primary);
            font-size: 18px;
        }

        .info-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .info-item-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--color-text-muted);
            margin-bottom: 4px;
            font-weight: 700;
        }

        .info-item-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        /* Modern Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-sent {
            background-color: var(--status-sent-bg);
            color: var(--status-sent-text);
        }
        .status-sent::before { background-color: var(--status-sent-dot); }

        .status-received {
            background-color: var(--status-received-bg);
            color: var(--status-received-text);
        }
        .status-received::before { background-color: var(--status-received-dot); }

        .status-rejected {
            background-color: var(--status-rejected-bg);
            color: var(--status-rejected-text);
        }
        .status-rejected::before { background-color: var(--status-rejected-dot); }
        .status-draft {
    background-color: #f1f5f9;
    color: #475569;
}

.status-draft::before {
    background-color: #94a3b8;
}

        /* Workspace Grid (2 Columns Layout) */
        .workspace-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            align-items: start;
        }

        .list-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .list-section-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        .fill-status-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--status-received-text);
            background-color: var(--status-received-bg);
            padding: 4px 10px;
            border-radius: 20px;
        }

        .fill-status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--status-received-dot);
        }

        .vehicle-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Vehicle Row/Card */
        .vehicle-card {
            background-color: var(--color-bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border);
            padding: 16px;
            display: grid;
            grid-template-columns: 30px 40px 2fr 1fr 1fr 1.2fr 1.2fr;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .vehicle-card:hover {
            box-shadow: var(--shadow-md);
            border-color: #cbd5e1;
        }

        .vehicle-index {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-text-muted);
        }

        .vehicle-icon-wrapper {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: var(--color-text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .vehicle-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        .vehicle-label-mobile {
            display: none;
            font-size: 10px;
            font-weight: 700;
            color: var(--color-text-muted);
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .vehicle-color-tag {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            text-align: center;
            display: inline-block;
        }

        .vehicle-qty-expected {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text-main);
            text-align: center;
        }

        /* Modern input 44px, border radius 10px, border #e2e8f0, focus state blue */
        .vehicle-qty-input {
            width: 100%;
            height: 44px;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            padding: 0 12px;
            font-family: var(--font-family);
            font-size: 15px;
            font-weight: 700;
            color: var(--color-text-main);
            text-align: center;
            outline: none;
            transition: all 0.2s ease;
        }

        .vehicle-qty-input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Dynamic classes from JS */
        .qty-match {
            border-color: #10b981 !important;
            background-color: #ecfdf5;
            color: #065f46;
        }

        .qty-mismatch {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
            color: #991b1b;
        }

        /* Validation Badge Row */
        .validation-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .val-success {
            background-color: var(--status-received-bg);
            color: var(--status-received-text);
        }

        .val-danger {
            background-color: var(--status-rejected-bg);
            color: var(--status-rejected-text);
        }

        /* Summary Validation Card */
        .summary-card {
            background-color: var(--color-bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--color-border);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 94px;
        }

        .summary-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--color-text-main);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-label {
            font-size: 13px;
            color: var(--color-text-muted);
            font-weight: 600;
        }

        .summary-val {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text-main);
        }

        .summary-val-highlight {
            font-size: 18px;
            font-weight: 800;
        }

        .summary-divider {
            height: 1px;
            background-color: #f1f5f9;
        }

        /* Validation status badge */
        .summary-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            width: 100%;
            justify-content: center;
            margin-top: 8px;
        }

        .status-verified {
            background-color: var(--status-received-bg);
            color: var(--status-received-text);
            border: 1px solid #a7f3d0;
        }

        .status-unverified {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        /* Bottom Fixed Action Bar */
        .bottom-action-bar {
            position: fixed;
            bottom: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 80px;
            background-color: #ffffff;
            border-top: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 95;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
            transition: left 0.3s ease;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: 46px;
            padding: 0 20px;
            border-radius: 8px;
            font-family: var(--font-family);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--color-text-main);
            border: 1px solid var(--color-border);
        }

        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-primary {
    background-color: #000000;
    color: #ffffff;
}

.btn-primary:hover {
    background-color: #1e293b;
}


        .action-status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text-muted);
        }

        .action-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Desktop Column Headers (Grid Mode) */
        .grid-header-row {
            display: grid;
            grid-template-columns: 30px 40px 2fr 1fr 1fr 1.2fr 1.2fr;
            gap: 16px;
            padding: 0 16px 10px 16px;
            border-bottom: 1px solid var(--color-border);
            margin-bottom: 8px;
        }

        .grid-header-item {
            font-size: 11px;
            font-weight: 700;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .grid-header-item.center {
            text-align: center;
        }

        /* Sidebar Overlay Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.3);
            backdrop-filter: blur(4px);
            z-index: 99;
        }

        /* Responsive Layouts */
        @media (max-width: 1200px) {
            .workspace-grid {
                grid-template-columns: 1fr;
            }
            .summary-card {
                position: static;
                width: 100%;
            }
        }

        @media (max-width: 992px) {
            :root {
                --sidebar-width: 0px;
            }
            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .sidebar-overlay.active {
                display: block;
            }
            .layout-container {
                margin-left: 0;
            }
            .bottom-action-bar {
                left: 0;
            }
            .mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px 16px;
            }
            .top-header {
                padding: 0 16px;
            }
            .grid-header-row {
                display: none;
            }
            .vehicle-card {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                padding: 16px;
            }
            .vehicle-index {
                grid-column: span 2;
                font-size: 11px;
                border-bottom: 1px dashed var(--color-border);
                padding-bottom: 8px;
            }
            .vehicle-icon-wrapper {
                display: none;
            }
            .vehicle-info {
                grid-column: span 2;
            }
            .vehicle-color-wrapper {
                grid-column: 1;
            }
            .vehicle-qty-wrapper {
                grid-column: 2;
                text-align: right;
            }
            .vehicle-qty-expected {
                text-align: right;
            }
            .vehicle-input-wrapper {
                grid-column: 1;
            }
            .vehicle-validation-wrapper {
                grid-column: 2;
                display: flex;
                justify-content: flex-end;
            }
            .vehicle-label-mobile {
                display: block;
            }
            .bottom-action-bar {
                flex-direction: column;
                height: auto;
                padding: 16px;
                gap: 12px;
            }
            .action-status-indicator {
                order: -1;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
            .layout-container {
                padding-bottom: 160px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar Kiri - MENYESUAIKAN 100% form.php -->
    <aside class="sidebar" id="sidebarMenu">
        <div class="logo-container">
    <div class="logo-text">DealerLink</div>
</div>
        
        <!-- Profile Card dengan foto placeholder/Unsplash & Role "Manager Operasional" -->
        <div class="profile-card">
            <img class="profile-img" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Manager Operasional">
            <div class="profile-info">
                <div class="profile-name">Admin Dealer</div>
                <div class="profile-role">Manager Operasional</div>
            </div>
        </div>

        <!-- Menu Navigation -->
        <nav style="flex-grow: 1;">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="#"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
                </li>
                <li class="menu-item">
                    <a href="#"><i class="fa-solid fa-car"></i> Inventaris</a>
                </li>
                <li class="menu-item active">
                    <a href="#"><i class="fa-solid fa-basket-shopping"></i> Procurement</a>
                </li>
                <li class="menu-item">
                    <a href="#"><i class="fa-solid fa-file-invoice"></i> Sales SPK</a>
                </li>
                <li class="menu-item">
                    <a href="#"><i class="fa-solid fa-wrench"></i> Layanan Servis</a>
                </li>
                <li class="menu-item">
                    <a href="#"><i class="fa-solid fa-file-lines"></i> Laporan</a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Layout Container -->
    <div class="layout-container">
        
        <!-- Header - Identik dengan form.php (Height 70px, Title "Procurement") -->
        <header class="header">
            <div class="header-title-section">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="page-title-main">Procurement</div>
            </div>
            
            <button class="notification-btn">
                <i class="fa-regular fa-bell"></i>
                <span class="notification-dot"></span>
            </button>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Breadcrumbs -->
            <div class="breadcrumb">
                <a href="#">Procurement</a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-active">Receipt</span>
            </div>

            <!-- Page Title -->
            <div class="page-header">
                <h1 class="page-title">Pencatatan Penerimaan Unit</h1>
                <p class="page-description">Cocokkan jumlah kendaraan yang diterima dari pabrik dengan jumlah yang tercatat pada permintaan pengadaan.</p>
            </div>

            <!-- Formulir Terintegrasi -->
            <form method="POST" action="/procurement/receipt/store" id="receiptForm">
                <!-- Hidden input untuk procurement_id -->
                <input type="hidden" name="procurement_id" value="<?= htmlspecialchars($procurement['id']) ?>">

                <!-- Card Informasi Pengadaan -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fa-solid fa-circle-info info-card-icon"></i>
                        <h2 class="info-card-title">Informasi Pengadaan</h2>
                    </div>
                    <div class="info-grid">
                        <div>
                            <div class="info-item-label">Kode Permintaan</div>
                            <div class="info-item-value"><?= htmlspecialchars($procurement['request_code']) ?></div>
                        </div>
                        <div>
                            <div class="info-item-label">ID Pengadaan</div>
                            <div class="info-item-value"><?= htmlspecialchars($procurement['id']) ?></div>
                        </div>
                        <div>
                            <div class="info-item-label">Tanggal Pengiriman</div>
                            <div class="info-item-value"><?= htmlspecialchars($procurement['shipping_date'] ?? '01 Juni 2024') ?></div>
                        </div>
                        <div>
                            <div class="info-item-label">Supplier</div>
                            <div class="info-item-value"><?= htmlspecialchars($procurement['supplier'] ?? 'PT Astra International') ?></div>
                        </div>
                        <div>
                            <div class="info-item-label">Status</div>
                            <div>
                                <?php 
                                    $statusClass = 'status-draft';
                                    $statusLabel = htmlspecialchars($procurement['status']);
                                    switch (strtolower($procurement['status'])) {
                                        case 'sent':
                                            $statusClass = 'status-sent';
                                            break;
                                        case 'received':
                                            $statusClass = 'status-received';
                                            break;
                                        case 'rejected':
                                            $statusClass = 'status-rejected';
                                            break;
                                    }
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workspace Grid (2 Columns Layout) -->
                <div class="workspace-grid">
                    
                    <!-- Left: Vehicle Card List -->
                    <div>
                        <div class="list-section-header">
                            <h3 class="list-section-title">Detail Kendaraan</h3>
                            <div class="fill-status-badge">
                                <span class="fill-status-dot"></span>
                                <span>Semua terisi</span>
                            </div>
                        </div>

                        <!-- Header Kolom Desktop (Hidden di Mobile) -->
                        <div class="grid-header-row">
                            <div class="grid-header-item">#</div>
                            <div class="grid-header-item"></div>
                            <div class="grid-header-item">Kendaraan</div>
                            <div class="grid-header-item">Warna</div>
                            <div class="grid-header-item center">Dipesan</div>
                            <div class="grid-header-item center">Diterima</div>
                            <div class="grid-header-item center">Status Validasi</div>
                        </div>

                        <!-- Card List -->
                        <div class="vehicle-list">
                            <?php $index = 1; foreach ($details as $detail): ?>
                                <div class="vehicle-card" data-expected="<?= (int)$detail['quantity'] ?>">
                                    <!-- Index -->
                                    <div class="vehicle-index">
                                        <?= str_pad($index++, 2, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    
                                    <!-- Icon -->
                                    <div class="vehicle-icon-wrapper">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                    
                                    <!-- Brand + Type -->
                                    <div class="vehicle-info">
                                        <span class="vehicle-label-mobile">Kendaraan</span>
                                        <div class="vehicle-name">
                                            <?= htmlspecialchars($detail['brand']) ?> 
                                            <?= htmlspecialchars($detail['type']) ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Color -->
                                    <div class="vehicle-color-wrapper">
                                        <span class="vehicle-label-mobile">Warna</span>
                                        <span class="vehicle-color-tag">
                                            <?= htmlspecialchars($detail['color']) ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Expected Quantity -->
                                    <div class="vehicle-qty-wrapper">
                                        <span class="vehicle-label-mobile">Jumlah Dipesan</span>
                                        <div class="vehicle-qty-expected">
                                            <?= htmlspecialchars($detail['quantity']) ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Input Actual Quantity -->
                                    <div class="vehicle-input-wrapper">
                                        <span class="vehicle-label-mobile">Jumlah Diterima</span>
                                        <input 
                                            type="number" 
                                            class="vehicle-qty-input"
                                            name="received_quantities[<?= htmlspecialchars($detail['vehicle_id']) ?>]" 
                                            min="0" 
                                            value="<?= htmlspecialchars($detail['quantity']) ?>" 
                                            required
                                        >
                                    </div>
                                    
                                    <!-- Status Validation Badge -->
                                    <div class="vehicle-validation-wrapper">
                                        <span class="vehicle-label-mobile">Status Validasi</span>
                                        <span class="validation-badge val-success">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Sesuai</span>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Right: Summary Validation Card -->
                    <aside class="summary-card">
                        <h3 class="summary-title">Ringkasan Validasi</h3>
                        <div class="summary-list">
                            <div class="summary-row">
                                <span class="summary-label">Total Model</span>
                                <span class="summary-val" id="summaryTotalModels"><?= count($details) ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Dipesan</span>
                                <span class="summary-val" id="summaryTotalExpected">0 Unit</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Total Diterima</span>
                                <span class="summary-val summary-val-highlight" id="summaryTotalReceived">0 Unit</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row">
                                <span class="summary-label">Selisih</span>
                                <span class="summary-val" id="summaryDifference">0 Unit</span>
                            </div>
                            
                            <!-- Dynamic Status Validasi Ringkasan -->
                            <div class="summary-status-badge status-verified" id="summaryStatusLabel">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Sesuai</span>
                            </div>
                        </div>
                    </aside>
                </div>

                <!-- Bottom Fixed Action Bar -->
                <div class="bottom-action-bar">
                    <!-- Left: Back Button -->
                    <a href="/" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Riwayat</span>
                    </a>
                    
                    <!-- Center/Right Info Dynamic Indicator -->
                    <div class="navbar-actions"
     style="display:flex;align-items:center;margin-left:auto;gap:24px;">
                        <div class="action-status-indicator" id="actionStatusIndicator">
                            <span class="action-dot" style="background-color: var(--status-received-dot);"></span>
                            <span id="actionIndicatorText">Semua Sesuai</span>
                        </div>
                        
                        <!-- Submit Button (Primary Blue from form.php) -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan & Validasi</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <!-- Interaktivitas & Perhitungan Otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Mobile Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarMenu = document.getElementById('sidebarMenu');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if(mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebarMenu.classList.add('active');
                    sidebarOverlay.classList.add('active');
                });
            }

            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebarMenu.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }

            // Realtime Validation & Calculations
            const vehicleCards = document.querySelectorAll('.vehicle-card');
            
            function calculateTotals() {
                let totalExpected = 0;
                let totalReceived = 0;
                let totalDiff = 0;

                vehicleCards.forEach(card => {
                    const expectedVal = parseInt(card.getAttribute('data-expected')) || 0;
                    const inputElement = card.querySelector('.vehicle-qty-input');
                    const receivedVal = parseInt(inputElement.value) || 0;
                    const valBadge = card.querySelector('.validation-badge');
                    
                    totalExpected += expectedVal;
                    totalReceived += receivedVal;

                    const cardDiff = receivedVal - expectedVal;
                    
                    if (cardDiff === 0) {
                        inputElement.className = 'vehicle-qty-input qty-match';
                        valBadge.className = 'validation-badge val-success';
                        valBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Sesuai</span>';
                    } else {
                        inputElement.className = 'vehicle-qty-input qty-mismatch';
                        valBadge.className = 'validation-badge val-danger';
                        const diffText = cardDiff > 0 ? `Lebih ${cardDiff} Unit` : `Kurang ${Math.abs(cardDiff)} Unit`;
                        valBadge.innerHTML = `<i class="fa-solid fa-circle-xmark"></i> <span>${diffText}</span>`;
                        totalDiff += Math.abs(cardDiff);
                    }
                });

                // Update Summary Card
                document.getElementById('summaryTotalExpected').textContent = `${totalExpected} Unit`;
                document.getElementById('summaryTotalReceived').textContent = `${totalReceived} Unit`;
                
                const finalDifference = totalReceived - totalExpected;
                const differenceLabel = document.getElementById('summaryDifference');
                const statusLabelCard = document.getElementById('summaryStatusLabel');
                const actionIndicator = document.getElementById('actionStatusIndicator');
                
                if (finalDifference === 0) {
                    differenceLabel.textContent = '0 Unit';
                    differenceLabel.style.color = 'var(--status-received-text)';
                    
                    statusLabelCard.className = 'summary-status-badge status-verified';
                    statusLabelCard.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Sesuai</span>';
                    
                    actionIndicator.innerHTML = '<span class="action-dot" style="background-color: var(--status-received-dot);"></span> <span id="actionIndicatorText">Semua Sesuai</span>';
                } else {
                    const diffText = finalDifference > 0 ? `Lebih ${finalDifference} unit` : `Selisih ${Math.abs(finalDifference)} unit`;
                    differenceLabel.textContent = `${finalDifference > 0 ? '+' : ''}${finalDifference} Unit`;
                    differenceLabel.style.color = '#d97706'; // Orange-yellow match color
                    
                    statusLabelCard.className = 'summary-status-badge status-unverified';
                    statusLabelCard.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <span>Perlu Verifikasi</span>';
                    
                    actionIndicator.innerHTML = `<span class="action-dot" style="background-color: var(--status-sent-dot);"></span> <span id="actionIndicatorText">${diffText}</span>`;
                }
            }

            // Bind input events
            vehicleCards.forEach(card => {
                const input = card.querySelector('.vehicle-qty-input');
                input.addEventListener('input', calculateTotals);
                input.addEventListener('change', calculateTotals);
            });

            // Initial calculation run
            calculateTotals();
        });
    </script>
</body>
</html>