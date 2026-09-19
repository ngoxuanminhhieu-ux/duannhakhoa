<?php
$current_page = 'services';
$page_title = 'Dịch Vụ Nha Khoa - Nha Khoa Smile';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY price ASC");
    $services = $stmt->fetchAll();
} catch (Exception $e) {
    $services = [];
}
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-dark) 100%);">
    <div class="container text-center py-4">
        <h1 class="display-4 fw-bold text-white mb-2">Dịch Vụ Nha Khoa Chuyên Sâu</h1>
        <p class="lead text-white-50">Giải pháp toàn diện cho sức khỏe và vẻ đẹp nụ cười của bạn</p>
    </div>
</div>

<div class="container mb-5 py-3">
    <div class="row g-4">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="service-card shadow-sm h-100">
                        <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" class="w-100" style="height: 220px; object-fit: cover;">
                        <div class="service-card-body p-4 d-flex flex-column">
                            <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($service['name']); ?></h4>
                            <p class="text-muted small mb-4 flex-grow-1"><?php echo htmlspecialchars($service['description']); ?></p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div>
                                    <small class="text-muted d-block">Giá tham khảo:</small>
                                    <span class="fs-5 fw-bold text-primary"><?php echo number_format($service['price'], 0, ',', '.'); ?> VNĐ</span>
                                </div>
                                <a href="booking.php?service_id=<?php echo $service['id']; ?>" class="btn btn-primary-clinic btn-sm">
                                    Đặt lịch ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có thông tin dịch vụ.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
