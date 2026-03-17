<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? $site_name); ?> - Apartman Yönetimi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>public/assets/css/professional.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --primary-bg: #4361ee;
            --secondary-bg: #4895ef;
            --dark-bg: #1e1e2d;
            --light-bg: #f5f7fb;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--light-bg); overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--dark-bg); color: #fff; z-index: 1000; overflow-x: hidden; display: flex; flex-direction: column; transition: all 0.3s ease; }
        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        .sidebar-header { 
            padding: 20px 15px; 
            font-size: 1.15rem; 
            font-weight: 700; 
            color: #fff; 
            display: flex; 
            align-items: center; 
            gap: 10px;
            flex-shrink: 0;
            line-height: 1.2;
            overflow: hidden;
        }
        .sidebar-header i { font-size: 1.4rem; flex-shrink: 0; color: var(--primary-bg); }
        .sidebar.collapsed .sidebar-header span { display: none; }
        .sidebar.collapsed .sidebar-footer { display: none; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 25px; font-weight: 500; border-left: 3px solid transparent; white-space: nowrap; display: flex; align-items: center; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.05); border-left: 3px solid var(--primary-bg); }
        .sidebar .nav-link i { margin-right: 15px; width: 20px; font-size: 1.1rem; }
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar.collapsed .nav-link i { margin-right: 0; margin-left: auto; margin-right: auto; }
        .main-content { margin-left: var(--sidebar-width); padding: 25px; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content.expanded { margin-left: var(--sidebar-collapsed-width); }
        .navbar { background: #fff; box-shadow: 0 2px 15px rgba(0,0,0,0.05); margin-bottom: 30px; border-radius: 15px; }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); margin-bottom: 25px; }
        .stat-card {  }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        #sidebarToggle { cursor: pointer; padding: 5px 10px; border-radius: 8px; color: var(--primary-bg); background: var(--light-bg); }
        #sidebarToggle:hover { background: var(--primary-bg); color: #fff; }

        /* Mobile Adjustments & Global Enhancements */
        @media (max-width: 991.98px) {
            :root {
                --sidebar-width: 280px;
            }
            body { font-size: 14px; }
            .sidebar { 
                left: -100%; 
                box-shadow: 10px 0 20px rgba(0,0,0,0.1); 
                background: #0f172a !important; 
                border-right: 1px solid rgba(255,255,255,0.1);
            }
            .sidebar.mobile-show { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; backdrop-filter: blur(2px); }
            .sidebar-overlay.active { display: block; }
            .navbar { margin-bottom: 20px; border-radius: 12px; padding: 12px 15px !important; }
            .navbar-text { font-size: 0.9rem !important; }
            
            /* Table to Card Transformation */
            .table-responsive { border: none; }
            .table thead { display: none; } /* Hide headers on mobile */
            .table tbody tr { 
                display: block; 
                background: #fff; 
                border-radius: 12px; 
                margin-bottom: 15px; 
                padding: 15px; 
                box-shadow: 0 4px 10px rgba(0,0,0,0.03);
                border: 1px solid rgba(0,0,0,0.05);
            }
            .table tbody td { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                padding: 8px 0 !important; 
                border: none;
                border-bottom: 1px solid rgba(0,0,0,0.03);
                text-align: right;
                width: 100%;
            }
            .table tbody td:last-child { border-bottom: none; }
            .table tbody td::before { 
                content: attr(data-label); 
                font-weight: 700; 
                text-align: left; 
                color: var(--dark-bg);
                font-size: 0.8rem;
                text-transform: uppercase;
                opacity: 0.7;
            }
            .table tbody td .btn-group, .table tbody td .btn { 
                margin-left: auto; 
            }

            /* Dashboard Specific */
            .stat-card { border-radius: 12px; }
            .stat-card .card-body { padding: 15px; }
            
            /* Buttons & Forms */
            .btn { padding: 10px 18px; border-radius: 10px; font-weight: 600; }
            .modal-content { border-radius: 20px; }
            .form-control, .form-select { padding: 12px; border-radius: 10px; }
            
            /* Headers */
            h2 { font-size: 1.5rem; }
            .card-title { font-size: 1.1rem; }
        }

        /* Glassmorphism Effect for certain elements */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-building-user text-primary-emphasis me-2"></i> <span><?php echo e($site_name); ?></span>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>dashboard" class="nav-link"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>block" class="nav-link"><i class="fas fa-cubes"></i> <span>Blok Yönetimi</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>apartment" class="nav-link"><i class="fas fa-door-open"></i> <span>Daireler</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>resident" class="nav-link"><i class="fas fa-users"></i> <span>Site Sakinleri</span></a></li>
            <hr class="mx-3 opacity-25">
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>due" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> <span>Aidat Aç</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>extracharge" class="nav-link"><i class="fas fa-plus-circle"></i> <span>Ekstra Borç</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>payment" class="nav-link"><i class="fas fa-credit-card"></i> <span>Ödeme Gir</span></a></li>
            <hr class="mx-3 opacity-25">
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>expense" class="nav-link"><i class="fas fa-receipt"></i> <span>Gider Yönetimi</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>report" class="nav-link"><i class="fas fa-file-alt"></i> <span>Raporlar</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>settings" class="nav-link"><i class="fas fa-cog"></i> <span>Genel Ayarlar</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>whatsapp" class="nav-link"><i class="fab fa-whatsapp"></i> <span>WhatsApp Ayarları</span></a></li>
            <li class="nav-item"><a href="<?php echo SITE_URL; ?>admin" class="nav-link"><i class="fas fa-user-shield"></i> <span>Yöneticiler</span></a></li>
            <li class="nav-item mt-auto"><a href="<?php echo SITE_URL; ?>auth/logout" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> <span>Çıkış Yap</span></a></li>
        </ul>
        <div class="sidebar-footer p-3 text-center border-top border-secondary border-opacity-25">
            <small class="text-white-50" style="font-size: 0.7rem;">Mümin Dönmez Tarafından tasarlanmıştır</small>
        </div>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg px-4 py-3">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <div id="sidebarToggle" class="me-3">
                        <i class="fas fa-bars fs-5"></i>
                    </div>
                    <span class="navbar-text fw-bold fs-5">Hoş geldin, <?php echo e($_SESSION['admin_user']); ?></span>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['admin_user']); ?>&background=4361ee&color=fff" class="rounded-circle" width="40" data-bs-toggle="dropdown" style="cursor:pointer">
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Ayarlar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>auth/logout"><i class="fas fa-sign-out-alt me-2"></i> Çıkış</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
