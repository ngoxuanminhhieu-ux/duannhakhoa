<?php
// web/admin/patients.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';

// Handle POST actions: Add, Edit, Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $full_name     = trim($_POST['full_name'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $address       = trim($_POST['address'] ?? '');

        if (empty($full_name) || empty($phone) || empty($email)) {
            $error = 'Vui lòng nhập Họ tên, Số điện thoại và Email.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO patients (full_name, phone, email, date_of_birth, address) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$full_name, $phone, $email, $date_of_birth, $address]);
                $message = 'Thêm bệnh nhân mới thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi: Số điện thoại hoặc email có thể đã tồn tại.';
            }
        }
    } elseif ($action === 'edit') {
        $id            = (int)($_POST['id'] ?? 0);
        $full_name     = trim($_POST['full_name'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $address       = trim($_POST['address'] ?? '');

        if ($id > 0 && !empty($full_name) && !empty($phone)) {
            try {
                $stmt = $pdo->prepare("UPDATE patients SET full_name = ?, phone = ?, email = ?, date_of_birth = ?, address = ? WHERE id = ?");
                $stmt->execute([$full_name, $phone, $email, $date_of_birth, $address, $id]);
                $message = 'Cập nhật thông tin bệnh nhân thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi cập nhật: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Đã xóa bệnh nhân khỏi hệ thống!';
            } catch (Exception $e) {
                $error = 'Lỗi khi xóa bệnh nhân: ' . $e->getMessage();
            }
        }
    }
}

// Fetch patients list with search keyword
$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE full_name LIKE ? OR phone LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM patients ORDER BY id DESC");
}
$patients = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Bệnh Nhân - Admin Nha Khoa</title>
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
                    <li class="nav-item"><a class="nav-link active" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a></li>
                </ul>
                <hr class="border-secondary my-4">
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Quản Lý Bệnh Nhân</h3>
                    <p class="text-muted small mb-0">Danh sách hồ sơ bệnh nhân khám tại phòng khám</p>
                </div>
                <button type="button" class="btn btn-primary-clinic" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                    <i class="fas fa-user-plus me-1"></i> Thêm Bệnh Nhân Mới
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

            <!-- Search Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <form action="patients.php" method="GET" class="row g-2">
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, số điện thoại hoặc email bệnh nhân..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Tìm kiếm</button>
                            <?php if (!empty($search)): ?>
                                <a href="patients.php" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Patients Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Họ và Tên</th>
                                <th>Số điện thoại</th>
                                <th>Email</th>
                                <th>Ngày sinh</th>
                                <th>Địa chỉ</th>
                                <th>Ngày tạo</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients)): ?>
                                <?php foreach ($patients as $p): ?>
                                    <tr>
                                        <td><strong>#<?php echo $p['id']; ?></strong></td>
                                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($p['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($p['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                                        <td><?php echo $p['date_of_birth'] ? date('d/m/Y', strtotime($p['date_of_birth'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($p['address'] ?? '-'); ?></td>
                                        <td><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($p['created_at'])); ?></small></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editPatientModal<?php echo $p['id']; ?>">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <form action="patients.php" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bệnh nhân này?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i> Xóa</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Patient Modal -->
                                    <div class="modal fade" id="editPatientModal<?php echo $p['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="patients.php" method="POST">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Chỉnh Sửa Bệnh Nhân #<?php echo $p['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Họ tên bệnh nhân</label>
                                                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($p['full_name']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Số điện thoại</label>
                                                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($p['phone']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Email</label>
                                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($p['email']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Ngày sinh</label>
                                                            <input type="date" name="date_of_birth" class="form-control" value="<?php echo $p['date_of_birth']; ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold">Địa chỉ</label>
                                                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($p['address'] ?? ''); ?>">
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Không tìm thấy bệnh nhân nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="patients.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm Bệnh Nhân Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Họ và tên bệnh nhân *</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Nguyễn Văn A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="0912..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ngày sinh</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" placeholder="Số nhà, Tên đường, Quận/Huyện">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm Bệnh Nhân</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
