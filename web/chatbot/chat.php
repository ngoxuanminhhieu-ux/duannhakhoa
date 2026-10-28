<?php
// web/chatbot/chat.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../data/services_detail_data.php';

// 1. Đọc dữ liệu đầu vào
$inputRaw = file_get_contents('php://input');
$data     = json_decode($inputRaw, true);

if (!isset($data['message']) || empty(trim($data['message']))) {
    echo json_encode([
        'success' => false,
        'error'   => 'Tin nhắn không được để trống.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userMessage = trim($data['message']);
$sessionId   = session_id() ?: 'default_session';
session_write_close();

// 2. KẾT NỐI CSDL & TRÍCH XUẤT THÔNG TIN THỰC TẾ
$pdo = getDB();

$clinicInfo = [
    'name'            => 'Nha Khoa Quốc Tế Premier Dental Clinic',
    'address'         => '123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh',
    'hotline'         => '1900 6868 (Miễn phí cước gọi)',
    'emergency_phone' => '0900.123.456 (Trực cấp cứu răng hàm mặt 24/7)',
    'email'           => 'contact@premierdental.vn',
    'working_hours'   => "• Thứ 2 - Thứ 7: 08:00 - 20:00 (làm việc thông trưa)\n• Chủ Nhật: 08:00 - 17:30",
    'policies'        => [
        'Miễn phí 100% chi phí thăm khám lâm sàng & chụp phim 3D CT ConeBeam / Panorama toàn cảnh cho khách hàng đặt lịch online.',
        'Chương trình Niềng răng & Cấy ghép Implant Trả góp 0% lãi suất: Chỉ cần trả trước 30%, số tiền còn lại trả trong 6 - 12 tháng qua thẻ tín dụng hơn 25 ngân hàng liên kết.',
        'Bảo hành Trụ Implant trọn đời chính hãng (Straumann Thụy Sĩ, Osstem Hàn Quốc) kèm thẻ quét mã QR Code.',
        'Bảo hành Bọc răng sứ thẩm mỹ từ 10 - 15 năm (Cercon HT, Emax, Lava Plus).',
        'Bảo hành Trám răng thẩm mỹ Composite 2 năm.',
        'Cam kết hiệu quả chỉnh nha niềng răng bằng hợp đồng pháp lý minh bạch trước khi điều trị.'
    ]
];

// Lấy danh sách dịch vụ thực tế từ MySQL
$dbServices = [];
if ($pdo) {
    try {
        $stmtSvc = $pdo->query("SELECT id, name, description, price FROM services ORDER BY id ASC");
        if ($stmtSvc) {
            $dbServices = $stmtSvc->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (\Throwable $e) {}
}

// Lấy danh sách bác sĩ thực tế từ MySQL
$dbDoctors = [];
if ($pdo) {
    try {
        $stmtDoc = $pdo->query("SELECT id, full_name, specialty, experience, description FROM doctors ORDER BY id ASC");
        if ($stmtDoc) {
            $dbDoctors = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (\Throwable $e) {}
}

// 3. TẠO SYSTEM PROMPT CHUẨN XÁC DỰA TRÊN CƠ SỞ DỮ LIỆU
$servicesSummary = "";
if (!empty($dbServices)) {
    foreach ($dbServices as $s) {
        $priceFmt = number_format($s['price'], 0, ',', '.') . 'đ';
        $servicesSummary .= "• " . $s['name'] . ": Giá niêm yết từ " . $priceFmt . " - " . $s['description'] . "\n";
    }
} else {
    $servicesSummary = "• Khám tổng quát & Chụp X-quang: Miễn phí\n• Cạo vôi răng: 250.000đ\n• Trám răng Composite: 350.000đ\n• Nhổ răng Piezotome: 500.000đ\n• Điều trị tủy: 800.000đ\n• Tẩy trắng răng Laser: 1.500.000đ\n• Bọc răng sứ: 2.000.000đ\n• Dán sứ Veneer: 4.000.000đ\n• Niềng răng: 20.000.000đ\n• Cấy ghép Implant: 12.000.000đ\n";
}

$doctorsSummary = "";
if (!empty($dbDoctors)) {
    foreach ($dbDoctors as $d) {
        $doctorsSummary .= "• " . $d['full_name'] . " - Chuyên khoa: " . $d['specialty'] . " (" . $d['experience'] . ")\n";
    }
} else {
    $doctorsSummary = "• ThS. BS. Nguyễn Văn An - Răng Hàm Mặt Tổng Quát (12 năm kinh nghiệm)\n• BS. CKI. Trần Thị Mai - Chuyên gia Chỉnh nha & Niềng răng Invisalign (8 năm kinh nghiệm)\n• ThS. BS. Lê Hoàng Nam - Chuyên gia Cấy ghép Implant Nha khoa (15 năm kinh nghiệm)\n• BS. Phạm Thùy Linh - Nha khoa Thẩm mỹ & Dán sứ Veneer (7 năm kinh nghiệm)\n• BS. CKI. Đỗ Minh Đức - Phẫu thuật Tiểu phẫu & Nhổ răng khôn Piezotome (10 năm kinh nghiệm)\n";
}

$systemPrompt = "Bạn là Trợ lý AI Tư vấn Chuyên nghiệp của " . $clinicInfo['name'] . ".

THÔNG TIN CHÍNH THỨC CỦA PHÒNG KHÁM TỪ CƠ SỞ DỮ LIỆU:
- Địa chỉ phòng khám: " . $clinicInfo['address'] . "
- Tổng đài CSKH & Đặt lịch: " . $clinicInfo['hotline'] . "
- Hotline Cấp cứu 24/7: " . $clinicInfo['emergency_phone'] . "
- Email: " . $clinicInfo['email'] . "
- Thời gian làm việc:
" . $clinicInfo['working_hours'] . "

CHÍNH SÁCH PHÒNG KHÁM:
- " . implode("\n- ", $clinicInfo['policies']) . "

BẢNG GIÁ DỊCH VỤ NIÊM YẾT TỪ CƠ SỞ DỮ LIỆU:
" . $servicesSummary . "

ĐỘI NGŨ BÁC SĨ CHUYÊN KHOA TỪ CƠ SỞ DỮ LIỆU:
" . $doctorsSummary . "

QUY TẮC BẮT BUỘC KHI TRẢ LỜI:
1. ĐỊA CHỈ PHÒNG KHÁM: Khi khách hỏi về địa chỉ, nơi ở, vị trí, ở đâu, đường nào, chỉ đường: Cung cấp CHÍNH XÁC: '" . $clinicInfo['address'] . "'. Kèm thời gian làm việc và hotline '" . $clinicInfo['hotline'] . "'. TUYỆT ĐỐI KHÔNG dùng bất kỳ placeholder nào như [Số nhà, Tên đường]!
2. GIỜ LÀM VIỆC & HOTLINE: Cung cấp đầy đủ lịch làm việc Thứ 2 - Thứ 7 (08:00 - 20:00) và Chủ Nhật (08:00 - 17:30).
3. BÁC SĨ: Giới thiệu đúng bác sĩ phụ trách chuyên khoa từ CSDL phòng khám.
4. BẢNG GIÁ & DỊCH VỤ: Trả lời đúng giá niêm yết trong CSDL, tư vấn chi tiết ưu điểm công nghệ.
5. CHÍNH SÁCH: Nhấn mạnh ưu đãi MIỄN PHÍ 100% khám và chụp X-quang khi đặt hẹn online, hỗ trợ TRẢ GÓP 0% và bảo hành trọn đời Implant.
6. THÁI ĐỘ: Lịch sự, ân cần, chuyên nghiệp như bác sĩ nha khoa. Trả lời bằng tiếng Việt chuẩn có định dạng Markdown rõ ràng.";

// 4. CẤU HÌNH GEMINI API
$aiEnabled      = (get_setting('ai_enabled', '1') === '1');
$geminiApiKey   = get_setting('gemini_api_key') ?: (getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ''));
$geminiModel    = get_setting('gemini_model', 'gemini-3.8-flash');
$geminiTemp     = (float)get_setting('gemini_temperature', '0.7');
$geminiTokens   = (int)get_setting('gemini_max_tokens', '2048');

if (in_array($geminiModel, ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.5-flash-lite'])) {
    $geminiModel = 'gemini-3.8-flash';
}

$aiResponse   = '';
$isApiSuccess = false;

function requestGeminiAI($model, $apiKey, $systemPrompt, $userMessage, $temp, $tokens) {
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $genConfig = [
        'temperature'     => $temp,
        'maxOutputTokens' => max($tokens, 2048),
        'topP'            => 0.95
    ];

    if (strpos($model, 'lite') === false) {
        $genConfig['thinkingConfig'] = [
            'thinkingBudget' => 0
        ];
    }

    $payload = json_encode([
        'system_instruction' => [
            'parts' => [['text' => $systemPrompt]]
        ],
        'contents' => [
            [
                'role'  => 'user',
                'parts' => [['text' => $userMessage]]
            ]
        ],
        'generationConfig' => $genConfig,
        'safetySettings' => [
            ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH']
        ]
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $curlErr  = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$curlErr && $response && $httpCode === 200) {
        $resData = json_decode($response, true);
        $text    = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if (!empty($text)) {
            return trim($text);
        }
    }

    // Thử lại không gửi thinkingConfig nếu bị mã 400
    if ($httpCode === 400 && isset($genConfig['thinkingConfig'])) {
        unset($genConfig['thinkingConfig']);
        $retryPayload = json_encode([
            'system_instruction' => [
                'parts' => [['text' => $systemPrompt]]
            ],
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $userMessage]]
                ]
            ],
            'generationConfig' => $genConfig
        ], JSON_UNESCAPED_UNICODE);

        $chRetry = curl_init($apiUrl);
        curl_setopt($chRetry, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chRetry, CURLOPT_POST, true);
        curl_setopt($chRetry, CURLOPT_POSTFIELDS, $retryPayload);
        curl_setopt($chRetry, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($chRetry, CURLOPT_TIMEOUT, 25);
        curl_setopt($chRetry, CURLOPT_SSL_VERIFYPEER, true);

        $responseRetry = curl_exec($chRetry);
        $httpCodeRetry = curl_getinfo($chRetry, CURLINFO_HTTP_CODE);
        curl_close($chRetry);

        if ($responseRetry && $httpCodeRetry === 200) {
            $resDataRetry = json_decode($responseRetry, true);
            $textRetry = $resDataRetry['candidates'][0]['content']['parts'][0]['text'] ?? '';
            if (!empty($textRetry)) {
                return trim($textRetry);
            }
        }
    }

    return null;
}

// 5. GỌI GEMINI API
if ($aiEnabled && !empty($geminiApiKey) && strlen($geminiApiKey) > 10) {
    $reply = requestGeminiAI($geminiModel, $geminiApiKey, $systemPrompt, $userMessage, $geminiTemp, $geminiTokens);
    if (!empty($reply)) {
        $aiResponse   = $reply;
        $isApiSuccess = true;
    } else {
        // Fallback sang các model khả dụng khác
        $fallbackModels = ['gemini-3.8-flash', 'gemini-3.6-flash', 'gemini-flash-latest'];
        foreach ($fallbackModels as $fbModel) {
            if ($fbModel !== $geminiModel) {
                $replyFallback = requestGeminiAI($fbModel, $geminiApiKey, $systemPrompt, $userMessage, $geminiTemp, $geminiTokens);
                if (!empty($replyFallback)) {
                    $aiResponse   = $replyFallback;
                    $isApiSuccess = true;
                    break;
                }
            }
        }
    }
}

// 6. CƠ CHẾ FALLBACK THÔNG MINH TRUY XUẤT TRỰC TIẾP CƠ SỞ DỮ LIỆU
if (!$isApiSuccess || empty($aiResponse)) {
    $msgLower = mb_strtolower($userMessage, 'UTF-8');

    // 6.1. HỎI ĐỊA CHỈ PHÒNG KHÁM / VỊ TRÍ / Ở ĐÂU
    if (preg_match('/\b(địa chỉ|ở đâu|chỗ nào|nơi nào|vị trí|chi nhánh|tới đâu|đường nào|sơ đồ|chỉ đường)\b/u', $msgLower)) {
        $aiResponse = "Chào bạn! **" . $clinicInfo['name'] . "** rất hân hạnh được đón tiếp bạn tại phòng khám:\n\n"
            . "📍 **Địa chỉ phòng khám:** " . $clinicInfo['address'] . "\n"
            . "⏰ **Thời gian làm việc:**\n"
            . $clinicInfo['working_hours'] . "\n\n"
            . "📞 **Tổng đài đặt lịch & CSKH:** " . $clinicInfo['hotline'] . "\n"
            . "🚨 **Hotline Cấp cứu 24/7:** " . $clinicInfo['emergency_phone'] . "\n\n"
            . "🎁 **Ưu đãi đặc biệt:** Khi đặt lịch hẹn trước qua website hoặc AI Chatbot, bạn sẽ được **MIỄN PHÍ 100% chi phí khám lâm sàng và chụp phim X-quang 3D toàn cảnh**.\n"
            . "👉 Bạn có muốn đặt lịch hẹn khám cùng Bác sĩ chuyên khoa ngay hôm nay không ạ?";
    }
    // 6.2. HỎI GIỜ LÀM VIỆC / THỜI GIAN MỞ CỬA
    elseif (preg_match('/\b(mấy giờ|giờ làm|mở cửa|đóng cửa|lịch làm|thời gian làm|chủ nhật|thứ 7)\b/u', $msgLower)) {
        $aiResponse = "Chào bạn! Thời gian làm việc chính thức tại **" . $clinicInfo['name'] . "** như sau:\n\n"
            . "⏰ **Khung giờ phục vụ:**\n"
            . $clinicInfo['working_hours'] . "\n\n"
            . "📍 **Địa chỉ:** " . $clinicInfo['address'] . "\n"
            . "📞 **Tổng đài CSKH:** " . $clinicInfo['hotline'] . "\n\n"
            . "Phòng khám làm việc thông trưa tất cả các ngày trong tuần để thuận tiện cho quý khách đến thăm khám!";
    }
    // 6.3. HỎI ĐỘI NGŨ BÁC SĨ CHUYÊN GIA
    elseif (preg_match('/\b(bác sĩ|chuyên gia|ai khám|bác sỹ|đội ngũ)\b/u', $msgLower)) {
        $aiResponse = "Chào bạn! **" . $clinicInfo['name'] . "** quy tụ đội ngũ Bác sĩ Chuyên khoa Răng Hàm Mặt giàu kinh nghiệm:\n\n";
        if (!empty($dbDoctors)) {
            foreach ($dbDoctors as $doc) {
                $aiResponse .= "👨‍⚕️ **" . $doc['full_name'] . "**\n"
                    . "• **Chuyên khoa:** " . $doc['specialty'] . "\n"
                    . "• **Kinh nghiệm:** " . $doc['experience'] . "\n"
                    . "• **Mô tả:** " . $doc['description'] . "\n\n";
            }
        } else {
            $aiResponse .= "• **ThS. BS. Nguyễn Văn An**: Chuyên gia Răng Hàm Mặt Tổng Quát (12 năm kinh nghiệm)\n"
                . "• **BS. CKI. Trần Thị Mai**: Chuyên gia Chỉnh nha & Niềng răng Invisalign (8 năm kinh nghiệm)\n"
                . "• **ThS. BS. Lê Hoàng Nam**: Chuyên gia Cấy ghép Implant Nha khoa Thụy Sĩ (15 năm kinh nghiệm)\n"
                . "• **BS. Phạm Thùy Linh**: Thiết kế nụ cười Smile Design & Dán sứ Veneer (7 năm kinh nghiệm)\n"
                . "• **BS. CKI. Đỗ Minh Đức**: Phẫu thuật Nhổ răng khôn sóng siêu âm Piezotome (10 năm kinh nghiệm)\n\n";
        }
        $aiResponse .= "👉 Bạn muốn đặt lịch hẹn khám trực tiếp với Bác sĩ nào, hãy nhắn lại để em hỗ trợ nhé!";
    }
    // 6.4. HỎI CHÍNH SÁCH BẢO HÀNH & TRẢ GÓP 0%
    elseif (preg_match('/\b(bảo hành|trả góp|thanh toán|bảo hiểm|thẻ tín dụng|0%)\b/u', $msgLower)) {
        $aiResponse = "Chào bạn! Về **Chính sách & Quyền lợi Khách hàng** tại Premier Dental Clinic:\n\n"
            . "💳 **1. Chương trình Trả góp 0% Lãi suất:**\n"
            . "• Áp dụng cho dịch vụ Niềng răng & Cấy ghép Implant.\n"
            . "• Chỉ cần thanh toán trước 30%, phần còn lại chia đều trong 6 - 12 tháng qua thẻ tín dụng hơn 25 ngân hàng đối tác.\n\n"
            . "🛡️ **2. Chính sách Bảo hành Chính hãng:**\n"
            . "• **Cấy ghép Implant:** Bảo hành TRỌN ĐỜI trụ Implant Thụy Sĩ/Hàn Quốc (thẻ quét mã QR Code điện tử).\n"
            . "• **Bọc răng sứ thẩm mỹ:** Bảo hành từ 10 - 15 NĂM (Cercon HT, Emax, Lava Plus).\n"
            . "• **Trám răng Composite:** Bảo hành 2 năm.\n\n"
            . "🎁 **Khám & Chụp phim X-quang 3D:** Miễn phí 100% khi đặt hẹn trước qua hotline " . $clinicInfo['hotline'] . "!";
    }
    // 6.5. HỎI TỪNG DỊCH VỤ CỤ THỂ
    elseif (strpos($msgLower, 'niềng răng') !== false || strpos($msgLower, 'chỉnh nha') !== false || strpos($msgLower, 'invisalign') !== false || strpos($msgLower, 'mắc cài') !== false) {
        $aiResponse = formatServiceAiDetail(9);
    } elseif (strpos($msgLower, 'implant') !== false || strpos($msgLower, 'trồng răng') !== false || strpos($msgLower, 'cấy ghép') !== false) {
        $aiResponse = formatServiceAiDetail(10);
    } elseif (strpos($msgLower, 'bọc sứ') !== false || strpos($msgLower, 'răng sứ') !== false) {
        $aiResponse = formatServiceAiDetail(7);
    } elseif (strpos($msgLower, 'veneer') !== false || strpos($msgLower, 'dán sứ') !== false) {
        $aiResponse = formatServiceAiDetail(8);
    } elseif (strpos($msgLower, 'tẩy trắng') !== false || strpos($msgLower, 'trắng răng') !== false) {
        $aiResponse = formatServiceAiDetail(6);
    } elseif (strpos($msgLower, 'trám răng') !== false || strpos($msgLower, 'hàn răng') !== false) {
        $aiResponse = formatServiceAiDetail(3);
    } elseif (strpos($msgLower, 'nhổ răng') !== false || strpos($msgLower, 'răng khôn') !== false) {
        $aiResponse = formatServiceAiDetail(4);
    } elseif (strpos($msgLower, 'tủy') !== false || strpos($msgLower, 'chữa tủy') !== false) {
        $aiResponse = formatServiceAiDetail(5);
    } elseif (strpos($msgLower, 'cạo vôi') !== false || strpos($msgLower, 'cao răng') !== false) {
        $aiResponse = formatServiceAiDetail(2);
    } elseif (strpos($msgLower, 'khám') !== false || strpos($msgLower, 'tư vấn') !== false) {
        $aiResponse = formatServiceAiDetail(1);
    }
    // 6.6. HỎI CHUNG VỀ DANH MỤC / BẢNG GIÁ DỊCH VỤ TỪ CSDL
    elseif (preg_match('/\b(danh mục|bảng giá|giá|chi phí|dịch vụ|bao nhiêu)\b/u', $msgLower)) {
        $aiResponse = "Xin chào bạn! Dưới đây là **Bảng giá dịch vụ niêm yết chính thức** từ CSDL của " . $clinicInfo['name'] . ":\n\n";
        if (!empty($dbServices)) {
            foreach ($dbServices as $s) {
                $priceFmt = number_format($s['price'], 0, ',', '.') . ' VNĐ';
                $aiResponse .= "• **" . $s['name'] . ":** " . $priceFmt . " *(Bác sĩ chuyên khoa trực tiếp khám & điều trị)*\n";
            }
        } else {
            $aiResponse .= "• Khám & Tư vấn Răng tổng quát: Miễn phí (0đ)\n"
                . "• Cạo vôi răng & Đánh bóng siêu âm: 250.000 VNĐ\n"
                . "• Trám răng thẩm mỹ Composite: 350.000 VNĐ / răng\n"
                . "• Nhổ răng thường / Răng khôn sóng Piezotome: 500.000 - 2.500.000 VNĐ\n"
                . "• Điều trị tủy răng công nghệ nội nha: 800.000 VNĐ\n"
                . "• Tẩy trắng răng Laser Whitening: 1.500.000 VNĐ\n"
                . "• Bọc răng sứ thẩm mỹ Cercon/Zirconia: 2.000.000 VNĐ / răng\n"
                . "• Dán sứ Veneer Emax bảo tồn men răng: 4.000.000 VNĐ / răng\n"
                . "• Niềng răng Chỉnh nha (Mắc cài / Invisalign): Từ 20.000.000 VNĐ\n"
                . "• Cấy ghép Implant Nha khoa Thụy Sĩ: Từ 12.000.000 VNĐ / trụ\n";
        }
        $aiResponse .= "\n📍 **Địa chỉ phòng khám:** " . $clinicInfo['address'] . "\n"
            . "📞 **Hotline hỗ trợ:** " . $clinicInfo['hotline'] . "\n"
            . "👉 Bạn đang quan tâm dịch vụ nào, hãy nhắn lại để em tư vấn chi tiết quy trình nhé!";
    }
    // 6.7. MẶC ĐỊNH
    else {
        $aiResponse = "Xin chào bạn! Tôi là **Trợ lý AI của " . $clinicInfo['name'] . "**.\n\n"
            . "Tôi có thể hỗ trợ bạn:\n"
            . "• 📍 **Địa chỉ & Giờ làm việc:** " . $clinicInfo['address'] . " (08:00 - 20:00)\n"
            . "• 🦷 **Tư vấn dịch vụ:** Trám răng, Nhổ răng khôn, Lấy tủy, Niềng răng, Trồng răng Implant, Bọc sứ...\n"
            . "• 💰 **Bảng giá niêm yết CSDL:** Xem chi phí từng dịch vụ\n"
            . "• 👨‍⚕️ **Đội ngũ Bác sĩ:** Thông tin bác sĩ chuyên khoa phụ trách\n"
            . "• 💳 **Chính sách:** Hỗ trợ trả góp 0%, bảo hành trọn đời Implant\n\n"
            . "Bạn cần tư vấn dịch vụ nào hoặc đang gặp triệu chứng gì ở răng miệng ạ?";
    }
}

// 7. LƯU LỊCH SỬ TIN NHẮN VÀO CSDL MYSQL
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, user_message, ai_response) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $userMessage, $aiResponse]);
    } catch (\Throwable $e) {}
}

// 8. TRẢ VỀ PHẢN HỒI JSON CHUẨN UTF-8
echo json_encode([
    'success' => true,
    'reply'   => $aiResponse
], JSON_UNESCAPED_UNICODE);
