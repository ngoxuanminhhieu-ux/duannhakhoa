<?php
$depth = isset($is_page) && $is_page ? '../' : './';
?>
    <!-- AI Chatbot Floating Widget -->
    <div class="chatbot-toggler" id="chatbotToggler" title="Tư vấn sức khỏe răng miệng AI 24/7">
        <div class="pulse-ring"></div>
        <i class="fas fa-robot"></i>
    </div>

    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div>
                    <div class="chatbot-title">Trợ Lý AI Nha Khoa Quốc Tế Premier</div>
                    <div class="chatbot-status">Trực tuyến 24/7</div>
                </div>
            </div>
            <button type="button" class="chatbot-close-btn" id="closeChatbot" title="Đóng cửa sổ chat">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chat-msg bot">
                Xin chào! Tôi là Trợ Lý AI của <strong>Nha Khoa Quốc Tế Premier</strong>. Tôi có thể tư vấn sức khỏe răng miệng, báo giá dịch vụ (Niềng răng, Implant, Răng sứ thẩm mỹ) hoặc hỗ trợ bạn đặt lịch khám miễn phí cùng Bác sĩ Chuyên khoa. Bạn cần tôi hỗ trợ vấn đề gì hôm nay?
            </div>
        </div>

        <!-- Quick Suggestion Chips -->
        <div class="chatbot-quick-chips" id="chatbotChips">
            <button type="button" class="chat-chip" data-query="Bảng giá niềng răng hiện nay?">🦷 Bảng giá niềng răng</button>
            <button type="button" class="chat-chip" data-query="Trụ Implant loại nào tốt nhất và chi phí bao nhiêu?">✨ Cấy ghép Implant</button>
            <button type="button" class="chat-chip" data-query="Tẩy trắng răng Laser có đau buốt không?">🌟 Tẩy trắng răng</button>
            <button type="button" class="chat-chip" data-query="Hướng dẫn tôi đặt lịch hẹn khám răng">📅 Đặt lịch khám</button>
        </div>
        
        <!-- Audio Listening Status Indicator -->
        <div class="chatbot-voice-status" id="chatbotVoiceStatus" style="display: none;">
            <div class="voice-wave">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <span class="voice-status-text" id="voiceStatusText">Đang lắng nghe... Hãy nói câu hỏi của bạn</span>
            <button type="button" class="voice-cancel-btn" id="cancelVoiceBtn" title="Hủy bỏ lắng nghe">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Live Camera Viewfinder Overlay -->
        <div class="chatbot-camera-overlay" id="chatbotCameraOverlay" style="display: none;">
            <div class="camera-overlay-header">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-camera text-info"></i>
                    <span class="fw-semibold small">Chụp ảnh răng / khoang miệng</span>
                </div>
                <button type="button" class="camera-close-btn" id="closeCameraBtn" title="Đóng camera">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="camera-video-container">
                <video id="chatbotCameraVideo" autoplay playsinline muted></video>
                <div class="camera-guide-box">
                    <span class="camera-guide-text"><i class="fas fa-crosshairs me-1"></i> Căn chỉnh hàm răng vào giữa khung</span>
                </div>
                <div class="camera-flash-effect" id="cameraFlashEffect"></div>
            </div>

            <div class="camera-overlay-toolbar">
                <button type="button" class="btn-cam-switch" id="switchCameraBtn" title="Đổi camera trước / sau">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button type="button" class="btn-cam-shutter" id="snapPhotoBtn" title="Chụp ảnh ngay">
                    <span class="shutter-inner"></span>
                </button>
                <button type="button" class="btn-cam-cancel" id="cancelCameraBtn" title="Hủy bỏ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <canvas id="chatbotCameraCanvas" style="display: none;"></canvas>
        </div>

        <!-- Image Upload Preview Box -->
        <div class="chatbot-img-preview-box" id="chatbotImagePreviewArea" style="display: none;">
            <div class="preview-img-wrapper">
                <img src="" id="chatbotPreviewThumb" alt="Ảnh xem trước">
                <button type="button" class="btn-remove-preview" id="removeChatbotImgBtn" title="Xóa ảnh này">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="preview-img-info">
                <span class="preview-img-name" id="chatbotPreviewName">anh_rang.jpg</span>
                <span class="preview-img-meta" id="chatbotPreviewMeta">Sẵn sàng phân tích</span>
            </div>
            <button type="button" class="btn-change-preview" id="changeChatbotImgBtn" title="Chọn ảnh khác">
                <i class="fas fa-sync-alt me-1"></i> Đổi ảnh
            </button>
        </div>
        
        <div class="chatbot-input-area">
            <!-- Hidden File Input for Device Files -->
            <input type="file" id="chatbotFileInput" accept="image/*,.jpg,.jpeg,.png,.webp,.jfif,.bmp" style="display: none;">
            <!-- Hidden Native Camera Input for Direct Mobile Capture Fallback -->
            <input type="file" id="chatbotCameraInput" accept="image/*" capture="environment" style="display: none;">
            
            <!-- Open Camera Button -->
            <button type="button" class="chatbot-tool-btn chatbot-cam-btn" id="openChatbotCameraBtn" title="Chụp ảnh trực tiếp từ Camera / Webcam">
                <i class="fas fa-camera"></i>
            </button>
            <!-- Upload File Button -->
            <button type="button" class="chatbot-tool-btn chatbot-upload-btn" id="uploadChatbotImgBtn" title="Tải ảnh răng/khoang miệng từ máy">
                <i class="fas fa-image"></i>
            </button>
            
            <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Nhập câu hỏi, dán ảnh (Ctrl+V) hoặc chụp..." autocomplete="off">
            <button type="button" class="chatbot-tool-btn chatbot-voice-btn" id="voiceChatbotBtn" title="Tra cứu & Hỏi đáp bằng giọng nói (Voice)">
                <i class="fas fa-microphone"></i>
            </button>
            <button type="button" class="chatbot-send-btn" id="sendChatbotBtn" title="Gửi câu hỏi">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Toast Container for Modern Notifications -->
    <div class="toast-container-custom" id="toastContainer"></div>

    <!-- Luxury Medical Footer -->
    <footer class="footer-clinic">
        <div class="container">
            <div class="row g-4 mb-5">
                <!-- Cột 1: Thông tin thương hiệu -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="brand-logo-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <div>
                            <h4 class="text-white mb-0 fw-bold">PREMIER DENTAL</h4>
                            <small class="text-info fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Nha Khoa Quốc Tế</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-4" style="line-height: 1.7;">
                        Hệ thống Nha Khoa Quốc Tế Premier được Sở Y Tế cấp phép hoạt động chính quy. Tiên phong áp dụng công nghệ chẩn đoán hình ảnh 3D CT ConeBeam và tích hợp Trợ lý AI Assistant mang đến nụ cười tự tin chuẩn y khoa.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="https://facebook.com" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://youtube.com" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-youtube"></i></a>
                        <a href="https://tiktok.com" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <!-- Cột 2: Danh mục dịch vụ -->
                <div class="col-lg-2 col-md-6">
                    <h5>Dịch Vụ Nổi Bật</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo $depth; ?>pages/services.php"><i class="fas fa-chevron-right text-info" style="font-size: 0.65rem;"></i> Niềng răng Chỉnh nha</a></li>
                        <li><a href="<?php echo $depth; ?>pages/services.php"><i class="fas fa-chevron-right text-info" style="font-size: 0.65rem;"></i> Cấy ghép Implant 3D</a></li>
                        <li><a href="<?php echo $depth; ?>pages/services.php"><i class="fas fa-chevron-right text-info" style="font-size: 0.65rem;"></i> Bọc răng sứ thẩm mỹ</a></li>
                        <li><a href="<?php echo $depth; ?>pages/services.php"><i class="fas fa-chevron-right text-info" style="font-size: 0.65rem;"></i> Tẩy trắng Laser</a></li>
                        <li><a href="<?php echo $depth; ?>pages/services.php"><i class="fas fa-chevron-right text-info" style="font-size: 0.65rem;"></i> Nhổ răng khôn Piezotome</a></li>
                        <li><a href="<?php echo $depth; ?>pages/my_appointments.php" class="text-info fw-semibold"><i class="far fa-calendar-check text-warning" style="font-size: 0.75rem;"></i> Tra cứu lịch hẹn của tôi</a></li>
                    </ul>
                </div>
                
                <!-- Cột 3: Giờ làm việc & Cơ sở -->
                <div class="col-lg-3 col-md-6">
                    <h5>Thời Gian Làm Việc</h5>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2 d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                            <span>Thứ 2 - Thứ 7:</span>
                            <strong class="text-white">08:00 - 20:00</strong>
                        </li>
                        <li class="mb-2 d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                            <span>Chủ Nhật:</span>
                            <strong class="text-white">08:00 - 17:30</strong>
                        </li>
                        <li class="pt-1 text-info fw-semibold">
                            <i class="fas fa-bolt me-1 text-warning"></i> Trực cấp cứu & tư vấn 24/7
                        </li>
                    </ul>
                    <div class="mt-3 p-3 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                        <small class="text-light d-block mb-1"><i class="fas fa-shield-alt text-info me-1"></i> Giấy phép hoạt động SYT:</small>
                        <strong class="text-white" style="font-size: 0.85rem;">Số 08925/HCM-GPHĐ</strong>
                    </div>
                </div>
                
                <!-- Cột 4: Thông tin liên hệ -->
                <div class="col-lg-3 col-md-6">
                    <h5>Liên Hệ Trực Tiếp</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt text-info mt-1"></i>
                            <span class="text-muted">123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-phone-alt text-info"></i>
                            <span class="text-white fw-bold">Tổng đài: 1900 6868</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-mobile-alt text-info"></i>
                            <span class="text-warning fw-bold">Hotline: 0900.123.456</span>
                        </li>
                        <li class="mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-envelope text-info"></i>
                            <span class="text-muted">contact@premierdental.vn</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong>Premier Dental Clinic</strong>. Toàn bộ bản quyền được bảo hộ.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="<?php echo $depth; ?>login.php?tab=login&role=admin" class="text-muted text-decoration-none me-3">
                            <i class="fas fa-user-shield me-1 text-info"></i> Cổng Quản Trị Admin
                        </a>
                        <a href="<?php echo $depth; ?>pages/booking.php" class="text-info text-decoration-none fw-semibold">
                            <i class="far fa-calendar-check me-1"></i> Đặt lịch trực tuyến
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Application JS -->
    <script src="<?php echo $depth; ?>assets/js/main.js"></script>
</body>
</html>
