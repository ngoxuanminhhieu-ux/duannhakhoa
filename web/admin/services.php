<?php
// web/admin/services.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';

// Handle POST actions: Add, Edit, Delete Service
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name        = trim($_POST['name'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image       = trim($_POST['image'] ?? '');

        if (empty($name) || $price <= 0) {
            $error = 'Vui lòng nhập Tên dịch vụ và Giá hợp lệ.';
        } else {
            if (empty($image)) {
                $image = 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop&q=80';
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO services (name, price, description, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $description, $image]);
                $message = 'Thêm dịch vụ nha khoa mới thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi khi thêm dịch vụ: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'edit') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $price       = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $image       = trim($_POST['image'] ?? '');

        if ($id > 0 && !empty($name) && $price > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE services SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $price, $description, $image, $id]);
                $message = 'Cập nhật dịch vụ thành công!';
            } catch (Exception $e) {
                $error = 'Lỗi khi cập nhật dịch vụ: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Đã xóa dịch vụ khỏi hệ thống!';
            } catch (Exception $e) {
                $error = 'Lỗi khi xóa dịch vụ: ' . $e->getMessage();
            }
        }
    }
}

// Fetch Services
$services = $pdo->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Dịch Vụ - Admin Nha Khoa</title>
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
                    <li class="nav-item"><a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a></li>
                </ul>
                <hr class="border-secondary my-4">
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Đăng xuất</a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Quản Lý Dịch Vụ Nha Khoa</h3>
                    <p class="text-muted small mb-0">Danh sách các dịch vụ và bảng giá niêm yết</p>
                </div>
                <button type="button" class="btn btn-primary-clinic" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="fas fa-plus me-1"></i> Thêm Dịch Vụ Mới
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

            <!-- Services Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên dịch vụ</th>
                                <th>Giá tham khảo</th>
                                <th>Mô tả chi tiết</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $svc): ?>
                                <tr>
                                    <td><strong>#<?php echo $svc['id']; ?></strong></td>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($svc['image']); ?>" width="60" height="50" class="rounded object-fit-cover" alt="Service">
                                    </td>
                                    <td class="fw-bold text-primary"><?php echo htmlspecialchars($svc['name']); ?></td>
                                    <td class="fw-bold text-success fs-6"><?php echo number_format($svc['price'], 0, ',', '.'); ?> VNĐ</td>
                                    <td><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($svc['description'], 0, 90, '...')); ?></small></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editServiceModal<?php echo $svc['id']; ?>">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                        <form action="services.php" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa dịch vụ này?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $svc['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i> Xóa</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Service Modal -->
                                <div class="modal fade" id="editServiceModal<?php echo $svc['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="services.php" method="POST">
                                                <input type="hidden" name="action" value="edit">
                                                <input type="hidden" name="id" value="<?php echo $svc['id']; ?>">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Sửa Dịch Vụ #<?php echo $svc['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Tên dịch vụ *</label>
                                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($svc['name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Giá niêm yết (VNĐ) *</label>
                                                        <input type="number" name="price" class="form-control" value="<?php echo $svc['price']; ?>" step="1000" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Mô tả dịch vụ</label>
                                                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($svc['description']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">URL Hình ảnh</label>
                                                        <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($svc['image']); ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="services.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm Dịch Vụ Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên dịch vụ *</label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Tẩy trắng răng Laser" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá tham khảo (VNĐ) *</label>
                        <input type="number" name="price" class="form-control" placeholder="2000000" step="1000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả dịch vụ</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Mô tả công nghệ, quy trình thực hiện..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL Hình ảnh</label>
                        <input type="text" name="image" class="form-control" placeholder="https://images.unsplash.com/...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm Dịch Vụ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
