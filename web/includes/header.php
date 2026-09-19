<?php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
if (!isset($page_title)) {
    $page_title = "Phòng Khám Nha Khoa Hiện Đại - Chăm Sóc Nụ Cười Việt";
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
    <meta name="description" content="Phòng khám nha khoa hiện đại uy tín hàng đầu. Cung cấp các dịch vụ tẩy trắng, niềng răng, bọc sứ, implant không đau với đội ngũ bác sĩ chuyên khoa.">
    
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $depth; ?>assets/css/style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-clinic sticky-top">
        <div class="container">
            <a class="navbar-brand-logo" href="<?php echo $depth; ?>index.php">
                <i class="fas fa-tooth"></i>
                <span>NHA KHOA SMILE</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'services') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/services.php">Dịch vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'doctors') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/doctors.php">Đội ngũ bác sĩ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'booking') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/booking.php">Đặt lịch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($current_page) && $current_page == 'contact') ? 'active' : ''; ?>" href="<?php echo $depth; ?>pages/contact.php">Liên hệ</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-2">
                    <a href="<?php echo $depth; ?>pages/booking.php" class="btn btn-primary-clinic">
                        <i class="far fa-calendar-alt"></i> Đặt lịch ngay
                    </a>
                </div>
            </div>
        </div>
    </nav>
