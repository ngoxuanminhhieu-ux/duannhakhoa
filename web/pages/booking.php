<?php
$current_page = 'booking';
$page_title = 'Đặt Lịch Khám - Nha Khoa Smile';
$is_page = true;
require_once __DIR__ . '/../config/database.php';

$success_message = '';
$error_message = '';

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
    if (empty($full_name) || empty($phone) || empty($email) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
                // Update email or DOB if changed
                $stmt_upd = $pdo->prepare("UPDATE patients SET full_name = ?, email = ?, date_of_birth = COALESCE(?, date_of_birth) WHERE id = ?");
                $stmt_upd->execute([$full_name, $email, $date_of_birth, $patient_id]);
            } else {
                // Insert new patient
                $stmt_ins = $pdo->prepare("INSERT INTO patients (full_name, phone, email, date_of_birth) VALUES (?, ?, ?, ?)");
                $stmt_ins->execute([$full_name, $phone, $email, $date_of_birth]);
                $patient_id = $pdo->lastInsertId();
            }

            // 2. Insert Appointment
            $stmt_app = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, service_id, appointment_date, appointment_time, note, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt_app->execute([$patient_id, $doctor_id, $service_id, $appointment_date, $appointment_time, $note]);

            $pdo->commit();
            $success_message = 'Đặt lịch thành công! Phòng khám sẽ liên hệ với bạn để xác nhận trong thời gian sớm nhất.';
            
            // Reset selected values after successful booking
            $selected_service_id = 0;
            $selected_doctor_id = 0;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Lỗi hệ thống khi đặt lịch: ' . $e->getMessage();
        }
    }
}

// Fetch list of services and doctors for dropdown options
try {
    $services_list = $pdo->query("SELECT id, name, price FROM services ORDER BY name ASC")->fetchAll();
    $doctors_list  = $pdo->query("SELECT id, full_name, specialty FROM doctors ORDER BY full_name ASC")->fetchAll();
} catch (Exception $e) {
    $services_list = [];
    $doctors_list  = [];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-dark) 100%);">
    <div class="container text-center py-4">
        <h1 class="display-4 fw-bold text-white mb-2">Đặt Lịch Khám Trực Tuyến</h1>
        <p class="lead text-white-50">Nhanh chóng - Tiện lợi - Chọn giờ & Bác sĩ mong muốn</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show p-4 mb-4 rounded-3 shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-check-circle fs-1 text-success"></i>
                        <div>
                            <h4 class="alert-heading fw-bold mb-1">Chúc mừng!</h4>
                            <p class="mb-0 fs-6"><?php echo htmlspecialchars($success_message); ?></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show p-3 mb-4 rounded-3 border-0" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="booking-container">
                <h3 class="fw-bold mb-4 text-center text-primary"><i class="far fa-edit me-2"></i> Thông Tin Đăng Ký Khám</h3>
                <form action="booking.php" method="POST" id="bookingForm">
                    <div class="row g-3">
                        <!-- Họ và Tên -->
                        <div class="col-md-6">
                            <label for="full_name" class="form-label fw-semibold">Họ và tên bệnh nhân <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Ví dụ: Nguyễn Văn Tuấn" required>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Ví dụ: 0912345678" required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                        </div>

                        <!-- Ngày sinh -->
                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label fw-semibold">Ngày sinh</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                        </div>

                        <!-- Chọn Dịch vụ -->
                        <div class="col-md-6">
                            <label for="service_id" class="form-label fw-semibold">Dịch vụ nha khoa mong muốn</label>
                            <select class="form-select" id="service_id" name="service_id">
                                <option value="">-- Chọn dịch vụ nha khoa --</option>
                                <?php foreach ($services_list as $svc): ?>
                                    <option value="<?php echo $svc['id']; ?>" <?php echo ($selected_service_id == $svc['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($svc['name']) . ' (' . number_format($svc['price'], 0, ',', '.') . ' VNĐ)'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Chọn Bác sĩ -->
                        <div class="col-md-6">
                            <label for="doctor_id" class="form-label fw-semibold">Bác sĩ khám mong muốn</label>
                            <select class="form-select" id="doctor_id" name="doctor_id">
                                <option value="">-- Bác sĩ khám bất kỳ / Chọn bác sĩ --</option>
                                <?php foreach ($doctors_list as $doc): ?>
                                    <option value="<?php echo $doc['id']; ?>" <?php echo ($selected_doctor_id == $doc['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($doc['full_name']) . ' - ' . htmlspecialchars($doc['specialty']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Ngày khám -->
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label fw-semibold">Ngày khám <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Giờ khám -->
                        <div class="col-md-6">
                            <label for="appointment_time" class="form-label fw-semibold">Khung giờ khám <span class="text-danger">*</span></label>
                            <select class="form-select" id="appointment_time" name="appointment_time" required>
                                <option value="">-- Chọn giờ khám --</option>
                                <option value="08:30:00">08:30 Sáng</option>
                                <option value="09:30:00">09:30 Sáng</option>
                                <option value="10:30:00">10:30 Sáng</option>
                                <option value="14:00:00">14:00 Chiều</option>
                                <option value="15:30:00">15:30 Chiều</option>
                                <option value="17:00:00">17:00 Chiều</option>
                                <option value="18:30:00">18:30 Tối</option>
                            </select>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-12">
                            <label for="note" class="form-label fw-semibold">Ghi chú triệu chứng hoặc yêu cầu khác</label>
                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Mô tả triệu chứng đau răng hoặc ghi chú thời gian thuận tiện nhất cho bạn..."></textarea>
                        </div>

                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-primary-clinic btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i> Đặt lịch ngay
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
