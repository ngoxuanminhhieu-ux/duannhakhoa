<?php
$current_page = 'contact';
$page_title = 'Liên Hệ - Nha Khoa Smile';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-dark) 100%);">
    <div class="container text-center py-4">
        <h1 class="display-4 fw-bold text-white mb-2">Liên Hệ Với Chúng Tôi</h1>
        <p class="lead text-white-50">Nha Khoa Smile luôn lắng nghe và đồng hành cùng nụ cười của bạn</p>
    </div>
</div>

<div class="container mb-5 py-3">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h3 class="fw-bold mb-4 text-primary">Thông Tin Trực Tiếp</h3>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="fas fa-map-marker-alt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Địa chỉ phòng khám</h6>
                        <p class="text-muted small mb-0">123 Đường Nguyễn Trãi, Phường 2, Quận 5, TP. Hồ Chí Minh</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="fas fa-phone-alt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Hotline tư vấn & Đặt lịch</h6>
                        <p class="text-muted small mb-0">1900 6868 - 0912 345 678</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="fas fa-envelope fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email phản hồi</h6>
                        <p class="text-muted small mb-0">contact@nhakhoasmile.vn</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="feature-icon bg-primary-subtle text-primary p-3 rounded-3">
                        <i class="fas fa-clock fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Thời gian phục vụ</h6>
                        <p class="text-muted small mb-0">Thứ 2 - Thứ 6: 08:00 - 20:00<br>Thứ 7 - Chủ Nhật: 08:00 - 17:30</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h3 class="fw-bold mb-4 text-primary">Gửi Thắc Mắc & Góp Ý</h3>
                <form action="#" method="POST" onsubmit="alert('Cảm ơn bạn đã gửi ý kiến đóng góp! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.'); return false;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ tên</label>
                            <input type="text" class="form-control" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="tel" class="form-control" placeholder="0912..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" placeholder="name@example.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nội dung thắc mắc</label>
                            <textarea class="form-control" rows="4" placeholder="Nhập câu hỏi hoặc ý kiến của bạn..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary-clinic px-4">
                                <i class="fas fa-paper-plane me-1"></i> Gửi thông tin
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
