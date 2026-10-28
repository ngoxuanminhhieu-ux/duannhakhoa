<?php
$current_page = 'doctors';
$page_title = 'Đội Ngũ Bác Sĩ Chuyên Khoa - Nha Khoa Quốc Tế Premier';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/doctors_detail_data.php';

$doctors = get_doctors_for_view();

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Banner Header -->
<div class="page-header-light">
    <div class="container text-center">
        <span class="badge-page-tag mb-3">
            <i class="fas fa-user-md text-primary me-1"></i> 100% Bác Sĩ CKI & ThS Răng Hàm Mặt
        </span>
        <h1 class="page-header-title">Đội Ngũ Bác Sĩ Chuyên Khoa</h1>
        <p class="page-header-desc">Chuyên môn cao – Tận tâm – Tốt nghiệp Đại học Y Dược chính quy và tu nghiệp chuyên sâu tại các quốc gia có nền nha khoa phát triển nhất thế giới.</p>
    </div>
</div>

<div class="container my-5 py-3">
    <!-- Thống kê năng lực bác sĩ -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded-4 border text-center shadow-sm h-100">
                <span class="d-block fs-3 fw-bold text-primary">100%</span>
                <small class="text-muted fw-semibold">Bác sĩ chính quy ĐH Y Dược</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded-4 border text-center shadow-sm h-100">
                <span class="d-block fs-3 fw-bold text-info">10+ Năm</span>
                <small class="text-muted fw-semibold">Kinh nghiệm lâm sàng</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded-4 border text-center shadow-sm h-100">
                <span class="d-block fs-3 fw-bold text-success">15,000+</span>
                <small class="text-muted fw-semibold">Ca điều trị thành công</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-3 bg-white rounded-4 border text-center shadow-sm h-100">
                <span class="d-block fs-3 fw-bold text-warning">1:1</span>
                <small class="text-muted fw-semibold">Khám & điều trị trực tiếp</small>
            </div>
        </div>
    </div>

    <!-- Doctors Grid -->
    <div class="row g-4">
        <?php foreach ($doctors as $doctor): 
            $imgSrc = $doctor['image'];
            if (!empty($imgSrc) && strpos($imgSrc, 'http://') !== 0 && strpos($imgSrc, 'https://') !== 0) {
                $imgSrc = $depth . ltrim($imgSrc, './');
            }
            if (empty($imgSrc)) {
                $imgSrc = $depth . 'assets/images/dr-nguyen-an.jpg';
            }
            $modalId = 'doctorDetailModal' . $doctor['id'];
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="doctor-card h-100 shadow-sm border rounded-4 overflow-hidden d-flex flex-column" style="cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;" data-bs-toggle="modal" data-bs-target="#<?php echo $modalId; ?>">
                    <div class="doctor-img-wrapper position-relative overflow-hidden" style="height: 290px; background: #e2e8f0;">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.5s ease;" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&auto=format&fit=crop&q=80';">
                        <div class="position-absolute bottom-0 start-0 end-0 p-3 text-center" style="background: linear-gradient(180deg, transparent 0%, rgba(7, 21, 39, 0.85) 100%);">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm" style="font-size: 0.8rem;">
                                <i class="fas fa-id-card me-1"></i> Nhấn Để Xem Chi Tiết Bác Sĩ
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
                            <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary btn-sm flex-fill rounded-pill py-2">
                                <i class="far fa-calendar-check me-1"></i> Đặt Lịch
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Detail Bootstrap Modal -->
            <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="<?php echo $modalId; ?>Label" aria-hidden="true">
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
                                <!-- Cột Trái: Ảnh & Chỉ Số -->
                                <div class="col-md-5 text-center">
                                    <div class="position-relative rounded-4 overflow-hidden shadow-sm border mb-3">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($doctor['full_name']); ?>" class="w-100" style="height: 300px; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=400&auto=format&fit=crop&q=80';">
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

                                <!-- Cột Phải: Thông Tin Chi Tiết -->
                                <div class="col-md-7">
                                    <h3 class="fw-bold text-dark mb-1" id="<?php echo $modalId; ?>Label"><?php echo htmlspecialchars($doctor['full_name']); ?></h3>
                                    <p class="text-primary fw-bold mb-2"><?php echo htmlspecialchars($doctor['title'] ?? 'Bác Sĩ Chuyên Khoa Răng Hàm Mặt'); ?></p>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-graduation-cap text-primary me-1"></i> <?php echo htmlspecialchars($doctor['degree'] ?? 'Đại học Y Dược chính quy'); ?>
                                    </p>

                                    <hr class="my-3">

                                    <!-- Giới thiệu chuyên môn -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-user-md text-primary me-2"></i> Giới Thiệu Chuyên Môn</h6>
                                        <p class="text-secondary small line-height-lg mb-0">
                                            <?php echo nl2br(htmlspecialchars($doctor['description'])); ?>
                                        </p>
                                    </div>

                                    <!-- Thế mạnh điều trị -->
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

                                    <!-- Bằng cấp & chứng chỉ -->
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

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 pt-3 border-top">
                                        <a href="booking.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary rounded-pill px-4 py-2 flex-grow-1 text-center fw-bold shadow-sm">
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
