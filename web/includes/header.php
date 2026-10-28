<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

// Tự động đồng bộ full_name mới nhất từ CSDL vào Session (Khắc phục triệt để lỗi font của session cũ)
if (!empty($_SESSION['logged_in']) && !empty($_SESSION['username'])) {
    try {
        $pdoSync = getDB();
        if ($pdoSync) {
            $stmtSync = $pdoSync->prepare("SELECT full_name, email, phone FROM users WHERE username = ?");
            $stmtSync->execute([$_SESSION['username']]);
            $userFresh = $stmtSync->fetch(PDO::FETCH_ASSOC);
            if ($userFresh && !empty($userFresh['full_name'])) {
                $_SESSION['full_name'] = $userFresh['full_name'];
                if (!empty($userFresh['phone'])) $_SESSION['phone'] = $userFresh['phone'];
                if (!empty($userFresh['email'])) $_SESSION['email'] = $userFresh['email'];
            }
        }
    } catch (\Throwable $e) {}
}

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
if (!isset($page_title)) {
    $page_title = "Nha Khoa Quốc Tế Premier - Chăm Sóc Nụ Cười, Kiến Tạo Tự Tin";
}

// Calculate base path for assets & pages depending on directory depth
$depth = isset($is_page) && $is_page ? '../' : './';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="Nha Khoa Quốc Tế Premier - Hệ thống nha khoa công nghệ cao hàng đầu Việt Nam. Khám răng, Niềng răng, Implant, Răng sứ thẩm mỹ chuẩn quốc tế ISO 9001.">
    
    <!-- Google Fonts for Full Vietnamese Typography Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 Pro / Free -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Design System CSS with Cache-Busting Version Query -->
    <link rel="stylesheet" href="<?php echo $depth; ?>assets/css/style.css?v=<?php echo time(); ?>">

    <!-- Critical Inlined Safety Styles (Prevents FOUC & Broken Links) -->
    <style>
        .brand-logo-custom {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            text-decoration: none !important;
            flex-shrink: 0 !important;
            white-space: nowrap !important;
            margin-right: 36px !important;
        }
        @media (min-width: 1400px) {
            .brand-logo-custom {
                margin-right: 48px !important;
            }
        }
        @media (max-width: 1280px) and (min-width: 992px) {
            .brand-logo-custom {
                margin-right: 22px !important;
                gap: 8px !important;
            }
            .brand-logo-title {
                font-size: 1.12rem !important;
            }
            .navbar-clinic .nav-link {
                padding: 6px 9px !important;
                font-size: 0.85rem !important;
            }
        }
        @media (max-width: 991.98px) {
            .brand-logo-custom {
                margin-right: auto !important;
            }
        }
        .brand-logo-custom:hover {
            text-decoration: none !important;
        }
        .brand-logo-icon {
            width: 40px !important;
            height: 40px !important;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #ffffff !important;
            font-size: 1.25rem !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28) !important;
            flex-shrink: 0 !important;
            transition: all 0.25s ease !important;
        }
        .brand-logo-custom:hover .brand-logo-icon {
            transform: scale(1.05) rotate(4deg) !important;
            box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4) !important;
        }
        .brand-logo-text {
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }
        .brand-logo-title {
            display: block !important;
            font-size: 1.2rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.01em !important;
            color: #0c4a6e !important;
            line-height: 1.1 !important;
            white-space: nowrap !important;
            text-decoration: none !important;
        }
        .brand-logo-title span {
            color: #0284c7 !important;
        }
        .brand-logo-subtitle {
            display: block !important;
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 1.2px !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            margin-top: 2px !important;
        }
        .top-bar-custom {
            background: #071527 !important;
            color: #e2e8f0 !important;
        }
        .top-bar-custom a {
            color: #e2e8f0 !important;
            text-decoration: none !important;
        }
        .navbar-clinic .nav-link {
            text-decoration: none !important;
        }
        .page-header-light {
            background: linear-gradient(180deg, #e0f2fe 0%, #f8fafc 100%) !important;
            border-bottom: 1px solid #bae6fd !important;
            padding: 55px 0 50px !important;
            position: relative !important;
        }
        .page-header-title {
            color: #0f172a !important;
            font-size: 2.75rem !important;
            font-weight: 800 !important;
            margin-bottom: 10px !important;
            letter-spacing: -0.025em !important;
            line-height: 1.2 !important;
        }
        .page-header-desc {
            color: #475569 !important;
            font-size: 1.125rem !important;
            max-width: 680px !important;
            margin: 0 auto !important;
            line-height: 1.65 !important;
        }
        .badge-page-tag {
            background: #ffffff !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd !important;
            font-weight: 700 !important;
            padding: 6px 18px !important;
            border-radius: 9999px !important;
            font-size: 0.82rem !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.12) !important;
        }
    </style>
</head>
<body>

    <!-- TOP INFORMATION BAR -->
    <div class="top-bar-custom d-none d-md-block">
        <div class="container-fluid px-3 px-xl-5" style="max-width: 1440px;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-4">
                    <span><i class="fas fa-map-marker-alt text-info me-2"></i> 123 Nguyễn Văn Cừ, Quận 5, TP.HCM</span>
                    <span><i class="far fa-clock text-info me-2"></i> 08:00 - 20:00 (Tất cả các ngày trong tuần)</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="tel:19006868" class="fw-bold"><i class="fas fa-phone-volume text-info me-1"></i> Hotline: 1900 6868</a>
                    <a href="tel:0900123456" class="top-emergency-badge"><i class="fas fa-bolt"></i> Cấp Cứu 24/7: 0900.123.456</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-clinic sticky-top">
        <div class="container-fluid px-3 px-xl-5" style="max-width: 1440px;">
            <a class="brand-logo-custom me-3 me-lg-4 me-xl-5" href="<?php echo $depth; ?>index.php">
                <div class="brand-logo-icon">
                    <i class="fas fa-tooth"></i>
                </div>
                <div class="brand-logo-text">
                    <span class="brand-logo-title">PREMIER <span>DENTAL</span></span>
                    <span class="brand-logo-subtitle">Nha Khoa Quốc Tế</span>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars fa-lg text-primary"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'home') ? 'active' : ''; ?>" href="<?php echo $depth; ?>index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'about') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'services') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/services.php">Dịch vụ & Bảng giá</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'doctors') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/doctors.php">Đội ngũ bác sĩ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'my_appointments') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/my_appointments.php">
                            <i class="far fa-calendar-check me-1"></i> Lịch hẹn của tôi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'contact') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/contact.php">Liên hệ</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <div class="dropdown">
                            <button class="user-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-profile-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="user-profile-name"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                    <span class="user-profile-role badge-admin">Admin</span>
                                <?php else: ?>
                                    <span class="user-profile-role badge-patient">Bệnh nhân</span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 rounded-4 p-2">
                                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                    <li><a class="dropdown-item py-2 rounded-3" href="<?php echo $depth; ?>admin/dashboard.php"><i class="fas fa-chart-line text-primary me-2"></i> Bảng điều khiển Admin</a></li>
                                    <li><a class="dropdown-item py-2 rounded-3" href="<?php echo $depth; ?>admin/appointments.php"><i class="far fa-calendar-check text-success me-2"></i> Quản lý Lịch hẹn</a></li>
                                    <li><a class="dropdown-item py-2 rounded-3" href="<?php echo $depth; ?>admin/patients.php"><i class="fas fa-users text-info me-2"></i> Quản lý Bệnh nhân</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item py-2 rounded-3" href="<?php echo $depth; ?>pages/my_appointments.php"><i class="far fa-calendar-check text-primary me-2"></i> Lịch hẹn của tôi</a></li>
                                    <li><a class="dropdown-item py-2 rounded-3" href="<?php echo $depth; ?>pages/booking.php"><i class="far fa-calendar-plus text-success me-2"></i> Đặt lịch hẹn mới</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li><a class="dropdown-item py-2 rounded-3 text-danger" href="<?php echo $depth; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $depth; ?>login.php?tab=login" class="btn-nav-login">
                            <i class="far fa-user me-1"></i> Đăng nhập
                        </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo $depth; ?>pages/booking.php" class="btn-nav-booking">
                        <i class="far fa-calendar-alt"></i> <span>Đặt Lịch Khám</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
