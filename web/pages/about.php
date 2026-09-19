<?php
$current_page = 'about';
$page_title = 'Giới Thiệu - Nha Khoa Smile';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, var(--dark-navy) 0%, var(--primary-dark) 100%);">
    <div class="container text-center py-4">
        <h1 class="display-4 fw-bold text-white mb-2">Giới Thiệu Về Nha Khoa Smile</h1>
        <p class="lead text-white-50">Hành trình 15 năm kiến tạo nụ cười hạnh phúc cho hàng triệu người Việt</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <span class="text-primary fw-bold text-uppercase">Tầm nhìn & Sứ mệnh</span>
            <h2 class="display-6 fw-bold mt-2 mb-4">Tiên phong công nghệ nha khoa không đau</h2>
            <p class="text-muted">Được thành lập từ năm 2011, Nha Khoa Smile không ngừng đầu tư hệ thống trang thiết bị tân tiến nhập khẩu từ Đức, Mỹ và Thụy Sĩ. Chúng tôi hiểu rằng khám nha khoa có thể mang lại nỗi sợ ê buốt cho nhiều người, vì vậy sứ mệnh lớn nhất của chúng tôi là ứng dụng công nghệ điều trị **nhẹ nhàng - vô trùng - hiệu quả tuyệt đối**.</p>
            <p class="text-muted">Với đội ngũ bác sĩ là các Thạc sĩ, Bác sĩ CKI giàu kinh nghiệm, chúng tôi tự hào mang lại hàng ngàn nụ cười tự tin mỗi năm.</p>
        </div>
        <div class="col-lg-6">
            <img src="https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=800&auto=format&fit=crop&q=80" alt="Cơ sở vật chất phòng khám" class="img-fluid rounded-4 shadow-lg">
        </div>
    </div>

    <!-- Core Values -->
    <div class="row g-4 my-5">
        <div class="col-12 text-center mb-3">
            <h3 class="fw-bold">Giá Trị Cốt Lõi</h3>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                <div class="feature-icon mx-auto mb-3">
                    <i class="fas fa-heart text-danger fs-2"></i>
                </div>
                <h5 class="fw-bold">Tận Tâm</h5>
                <p class="text-muted small">Coi bệnh nhân như người thân trong gia đình, lắng nghe và thấu hiểu mọi nỗi niềm.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                <div class="feature-icon mx-auto mb-3">
                    <i class="fas fa-shield-alt text-primary fs-2"></i>
                </div>
                <h5 class="fw-bold">Chất Lượng</h5>
                <p class="text-muted small">Cam kết vật liệu sứ, trụ Implant chính hãng có chứng nhận nguồn gốc xuất xứ rõ ràng.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm p-4 rounded-4 text-center">
                <div class="feature-icon mx-auto mb-3">
                    <i class="fas fa-rocket text-success fs-2"></i>
                </div>
                <h5 class="fw-bold">Đổi Mới</h5>
                <p class="text-muted small">Liên tục cập nhật kiến thức y khoa mới và tiếp thu quy trình công nghệ 4.0.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
