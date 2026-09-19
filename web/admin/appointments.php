<?php
// web/admin/appointments.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';

// Handle Status Updates or Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        if (in_array($action, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            try {
                $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
                $stmt->execute([$action, $id]);
                $message = "Đã cập nhật trạng thái lịch hẹn thành '" . strtoupper($action) . "'.";
            } catch (Exception $e) {
                $error = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
            }
        } elseif ($action === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Đã xóa lịch hẹn khỏi hệ thống!";
            } catch (Exception $e) {
                $error = "Lỗi khi xóa lịch hẹn: " . $e->getMessage();
            }
        }
    }
}

// Filters & Search
$status_filter = trim($_GET['status'] ?? '');
$date_filter   = trim($_GET['date'] ?? '');
$search        = trim($_GET['search'] ?? '');

$sql = "
    SELECT a.*, p.full_name as patient_name, p.phone as patient_phone, p.email as patient_email,
           d.full_name as doctor_name, s.name as service_name, s.price as service_price
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    LEFT JOIN services s ON a.service_id = s.id
    WHERE 1=1
";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND a.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_filter)) {
    $sql .= " AND a.appointment_date = ?";
    $params[] = $date_filter;
}

if (!empty($search)) {
    $sql .= " AND (p.full_name LIKE ? OR p.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Lịch Hẹn - Admin Nha Khoa</title>
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
                    <li class="nav-item"><a class="nav-link active" href="appointments.php"><i class="far fa-calendar-check me-2"></i> Lịch Hẹn</a></li>
                    <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a></li>
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
                    <h3 class="fw-bold mb-0">Quản Lý Lịch Hẹn Khám</h3>
                    <p class="text-muted small mb-0">Xem và cập nhật trạng thái lịch hẹn của bệnh nhân</p>
                </div>
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

            <!-- Filter & Search Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <form action="appointments.php" method="GET" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Tìm tên hoặc số điện thoại..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>Chờ xác nhận (Pending)</option>
                                <option value="confirmed" <?php echo ($status_filter === 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận (Confirmed)</option>
                                <option value="completed" <?php echo ($status_filter === 'completed') ? 'selected' : ''; ?>>Đã hoàn thành (Completed)</option>
                                <option value="cancelled" <?php echo ($status_filter === 'cancelled') ? 'selected' : ''; ?>>Đã hủy (Cancelled)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Lọc</button>
                            <a href="appointments.php" class="btn btn-outline-secondary"><i class="fas fa-undo"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Bệnh nhân</th>
                                <th>Số điện thoại</th>
                                <th>Dịch vụ yêu cầu</th>
                                <th>Bác sĩ chọn</th>
                                <th>Ngày & Giờ</th>
                                <th>Ghi chú</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Cập nhật trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $app): ?>
                                    <tr>
                                        <td><strong>#<?php echo $app['id']; ?></strong></td>
                                        <td class="fw-semibold text-primary"><?php echo htmlspecialchars($app['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['patient_phone']); ?></td>
                                        <td><?php echo htmlspecialchars($app['service_name'] ?? 'Khám tổng quát'); ?></td>
                                        <td><?php echo htmlspecialchars($app['doctor_name'] ?? 'Tự động phân công'); ?></td>
                                        <td>
                                            <div class="fw-bold"><i class="far fa-calendar me-1 text-muted"></i><?php echo date('d/m/Y', strtotime($app['appointment_date'])); ?></div>
                                            <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo date('H:i', strtotime($app['appointment_time'])); ?></small>
                                        </td>
                                        <td><small class="text-muted"><?php echo htmlspecialchars($app['note'] ?? '-'); ?></small></td>
                                        <td>
                                            <?php 
                                                $status = $app['status'];
                                                $badge_class = 'badge-pending';
                                                $status_label = 'Chờ xác nhận';
                                                if ($status == 'confirmed') { $badge_class = 'badge-confirmed'; $status_label = 'Đã xác nhận'; }
                                                elseif ($status == 'completed') { $badge_class = 'badge-completed'; $status_label = 'Hoàn thành'; }
                                                elseif ($status == 'cancelled') { $badge_class = 'badge-cancelled'; $status_label = 'Đã hủy'; }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> px-3 py-2 rounded-pill"><?php echo $status_label; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Đổi trạng thái
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <form action="appointments.php" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                            <input type="hidden" name="action" value="confirmed">
                                                            <button type="submit" class="dropdown-item text-primary"><i class="fas fa-check-circle me-2"></i> Xác nhận lịch</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="appointments.php" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                            <input type="hidden" name="action" value="completed">
                                                            <button type="submit" class="dropdown-item text-success"><i class="fas fa-check-double me-2"></i> Hoàn thành khám</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="appointments.php" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                            <input type="hidden" name="action" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-warning"><i class="fas fa-ban me-2"></i> Hủy lịch hẹn</button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="appointments.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa lịch hẹn này?');">
                                                            <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Xóa lịch hẹn</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Không tìm thấy lịch hẹn phù hợp.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
