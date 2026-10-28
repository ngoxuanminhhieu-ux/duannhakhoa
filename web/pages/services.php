<?php
$current_page = 'services';
$page_title = 'Bảng Giá & Dịch Vụ Nha Khoa Chuyên Sâu - Premier Dental';
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

<!-- Banner Header -->
<div class="page-header-light">
    <div class="container text-center">
        <span class="badge-page-tag mb-3">
            <i class="fas fa-tags text-primary me-1"></i> Bảng Giá Minh Bạch 2026
        </span>
        <h1 class="page-header-title">Dịch Vụ Nha Khoa Chuyên Sâu</h1>
        <p class="page-header-desc">Cam kết bảng giá niêm yết công khai, không phát sinh chi phí phụ, chính sách bảo hành chính hãng lên tới 20 năm.</p>
    </div>
</div>

<div class="container my-5 py-3">
    <!-- Cam kết bảng giá -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm">
                <i class="fas fa-check-double text-success fs-3"></i>
                <div>
                    <strong class="d-block text-dark-navy">Không Phát Sinh Chi Phí</strong>
                    <small class="text-muted">Báo giá chi tiết 100% trước khi điều trị</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm">
                <i class="fas fa-award text-warning fs-3"></i>
                <div>
                    <strong class="d-block text-dark-navy">Bảo Hành Điện Tử</strong>
                    <small class="text-muted">Tra cứu mã thẻ bảo hành trên hệ thống toàn quốc</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-white rounded-4 border d-flex align-items-center gap-3 shadow-sm">
                <i class="fas fa-credit-card text-primary fs-3"></i>
                <div>
                    <strong class="d-block text-dark-navy">Trả Góp 0% Lãi Suất</strong>
                    <small class="text-muted">Áp dụng cho dịch vụ Niềng răng & Implant</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Categories -->
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
        <button class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold service-filter-btn active" data-filter="all">Tất cả dịch vụ (10)</button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold service-filter-btn" data-filter="tongquat">Tổng quát & Phòng ngừa</button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold service-filter-btn" data-filter="dieutri">Điều trị & Tiểu phẫu</button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold service-filter-btn" data-filter="thammy">Thẩm mỹ & Phục hình</button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 fw-semibold service-filter-btn" data-filter="chinhnha">Chỉnh nha & Implant</button>
    </div>

    <!-- Services Grid -->
    <div class="row g-4" id="servicesGrid">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): 
                $imgUrl = $service['image'];
                if (empty($imgUrl)) {
                    $imgUrl = 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=600&auto=format&fit=crop&q=80';
                }
                
                // Determine Category Tag
                $sId = (int)$service['id'];
                $filterCategory = 'tongquat';
                if (in_array($sId, [1, 2])) {
                    $filterCategory = 'tongquat';
                } elseif (in_array($sId, [3, 4, 5])) {
                    $filterCategory = 'dieutri';
                } elseif (in_array($sId, [6, 7, 8])) {
                    $filterCategory = 'thammy';
                } elseif (in_array($sId, [9, 10])) {
                    $filterCategory = 'chinhnha';
                }
            ?>
                <div class="col-md-6 col-lg-4 service-col-item" data-category="<?php echo $filterCategory; ?>">
                    <div class="service-card shadow-sm h-100">
                        <a href="service_detail.php?id=<?php echo $service['id']; ?>" class="service-img-wrapper d-block" title="Xem chi tiết <?php echo htmlspecialchars($service['name']); ?>">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" loading="lazy">
                            <div class="service-badge-warranty">
                                <i class="fas fa-shield-alt text-info me-1"></i> Chuẩn ISO 9001
                            </div>
                        </a>
                        <div class="service-card-body">
                            <h4 class="service-title mb-2">
                                <a href="service_detail.php?id=<?php echo $service['id']; ?>" class="service-title-link">
                                    <?php echo htmlspecialchars($service['name']); ?>
                                </a>
                            </h4>
                            <p class="service-desc"><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <div class="service-footer flex-column align-items-stretch gap-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="service-price-label">Giá niêm yết:</span>
                                    <span class="service-price-val"><?php echo number_format($service['price'], 0, ',', '.'); ?>đ</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <a href="service_detail.php?id=<?php echo $service['id']; ?>" class="btn-service-view-detail flex-grow-1 text-center">
                                        <i class="fas fa-info-circle me-1"></i> Quy trình & Tác dụng
                                    </a>
                                    <a href="booking.php?service_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" title="Đặt lịch hẹn ngay">
                                        <i class="far fa-calendar-alt me-1"></i> Đặt lịch
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 border">
                    <i class="fas fa-stethoscope text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">Đang cập nhật danh mục dịch vụ...</h5>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.service-filter-btn');
    const serviceItems = document.querySelectorAll('.service-col-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary');

            const filterValue = this.getAttribute('data-filter');

            serviceItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
