<?php
$current_page = 'booking';
$page_title = 'Đặt Lịch Khám Ưu Tiên - Nha Khoa Quốc Tế Premier';
$is_page = true;
require_once __DIR__ . '/../config/database.php';

$success_message = '';
$error_message = '';
$last_booked_phone = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$default_name   = $user_logged_in ? ($_SESSION['full_name'] ?? '') : '';
$default_phone  = $user_logged_in ? ($_SESSION['phone'] ?? '') : '';
$default_email  = $user_logged_in ? ($_SESSION['email'] ?? '') : '';

// Selected service or doctor from URL parameters
$selected_service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
$selected_doctor_id  = isset($_GET['doctor_id'])  ? (int)$_GET['doctor_id']  : 0;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name'] ?? '');
    $phone            = trim($_POST['phone'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $date_of_birth    = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $service_id       = !empty($_POST['service_id']) ? (int)$_POST['service_id'] : null;
    $doctor_id        = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null;
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = trim($_POST['appointment_time'] ?? '');
    $note             = trim($_POST['note'] ?? '');

    // Server-side Validation
    if (empty($full_name) || empty($phone) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Định dạng email không hợp lệ. Vui lòng kiểm tra lại.';
    } elseif (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
        $error_message = 'Số điện thoại không hợp lệ (gồm 10 chữ số bắt đầu bằng 03, 05, 07, 08, 09).';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Check if patient exists by phone number
            $stmt_check = $pdo->prepare("SELECT id FROM patients WHERE phone = ?");
            $stmt_check->execute([$phone]);
            $patient = $stmt_check->fetch();

            if ($patient) {
                $patient_id = $patient['id'];
                $stmt_upd = $pdo->prepare("UPDATE patients SET full_name = ?, email = COALESCE(NULLIF(?, ''), email), date_of_birth = COALESCE(?, date_of_birth) WHERE id = ?");
                $stmt_upd->execute([$full_name, $email, $date_of_birth, $patient_id]);
            } else {
                $stmt_ins = $pdo->prepare("INSERT INTO patients (full_name, phone, email, date_of_birth) VALUES (?, ?, ?, ?)");
                $stmt_ins->execute([$full_name, $phone, $email, $date_of_birth]);
                $patient_id = $pdo->lastInsertId();
            }

            // 2. Insert Appointment
            $stmt_app = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, appointment_time, note, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt_app->execute([$patient_id, $doctor_id, $service_id, $appointment_date, $appointment_time, $note]);

            $pdo->commit();
            $last_booked_phone = $phone;
            $success_message = 'Đặt lịch thành công! Nhân viên lễ tân Premier Dental sẽ liên hệ với quý khách trong vòng 15 phút để xác nhận.';
            
            // Reset selected values
            $selected_service_id = 0;
            $selected_doctor_id = 0;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Đã có lỗi hệ thống xảy ra khi lưu lịch hẹn. Vui lòng liên hệ Hotline 1900 6868 để được hỗ trợ!';
        }
    }
}

// Fetch all services & doctors for dropdowns
require_once __DIR__ . '/../data/doctors_detail_data.php';
$services_list = [];
if (!empty($pdo)) {
    try {
        $services_list = $pdo->query("SELECT id, name, price FROM services ORDER BY price ASC")->fetchAll();
    } catch (\Throwable $e) {
        $services_list = [];
    }
}
$doctors_full_list = get_doctors_for_view();
$doctors_list = $doctors_full_list;

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header Banner -->
<div class="page-header-light">
    <div class="container text-center">
        <span class="badge-page-tag mb-3">
            <i class="fas fa-calendar-check text-primary me-1"></i> Trực Tuyến 24/7
        </span>
        <h1 class="page-header-title">Đăng Ký Khám Ưu Tiên</h1>
        <p class="page-header-desc">Chọn chuyên khoa, bác sĩ và thời gian khám phù hợp để được phục vụ chu đáo nhất mà không cần chờ đợi.</p>
    </div>
</div>

<div class="container my-5 py-2">
    <!-- Success / Error Alert Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-check-circle text-success fs-1"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Xác Nhận Đặt Lịch Thành Công!</h5>
                    <p class="mb-0 text-success-emphasis"><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            </div>
            <div>
                <a href="my_appointments.php<?php echo !empty($last_booked_phone) ? '?phone=' . urlencode($last_booked_phone) : ''; ?>" class="btn btn-primary-clinic rounded-pill px-4 py-2 fw-bold text-nowrap shadow-sm">
                    <i class="far fa-calendar-check me-2"></i> Xem Lịch Hẹn Của Tôi Ngay
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center gap-3">
            <i class="fas fa-exclamation-circle text-danger fs-2"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Không Thể Hoàn Tất</h5>
                <p class="mb-0 text-danger-emphasis"><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-5">
        <!-- Cột trái: Form Đặt Lịch Wizard -->
        <div class="col-lg-8">
            <div class="booking-wizard-card">
                <!-- 3-Step Wizard Visual Indicator -->
                <div class="booking-step-indicator">
                    <div class="booking-step-item active">
                        <div class="booking-step-circle">1</div>
                        <span class="booking-step-label">Dịch Vụ</span>
                    </div>
                    <div class="booking-step-item active">
                        <div class="booking-step-circle">2</div>
                        <span class="booking-step-label">Thời Gian</span>
                    </div>
                    <div class="booking-step-item active">
                        <div class="booking-step-circle">3</div>
                        <span class="booking-step-label">Thông Tin</span>
                    </div>
                </div>

                <form method="POST" action="booking.php" id="bookingForm">
                    <!-- Bước 1: Chọn Dịch Vụ & Bác Sĩ -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark-navy mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</span>
                            Nhu Cầu Khám Chữa Bệnh
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="service_id" class="form-label-custom">
                                    <i class="fas fa-stethoscope text-primary"></i> Dịch vụ quan tâm
                                </label>
                                <select class="form-select form-select-custom" id="service_id" name="service_id">
                                    <option value="">-- Khám tổng quát & Tư vấn (Mặc định) --</option>
                                    <?php foreach ($services_list as $svc): ?>
                                        <option value="<?php echo $svc['id']; ?>" <?php echo ($selected_service_id == $svc['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($svc['name']); ?> (Từ <?php echo number_format($svc['price'], 0, ',', '.'); ?>đ)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="doctor_id" class="form-label-custom">
                                    <i class="fas fa-user-md text-primary"></i> Bác sĩ chỉ định (Tùy chọn)
                                </label>
                                <select class="form-select form-select-custom" id="doctor_id" name="doctor_id" onchange="displayDoctorInfo(this.value)">
                                    <option value="">-- Lễ tân tự động xếp Bác sĩ chuyên khoa --</option>
                                    <?php foreach ($doctors_list as $doc): ?>
                                        <option value="<?php echo $doc['id']; ?>" <?php echo ($selected_doctor_id == $doc['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($doc['full_name']); ?> - <?php echo htmlspecialchars($doc['specialty']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Selected Doctor Preview Card -->
                        <div id="selectedDoctorInfoCard" class="mt-3 p-3 rounded-4 bg-light border shadow-sm" style="display:none;"></div>
                    </div>

                    <!-- Bước 2: Chọn Ngày Giờ Khám -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark-navy mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">2</span>
                            Thời Gian Mong Muốn
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="appointment_date" class="form-label-custom">
                                    <i class="far fa-calendar-alt text-primary"></i> Ngày hẹn khám <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control form-control-custom" id="appointment_date" name="appointment_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="appointment_time" class="form-label-custom">
                                    <i class="far fa-clock text-primary"></i> Khung giờ hẹn <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-custom" id="appointment_time" name="appointment_time" required>
                                    <option value="08:30:00">08:30 - Sáng sớm</option>
                                    <option value="09:30:00" selected>09:30 - Buổi sáng</option>
                                    <option value="10:30:00">10:30 - Buổi sáng</option>
                                    <option value="14:00:00">14:00 - Đầu giờ chiều</option>
                                    <option value="15:30:00">15:30 - Buổi chiều</option>
                                    <option value="17:00:00">17:00 - Chiều muộn</option>
                                    <option value="18:30:00">18:30 - Buổi tối</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Bước 3: Thông Tin Khách Hàng -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark-navy mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">3</span>
                            Thông Tin Liên Hệ Bệnh Nhân
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label-custom">
                                    <i class="far fa-user text-primary"></i> Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="full_name" name="full_name" value="<?php echo htmlspecialchars($default_name); ?>" placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label-custom">
                                    <i class="fas fa-phone-alt text-primary"></i> Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control form-control-custom" id="phone" name="phone" value="<?php echo htmlspecialchars($default_phone); ?>" placeholder="0901 234 567" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label-custom">
                                    <i class="far fa-envelope text-primary"></i> Email nhận xác nhận
                                </label>
                                <input type="email" class="form-control form-control-custom" id="email" name="email" value="<?php echo htmlspecialchars($default_email); ?>" placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label-custom">
                                    <i class="far fa-calendar text-primary"></i> Ngày sinh (Năm sinh)
                                </label>
                                <input type="date" class="form-control form-control-custom" id="date_of_birth" name="date_of_birth">
                            </div>
                            <div class="col-12">
                                <label for="note" class="form-label-custom">
                                    <i class="far fa-comment-dots text-primary"></i> Tình trạng răng miệng & Ghi chú thêm
                                </label>
                                <textarea class="form-control form-control-custom" id="note" name="note" rows="3" placeholder="Ví dụ: Răng hàm số 6 bị đau nhức khi ăn đồ lạnh, muốn khám và chụp phim..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm text-uppercase" style="letter-spacing: 0.03em;">
                            <i class="far fa-check-circle me-2"></i> Xác Nhận Đặt Lịch Hẹn Ngay
                        </button>
                        <small class="text-center text-muted mt-2">
                            <i class="fas fa-lock text-success me-1"></i> Thông tin của quý khách được bảo mật tuyệt đối theo tiêu chuẩn Bộ Y Tế.
                        </small>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cột phải: Live Summary Sidebar & Hỗ trợ -->
        <div class="col-lg-4">
            <div class="booking-summary-sidebar">
                <h5 class="fw-bold text-dark-navy mb-3">
                    <i class="fas fa-clipboard-check text-primary me-2"></i> Quyền Lợi Bệnh Nhân
                </h5>
                <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                    <li class="d-flex align-items-start gap-2 small">
                        <i class="fas fa-check text-success mt-1"></i>
                        <span><strong>Khám & tư vấn miễn phí</strong> với bác sĩ chuyên khoa.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2 small">
                        <i class="fas fa-check text-success mt-1"></i>
                        <span><strong>Chụp phim CT 3D</strong> chẩn đoán không tính phí lần đầu.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2 small">
                        <i class="fas fa-check text-success mt-1"></i>
                        <span><strong>Ưu tiên khám ngay</strong> theo đúng khung giờ hẹn trước.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2 small">
                        <i class="fas fa-check text-success mt-1"></i>
                        <span><strong>Nhắc lịch tự động</strong> qua tin nhắn và hỗ trợ đổi lịch linh hoạt.</span>
                    </li>
                </ul>

                <hr class="border-primary border-opacity-25 my-4">

                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.3rem;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Cần hỗ trợ gấp?</small>
                        <strong class="text-dark-navy fs-5">Hotline 1900 6868</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
const doctorsDatabase = <?php echo json_encode($doctors_full_list, JSON_UNESCAPED_UNICODE); ?>;

function displayDoctorInfo(doctorId) {
    const card = document.getElementById('selectedDoctorInfoCard');
    if (!card) return;
    if (!doctorId) {
        card.style.display = 'none';
        card.innerHTML = '';
        return;
    }
    const doc = doctorsDatabase.find(d => String(d.id) === String(doctorId));
    if (!doc) {
        card.style.display = 'none';
        return;
    }
    
    let imgSrc = doc.image || 'assets/images/dr-nguyen-an.jpg';
    if (!imgSrc.startsWith('http') && !imgSrc.startsWith('../')) {
        imgSrc = '../' + imgSrc.replace(/^\.?\//, '');
    }

    card.style.display = 'block';
    card.innerHTML = `
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="${imgSrc}" class="rounded-circle shadow-sm border border-2 border-primary" style="width:72px; height:72px; object-fit:cover;" alt="${doc.full_name}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=200';">
            </div>
            <div class="col">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2 py-1" style="font-size:0.75rem;">${doc.specialty}</span>
                    <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-2 py-1" style="font-size:0.75rem;"><i class="fas fa-check-circle me-1"></i> Bác sĩ chính thức</span>
                </div>
                <h6 class="fw-bold text-dark mb-1">${doc.full_name} <small class="text-primary fw-normal">(${doc.title || 'Bác sĩ chuyên khoa'})</small></h6>
                <p class="text-muted small mb-1">
                    <i class="fas fa-award text-warning me-1"></i> <strong>Kinh nghiệm:</strong> ${doc.experience} &nbsp;|&nbsp; 
                    <i class="fas fa-star text-warning me-1"></i> <strong>Đánh giá:</strong> ${doc.rating || '4.9/5'} &nbsp;|&nbsp;
                    <i class="fas fa-clock text-info me-1"></i> <strong>Lịch:</strong> ${doc.schedule || 'Thứ 2 - Thứ 7'}
                </p>
                <p class="text-secondary small mb-0 fst-italic" style="font-size:0.82rem;">"${doc.description ? doc.description.substring(0, 160) + '...' : ''}"</p>
            </div>
        </div>
    `;
}

// Auto-trigger if pre-selected
document.addEventListener('DOMContentLoaded', () => {
    const docSelect = document.getElementById('doctor_id');
    if (docSelect && docSelect.value) {
        displayDoctorInfo(docSelect.value);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
