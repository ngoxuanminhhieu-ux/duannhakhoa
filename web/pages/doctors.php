<?php
$current_page = 'doctors';
$page_title = 'Đội Ngũ Bác Sĩ - Nha Khoa Smile';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM doctors ORDER BY id ASC");
    $doctors = $stmt->fetchAll();
} catch (Exception $e) {
    $doctors = [];
}
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-dark) 100%);">
    <div class="container text-center py-4">
        <h1 class="display-4 fw-bold text-white mb-2">Đội Ngũ Bác Sĩ Chuyên Khoa</h1>
        <p class="lead text-white-50">Chuyên môn cao - Tận tâm - Giàu kinh nghiệm điều trị thực tế</p>
    </div>
</div>

<div class="container mb-5 py-3">
    <div class="row g-4">
        <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="doctor-card h-100 shadow-sm border">
                        <div class="doctor-img-wrapper">
                            <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-100" style="height: 300px; object-fit: cover;">
                        </div>
                        <div class="doctor-body p-4">
                            <span class="badge bg-primary-subtle text-primary fw-bold mb-2"><?php echo htmlspecialchars($doctor['specialty']); ?></span>
                            <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($doctor['full_name']); ?></h4>
                            <p class="text-muted small fw-semibold mb-3"><i class="fas fa-clock me-1 text-accent"></i> <?php echo htmlspecialchars($doctor['experience']); ?></p>
                            <p class="text-muted small mb-4"><?php echo htmlspecialchars($doctor['description']); ?></p>
                            <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-outline-clinic btn-sm w-100">
                                <i class="far fa-calendar-alt me-1"></i> Đặt lịch hẹn bác sĩ
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có thông tin bác sĩ.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
