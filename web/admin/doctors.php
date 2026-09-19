<?php
// web/admin/doctors.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';

// Handle POST actions: Add, Edit, Delete Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $full_name   = trim($_POST['full_name'] ?? '');
        $specialty   = trim($_POST['specialty'] ?? '');
        $experience  = trim($_POST['experience'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image       = trim($_POST['image'] ?? '');

        if (empty($full_name) || empty($specialty)) {
            $error = 'Vui lòng nhập Họ tên bác sĩ và Chuyên khoa.';
        } else {
            if (empty($image)) {
                $image = 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&auto=format&fit=crop&q=80';
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO doctors (full_name, specialty, experience, description, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$full_name, $specialty, $experience, $description, $image]);
                $message = 'Thêm bác sĩ thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi khi thêm bác sĩ: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'edit') {
        $id          = (int)($_POST['id'] ?? 0);
        $full_name   = trim($_POST['full_name'] ?? '');
        $specialty   = trim($_POST['specialty'] ?? '');
        $experience  = trim($_POST['experience'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image       = trim($_POST['image'] ?? '');

        if ($id > 0 && !empty($full_name) && !empty($specialty)) {
            try {
                $stmt = $pdo->prepare("UPDATE doctors SET full_name = ?, specialty = ?, experience = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([$full_name, $specialty, $experience, $description, $image, $id]);
                $message = 'Cập nhật thông tin bác sĩ thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi khi cập nhật: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Đã xóa bác sĩ khỏi hệ thống!';
            } catch (Exception $e) {
                $error = 'Lỗi khi xóa bác sĩ: ' . $e->getMessage();
            }
        }
    }
}

// Fetch doctors
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bác Sĩ - Admin Nha Khoa</title>
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 px-0 admin-sidebar">
            <div class="p-3 text-center border-bottom border-secondary">
                <a href="../index.php" class="text-white text-decoration-none d-block fw-bold fs-5">
                    <i class="fas fa-tooth text-info me-2"></i> SMILE ADMIN
                </a>
            </div>
            <div class="p-3">
                <small class="text-uppercase text-muted fw-bold d-block mb-3 px-2">Quản lý hệ thống</small>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointments.php"><i class="far fa-calendar-check me-2"></i> Lịch Hẹn</a></li>
                    <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a></li>
                    <li class="nav-item"><a class="nav-link active" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a></li>
                </ul>
                <hr class="border-secondary my-4">
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Quản Lý Đội Ngũ Bác Sĩ</h3>
                    <p class="text-muted small mb-0">Danh sách các bác sĩ nha khoa tại phòng khám</p>
                </div>
                <button type="button" class="btn btn-primary-clinic" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                    <i class="fas fa-plus me-1"></i> Thêm Bác Sĩ Mới
                </button>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Doctors Grid/Table -->
            <div class="row g-4">
                <?php foreach ($doctors as $doc): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                            <div class="row g-0 align-items-center">
                                <div class="col-4">
                                    <img src="<?php echo htmlspecialchars($doc['image']); ?>" class="img-fluid rounded-start h-100" style="object-fit: cover; min-height: 160px;" alt="Doctor">
                                </div>
                                <div class="col-8">
                                    <div class="card-body py-3 pe-3">
                                        <span class="badge bg-primary-subtle text-primary fw-bold mb-1"><?php echo htmlspecialchars($doc['specialty']); ?></span>
                                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($doc['full_name']); ?></h5>
                                        <p class="text-muted small mb-2"><i class="fas fa-award me-1 text-warning"></i> <?php echo htmlspecialchars($doc['experience']); ?></p>
                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDoctorModal<?php echo $doc['id']; ?>">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <form action="doctors.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bác sĩ này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i> Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Doctor Modal -->
                    <div class="modal fade" id="editDoctorModal<?php echo $doc['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="doctors.php" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?php echo $doc['id']; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Chỉnh Sửa Bác Sĩ #<?php echo $doc['id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Họ và tên bác sĩ *</label>
                                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($doc['full_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Chuyên khoa *</label>
                                            <input type="text" name="specialty" class="form-control" value="<?php echo htmlspecialchars($doc['specialty']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Số năm kinh nghiệm</label>
                                            <input type="text" name="experience" class="form-control" value="<?php echo htmlspecialchars($doc['experience']); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Mô tả tiểu sử</label>
                                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($doc['description']); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">URL Hình ảnh</label>
                                            <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($doc['image']); ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <button type="submit" class="btn btn-primary">Lưu cập nhật</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="doctors.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm Bác Sĩ Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên bác sĩ *</label>
                        <input type="text" name="full_name" class="form-control" placeholder="ThS. BS. Nguyễn Văn A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chuyên khoa *</label>
                        <input type="text" name="specialty" class="form-control" placeholder="Ví dụ: Răng Hàm Mặt, Cấy Ghép Implant" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kinh nghiệm</label>
                        <input type="text" name="experience" class="form-control" placeholder="Ví dụ: 10 năm kinh nghiệm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả tiểu sử</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tốt nghiệp Y Dược, chứng chỉ chuyên khoa..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL Hình ảnh</label>
                        <input type="text" name="image" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm Bác Sĩ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
