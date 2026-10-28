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
$inputRaw = preg_replace('/^\xEF\xBB\xBF/', '', trim((string)$inputRaw));
$data     = !empty($inputRaw) ? json_decode($inputRaw, true) : null;

$userMessage = '';
if (is_array($data) && !empty($data['message'])) {
    $userMessage = trim($data['message']);
} elseif (!empty($_POST['message'])) {
    $userMessage = trim($_POST['message']);
}

if (empty($userMessage)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Tin nhắn không được để trống.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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
$geminiModel    = get_setting('gemini_model', 'gemini-3.6-flash');
$geminiTemp     = (float)get_setting('gemini_temperature', '0.7');
$geminiTokens   = (int)get_setting('gemini_max_tokens', '2048');

if (in_array($geminiModel, ['gemini-3.8-flash', 'gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.5-flash-lite'])) {
    $geminiModel = 'gemini-3.6-flash';
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
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
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
        curl_setopt($chRetry, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($chRetry, CURLOPT_TIMEOUT, 5);
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

// 5. GỌI GEMINI API HOẶC PYTHON FASTAPI BACKEND
$pythonApiUrl = (getenv('PYTHON_API_URL') ?: 'http://host.docker.internal:8000') . '/api/chat';

function requestPythonAiBackend($url, $userMessage) {
    $payload = json_encode(['message' => $userMessage], JSON_UNESCAPED_UNICODE);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8']);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $httpCode === 200) {
        $resData = json_decode($response, true);
        $reply = $resData['reply'] ?? '';
        if (!empty($reply)) {
            return trim($reply);
        }
    }
    return null;
}

// 5.1 Thử gọi Python FastAPI Backend (có RAG ChromaDB & Pipeline chuyên sâu)
if (empty($aiResponse)) {
    try {
        $pyReply = requestPythonAiBackend($pythonApiUrl, $userMessage);
        // Chỉ dùng kết quả từ Python nếu nó không phải là danh sách dịch vụ dump mặc định (10 dịch vụ)
        if (!empty($pyReply)) {
            $isDumpList = (strpos($pyReply, 'DANH SÁCH DỊCH VỤ NHA KHOA PREMIER (10 dịch vụ') !== false);
            if (!$isDumpList) {
                $aiResponse = $pyReply;
                $isApiSuccess = true;
            }
        }
    } catch (\Throwable $e) {}
}

// 5.2 Thử gọi Google Gemini API nếu có API key hợp lệ
if (!$isApiSuccess && $aiEnabled && !empty($geminiApiKey) && strlen($geminiApiKey) > 10) {
    try {
        $reply = requestGeminiAI($geminiModel, $geminiApiKey, $systemPrompt, $userMessage, $geminiTemp, $geminiTokens);
        if (!empty($reply)) {
            $aiResponse   = $reply;
            $isApiSuccess = true;
        }
    } catch (\Throwable $e) {
        $isApiSuccess = false;
    }
}

// Đảm bảo hàm formatServiceAiDetail luôn tồn tại
if (!function_exists('formatServiceAiDetail')) {
    function formatServiceAiDetail($id) {
        $svc = function_exists('get_service_detail_by_id') ? get_service_detail_by_id($id) : null;
        if ($svc) {
            return "🦷 **" . $svc['name'] . "**\n• **Giá niêm yết:** Từ " . $svc['price_formatted'] . "\n• **Quy trình:** " . $svc['tagline'] . "\n• **Bảo hành:** " . $svc['warranty'] . "\n👉 Vui lòng liên hệ hotline 1900 6868 hoặc đặt lịch khám miễn phí!";
        }
        return "Dịch vụ nha khoa chuyên sâu chất lượng cao tại Nha Khoa Quốc Tế Premier. Đặt lịch online được miễn phí 100% khám và chụp X-quang 3D!";
    }
}

// Hàm chuyển đổi tiếng Việt có dấu sang không dấu
if (!function_exists('vn_to_plain')) {
    function vn_to_plain($str) {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ'
        ];
        foreach ($unicode as $non => $uni) {
            $str = preg_replace("/($uni)/iu", $non, $str);
        }
        return $str;
    }
}

// 6. CƠ CHẾ HYBRID KNOWLEDGE & CHUYÊN GIA NHA KHOA TOÀN NĂNG (TUYỆT ĐỐI KHÔNG TỪ CHỐI)
if (!$isApiSuccess || empty($aiResponse)) {
    $msgLower = mb_strtolower($userMessage, 'UTF-8');
    $msgPlain = vn_to_plain($msgLower);
    $msgSearch = $msgLower . ' ' . $msgPlain;

    // 6.1. TRƯỜNG ĐẠI HỌC CNTT & TRUYỀN THÔNG (ICTU) / ĐẠI HỌC THÁI NGUYÊN / ĐỀ ÁN
    if (preg_match('/\b(ictu|trường nào|truong nao|đại học thái nguyên|dai hoc thai nguyen|cntt|cntt & tt|thái nguyên|thai nguyen)\b/u', $msgSearch)) {
        $aiResponse = "🎓 **ICTU là Trường Đại học Công nghệ Thông tin & Truyền thông** (thuộc Đại học Thái Nguyên).\n\n"
            . "Đây là trường đại học công lập trọng điểm hàng đầu tại khu vực Trung du và Miền núi phía Bắc về đào tạo Công nghệ Thông tin, Kỹ thuật Phần mềm và Trí tuệ Nhân tạo.\n\n"
            . "📌 Dự án **Hệ Thống Quản Trị Website & AI Chatbot Nha Khoa Quốc Tế Premier** là đề án ứng dụng thực tiễn được nghiên cứu và phát triển bởi sinh viên ICTU, tích hợp công nghệ AI tư vấn 24/7, RAG tri thức y khoa và sàng lọc nha khoa số!";
    }
    // 6.2. GIỚI THIỆU CHATBOT / BẠN LÀ AI
    elseif (preg_match('/\b(bạn là ai|ban la ai|mày là ai|may la ai|tên gì|ten gi|giới thiệu|gioi thieu|ai tạo ra|ai tao ra|chatbot là gì|chatbot la gi|em là ai|em la ai)\b/u', $msgSearch)) {
        $aiResponse = "👋 Xin chào bạn! Tôi là **Chuyên gia Tư vấn AI Toàn năng của Nha Khoa Quốc Tế Premier Dental Clinic**.\n\n"
            . "Tôi được phát triển bởi nhóm sinh viên **Trường Đại học Công nghệ Thông tin & Truyền thông (ICTU) - Đại học Thái Nguyên**, ứng dụng mô hình RAG kết hợp Tri thức Y khoa Nha khoa chuyên sâu.\n\n"
            . "🌟 **Tôi có thể hỗ trợ bạn:**\n"
            . "• 🩺 Tư vấn chẩn đoán ban đầu mọi triệu chứng răng miệng (đau nhức, sâu răng, ê buốt, viêm nướu...)\n"
            . "• 🦷 Hướng dẫn chi tiết các dịch vụ: Trồng răng Implant, Niềng răng Chỉnh nha, Bọc răng sứ, Nhổ răng khôn Piezotome, Lấy tủy vi phẫu...\n"
            . "• 💰 Tra cứu Bảng giá niêm yết chính thức & Chính sách Trả góp 0% lãi suất\n"
            . "• 👨‍⚕️ Cung cấp hồ sơ đội ngũ Bác sĩ chuyên khoa\n"
            . "• 📅 Hỗ trợ đặt lịch khám ưu tiên miễn phí 100% chụp X-quang 3D CT ConeBeam.\n\n"
            . "Bạn cần tôi giải đáp thắc mắc gì hôm nay?";
    }
    // 6.3. CHÀO HỎI & GIAO TIẾP
    elseif (preg_match('/^(chào|chao|xin chào|xin chao|hi|hello|alo|ê|bạn ơi|ban oi|cho tôi hỏi|cho toi hoi|tư vấn giùm|tu van gium|ad ơi|ad oi)/u', $msgSearch) && !preg_match('/\b(giá|gia|chi phí|chi phi|đau|dau|sưng|sung|nhổ|nho|niềng|nieng|implant|bác sĩ|bac si)\b/u', $msgSearch)) {
        $aiResponse = "👋 **Xin chào bạn! Tôi là Trợ Lý AI của Nha Khoa Quốc Tế Premier Clinic.**\n\n"
            . "Tôi có thể hỗ trợ bạn:\n"
            . "• 🦷 **Tư vấn dịch vụ nha khoa:** Niềng răng, Trồng răng Implant, Bọc răng sứ, Nhổ răng khôn, Trám răng, Lấy tủy...\n"
            . "• 💰 **Bảng giá niêm yết CSDL:** Xem chi phí chi tiết từng dịch vụ\n"
            . "• 👨‍⚕️ **Đội ngũ Bác sĩ:** Thông tin bác sĩ chuyên khoa phụ trách\n"
            . "• 📍 **Địa chỉ & Giờ làm việc:** " . $clinicInfo['address'] . " (08:00 - 20:00)\n"
            . "• 💳 **Chính sách:** Trả góp 0% lãi suất, bảo hành trọn đời Implant\n"
            . "• 📅 **Đặt lịch khám:** Miễn phí 100% khám & chụp X-quang 3D\n\n"
            . "Bạn đang quan tâm dịch vụ nào hoặc cần tư vấn vấn đề gì về răng miệng ạ?";
    }
    // 6.4. TRIỆU CHỨNG ĐAU RĂNG / NHỨC RĂNG / Ê BUỐT / SƯNG NƯỚU / CHẢY MÁU
    elseif (preg_match('/\b(đau răng|dau rang|nhức răng|nhuc rang|ê buốt|e buot|buốt răng|buot rang|sưng nướu|sung nuou|sưng lợi|sung loi|chảy máu|chay mau|áp xe|ap xe|viêm tủy|viem tuy|nhức buốt|mưng mủ|lung lay|đau nhức|dau nhuc)\b/u', $msgSearch)) {
        $aiResponse = "⚠️ **TƯ VẤN Y KHOA KHẨN: HƯỚNG DẪN XỬ LÝ TRIỆU CHỨNG ĐAU NHỨC RĂNG MIỆNG**\n\n"
            . "Cơn đau nhức, ê buốt hoặc sưng nướu thường do các nguyên nhân chính sau:\n"
            . "1. **Sâu răng tiến triển / Viêm tủy:** Lỗ sâu ăn sâu vào ngà răng hoặc tủy gây đau nhức giật theo nhịp mạch đập, đau dữ dội về đêm.\n"
            . "2. **Viêm nướu / Viêm nha chu:** Vi khuẩn mảng bám tích tụ dưới nướu gây sưng đỏ, chảy máu khi đánh răng, có thể dẫn đến tiêu xương và răng lung lay.\n"
            . "3. **Răng khôn mọc lệch / mọc ngầm:** Chèn ép răng số 7 lân cận, gây viêm lợi trùm, sưng má và khó há miệng.\n\n"
            . "🧊 **Biện pháp sơ cứu giảm đau an toàn tại nhà:**\n"
            . "• Súc miệng nhẹ nhàng bằng nước muối sinh lý ấm (0.9%) 3-4 lần/ngày để kháng khuẩn.\n"
            . "• Chườm đá lạnh ngoài má tại vị trí đau khoảng 10-15 phút/lần giúp co mạch, giảm sưng và ức chế cảm giác đau.\n"
            . "• Có thể dùng thuốc giảm đau không kê đơn như Paracetamol 500mg (nếu không có tiền sử dị ứng hoặc bệnh gan).\n"
            . "• Tuyệt đối KHÔNG tự ý ngậm cồn, bôi thuốc lá hoặc chọc vật nhọn vào lỗ sâu.\n\n"
            . "🏥 **Khuyến cáo:** Các biện pháp tại nhà chỉ mang tính tạm thời. Bạn nên đến phòng khám ngay để được Bác sĩ kiểm tra và **chụp phim 3D CT ConeBeam hoàn toàn MIỄN PHÍ** để điều trị triệt để căn nguyên bệnh lý nhé!\n\n"
            . "📞 Hotline Cấp cứu 24/7: **" . $clinicInfo['emergency_phone'] . "** | Tổng đài đặt hẹn: **" . $clinicInfo['hotline'] . "**";
    }
    // 6.5. ĐỊA CHỈ PHÒNG KHÁM / VỊ TRÍ / Ở ĐÂU
    elseif (preg_match('/\b(địa chỉ|dia chi|ở đâu|o dau|chỗ nào|cho nao|nơi nào|noi nao|vị trí|vi tri|chi nhánh|chi nhanh|tới đâu|toi dau|đường nào|duong nao|sơ đồ|chi duong|chỉ đường)\b/u', $msgSearch)) {
        $aiResponse = "Chào bạn! **" . $clinicInfo['name'] . "** rất hân hạnh được đón tiếp bạn tại phòng khám:\n\n"
            . "📍 **Địa chỉ phòng khám:** " . $clinicInfo['address'] . "\n"
            . "⏰ **Thời gian làm việc:**\n"
            . $clinicInfo['working_hours'] . "\n\n"
            . "📞 **Tổng đài đặt lịch & CSKH:** " . $clinicInfo['hotline'] . "\n"
            . "🚨 **Hotline Cấp cứu 24/7:** " . $clinicInfo['emergency_phone'] . "\n\n"
            . "🎁 **Ưu đãi đặc biệt:** Khi đặt lịch hẹn trước qua website hoặc AI Chatbot, bạn sẽ được **MIỄN PHÍ 100% chi phí khám lâm sàng và chụp phim X-quang 3D toàn cảnh**.\n"
            . "👉 Bạn có muốn đặt lịch hẹn khám cùng Bác sĩ chuyên khoa ngay hôm nay không ạ?";
    }
    // 6.6. GIỜ LÀM VIỆC / THỜI GIAN MỞ CỬA
    elseif (preg_match('/\b(mấy giờ|may gio|giờ làm|gio lam|mở cửa|mo cua|đóng cửa|dong cua|lịch làm|lich lam|thời gian làm|thoi gian lam|chủ nhật|chu nhat|thứ 7|thu 7)\b/u', $msgSearch)) {
        $aiResponse = "Chào bạn! Thời gian làm việc chính thức tại **" . $clinicInfo['name'] . "** như sau:\n\n"
            . "⏰ **Khung giờ phục vụ:**\n"
            . $clinicInfo['working_hours'] . "\n\n"
            . "📍 **Địa chỉ:** " . $clinicInfo['address'] . "\n"
            . "📞 **Tổng đài CSKH:** " . $clinicInfo['hotline'] . "\n\n"
            . "Phòng khám làm việc thông trưa tất cả các ngày trong tuần để thuận tiện cho quý khách đến thăm khám!";
    }
    // 6.7. ĐỘI NGŨ BÁC SĨ CHUYÊN GIA
    elseif (preg_match('/\b(bác sĩ|bac si|chuyên gia|chuyen gia|ai khám|ai kham|bác sỹ|bac sy|đội ngũ|doi ngu)\b/u', $msgSearch)) {
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
    // 6.8. CHÍNH SÁCH BẢO HÀNH & TRẢ GÓP 0%
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
    // 6.9. TRẺ EM / RĂNG SỮA / THAY RĂNG
    elseif (preg_match('/\b(trẻ em|răng sữa|bé|mấy tuổi|thay răng|trẻ con|sún răng|sâu răng sữa)\b/u', $msgLower)) {
        $aiResponse = "👶 **TƯ VẤN NHA KHOA TRẺ EM & RĂNG SỮA:**\n\n"
            . "• **Độ tuổi khám răng đầu tiên:** Hiệp hội Nha khoa khuyến nghị nên cho bé đi khám răng ngay từ khi mọc chiếc răng đầu tiên (khoảng 6-12 tháng tuổi).\n"
            . "• **Tầm quan trọng của răng sữa:** Răng sữa giúp bé ăn nhai, phát âm chuẩn và đặc biệt là 'giữ chỗ' cho mầm răng vĩnh viễn mọc đúng vị trí sau này.\n"
            . "• **Dịch vụ nha khoa trẻ em tại Premier:**\n"
            . "  + Trám bít hố rãnh ngừa sâu răng cho trẻ: 150.000đ / răng\n"
            . "  + Bôi Vecni Fluor ngừa sâu răng tăng cường men răng: 200.000đ / hàm\n"
            . "  + Nhổ răng sữa không đau: 50.000 - 100.000đ / răng\n"
            . "  + Tiền chỉnh nha can thiệp thói quen xấu (mút ngón tay, đẩy lưỡi): Giúp cung hàm phát triển đều đặn.\n"
            . "👉 Các bác sĩ tại Premier rất thấu hiểu tâm lý trẻ nhỏ, nhẹ nhàng và tạo môi trường thân thiện cho bé!";
    }
    // 6.10. PHỤ NỮ MANG THAI & CHO CON BÚ
    elseif (preg_match('/\b(mang thai|bà bầu|có bầu|cho con bú|thai kỳ|sinh em bé)\b/u', $msgLower)) {
        $aiResponse = "🤰 **TƯ VẤN NHA KHOA CHO PHỤ NỮ MANG THAI & CHO CON BÚ:**\n\n"
            . "• **Thời điểm vàng điều trị răng:** **Tam cá nguyệt thứ 2 (Tháng thứ 4 đến tháng thứ 6 của thai kỳ)** là thời điểm an toàn và thích hợp nhất để can thiệp các thủ thuật nha khoa như cạo vôi răng, trám răng hoặc điều trị tủy.\n"
            . "• **3 tháng đầu và 3 tháng cuối:** Hạn chế can thiệp xâm lấn như nhổ răng khôn hay chụp X-quang, trừ trường hợp cấp cứu đau nhức dữ dội.\n"
            . "• **Viêm nướu thai nghén:** Do thay đổi nội tiết tố estrogen và progesterone, nướu răng rất dễ sưng đỏ và chảy máu. Mẹ bầu nên cạo vôi răng định kỳ nhẹ nhàng để phòng ngừa.\n"
            . "• **An toàn thuốc:** Phòng khám sử dụng các loại thuốc tê và kháng sinh an toàn nhóm B cho thai kỳ theo hướng dẫn của Bộ Y Tế.";
    }
    // 6.11. DINH DƯỠNG / ĂN GÌ & KIÊNG GÌ
    elseif (preg_match('/\b(ăn gì|kiêng gì|ăn uống|thực phẩm|dinh dưỡng|mới nhổ răng ăn|sau khi nhổ)\b/u', $msgLower)) {
        $aiResponse = "🥗 **HƯỚNG DẪN DINH DƯỠNG & CHẾ ĐỘ ĂN UỐNG NHA KHOA:**\n\n"
            . "• **Sau khi nhổ răng / cấy Implant / tiểu phẫu:**\n"
            . "  + **NÊN ĂN:** Thức ăn mềm, nguội, lỏng dễ nuốt như cháo dinh dưỡng, súp, sữa chua, sinh tố bơ, trứng mềm trong 2-3 ngày đầu.\n"
            . "  + **KIÊNG:** Đồ ăn quá nóng, cay, cứng, giòn (dễ lọt vụn vào ổ răng), không dùng ống hút (áp lực hút có thể làm bật cục máu đông gây chảy máu).\n"
            . "• **Sau khi tẩy trắng răng:**\n"
            . "  + Kiêng thực phẩm có màu sẫm trong 48h đầu (cà phê, trà đặc, nước tương, nghệ, tương cà, sốt vang).\n"
            . "• **Người niềng răng:**\n"
            . "  + Cắt nhỏ thức ăn, tránh cắn trực tiếp đồ cứng bằng răng cửa để tránh bong mắc cài.";
    }
    // 6.12. THUỐC LÁ, RƯỢU BIA & TÁC HẠI
    elseif (preg_match('/\b(thuốc lá|hút thuốc|rượu bia|cà phê|ố vàng|khói thuốc)\b/u', $msgLower)) {
        $aiResponse = "🚬 **ẢNH HƯỞNG CỦA THUỐC LÁ & CHẤT KÍCH THÍCH LÊN RĂNG MIỆNG:**\n\n"
            . "• **Nguy cơ tiêu xương & tụt nướu:** Nicotine trong khói thuốc làm co mạch máu, giảm lưu lượng máu nuôi dưỡng nướu, tăng nguy cơ viêm nha chu gấp 3-5 lần.\n"
            . "• **Ảnh hưởng cấy ghép Implant:** Tỷ lệ thất bại của Implant ở người hút thuốc cao gấp đôi do trụ khó tích hợp với xương hàm. Khuyến cáo ngừng hút thuốc ít nhất 2 tuần trước và 8 tuần sau phẫu thuật cấy trụ.\n"
            . "• **Ố vàng men răng:** Hắc ín và nicotine bám chặt vào men răng gây ố vàng sẫm màu và hôi miệng dai dẳng.";
    }
    // 6.13. NGHIẾN RĂNG / ĐAU KHỚP THÁI DƯƠNG HÀM (TMJ)
    elseif (preg_match('/\b(nghiến răng|khớp thái dương hàm|tmj|mỏi hàm|kêu lục cục|cắn chặt)\b/u', $msgLower)) {
        $aiResponse = "🦷 **CHỨNG NGHIẾN RĂNG KHI NGỦ & RỐI LOẠN KHỚP THÁI DƯƠNG HÀM (TMJ):**\n\n"
            . "• **Nguyên nhân:** Thường do căng thẳng (stress), sai lệch khớp cắn, răng chen chúc hoặc thói quen cơ hàm hoạt động quá mức trong giấc ngủ.\n"
            . "• **Hậu quả nếu không điều trị:** Răng bị mòn vẹt men răng, lộ ngà gây ê buốt dữ dội, mỏi hàm khi nhai, đau nửa đầu và phát ra tiếng 'lục cục' ở khớp gần tai.\n"
            . "• **Giải pháp điều trị tại Premier:**\n"
            . "  + **Máng chống nghiến cá nhân hóa (Nightguard):** Đeo ban đêm để bảo vệ bề mặt răng không bị cọ xát.\n"
            . "  + **Điều chỉnh khớp cắn / Chỉnh nha:** Nắn chỉnh răng về đúng vị trí chuẩn khớp nhai cân bằng.";
    }
    // 6.14. BỆNH TOÀN THÂN (TIỂU ĐƯỜNG, HUYẾT ÁP, TIM MẠCH)
    elseif (preg_match('/\b(tiểu đường|huyết áp|tim mạch|đông máu|uống thuốc chống đông)\b/u', $msgLower)) {
        $aiResponse = "🩺 **LƯU Ý ĐIỀU TRỊ NHA KHOA CHO BỆNH NHÂN CÓ BỆNH LÝ TOÀN THÂN:**\n\n"
            . "• **Bệnh Tiểu đường (Đái tháo đường):** Khi chỉ số đường huyết ổn định (HbA1c < 7%, đường huyết đói < 8 mmol/L), bạn hoàn toàn có thể nhổ răng hoặc cấy ghép Implant bình thường.\n"
            . "• **Huyết áp cao / Tim mạch:** Cần uống thuốc kiểm soát huyết áp đều đặn. Trước mọi thủ thuật, bác sĩ sẽ đo huyết áp và sử dụng thuốc tê chuyên dụng không chứa adrenaline (hoặc nồng độ co mạch cực thấp).\n"
            . "• **Đang dùng thuốc chống đông máu:** Cần thông báo rõ với bác sĩ để có phác đồ phối hợp cùng bác sĩ tim mạch tạm ngưng thuốc đúng quy trình trước khi can thiệp tiểu phẫu.";
    }
    // 6.15. SỢ ĐAU / TÂM LÝ SỢ NHA SĨ
    elseif (preg_match('/\b(sợ đau|có đau không|đau không|sợ nha sĩ|sợ tiêm|gây tê)\b/u', $msgLower)) {
        $aiResponse = "🌿 **BẠN HOÀN TOÀN CÓ THỂ YÊN TÂM: ĐIỀU TRỊ ÊM ÁI KHÔNG ĐAU TẠI PREMIER:**\n\n"
            . "• **Công nghệ Gây tê đa tầng:** Bác sĩ bôi gel tê bề mặt trước khi tiêm thuốc tê bằng kim siêu vi điểm nano, giúp bạn gần như không có cảm giác châm chích.\n"
            . "• **Thiết bị siêu âm Piezotome:** Nhổ răng bằng năng lượng sóng rung siêu âm bóc tách mô êm ái, hoàn toàn không dùng kìm bẩy thô bạo.\n"
            . "• **Đội ngũ y bác sĩ tận tâm:** Luôn lắng nghe, thao tác nhẹ nhàng và sẵn sàng tạm dừng bất cứ khi nào bạn cảm thấy chưa thoải mái!";
    }
    // 6.16. CÂU HỎI VỀ THỜI TIẾT / NGOÀI LỀ / ĐỜI SỐNG
    elseif (preg_match('/\b(thời tiết|mưa|nắng|nhiệt độ|trời hôm nay|hôm nay thế nào)\b/u', $msgLower)) {
        $aiResponse = "🌤️ Hôm nay thời tiết có thể thay đổi thất thường theo mùa, bạn nhớ chú ý giữ gìn sức khỏe, mang theo áo ấm hoặc ô che khi ra ngoài nhé!\n\n"
            . "💡 *Mẹo sức khỏe răng miệng khi thời tiết thay đổi:* Khi trời trở lạnh, các răng có vết sâu ngà hoặc mòn men răng rất dễ bị ê buốt. Bạn nên uống nước ấm và chải răng bằng kem đánh răng chuyên dụng chống ê buốt.\n\n"
            . "Tôi là Trợ lý AI Nha Khoa Premier, bạn cần tôi hỗ trợ tư vấn dịch vụ hay kiểm tra sức khỏe răng miệng nào hôm nay không ạ?";
    }
    // 6.17. CẢM ƠN / TẠM BIỆT
    elseif (preg_match('/\b(cảm ơn|thanks|thank you|tạm biệt|bye|tuyệt vời|tốt quá)\b/u', $msgLower)) {
        $aiResponse = "Dạ không có gì ạ! Rất vui được hỗ trợ và đồng hành cùng bạn chăm sóc nụ cười sáng khỏe! 😊\n\n"
            . "Nếu cần thêm bất kỳ thông tin nào về chăm sóc răng miệng hay đặt lịch khám, bạn cứ nhắn tin cho em bất cứ lúc nào nhé. Chúc bạn một ngày thật nhiều niềm vui và luôn tự tin với nụ cười của mình! ✨";
    }
    // 6.18. HỎI TỪNG DỊCH VỤ CỤ THỂ
    elseif (strpos($msgSearch, 'niềng răng') !== false || strpos($msgSearch, 'nieng rang') !== false || strpos($msgSearch, 'chỉnh nha') !== false || strpos($msgSearch, 'chinh nha') !== false || strpos($msgSearch, 'invisalign') !== false || strpos($msgSearch, 'mắc cài') !== false || strpos($msgSearch, 'mac cai') !== false) {
        $aiResponse = formatServiceAiDetail(9);
    } elseif (strpos($msgSearch, 'implant') !== false || strpos($msgSearch, 'trồng răng') !== false || strpos($msgSearch, 'trong rang') !== false || strpos($msgSearch, 'cấy ghép') !== false || strpos($msgSearch, 'cay ghep') !== false) {
        $aiResponse = formatServiceAiDetail(10);
    } elseif (strpos($msgSearch, 'bọc sứ') !== false || strpos($msgSearch, 'boc su') !== false || strpos($msgSearch, 'răng sứ') !== false || strpos($msgSearch, 'rang su') !== false) {
        $aiResponse = formatServiceAiDetail(7);
    } elseif (strpos($msgSearch, 'veneer') !== false || strpos($msgSearch, 'dán sứ') !== false || strpos($msgSearch, 'dan su') !== false) {
        $aiResponse = formatServiceAiDetail(8);
    } elseif (strpos($msgSearch, 'tẩy trắng') !== false || strpos($msgSearch, 'tay trang') !== false || strpos($msgSearch, 'trắng răng') !== false || strpos($msgSearch, 'trang rang') !== false) {
        $aiResponse = formatServiceAiDetail(6);
    } elseif (strpos($msgSearch, 'trám răng') !== false || strpos($msgSearch, 'tram rang') !== false || strpos($msgSearch, 'hàn răng') !== false || strpos($msgSearch, 'han rang') !== false) {
        $aiResponse = formatServiceAiDetail(3);
    } elseif (strpos($msgSearch, 'nhổ răng') !== false || strpos($msgSearch, 'nho rang') !== false || strpos($msgSearch, 'răng khôn') !== false || strpos($msgSearch, 'rang khon') !== false || strpos($msgSearch, 'nhổ') !== false || strpos($msgSearch, 'nho') !== false) {
        $aiResponse = formatServiceAiDetail(4);
    } elseif (strpos($msgSearch, 'tủy') !== false || strpos($msgSearch, 'tuy') !== false || strpos($msgSearch, 'chữa tủy') !== false || strpos($msgSearch, 'chua tuy') !== false) {
        $aiResponse = formatServiceAiDetail(5);
    } elseif (strpos($msgSearch, 'cạo vôi') !== false || strpos($msgSearch, 'cao voi') !== false || strpos($msgSearch, 'cao răng') !== false || strpos($msgSearch, 'cao rang') !== false) {
        $aiResponse = formatServiceAiDetail(2);
    } elseif (strpos($msgSearch, 'khám') !== false || strpos($msgSearch, 'kham') !== false) {
        $aiResponse = formatServiceAiDetail(1);
    }
    // 6.18.5. HỎI NĂM NAY / THỜI GIAN / NGÀY THÁNG
    elseif (preg_match('/(năm nay|nam nay|hôm nay là|hom nay la|năm bao nhiêu|nam bao nhieu|năm nào|nam nao|ngày mấy|ngay may|tháng mấy|thang may)/iu', $msgSearch)) {
        $curYear = date('Y');
        $curDate = date('d/m/Y');
        $aiResponse = "Dạ hôm nay là ngày **{$curDate}**, năm nay là năm **{$curYear}** ạ! 😊\n\nChúc bạn một ngày tràn đầy năng lượng và luôn tự tin với nụ cười rạng rỡ! Bạn cần em hỗ trợ thêm thông tin gì về chăm sóc răng miệng hay đặt lịch khám không ạ?";
    }
    // 6.19. HỎI CHUNG VỀ DANH MỤC / BẢNG GIÁ DỊCH VỤ TỪ CSDL
    elseif (preg_match('/\b(danh mục|danh muc|bảng giá|bang gia|giá|gia|chi phí|chi phi|dịch vụ|dich vu|bao nhiêu|bao nhieu|báo giá|bao gia)\b/u', $msgSearch) && !preg_match('/(năm|tháng|ngày|tuổi)/iu', $msgSearch)) {
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
    // 6.20. SEMANTIC FALLBACK CHO MỌI CÂU HỎI KHÁC (TUYỆT ĐỐI KHÔNG TỪ CHỐI)
    else {
        $aiResponse = "Dạ cảm ơn câu hỏi của bạn! Với vai trò là **Chuyên gia Trợ lý AI của Nha Khoa Quốc Tế Premier**, tôi luôn sẵn sàng hỗ trợ giải đáp mọi băn khoăn về sức khỏe, nha khoa và các dịch vụ chăm sóc nụ cười toàn diện.\n\n"
            . "Đối với vấn đề bạn vừa chia sẻ: *" . htmlspecialchars($userMessage) . "*:\n"
            . "• Để có đánh giá chính xác và lời khuyên y khoa phù hợp nhất, bạn có thể chia sẻ thêm chi tiết về tình trạng hiện tại của mình (ví dụ: đã xuất hiện bao lâu, có kèm cảm giác đau nhức hay khó chịu nào không)?\n"
            . "• Hoặc bạn có thể để lại Số điện thoại hoặc bấm nút **'Đặt Lịch Khám'** để được Bác sĩ Chuyên khoa thăm khám lâm sàng và chụp phim X-quang 3D toàn diện **hoàn toàn MIỄN PHÍ (0đ)**.\n\n"
            . "📍 **Địa chỉ:** " . $clinicInfo['address'] . " (08:00 - 20:00 hàng ngày)\n"
            . "📞 **Hotline hỗ trợ 24/7:** " . $clinicInfo['hotline'] . " (Cấp cứu: " . $clinicInfo['emergency_phone'] . ")";
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
