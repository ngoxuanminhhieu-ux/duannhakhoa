<?php
$current_page = 'services';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../data/services_detail_data.php';

// Get Service ID
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Fetch from DB if available
$serviceDb = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$serviceId]);
    $serviceDb = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $serviceDb = null;
}

// Fetch Rich Medical Detail
$detail = get_service_detail_by_id($serviceId);

// Fallback if not found
if (!$detail) {
    if ($serviceDb) {
        $detail = [
            'id' => $serviceDb['id'],
            'name' => $serviceDb['name'],
            'category' => 'Dịch Vụ Nha Khoa Chuyên Sâu',
            'tagline' => 'Điều trị nha khoa chuẩn Quốc tế ISO 9001 tại Premier Dental Clinic',
            'price_from' => $serviceDb['price'],
            'price_formatted' => number_format($serviceDb['price'], 0, ',', '.') . 'đ',
            'duration' => '30 - 60 phút',
            'pain_level' => 'Gây tê cục bộ nhẹ nhàng, không đau, êm ái',
            'warranty' => 'Bảo hành chính hãng & Tái khám miễn phí',
            'image' => $serviceDb['image'] ?: 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=800&auto=format&fit=crop&q=80',
            'technology' => 'Hệ thống thiết bị kỹ thuật số nhập khẩu Đức & Thụy Sĩ, quy trình vô trùng Class B',
            'how_it_works' => 'Được trực tiếp thực hiện bởi đội ngũ Bác sĩ Thạc sĩ, CKI với sự hỗ trợ của trang thiết bị công nghệ hiện đại, đảm bảo chính xác, an toàn và bảo tồn tối đa cấu trúc răng thật.',
            'effects' => [
                'Điều trị triệt để các vấn đề bệnh lý và khiếm khuyết của răng miệng.',
                'Phục hồi toàn diện khả năng ăn nhai vững chắc và tự tin.',
                'Nâng cao tính thẩm mỹ của hàm răng và nụ cười rạng rỡ.',
                'Ngăn ngừa các biến chứng nguy hiểm ảnh hưởng đến xương hàm và khớp cắn.'
            ],
            'target_audience' => 'Khách hàng có nhu cầu điều trị và chăm sóc nụ cười khỏe đẹp bền vững.',
            'steps' => [
                ['step' => 1, 'title' => 'Thăm khám & Chụp X-quang 3D', 'desc' => 'Đánh giá cấu trúc hàm và tình trạng răng cần điều trị.'],
                ['step' => 2, 'title' => 'Vệ sinh & Sát khuẩn vô trùng', 'desc' => 'Làm sạch khu vực điều trị, đảm bảo an toàn y khoa tuyệt đối.'],
                ['step' => 3, 'title' => 'Tiến hành thao tác chuyên môn', 'desc' => 'Bác sĩ thực hiện kỹ thuật với máy móc hiện đại và công nghệ êm ái.'],
                ['step' => 4, 'title' => 'Kiểm tra & Hoàn thiện', 'desc' => 'Đánh giá khớp cắn, tính thẩm mỹ và độ vững chắc.'],
                ['step' => 5, 'title' => 'Cấp sổ bảo hành & Hướng dẫn chăm sóc', 'desc' => 'Bác sĩ dặn dò chế độ ăn uống và hẹn lịch tái khám định kỳ.']
            ],
            'post_care' => 'Tuân thủ hướng dẫn vệ sinh của bác sĩ, đánh răng 2 lần/ngày và tái khám đúng hẹn.',
            'doctor_specialist' => 'ThS.BS Nguyễn Văn An (12 năm kinh nghiệm)',
            'faqs' => [
                ['q' => 'Dịch vụ này có đau không?', 'a' => 'Quy trình được thực hiện với công nghệ hiện đại và gây tê êm dịu, bạn sẽ cảm thấy nhẹ nhàng và thoải mái suốt quá trình thực hiện.']
            ]
        ];
    } else {
        // Redirect to services list if service completely invalid
        header('Location: services.php');
        exit;
    }
}

// Override with DB price and image if exists
if ($serviceDb) {
    if (!empty($serviceDb['image'])) {
        $detail['image'] = $serviceDb['image'];
    }
}

$page_title = $detail['name'] . ' - Quy Trình & Chi Tiết Dịch Vụ Premier Dental';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<nav class="bg-light py-2 border-bottom">
    <div class="container">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="../index.php" class="text-muted text-decoration-none"><i class="fas fa-home me-1"></i> Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="services.php" class="text-muted text-decoration-none">Dịch vụ & Bảng giá</a></li>
            <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page"><?php echo htmlspecialchars($detail['name']); ?></li>
        </ol>
    </div>
</nav>

<!-- Service Hero Banner -->
<div class="page-header-light">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge-page-tag mb-3">
                    <i class="fas fa-certificate text-primary me-1"></i> <?php echo htmlspecialchars($detail['category']); ?>
                </span>
                <h1 class="page-header-title text-start mb-2"><?php echo htmlspecialchars($detail['name']); ?></h1>
                <p class="lead text-muted mb-4" style="line-height: 1.6; max-width: 750px;">
                    <?php echo htmlspecialchars($detail['tagline']); ?>
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="booking.php?service_id=<?php echo $detail['id']; ?>" class="btn-hero-booking" style="padding: 12px 28px; font-size: 1rem;">
                        <i class="far fa-calendar-check me-2"></i> Đặt lịch dịch vụ này
                    </a>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold" onclick="askAiAboutService('<?php echo addslashes($detail['name']); ?>')">
                        <i class="fas fa-robot me-2"></i> Hỏi Trợ lý AI về dịch vụ này
                    </button>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="p-3 bg-white rounded-4 border shadow-sm d-inline-block text-start" style="min-width: 240px;">
                    <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Mức giá niêm yết từ</small>
                    <span class="d-block fs-3 fw-bold text-primary mb-1"><?php echo htmlspecialchars($detail['price_formatted']); ?></span>
                    <small class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i> Minh bạch 100% - Không phụ phí</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">

    <!-- 4 Quick KPI Highlight Cards -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-4 border shadow-sm text-center h-100">
                <i class="far fa-clock text-primary fs-3 mb-2 d-block"></i>
                <small class="text-muted d-block fw-semibold mb-1">Thời gian thực hiện</small>
                <strong class="text-dark-navy" style="font-size: 0.95rem;"><?php echo htmlspecialchars($detail['duration']); ?></strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-4 border shadow-sm text-center h-100">
                <i class="fas fa-feather-alt text-success fs-3 mb-2 d-block"></i>
                <small class="text-muted d-block fw-semibold mb-1">Mức độ cảm giác</small>
                <strong class="text-dark-navy" style="font-size: 0.95rem;"><?php echo htmlspecialchars($detail['pain_level']); ?></strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-4 border shadow-sm text-center h-100">
                <i class="fas fa-shield-alt text-warning fs-3 mb-2 d-block"></i>
                <small class="text-muted d-block fw-semibold mb-1">Chính sách bảo hành</small>
                <strong class="text-dark-navy" style="font-size: 0.95rem;"><?php echo htmlspecialchars($detail['warranty']); ?></strong>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-4 border shadow-sm text-center h-100">
                <i class="fas fa-microchip text-info fs-3 mb-2 d-block"></i>
                <small class="text-muted d-block fw-semibold mb-1">Tiêu chuẩn kỹ thuật</small>
                <strong class="text-dark-navy" style="font-size: 0.95rem;">Chuẩn ISO 9001</strong>
            </div>
        </div>
    </div>

    <!-- MAIN 3 PILLARS: 1. TÁC DỤNG RA SAO | 2. LÀM NHƯ THẾ NÀO | 3. GỒM CÁC BƯỚC GÌ -->
    <div class="row g-5">
        <!-- Cột Trái: Nội dung chi tiết chuyên sâu -->
        <div class="col-lg-8">

            <!-- KHỐI 1: TÁC DỤNG RA SAO & LỢI ÍCH -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-white">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="feature-icon-wrapper mb-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Câu hỏi 1</span>
                        <h3 class="fw-bold text-dark-navy mb-0" style="font-size: 1.6rem;">Tác Dụng Ra Sao & Lợi Ích Vượt Trội</h3>
                    </div>
                </div>

                <p class="text-muted mb-4" style="line-height: 1.7;">
                    Dịch vụ <strong><?php echo htmlspecialchars($detail['name']); ?></strong> tại Nha Khoa Quốc Tế Premier Dental được thiết kế để mang lại kết quả tối ưu nhất cho cả sức khỏe mô sinh học và thẩm mỹ nụ cười:
                </p>

                <div class="row g-3 mb-4">
                    <?php foreach ($detail['effects'] as $effect): ?>
                        <div class="col-12">
                            <div class="p-3 rounded-3 border-start border-4 border-primary bg-light d-flex align-items-start gap-3">
                                <i class="fas fa-check-circle text-primary fs-5 mt-1 flex-shrink-0"></i>
                                <span class="text-dark-navy" style="line-height: 1.6; font-size: 0.95rem;">
                                    <?php echo htmlspecialchars($effect); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Đối tượng phù hợp -->
                <div class="p-4 rounded-4" style="background: #f0f9ff; border: 1px dashed #0284c7;">
                    <h6 class="fw-bold text-primary mb-2"><i class="fas fa-user-check me-2"></i> Đối tượng chỉ định nên thực hiện:</h6>
                    <p class="mb-0 text-dark-navy" style="font-size: 0.93rem; line-height: 1.65;">
                        <?php echo htmlspecialchars($detail['target_audience']); ?>
                    </p>
                </div>
            </div>

            <!-- KHỐI 2: LÀM NHƯ THẾ NÀO & CÔNG NGHỆ ÁP DỤNG -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-white">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="feature-icon-wrapper mb-0" style="width: 48px; height: 48px; font-size: 1.25rem; background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);">
                        <i class="fas fa-cogs text-white"></i>
                    </div>
                    <div>
                        <span class="badge bg-info-subtle text-info fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Câu hỏi 2</span>
                        <h3 class="fw-bold text-dark-navy mb-0" style="font-size: 1.6rem;">Làm Như Thế Nào & Công Nghệ Áp Dụng</h3>
                    </div>
                </div>

                <div class="p-3 mb-4 rounded-3 bg-light border">
                    <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 0.75rem;">Hệ thống công nghệ & thiết bị:</small>
                    <strong class="text-primary fs-6"><?php echo htmlspecialchars($detail['technology']); ?></strong>
                </div>

                <h5 class="fw-bold text-dark-navy mb-3">Cơ chế kỹ thuật thực hiện:</h5>
                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 0.98rem;">
                    <?php echo htmlspecialchars($detail['how_it_works']); ?>
                </p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100 bg-white shadow-sm">
                            <h6 class="fw-bold text-dark-navy mb-2"><i class="fas fa-shield-virus text-success me-2"></i> Vô trùng khép kín Class B</h6>
                            <p class="text-muted small mb-0">100% dụng cụ hấp tiệt trùng nhiệt độ cao bằng lò Autoclave, đóng gói riêng biệt chống lây nhiễm chéo tuyệt đối.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100 bg-white shadow-sm">
                            <h6 class="fw-bold text-dark-navy mb-2"><i class="fas fa-heart text-danger me-2"></i> Kiểm soát cơn đau êm ái</h6>
                            <p class="text-muted small mb-0">Thoa gel tê bề mặt và tiêm vi điểm liều lượng chuẩn xác, giúp toàn bộ liệu trình diễn ra thư thái, dễ chịu.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KHỐI 3: GỒM CÁC BƯỚC GÌ - QUY TRÌNH CHUẨN Y KHOA -->
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-white">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="feature-icon-wrapper mb-0" style="width: 48px; height: 48px; font-size: 1.25rem; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                        <i class="fas fa-list-ol text-white"></i>
                    </div>
                    <div>
                        <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Câu hỏi 3</span>
                        <h3 class="fw-bold text-dark-navy mb-0" style="font-size: 1.6rem;">Gồm Các Bước Gì - Quy Trình Chuẩn Y Khoa</h3>
                    </div>
                </div>

                <p class="text-muted mb-4">
                    Tại Premier Dental, mọi dịch vụ đều tuân thủ nghiêm ngặt quy trình chuẩn quốc tế gồm các bước rõ ràng sau:
                </p>

                <!-- Treatment Steps Timeline -->
                <div class="treatment-steps-container">
                    <?php foreach ($detail['steps'] as $idx => $st): ?>
                        <div class="d-flex align-items-start gap-4 mb-4 pb-3 <?php echo ($idx < count($detail['steps']) - 1) ? 'border-bottom border-light-subtle' : ''; ?>">
                            <div class="step-circle-badge flex-shrink-0">
                                <span><?php echo $st['step']; ?></span>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark-navy mb-2"><?php echo htmlspecialchars($st['title']); ?></h5>
                                <p class="text-muted mb-0" style="line-height: 1.65; font-size: 0.94rem;">
                                    <?php echo htmlspecialchars($st['desc']); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Chăm sóc hậu phẫu -->
                <div class="p-3 bg-light rounded-3 mt-3 border">
                    <strong class="text-dark-navy d-block mb-1"><i class="fas fa-notes-medical text-primary me-2"></i> Hướng dẫn sau điều trị:</strong>
                    <small class="text-muted" style="line-height: 1.6; display: block;"><?php echo htmlspecialchars($detail['post_care']); ?></small>
                </div>
            </div>

            <!-- KHỐI 4: CÂU HỎI THƯỜNG GẶP (FAQ) -->
            <?php if (!empty($detail['faqs'])): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-white">
                    <h4 class="fw-bold text-dark-navy mb-4"><i class="far fa-question-circle text-info me-2"></i> Câu Hỏi Thường Gặp</h4>
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <?php foreach ($detail['faqs'] as $fIndex => $faq): ?>
                            <div class="accordion-item border-0 mb-3 rounded-3 overflow-hidden" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?php echo ($fIndex > 0) ? 'collapsed' : ''; ?> fw-bold text-dark-navy" type="button" data-bs-toggle="collapse" data-bs-target="#faq_<?php echo $fIndex; ?>" style="background: transparent;">
                                        <?php echo htmlspecialchars($faq['q']); ?>
                                    </button>
                                </h2>
                                <div id="faq_<?php echo $fIndex; ?>" class="accordion-collapse collapse <?php echo ($fIndex === 0) ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted pt-0" style="line-height: 1.7; font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($faq['a']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Cột Phải: Thẻ thông tin dịch vụ & Đặt lịch nhanh (Sticky) -->
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 90px;">
                
                <!-- Thẻ Tóm Tắt & Đặt Lịch -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <img src="<?php echo htmlspecialchars($detail['image']); ?>" alt="<?php echo htmlspecialchars($detail['name']); ?>" class="img-fluid rounded-3 mb-3 w-100" style="height: 200px; object-fit: cover;">
                    
                    <h5 class="fw-bold text-dark-navy mb-1"><?php echo htmlspecialchars($detail['name']); ?></h5>
                    <small class="text-muted d-block mb-3"><?php echo htmlspecialchars($detail['category']); ?></small>
                    
                    <div class="p-3 rounded-3 bg-light border mb-4">
                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.72rem;">Chi phí niêm yết:</small>
                        <span class="fs-4 fw-bold text-primary"><?php echo htmlspecialchars($detail['price_formatted']); ?></span>
                    </div>

                    <a href="booking.php?service_id=<?php echo $detail['id']; ?>" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-2" style="font-size: 1.05rem;">
                        <i class="far fa-calendar-alt me-1"></i> Đặt Lịch Khám Ngay
                    </a>
                    <small class="text-center text-muted d-block mb-3" style="font-size: 0.78rem;">
                        <i class="fas fa-bolt text-warning me-1"></i> Đặt trực tuyến nhận ưu tiên khám đúng giờ
                    </small>

                    <hr class="my-3">

                    <!-- Bác sĩ chuyên môn -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px; font-size: 1.1rem;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">Bác sĩ phụ trách chuyên khoa</small>
                            <strong class="text-dark-navy small d-block"><?php echo htmlspecialchars($detail['doctor_specialist']); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Thẻ Trợ lý AI Hỏi đáp nhanh -->
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #071527 0%, #0c2744 100%);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="status-indicator-dot online"></div>
                        <span class="text-info fw-bold small text-uppercase" style="letter-spacing: 0.05em;">Trợ lý AI Nha Khoa 24/7</span>
                    </div>
                    <h6 class="fw-bold mb-2">Bạn có câu hỏi riêng về dịch vụ này?</h6>
                    <p class="small text-white-50 mb-3" style="line-height: 1.6;">
                        Hỏi ngay Trợ lý AI để được tư vấn minh bạch về kỹ thuật, bảng giá và triệu chứng của bạn trong 5 giây!
                    </p>
                    <button type="button" class="btn btn-outline-info w-100 rounded-pill py-2 btn-sm fw-bold" onclick="askAiAboutService('<?php echo addslashes($detail['name']); ?>')">
                        <i class="fas fa-comment-dots me-1"></i> Trò chuyện ngay với AI
                    </button>
                </div>

                <!-- Danh sách dịch vụ khác -->
                <div class="mt-4 text-center">
                    <a href="services.php" class="text-primary text-decoration-none fw-semibold small">
                        <i class="fas fa-arrow-left me-1"></i> Xem toàn bộ 10 dịch vụ khác
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function askAiAboutService(serviceName) {
    const chatBtn = document.getElementById('openChatbotBtn');
    const chatWindow = document.getElementById('chatbotWindow');
    const chatInput = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('sendChatbotBtn');

    if (chatWindow && chatWindow.style.display === 'none') {
        chatWindow.style.display = 'flex';
    }
    if (chatInput) {
        chatInput.value = 'Tư vấn chi tiết cho tôi về dịch vụ ' + serviceName + ' (gồm các bước gì, làm như thế nào và tác dụng ra sao)';
        chatInput.focus();
        if (sendBtn) {
            sendBtn.click();
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
