/* ==========================================================================
   PREMIER DENTAL CLINIC - CLIENT-SIDE JAVASCRIPT
   Modern UI Interactions, Real-Time Validation & AI Chatbot Controller
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

  // ------------------------------------------------------------------------
  // 1. Toast Notification System
  // ------------------------------------------------------------------------
  window.showToast = function (message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast-custom';

    let iconHtml = '<i class="fas fa-info-circle text-info fs-5"></i>';
    let borderColor = 'var(--primary)';

    if (type === 'success') {
      iconHtml = '<i class="fas fa-check-circle text-success fs-5"></i>';
      borderColor = 'var(--success)';
    } else if (type === 'error' || type === 'danger') {
      iconHtml = '<i class="fas fa-exclamation-circle text-danger fs-5"></i>';
      borderColor = 'var(--danger)';
    } else if (type === 'warning') {
      iconHtml = '<i class="fas fa-exclamation-triangle text-warning fs-5"></i>';
      borderColor = 'var(--warning)';
    }

    toast.style.borderLeftColor = borderColor;
    toast.innerHTML = `
      ${iconHtml}
      <div class="flex-grow-1 text-dark fw-medium" style="font-size: 0.92rem;">${message}</div>
      <button type="button" class="btn-close btn-sm shadow-none ms-2" style="font-size: 0.75rem;"></button>
    `;

    container.appendChild(toast);

    const closeBtn = toast.querySelector('.btn-close');
    closeBtn.addEventListener('click', () => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(40px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    });

    // Auto dismiss after 4.5 seconds
    setTimeout(() => {
      if (toast.parentNode) {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(40px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }
    }, 4500);
  };

  // ------------------------------------------------------------------------
  // 2. AI Chatbot Widget Controller
  // ------------------------------------------------------------------------
  const chatbotToggler = document.getElementById('chatbotToggler');
  const chatbotWindow = document.getElementById('chatbotWindow');
  const closeChatbot = document.getElementById('closeChatbot');
  const chatbotInput = document.getElementById('chatbotInput');
  const sendChatbotBtn = document.getElementById('sendChatbotBtn');
  const chatbotMessages = document.getElementById('chatbotMessages');
  const chatbotChips = document.getElementById('chatbotChips');
  const voiceChatbotBtn = document.getElementById('voiceChatbotBtn');
  const chatbotVoiceStatus = document.getElementById('chatbotVoiceStatus');
  const voiceStatusText = document.getElementById('voiceStatusText');
  const cancelVoiceBtn = document.getElementById('cancelVoiceBtn');

  // Image & Camera Elements
  const openChatbotCameraBtn = document.getElementById('openChatbotCameraBtn');
  const uploadChatbotImgBtn = document.getElementById('uploadChatbotImgBtn');
  const chatbotFileInput = document.getElementById('chatbotFileInput');
  const chatbotCameraInput = document.getElementById('chatbotCameraInput');
  const chatbotCameraOverlay = document.getElementById('chatbotCameraOverlay');
  const chatbotCameraVideo = document.getElementById('chatbotCameraVideo');
  const chatbotCameraCanvas = document.getElementById('chatbotCameraCanvas');
  const closeCameraBtn = document.getElementById('closeCameraBtn');
  const cancelCameraBtn = document.getElementById('cancelCameraBtn');
  const snapPhotoBtn = document.getElementById('snapPhotoBtn');
  const switchCameraBtn = document.getElementById('switchCameraBtn');
  const cameraFlashEffect = document.getElementById('cameraFlashEffect');

  const chatbotImagePreviewArea = document.getElementById('chatbotImagePreviewArea');
  const chatbotPreviewThumb = document.getElementById('chatbotPreviewThumb');
  const chatbotPreviewName = document.getElementById('chatbotPreviewName');
  const chatbotPreviewMeta = document.getElementById('chatbotPreviewMeta');
  const removeChatbotImgBtn = document.getElementById('removeChatbotImgBtn');
  const changeChatbotImgBtn = document.getElementById('changeChatbotImgBtn');

  let selectedImageFile = null;
  let selectedImageDataUrl = null;
  let cameraStream = null;
  let currentFacingMode = 'user'; // 'user' hoặc 'environment'

  if (chatbotToggler && chatbotWindow) {
    // Toggle chat window open/close
    chatbotToggler.addEventListener('click', function () {
      chatbotWindow.classList.toggle('active');
      if (chatbotWindow.classList.contains('active')) {
        setTimeout(() => {
          if (chatbotInput) chatbotInput.focus();
        }, 200);
      }
    });

    if (closeChatbot) {
      closeChatbot.addEventListener('click', function () {
        stopVoiceRecognition();
        closeCamera();
        chatbotWindow.classList.remove('active');
      });
    }

    // Quick Question Chips click listener
    if (chatbotChips) {
      chatbotChips.addEventListener('click', function (e) {
        const chip = e.target.closest('.chat-chip');
        if (chip && chatbotInput) {
          const query = chip.getAttribute('data-query');
          if (query) {
            chatbotInput.value = query;
            sendChatMessage();
          }
        }
      });
    }

    // ------------------------------------------------------------------------
    // Camera Viewfinder & Capture Logic
    // ------------------------------------------------------------------------
    async function openCamera() {
      // Kiểm tra hỗ trợ WebRTC getUserMedia
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        // Fallback sang input capture native trên thiết bị di động
        if (chatbotCameraInput) {
          chatbotCameraInput.value = '';
          chatbotCameraInput.click();
        } else if (chatbotFileInput) {
          chatbotFileInput.value = '';
          chatbotFileInput.click();
        }
        return;
      }

      try {
        if (chatbotCameraOverlay) {
          chatbotCameraOverlay.style.display = 'flex';
        }

        if (cameraStream) {
          cameraStream.getTracks().forEach(track => track.stop());
        }

        const constraints = {
          video: {
            facingMode: currentFacingMode,
            width: { ideal: 1280 },
            height: { ideal: 720 }
          },
          audio: false
        };

        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        if (chatbotCameraVideo) {
          chatbotCameraVideo.srcObject = cameraStream;
          await chatbotCameraVideo.play();
        }
      } catch (err) {
        console.warn('Không thể truy cập camera stream:', err);
        closeCamera();

        let errMsg = 'Không thể mở trực tiếp webcam. Vui lòng cấp quyền Camera trong trình duyệt.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
          errMsg = 'Bạn đã chặn quyền truy cập Camera. Vui lòng bật lại trong cài đặt trình duyệt hoặc chọn ảnh từ máy.';
        }

        if (typeof window.showToast === 'function') {
          window.showToast(errMsg, 'warning');
        }

        // Fallback sang input capture mobile
        if (chatbotCameraInput) {
          chatbotCameraInput.value = '';
          chatbotCameraInput.click();
        }
      }
    }

    function closeCamera() {
      if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
      }
      if (chatbotCameraVideo) {
        chatbotCameraVideo.srcObject = null;
      }
      if (chatbotCameraOverlay) {
        chatbotCameraOverlay.style.display = 'none';
      }
    }

    async function switchCamera() {
      currentFacingMode = currentFacingMode === 'user' ? 'environment' : 'user';
      await openCamera();
    }

    function snapPhoto() {
      if (!chatbotCameraVideo || !chatbotCameraCanvas) return;

      const video = chatbotCameraVideo;
      const canvas = chatbotCameraCanvas;

      const width = video.videoWidth || 640;
      const height = video.videoHeight || 480;

      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, width, height);

      // Hiệu ứng chớp flash
      if (cameraFlashEffect) {
        cameraFlashEffect.classList.add('flash');
        setTimeout(() => {
          cameraFlashEffect.classList.remove('flash');
        }, 180);
      }

      // Xuất Blob JPEG chất lượng cao
      canvas.toBlob(function (blob) {
        if (blob) {
          const fileName = `chup_camera_${Date.now()}.jpg`;
          const file = new File([blob], fileName, { type: 'image/jpeg' });
          closeCamera();
          handleImageSelect(file);
          if (typeof window.showToast === 'function') {
            window.showToast('Đã chụp ảnh khoang miệng thành công!', 'success');
          }
        }
      }, 'image/jpeg', 0.92);
    }

    if (openChatbotCameraBtn) {
      openChatbotCameraBtn.addEventListener('click', function () {
        openCamera();
      });
    }

    if (closeCameraBtn) {
      closeCameraBtn.addEventListener('click', closeCamera);
    }

    if (cancelCameraBtn) {
      cancelCameraBtn.addEventListener('click', closeCamera);
    }

    if (snapPhotoBtn) {
      snapPhotoBtn.addEventListener('click', snapPhoto);
    }

    if (switchCameraBtn) {
      switchCameraBtn.addEventListener('click', switchCamera);
    }

    if (chatbotCameraInput) {
      chatbotCameraInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (file) {
          handleImageSelect(file);
        }
      });
    }

    // ------------------------------------------------------------------------
    // Image Upload & Preview Handler
    // ------------------------------------------------------------------------
    if (uploadChatbotImgBtn && chatbotFileInput) {
      uploadChatbotImgBtn.addEventListener('click', function () {
        chatbotFileInput.value = '';
        chatbotFileInput.click();
      });
    }

    if (changeChatbotImgBtn && chatbotFileInput) {
      changeChatbotImgBtn.addEventListener('click', function () {
        chatbotFileInput.value = '';
        chatbotFileInput.click();
      });
    }

    if (removeChatbotImgBtn) {
      removeChatbotImgBtn.addEventListener('click', function () {
        clearImageSelection();
      });
    }

    if (chatbotFileInput) {
      chatbotFileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (file) {
          handleImageSelect(file);
        }
      });
    }

    // ------------------------------------------------------------------------
    // Clipboard Paste Handler (Ctrl+V Image Support)
    // ------------------------------------------------------------------------
    function handlePasteImage(e) {
      const clipboardData = e.clipboardData || (window.clipboardData);
      if (!clipboardData) return;

      const items = clipboardData.items;
      if (!items || items.length === 0) return;

      for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item.type && item.type.indexOf('image') !== -1) {
          const file = item.getAsFile();
          if (file) {
            e.preventDefault();
            const ext = (file.type && file.type.split('/')[1]) || 'png';
            const cleanExt = ext === 'jpeg' ? 'jpg' : ext;
            const pastedFile = new File([file], `anh_dan_${Date.now()}.${cleanExt}`, {
              type: file.type || 'image/png'
            });
            handleImageSelect(pastedFile);
            if (typeof window.showToast === 'function') {
              window.showToast('Đã dán ảnh từ clipboard thành công!', 'success');
            }
            break;
          }
        }
      }
    }

    if (chatbotInput) {
      chatbotInput.addEventListener('paste', handlePasteImage);
    }
    if (chatbotWindow) {
      chatbotWindow.addEventListener('paste', handlePasteImage);
    }

    function handleImageSelect(file) {
      if (!file) return;

      // Kiểm tra định dạng ảnh (hỗ trợ linh hoạt MIME và phần mở rộng)
      const isImageMime = file.type && file.type.toLowerCase().startsWith('image/');
      const isImageExt = /\.(jpe?g|png|webp|jfif|bmp|gif|heic|heif)$/i.test(file.name || '');

      if (!isImageMime && !isImageExt) {
        if (typeof window.showToast === 'function') {
          window.showToast('Vui lòng chọn hoặc dán tệp ảnh hợp lệ (JPG, PNG, WEBP...).', 'warning');
        }
        clearImageSelection();
        return;
      }

      // Validate file size (max 10MB)
      if (file.size > 10 * 1024 * 1024) {
        if (typeof window.showToast === 'function') {
          window.showToast('Dung lượng ảnh vượt quá 10MB. Vui lòng chọn ảnh nhẹ hơn.', 'warning');
        }
        clearImageSelection();
        return;
      }

      selectedImageFile = file;

      const reader = new FileReader();
      reader.onload = function (e) {
        selectedImageDataUrl = e.target.result;
        if (chatbotPreviewThumb) chatbotPreviewThumb.src = selectedImageDataUrl;
        if (chatbotPreviewName) chatbotPreviewName.textContent = file.name || 'Ảnh răng đã chọn';
        if (chatbotPreviewMeta) {
          const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
          const sizeText = file.size >= 1024 * 1024 ? `${sizeMb} MB` : `${Math.round(file.size / 1024)} KB`;
          chatbotPreviewMeta.textContent = `${sizeText} • Sẵn sàng phân tích`;
        }
        if (chatbotImagePreviewArea) chatbotImagePreviewArea.style.display = 'flex';
        if (uploadChatbotImgBtn) uploadChatbotImgBtn.classList.add('has-image');
        if (openChatbotCameraBtn) openChatbotCameraBtn.classList.add('has-image');
        if (chatbotInput) {
          chatbotInput.placeholder = 'Nhập câu hỏi hoặc mô tả vị trí răng (tùy chọn)...';
          chatbotInput.focus();
        }
      };
      reader.readAsDataURL(file);
    }

    function clearImageSelection() {
      selectedImageFile = null;
      selectedImageDataUrl = null;
      if (chatbotFileInput) chatbotFileInput.value = '';
      if (chatbotCameraInput) chatbotCameraInput.value = '';
      if (chatbotImagePreviewArea) chatbotImagePreviewArea.style.display = 'none';
      if (uploadChatbotImgBtn) uploadChatbotImgBtn.classList.remove('has-image');
      if (openChatbotCameraBtn) openChatbotCameraBtn.classList.remove('has-image');
      if (chatbotInput) chatbotInput.placeholder = 'Nhập câu hỏi, dán ảnh (Ctrl+V) hoặc chụp...';
    }

    // ------------------------------------------------------------------------
    // Send Message Logic (Supports Text & Vision Multimodal)
    // ------------------------------------------------------------------------
    function sendChatMessage() {
      const message = chatbotInput ? chatbotInput.value.trim() : '';

      // Stop listening if user clicks send while speaking
      stopVoiceRecognition();

      // Case A: User attached an image for Dental AI Vision analysis
      if (selectedImageFile) {
        sendImageAnalysisMessage(selectedImageFile, selectedImageDataUrl, message);
        return;
      }

      // Case B: Regular text message
      if (!message) return;

      // Append User message
      appendMessage('user', message);
      if (chatbotInput) chatbotInput.value = '';

      // Append Loading indicator with typing dots
      const loadingId = 'loading-' + Date.now();
      const loadingHtml = `
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted small">Bác sĩ AI đang trả lời</span>
          <div class="typing-dots">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
          </div>
        </div>
      `;
      appendMessage('bot', loadingHtml, loadingId);

      // Determine Chatbot API path based on location
      let chatApiPath = window.CHAT_API_PATH || 'chatbot/chat.php';
      const pathname = window.location.pathname;
      if (!window.CHAT_API_PATH) {
        if (pathname.includes('/pages/') || pathname.includes('/admin/')) {
          chatApiPath = '../chatbot/chat.php';
        } else if (pathname.endsWith('/') || pathname.endsWith('/index.php')) {
          chatApiPath = 'chatbot/chat.php';
        }
      }

      fetch(chatApiPath, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Mã phản hồi từ máy chủ: ' + response.status);
        }
        return response.json();
      })
      .then(data => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();

        if (data && data.reply) {
          appendMessage('bot', data.reply);
        } else if (data && data.error) {
          appendMessage('bot', '⚠️ ' + data.error);
        } else {
          appendMessage('bot', 'Xin lỗi, trợ lý AI hiện đang bận. Bạn vui lòng liên hệ hotline 1900 6868 để được tư vấn ngay!');
        }
      })
      .catch(err => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();

        const q = message.toLowerCase();
        let fallbackReply = '';
        if (q.includes('nhổ răng') || q.includes('răng khôn')) {
          fallbackReply = '🦷 **TƯ VẤN DỊCH VỤ NHỔ RĂNG / RĂNG KHÔN:**<br>• Chi phí niêm yết: Từ 500.000đ (răng thường) - 1.500.000đ (răng khôn).<br>• Công nghệ: Sóng siêu âm Piezotome không đau, không rạch rộng, màng PRF kích thích lành thương nhanh.<br>• Bác sĩ phụ trách: BS. CKI Đỗ Minh Đức (Chuyên gia phẫu thuật miệng).<br>🎁 Đặt lịch online được **MIỄN PHÍ 100% chụp phim 3D ConeBeam**!';
        } else if (q.includes('niềng răng') || q.includes('chỉnh nha') || q.includes('invisalign')) {
          fallbackReply = '✨ **DỊCH VỤ NIỀNG RĂNG CHỈNH NHA:**<br>• Niềng răng mắc cài / Invisalign: Từ 20.000.000đ.<br>• Trả góp 0% lãi suất chỉ từ 1 triệu/tháng.<br>• Bác sĩ: BS. CKI Trần Thị Mai (Chuyên gia Invisalign).';
        } else if (q.includes('implant') || q.includes('trồng răng')) {
          fallbackReply = '🌟 **DỊCH VỤ CẤY GHÉP IMPLANT:**<br>• Trụ Implant Thụy Sĩ/Hàn Quốc: Từ 12.000.000đ.<br>• Bảo hành trọn đời chính hãng.<br>• Bác sĩ: ThS. BS Lê Hoàng Nam.';
        } else if (q.includes('giá') || q.includes('chi phí') || q.includes('bao nhiêu')) {
          fallbackReply = '💰 **BẢNG GIÁ DỊCH VỤ NIÊM YẾT:**<br>• Khám & Chụp X-quang 3D: Miễn phí (0đ)<br>• Cạo vôi răng siêu âm: 250.000đ<br>• Trám răng Composite: 350.000đ<br>• Nhổ răng Piezotome: Từ 500.000đ<br>• Tẩy trắng Laser: 1.500.000đ<br>• Bọc sứ Cercon: Từ 2.000.000đ<br>• Trồng Implant: Từ 12.000.000đ';
        }

        if (fallbackReply) {
          appendMessage('bot', fallbackReply);
        } else {
          appendMessage('bot', '⚠️ Tín hiệu mạng đến máy chủ AI gián đoạn tạm thời. Bạn có thể gọi trực tiếp Hotline <strong>1900 6868</strong> (0900.123.456) hoặc bấm "Đặt lịch khám" để được hỗ trợ tức thì.');
        }
      });
    }

    // Handler for Dental Image Vision Screening
    function sendImageAnalysisMessage(file, dataUrl, message) {
      // 1. Build User chat bubble with preview image and optional text
      let userHtml = '';
      if (dataUrl) {
        userHtml += `<img src="${dataUrl}" class="chat-user-img-thumb" alt="Ảnh răng bệnh nhân tải lên">`;
      }
      if (message) {
        userHtml += `<div>${message}</div>`;
      } else {
        userHtml += `<div><em>📷 Đã gửi 1 ảnh răng/khoang miệng để AI phân tích</em></div>`;
      }
      appendMessage('user', userHtml);

      // Clear input & image selection
      if (chatbotInput) chatbotInput.value = '';
      clearImageSelection();

      // 2. Append Loading state with scanning text
      const loadingId = 'loading-vision-' + Date.now();
      const loadingHtml = `
        <div class="d-flex align-items-center gap-2">
          <span class="text-primary small fw-semibold">
            <i class="fas fa-microscope fa-spin me-1"></i> Bác sĩ AI đang phân tích hình ảnh...
          </span>
          <div class="typing-dots">
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
            <span class="typing-dot"></span>
          </div>
        </div>
      `;
      appendMessage('bot', loadingHtml, loadingId);

      // 3. Prepare FormData
      const formData = new FormData();
      formData.append('image', file);
      if (message) {
        formData.append('message', message);
      }

      // Determine Vision API path
      let visionApiPath = window.VISION_API_PATH || 'api/analyze-dental-image.php';
      const pathname = window.location.pathname;
      if (!window.VISION_API_PATH) {
        if (pathname.includes('/pages/') || pathname.includes('/admin/')) {
          visionApiPath = '../api/analyze-dental-image.php';
        } else if (pathname.endsWith('/') || pathname.endsWith('/index.php')) {
          visionApiPath = 'api/analyze-dental-image.php';
        }
      }

      // 4. POST to Backend Vision API
      fetch(visionApiPath, {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(errData => {
            throw new Error(errData.error || ('Mã phản hồi từ máy chủ: ' + response.status));
          });
        }
        return response.json();
      })
      .then(data => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();

        if (data && data.success && data.analysis) {
          const reportHtml = formatVisionReport(data.analysis);
          appendMessage('bot', reportHtml);
        } else if (data && data.error) {
          appendMessage('bot', '⚠️ ' + data.error);
        } else {
          appendMessage('bot', '⚠️ Không thể phân tích hình ảnh lúc này. Vui lòng thử lại hoặc chụp ảnh rõ nét hơn.');
        }
      })
      .catch(err => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();
        appendMessage('bot', '⚠️ Lỗi phân tích ảnh: ' + (err.message || 'Không thể kết nối đến máy chủ AI Vision.'));
      });
    }

    // Format Structured Vision AI Response Card
    function formatVisionReport(analysis) {
      const quality = analysis.image_quality || 'acceptable';
      const riskLevel = analysis.risk_level || 'low';

      let riskBadgeClass = 'badge-risk-low';
      let riskText = 'Thấp (Chưa thấy dấu hiệu khẩn cấp)';
      if (riskLevel === 'medium') {
        riskBadgeClass = 'badge-risk-medium';
        riskText = 'Trung bình (Cần kiểm tra)';
      } else if (riskLevel === 'high') {
        riskBadgeClass = 'badge-risk-high';
        riskText = 'Đáng lưu ý (Nên khám sớm)';
      }

      let qualityText = 'Tốt (Rõ nét, đủ ánh sáng)';
      if (quality === 'acceptable') qualityText = 'Tương đối (Có thể quan sát)';
      if (quality === 'insufficient') qualityText = 'Chưa đạt (Cần chụp lại)';

      let bookingUrl = 'pages/booking.php';
      if (window.location.pathname.includes('/pages/')) {
        bookingUrl = 'booking.php';
      }

      let cardHtml = `
        <div class="dental-vision-card">
          <div class="vision-card-header">
            <div class="vision-card-title">
              <i class="fas fa-tooth text-primary"></i> KẾT QUẢ SÀNG LỌC HÌNH ẢNH
            </div>
            <span class="vision-risk-badge ${riskBadgeClass}">Lưu ý: ${riskText}</span>
          </div>

          <div class="vision-section">
            <div class="vision-section-label"><i class="fas fa-camera"></i> Chất lượng ảnh:</div>
            <div class="small fw-semibold text-dark">${qualityText}</div>
          </div>
      `;

      // If image quality is insufficient, display guidance
      if (quality === 'insufficient') {
        cardHtml += `
          <div class="vision-section">
            <div class="alert alert-warning py-2 px-3 mb-0 small" style="border-radius: 8px;">
              <strong><i class="fas fa-exclamation-triangle me-1"></i> Ảnh chưa đủ rõ nét:</strong><br>
              ${analysis.recommendation || 'Ảnh chụp bị tối, mờ, bị che khuất hoặc không thấy rõ răng/nướu. Bạn vui lòng chụp lại ảnh cận cảnh với ánh sáng rõ để AI có thể hỗ trợ tốt nhất nhé!'}
            </div>
          </div>
        `;
      } else {
        // Visible Findings
        if (analysis.visible_findings && analysis.visible_findings.length > 0) {
          cardHtml += `
            <div class="vision-section">
              <div class="vision-section-label"><i class="fas fa-search text-primary"></i> Dấu hiệu có thể quan sát:</div>
              <ul class="vision-findings-list">
          `;
          analysis.visible_findings.forEach(f => {
            const conf = f.confidence ? `<span class="badge bg-light text-secondary border ms-1" style="font-size:0.68rem;">Độ tin cậy: ${f.confidence}</span>` : '';
            cardHtml += `
              <li class="vision-finding-item">
                <span class="vision-finding-name">• ${f.finding}</span>${conf}<br>
                <span class="vision-finding-desc">${f.description || ''}</span>
              </li>
            `;
          });
          cardHtml += `</ul></div>`;
        }

        // Possible Conditions
        if (analysis.possible_conditions && analysis.possible_conditions.length > 0) {
          cardHtml += `
            <div class="vision-section">
              <div class="vision-section-label"><i class="fas fa-stethoscope text-info"></i> Tình trạng có thể liên quan:</div>
              <ul class="vision-findings-list">
          `;
          analysis.possible_conditions.forEach(c => {
            cardHtml += `
              <li class="vision-finding-item">
                <span class="vision-finding-name">• ${c.condition}</span><br>
                <span class="vision-finding-desc">${c.reason || ''}</span>
              </li>
            `;
          });
          cardHtml += `</ul></div>`;
        }

        // Recommendation
        if (analysis.recommendation) {
          cardHtml += `
            <div class="vision-section">
              <div class="vision-section-label"><i class="fas fa-lightbulb text-warning"></i> Khuyến nghị của Bác sĩ AI:</div>
              <div class="vision-recommendation-box">${analysis.recommendation}</div>
            </div>
          `;
        }
      }

      // Emergency Alert if High Risk
      if (riskLevel === 'high') {
        cardHtml += `
          <div class="vision-emergency-alert">
            <i class="fas fa-exclamation-circle fs-4"></i>
            <div>
              <strong>Khuyến nghị khẩn cấp:</strong> Nếu bạn đang đau buốt dữ dội, sưng lan vùng mặt hoặc khó nuốt, vui lòng liên hệ cấp cứu hoặc đến cơ sở y tế gần nhất ngay lập tức!
            </div>
          </div>
        `;
      }

      // Safety Disclaimer Box
      const limitationsText = analysis.limitations ? ` ${analysis.limitations}` : '';
      cardHtml += `
          <div class="vision-disclaimer-box">
            <strong>📌 Lưu ý quan trọng:</strong> Đây là công cụ hỗ trợ sàng lọc/tham khảo ban đầu từ hình ảnh, <strong>tuyệt đối không phải chẩn đoán y khoa chính thức</strong> và không thay thế việc khám lâm sàng trực tiếp với Bác sĩ cùng phim chụp X-quang/xét nghiệm.${limitationsText}
          </div>

          <div class="vision-booking-action">
            <a href="${bookingUrl}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-bold text-white shadow-sm" style="font-size: 0.82rem; text-decoration: none;">
              <i class="far fa-calendar-check me-1"></i> Đặt lịch khám Bác sĩ (Miễn phí 0đ)
            </a>
          </div>
        </div>
      `;

      return cardHtml;
    }

    if (sendChatbotBtn) {
      sendChatbotBtn.addEventListener('click', sendChatMessage);
    }

    if (chatbotInput) {
      chatbotInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          sendChatMessage();
        }
      });
    }

    // ------------------------------------------------------------------------
    // Voice Recognition (Speech to Text) for Chatbot
    // ------------------------------------------------------------------------
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    let isListening = false;
    let autoSendTimer = null;

    if (SpeechRecognition) {
      try {
        recognition = new SpeechRecognition();
        recognition.lang = 'vi-VN';
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.maxAlternatives = 1;

        recognition.onstart = function () {
          isListening = true;
          updateVoiceUI(true, 'Đang lắng nghe... Hãy nói câu hỏi của bạn');
        };

        recognition.onresult = function (event) {
          let interimTranscript = '';
          let finalTranscript = '';

          for (let i = event.resultIndex; i < event.results.length; ++i) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
              finalTranscript += transcript;
            } else {
              interimTranscript += transcript;
            }
          }

          const currentText = finalTranscript || interimTranscript;
          if (chatbotInput && currentText) {
            chatbotInput.value = currentText;
          }
          if (voiceStatusText && currentText) {
            voiceStatusText.textContent = `🎙️ "${currentText}"`;
          }

          // If final sentence recognized, auto-send after 700ms delay
          if (finalTranscript) {
            if (voiceStatusText) {
              voiceStatusText.textContent = `✅ Đã nhận diện: "${finalTranscript}"`;
            }
            clearTimeout(autoSendTimer);
            autoSendTimer = setTimeout(() => {
              stopVoiceRecognition();
              sendChatMessage();
            }, 700);
          }
        };

        recognition.onerror = function (event) {
          console.warn('SpeechRecognition error:', event.error);
          if (event.error === 'not-allowed') {
            window.showToast('Trình duyệt chưa được cấp quyền micro. Vui lòng bật quyền Micro để tra cứu bằng giọng nói.', 'warning');
          } else if (event.error === 'no-speech') {
            window.showToast('Chưa phát hiện giọng nói. Bạn vui lòng thử lại và nói to hơn nhé.', 'info');
          } else if (event.error !== 'aborted') {
            window.showToast('Lỗi nhận diện âm thanh: ' + event.error, 'warning');
          }
          stopVoiceRecognition();
        };

        recognition.onend = function () {
          updateVoiceUI(false);
          isListening = false;
        };
      } catch (err) {
        console.error('SpeechRecognition init error:', err);
      }
    }

    function startVoiceRecognition() {
      if (!recognition) {
        window.showToast('Trình duyệt của bạn chưa hỗ trợ nhận diện giọng nói (Web Speech API). Vui lòng sử dụng Google Chrome, Edge hoặc Safari.', 'warning');
        return;
      }
      try {
        recognition.start();
      } catch (e) {
        try {
          recognition.stop();
          setTimeout(() => recognition.start(), 200);
        } catch (ex) {
          console.warn(ex);
        }
      }
    }

    function stopVoiceRecognition() {
      clearTimeout(autoSendTimer);
      if (recognition && isListening) {
        try {
          recognition.stop();
        } catch (e) {}
      }
      updateVoiceUI(false);
      isListening = false;
    }

    function updateVoiceUI(listening, statusText = '') {
      if (voiceChatbotBtn) {
        if (listening) {
          voiceChatbotBtn.classList.add('listening');
          voiceChatbotBtn.title = 'Đang lắng nghe... Nhấn lại để dừng';
        } else {
          voiceChatbotBtn.classList.remove('listening');
          voiceChatbotBtn.title = 'Tra cứu & Hỏi đáp bằng giọng nói (Voice)';
        }
      }
      if (chatbotVoiceStatus) {
        chatbotVoiceStatus.style.display = listening ? 'flex' : 'none';
      }
      if (voiceStatusText && statusText) {
        voiceStatusText.textContent = statusText;
      }
      if (chatbotInput) {
        chatbotInput.placeholder = listening ? '🎙️ Đang lắng nghe... Hãy nói câu hỏi của bạn...' : 'Nhập câu hỏi hoặc nhấn mic để nói...';
      }
    }

    if (voiceChatbotBtn) {
      voiceChatbotBtn.addEventListener('click', function () {
        if (isListening) {
          stopVoiceRecognition();
        } else {
          startVoiceRecognition();
        }
      });
    }

    if (cancelVoiceBtn) {
      cancelVoiceBtn.addEventListener('click', function () {
        stopVoiceRecognition();
      });
    }

    // Append formatted message to chat
    function appendMessage(sender, text, customId = null) {
      const msgDiv = document.createElement('div');
      msgDiv.classList.add('chat-msg', sender);
      if (customId) msgDiv.id = customId;

      if (customId && text.includes('typing-dots')) {
        msgDiv.innerHTML = text;
      } else {
        // Markdown parsing for bold, code, bullets, links & linebreaks
        let formattedText = text
          .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
          .replace(/\*(.*?)\*/g, '<em>$1</em>')
          .replace(/`([^`]+)`/g, '<code>$1</code>')
          .replace(/^[•\-*]\s+(.*)$/gm, '• $1')
          .replace(/\n/g, '<br>');

        // If bot talks about booking, suggest a direct link
        if (sender === 'bot' && (text.toLowerCase().includes('đặt lịch') || text.toLowerCase().includes('lịch hẹn') || text.toLowerCase().includes('khám răng'))) {
          let bookingUrl = 'pages/booking.php';
          if (window.location.pathname.includes('/pages/')) {
            bookingUrl = 'booking.php';
          }
          formattedText += `<div class="mt-2 pt-2 border-top border-light border-opacity-25">
            <a href="${bookingUrl}" class="btn btn-sm btn-primary-clinic text-white rounded-pill px-3 py-1" style="font-size: 0.8rem; text-decoration: none;">
              <i class="far fa-calendar-check me-1"></i> Đặt lịch ngay tại đây
            </a>
          </div>`;
        }

        // Voice Text-To-Speech (TTS) Speaker Button for Bot responses
        if (sender === 'bot' && !customId) {
          const plainText = text.replace(/<[^>]*>?/gm, '').replace(/[*`#_]/g, '').trim();
          formattedText += `<div class="mt-2 pt-1 border-top border-light border-opacity-25 d-flex justify-content-end">
            <button type="button" class="chat-tts-btn" data-tts="${encodeURIComponent(plainText)}" title="Nghe Bác sĩ AI đọc câu trả lời này">
              <i class="fas fa-volume-up me-1"></i> Nghe đọc
            </button>
          </div>`;
        }

        msgDiv.innerHTML = formattedText;
      }

      chatbotMessages.appendChild(msgDiv);
      chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Event delegation for Text-to-Speech playback
    if (chatbotMessages) {
      chatbotMessages.addEventListener('click', function (e) {
        const ttsBtn = e.target.closest('.chat-tts-btn');
        if (!ttsBtn) return;

        if (!('speechSynthesis' in window)) {
          window.showToast('Trình duyệt chưa hỗ trợ phát giọng nói.', 'info');
          return;
        }

        // If currently speaking this button, stop it
        if (ttsBtn.classList.contains('speaking')) {
          window.speechSynthesis.cancel();
          ttsBtn.classList.remove('speaking');
          ttsBtn.innerHTML = '<i class="fas fa-volume-up me-1"></i> Nghe đọc';
          return;
        }

        // Cancel any ongoing speech
        window.speechSynthesis.cancel();
        document.querySelectorAll('.chat-tts-btn').forEach(btn => {
          btn.classList.remove('speaking');
          btn.innerHTML = '<i class="fas fa-volume-up me-1"></i> Nghe đọc';
        });

        const rawText = decodeURIComponent(ttsBtn.getAttribute('data-tts') || '');
        if (!rawText) return;

        const utterance = new SpeechSynthesisUtterance(rawText);
        utterance.lang = 'vi-VN';
        utterance.rate = 0.95; // Natural human pace

        // Try to pick Vietnamese voice
        const voices = window.speechSynthesis.getVoices();
        const viVoice = voices.find(v => v.lang.includes('vi') || v.lang.includes('VI'));
        if (viVoice) utterance.voice = viVoice;

        ttsBtn.classList.add('speaking');
        ttsBtn.innerHTML = '<i class="fas fa-stop me-1"></i> Dừng đọc';

        utterance.onend = function () {
          ttsBtn.classList.remove('speaking');
          ttsBtn.innerHTML = '<i class="fas fa-volume-up me-1"></i> Nghe đọc';
        };

        utterance.onerror = function () {
          ttsBtn.classList.remove('speaking');
          ttsBtn.innerHTML = '<i class="fas fa-volume-up me-1"></i> Nghe đọc';
        };

        window.speechSynthesis.speak(utterance);
      });
    }
  }

  // ------------------------------------------------------------------------
  // 3. Smooth Scroll for Internal Hash Anchors
  // ------------------------------------------------------------------------
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId !== '#' && targetId.length > 1) {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });

  // ------------------------------------------------------------------------
  // 4. Client Form Validation & Helpers
  // ------------------------------------------------------------------------
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    // Set minimum date to today
    const dateInput = document.getElementById('appointment_date');
    if (dateInput) {
      const todayStr = new Date().toISOString().split('T')[0];
      dateInput.min = todayStr;
      if (!dateInput.value) {
        dateInput.value = todayStr;
      }
    }

    bookingForm.addEventListener('submit', function (e) {
      const phoneInput = document.getElementById('phone');
      const emailInput = document.getElementById('email');
      const nameInput = document.getElementById('full_name');

      if (nameInput && nameInput.value.trim().length < 2) {
        e.preventDefault();
        window.showToast('Vui lòng nhập họ và tên hợp lệ (tối thiểu 2 ký tự).', 'warning');
        nameInput.focus();
        return false;
      }

      if (phoneInput) {
        const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;
        if (!phoneRegex.test(phoneInput.value.trim())) {
          e.preventDefault();
          window.showToast('Số điện thoại không hợp lệ! Vui lòng nhập 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).', 'error');
          phoneInput.focus();
          return false;
        }
      }

      if (emailInput && emailInput.value.trim()) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
          e.preventDefault();
          window.showToast('Địa chỉ email không đúng định dạng. Vui lòng kiểm tra lại.', 'warning');
          emailInput.focus();
          return false;
        }
      }
    });
  }

  // ------------------------------------------------------------------------
  // 5. Image Error Fallback Handler
  // ------------------------------------------------------------------------
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function () {
      if (!this.getAttribute('data-has-fallback')) {
        this.setAttribute('data-has-fallback', 'true');
        this.src = 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=600&auto=format&fit=crop&q=80';
      }
    });
  });

});
