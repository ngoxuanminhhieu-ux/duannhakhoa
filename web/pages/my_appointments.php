<?php
// web/pages/my_appointments.php
// Tra cứu & Quản lý lịch hẹn cá nhân dành cho khách hàng (Hỗ trợ cả đã đăng nhập & tra cứu nhanh qua SĐT)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = 'my_appointments';
$page_title   = 'Lịch Hẹn Của Tôi - Lịch Đã Đặt Khám - Premier Dental';
$is_page      = true;

require_once __DIR__ . '/../config/database.php';

$userLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
if ($userLoggedIn && !empty($_SESSION['username']) && !empty($pdo)) {
    try {
        $stmtSync = $pdo->prepare("SELECT full_name, email, phone FROM users WHERE username = ?");
        $stmtSync->execute([$_SESSION['username']]);
        $uFresh = $stmtSync->fetch(PDO::FETCH_ASSOC);
        if ($uFresh && !empty($uFresh['full_name'])) {
            $_SESSION['full_name'] = $uFresh['full_name'];
            if (!empty($uFresh['phone'])) $_SESSION['phone'] = $uFresh['phone'];
            if (!empty($uFresh['email'])) $_SESSION['email'] = $uFresh['email'];
        }
    } catch (\Throwable $e) {}
}
$sessionPhone = $userLoggedIn ? ($_SESSION['phone'] ?? '') : '';
$sessionEmail = $userLoggedIn ? ($_SESSION['email'] ?? '') : '';
$patientId    = $userLoggedIn ? ($_SESSION['patient_id'] ?? null) : null;
$userName     = $userLoggedIn ? ($_SESSION['full_name'] ?? $_SESSION['username']) : '';

$lookupPhone  = trim($_GET['phone'] ?? $_POST['lookup_phone'] ?? '');
if (empty($lookupPhone) && $userLoggedIn && !empty($sessionPhone)) {
    $lookupPhone = $sessionPhone;
}

$successMsg = '';
$errorMsg   = '';

// Xử lý Hủy lịch hẹn (khi trạng thái là pending)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
    $cancelId = (int)($_POST['appointment_id'] ?? 0);
    $confirmPhone = trim($_POST['verify_phone'] ?? $lookupPhone);

    if ($cancelId > 0 && !empty($pdo)) {
        try {
            // Kiểm tra quyền hủy: lịch hẹn phải tồn tại, đang pending và thuộc về SĐT/bệnh nhân này
            $stmtCheck = $pdo->prepare("
                SELECT a.id, a.status 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                WHERE a.id = ? AND a.status = 'pending' AND (p.phone = ? OR p.id = ?)
            ");
            $stmtCheck->execute([$cancelId, $confirmPhone, $patientId ?: 0]);
            $appToCancel = $stmtCheck->fetch();

            if ($appToCancel) {
                $stmtCancel = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
                $stmtCancel->execute([$cancelId]);
                $successMsg = "Lịch hẹn #$cancelId đã được hủy thành công theo yêu cầu của quý khách.";
            } else {
                $errorMsg = "Không thể hủy lịch hẹn này. Lịch hẹn có thể đã được duyệt xác nhận hoặc không tìm thấy thông tin xác thực.";
            }
        } catch (\Exception $e) {
            $errorMsg = "Lỗi khi xử lý hủy lịch hẹn. Vui lòng liên hệ Hotline 1900 6868.";
        }
    }
}

// Truy vấn danh sách lịch hẹn
$appointments = [];
$searched = false;

if (!empty($pdo)) {
    try {
        if ($userLoggedIn && empty($lookupPhone)) {
            // Trường hợp đăng nhập không có SĐT: tìm theo patient_id hoặc email
            $stmt = $pdo->prepare("
                SELECT a.*, 
                       s.name as service_name, s.price as service_price,
                       d.full_name as doctor_name, d.specialty as doctor_specialty, d.image as doctor_image,
                       p.full_name as patient_name, p.phone as patient_phone, p.email as patient_email
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN doctors d ON a.doctor_id = d.id
                WHERE p.id = ? OR (p.email = ? AND ? != '')
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
            ");
            $stmt->execute([$patientId ?: 0, $sessionEmail, $sessionEmail]);
            $appointments = $stmt->fetchAll();
            $searched = true;
        } elseif (!empty($lookupPhone)) {
            // Tìm kiếm theo số điện thoại (cho cả người chưa đăng nhập và đã đăng nhập)
            $cleanPhone = preg_replace('/[^0-9]/', '', $lookupPhone);
            $stmt = $pdo->prepare("
                SELECT a.*, 
                       s.name as service_name, s.price as service_price,
                       d.full_name as doctor_name, d.specialty as doctor_specialty, d.image as doctor_image,
                       p.full_name as patient_name, p.phone as patient_phone, p.email as patient_email
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                LEFT JOIN services s ON a.service_id = s.id
                LEFT JOIN doctors d ON a.doctor_id = d.id
                WHERE p.phone = ? OR p.phone LIKE ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
            ");
            $stmt->execute([$lookupPhone, "%$cleanPhone%"]);
            $appointments = $stmt->fetchAll();
            $searched = true;
        }
    } catch (\Exception $e) {
        $appointments = [];
    }
}

// Lấy thông tin bệnh nhân đại diện nếu tìm thấy
$primaryPatientName = '';
if (!empty($appointments)) {
    $primaryPatientName = $appointments[0]['patient_name'] ?? '';
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header Banner -->
<div class="page-header-light" style="background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 50%, #ffffff 100%); padding: 48px 0 36px; border-bottom: 1px solid #bae6fd;">
    <div class="container text-center">
        <span class="badge-page-tag mb-3 d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-white text-primary shadow-sm border border-info border-opacity-25">
            <i class="far fa-calendar-check text-primary"></i> Cổng Khách Hàng - Tra Cứu Lịch Hẹn
        </span>
        <h1 class="page-header-title text-dark-navy fw-bold mb-2" style="font-size: 2.2rem;">
            Lịch Hẹn Của Tôi
        </h1>
        <p class="page-header-desc text-muted mx-auto mb-0" style="max-width: 680px;">
            Theo dõi chi tiết các lịch khám đã đăng ký, thời gian hẹn, bác sĩ chuyên khoa phụ trách và tiến trình xử lý tại Nha Khoa Quốc Tế Premier Dental.
        </p>
    </div>
</div>

<div class="container my-5">
    <!-- Hộp thông báo Alert -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 p-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-4 text-success"></i>
                <div>
                    <strong>Thành công:</strong> <?php echo htmlspecialchars($successMsg); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 p-3 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-circle fs-4 text-danger"></i>
                <div>
                    <strong>Lưu ý:</strong> <?php echo htmlspecialchars($errorMsg); ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- THANH TRA CỨU NHANH THEO SỐ ĐIỆN THOẠI -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1.5px solid #e2e8f0 !important;">
        <div class="row align-items-center g-3">
            <div class="col-lg-5">
                <h5 class="fw-bold text-dark-navy mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-search text-primary"></i> Tra Cứu Lịch Khám Bằng Số Điện Thoại
                </h5>
                <p class="small text-muted mb-0">
                    Nhập số điện thoại bạn đã dùng khi đặt lịch để tra cứu tức thì thông tin ca khám.
                </p>
            </div>
            <div class="col-lg-7">
                <form action="my_appointments.php" method="GET" class="d-flex flex-sm-row flex-column gap-2">
                    <div class="input-group flex-grow-1" style="border: 1.5px solid #0284c7; border-radius: 14px; overflow: hidden; background: #fff;">
                        <span class="input-group-text bg-white border-0 text-primary px-3">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        <input type="tel" name="phone" class="form-control border-0 py-2" 
                               value="<?php echo htmlspecialchars($lookupPhone); ?>" 
                               placeholder="Nhập số điện thoại (VD: 0987654321)..." 
                               pattern="[0-9]{9,11}" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary-clinic px-4 py-2 text-nowrap rounded-3 fw-bold">
                        <i class="fas fa-search me-1"></i> Tra Cứu Ngay
                    </button>
                </form>
                <!-- Gợi ý số điện thoại mẫu -->
                <div class="d-flex align-items-center gap-2 mt-2">
                    <small class="text-muted">Gợi ý thử nhanh:</small>
                    <a href="my_appointments.php?phone=0987654321" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none rounded-pill px-2 py-1">
                        0987654321
                    </a>
                    <a href="my_appointments.php?phone=0912345678" class="badge bg-info-subtle text-info border border-info-subtle text-decoration-none rounded-pill px-2 py-1">
                        0912345678
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- CỘT TRÁI: THÔNG TIN BỆNH NHÂN & TIỆN ÍCH -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <div class="rounded-circle text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2.2rem; background: #e0f2fe;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark-navy">
                        <?php 
                        if ($userLoggedIn) {
                            echo htmlspecialchars($userName);
                        } elseif (!empty($primaryPatientName)) {
                            echo htmlspecialchars($primaryPatientName);
                        } elseif (!empty($lookupPhone)) {
                            echo "SĐT: " . htmlspecialchars($lookupPhone);
                        } else {
                            echo "Khách Hàng Premier Dental";
                        }
                        ?>
                    </h5>
                    <?php if ($userLoggedIn): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">Đã Đăng Nhập Tài Khoản</span>
                    <?php elseif (!empty($lookupPhone)): ?>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1">Tra Cứu Khách Vãng Lai</span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">Chưa Nhập Số Điện Thoại</span>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted"><i class="fas fa-phone-alt me-2 text-primary"></i>Số điện thoại:</span>
                        <strong class="text-dark-navy"><?php echo htmlspecialchars($lookupPhone ?: ($sessionPhone ?: 'Chưa nhập')); ?></strong>
                    </div>
                    <?php if (!empty($sessionEmail)): ?>
                        <div class="d-flex justify-content-between pb-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-envelope me-2 text-primary"></i>Email:</span>
                            <strong class="text-dark-navy"><?php echo htmlspecialchars($sessionEmail); ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted"><i class="far fa-calendar-check me-2 text-primary"></i>Lịch hẹn tìm thấy:</span>
                        <span class="badge bg-primary rounded-pill px-2 py-1"><?php echo count($appointments); ?> Lịch hẹn</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="fas fa-clinic-medical me-2 text-primary"></i>Cơ sở khám:</span>
                        <strong class="text-dark-navy text-end">123 Nguyễn Văn Cừ, Q.5</strong>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid gap-2">
                    <a href="booking.php" class="btn btn-primary-clinic w-100 rounded-pill py-2 fw-semibold">
                        <i class="far fa-calendar-plus me-1"></i> Đặt thêm lịch khám mới
                    </a>
                    <?php if (!$userLoggedIn): ?>
                        <a href="../login.php?tab=login" class="btn btn-outline-secondary rounded-pill py-2 small fw-semibold">
                            <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập để lưu hồ sơ trọn đời
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hotline hỗ trợ trực tiếp -->
            <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm mb-4">
                <div class="rounded-circle text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.3rem; background: #e0f2fe;">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Hỗ trợ dời lịch / tư vấn gấp:</small>
                    <a href="tel:19006868" class="text-primary text-decoration-none fw-bold fs-6">1900 6868 (Miễn phí 24/7)</a>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: DANH SÁCH LỊCH HẸN ĐÃ ĐẶT -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-2 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark-navy mb-1">
                            <i class="far fa-calendar-alt text-primary me-2"></i> Danh Sách Lịch Đã Đặt Để Khám
                        </h5>
                        <p class="small text-muted mb-0">
                            Hiển thị toàn bộ lịch hẹn theo số điện thoại đã xác nhận.
                        </p>
                    </div>
                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 fw-semibold">
                        <?php echo count($appointments); ?> Lịch Hẹn
                    </span>
                </div>

                <?php if (!empty($appointments)): ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($appointments as $app): 
                            $status = $app['status'] ?? 'pending';
                            $statusBadgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                            $statusText = 'Chờ xác nhận (Lễ tân sẽ liên hệ)';
                            $statusIcon = 'fa-clock';
                            $canCancel = true;

                            if ($status === 'confirmed') {
                                $statusBadgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                $statusText = 'Đã xác nhận (Phòng khám sẵn sàng đón bạn)';
                                $statusIcon = 'fa-check-circle';
                                $canCancel = false;
                            } elseif ($status === 'completed') {
                                $statusBadgeClass = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                $statusText = 'Đã khám hoàn tất';
                                $statusIcon = 'fa-flag-checkered';
                                $canCancel = false;
                            } elseif ($status === 'cancelled') {
                                $statusBadgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                $statusText = 'Đã hủy lịch hẹn';
                                $statusIcon = 'fa-times-circle';
                                $canCancel = false;
                            }
                        ?>
                            <div class="p-3 p-md-4 rounded-4 border bg-white shadow-sm" style="border-left: 5px solid #0284c7 !important;">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-3 py-1">
                                                Mã hẹn #<?php echo $app['id']; ?>
                                            </span>
                                            <span class="badge rounded-pill px-3 py-1 small fw-semibold <?php echo $statusBadgeClass; ?>">
                                                <i class="fas <?php echo $statusIcon; ?> me-1"></i> <?php echo $statusText; ?>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold text-dark-navy mb-0 mt-2">
                                            <?php echo htmlspecialchars($app['service_name'] ?: 'Khám tổng quát & Tư vấn chuyên sâu'); ?>
                                        </h5>
                                        <?php if (!empty($app['service_price'])): ?>
                                            <small class="text-primary fw-bold">Chi phí dự kiến: <?php echo number_format($app['service_price'], 0, ',', '.'); ?> VNĐ</small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-md-end text-start">
                                        <small class="text-muted d-block">Đặt ngày: <?php echo date('d/m/Y H:i', strtotime($app['created_at'])); ?></small>
                                    </div>
                                </div>

                                <div class="row g-3 py-2 my-1 bg-light rounded-3 px-2 border">
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">
                                            <i class="far fa-calendar-alt text-primary me-1"></i> Ngày khám:
                                            <strong class="text-dark-navy fs-6 ms-1"><?php echo date('d/m/Y', strtotime($app['appointment_date'])); ?></strong>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="far fa-clock text-primary me-1"></i> Giờ khám:
                                            <strong class="text-dark-navy fs-6 ms-1"><?php echo date('H:i', strtotime($app['appointment_time'])); ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-user-md text-primary me-1"></i> Bác sĩ phụ trách:
                                            <strong class="text-dark-navy ms-1"><?php echo htmlspecialchars($app['doctor_name'] ?: 'Lễ tân phân công theo chuyên khoa'); ?></strong>
                                        </div>
                                        <?php if (!empty($app['doctor_specialty'])): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-stethoscope text-primary me-1"></i> Chuyên khoa:
                                                <span class="ms-1"><?php echo htmlspecialchars($app['doctor_specialty']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (!empty($app['note'])): ?>
                                    <div class="small text-muted mt-2 p-2 rounded bg-white border border-dashed">
                                        <i class="far fa-comment-alt text-primary me-1"></i> <strong>Ghi chú bệnh án:</strong> <?php echo htmlspecialchars($app['note']); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Hành động đối với lịch hẹn -->
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-2 border-top">
                                    <div class="small text-muted">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> 123 Nguyễn Văn Cừ, P.4, Q.5, TP.HCM
                                    </div>

                                    <div class="d-flex gap-2">
                                        <?php if ($canCancel): ?>
                                            <form action="my_appointments.php?phone=<?php echo urlencode($lookupPhone); ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn khám #<?php echo $app['id']; ?> này không?');">
                                                <input type="hidden" name="action" value="cancel_appointment">
                                                <input type="hidden" name="appointment_id" value="<?php echo $app['id']; ?>">
                                                <input type="hidden" name="verify_phone" value="<?php echo htmlspecialchars($lookupPhone); ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                    <i class="fas fa-times me-1"></i> Hủy Lịch Này
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <a href="https://maps.google.com/?q=123+Nguyen+Van+Cu+Quan+5+Ho+Chi+Minh" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="fas fa-directions me-1"></i> Chỉ đường
                                        </a>
                                        <a href="tel:19006868" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="fas fa-phone-alt me-1"></i> Gọi hỗ trợ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="rounded-circle text-muted mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2.5rem; background: #f1f5f9;">
                            <i class="far fa-calendar-times"></i>
                        </div>
                        <?php if ($searched): ?>
                            <h5 class="fw-bold text-dark-navy mb-2">Không tìm thấy lịch hẹn cho số: <?php echo htmlspecialchars($lookupPhone); ?></h5>
                            <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                                Vui lòng kiểm tra lại số điện thoại bạn đã điền khi đặt lịch khám hoặc đăng ký đặt một lịch khám mới ngay bây giờ.
                            </p>
                        <?php else: ?>
                            <h5 class="fw-bold text-dark-navy mb-2">Vui lòng nhập số điện thoại để tra cứu</h5>
                            <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                                Nhập số điện thoại của bạn vào khung tìm kiếm phía trên để hiển thị toàn bộ lịch khám đã đặt tại phòng khám.
                            </p>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="booking.php" class="btn btn-primary-clinic rounded-pill px-4 py-2">
                                <i class="far fa-calendar-plus me-1"></i> Đăng ký lịch khám mới
                            </a>
                            <a href="services.php" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                <i class="fas fa-tooth me-1"></i> Xem bảng giá dịch vụ
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
