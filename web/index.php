<?php
$current_page = 'home';
$page_title = 'Nha Khoa Quốc Tế Premier - Chăm Sóc Nụ Cười, Kiến Tạo Tự Tin';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Fetch services and doctors from MySQL database or fallback
require_once __DIR__ . '/data/doctors_detail_data.php';

$services = [];
if (!empty($pdo)) {
    try {
        $stmt_services = $pdo->query("SELECT * FROM services ORDER BY id ASC LIMIT 8");
        $services = $stmt_services ? $stmt_services->fetchAll() : [];
    } catch (\Throwable $e) {
        $services = [];
    }
}
$doctors = get_doctors_for_view();
?>

<!-- Floating Quick Action Bar (Hotline, Zalo, Đặt Lịch) -->
<div class="floating-quick-bar">
    <a href="tel:19006868" class="float-btn-item float-btn-hotline" title="Gọi ngay Hotline 1900 6868">
        <i class="fas fa-phone-alt"></i>
        <span class="float-btn-tooltip">Hotline: 1900 6868</span>
    </a>
    <a href="https://zalo.me" target="_blank" class="float-btn-item float-btn-zalo" title="Tư vấn Zalo 24/7">
        <i class="fas fa-comment-dots"></i>
        <span class="float-btn-tooltip">Tư vấn Zalo 24/7</span>
    </a>
    <a href="pages/booking.php" class="float-btn-item float-btn-booking" title="Đặt lịch khám 0đ">
        <i class="far fa-calendar-check"></i>
        <span class="float-btn-tooltip">Đặt lịch khám miễn phí</span>
    </a>
    <a href="pages/my_appointments.php" class="float-btn-item float-btn-booking bg-info text-white" title="Lịch hẹn đã đặt của tôi">
        <i class="fas fa-clipboard-check"></i>
        <span class="float-btn-tooltip">Lịch hẹn của tôi</span>
    </a>
</div>

<!-- 1. Hero Section (Ultra-Modern Light Medical Glassmorphism with Real Dental Image) -->
<section class="hero-section-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Cột trái: Thông điệp, Kêu gọi hành động & Cam kết -->
            <div class="col-lg-7">
                <div class="hero-badge-light">
                    <i class="fas fa-certificate text-warning"></i> Hệ Thống Nha Khoa Quốc Tế Đạt Chuẩn Sở Y Tế
                </div>
                <h1 class="hero-title-light">
                    Chăm sóc nụ cười –<br>
                    <span class="text-highlight-cyan">Kiến tạo tự tin</span>
                </h1>
                <p class="hero-desc-light">
                    Nha Khoa Quốc Tế Premier quy tụ đội ngũ Bác sĩ - Chuyên gia hàng đầu Răng Hàm Mặt, tiên phong ứng dụng công nghệ 3D CT ConeBeam, máy Scan răng iTero 5D và quy trình điều trị vô trùng tuyệt đối tiêu chuẩn Châu Âu.
                </p>
                
                <!-- Nhóm nút hành động nhanh gọn, cân đối -->
                <div class="hero-actions-group">
                    <a href="pages/booking.php" class="btn-hero-booking">
                        <i class="far fa-calendar-check"></i>
                        <span>Đặt Lịch Khám Miễn Phí</span>
                    </a>
                    <button type="button" class="btn-hero-ai" onclick="document.getElementById('chatbotToggler').click();">
                        <span class="ai-pulse-dot"></span>
                        <i class="fas fa-robot"></i>
                        <span>Tư Vấn AI 24/7</span>
                    </button>
                    <a href="pages/my_appointments.php" class="btn-hero-sub">
                        <i class="fas fa-clipboard-list text-primary"></i>
                        <span>Lịch Hẹn Của Tôi</span>
                    </a>
                </div>

                <!-- 4 Tiêu chí bảo đảm dạng Lưới 2x2 trực quan, cân đối -->
                <div class="hero-trust-grid">
                    <div class="trust-badge-item">
                        <i class="fas fa-user-graduate"></i>
                        <span>100% BS Chuyên Khoa RHM</span>
                    </div>
                    <div class="trust-badge-item">
                        <i class="fas fa-shield-virus"></i>
                        <span>Vô trùng tuyệt đối Class B</span>
                    </div>
                    <div class="trust-badge-item">
                        <i class="fas fa-x-ray"></i>
                        <span>Khám & Chụp phim 3D 0đ</span>
                    </div>
                    <div class="trust-badge-item">
                        <i class="fas fa-medal"></i>
                        <span>Bảo hành chính hãng 20 năm</span>
                    </div>
                </div>

                <!-- 4 Chỉ số KPI Thống kê uy tín -->
                <div class="hero-stats-row">
                    <div class="hero-stat-item">
                        <span class="hero-stat-number">15,000+</span>
                        <span class="hero-stat-label">Nụ cười tỏa sáng</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-number">100%</span>
                        <span class="hero-stat-label">Bác sĩ CKI & Thạc sĩ</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-number">15+ Năm</span>
                        <span class="hero-stat-label">Uy tín phát triển</span>
                    </div>
                    <div class="hero-stat-item">
                        <span class="hero-stat-number">99.8%</span>
                        <span class="hero-stat-label">Đánh giá hài lòng</span>
                    </div>
                </div>
            </div>
            
            <!-- Cột phải: Hình Ảnh Nha Khoa Thực Tế & Thẻ Đánh Giá Tin Cậy -->
            <div class="col-lg-5">
                <div class="hero-visual-container">
                    <div class="hero-main-img-wrapper">
                        <img src="assets/images/hero-dentist-smile.jpg" alt="Bác sĩ Nha Khoa Quốc Tế Premier và nụ cười rạng rỡ của khách hàng" class="hero-main-img">
                    </div>
                    <!-- Thẻ nổi 1: Rating uy tín -->
                    <div class="hero-floating-card top-right">
                        <div class="hero-floating-card-icon">
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <div class="hero-floating-card-text">
                            <strong>4.9 / 5.0 ⭐ Rating</strong>
                            <span>15.000+ Khách hàng hài lòng</span>
                        </div>
                    </div>
                    <!-- Thẻ nổi 2: Đội ngũ Bác sĩ chuyên môn cao -->
                    <div class="hero-floating-card bottom-left">
                        <div class="hero-floating-card-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="hero-floating-card-text">
                            <strong>Đội Ngũ Bác Sĩ Răng Hàm Mặt</strong>
                            <span>Hơn 12 năm kinh nghiệm lâm sàng</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Booking Card bên dưới ảnh -->
                <div class="mt-4 p-3 rounded-4 hero-quick-promo-box">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="text-dark small fw-bold d-block"><i class="fas fa-bolt text-warning me-1"></i> Khám tổng quát & Chụp phim 3D 0đ:</span>
                            <small class="text-muted">Ưu đãi miễn phí 100% khi đặt lịch trước hôm nay</small>
                        </div>
                        <a href="pages/booking.php" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold text-white">
                            Đặt hẹn ưu tiên <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Trụ Cột Cam Kết Chất Lượng (4 Pillars) -->
<section class="py-5 bg-white" id="features">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag">Tiêu chuẩn quốc tế</span>
                <h2 class="section-title">Tại Sao Hơn 15.000 Khách Hàng Chọn Premier Dental?</h2>
                <p class="section-desc">Chúng tôi kết hợp chuyên môn y khoa xuất sắc và công nghệ số tiên tiến để tạo nên trải nghiệm điều trị nha khoa êm ái, bền đẹp và an tâm trọn đời.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h5 class="feature-card-title">100% Bác Sĩ Chuyên Khoa</h5>
                    <p class="feature-card-desc">Đội ngũ Thạc sĩ, Bác sĩ CKI tốt nghiệp Đại học Y Dược chính quy, tu nghiệp chuyên sâu tại Đức, Thụy Sĩ và Hoa Kỳ.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <h5 class="feature-card-title">Công Nghệ 3D Kỹ Thuật Số</h5>
                    <p class="feature-card-desc">Sở hữu máy chụp CT Cone Beam 3D, máy scan trong miệng iTero 5D và công nghệ thiết kế nụ cười Smile Design độc quyền.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-shield-virus"></i>
                    </div>
                    <h5 class="feature-card-title">Vô Trùng Chuẩn Class B</h5>
                    <p class="feature-card-desc">1 Bệnh nhân – 1 Bộ dụng cụ riêng biệt được đóng gói và hấp sấy theo tiêu chuẩn kiểm soát nhiễm khuẩn khắt khe của Bộ Y Tế.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon-wrapper">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h5 class="feature-card-title">Tư Vấn AI & Bảo Hành Tới 20 Năm</h5>
                    <p class="feature-card-desc">Trợ lý AI sẵn sàng giải đáp thắc mắc 24/7. Thẻ bảo hành điện tử chính hãng kiểm tra mã vạch xuất xứ minh bạch toàn quốc.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. NEW: Hành Trình Kiến Tạo Nụ Cười Rạng Rỡ (Smile Transformation Showcase) -->
<section class="py-5 smile-showcase-section" id="smile-showcase">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag"><i class="fas fa-magic me-1"></i> Kết quả khách hàng thực tế</span>
                <h2 class="section-title">Hành Trình Kiến Tạo Nụ Cười Rạng Rỡ</h2>
                <p class="section-desc">Hơn 15.000 nụ cười tỏa sáng tự tin đã được tái tạo thành công tại Nha Khoa Quốc Tế Premier với công nghệ nha khoa kỹ thuật số hiện đại và phác đồ điều trị không đau.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Khách hàng 1: Dán Sứ Veneer Emax -->
            <div class="col-lg-4 col-md-6">
                <div class="smile-card">
                    <div class="smile-thumb-wrapper">
                        <img src="assets/images/smile-woman.jpg" alt="Kết quả dán sứ Veneer Emax tại Nha Khoa Premier" class="smile-thumb-img" loading="lazy">
                        <span class="smile-badge-tag"><i class="fas fa-certificate"></i> Dán Sứ Veneer Emax</span>
                    </div>
                    <div class="smile-card-content">
                        <div class="smile-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="ms-1 fw-bold text-dark">5.0 / 5.0</span>
                        </div>
                        <h4 class="smile-title">Răng Đều Đẹp, Trắng Sáng Tự Nhiên</h4>
                        <p class="smile-testimonial">
                            "Trước đây tôi rất tự ti vì hàm răng xỉn màu và thưa kẽ. Nhờ BS Mai thiết kế nụ cười và dán sứ Veneer siêu mỏng, hàm răng trắng trong tự nhiên, ai cũng khen cười duyên hơn hẳn!"
                        </p>
                        <div class="smile-patient-footer">
                            <div>
                                <div class="patient-info-name">Chị Mai Phương</div>
                                <div class="patient-info-meta">26 tuổi • TP. Hồ Chí Minh</div>
                            </div>
                            <span class="patient-tag-pill">Dán Sứ Veneer</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khách hàng 2: Niềng Răng Trong Suốt Invisalign -->
            <div class="col-lg-4 col-md-6">
                <div class="smile-card">
                    <div class="smile-thumb-wrapper">
                        <img src="assets/images/smile-man.jpg" alt="Kết quả niềng răng trong suốt Invisalign" class="smile-thumb-img" loading="lazy">
                        <span class="smile-badge-tag"><i class="fas fa-certificate"></i> Niềng Răng Invisalign</span>
                    </div>
                    <div class="smile-card-content">
                        <div class="smile-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="ms-1 fw-bold text-dark">5.0 / 5.0</span>
                        </div>
                        <h4 class="smile-title">Khớp Cắn Chuẩn, Tự Tin Giao Tiếp</h4>
                        <p class="smile-testimonial">
                            "Niềng răng máng trong suốt Invisalign tại Premier thực sự vượt mong đợi. Không vướng víu mắc cài, ăn uống thoải mái, sau 18 tháng các răng đều tăm tắp đúng chuẩn tỷ lệ vàng."
                        </p>
                        <div class="smile-patient-footer">
                            <div>
                                <div class="patient-info-name">Anh Tuấn Kiệt</div>
                                <div class="patient-info-meta">29 tuổi • Doanh nhân</div>
                            </div>
                            <span class="patient-tag-pill">Niềng Răng 3D</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khách hàng 3: Tư Vấn & Mô Phỏng 3D Smile Design -->
            <div class="col-lg-4 col-md-6 mx-auto">
                <div class="smile-card">
                    <div class="smile-thumb-wrapper">
                        <img src="assets/images/doctor-consult.jpg" alt="Bác sĩ tư vấn kế hoạch điều trị và mô phỏng nụ cười 3D" class="smile-thumb-img" loading="lazy">
                        <span class="smile-badge-tag"><i class="fas fa-certificate"></i> 3D Smile Design</span>
                    </div>
                    <div class="smile-card-content">
                        <div class="smile-stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <span class="ms-1 fw-bold text-dark">5.0 / 5.0</span>
                        </div>
                        <h4 class="smile-title">Mô Phỏng Thấy Trước Nụ Cười</h4>
                        <p class="smile-testimonial">
                            "Điều tôi ưng ý nhất là được bác sĩ quét dấu răng bằng máy 3D iTero 5D và xem trước kết quả hàm răng tương lai chỉ sau 5 phút. Quy trình rất chuyên nghiệp, êm ái và minh bạch."
                        </p>
                        <div class="smile-patient-footer">
                            <div>
                                <div class="patient-info-name">Chị Thu Thảo</div>
                                <div class="patient-info-meta">32 tuổi • Giảng viên</div>
                            </div>
                            <span class="patient-tag-pill">Thiết Kế Nụ Cười</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="pages/booking.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="far fa-calendar-check me-2"></i> Đặt Lịch Khám Để Được Tư Vấn Nụ Cười Của Bạn
            </a>
        </div>
    </div>
</section>

<!-- 4. Dịch Vụ Nha Khoa Chuyên Sâu -->
<section class="py-5" style="background-color: #ffffff;" id="services">
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-5">
            <div>
                <span class="section-tag">Dịch vụ chuyên sâu</span>
                <h2 class="section-title mb-0">Dịch Vụ Nha Khoa Nổi Bật</h2>
            </div>
            <a href="pages/services.php" class="btn btn-outline-primary rounded-pill px-4 py-2 mt-3 mt-md-0 fw-semibold">
                Xem toàn bộ bảng giá <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($services as $service): 
                $imgUrl = $service['image'];
                if (empty($imgUrl)) {
                    $imgUrl = 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=500&auto=format&fit=crop&q=80';
                }
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="service-card">
                    <a href="pages/service_detail.php?id=<?php echo $service['id']; ?>" class="service-img-wrapper d-block" title="Xem chi tiết <?php echo htmlspecialchars($service['name']); ?>">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" loading="lazy">
                        <div class="service-badge-warranty">
                            <i class="fas fa-shield-alt text-info me-1"></i> Bảo hành chu đáo
                        </div>
                    </a>
                    <div class="service-card-body">
                        <h5 class="service-title mb-2">
                            <a href="pages/service_detail.php?id=<?php echo $service['id']; ?>" class="service-title-link">
                                <?php echo htmlspecialchars($service['name']); ?>
                            </a>
                        </h5>
                        <p class="service-desc"><?php echo htmlspecialchars(mb_strimwidth($service['description'], 0, 95, "...")); ?></p>
                        
                        <div class="service-footer flex-column align-items-stretch gap-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="service-price-label">Giá niêm yết từ:</span>
                                <span class="service-price-val"><?php echo number_format($service['price'], 0, ',', '.'); ?>đ</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <a href="pages/service_detail.php?id=<?php echo $service['id']; ?>" class="btn-service-view-detail flex-grow-1 text-center">
                                    <i class="fas fa-info-circle me-1"></i> Chi tiết & Quy trình
                                </a>
                                <a href="pages/booking.php?service_id=<?php echo $service['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" title="Đặt lịch khám ngay">
                                    <i class="far fa-calendar-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. NEW: Công Nghệ & Trang Thiết Bị Nha Khoa Tiêu Chuẩn Châu Âu -->
<section class="py-5" style="background-color: #f8fafc;" id="technology">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag"><i class="fas fa-microscope me-1"></i> Công nghệ hiện đại</span>
                <h2 class="section-title">Trang Thiết Bị Nha Khoa Chuẩn Châu Âu</h2>
                <p class="section-desc">Premier Dental đầu tư đồng bộ hệ thống máy móc chẩn đoán và điều trị số 1 thế giới, giúp quá trình thăm khám chuẩn xác 100%, êm ái và rút ngắn 50% thời gian điều trị.</p>
            </div>
        </div>

        <div class="tech-showcase-box">
            <div class="row align-items-center g-5">
                <!-- Cột trái: Hình ảnh thao tác quét 3D thực tế -->
                <div class="col-lg-5">
                    <div class="tech-img-wrapper">
                        <img src="assets/images/dental-tech-scanner.jpg" alt="Máy Scan trong miệng 3D iTero Element 5D tại Nha Khoa Premier" class="tech-scanner-img" loading="lazy">
                    </div>
                    <div class="text-center mt-3">
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-semibold">
                            <i class="fas fa-camera me-1"></i> Máy Scan 3D iTero Element 5D - Lấy dấu trong 60 giây
                        </span>
                    </div>
                </div>

                <!-- Cột phải: 4 thiết bị công nghệ đỉnh cao -->
                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="tech-item-card">
                                <div class="tech-item-icon">
                                    <i class="fas fa-vr-cardboard"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Scan 3D iTero 5D</h5>
                                <p class="text-muted small mb-0">Quét mẫu hàm kỹ thuật số siêu nhanh chỉ sau 60 giây, mô phỏng trước nụ cười tương lai ngay trên màn hình.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="tech-item-card">
                                <div class="tech-item-icon">
                                    <i class="fas fa-x-ray"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Máy Chụp CT ConeBeam 3D</h5>
                                <p class="text-muted small mb-0">Khảo sát toàn diện mật độ xương hàm, vị trí chân răng và dây thần kinh chuẩn xác tới từng micromet.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="tech-item-card">
                                <div class="tech-item-icon">
                                    <i class="fas fa-wave-square"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Nhổ Răng Siêu Âm Piezotome</h5>
                                <p class="text-muted small mb-0">Bước sóng siêu âm tác động chọn lọc lên mô cứng, không xâm lấn mô mềm, không gây đau, lành thương cực nhanh.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="tech-item-card">
                                <div class="tech-item-icon">
                                    <i class="fas fa-pump-soap"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Vô Trùng Khép Kín Class B</h5>
                                <p class="text-muted small mb-0">Hệ thống lò hấp sấy chân không Autoclave tiêu chuẩn Châu Âu, đảm bảo vô trùng 100% cho từng bộ dụng cụ.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. Đội Ngũ Bác Sĩ Chuyên Khoa -->
<section class="py-5 bg-white" id="doctors">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag">Chuyên gia hàng đầu</span>
                <h2 class="section-title">Đội Ngũ Bác Sĩ Nha Khoa Tận Tâm</h2>
                <p class="section-desc">Được đào tạo bài bản từ các trường Y khoa danh tiếng, bác sĩ tại Premier Dental luôn lắng nghe và đặt sự an toàn, thẩm mỹ tự nhiên của bệnh nhân lên hàng đầu.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($doctors as $doctor): 
                $imgSrc = $doctor['image'];
                if (!empty($imgSrc) && strpos($imgSrc, 'http://') !== 0 && strpos($imgSrc, 'https://') !== 0) {
                    $imgSrc = './' . ltrim($imgSrc, './');
                }
                if (empty($imgSrc)) {
                    $imgSrc = './assets/images/dr-nguyen-an.jpg';
                }
                $modalId = 'homeDoctorModal' . $doctor['id'];
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="doctor-card h-100 shadow-sm border rounded-4 overflow-hidden d-flex flex-column" style="cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                    <div class="doctor-img-wrapper position-relative overflow-hidden" style="height: 280px; background: #e2e8f0;">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" loading="lazy" onerror="this.onerror=null; this.src='./assets/images/dr-nguyen-an.jpg';">
                        <div class="position-absolute bottom-0 start-0 end-0 p-2 text-center" style="background: linear-gradient(180deg, transparent 0%, rgba(7, 21, 39, 0.85) 100%);">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.78rem;">
                                <i class="fas fa-id-card me-1"></i> Xem Chi Tiết Bác Sĩ
                            </span>
                        </div>
                    </div>
                    <div class="doctor-body p-4 d-flex flex-column flex-grow-1 text-center">
                        <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-3 py-1 mb-2 mx-auto" style="font-size: 0.78rem;">
                            <?php echo htmlspecialchars($doctor['specialty']); ?>
                        </span>
                        <h4 class="doctor-name fw-bold text-dark mb-1 fs-5"><?php echo htmlspecialchars($doctor['full_name']); ?></h4>
                        <div class="doctor-exp text-muted small mb-2">
                            <i class="fas fa-award text-warning me-1"></i> <?php echo htmlspecialchars($doctor['experience']); ?>
                        </div>
                        <p class="doctor-desc text-secondary small line-height-md mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo htmlspecialchars($doctor['description']); ?>
                        </p>
                        
                        <div class="d-flex gap-2 pt-2 border-top mt-auto" onclick="event.stopPropagation();">
                            <button type="button" class="btn btn-outline-primary btn-sm flex-fill rounded-pill py-2" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                                <i class="fas fa-eye me-1"></i> Xem Hồ Sơ
                            </button>
                            <a href="pages/booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary btn-sm flex-fill rounded-pill py-2">
                                <i class="far fa-calendar-check me-1"></i> Đặt Lịch
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Detail Modal (Homepage) -->
            <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-light border-0 py-3 px-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-3 py-1">
                                    <?php echo htmlspecialchars($doctor['specialty']); ?>
                                </span>
                                <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-3 py-1">
                                    <i class="fas fa-check-circle me-1"></i> Đã Xác Thực Bộ Y Tế
                                </span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 p-md-5">
                            <div class="row g-4 align-items-start">
                                <div class="col-md-5 text-center">
                                    <div class="position-relative rounded-4 overflow-hidden shadow-sm border mb-3">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-100" style="height: 300px; object-fit: cover;" onerror="this.onerror=null; this.src='./assets/images/dr-nguyen-an.jpg';">
                                        <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: rgba(7, 21, 39, 0.88); color:#ffffff; font-size: 0.78rem; font-weight: 700;">
                                            <span class="d-inline-block rounded-circle bg-success me-2" style="width:8px; height:8px; box-shadow: 0 0 6px #10b981;"></span> Đang Nhận Lịch Khám Trực Tiếp
                                        </div>
                                    </div>
                                    <div class="bg-light p-3 rounded-4 border text-start">
                                        <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                                            <i class="fas fa-medal text-warning fs-5"></i>
                                            <div>
                                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size:0.72rem;">Kinh Nghiệm</small>
                                                <strong class="text-dark"><?php echo htmlspecialchars($doctor['experience']); ?></strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                                            <i class="fas fa-user-check text-success fs-5"></i>
                                            <div>
                                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size:0.72rem;">Ca Điều Trị</small>
                                                <strong class="text-dark"><?php echo htmlspecialchars($doctor['cases_count'] ?? '3.000+ ca thành công'); ?></strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
                                            <i class="fas fa-star text-warning fs-5"></i>
                                            <div>
                                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size:0.72rem;">Đánh Giá Hài Lòng</small>
                                                <strong class="text-dark"><?php echo htmlspecialchars($doctor['rating'] ?? '4.9 / 5.0 (Rất hài lòng)'); ?></strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fas fa-clock text-primary fs-5"></i>
                                            <div>
                                                <small class="text-muted d-block text-uppercase fw-semibold" style="font-size:0.72rem;">Lịch Khám Định Kỳ</small>
                                                <strong class="text-dark small"><?php echo htmlspecialchars($doctor['schedule'] ?? 'Thứ 2 - Thứ 7 (08:00 - 18:00)'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <h3 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                                    <p class="text-primary fw-bold mb-2"><?php echo htmlspecialchars($doctor['title'] ?? 'Bác Sĩ Chuyên Khoa Răng Hàm Mặt'); ?></p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-graduation-cap text-primary me-1"></i> <?php echo htmlspecialchars($doctor['degree'] ?? 'Đại học Y Dược chính quy'); ?>
                                    </p>
                                    <hr class="my-3">
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-user-md text-primary me-2"></i> Giới Thiệu Chuyên Môn</h6>
                                        <p class="text-secondary small line-height-lg mb-0">
                                            <?php echo nl2br(htmlspecialchars($doctor['description'])); ?>
                                        </p>
                                    </div>
                                    <?php if (!empty($doctor['strengths'])): ?>
                                        <div class="mb-3">
                                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-stethoscope text-primary me-2"></i> Thế Mạnh Lâm Sàng & Kỹ Thuật Đỉnh Cao</h6>
                                            <div class="row g-2">
                                                <?php foreach ($doctor['strengths'] as $st): ?>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-start gap-2 small text-dark">
                                                            <i class="fas fa-check-circle text-success mt-1 flex-shrink-0"></i>
                                                            <span><?php echo htmlspecialchars($st); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($doctor['certifications'])): ?>
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-award text-warning me-2"></i> Bằng Cấp & Chứng Chỉ Quốc Tế</h6>
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($doctor['certifications'] as $cert): ?>
                                                    <li class="d-flex align-items-start gap-2 small text-secondary mb-1">
                                                        <i class="fas fa-medal text-warning mt-1 flex-shrink-0"></i>
                                                        <span><?php echo htmlspecialchars($cert); ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex gap-2 pt-3 border-top">
                                        <a href="pages/booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary rounded-pill px-4 py-2 flex-grow-1 text-center fw-bold shadow-sm">
                                            <i class="far fa-calendar-check me-1"></i> Đặt Lịch Khám Với Bác Sĩ (0đ)
                                        </a>
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. Quy Trình 5 Bước Khám Chữa Bệnh Chuẩn Y Khoa -->
<section class="py-5" style="background: linear-gradient(180deg, #f8fafc 0%, #e0f2fe 100%);" id="process">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag">Quy trình chuẩn hóa</span>
                <h2 class="section-title">5 Bước Khám Chữa Bệnh Chuẩn Quốc Tế</h2>
                <p class="section-desc">Mọi khách hàng đến với Premier Dental đều trải qua quy trình chuyên nghiệp, minh bạch và an toàn tuyệt đối.</p>
            </div>
        </div>
        
        <div class="row g-4 process-roadmap-wrapper">
            <div class="col-md">
                <div class="process-step-card">
                    <div class="process-step-num">1</div>
                    <h5 class="process-step-title">Đặt Hẹn & Tiếp Nhận</h5>
                    <p class="process-step-desc">Đăng ký online hoặc qua Hotline để được xếp lịch ưu tiên, không chờ đợi.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step-card">
                    <div class="process-step-num">2</div>
                    <h5 class="process-step-title">Khám & Tư Vấn 1:1</h5>
                    <p class="process-step-desc">Bác sĩ chuyên khoa thăm khám lâm sàng và lắng nghe kỹ mong muốn của bạn.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step-card">
                    <div class="process-step-num">3</div>
                    <h5 class="process-step-title">Chẩn Đoán 3D 0đ</h5>
                    <p class="process-step-desc">Chụp X-quang CT Cone Beam 3D khảo sát cấu trúc xương hàm chính xác từng milimet.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step-card">
                    <div class="process-step-num">4</div>
                    <h5 class="process-step-title">Điều Trị Không Đau</h5>
                    <p class="process-step-desc">Thực hiện với công nghệ hiện đại, gây tê cục bộ êm ái trong phòng vô trùng Class B.</p>
                </div>
            </div>
            <div class="col-md">
                <div class="process-step-card">
                    <div class="process-step-num">5</div>
                    <h5 class="process-step-title">Bảo Hành & Tái Khám</h5>
                    <p class="process-step-desc">Cấp thẻ bảo hành điện tử chính hãng và nhắc lịch tái khám định kỳ hoàn toàn miễn phí.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. Cảm Nhận & Đánh Giá Của Khách Hàng -->
<section class="py-5 bg-white" id="testimonials">
    <div class="container py-4">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <span class="section-tag">Khách hàng chia sẻ</span>
                <h2 class="section-title">Hơn 15.000 Khách Hàng Đã Hài Lòng</h2>
                <p class="section-desc">Cùng lắng nghe những cảm nhận chân thực từ những bệnh nhân đã tái tạo thành công nụ cười tự tin tại Premier Dental.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">"Tôi vừa thực hiện dán 8 mặt sứ Veneer tại Premier Dental. Bác sĩ tư vấn rất kỹ, form răng tự nhiên, không phải mài nhiều mô răng thật. Rất êm và hoàn toàn không bị ê buốt!"</p>
                    <div class="testimonial-author">
                        <img src="assets/images/smile-woman.jpg" class="testimonial-avatar" alt="Khách hàng Minh Châu">
                        <div>
                            <div class="testimonial-name">Chị Minh Châu</div>
                            <div class="testimonial-service-used">Dán sứ Veneer Emax • Quận 1</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">"Tôi bị mất 2 răng hàm đã 3 năm, việc ăn nhai rất khó khăn. Nhờ ThS. BS Nam cấy ghép Implant Thụy Sĩ, chỉ sau 15 phút là xong. Bây giờ tôi ăn nhai thoải mái như răng thật."</p>
                    <div class="testimonial-author">
                        <img src="assets/images/smile-man.jpg" class="testimonial-avatar" alt="Khách hàng Quốc Bảo">
                        <div>
                            <div class="testimonial-name">Anh Quốc Bảo</div>
                            <div class="testimonial-service-used">Cấy ghép Implant Straumann • Quận 3</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-quote">"Phòng khám sạch đẹp, thơm tho như khách sạn 5 sao. Đặc biệt có trợ lý AI tư vấn và kiểm tra bảng giá minh bạch ngay trên web trước khi đến khám nên mình rất yên tâm!"</p>
                    <div class="testimonial-author">
                        <img src="assets/images/doctor-consult.jpg" class="testimonial-avatar" alt="Khách hàng Thanh Thảo">
                        <div>
                            <div class="testimonial-name">Bạn Thanh Thảo</div>
                            <div class="testimonial-service-used">Niềng răng Invisalign • Bình Thạnh</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 9. CTA Banner Sang Trọng & Kêu Gọi Đặt Lịch -->
<section class="py-5">
    <div class="container">
        <div class="cta-banner-luxury d-lg-flex align-items-center justify-content-between">
            <div class="mb-4 mb-lg-0">
                <span class="badge bg-info text-dark fw-bold px-3 py-2 rounded-pill mb-3">
                    <i class="fas fa-gift me-1"></i> Ưu Đãi Tháng Này
                </span>
                <h2 class="cta-title-luxury">Bạn Đang Gặp Vấn Đề Về Răng Miệng?</h2>
                <p class="cta-desc-luxury">Đừng để cơn đau hay hàm răng khấp khểnh làm giảm sự tự tin. Đăng ký ngay hôm nay để nhận gói khám tổng quát & chụp phim 3D miễn phí 100%!</p>
            </div>
            <a href="pages/booking.php" class="btn-cta-luxury">
                <i class="far fa-calendar-alt text-primary"></i> Đặt Lịch Khám Ưu Tiên
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
