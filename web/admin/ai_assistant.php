<?php
// web/admin/ai_assistant.php
// Trang Trợ Lý AI Quản Trị & Điều Hành Phòng Khám Chuyên Sâu

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Tính toán các chỉ số thời gian thực
try {
    $total_patients = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $total_appointments = (int)$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    $today_appointments = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURRENT_DATE()")->fetchColumn();
    $pending_appointments = (int)$pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
    $total_doctors = (int)$pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
} catch (Exception $e) {
    $total_patients = $total_appointments = $today_appointments = $pending_appointments = $total_doctors = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trợ Lý AI Quản Trị - Premier Dental Clinic</title>
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .ai-chat-card {
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(12, 74, 110, 0.08);
            border: 1px solid #bae6fd;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 240px);
            min-height: 550px;
            overflow: hidden;
        }

        .ai-chat-header-bar {
            background: linear-gradient(135deg, #071527 0%, #0c4a6e 60%, #0284c7 100%);
            color: #ffffff;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #38bdf8;
        }

        .ai-chat-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .ai-bubble-row {
            display: flex;
            gap: 14px;
            max-width: 85%;
        }

        .ai-bubble-row.bot {
            align-self: flex-start;
        }

        .ai-bubble-row.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .ai-bubble-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .ai-bubble-row.bot .ai-bubble-avatar {
            background: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        .ai-bubble-row.user .ai-bubble-avatar {
            background: #0f172a;
            color: #ffffff;
        }

        .ai-bubble-content {
            padding: 14px 18px;
            border-radius: 16px;
            font-size: 0.92rem;
            line-height: 1.65;
        }

        .ai-bubble-row.bot .ai-bubble-content {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            border-top-left-radius: 4px;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .ai-bubble-row.user .ai-bubble-content {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            border-top-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .ai-bubble-content pre {
            background: #0f172a;
            color: #38bdf8;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin: 10px 0;
            overflow-x: auto;
        }

        .ai-quick-panel {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #bae6fd;
            padding: 20px;
            box-shadow: 0 6px 16px rgba(12, 74, 110, 0.05);
            margin-bottom: 20px;
        }

        .ai-preset-item {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0369a1;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ai-preset-item:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            transform: translateX(3px);
        }

        .ai-chat-input-container {
            padding: 16px 20px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 12px;
            align-items: center;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 px-0 admin-sidebar">
            <div class="p-3 text-center border-bottom border-secondary border-opacity-25">
                <a href="../index.php" class="text-white text-decoration-none d-flex align-items-center justify-content-center gap-2 fw-bold fs-5">
                    <div class="brand-logo-icon" style="width: 36px; height: 36px; font-size: 1.1rem;">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <span>PREMIER ADMIN</span>
                </a>
            </div>
            <div class="p-3">
                <small class="text-uppercase text-muted fw-bold d-block mb-3 px-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">Quản lý hệ thống</small>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="revenue.php"><i class="fas fa-chart-pie me-2 text-warning"></i> Doanh Thu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="appointments.php"><i class="far fa-calendar-check me-2"></i> Lịch Hẹn</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patients.php"><i class="fas fa-user-injured me-2"></i> Bệnh Nhân</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="doctors.php"><i class="fas fa-user-md me-2"></i> Bác Sĩ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php"><i class="fas fa-stethoscope me-2"></i> Dịch Vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ai_assistant.php"><i class="fas fa-robot text-info me-2"></i> Trợ Lý AI Admin</a>
                    </li>
                </ul>
                <hr class="border-secondary border-opacity-25 my-4">
                <a href="../index.php" target="_blank" class="btn btn-outline-info w-100 btn-sm mb-2 rounded-pill">
                    <i class="fas fa-external-link-alt me-1"></i> Xem Website
                </a>
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm rounded-pill">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold mb-1 text-dark-navy">
                        <i class="fas fa-robot text-primary me-2"></i> Trợ Lý AI Quản Trị & Vận Hành
                    </h3>
                    <p class="text-muted small mb-0">Hỗ trợ tra cứu dữ liệu thời gian thực, quản lý lịch hẹn, hỗ trợ soạn thảo và hướng dẫn y khoa.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-semibold">
                        <i class="fas fa-check-circle me-1"></i> Trực tuyến 24/7
                    </span>
                </div>
            </div>

            <!-- Metric Strip -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-calendar-day fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Lịch hẹn hôm nay</small>
                            <h4 class="fw-bold text-dark mb-0"><?php echo $today_appointments; ?> ca</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clock fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Chờ xác nhận</small>
                            <h4 class="fw-bold text-dark mb-0"><?php echo $pending_appointments; ?> ca</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info">
                            <i class="fas fa-user-injured fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Tổng bệnh nhân</small>
                            <h4 class="fw-bold text-dark mb-0"><?php echo $total_patients; ?> người</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-user-md fs-4"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Bác sĩ chuyên khoa</small>
                            <h4 class="fw-bold text-dark mb-0"><?php echo $total_doctors; ?> BS</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-Column Layout -->
            <div class="row g-4">
                <!-- Left: Full Chat UI -->
                <div class="col-lg-8">
                    <div class="ai-chat-card">
                        <div class="ai-chat-header-bar">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-white bg-opacity-20 p-2 text-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-robot text-info"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-0">Hội Thoại Cùng AI Executive Assistant</h6>
                                    <small class="text-light text-opacity-75" style="font-size: 0.75rem;">Kết nối trực tiếp Cơ sở dữ liệu Nha Khoa Premier</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="mainClearChatBtn">
                                <i class="fas fa-trash-alt me-1"></i> Làm mới hội thoại
                            </button>
                        </div>

                        <div class="ai-chat-body" id="mainAiChatBody">
                            <div class="ai-bubble-row bot">
                                <div class="ai-bubble-avatar"><i class="fas fa-robot"></i></div>
                                <div class="ai-bubble-content">
                                    Xin chào <strong><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Quản trị viên'); ?></strong>! Tôi là <strong>Trợ lý AI Quản Trị Điều Hành</strong> của Nha Khoa Quốc Tế Premier.<br><br>
                                    Tôi có thể hỗ trợ bạn thực hiện ngay:<br>
                                    • 📊 <strong>Báo cáo tổng hợp số liệu hôm nay:</strong> Tổng ca khám, bác sĩ trực, tình hình tiếp đón.<br>
                                    • ⏳ <strong>Lọc danh sách ca chờ duyệt:</strong> Báo cáo nhanh các lịch hẹn cần lễ tân gọi xác nhận.<br>
                                    • 🔍 <strong>Tra cứu hồ sơ bệnh nhân:</strong> Tìm nhanh qua số điện thoại hoặc họ tên bệnh nhân.<br>
                                    • 📝 <strong>Soạn thảo tin nhắn tự động:</strong> Mẫu tin nhắn Zalo/SMS nhắc lịch hẹn, cảm ơn sau khám.<br>
                                    • 💡 <strong>Quy trình y khoa & hậu phẫu:</strong> Hướng dẫn chăm sóc sau cấy ghép Implant, nhổ răng khôn Piezotome, dán sứ Veneer.<br><br>
                                    Bạn có thể chọn các lệnh nhanh ở cột bên phải hoặc nhập câu hỏi trực tiếp vào khung chat bên dưới!
                                </div>
                            </div>
                        </div>

                        <div class="ai-chat-input-container">
                            <input type="text" class="form-control rounded-pill px-4 py-2 border-primary border-opacity-50" id="mainAiChatInput" placeholder="Nhập câu hỏi hoặc yêu cầu quản trị (VD: Báo cáo hôm nay, tìm bệnh nhân, soạn tin nhắn nhắc hẹn...)..." autocomplete="off">
                            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="mainAiChatSendBtn">
                                <i class="fas fa-paper-plane me-1"></i> Gửi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Quick Presets & Tools -->
                <div class="col-lg-4">
                    <div class="ai-quick-panel">
                        <h6 class="fw-bold text-dark-navy mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-bolt text-warning"></i> Thao Tác Quản Trị Nhanh
                        </h6>
                        <div class="ai-preset-item" data-text="Thống kê tình hình phòng khám hôm nay">
                            <i class="fas fa-chart-pie text-primary"></i> 📊 Thống kê tình hình hôm nay
                        </div>
                        <div class="ai-preset-item" data-text="Danh sách lịch hẹn chờ duyệt">
                            <i class="fas fa-calendar-times text-warning"></i> ⏳ Lịch hẹn đang chờ duyệt
                        </div>
                        <div class="ai-preset-item" data-text="Danh sách bác sĩ và lịch trực">
                            <i class="fas fa-user-md text-success"></i> 👨‍⚕️ Lịch trực của các Bác sĩ
                        </div>
                        <div class="ai-preset-item" data-text="Mẫu tin nhắn nhắc hẹn bệnh nhân">
                            <i class="fas fa-sms text-info"></i> 📝 Mẫu tin nhắn SMS nhắc hẹn
                        </div>
                        <div class="ai-preset-item" data-text="Hướng dẫn chăm sóc hậu phẫu cấy Implant và nhổ răng">
                            <i class="fas fa-heartbeat text-danger"></i> 💡 Hướng dẫn chăm sóc hậu phẫu
                        </div>
                    </div>

                    <div class="ai-quick-panel">
                        <h6 class="fw-bold text-dark-navy mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-shield-alt text-primary"></i> Trạng Thái Hệ Thống AI
                        </h6>
                        <ul class="list-unstyled small mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex justify-content-between">
                                <span>Phiên bản AI:</span>
                                <strong class="text-dark">Premier AI 2.5 Executive</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Cơ chế phản hồi:</span>
                                <strong class="text-primary">CSDL Thời gian thực</strong>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Quyền hạn truy cập:</span>
                                <span class="badge bg-primary">Full Administrator</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span>Bảo mật dữ liệu:</span>
                                <strong class="text-success"><i class="fas fa-lock me-1"></i> Mã hóa SSL 256-bit</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function() {
    const input = document.getElementById('mainAiChatInput');
    const sendBtn = document.getElementById('mainAiChatSendBtn');
    const chatBody = document.getElementById('mainAiChatBody');
    const clearBtn = document.getElementById('mainClearChatBtn');
    const presetItems = document.querySelectorAll('.ai-preset-item');

    presetItems.forEach(item => {
        item.addEventListener('click', () => {
            const txt = item.getAttribute('data-text');
            if (txt) {
                sendMessage(txt);
            }
        });
    });

    clearBtn.addEventListener('click', () => {
        if (confirm('Làm mới lại toàn bộ cuộc hội thoại?')) {
            chatBody.innerHTML = `
                <div class="ai-bubble-row bot">
                    <div class="ai-bubble-avatar"><i class="fas fa-robot"></i></div>
                    <div class="ai-bubble-content">
                        Đã làm mới cuộc hội thoại. Hãy nhập câu hỏi hoặc chọn lệnh quản lý nhanh!
                    </div>
                </div>
            `;
        }
    });

    function sendMessage(customText) {
        const text = customText || input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        if (!customText) input.value = '';

        const typingId = 'typing_' + Date.now();
        const typingEl = document.createElement('div');
        typingEl.className = 'ai-bubble-row bot';
        typingEl.id = typingId;
        typingEl.innerHTML = `
            <div class="ai-bubble-avatar"><i class="fas fa-robot"></i></div>
            <div class="ai-bubble-content">
                <div class="admin-typing-dots"><span></span><span></span><span></span></div>
            </div>
        `;
        chatBody.appendChild(typingEl);
        chatBody.scrollTop = chatBody.scrollHeight;

        sendBtn.disabled = true;

        fetch('admin_chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById(typingId);
            if (el) el.remove();

            if (data && data.success && data.reply) {
                appendMessage('bot', data.reply);
            } else {
                appendMessage('bot', '⚠️ ' + (data.error || 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.'));
            }
        })
        .catch(err => {
            const el = document.getElementById(typingId);
            if (el) el.remove();
            appendMessage('bot', '⚠️ Không thể kết nối đến máy chủ: ' + err.message);
        })
        .finally(() => {
            sendBtn.disabled = false;
            input.focus();
        });
    }

    sendBtn.addEventListener('click', () => sendMessage());
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    function appendMessage(sender, text) {
        const row = document.createElement('div');
        row.className = 'ai-bubble-row ' + sender;

        const avatar = document.createElement('div');
        avatar.className = 'ai-bubble-avatar';
        avatar.innerHTML = sender === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user-shield"></i>';

        const content = document.createElement('div');
        content.className = 'ai-bubble-content';

        let formatted = escapeHtml(text);
        formatted = formatted.replace(/```([a-z]*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        formatted = formatted.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="text-primary fw-semibold">$1</a>');
        formatted = formatted.replace(/\n/g, '<br>');

        content.innerHTML = formatted;

        row.appendChild(avatar);
        row.appendChild(content);
        chatBody.appendChild(row);

        setTimeout(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 50);
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
})();
</script>

<?php include __DIR__ . '/chatbot_widget.php'; ?>
</body>
</html>
