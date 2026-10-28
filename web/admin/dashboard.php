<?php
// web/admin/dashboard.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';

// Calculate KPI Metrics
try {
    $total_patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $total_appointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $today_appointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURRENT_DATE()")->fetchColumn();
    $total_doctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $total_services = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $total_revenue = (float)$pdo->query("SELECT SUM(s.price) FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.status = 'completed'")->fetchColumn();

    // Recent 10 Appointments
    $stmt_recent = $pdo->query("
        SELECT a.*, p.full_name as patient_name, p.phone as patient_phone, 
               d.full_name as doctor_name, s.name as service_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN services s ON a.service_id = s.id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
        LIMIT 10
    ");
    $recent_appointments = $stmt_recent->fetchAll();

} catch (Exception $e) {
    $total_patients = $total_appointments = $today_appointments = $total_doctors = $total_services = 0;
    $recent_appointments = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển Quản Trị - Premier Dental Clinic</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="revenue.php"><i class="fas fa-chart-pie me-2 text-warning"></i> Doanh Thu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php"><i class="far fa-calendar-check me-2"></i> Lịch Hẹn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ai_assistant.php"><i class="fas fa-robot text-info me-2"></i> Trợ Lý AI Admin</a>
                    </li>
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
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1 text-dark-navy">Bảng Điều Khiển Quản Trị</h3>
                    <p class="text-muted small mb-0">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Quản trị viên'); ?></strong>! Hôm nay là <?php echo date('d/m/Y'); ?>.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="appointments.php" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-plus me-1"></i> Quản lý lịch hẹn
                    </a>
                </div>
            </div>

            <!-- KPI Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-xl">
                    <a href="patients.php" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Tổng Bệnh Nhân</span>
                                <div class="kpi-val text-primary"><?php echo number_format($total_patients); ?></div>
                            </div>
                            <div class="kpi-icon-box bg-primary-subtle text-primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl">
                    <a href="appointments.php" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Tổng Lịch Hẹn</span>
                                <div class="kpi-val text-info"><?php echo number_format($total_appointments); ?></div>
                            </div>
                            <div class="kpi-icon-box bg-info-subtle text-info">
                                <i class="far fa-calendar-alt"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 col-xl">
                    <a href="appointments.php?date=<?php echo date('Y-m-d'); ?>" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Lịch Hẹn Hôm Nay</span>
                                <div class="kpi-val text-warning"><?php echo number_format($today_appointments); ?></div>
                            </div>
                            <div class="kpi-icon-box bg-warning-subtle text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl">
                    <a href="doctors.php" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Đội Ngũ Bác Sĩ</span>
                                <div class="kpi-val text-success"><?php echo number_format($total_doctors); ?></div>
                            </div>
                            <div class="kpi-icon-box bg-success-subtle text-success">
                                <i class="fas fa-user-md"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl">
                    <a href="services.php" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Dịch Vụ Niêm Yết</span>
                                <div class="kpi-val text-purple" style="color: #8b5cf6;"><?php echo number_format($total_services); ?></div>
                            </div>
                            <div class="kpi-icon-box" style="background-color: #f3e8ff; color: #8b5cf6;">
                                <i class="fas fa-tooth"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-xl">
                    <a href="revenue.php" class="kpi-card-link">
                        <div class="kpi-card">
                            <div>
                                <span class="kpi-label">Tổng Thực Thu</span>
                                <div class="kpi-val text-success" style="font-size: 1.45rem;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> ₫</div>
                            </div>
                            <div class="kpi-icon-box bg-success-subtle text-success">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Appointments Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="fw-bold mb-0 text-dark-navy">
                        <i class="fas fa-list-alt me-2 text-primary"></i> 10 Lịch Hẹn Đăng Ký Mới Nhất
                    </h5>
                    <a href="appointments.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        Xem tất cả lịch hẹn &rarr;
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Mã #</th>
                                <th>Bệnh nhân</th>
                                <th>Số điện thoại</th>
                                <th>Dịch vụ yêu cầu</th>
                                <th>Bác sĩ chỉ định</th>
                                <th>Thời gian hẹn</th>
                                <th class="pe-4">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_appointments)): ?>
                                <?php foreach ($recent_appointments as $app): ?>
                                    <tr>
                                        <td class="ps-4"><strong class="text-primary">#<?php echo $app['id']; ?></strong></td>
                                        <td><div class="fw-bold text-dark-navy"><?php echo htmlspecialchars($app['patient_name']); ?></div></td>
                                        <td>
                                            <a href="tel:<?php echo htmlspecialchars($app['patient_phone']); ?>" class="text-primary fw-semibold small text-decoration-none">
                                                <i class="fas fa-phone-alt me-1"></i><?php echo htmlspecialchars($app['patient_phone']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge-service-tag">
                                                <i class="fas fa-tooth me-1"></i><?php echo htmlspecialchars($app['service_name'] ?? 'Khám & Tư vấn tổng quát'); ?>
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
                                        <td class="pe-4">
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="far fa-folder-open fs-2 mb-2 d-block"></i>
                                        Chưa có dữ liệu lịch hẹn nào trong hệ thống.
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
