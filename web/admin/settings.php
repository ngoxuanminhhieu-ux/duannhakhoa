<?php
// web/admin/settings.php
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
require_once __DIR__ . '/auth.php';
checkAdminAuth();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Xử lý AJAX: Kiểm tra kết nối AI tức thì
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_connection') {
    header('Content-Type: application/json; charset=utf-8');
    $apiKey = trim($_POST['api_key'] ?? '');
    $model = trim($_POST['model'] ?? 'gemini-3.6-flash');

    if (empty($apiKey)) {
        echo json_encode([
            'success' => false,
            'error' => 'Vui lòng nhập Gemini API Key để kiểm tra.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $testPrompt = "Bạn là trợ lý AI của Nha Khoa Quốc Tế Premier Dental. Hãy viết một câu chào ngắn gọn (dưới 25 từ) thể hiện sự thân thiện và sẵn sàng hỗ trợ khách hàng.";
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $genConfig = [
        'temperature'     => 0.7,
        'maxOutputTokens' => 1024
    ];
    if (strpos($model, 'lite') === false) {
        $genConfig['thinkingConfig'] = [
            'thinkingBudget' => 0
        ];
    }

    $payload = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [['text' => $testPrompt]]
            ]
        ],
        'generationConfig' => $genConfig
    ], JSON_UNESCAPED_UNICODE);

    $start = microtime(true);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $duration = round(microtime(true) - $start, 2);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    // Tự động thử lại không kèm thinkingConfig nếu gặp lỗi 400
    if ($httpCode === 400 && isset($genConfig['thinkingConfig'])) {
        unset($genConfig['thinkingConfig']);
        $retryPayload = json_encode([
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $testPrompt]]
                ]
            ],
            'generationConfig' => $genConfig
        ], JSON_UNESCAPED_UNICODE);

        $chRetry = curl_init($url);
        curl_setopt($chRetry, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chRetry, CURLOPT_POST, true);
        curl_setopt($chRetry, CURLOPT_POSTFIELDS, $retryPayload);
        curl_setopt($chRetry, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($chRetry, CURLOPT_TIMEOUT, 25);
        curl_setopt($chRetry, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($chRetry);
        $duration = round(microtime(true) - $start, 2);
        $httpCode = curl_getinfo($chRetry, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($chRetry);
        curl_close($chRetry);
    }

    if ($curlErr) {
        echo json_encode([
            'success' => false,
            'error' => 'Lỗi kết nối mạng (cURL): ' . $curlErr,
            'duration' => $duration
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resData = json_decode($response, true);
    if ($httpCode === 200 && isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
        $replyText = trim($resData['candidates'][0]['content']['parts'][0]['text']);
        echo json_encode([
            'success' => true,
            'model' => $model,
            'duration' => $duration,
            'http_code' => $httpCode,
            'reply' => $replyText
        ], JSON_UNESCAPED_UNICODE);
    } else {
        $msg = $resData['error']['message'] ?? ('Mã phản hồi HTTP: ' . $httpCode);
        echo json_encode([
            'success' => false,
            'model' => $model,
            'duration' => $duration,
            'http_code' => $httpCode,
            'error' => $msg
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

$successMsg = '';
$errorMsg   = '';

// Xử lý POST lưu cài đặt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $aiEnabled = isset($_POST['ai_enabled']) ? '1' : '0';
    $apiKey    = trim($_POST['gemini_api_key'] ?? '');
    
    $modelChoice = trim($_POST['gemini_model'] ?? 'gemini-3.6-flash');
    if ($modelChoice === 'custom' && !empty(trim($_POST['custom_gemini_model'] ?? ''))) {
        $modelChoice = trim($_POST['custom_gemini_model']);
    }
    
    $temperature = (float)($_POST['gemini_temperature'] ?? 0.7);
    $temperature = max(0.0, min(1.0, $temperature));
    $maxTokens   = (int)($_POST['gemini_max_tokens'] ?? 1024);
    $maxTokens   = max(128, min(4096, $maxTokens));
    $systemPrompt = trim($_POST['ai_system_prompt'] ?? '');

    set_setting('ai_enabled', $aiEnabled);
    set_setting('gemini_api_key', $apiKey);
    set_setting('gemini_model', $modelChoice);
    set_setting('gemini_temperature', (string)$temperature);
    set_setting('gemini_max_tokens', (string)$maxTokens);
    set_setting('ai_system_prompt', $systemPrompt);

    // Đồng bộ vào file .env nếu có thể ghi
    $envFile = dirname(__DIR__, 2) . '/.env';
    if (file_exists($envFile) && is_writable($envFile)) {
        $envContent = file_get_contents($envFile);
        if (strpos($envContent, 'GEMINI_API_KEY=') !== false) {
            $envContent = preg_replace('/GEMINI_API_KEY=.*/', 'GEMINI_API_KEY=' . $apiKey, $envContent);
        } else {
            $envContent .= "\nGEMINI_API_KEY=" . $apiKey . "\n";
        }
        @file_put_contents($envFile, $envContent);
    }

    $successMsg = 'Đã lưu cấu hình AI và thông số chatbot thành công!';
}

// Đọc cài đặt hiện tại
$currentSettings = get_all_settings();
$aiEnabledCurrent = ($currentSettings['ai_enabled'] ?? '1') === '1';
$apiKeyCurrent = $currentSettings['gemini_api_key'] ?? '';
$modelCurrent = $currentSettings['gemini_model'] ?? 'gemini-3.6-flash';
$tempCurrent = $currentSettings['gemini_temperature'] ?? '0.7';
$maxTokensCurrent = (int)($currentSettings['gemini_max_tokens'] ?? 1024);
$systemPromptCurrent = $currentSettings['ai_system_prompt'] ?? '';

$knownModels = [
    'gemini-3.6-flash'      => 'Gemini 3.6 Flash (🚀 Khuyên dùng - Cực kỳ thông minh & Phản hồi chuẩn xác)',
    'gemini-flash-latest'   => 'Gemini Flash Latest (⚡ Tự động dùng bản Flash mới nhất từ Google)',
    'gemini-3.5-flash'      => 'Gemini 3.5 Flash (✨ Cực kỳ ổn định trên mọi tài khoản)',
    'gemini-3.5-flash-lite' => 'Gemini 3.5 Flash-Lite (⚡ Phản hồi siêu tốc ~1s)',
    'custom'                => 'Tùy chỉnh model khác...'
];
$isCustomModel = !array_key_exists($modelCurrent, $knownModels) && $modelCurrent !== 'custom';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cấu Hình Trí Tuệ Nhân Tạo AI - Premier Admin</title>
    <!-- Google Fonts for Full Vietnamese Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            background: #ffffff;
            margin-bottom: 24px;
        }
        .card-custom .card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            font-weight: 600;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.92rem;
        }
        .form-text {
            color: #64748b;
            font-size: 0.83rem;
        }
        .badge-status-on {
            background: #dcfce7;
            color: #166534;
        }
        .badge-status-off {
            background: #fee2e2;
            color: #991b1b;
        }
        .test-box {
            background: #f1f5f9;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            padding: 14px;
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
                <small class="text-uppercase text-muted fw-bold d-block mb-3 px-2">Quản lý hệ thống</small>
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
                        <a class="nav-link" href="ai_assistant.php"><i class="fas fa-robot text-info me-2"></i> Trợ Lý AI Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="settings.php"><i class="fas fa-sliders-h me-2"></i> Cấu Hình AI</a>
                    </li>
                </ul>
                <hr class="border-secondary my-4">
                <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4">
            <!-- Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fas fa-sliders-h text-primary me-2"></i>Cấu Hình Trí Tuệ Nhân Tạo (Gemini AI)</h3>
                    <p class="text-muted small mb-0">Quản lý kết nối API Gemini, tùy chỉnh Model và chỉ dẫn tư vấn cho trợ lý AI phòng khám.</p>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i> Mở Website Thử Nghiệm
                </a>
            </div>

            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($successMsg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($errorMsg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="settings.php" id="aiSettingsForm">
                <input type="hidden" name="save_settings" value="1">

                <div class="row">
                    <!-- Cột trái: Cấu hình Model & API Key -->
                    <div class="col-lg-7">
                        <div class="card card-custom">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-brain text-primary me-2"></i>Thông Số Kết Nối Google Gemini</span>
                                <span class="badge <?php echo $aiEnabledCurrent ? 'badge-status-on' : 'badge-status-off'; ?> px-2 py-1">
                                    <?php echo $aiEnabledCurrent ? '<i class="fas fa-circle text-success me-1"></i> Đang Bật AI' : '<i class="fas fa-circle text-danger me-1"></i> Đang Tắt AI'; ?>
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <!-- Bật/Tắt AI -->
                                <div class="mb-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="form-check-label fw-bold text-dark mb-0" for="aiEnabledSwitch">
                                            Kích hoạt Trợ lý Chatbot AI
                                        </label>
                                        <div class="form-text mt-1">Khi bật, chatbot trên website sẽ dùng Google Gemini để trả lời thông minh. Khi tắt sẽ dùng kịch bản mẫu cố định.</div>
                                    </div>
                                    <div class="form-check form-switch fs-4">
                                        <input class="form-check-input" type="checkbox" role="switch" id="aiEnabledSwitch" name="ai_enabled" value="1" <?php echo $aiEnabledCurrent ? 'checked' : ''; ?>>
                                    </div>
                                </div>

                                <!-- Gemini API Key -->
                                <div class="mb-4">
                                    <label for="geminiApiKey" class="form-label d-flex justify-content-between">
                                        <span>Google Gemini API Key <span class="text-danger">*</span></span>
                                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-decoration-none small text-primary">
                                            <i class="fas fa-key me-1"></i> Lấy API Key miễn phí tại Google AI Studio
                                        </a>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" class="form-control" id="geminiApiKey" name="gemini_api_key" 
                                               value="<?php echo htmlspecialchars($apiKeyCurrent); ?>" 
                                               placeholder="Nhập khóa API Gemini (ví dụ: AIzaSy... hoặc AQ.Ab...)" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleApiKeyBtn" title="Hiện/Ẩn Key">
                                            <i class="fas fa-eye" id="toggleApiKeyIcon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text mt-1">
                                        <?php if (!empty($apiKeyCurrent)): ?>
                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Đã có API Key được lưu (độ dài: <?php echo strlen($apiKeyCurrent); ?> ký tự).</span>
                                        <?php else: ?>
                                            <span class="text-warning"><i class="fas fa-exclamation-circle me-1"></i>Chưa cấu hình API Key. Chatbot sẽ dùng câu trả lời kịch bản mặc định.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Chọn Model Gemini -->
                                <div class="mb-4">
                                    <label for="geminiModelSelect" class="form-label">Chọn Model AI Gemini <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-lg" id="geminiModelSelect" name="gemini_model">
                                        <?php foreach ($knownModels as $modelVal => $modelLabel): ?>
                                            <option value="<?php echo $modelVal; ?>" <?php echo ($modelCurrent === $modelVal || ($isCustomModel && $modelVal === 'custom')) ? 'selected' : ''; ?>>
                                                <?php echo $modelLabel; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text mt-1">
                                        Khuyên dùng <strong>gemini-3.6-flash</strong> để trả lời thông minh, đầy đủ và chuẩn xác nhất, hoặc <strong>gemini-3.5-flash-lite</strong> nếu muốn phản hồi thần tốc (~1s).
                                    </div>
                                </div>

                                <!-- Custom Model Input (Hiển thị nếu chọn Custom) -->
                                <div class="mb-4 <?php echo $isCustomModel ? '' : 'd-none'; ?>" id="customModelContainer">
                                    <label for="customGeminiModel" class="form-label">Tên Model Tùy Chỉnh</label>
                                    <input type="text" class="form-control" id="customGeminiModel" name="custom_gemini_model" 
                                           value="<?php echo htmlspecialchars($isCustomModel ? $modelCurrent : ''); ?>" 
                                           placeholder="Ví dụ: gemini-3.5-flash, gemini-3.8-flash...">
                                    <div class="form-text">Điền tên định danh model chính xác theo tài liệu Google Gemini.</div>
                                </div>

                                <!-- Temperature Slider -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="temperatureRange" class="form-label mb-0">Độ Sáng Tạo (Temperature)</label>
                                        <span class="badge bg-primary fs-6 px-2 py-1" id="tempValueDisplay"><?php echo htmlspecialchars($tempCurrent); ?></span>
                                    </div>
                                    <input type="range" class="form-range" min="0.0" max="1.0" step="0.1" id="temperatureRange" name="gemini_temperature" value="<?php echo htmlspecialchars($tempCurrent); ?>">
                                    <div class="d-flex justify-content-between text-muted small">
                                        <span><i class="fas fa-bullseye me-1"></i>0.0: Chính xác, nhất quán</span>
                                        <span>0.7: Chuẩn mực (khuyên dùng)</span>
                                        <span>1.0: Sáng tạo, linh hoạt <i class="fas fa-lightbulb ms-1"></i></span>
                                    </div>
                                </div>

                                <!-- Max Output Tokens -->
                                <div class="mb-3">
                                    <label for="maxTokensSelect" class="form-label">Độ Dài Phản Hồi Tối Đa (Max Output Tokens)</label>
                                    <select class="form-select" id="maxTokensSelect" name="gemini_max_tokens">
                                        <option value="512" <?php echo $maxTokensCurrent === 512 ? 'selected' : ''; ?>>512 tokens (~150 - 200 từ tiếng Việt - Ngắn gọn)</option>
                                        <option value="1024" <?php echo $maxTokensCurrent === 1024 ? 'selected' : ''; ?>>1024 tokens (~300 - 450 từ tiếng Việt - Khuyên dùng)</option>
                                        <option value="2048" <?php echo $maxTokensCurrent === 2048 ? 'selected' : ''; ?>>2048 tokens (~600 - 900 từ tiếng Việt - Chi tiết chuyên sâu)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Khung Thử Nghiệm Kết Nối AI Tức Thì -->
                        <div class="card card-custom border-primary-subtle">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-vial me-2"></i>Kiểm Tra Kết Nối AI Trực Tiếp</span>
                                <small class="text-white-50">Test thực tế không cần lưu trước</small>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-3">
                                    Bấm nút bên dưới để gửi một truy vấn kiểm tra trực tiếp tới Google Gemini với API Key và Model đang điền ở trên. Bạn sẽ thấy ngay câu trả lời từ AI và thời gian phản hồi thực tế.
                                </p>
                                <button type="button" class="btn btn-outline-primary w-100 fw-bold py-2" id="btnTestConnection">
                                    <i class="fas fa-paper-plane me-2"></i> Gửi Yêu Cầu Kiểm Tra Kết Nối
                                </button>

                                <div id="testResultBox" class="mt-3 d-none">
                                    <!-- Kết quả test hiển thị ở đây qua JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: System Prompt & Lưu cấu hình -->
                    <div class="col-lg-5">
                        <div class="card card-custom">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-comment-medical text-primary me-2"></i>Lời Nhắc Hệ Thống (System Prompt)</span>
                                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 text-muted" id="btnResetPrompt">
                                    <i class="fas fa-undo me-1"></i> Khôi phục mẫu
                                </button>
                            </div>
                            <div class="card-body p-4">
                                <label for="systemPromptText" class="form-label">Ngữ cảnh & Chỉ dẫn hành vi của AI:</label>
                                <textarea class="form-control font-monospace" id="systemPromptText" name="ai_system_prompt" rows="16" style="font-size: 0.88rem; line-height: 1.5;"><?php echo htmlspecialchars($systemPromptCurrent); ?></textarea>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle text-primary me-1"></i>
                                    Bạn có thể thay đổi số hotline, địa chỉ phòng khám, bảng giá dịch vụ mới hoặc thêm bác sĩ phụ trách tại đây. AI sẽ dùng thông tin này để trả lời chính xác cho khách hàng!
                                </div>
                            </div>
                        </div>

                        <!-- Card Submit -->
                        <div class="card card-custom">
                            <div class="card-body p-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm">
                                    <i class="fas fa-save me-2"></i> Lưu Toàn Bộ Cấu Hình AI
                                </button>
                                <p class="text-muted small mt-2 mb-0">Cấu hình sẽ áp dụng ngay lập tức cho Chatbot bên ngoài trang chủ.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle API Key Visibility
    const apiKeyInput = document.getElementById('geminiApiKey');
    const toggleApiKeyBtn = document.getElementById('toggleApiKeyBtn');
    const toggleApiKeyIcon = document.getElementById('toggleApiKeyIcon');

    toggleApiKeyBtn.addEventListener('click', function() {
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            toggleApiKeyIcon.classList.remove('fa-eye');
            toggleApiKeyIcon.classList.add('fa-eye-slash');
        } else {
            apiKeyInput.type = 'password';
            toggleApiKeyIcon.classList.remove('fa-eye-slash');
            toggleApiKeyIcon.classList.add('fa-eye');
        }
    });

    // 2. Custom Model Selector Toggle
    const modelSelect = document.getElementById('geminiModelSelect');
    const customModelContainer = document.getElementById('customModelContainer');
    const customModelInput = document.getElementById('customGeminiModel');

    modelSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customModelContainer.classList.remove('d-none');
            customModelInput.focus();
        } else {
            customModelContainer.classList.add('d-none');
        }
    });

    // 3. Temperature Range sync
    const tempRange = document.getElementById('temperatureRange');
    const tempDisplay = document.getElementById('tempValueDisplay');
    tempRange.addEventListener('input', function() {
        tempDisplay.textContent = parseFloat(this.value).toFixed(1);
    });

    // 4. Reset Default Prompt
    const defaultPrompt = `Bạn là trợ lý AI chuyên nghiệp của phòng khám nha khoa Smile tại TP. Hồ Chí Minh. Nhiệm vụ của bạn là tư vấn thân thiện, chuyên nghiệp về các dịch vụ nha khoa, bảng giá, đội ngũ bác sĩ và chính sách phòng khám. Thông tin phòng khám: Địa chỉ 123 Đường Nguyễn Trãi, Phường 2, Quận 5, TP.HCM. Hotline: 1900 6868. Giờ làm việc: Thứ 2 - Thứ 7: 08:00-20:00, Chủ Nhật: 08:30-17:30. Dịch vụ: Niềng răng (25-60 triệu), Implant (13-24 triệu/trụ), Bọc răng sứ (3.5-6.5 triệu/răng), Tẩy trắng (2 triệu), Trám răng (300-600k), Cạo vôi (250k), Nhổ răng khôn (1.5-3 triệu). QUAN TRỌNG: Không chẩn đoán bệnh, không đưa cam kết 100%. Nếu triệu chứng nghiêm trọng (đau dữ dội, sưng mặt, sốt cao, chảy máu kéo dài) hãy khuyên đến khám ngay. Trả lời bằng tiếng Việt, ngắn gọn, dùng emoji phù hợp, định dạng markdown rõ ràng.`;

    document.getElementById('btnResetPrompt').addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn khôi phục lại Lời nhắc hệ thống mặc định của phòng khám?')) {
            document.getElementById('systemPromptText').value = defaultPrompt;
        }
    });

    // 5. Test AI Connection button
    const btnTest = document.getElementById('btnTestConnection');
    const resultBox = document.getElementById('testResultBox');

    btnTest.addEventListener('click', async function() {
        const apiKey = apiKeyInput.value.trim();
        let selectedModel = modelSelect.value;
        if (selectedModel === 'custom') {
            selectedModel = customModelInput.value.trim();
        }

        if (!apiKey) {
            alert('Vui lòng nhập API Key trước khi thử nghiệm kết nối.');
            apiKeyInput.focus();
            return;
        }

        if (!selectedModel) {
            alert('Vui lòng chọn hoặc nhập tên Model.');
            return;
        }

        btnTest.disabled = true;
        btnTest.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang gửi truy vấn tới Gemini...';
        resultBox.classList.remove('d-none');
        resultBox.innerHTML = '<div class="text-muted small"><i class="fas fa-spinner fa-spin me-1"></i> Đang kết nối tới Google Generative Language API... Vui lòng đợi trong giây lát.</div>';

        try {
            const formData = new FormData();
            formData.append('action', 'test_connection');
            formData.append('api_key', apiKey);
            formData.append('model', selectedModel);

            const res = await fetch('settings.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                resultBox.innerHTML = `
                    <div class="alert alert-success mb-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-success"><i class="fas fa-check-circle me-1"></i> Kết Nối Thành Công!</strong>
                            <span class="badge bg-success">HTTP ${data.http_code} (${data.duration}s)</span>
                        </div>
                        <div class="small mb-1 text-muted"><strong>Model:</strong> <code>${data.model}</code></div>
                        <div class="p-2 bg-white rounded border mt-2 small">
                            <strong>Phản hồi từ Gemini:</strong><br>
                            ${data.reply.replace(/\\n/g, '<br>')}
                        </div>
                    </div>
                `;
            } else {
                resultBox.innerHTML = `
                    <div class="alert alert-danger mb-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-danger"><i class="fas fa-times-circle me-1"></i> Kết Nối Thất Bại</strong>
                            <span class="badge bg-danger">HTTP ${data.http_code || 'Err'} (${data.duration || 0}s)</span>
                        </div>
                        <div class="small mb-1 text-muted"><strong>Model:</strong> <code>${data.model || selectedModel}</code></div>
                        <div class="p-2 bg-white rounded border mt-2 small text-danger">
                            <strong>Chi tiết lỗi từ Google API:</strong><br>
                            ${data.error || 'Lỗi không xác định.'}
                        </div>
                    </div>
                `;
            }
        } catch (err) {
            resultBox.innerHTML = `
                <div class="alert alert-danger mb-0 p-3">
                    <strong class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Lỗi thực thi yêu cầu</strong>
                    <div class="small mt-1">${err.message}</div>
                </div>
            `;
        } finally {
            btnTest.disabled = false;
            btnTest.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Gửi Yêu Cầu Kiểm Tra Kết Nối';
        }
    });
});
</script>
<?php include __DIR__ . '/chatbot_widget.php'; ?>
</body>
</html>
