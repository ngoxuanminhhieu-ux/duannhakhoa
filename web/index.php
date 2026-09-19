<?php
$current_page = 'home';
$page_title = 'Nha Khoa Smile - Chăm Sóc Nụ Cười, Kiến Tạo Tự Tin';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Fetch services and doctors from MySQL database
try {
    $stmt_services = $pdo->query("SELECT * FROM services ORDER BY id ASC LIMIT 8");
    $services = $stmt_services->fetchAll();

    $stmt_doctors = $pdo->query("SELECT * FROM doctors ORDER BY id ASC LIMIT 5");
    $doctors = $stmt_doctors->fetchAll();
} catch (Exception $e) {
    $services = [];
    $doctors = [];
}
?>

<!-- 1. Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="fas fa-shield-alt"></i> Tiêu Chuẩn Nha Khoa Quốc Tế
                </div>
                <h1 class="hero-title">Chăm sóc nụ cười – Kiến tạo tự tin</h1>
                <p class="lead text-muted mb-4">
                    Nha Khoa Smile cung cấp các giải pháp chăm sóc và điều trị răng miệng toàn diện với công nghệ hiện đại không đau, giúp bạn sở hữu nụ cười rạng rỡ và khỏe mạnh nhất.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="pages/booking.php" class="btn btn-primary-clinic btn-lg">
                        <i class="far fa-calendar-check"></i> Đặt lịch ngay
                    </a>
                    <a href="pages/services.php" class="btn btn-outline-clinic btn-lg">
                        <i class="fas fa-stethoscope"></i> Tìm hiểu dịch vụ
                    </a>
                </div>
                
                <div class="row mt-5 pt-3 g-3 border-top">
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">15.000+</h3>
                        <small class="text-muted">Khách hàng tin dùng</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">15+</h3>
                        <small class="text-muted">Năm kinh nghiệm</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold text-primary mb-0">99.8%</h3>
                        <small class="text-muted">Hài lòng tuyệt đối</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=800&auto=format&fit=crop&q=80" alt="Phòng khám nha khoa hiện đại" class="img-fluid">
                    <div class="hero-experience-card">
                        <i class="fas fa-award"></i>
                        <div>
                            <h6 class="fw-bold mb-0">Bác Sĩ Chuyên Khoa I</h6>
                            <small class="text-muted">Tu nghiệp Hoa Kỳ & Thụy Sĩ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Giới thiệu Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="text-primary fw-bold text-uppercase tracking-wider">Về chúng tôi</span>
                <h2 class="display-6 fw-bold mt-2">Tại sao hàng ngàn khách hàng chọn Nha Khoa Smile?</h2>
                <p class="text-muted">Chúng tôi cam kết mang lại trải nghiệm khám và điều trị nha khoa êm ái, an toàn và đạt tính thẩm mỹ cao nhất.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Đội ngũ bác sĩ giàu kinh nghiệm</h5>
                    <p class="text-muted small mb-0">100% Bác sĩ tốt nghiệp Đại học Y Dược chính quy, có chứng chỉ hành nghề và tu nghiệp chuyên sâu ở nước ngoài.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Công nghệ hiện đại</h5>
                    <p class="text-muted small mb-0">Sở hữu máy chụp phim X-quang CT Cone Beam 3D, máy nhổ răng siêu âm Piezotome và thiết bị thiết kế nụ cười Smile Design.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-pump-soap"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Vô trùng tuyệt đối</h5>
                    <p class="text-muted small mb-0">Mỗi bệnh nhân một bộ dụng cụ riêng biệt được hấp tiệt trùng theo tiêu chuẩn phòng khép kín khắt khe của Bộ Y Tế.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Chăm sóc chu đáo</h5>
                    <p class="text-muted small mb-0">Hỗ trợ tư vấn trực tuyến 24/7, nhắc lịch hẹn tự động và chính sách bảo hành dịch vụ dài hạn lên tới 20 năm.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Dịch vụ nổi bật Section -->
<section class="py-5" style="background-color: #f8fafc;">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
            <div>
                <span class="text-primary fw-bold text-uppercase">Dịch vụ chuyên khoa</span>
                <h2 class="display-6 fw-bold mt-2 mb-0">Dịch vụ nha khoa nổi bật</h2>
            </div>
            <a href="pages/services.php" class="btn btn-outline-clinic mt-3 mt-md-0">
                Xem tất cả dịch vụ <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($services as $service): ?>
            <div class="col-md-6 col-lg-3">
                <div class="service-card">
                    <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                    <div class="service-card-body">
                        <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars(mb_strimwidth($service['description'], 0, 95, "...")); ?></p>
                        <div class="service-price">
                            <?php echo number_format($service['price'], 0, ',', '.'); ?> VNĐ
                        </div>
                        <a href="pages/booking.php?service_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary-clinic w-100 mt-3">
                            <i class="far fa-calendar-check me-1"></i> Đặt lịch ngay
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. Đội ngũ bác sĩ Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="text-primary fw-bold text-uppercase">Chuyên gia hàng đầu</span>
                <h2 class="display-6 fw-bold mt-2">Đội ngũ bác sĩ nha khoa tận tâm</h2>
                <p class="text-muted">Được đào tạo bài bản, tâm huyết với nghề và luôn đặt sự an toàn, thoải mái của bệnh nhân lên hàng đầu.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-6 col-lg-4">
                <div class="doctor-card">
                    <div class="doctor-img-wrapper">
                        <img src="<?php echo htmlspecialchars($doctor['image']); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>">
                    </div>
                    <div class="doctor-body">
                        <div class="doctor-specialty"><?php echo htmlspecialchars($doctor['specialty']); ?></div>
                        <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($doctor['full_name']); ?></h5>
                        <span class="badge bg-light text-primary border mb-3"><?php echo htmlspecialchars($doctor['experience']); ?></span>
                        <p class="text-muted small mb-3"><?php echo htmlspecialchars(mb_strimwidth($doctor['description'], 0, 110, "...")); ?></p>
                        <a href="pages/booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-outline-clinic btn-sm w-100">
                            Đặt lịch khám với Bác sĩ
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Quy trình 5 bước Section -->
<section class="py-5" style="background: linear-gradient(180deg, #f8fafc 0%, #e6f2ff 100%);">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="text-primary fw-bold text-uppercase">Quy trình điều trị</span>
                <h2 class="display-6 fw-bold mt-2">5 Bước khám chữa bệnh chuyên nghiệp</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md">
                <div class="process-step">
                    <div class="process-number">1</div>
                    <h6 class="fw-bold mb-2">Đặt lịch hẹn</h6>
                    <p class="text-muted small">Đăng ký lịch khám qua website hoặc tổng đài hotline.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step">
                    <div class="process-number">2</div>
                    <h6 class="fw-bold mb-2">Khám & Tư vấn</h6>
                    <p class="text-muted small">Bác sĩ khám tổng quát và lắng nghe nhu cầu của bạn.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step">
                    <div class="process-number">3</div>
                    <h6 class="fw-bold mb-2">Chẩn đoán CT 3D</h6>
                    <p class="text-muted small">Chụp X-quang chẩn đoán chính xác tình trạng răng miệng.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step">
                    <div class="process-number">4</div>
                    <h6 class="fw-bold mb-2">Tiến hành điều trị</h6>
                    <p class="text-muted small">Thực hiện điều trị với công nghệ không đau, vô trùng.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step">
                    <div class="process-number">5</div>
                    <h6 class="fw-bold mb-2">Chăm sóc sau điều trị</h6>
                    <p class="text-muted small">Hướng dẫn vệ sinh răng miệng và hẹn tái khám định kỳ.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. Đánh giá khách hàng Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="text-primary fw-bold text-uppercase">Cảm nhận khách hàng</span>
                <h2 class="display-6 fw-bold mt-2">Hơn 15.000 Nụ cười đã tỏa sáng</h2>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic text-muted">"Tôi vừa thực hiện bọc 8 răng sứ tại Nha Khoa Smile. Bác sĩ Linh làm việc rất tỉ mỉ, không đau một chút nào. Giao diện nụ cười mới giúp tôi tự tin hơn hẳn!"</p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" class="rounded-circle" width="48" height="48" alt="Khách hàng">
                        <div>
                            <h6 class="fw-bold mb-0">Chị Minh Châu</h6>
                            <small class="text-muted">Quận 1, TP.HCM</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic text-muted">"Lúc đầu tôi rất sợ nhổ răng khôn mọc lệch. Nhưng nhờ bác sĩ Đức sử dụng máy siêu âm Piezotome, quá trình nhổ chỉ mất 10 phút và sau đó sưng rất ít."</p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" class="rounded-circle" width="48" height="48" alt="Khách hàng">
                        <div>
                            <h6 class="fw-bold mb-0">Anh Quốc Bảo</h6>
                            <small class="text-muted">Quận 3, TP.HCM</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="fst-italic text-muted">"Phòng khám sạch đẹp, nhân viên lễ tân chu đáo. AI Chatbot trên website tư vấn cực kỳ nhanh và việc đặt lịch online rất thuận tiện!"</p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=100&auto=format&fit=crop&q=80" class="rounded-circle" width="48" height="48" alt="Khách hàng">
                        <div>
                            <h6 class="fw-bold mb-0">Bạn Thanh Thảo</h6>
                            <small class="text-muted">Bình Thạnh, TP.HCM</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. CTA Banner Section -->
<section class="py-5">
    <div class="container">
        <div class="cta-banner text-center text-lg-start d-lg-flex align-items-center justify-content-between">
            <div class="mb-4 mb-lg-0">
                <h2 class="display-6 fw-bold mb-2">Bạn đang gặp vấn đề về răng miệng?</h2>
                <p class="lead mb-0 text-white-50">Đừng để cơn đau làm ảnh hưởng chất lượng cuộc sống. Đặt lịch ngay hôm nay để nhận ưu đãi khám tư vấn miễn phí!</p>
            </div>
            <a href="pages/booking.php" class="btn btn-light btn-lg text-primary fw-bold px-4 py-3 shadow">
                <i class="far fa-calendar-alt me-2"></i> Đặt lịch khám ngay
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
