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
    <title>Bảng Điều Khiển Admin - Nha Khoa Smile</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
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
                </ul>
                <hr class="border-secondary my-4">
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Dashboard Thống Kê</h3>
                    <p class="text-muted small mb-0">Xin chào, <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></strong>! Hôm nay là <?php echo date('d/m/Y'); ?>.</p>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i> Xem Website
                </a>
            </div>

            <!-- KPI Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-xl">
                    <div class="admin-kpi-card">
                        <div>
                            <span class="text-muted small d-block">Tổng Bệnh Nhân</span>
                            <h2 class="fw-bold mb-0 text-primary"><?php echo number_format($total_patients); ?></h2>
                        </div>
                        <div class="admin-kpi-icon bg-primary-subtle text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl">
                    <div class="admin-kpi-card">
                        <div>
                            <span class="text-muted small d-block">Tổng Lịch Hẹn</span>
                            <h2 class="fw-bold mb-0 text-info"><?php echo number_format($total_appointments); ?></h2>
                        </div>
                        <div class="admin-kpi-icon bg-info-subtle text-info">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl">
                    <div class="admin-kpi-card">
                        <div>
                            <span class="text-muted small d-block">Lịch Hẹn Hôm Nay</span>
                            <h2 class="fw-bold mb-0 text-warning"><?php echo number_format($today_appointments); ?></h2>
                        </div>
                        <div class="admin-kpi-icon bg-warning-subtle text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl">
                    <div class="admin-kpi-card">
                        <div>
                            <span class="text-muted small d-block">Tổng Bác Sĩ</span>
                            <h2 class="fw-bold mb-0 text-success"><?php echo number_format($total_doctors); ?></h2>
                        </div>
                        <div class="admin-kpi-icon bg-success-subtle text-success">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl">
                    <div class="admin-kpi-card">
                        <div>
                            <span class="text-muted small d-block">Dịch Vụ Nha Khoa</span>
                            <h2 class="fw-bold mb-0 text-purple" style="color: #8b5cf6;"><?php echo number_format($total_services); ?></h2>
                        </div>
                        <div class="admin-kpi-icon bg-purple-subtle text-purple" style="background-color: #f3e8ff; color: #8b5cf6;">
                            <i class="fas fa-teeth"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i> Lịch Hẹn Gần Đây</h5>
                    <a href="appointments.php" class="btn btn-primary btn-sm">Xem tất cả lịch hẹn</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Bệnh nhân</th>
                                <th>Số điện thoại</th>
                                <th>Dịch vụ</th>
                                <th>Bác sĩ</th>
                                <th>Ngày & Giờ</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_appointments)): ?>
                                <?php foreach ($recent_appointments as $app): ?>
                                    <tr>
                                        <td><strong>#<?php echo $app['id']; ?></strong></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($app['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['patient_phone']); ?></td>
                                        <td><?php echo htmlspecialchars($app['service_name'] ?? 'Khám tổng quát'); ?></td>
                                        <td><?php echo htmlspecialchars($app['doctor_name'] ?? 'Chưa phân công'); ?></td>
                                        <td>
                                            <div><i class="far fa-calendar me-1 text-muted"></i><?php echo date('d/m/Y', strtotime($app['appointment_date'])); ?></div>
                                            <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo date('H:i', strtotime($app['appointment_time'])); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                $status = $app['status'];
                                                $badge_class = 'badge-pending';
                                                $status_label = 'Chờ xác nhận';
                                                if ($status == 'confirmed') { $badge_class = 'badge-confirmed'; $status_label = 'Đã xác nhận'; }
                                                elseif ($status == 'completed') { $badge_class = 'badge-completed'; $status_label = 'Đã hoàn thành'; }
                                                elseif ($status == 'cancelled') { $badge_class = 'badge-cancelled'; $status_label = 'Đã hủy'; }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> px-3 py-2 rounded-pill"><?php echo $status_label; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Chưa có lịch hẹn nào.</td>
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
