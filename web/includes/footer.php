<?php
$depth = isset($is_page) && $is_page ? '../' : './';
?>
    <!-- AI Chatbot Floating Widget -->
    <button class="chatbot-toggler" id="chatbotToggler" title="Tư vấn AI Nha Khoa">
        <i class="fas fa-robot"></i>
    </button>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <i class="fas fa-user-md"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Trợ Lý AI Nha Khoa</h6>
                    <small style="opacity: 0.85;"><i class="fas fa-circle text-success me-1" style="font-size: 0.5rem;"></i> Trực tuyến 24/7</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="closeChatbot"></button>
        </div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chat-msg bot">
                Xin chào! Tôi là Trợ Lý AI của Nha Khoa Smile. Tôi có thể tư vấn sức khỏe răng miệng, thông tin dịch vụ hoặc hướng dẫn bạn đặt lịch hẹn. Bạn cần hỗ trợ gì hôm nay?
            </div>
        </div>
        
        <div class="chatbot-input-area">
            <input type="text" id="chatbotInput" class="form-control" placeholder="Nhập thắc mắc về răng miệng..." autocomplete="off">
            <button type="button" id="sendChatbotBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-tooth text-info fs-3"></i>
                        <h4 class="text-white mb-0 fw-bold">NHA KHOA SMILE</h4>
                    </div>
                    <p>Phòng khám nha khoa tiêu chuẩn quốc tế. Mang lại nụ cười rạng rỡ và sự tự tin trọn vẹn cho khách hàng với công nghệ hàng đầu.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5>Liên kết nhanh</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo $depth; ?>index.php">Trang chủ</a></li>
                        <li class="mb-2"><a href="<?php echo $depth; ?>pages/about.php">Giới thiệu</a></li>
                        <li class="mb-2"><a href="<?php echo $depth; ?>pages/services.php">Dịch vụ nha khoa</a></li>
                        <li class="mb-2"><a href="<?php echo $depth; ?>pages/doctors.php">Đội ngũ bác sĩ</a></li>
                        <li class="mb-2"><a href="<?php echo $depth; ?>pages/booking.php">Đặt lịch khám</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Giờ làm việc</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Thứ 2 - Thứ 6:</span>
                            <strong class="text-white">08:00 - 20:00</strong>
                        </li>
                        <li class="mb-2 d-flex justify-content-between">
                            <span>Thứ 7 - Chủ Nhật:</span>
                            <strong class="text-white">08:00 - 17:30</strong>
                        </li>
                        <li class="mb-2 text-info fs-7">
                            <i class="fas fa-headset me-1"></i> Trực cấp cứu 24/7
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Thông tin liên hệ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt text-info mt-1"></i>
                            <span>123 Đường Nguyễn Trãi, Quận 5, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-phone-alt text-info"></i>
                            <span>Hotline: <strong>1900 6868 - 0912 345 678</strong></span>
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-envelope text-info"></i>
                            <span>Email: contact@nhakhoasmile.vn</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-secondary opacity-25">
            
            <div class="row align-items-center py-3">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 small">&copy; <?php echo date('Y'); ?> Nha Khoa Smile. Tất cả quyền được bảo lưu.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="<?php echo $depth; ?>admin/login.php" class="text-muted small text-decoration-none me-3">
                        <i class="fas fa-lock me-1"></i> Quản trị Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo $depth; ?>assets/js/main.js"></script>
</body>
</html>
