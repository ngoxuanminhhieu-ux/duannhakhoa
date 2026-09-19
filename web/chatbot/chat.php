<?php
// web/chatbot/chat.php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

// 1. Read input JSON
$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);

if (!isset($data['message']) || empty(trim($data['message']))) {
    echo json_encode([
        'success' => false,
        'error'   => 'Tin nhắn không được để trống.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userMessage = trim($data['message']);
$sessionId   = session_id() ?: 'default_session';

// 2. System Prompt Definition
$systemPrompt = "Bạn là trợ lý AI chuyên nghiệp của phòng khám nha khoa Smile. "
    . "Bạn cung cấp thông tin nha khoa cơ bản, dễ hiểu, thân thiện và an toàn. "
    . "Không được tự chẩn đoán bệnh hoặc thay thế bác sĩ. "
    . "Khi người dùng có triệu chứng nghiêm trọng (như đau dữ dội, sưng mặt, sốt, chảy máu kéo dài), hãy khuyến nghị họ đến cơ sở nha khoa để được khám trực tiếp ngay lập tức. "
    . "Không đưa ra cam kết điều trị 100%. "
    . "Nếu người dùng có nhu cầu đặt lịch khám, hãy vui vẻ hướng dẫn họ truy cập trang Đặt Lịch (/pages/booking.php) hoặc liên hệ Hotline 1900 6868.";

$openAiApiKey = getenv('OPENAI_API_KEY') ?: ($_ENV['OPENAI_API_KEY'] ?? '');

$aiResponse = '';
$isApiSuccess = false;

// 3. Try Calling OpenAI API if API key is present and not dummy
if (!empty($openAiApiKey) && strpos($openAiApiKey, 'your_') === false && strlen($openAiApiKey) > 20) {
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    $payload = json_encode([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userMessage]
        ],
        'temperature' => 0.7,
        'max_tokens' => 500
    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openAiApiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);

    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if (!$curlErr && $response) {
        $resData = json_decode($response, true);
        if (isset($resData['choices'][0]['message']['content'])) {
            $aiResponse = trim($resData['choices'][0]['message']['content']);
            $isApiSuccess = true;
        }
    }
}

// 4. Intelligent Fallback Response (if API Key is not set or request timed out)
if (!$isApiSuccess || empty($aiResponse)) {
    $msgLower = mb_strtolower($userMessage);

    if (strpos($msgLower, 'đau răng') !== false || strpos($msgLower, 'nhức răng') !== false || strpos($msgLower, 'sâu răng') !== false) {
        $aiResponse = "Khi bị đau hoặc nhức răng, bạn nên:\n"
            . "1. Chườm lạnh bên ngoài má để giảm sưng nhẹ.\n"
            . "2. Súc miệng bằng nước muối ấm loãng để sát khuẩn.\n"
            . "3. Tránh nhai đồ quá cứng, quá nóng hoặc quá lạnh.\n"
            . "⚠️ Lưu ý: Nếu cơn đau kéo dài hoặc có dấu hiệu sưng sốt, bạn nên đến Nha Khoa Smile để bác sĩ kiểm tra và xử lý dứt điểm. Bạn có thể nhấn vào mục 'Đặt Lịch' trên menu để chọn giờ khám phù hợp nhé!";
    } elseif (strpos($msgLower, 'đặt lịch') !== false || strpos($msgLower, 'hẹn khám') !== false || strpos($msgLower, 'đăng ký') !== false) {
        $aiResponse = "Để đặt lịch hẹn khám nhanh nhất tại Nha Khoa Smile, bạn vui lòng chọn mục **Đặt Lịch** trên thanh menu hoặc truy cập trang `/pages/booking.php`. Bạn chỉ cần điền tên, số điện thoại và chọn giờ khám mong muốn, phòng khám sẽ gọi lại xác nhận ngay!";
    } elseif (strpos($msgLower, 'giá') !== false || strpos($msgLower, 'chi phí') !== false || strpos($msgLower, 'bao nhiêu') !== false) {
        $aiResponse = "Nha Khoa Smile cung cấp bảng giá niêm yết minh bạch:\n"
            . "• Khám tổng quát: 100.000 VNĐ\n"
            . "• Cạo vôi răng: 250.000 VNĐ\n"
            . "• Trám răng Composite: 350.000 VNĐ\n"
            . "• Tẩy trắng Laser: 2.000.000 VNĐ\n"
            . "• Bọc răng sứ: Từ 3.500.000 VNĐ/răng\n"
            . "• Niềng răng: Từ 25.000.000 VNĐ\n"
            . "Bạn có thể vào mục 'Dịch vụ' để xem chi tiết đầy đủ bảng giá!";
    } elseif (strpos($msgLower, 'bác sĩ') !== false || strpos($msgLower, 'chuyên gia') !== false) {
        $aiResponse = "Đội ngũ bác sĩ tại Nha Khoa Smile gồm các Thạc sĩ, Bác sĩ CKI giàu kinh nghiệm như ThS.BS Nguyễn Văn An (Tổng quát), BS.CKI Trần Thị Mai (Chỉnh nha niềng răng), ThS.BS Lê Hoàng Nam (Implant). Bạn có thể xem hồ sơ bác sĩ ở trang 'Đội ngũ bác sĩ'!";
    } else {
        $aiResponse = "Cảm ơn câu hỏi của bạn! Nha Khoa Smile luôn sẵn sàng hỗ trợ bạn chăm sóc sức khỏe răng miệng. Bạn có thể hỏi tôi về triệu chứng đau răng, giá dịch vụ, thông tin bác sĩ hoặc hướng dẫn đặt lịch hẹn khám!";
    }
}

// 5. Save to database table chatbot_messages
try {
    $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, user_message, ai_response) VALUES (?, ?, ?)");
    $stmt->execute([$sessionId, $userMessage, $aiResponse]);
} catch (Exception $e) {
    // Silent fail for logging if db error occurs
}

// 6. Return JSON response
echo json_encode([
    'success' => true,
    'reply'   => $aiResponse
], JSON_UNESCAPED_UNICODE);
