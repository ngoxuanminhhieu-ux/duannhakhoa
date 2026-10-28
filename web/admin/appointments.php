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
                $message = "Đã cập nhật trạng thái lịch hẹn #" . $id . " thành công.";
            } catch (Exception $e) {
                $error = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
            }
        } elseif ($action === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Đã xóa lịch hẹn #" . $id . " khỏi hệ thống!";
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
    $sql .= " AND (p.full_name LIKE ? OR p.phone LIKE ? OR a.id LIKE ?)";
    $params[] = "%$search%";
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
    <title>Quản Lý Lịch Hẹn - Premier Dental Admin</title>
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 px-0 admin-sidebar">
            <div class="p-3 text-center border-bottom border-secondary border-opacity-25">
                <a href="../index.php" class="text-white text-decoration-none d-flex align-items-center justify-content-center gap-2 fw-bold fs-5">
                    <div class="brand-logo-icon" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <span>PREMIER ADMIN</span>
                </a>
            </div>
            <div class="p-3">
                <small class="text-uppercase text-muted fw-bold d-block mb-3 px-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Quản lý hệ thống</small>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="revenue.php"><i class="fas fa-chart-pie me-2 text-warning"></i> Doanh Thu</a></li>
                    <li class="nav-item"><a class="nav-link active" href="appointments.php"><i class="far fa-calendar-check me-2"></i> Lịch Hẹn</a></li>
                    <li class="nav-item"><a class="nav-link" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a></li>
                    <li class="nav-item"><a class="nav-link" href="ai_assistant.php"><i class="fas fa-robot text-info me-2"></i> Trợ Lý AI Admin</a></li>
                </ul>
                <hr class="border-secondary border-opacity-25 my-4">
                <a href="../index.php" target="_blank" class="btn btn-outline-info w-100 btn-sm mb-2 rounded-pill">
                    <i class="fas fa-external-link-alt me-1"></i> Xem Website
                </a>
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm rounded-pill">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1 text-dark-navy">Quản Lý Lịch Hẹn Khám Bệnh</h3>
                    <p class="text-muted small mb-0">Theo dõi, duyệt lịch khám, phân công bác sĩ và đổi trạng thái tiếp đón</p>
                </div>
                <a href="../pages/booking.php" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-plus me-1"></i> Đặt lịch mới (Form Khách)
                </a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Filter & Search Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-3">
                    <form action="appointments.php" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Tìm tên bệnh nhân, SĐT, mã ID..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="pending" <?php echo ($status_filter === 'pending') ? 'selected' : ''; ?>>⏳ Chờ xác nhận (Pending)</option>
                                <option value="confirmed" <?php echo ($status_filter === 'confirmed') ? 'selected' : ''; ?>>✅ Đã xác nhận (Confirmed)</option>
                                <option value="completed" <?php echo ($status_filter === 'completed') ? 'selected' : ''; ?>>🏆 Đã hoàn tất (Completed)</option>
                                <option value="cancelled" <?php echo ($status_filter === 'cancelled') ? 'selected' : ''; ?>>❌ Đã hủy (Cancelled)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill"><i class="fas fa-filter me-1"></i> Lọc</button>
                            <a href="appointments.php" class="btn btn-outline-secondary rounded-pill" title="Đặt lại bộ lọc"><i class="fas fa-undo"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Appointments Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0 text-dark-navy">
                        <i class="far fa-calendar-check text-primary me-2"></i> Danh Sách Lịch Hẹn (<?php echo count($appointments); ?> kết quả)
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mã #</th>
                                <th>Bệnh nhân</th>
                                <th>Liên hệ</th>
                                <th>Dịch vụ khám</th>
                                <th>Bác sĩ</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $app): ?>
                                    <tr>
                                        <td class="ps-4"><strong>#<?php echo $app['id']; ?></strong></td>
                                        <td>
                                            <div class="fw-bold text-dark-navy"><?php echo htmlspecialchars($app['patient_name']); ?></div>
                                            <?php if (!empty($app['note'])): ?>
                                                <small class="text-muted text-truncate d-block max-w-200" title="<?php echo htmlspecialchars($app['note']); ?>">
                                                    <i class="far fa-comment-dots text-primary me-1"></i><?php echo htmlspecialchars($app['note']); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><a href="tel:<?php echo htmlspecialchars($app['patient_phone']); ?>" class="text-primary fw-semibold"><i class="fas fa-phone-alt me-1 small"></i><?php echo htmlspecialchars($app['patient_phone']); ?></a></div>
                                            <?php if (!empty($app['patient_email'])): ?>
                                                <small class="text-muted"><i class="far fa-envelope me-1"></i><?php echo htmlspecialchars($app['patient_email']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-service-tag">
                                                <i class="fas fa-tooth me-1"></i><?php echo htmlspecialchars($app['service_name'] ?? 'Khám tổng quát'); ?>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="doctor-avatar-xs"><i class="fas fa-user-md"></i></div>
                                                <span class="small fw-semibold text-dark"><?php echo htmlspecialchars($app['doctor_name'] ?? 'Lễ tân xếp lịch'); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <div class="small fw-semibold text-dark-navy"><i class="far fa-calendar-alt text-primary me-1"></i><?php echo date('d/m/Y', strtotime($app['appointment_date'])); ?></div>
                                            <small class="text-muted"><i class="far fa-clock text-info me-1"></i><?php echo date('H:i', strtotime($app['appointment_time'])); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                $status = $app['status'];
                                                $badge_class = 'pending';
                                                $status_icon = 'fa-clock';
                                                $status_label = 'Chờ xác nhận';
                                                if ($status == 'confirmed') { $badge_class = 'confirmed'; $status_icon = 'fa-check-circle'; $status_label = 'Đã xác nhận'; }
                                                elseif ($status == 'completed') { $badge_class = 'completed'; $status_icon = 'fa-check-double'; $status_label = 'Đã hoàn tất'; }
                                                elseif ($status == 'cancelled') { $badge_class = 'cancelled'; $status_icon = 'fa-ban'; $status_label = 'Đã hủy'; }
                                            ?>
                                            <span class="badge-status <?php echo $badge_class; ?>">
                                                <i class="fas <?php echo $status_icon; ?>"></i> <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-action-group justify-content-end">
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn-action btn-action-status dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-sliders-h me-1 text-primary"></i> Trạng thái
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2">
                                                        <li>
                                                            <form method="POST" action="appointments.php">
                                                                <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                                <input type="hidden" name="action" value="confirmed">
                                                                <button type="submit" class="dropdown-item py-2 rounded-2 text-primary fw-semibold"><i class="fas fa-check me-2"></i> Xác nhận lịch hẹn</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="appointments.php">
                                                                <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                                <input type="hidden" name="action" value="completed">
                                                                <button type="submit" class="dropdown-item py-2 rounded-2 text-success fw-semibold"><i class="fas fa-check-double me-2"></i> Đã khám xong</button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="appointments.php">
                                                                <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                                <input type="hidden" name="action" value="cancelled">
                                                                <button type="submit" class="dropdown-item py-2 rounded-2 text-warning fw-semibold"><i class="fas fa-times me-2"></i> Hủy lịch hẹn</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <form method="POST" action="appointments.php" class="d-inline m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn lịch hẹn này không?');">
                                                    <input type="hidden" name="id" value="<?php echo $app['id']; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn-action btn-action-delete" title="Xóa lịch hẹn"><i class="fas fa-trash-alt"></i> Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="far fa-folder-open fs-2 mb-2 d-block"></i>
                                        Không tìm thấy lịch hẹn nào phù hợp với điều kiện tìm kiếm.
                                    </td>
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
<?php include __DIR__ . '/chatbot_widget.php'; ?>
</body>
</html>
