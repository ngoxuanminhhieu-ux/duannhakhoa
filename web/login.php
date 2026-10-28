<?php
// web/login.php
// Trang Đăng nhập & Đăng ký Hợp nhất (Dành cho cả Khách hàng và Admin)

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth_helper.php';

// Đã đăng nhập rồi thì chuyển hướng
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

$errorMsg   = '';
$successMsg = '';

// Nhận thông báo từ GET query
if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out') {
    $successMsg = 'Bạn đã đăng xuất an toàn khỏi hệ thống.';
}

$activeTab  = isset($_GET['tab']) && $_GET['tab'] === 'register' ? 'register' : 'login';
$targetRole = isset($_GET['role']) && $_GET['role'] === 'admin' ? 'admin' : 'user';
$redirectParam = trim($_GET['redirect'] ?? '');

// Hỗ trợ điền nhanh tài khoản Demo khi click từ trang chủ
$isDemoUser = isset($_GET['demo']) && in_array($_GET['demo'], ['user', '1', 'patient']);
$defaultIdentity = $isDemoUser ? 'khachhang' : '';
$defaultPassword = $isDemoUser ? '123456' : '';
if ($isDemoUser) {
    $targetRole = 'user';
    $activeTab  = 'login';
    $successMsg = 'Đã tự động điền tài khoản Khách hàng Demo (khachhang / 123456). Bấm Đăng Nhập để trải nghiệm ngay!';
}

// Xử lý gửi Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = trim($_POST['form_action'] ?? 'login');

    if ($formAction === 'login') {
        $identity = trim($_POST['identity'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $roleType = trim($_POST['account_type'] ?? 'user');

        $result = attempt_login($identity, $password, $roleType);

        if ($result['success']) {
            $dest = !empty($redirectParam) ? $redirectParam : $result['redirect'];
            header("Location: " . $dest);
            exit;
        } else {
            $errorMsg = $result['error'];
            $activeTab = 'login';
            $targetRole = $roleType;
        }
    } elseif ($formAction === 'register') {
        $fullName = trim($_POST['reg_fullname'] ?? '');
        $phone    = trim($_POST['reg_phone'] ?? '');
        $email    = trim($_POST['reg_email'] ?? '');
        $username = trim($_POST['reg_username'] ?? '');
        $password = trim($_POST['reg_password'] ?? '');
        $confirmPass = trim($_POST['reg_confirm_password'] ?? '');

        $result = attempt_register($fullName, $phone, $email, $username, $password, $confirmPass, 'user');

        if ($result['success']) {
            $dest = !empty($redirectParam) ? $redirectParam : $result['redirect'];
            header("Location: " . $dest);
            exit;
        } else {
            $errorMsg = $result['error'];
            $activeTab = 'register';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký - Nha Khoa Quốc Tế Premier</title>
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Toàn bộ trang và các thành phần thừa hưởng font chữ chuẩn tiếng Việt */
        * {
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: var(--gradient-hero, linear-gradient(135deg, #071527 0%, #0c2744 50%, #0a3a60 100%));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 14px;
            letter-spacing: -0.01em;
        }

        .auth-container {
            width: 100%;
            max-width: 530px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.12);
            overflow: hidden;
            border: none;
        }

        .auth-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 60%, #0c4a6e 100%);
            padding: 32px 24px 28px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .auth-header h1, .auth-header .brand-title {
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .auth-header p {
            letter-spacing: 0.01em;
            font-size: 0.88rem;
        }

        /* Tabs Đăng Nhập / Đăng Ký */
        .auth-nav-tabs {
            border-bottom: 2px solid #e2e8f0;
            background: #ffffff;
        }

        .auth-nav-tabs .nav-link {
            font-weight: 700;
            font-size: 0.98rem;
            color: #64748b;
            border: none;
            padding: 16px 20px;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
        }

        .auth-nav-tabs .nav-link:hover {
            color: #0284c7;
            background: rgba(2, 132, 199, 0.04);
        }

        .auth-nav-tabs .nav-link.active {
            color: #0284c7;
            background: transparent;
            border-bottom: 3px solid #0284c7;
        }

        /* Nhãn Form rõ nét, chuyên nghiệp */
        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 7px;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
        }

        .form-label .text-danger {
            color: #ef4444 !important;
            font-weight: 700;
            margin-left: 3px;
        }

        /* Ô nhập liệu (Input Box) - Độ tương phản cao, viền rõ, icon sắc nét */
        .auth-card .input-group {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .auth-card .input-group:hover {
            border-color: #94a3b8;
            background: #ffffff;
        }

        .auth-card .input-group:focus-within {
            border-color: #0284c7 !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.16), 0 2px 6px rgba(2, 132, 199, 0.08) !important;
        }

        /* Icon biểu tượng đầu ô nhập liệu */
        .auth-card .input-group-text {
            background: #f0f9ff;
            border: none;
            color: #0284c7;
            padding: 11px 15px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .auth-card .input-group:focus-within .input-group-text {
            background: #e0f2fe;
            color: #0369a1;
        }

        /* Ô Text Input chính */
        .auth-card .form-control {
            background: transparent !important;
            border: none !important;
            font-size: 0.94rem;
            font-weight: 500;
            color: #0f172a !important;
            padding: 11px 14px;
            line-height: 1.5;
            box-shadow: none !important;
        }

        .auth-card .form-control:focus {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            color: #0f172a !important;
        }

        .auth-card .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
            font-size: 0.88rem;
        }

        /* Nút ẩn/hiện mật khẩu */
        .auth-card .btn-eye-toggle {
            background: #f0f9ff;
            border: none;
            color: #64748b;
            padding: 0 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card .btn-eye-toggle:hover {
            background: #e0f2fe;
            color: #0284c7;
        }

        .auth-card .input-group:focus-within .btn-eye-toggle {
            background: #e0f2fe;
            color: #0284c7;
        }

        /* Nút Đăng Nhập & Đăng Ký (Nổi bật, sang trọng, tương phản cao) */
        .btn-primary-clinic {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
            font-size: 1.02rem !important;
            letter-spacing: 0.015em !important;
            padding: 13px 20px !important;
            box-shadow: 0 10px 24px -4px rgba(2, 132, 199, 0.45), 0 4px 8px -2px rgba(2, 132, 199, 0.25) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
        }

        .btn-primary-clinic:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%) !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 14px 30px -4px rgba(2, 132, 199, 0.58), 0 6px 14px -2px rgba(2, 132, 199, 0.3) !important;
        }

        .btn-primary-clinic:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35) !important;
        }

        .btn-primary-clinic i {
            font-size: 1.08rem;
            transition: transform 0.2s ease;
        }

        .btn-primary-clinic:hover i {
            transform: scale(1.1);
        }

        /* Thẻ chọn vai trò (Khách Hàng / Quản Trị Viên) */
        .role-pill {
            cursor: pointer;
            border: 1.5px solid #cbd5e1;
            background: #f8fafc;
            border-radius: 14px;
            padding: 12px 14px;
            text-align: center;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #475569;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .role-pill:hover {
            border-color: #93c5fd;
            background: #f0f9ff;
            color: #0284c7;
            transform: translateY(-1px);
        }

        .role-pill.active {
            border-color: #0284c7;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            color: #0369a1;
            font-weight: 700;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.18);
        }

        .role-pill#pillAdmin.active {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #b91c1c;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.18);
        }

        /* Thẻ điền thông tin tài khoản demo */
        .quick-demo-badge {
            background: #eff6ff;
            border: 1.5px dashed #38bdf8;
            color: #0369a1;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .quick-demo-badge:hover {
            background: #dbeafe;
            border-color: #0284c7;
            color: #0284c7;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
        }

        .quick-demo-badge.bg-warning-subtle {
            background: #fefce8 !important;
            border-color: #f59e0b !important;
            color: #92400e !important;
        }

        .quick-demo-badge.bg-warning-subtle:hover {
            background: #fef08a !important;
            border-color: #d97706 !important;
        }

        /* Quên mật khẩu & Chuyển tab links */
        .auth-forgot-link {
            color: #0284c7;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .auth-forgot-link:hover {
            color: #0369a1;
            text-decoration: underline;
        }

        .auth-switch-link {
            color: #0284c7;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .auth-switch-link:hover {
            color: #0369a1;
            text-decoration: underline;
        }

        .auth-footer-back {
            color: #64748b;
            font-weight: 600;
            font-size: 0.88rem;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .auth-footer-back:hover {
            color: #0284c7;
            transform: translateX(-3px);
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <!-- Header Banner -->
        <div class="auth-header">
            <a href="index.php" class="text-white text-decoration-none d-inline-flex align-items-center gap-2 mb-2">
                <i class="fas fa-tooth fs-3"></i>
                <span class="fs-4 fw-bold">PREMIER DENTAL</span>
            </a>
            <p class="small text-white-50 mb-0">Chăm sóc nụ cười – Kiến tạo tự tin</p>
        </div>

        <!-- Alert messages -->
        <div class="px-4 pt-3">
            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($successMsg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> <?php echo htmlspecialchars($errorMsg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Navigation Tabs (Đăng Nhập / Đăng Ký) -->
        <ul class="nav nav-tabs auth-nav-tabs nav-fill px-4" id="authTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100 <?php echo $activeTab === 'login' ? 'active' : ''; ?>" id="tab-login-btn" data-bs-toggle="tab" data-bs-target="#tab-login" type="button" role="tab">
                    <i class="fas fa-sign-in-alt me-1"></i> Đăng Nhập
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link w-100 <?php echo $activeTab === 'register' ? 'active' : ''; ?>" id="tab-register-btn" data-bs-toggle="tab" data-bs-target="#tab-register" type="button" role="tab">
                    <i class="fas fa-user-plus me-1"></i> Đăng Ký Mới
                </button>
            </li>
        </ul>

        <div class="tab-content p-4" id="authTabContent">
            <!-- ================= TAB 1: ĐĂNG NHẬP ================= -->
            <div class="tab-pane fade <?php echo $activeTab === 'login' ? 'show active' : ''; ?>" id="tab-login" role="tabpanel">
                <form action="login.php<?php echo !empty($redirectParam) ? '?redirect=' . urlencode($redirectParam) : ''; ?>" method="POST" id="loginForm">
                    <input type="hidden" name="form_action" value="login">
                    <input type="hidden" name="account_type" id="loginAccountType" value="<?php echo htmlspecialchars($targetRole); ?>">

                    <!-- Chọn đối tượng đăng nhập -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Bạn là ai?</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="role-pill <?php echo $targetRole !== 'admin' ? 'active' : ''; ?>" id="pillUser" onclick="selectRole('user')">
                                    <i class="fas fa-user-injured fs-5 d-block mb-1 text-primary"></i>
                                    <span>Khách Hàng</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="role-pill <?php echo $targetRole === 'admin' ? 'active' : ''; ?>" id="pillAdmin" onclick="selectRole('admin')">
                                    <i class="fas fa-user-shield fs-5 d-block mb-1 text-danger"></i>
                                    <span>Quản Trị Viên</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tên đăng nhập / Email / SĐT -->
                    <div class="mb-3">
                        <label for="loginIdentity" class="form-label fw-semibold" id="labelIdentity">
                            Tên đăng nhập / Số điện thoại
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="loginIdentity" name="identity" 
                                   value="<?php echo htmlspecialchars($defaultIdentity); ?>"
                                   placeholder="Nhập tên đăng nhập hoặc số điện thoại..." required autofocus>
                        </div>
                    </div>

                    <!-- Mật khẩu -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="loginPassword" class="form-label mb-0">Mật khẩu</label>
                            <a href="#" class="auth-forgot-link" onclick="alert('Vui lòng liên hệ Hotline 1900 6868 hoặc Quản trị viên để đặt lại mật khẩu.'); return false;">Quên mật khẩu?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="loginPassword" name="password" 
                                   value="<?php echo htmlspecialchars($defaultPassword); ?>"
                                   placeholder="Nhập mật khẩu của bạn..." required>
                            <button class="btn btn-eye-toggle" type="button" onclick="togglePass('loginPassword', 'loginEyeIcon')" title="Ẩn/hiện mật khẩu">
                                <i class="fas fa-eye" id="loginEyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Fill Cho Khách Hàng -->
                    <div class="mb-3 <?php echo $targetRole !== 'admin' ? '' : 'd-none'; ?>" id="userDemoBox">
                        <div class="quick-demo-badge" onclick="fillUserCreds()">
                            <i class="fas fa-magic me-2"></i> Bấm để điền mẫu Khách hàng: <strong class="ms-1">khachhang</strong> / <strong class="ms-1">123456</strong>
                        </div>
                    </div>

                    <!-- Quick Fill Cho Admin -->
                    <div class="mb-3 <?php echo $targetRole === 'admin' ? '' : 'd-none'; ?>" id="adminDemoBox">
                        <div class="quick-demo-badge bg-warning-subtle" onclick="fillAdminCreds()">
                            <i class="fas fa-shield-alt text-danger me-2"></i> Bấm để điền mẫu Admin: <strong class="ms-1">admin</strong> / <strong class="ms-1">admin123</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-clinic w-100 py-3 mb-3 fw-bold">
                        <i class="fas fa-sign-in-alt me-2"></i> Đăng Nhập Hệ Thống
                    </button>
                </form>

                <div class="text-center mt-3 pt-3 border-top">
                    <span class="text-muted small">Chưa có tài khoản?</span>
                    <a href="javascript:void(0)" onclick="switchToRegister()" class="auth-switch-link ms-1 small">
                        Đăng ký tài khoản mới ngay
                    </a>
                </div>
            </div>

            <!-- ================= TAB 2: ĐĂNG KÝ ================= -->
            <div class="tab-pane fade <?php echo $activeTab === 'register' ? 'show active' : ''; ?>" id="tab-register" role="tabpanel">
                <form action="login.php" method="POST" id="registerForm">
                    <input type="hidden" name="form_action" value="register">

                    <!-- Họ và tên -->
                    <div class="mb-3">
                        <label for="regFullname" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" id="regFullname" name="reg_fullname" 
                                   placeholder="Ví dụ: Nguyễn Văn Tuấn" required>
                        </div>
                    </div>

                    <!-- Số điện thoại & Email -->
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="regPhone" class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="regPhone" name="reg_phone" 
                                       placeholder="0912345678" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="regEmail" class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="regEmail" name="reg_email" 
                                       placeholder="email@example.com">
                            </div>
                        </div>
                    </div>

                    <!-- Tên đăng nhập -->
                    <div class="mb-3">
                        <label for="regUsername" class="form-label fw-semibold">Tên đăng nhập <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <input type="text" class="form-control" id="regUsername" name="reg_username" 
                                   placeholder="Từ 3 ký tự (chữ cái, số)" required>
                        </div>
                    </div>

                    <!-- Mật khẩu & Xác nhận mật khẩu -->
                    <div class="row g-2 mb-4">
                        <div class="col-sm-6">
                            <label for="regPassword" class="form-label fw-semibold">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="regPassword" name="reg_password" 
                                       placeholder="Ít nhất 6 ký tự" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="regConfirmPassword" class="form-label fw-semibold">Nhập lại MK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                                <input type="password" class="form-control" id="regConfirmPassword" name="reg_confirm_password" 
                                       placeholder="Nhập lại..." required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-clinic w-100 py-3 mb-3 fw-bold">
                        <i class="fas fa-user-plus me-2"></i> Hoàn Tất Đăng Ký Tài Khoản
                    </button>
                </form>

                <div class="text-center mt-3 pt-3 border-top">
                    <span class="text-muted small">Đã có tài khoản?</span>
                    <a href="javascript:void(0)" onclick="switchToLogin()" class="auth-switch-link ms-1 small">
                        Đăng nhập ngay
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer quay lại trang chủ -->
        <div class="p-3 bg-light text-center border-top">
            <a href="index.php" class="auth-footer-back">
                <i class="fas fa-arrow-left me-1"></i> Quay về Trang chủ Premier Dental
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function selectRole(role) {
    document.getElementById('loginAccountType').value = role;
    const pillUser = document.getElementById('pillUser');
    const pillAdmin = document.getElementById('pillAdmin');
    const adminDemoBox = document.getElementById('adminDemoBox');
    const userDemoBox = document.getElementById('userDemoBox');
    const labelIdentity = document.getElementById('labelIdentity');

    if (role === 'admin') {
        pillAdmin.classList.add('active');
        pillUser.classList.remove('active');
        adminDemoBox.classList.remove('d-none');
        if (userDemoBox) userDemoBox.classList.add('d-none');
        labelIdentity.textContent = 'Tên đăng nhập Admin';
        document.getElementById('loginIdentity').placeholder = 'Nhập admin...';
    } else {
        pillUser.classList.add('active');
        pillAdmin.classList.remove('active');
        adminDemoBox.classList.add('d-none');
        if (userDemoBox) userDemoBox.classList.remove('d-none');
        labelIdentity.textContent = 'Tên đăng nhập / Số điện thoại';
        document.getElementById('loginIdentity').placeholder = 'Nhập khachhang hoặc SĐT (0987654321)...';
    }
}

function fillUserCreds() {
    selectRole('user');
    document.getElementById('loginIdentity').value = 'khachhang';
    document.getElementById('loginPassword').value = '123456';
}

function fillAdminCreds() {
    selectRole('admin');
    document.getElementById('loginIdentity').value = 'admin';
    document.getElementById('loginPassword').value = 'admin123';
}

function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function switchToRegister() {
    const triggerEl = document.getElementById('tab-register-btn');
    const tab = new bootstrap.Tab(triggerEl);
    tab.show();
}

function switchToLogin() {
    const triggerEl = document.getElementById('tab-login-btn');
    const tab = new bootstrap.Tab(triggerEl);
    tab.show();
}
</script>
</body>
</html>
