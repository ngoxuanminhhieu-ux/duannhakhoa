<?php
$current_page = 'about';
$page_title = 'Giới Thiệu Về Chúng Tôi - Nha Khoa Quốc Tế Premier';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Banner Header -->
<div class="page-header-light">
    <div class="container text-center">
        <div class="mb-2">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">
                <i class="fas fa-award text-primary me-1"></i> Hành Trình 15 Năm Kiến Tạo Nụ Cười
            </span>
        </div>
        <h1 class="page-header-title fw-bold text-dark mt-2 mb-3">Hệ Thống Nha Khoa Quốc Tế Premier</h1>
        <p class="page-header-desc text-muted mx-auto" style="max-width: 780px;">
            Hơn 15 năm phát triển bền vững với sứ mệnh mang đến trải nghiệm nha khoa không đau, thẩm mỹ và an toàn chuẩn y khoa Quốc Tế cho hàng triệu gia đình Việt.
        </p>
    </div>
</div>

<div class="container my-5">
    <!-- 1. Key Metrics Strip -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-sm-6">
            <div class="about-stat-card-web">
                <div class="stat-icon"><i class="fas fa-history"></i></div>
                <h3>15+</h3>
                <p>Năm Phát Triển</p>
                <small>Khẳng định uy tín y khoa từ 2011</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="about-stat-card-web">
                <div class="stat-icon"><i class="fas fa-smile-beam"></i></div>
                <h3>50.000+</h3>
                <p>Nụ Cười Tái Sinh</p>
                <small>Khách hàng trong và ngoài nước tin chọn</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="about-stat-card-web">
                <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                <h3>100%</h3>
                <p>Bác Sĩ CKI & Thạc Sĩ</p>
                <small>Đại học Y Dược, tu nghiệp Pháp - Hoa Kỳ</small>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="about-stat-card-web">
                <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>0 VNĐ</h3>
                <p>Khám & Chụp Phim 3D</p>
                <small>Minh bạch chi phí 100% trước điều trị</small>
            </div>
        </div>
    </div>

    <!-- 2. Brand Story & Overview -->
    <div class="row align-items-center g-5 mb-5 py-3">
        <div class="col-lg-6">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-tooth me-1"></i> VỀ CHÚNG TÔI
            </span>
            <h2 class="fw-bold mb-4" style="color: #0f172a; font-size: 2.1rem; line-height: 1.3;">
                Tiên Phong Công Nghệ Nha Khoa Không Đau Chuẩn Y Khoa
            </h2>
            <p class="text-muted" style="line-height: 1.75;">
                Được thành lập từ năm 2011, <strong>Nha Khoa Quốc Tế Premier (Premier Dental Clinic)</strong> tự hào là một trong những trung tâm điều trị và phục hình răng miệng thẩm mỹ hàng đầu tại Việt Nam, được Sở Y Tế TP. Hồ Chí Minh cấp phép hoạt động chính quy số <strong>08925/HCM-GPHĐ</strong>.
            </p>
            <p class="text-muted" style="line-height: 1.75;">
                Chúng tôi hiểu rằng khám răng từng là nỗi âu lo và e ngại ê buốt của nhiều khách hàng. Vì vậy, kim chỉ nam trong mọi hoạt động của Premier Dental là <em>"Bảo tồn tối đa răng thật – Điều trị nhẹ nhàng, chuẩn y khoa – Tận tâm như người thân"</em>. Mỗi khách hàng đều được lập phác đồ cá nhân hóa 1-1, kiểm tra kỹ lưỡng và giải thích minh bạch chi phí trước khi thực hiện.
            </p>

            <div class="row g-3 mt-3">
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <i class="fas fa-heart text-danger fs-3 mb-2"></i>
                        <h6 class="fw-bold mb-1">Tận Tâm</h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Đồng hành trọn đời nụ cười</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <i class="fas fa-shield-virus text-primary fs-3 mb-2"></i>
                        <h6 class="fw-bold mb-1">Vô Trùng Class B</h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Chuẩn khép kín CHLB Đức</small>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3 border text-center">
                        <i class="fas fa-file-contract text-success fs-3 mb-2"></i>
                        <h6 class="fw-bold mb-1">Minh Bạch 100%</h6>
                        <small class="text-muted" style="font-size: 0.78rem;">Báo giá trọn gói văn bản</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="position-relative">
                <img src="../assets/images/doctor-consult.jpg" alt="Bác sĩ chuyên khoa tư vấn kế hoạch điều trị nụ cười tại Premier Dental" class="img-fluid rounded-4 shadow-lg w-100">
                <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded-3 shadow border" style="max-width: 280px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 text-center" style="width: 44px; height: 44px;">
                            <i class="fas fa-check-circle fs-4"></i>
                        </div>
                        <div>
                            <strong class="d-block text-dark small">Chứng nhận Y Khoa Quốc Tế</strong>
                            <small class="text-muted" style="font-size: 0.75rem;">100% Bác sĩ Chuyên khoa RHM</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Three Pillars: Vision - Mission - Philosophy -->
    <div class="my-5 py-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-compass me-1"></i> NGUYÊN TẮC PHỤNG SỰ
            </span>
            <h2 class="fw-bold text-dark">Tầm Nhìn, Sứ Mệnh & Triết Lý Y Đức</h2>
            <p class="text-muted">Định hình tiêu chuẩn điều trị nha khoa hiện đại, nhân văn và trường tồn</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="pillar-card-web">
                    <div class="pillar-icon-box-web"><i class="fas fa-eye"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Tầm Nhìn Chiến Lược</h5>
                    <p class="text-muted small" style="line-height: 1.7;">
                        Trở thành hệ thống nha khoa kỹ thuật số điều trị <strong>không đau – thẩm mỹ – an toàn</strong> hàng đầu Việt Nam, là điểm tựa niềm tin vững chắc cho hàng triệu nụ cười Việt và kiều bào trên toàn thế giới.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pillar-card-web highlight">
                    <div class="pillar-icon-box-web"><i class="fas fa-hand-holding-heart"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Sứ Mệnh Phụng Sự</h5>
                    <p class="text-muted small" style="line-height: 1.7;">
                        Xóa bỏ định kiến sợ đau trong khám nha khoa bằng công nghệ y học hiện đại nhất. Mang lại giải pháp điều trị tiêu chuẩn Châu Âu với mức chi phí hợp lý, minh bạch và chính sách trả góp 0% cho mọi người dân.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pillar-card-web">
                    <div class="pillar-icon-box-web"><i class="fas fa-stethoscope"></i></div>
                    <h5 class="fw-bold text-dark mb-3">Triết Lý Y Đức & Điều Trị</h5>
                    <p class="text-muted small" style="line-height: 1.7;">
                        <strong>"Bảo tồn tối đa răng thật – Điều trị đúng người, đúng bệnh"</strong>. Đội ngũ y bác sĩ tuân thủ nghiêm ngặt y đức: chẩn đoán 1-1 trung thực, phác đồ cá nhân hóa và tuyệt đối không mài răng quá mức chỉ định.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Advanced Technologies & Facilities -->
    <div class="my-5 py-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-microchip me-1"></i> CƠ SỞ VẬT CHẤT CAO CẤP
            </span>
            <h2 class="fw-bold text-dark">Hệ Thống Công Nghệ Kỹ Thuật Số Chuẩn Châu Âu</h2>
            <p class="text-muted">Đồng bộ 100% trang thiết bị nhập khẩu chính hãng từ Đức, Thụy Sĩ, Ý và Hoa Kỳ</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-x-ray"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Máy Chụp Phim 3D CT Cone Beam</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Khảo sát toàn diện cấu trúc xương hàm 3 chiều với độ phân giải siêu nét chỉ trong 10 giây, giảm 90% lượng tia xạ so với X-quang thường.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Chụp 10s siêu nhanh</span>
                        <span class="badge bg-light text-primary border">Độ chính xác milimet</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-magic"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Máy Quét Dấu Răng 3D iTero 5D</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Quét dấu quang học kỹ thuật số thay thế lấy dấu thạch cao truyền thống. Mô phỏng phác đồ niềng răng và thấy trước nụ cười tương lai chỉ sau 60 giây.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Quét dấu quang học 60s</span>
                        <span class="badge bg-light text-primary border">Mô phỏng nụ cười</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-wave-square"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Máy Nhổ Răng Siêu Âm Piezotome</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Sử dụng sóng siêu âm cao tần phân tách răng khôn nhẹ nhàng mà không tổn thương mô mềm và màng xương. Giảm sưng đau và lành thương siêu tốc.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Không đau – Không sưng</span>
                        <span class="badge bg-light text-primary border">Lành thương nhanh</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-shield-virus"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Vô Trùng Melag Class B (Đức)</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Hệ thống tiệt trùng 1 chiều khép kín đạt tiêu chuẩn ISO Class B Châu Âu. Mọi dụng cụ đều được sấy chân không 134°C và đóng gói niêm phong riêng biệt.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Chuẩn ISO Class B</span>
                        <span class="badge bg-light text-primary border">Chống lây nhiễm 100%</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-laptop-medical"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Thiết Kế Nụ Cười DSD</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Phần mềm Digital Smile Design phân tích tỉ lệ vàng nhân trắc học khuôn mặt, giúp khách hàng cùng bác sĩ lựa chọn dáng răng thẩm mỹ ưng ý nhất trước khi làm.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Tỷ lệ vàng nhân trắc</span>
                        <span class="badge bg-light text-primary border">Xem trước kết quả</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="about-tech-card-web">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="about-tech-icon-web"><i class="fas fa-chair"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Ghế Điều Trị Stern Weber (Ý)</h6>
                    </div>
                    <p class="text-muted small flex-grow-1" style="line-height: 1.65;">
                        Dòng ghế phẫu thuật cao cấp nhập khẩu nguyên chiếc từ Italia với đệm êm ái công thái học, tích hợp màn hình HD và hệ thống tự động khử trùng đường nước.
                    </p>
                    <div class="d-flex flex-wrap gap-1 mt-auto">
                        <span class="badge bg-light text-primary border">Nhập khẩu chính hãng Ý</span>
                        <span class="badge bg-light text-primary border">Tự khử trùng ống nước</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. 15-Year Milestone Journey -->
    <div class="my-5 py-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-history me-1"></i> DẤU ẤN BỀN VỮNG
            </span>
            <h2 class="fw-bold text-dark">Hành Trình 15 Năm Phát Triển</h2>
            <p class="text-muted">Từng bước khẳng định vị thế nha khoa công nghệ cao hàng đầu</p>
        </div>

        <div class="row g-3">
            <div class="col-lg-3 col-sm-6">
                <div class="milestone-box-web">
                    <span class="milestone-year-web">2011</span>
                    <h6 class="fw-bold text-dark mb-2">Thành Lập Cơ Sở Đầu Tiên</h6>
                    <p class="text-muted small mb-0">Khởi đầu tại Quận 5, TP.HCM với 5 ghế điều trị hiện đại, tập trung vào nha khoa tổng quát không đau.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="milestone-box-web">
                    <span class="milestone-year-web">2016</span>
                    <h6 class="fw-bold text-dark mb-2">Đạt Chuẩn Vô Trùng Quốc Tế</h6>
                    <p class="text-muted small mb-0">Đạt chứng nhận vô trùng ISO Class B; thành lập Trung tâm chuyên sâu Cấy ghép Implant & Chỉnh nha kỹ thuật số.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="milestone-box-web">
                    <span class="milestone-year-web">2020</span>
                    <h6 class="fw-bold text-dark mb-2">Số Hóa Toàn Diện</h6>
                    <p class="text-muted small mb-0">Đầu tư hệ thống 3D CT ConeBeam và máy quét răng iTero 5D; cán mốc phục vụ hơn 30.000 bệnh nhân.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="milestone-box-web">
                    <span class="milestone-year-web">2024 - 2026</span>
                    <h6 class="fw-bold text-dark mb-2">Nâng Tầm Chuỗi Cơ Sở & AI</h6>
                    <p class="text-muted small mb-0">Phát triển chuỗi cơ sở chuẩn 5 sao tại các quận trung tâm TP.HCM, tích hợp Trợ lý Trí tuệ Nhân tạo AI chăm sóc 24/7.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. 5-Step Treatment Procedure -->
    <div class="my-5 py-3">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-clipboard-check me-1"></i> TIÊU CHUẨN AN TOÀN
            </span>
            <h2 class="fw-bold text-dark">Quy Trình Khám & Điều Trị 5 Bước Chuẩn Y Khoa</h2>
            <p class="text-muted">Khép kín, an toàn tuyệt đối, nhanh chóng và minh bạch từ tiếp đón đến sau điều trị</p>
        </div>

        <div class="row g-3">
            <div class="col-lg col-md-4 col-sm-6">
                <div class="step-card-web">
                    <div class="step-number-web">01</div>
                    <h6 class="fw-bold text-dark mb-2">Tiếp Đón & Chụp 3D</h6>
                    <p class="text-muted small mb-0">Lễ tân tiếp đón chu đáo, miễn phí chụp phim 3D CT Cone Beam và khám tổng quát 0đ.</p>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="step-card-web">
                    <div class="step-number-web">02</div>
                    <h6 class="fw-bold text-dark mb-2">Hội Chẩn & Phác Đồ</h6>
                    <p class="text-muted small mb-0">Bác sĩ chuyên khoa đọc phim, phân tích khớp cắn và lập phác đồ cá nhân hóa 1-1.</p>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="step-card-web">
                    <div class="step-number-web">03</div>
                    <h6 class="fw-bold text-dark mb-2">Minh Bạch Chi Phí</h6>
                    <p class="text-muted small mb-0">Báo giá trọn gói văn bản, ký cam kết hiệu quả điều trị và hợp đồng bảo hành chính hãng.</p>
                </div>
            </div>
            <div class="col-lg col-md-6 col-sm-6">
                <div class="step-card-web">
                    <div class="step-number-web">04</div>
                    <h6 class="fw-bold text-dark mb-2">Điều Trị Nhẹ Nhàng</h6>
                    <p class="text-muted small mb-0">Thực hiện trong phòng thủ thuật vô trùng tuyệt đối với công nghệ gây tê không đau.</p>
                </div>
            </div>
            <div class="col-lg col-md-6 col-sm-12">
                <div class="step-card-web">
                    <div class="step-number-web">05</div>
                    <h6 class="fw-bold text-dark mb-2">Bảo Hành & Tái Khám</h6>
                    <p class="text-muted small mb-0">Kích hoạt thẻ bảo hành điện tử chính hãng, hướng dẫn chăm sóc và hẹn lịch tái khám.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Legal and Warranty Box -->
    <div class="p-4 p-md-5 rounded-4 text-white my-5 shadow-lg" style="background: linear-gradient(135deg, #071527 0%, #0c2744 50%, #0a3a60 100%);">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h4 class="fw-bold mb-3 d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-stamp text-warning"></i> Giấy Phép Hoạt Động & Cam Kết Pháp Lý
                </h4>
                <p class="text-light text-opacity-75 small mb-3" style="line-height: 1.7;">
                    Nha Khoa Quốc Tế Premier hoạt động hoàn toàn chính quy dưới sự kiểm duyệt và cấp phép của <strong>Sở Y Tế TP. Hồ Chí Minh</strong> (Giấy phép số <strong>08925/HCM-GPHĐ</strong>). Toàn bộ trụ Implant, khay niềng Invisalign và phôi sứ thẩm mỹ đều có tem chứng nhận nguồn gốc xuất xứ toàn cầu từ Thụy Sĩ, Mỹ và Đức.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-white bg-opacity-10 border border-light border-opacity-25 px-3 py-2 small">
                        <i class="fas fa-certificate text-info me-1"></i> Giấy phép SYT: 08925/HCM-GPHĐ
                    </span>
                    <span class="badge bg-white bg-opacity-10 border border-light border-opacity-25 px-3 py-2 small">
                        <i class="fas fa-shield-alt text-warning me-1"></i> Bảo hành Implant Trọn Đời
                    </span>
                    <span class="badge bg-white bg-opacity-10 border border-light border-opacity-25 px-3 py-2 small">
                        <i class="fas fa-credit-card text-success me-1"></i> Trả góp 0% liên kết 25+ ngân hàng
                    </span>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end text-center">
                <a href="<?php echo $depth; ?>pages/booking.php" class="btn btn-primary btn-lg shadow px-4 py-3 fw-bold">
                    <i class="far fa-calendar-check me-2"></i> Đặt Lịch Khám & Chụp Phim 0đ
                </a>
                <div class="mt-2 text-light text-opacity-75 small">
                    <i class="fas fa-phone-alt me-1 text-warning"></i> Hotline 24/7: <strong>1900 6868</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 8. CLINIC MAPS & LOCATIONS SECTION -->
<div class="py-5" style="background: #f0f9ff; border-top: 1px solid #bae6fd; border-bottom: 1px solid #bae6fd;" id="clinicMapSection">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-map-marked-alt me-1"></i> HỆ THỐNG CƠ SỞ & BẢN ĐỒ
            </span>
            <h2 class="fw-bold text-dark">Vị Trí Phòng Khám & Bản Đồ Chỉ Đường Trực Tiếp</h2>
            <p class="text-muted">Vị trí đắc địa tại mặt tiền các trục đường trung tâm TP.HCM, bãi đậu ô tô rộng rãi và bảo vệ túc trực 24/7</p>
        </div>

        <!-- Branch Selector Tabs -->
        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
            <button type="button" class="branch-nav-btn-web active" data-branch="cs1" onclick="switchWebAboutBranch('cs1')">
                <i class="fas fa-hospital me-1 text-primary"></i> Cơ Sở 1: Quận 5 (Trụ Sở Chính)
            </button>
            <button type="button" class="branch-nav-btn-web" data-branch="cs2" onclick="switchWebAboutBranch('cs2')">
                <i class="fas fa-gem me-1 text-primary"></i> Cơ Sở 2: Quận 1 (Trung Tâm Thẩm Mỹ)
            </button>
            <button type="button" class="branch-nav-btn-web" data-branch="cs3" onclick="switchWebAboutBranch('cs3')">
                <i class="fas fa-teeth-open me-1 text-primary"></i> Cơ Sở 3: Bình Thạnh (TT Implant KTS)
            </button>
        </div>

        <!-- Map & Info Card Grid -->
        <div class="row g-4 align-items-stretch">
            <!-- Left: Branch Info Card -->
            <div class="col-lg-5">
                <div class="branch-info-card-web">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-3 w-auto align-self-start" id="webBranchBadge">
                        <i class="fas fa-star me-1 text-warning"></i> Trụ sở chính chuẩn Quốc tế 5 sao
                    </span>
                    <h4 class="fw-bold text-dark mb-3" id="webBranchTitle">Trụ Sở Chính - Premier Dental Clinic Quận 5</h4>

                    <ul class="list-unstyled mb-4 d-flex flex-column gap-3 small flex-grow-1">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Địa chỉ chính xác:</strong>
                                <span class="text-muted" id="webBranchAddress">123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-directions text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Hướng dẫn lộ trình:</strong>
                                <span class="text-muted" id="webBranchDirections">Nằm ngay mặt tiền đường Nguyễn Văn Cừ, cách ngã tư Trần Hưng Đạo 200m, đối diện Trường Đại học Sư Phạm TP.HCM.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-parking text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Bãi đỗ xe ô tô & xe máy:</strong>
                                <span class="text-muted" id="webBranchParking">Bãi đỗ xe ô tô & xe máy rộng rãi miễn phí, bảo vệ trông giữ an toàn 24/7.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-bus text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Tuyến xe buýt thuận tiện:</strong>
                                <span class="text-muted" id="webBranchBus">Tuyến xe buýt số 01, 11, 45, 56, 139 dừng ngay trước cửa phòng khám.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-clock text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Thời gian làm việc:</strong>
                                <span class="text-muted" id="webBranchHours">Thứ 2 - Thứ 7: 08:00 - 20:00 (Làm việc thông trưa) | Chủ Nhật: 08:00 - 17:30</span>
                            </div>
                        </li>
                    </ul>

                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="d-flex flex-wrap gap-2 small text-dark fw-semibold">
                            <span><i class="fas fa-wifi text-primary me-1"></i> Wifi Tốc Độ Cao</span>
                            <span>•</span>
                            <span><i class="fas fa-coffee text-primary me-1"></i> Trà Nước VIP</span>
                            <span>•</span>
                            <span><i class="fas fa-wheelchair text-primary me-1"></i> Lối Đi Xe Lăn</span>
                        </div>
                    </div>

                    <div class="row g-2 mt-auto">
                        <div class="col-6">
                            <a id="webBranchDirBtn" href="https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh" target="_blank" class="btn btn-primary w-100 fw-bold small">
                                <i class="fas fa-diamond-turn-right me-1"></i> Chỉ Đường Maps
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo $depth; ?>pages/booking.php" class="btn btn-outline-primary w-100 fw-bold small">
                                <i class="far fa-calendar-check me-1"></i> Đặt Lịch Khám
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Interactive Google Map Embed -->
            <div class="col-lg-7">
                <div class="branch-map-embed-web">
                    <iframe 
                        id="webAboutMapIframe"
                        title="Bản đồ định vị Nha Khoa Quốc Tế Premier"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.669658423711!2d106.68006847570305!3d10.759917059495817!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa066518d9f042e0!2zMTIzIMSQxrDhu51uZyBOZ3V54buFbiBWxINuIEPhu6ssIFBoxrDhu51uZyA0LCBRdeG6rW4gNSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <div class="p-2 px-3 bg-white border-top d-flex justify-content-between align-items-center small text-muted">
                        <span><i class="fas fa-map-pin text-primary me-1"></i> <span id="webCurrentBranchName">Premier Dental Clinic Quận 5</span></span>
                        <a id="webFullMapLink" href="https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh" target="_blank" class="text-primary text-decoration-none fw-semibold">
                            <i class="fas fa-external-link-alt me-1"></i> Xem Toàn Màn Hình
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Switch branch function for web/pages/about.php
function switchWebAboutBranch(branchId) {
    const branches = {
        'cs1': {
            name: 'Trụ Sở Chính - Premier Dental Clinic Quận 5',
            badge: '<i class="fas fa-star me-1 text-warning"></i> Trụ sở chính chuẩn Quốc tế 5 sao',
            address: '123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh',
            directions: 'Nằm ngay mặt tiền đường Nguyễn Văn Cừ, cách ngã tư Trần Hưng Đạo 200m, đối diện Trường Đại học Sư Phạm TP.HCM.',
            parking: 'Bãi đỗ xe ô tô & xe máy rộng rãi miễn phí, bảo vệ trông giữ an toàn 24/7.',
            bus: 'Tuyến xe buýt số 01, 11, 45, 56, 139 dừng ngay trước cửa phòng khám.',
            hours: 'Thứ 2 - Thứ 7: 08:00 - 20:00 (Làm việc thông trưa) | Chủ Nhật: 08:00 - 17:30',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.669658423711!2d106.68006847570305!3d10.759917059495817!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa066518d9f042e0!2zMTIzIMSQxrDhu51uZyBOZ3V54buFbiBWxINuIEPhu6ssIFBoxrDhu51uZyA0LCBRdeG6rW4gNSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        },
        'cs2': {
            name: 'Chi Nhánh Quận 1 - Premier Dental Luxury Center',
            badge: '<i class="fas fa-gem me-1 text-warning"></i> Trung tâm Thẩm mỹ & Chỉnh nha Cao cấp',
            address: '45 Lê Duẩn, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            directions: 'Ngay trục đường ngoại giao trung tâm Quận 1, gần Nhà thờ Đức Bà và Dinh Độc Lập.',
            parking: 'Hầm đỗ xe ô tô tòa nhà miễn phí cho khách hàng của nha khoa.',
            bus: 'Tuyến xe buýt số 03, 19, 30, 45, 93 dừng tại trạm Công xã Paris.',
            hours: 'Thứ 2 - Thứ 7: 08:30 - 20:30 | Chủ Nhật: 08:30 - 18:00',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.387926941196!2d106.6980483757034!3d10.781577759095692!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f483c66f60d%3A0x63390f772fc7ee65!2zNDUgTMOqIER14bqpbiwgQuG6v24gTmdow6ksIFF14bqtbiAxLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2s!4v1700000000001!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=45+L%C3%AA+Du%E1%BA%A9n,+B%E1%BA%BFn+Ngh%C3%A9,+Qu%E1%BA%ADn+1,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        },
        'cs3': {
            name: 'Chi Nhánh Bình Thạnh - Premier Dental Digital Implant Center',
            badge: '<i class="fas fa-teeth-open me-1 text-warning"></i> Trung tâm Cấy ghép Implant Kỹ thuật số',
            address: '88 Điện Biên Phủ, Phường 15, Quận Bình Thạnh, TP. Hồ Chí Minh',
            directions: 'Nằm ngay trục đường lớn kết nối Quận 1 và Bình Thạnh, chân cầu Điện Biên Phủ.',
            parking: 'Sân đỗ xe ô tô và bãi giữ xe máy có mái che, bảo vệ túc trực 24/7.',
            bus: 'Tuyến xe buýt số 06, 14, 54, 150 dừng ngay trạm Cầu Điện Biên Phủ.',
            hours: 'Thứ 2 - Thứ 7: 08:00 - 20:00 | Chủ Nhật: 08:00 - 17:30',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.2323758364376!2d106.70295177570354!3d10.793444058874635!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528b497495b59%3A0x9ebffcc2a52ee78b!2zODggxJBp4buHbiBCacOqbiBQaOG7pywgUGjGsOG7nW5nIDE1LCBCw6xuaCBUaOG6oW5oLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2s!4v1700000000002!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=88+%C4%90i%E1%BB%87n+Bi%C3%AAn+Ph%E1%BB%A7,+Ph%C6%B0%E1%BB%9Dng+15,+B%C3%ACnh+Th%E1%BA%A1nh,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        }
    };

    const data = branches[branchId];
    if (!data) return;

    // Toggle active state on buttons
    document.querySelectorAll('.branch-nav-btn-web').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-branch') === branchId);
    });

    // Update content elements
    const badgeEl = document.getElementById('webBranchBadge');
    const titleEl = document.getElementById('webBranchTitle');
    const addressEl = document.getElementById('webBranchAddress');
    const directionsEl = document.getElementById('webBranchDirections');
    const parkingEl = document.getElementById('webBranchParking');
    const busEl = document.getElementById('webBranchBus');
    const hoursEl = document.getElementById('webBranchHours');
    const iframeEl = document.getElementById('webAboutMapIframe');
    const dirBtnEl = document.getElementById('webBranchDirBtn');
    const fullMapLink = document.getElementById('webFullMapLink');
    const currentLocEl = document.getElementById('webCurrentBranchName');

    if (badgeEl) badgeEl.innerHTML = data.badge;
    if (titleEl) titleEl.innerText = data.name;
    if (addressEl) addressEl.innerText = data.address;
    if (directionsEl) directionsEl.innerText = data.directions;
    if (parkingEl) parkingEl.innerText = data.parking;
    if (busEl) busEl.innerText = data.bus;
    if (hoursEl) hoursEl.innerText = data.hours;
    if (iframeEl) iframeEl.src = data.mapUrl;
    if (dirBtnEl) dirBtnEl.href = data.dirUrl;
    if (fullMapLink) fullMapLink.href = data.dirUrl;
    if (currentLocEl) currentLocEl.innerText = data.name;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
