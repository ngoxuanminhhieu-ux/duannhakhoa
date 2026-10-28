<?php
$current_page = 'contact';
$page_title = 'Liên Hệ & Địa Chỉ Phòng Khám - Premier Dental';
$is_page = true;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Banner Header -->
<div class="page-header-light">
    <div class="container text-center">
        <span class="badge-page-tag mb-3">
            <i class="fas fa-map-marked-alt text-primary me-1"></i> Kết Nối Trực Tiếp
        </span>
        <h1 class="page-header-title">Liên Hệ Với Chúng Tôi</h1>
        <p class="page-header-desc">Premier Dental luôn sẵn sàng lắng nghe mọi câu hỏi, thắc mắc và tư vấn giải pháp tốt nhất cho nụ cười của bạn.</p>
    </div>
</div>

<div class="container my-5 py-3">
    <div class="row g-4">
        <!-- Cột trái: Thông tin trực tiếp -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h4 class="fw-bold mb-4 text-dark-navy">Thông Tin Phòng Khám</h4>
                
                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-wrapper p-3 rounded-3" style="width: 50px; height: 50px; margin-bottom: 0;">
                        <i class="fas fa-map-marker-alt fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark-navy">Địa chỉ phòng khám</h6>
                        <p class="text-muted small mb-0">123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-wrapper p-3 rounded-3" style="width: 50px; height: 50px; margin-bottom: 0;">
                        <i class="fas fa-phone-volume fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark-navy">Tổng đài CSKH & Đặt lịch</h6>
                        <p class="text-muted small mb-0"><strong>1900 6868</strong> (Miễn phí cước gọi)</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-wrapper p-3 rounded-3" style="width: 50px; height: 50px; margin-bottom: 0;">
                        <i class="fas fa-bolt fs-5 text-danger"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark-navy">Hotline Cấp cứu 24/7</h6>
                        <p class="text-danger fw-bold small mb-0">0900.123.456 (Trực cấp cứu răng hàm mặt)</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3 mb-4">
                    <div class="feature-icon-wrapper p-3 rounded-3" style="width: 50px; height: 50px; margin-bottom: 0;">
                        <i class="fas fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark-navy">Email tiếp nhận ý kiến</h6>
                        <p class="text-muted small mb-0">contact@premierdental.vn</p>
                    </div>
                </div>

                <div class="d-flex align-items-start gap-3">
                    <div class="feature-icon-wrapper p-3 rounded-3" style="width: 50px; height: 50px; margin-bottom: 0;">
                        <i class="far fa-clock fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark-navy">Khung giờ mở cửa</h6>
                        <p class="text-muted small mb-0">Thứ 2 - Thứ 7: 08:00 - 20:00<br>Chủ Nhật: 08:00 - 17:30</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Form Góp ý & Đăng ký tư vấn -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h4 class="fw-bold mb-2 text-dark-navy">Gửi Tin Nhắn Cho Bác Sĩ</h4>
                <p class="text-muted small mb-4">Để lại thông tin và tình trạng răng miệng, chuyên viên y tế của Premier Dental sẽ phản hồi trong vòng 30 phút.</p>

                <form onsubmit="event.preventDefault(); window.showToast('Cảm ơn bạn! Chúng tôi đã tiếp nhận thông tin và sẽ gọi lại trong 30 phút.', 'success'); this.reset();">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-custom" placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control form-control-custom" placeholder="0901 234 567" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Chủ đề quan tâm</label>
                            <select class="form-select form-select-custom">
                                <option>Tư vấn Niềng răng Chỉnh nha</option>
                                <option>Tư vấn Cấy ghép Implant</option>
                                <option>Tư vấn Bọc răng sứ thẩm mỹ</option>
                                <option>Khám tổng quát & Lấy cao răng</option>
                                <option>Ý kiến đóng góp dịch vụ</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Nội dung thắc mắc <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-custom" rows="4" placeholder="Mô tả cụ thể câu hỏi hoặc triệu chứng của bạn..." required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-3 fw-bold w-100 shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i> Gửi Tin Nhắn Tư Vấn
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Clinic Maps & Locations Section -->
    <div class="mt-5 pt-4 border-top">
        <div class="text-center mb-4">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill mb-2 fw-semibold">
                <i class="fas fa-map-marked-alt me-1"></i> BẢN ĐỒ PHÒNG KHÁM
            </span>
            <h3 class="fw-bold text-dark">Chỉ Đường Trực Tiếp Đến Premier Dental</h3>
            <p class="text-muted small">Lựa chọn cơ sở gần bạn nhất để xem bản đồ chỉ đường và lộ trình giao thông thuận tiện</p>
        </div>

        <!-- Branch Selector Tabs -->
        <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
            <button type="button" class="branch-nav-btn-web active" data-branch="cs1" onclick="switchContactBranch('cs1')">
                <i class="fas fa-hospital me-1 text-primary"></i> Cơ Sở 1: Quận 5 (Trụ Sở Chính)
            </button>
            <button type="button" class="branch-nav-btn-web" data-branch="cs2" onclick="switchContactBranch('cs2')">
                <i class="fas fa-gem me-1 text-primary"></i> Cơ Sở 2: Quận 1 (Trung Tâm Thẩm Mỹ)
            </button>
            <button type="button" class="branch-nav-btn-web" data-branch="cs3" onclick="switchContactBranch('cs3')">
                <i class="fas fa-teeth-open me-1 text-primary"></i> Cơ Sở 3: Bình Thạnh (TT Implant KTS)
            </button>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="branch-info-card-web">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-3 w-auto align-self-start" id="contactBranchBadge">
                        <i class="fas fa-star me-1 text-warning"></i> Trụ sở chính chuẩn Quốc tế 5 sao
                    </span>
                    <h5 class="fw-bold text-dark mb-3" id="contactBranchTitle">Trụ Sở Chính - Premier Dental Clinic Quận 5</h5>

                    <ul class="list-unstyled mb-4 d-flex flex-column gap-3 small flex-grow-1">
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Địa chỉ:</strong>
                                <span class="text-muted" id="contactBranchAddress">123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-directions text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Lộ trình:</strong>
                                <span class="text-muted" id="contactBranchDirections">Mặt tiền đường Nguyễn Văn Cừ, cách ngã tư Trần Hưng Đạo 200m, đối diện Trường ĐH Sư Phạm TP.HCM.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-parking text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Bãi đỗ xe:</strong>
                                <span class="text-muted" id="contactBranchParking">Bãi đỗ ô tô & xe máy rộng rãi miễn phí, bảo vệ hỗ trợ 24/7.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fas fa-bus text-primary mt-1 fs-6"></i>
                            <div>
                                <strong class="d-block text-dark">Xe buýt:</strong>
                                <span class="text-muted" id="contactBranchBus">Tuyến 01, 11, 45, 56, 139 dừng ngay trước cửa phòng khám.</span>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <a id="contactBranchDirBtn" href="https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh" target="_blank" class="btn btn-primary w-100 fw-bold">
                            <i class="fas fa-diamond-turn-right me-1"></i> Mở Chỉ Đường Google Maps
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="branch-map-embed-web">
                    <iframe 
                        id="contactAboutMapIframe"
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
                        <span><i class="fas fa-map-pin text-primary me-1"></i> <span id="contactCurrentBranchName">Premier Dental Clinic Quận 5</span></span>
                        <a id="contactFullMapLink" href="https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh" target="_blank" class="text-primary text-decoration-none fw-semibold">
                            <i class="fas fa-external-link-alt me-1"></i> Xem Toàn Màn Hình
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchContactBranch(branchId) {
    const branches = {
        'cs1': {
            name: 'Trụ Sở Chính - Premier Dental Clinic Quận 5',
            badge: '<i class="fas fa-star me-1 text-warning"></i> Trụ sở chính chuẩn Quốc tế 5 sao',
            address: '123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh',
            directions: 'Mặt tiền đường Nguyễn Văn Cừ, cách ngã tư Trần Hưng Đạo 200m, đối diện Trường ĐH Sư Phạm TP.HCM.',
            parking: 'Bãi đỗ ô tô & xe máy rộng rãi miễn phí, bảo vệ hỗ trợ 24/7.',
            bus: 'Tuyến 01, 11, 45, 56, 139 dừng ngay trước cửa phòng khám.',
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
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.2323758364376!2d106.70295177570354!3d10.793444058874635!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528b497495b59%3A0x9ebffcc2a52ee78b!2zODggxJBp4buHbiBCacOqbiBQaOG7pywgUGjGsOG7nW5nIDE1LCBCw6xuaCBUaOG6oW5oLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2s!4v1700000000002!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=88+%C4%90i%E1%BB%87n+Bi%C3%AAn+Ph%E1%BB%A7,+Ph%C6%B0%E1%BB%9Dng+15,+B%C3%ACnh+Th%E1%BA%A1nh,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        }
    };

    const data = branches[branchId];
    if (!data) return;

    document.querySelectorAll('.branch-nav-btn-web').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-branch') === branchId);
    });

    const badgeEl = document.getElementById('contactBranchBadge');
    const titleEl = document.getElementById('contactBranchTitle');
    const addressEl = document.getElementById('contactBranchAddress');
    const directionsEl = document.getElementById('contactBranchDirections');
    const parkingEl = document.getElementById('contactBranchParking');
    const busEl = document.getElementById('contactBranchBus');
    const iframeEl = document.getElementById('contactAboutMapIframe');
    const dirBtnEl = document.getElementById('contactBranchDirBtn');
    const fullMapLink = document.getElementById('contactFullMapLink');
    const currentLocEl = document.getElementById('contactCurrentBranchName');

    if (badgeEl) badgeEl.innerHTML = data.badge;
    if (titleEl) titleEl.innerText = data.name;
    if (addressEl) addressEl.innerText = data.address;
    if (directionsEl) directionsEl.innerText = data.directions;
    if (parkingEl) parkingEl.innerText = data.parking;
    if (busEl) busEl.innerText = data.bus;
    if (iframeEl) iframeEl.src = data.mapUrl;
    if (dirBtnEl) dirBtnEl.href = data.dirUrl;
    if (fullMapLink) fullMapLink.href = data.dirUrl;
    if (currentLocEl) currentLocEl.innerText = data.name;
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
