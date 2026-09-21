<?php
// web/chatbot/chat.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// 4. Intelligent Fallback Response (if API Key is not set, failed, or request timed out)
if (!$isApiSuccess || empty($aiResponse)) {
    $msgLower = mb_strtolower($userMessage, 'UTF-8');

    // 4.1 Greetings & Introductions
    if (preg_match('/\b(chào|xin chào|hi|hello|tư vấn|bạn ơi|alo)\b/u', $msgLower) && !preg_match('/(giá|chi phí|bác sĩ|đặt lịch|bao nhiêu)/u', $msgLower)) {
        $aiResponse = "👋 **Xin chào! Tôi là Trợ lý AI của Nha Khoa Smile.**\n\n"
            . "Tôi có thể hỗ trợ bạn giải đáp thắc mắc về:\n"
            . "• 🦷 **Dịch vụ nha khoa:** Niềng răng, Implant, Răng sứ, Tẩy trắng, Nhổ răng khôn, Trám răng, Cạo vôi răng, Điều trị tủy...\n"
            . "• 💰 **Chi phí & Bảng giá** chi tiết từng dịch vụ\n"
            . "• 👨‍⚕️ **Đội ngũ Bác sĩ chuyên khoa** giàu kinh nghiệm\n"
            . "• 📋 **Chính sách trả góp 0% & Bảo hành chính hãng**\n"
            . "• 📅 **Hướng dẫn Đặt lịch hẹn khám miễn phí**\n\n"
            . "Bạn cần hỗ trợ thông tin gì hôm nay ạ?";
    }
    // 4.2 Symptoms (Pain, Swelling, Toothache)
    elseif (strpos($msgLower, 'đau răng') !== false || strpos($msgLower, 'nhức răng') !== false || strpos($msgLower, 'sâu răng') !== false || strpos($msgLower, 'sưng') !== false || strpos($msgLower, 'chảy máu') !== false) {
        $aiResponse = "🦷 **Tư vấn xử lý triệu chứng răng miệng:**\n"
            . "1. Chườm lạnh bên má bị đau để hỗ trợ giảm sưng và xoa dịu cơn đau.\n"
            . "2. Súc miệng nhẹ nhàng bằng nước muối ấm loãng để sát khuẩn.\n"
            . "3. Hạn chế dùng thức ăn quá cứng, quá nóng hoặc quá lạnh.\n\n"
            . "⚠️ **Lưu ý quan trọng:** Cơn đau nhức thường do sâu răng chạm tủy hoặc viêm nướu. Bạn nên đến Nha Khoa Smile để Bác sĩ khám trực tiếp và xử lý sớm, tránh biến chứng. Bạn có thể chọn mục **Đặt Lịch** trên thanh menu để hẹn giờ khám nhé!";
    }
    // 4.3 Root canal / Endodontic Treatment
    elseif (strpos($msgLower, 'tủy') !== false || strpos($msgLower, 'chữa tủy') !== false || strpos($msgLower, 'diệt tủy') !== false) {
        $aiResponse = "🦷 **Dịch vụ Điều trị tủy răng (Nội nha không đau):**\n"
            . "• Chi phí điều trị tủy răng cửa / răng tiền cối: 500.000 - 800.000 VNĐ/răng\n"
            . "• Chi phí điều trị tủy răng hàm: 1.000.000 - 1.500.000 VNĐ/răng\n"
            . "• Quy trình điều trị êm ái bằng máy vi phẫu, làm sạch triệt để vi khuẩn, bảo vệ răng thật tối đa.";
    }
    // 4.4 Scaling & Teeth Cleaning
    elseif (strpos($msgLower, 'cạo vôi') !== false || strpos($msgLower, 'cao răng') !== false || strpos($msgLower, 'lấy cao') !== false || strpos($msgLower, 'vệ sinh răng') !== false) {
        $aiResponse = "✨ **Dịch vụ Cạo vôi răng & Đánh bóng siêu âm:**\n"
            . "• Chi phí trọn gói: **250.000 VNĐ**\n"
            . "• Công nghệ sóng siêu âm Cavitron hiện đại, làm sạch mảng bám nhẹ nhàng, không gây đau hay ê buốt nướu.\n"
            . "• Khuyến nghị: Nên cạo vôi răng định kỳ 6 tháng/lần để ngừa viêm nướu và hôi miệng.";
    }
    // 4.5 Composite Fillings
    elseif (strpos($msgLower, 'trám răng') !== false || strpos($msgLower, 'hàn răng') !== false || strpos($msgLower, 'mẻ răng') !== false) {
        $aiResponse = "🦷 **Dịch vụ Trám răng thẩm mỹ Composite Nano 3M (USA):**\n"
            . "• Chi phí tham khảo: **300.000 - 600.000 VNĐ/răng** (tùy vị trí và mức độ tổn thương)\n"
            . "• Vật liệu Composite cao cấp có màu sắc trùng khớp 99% với màu răng tự nhiên, độ bền cao, bảo hành 2 năm.";
    }
    // 4.6 Teeth Whitening
    elseif (strpos($msgLower, 'tẩy trắng') !== false || strpos($msgLower, 'làm trắng') !== false || strpos($msgLower, 'whitening') !== false) {
        $aiResponse = "✨ **Dịch vụ Tẩy trắng răng Laser Whitening:**\n"
            . "• Tẩy trắng tại phòng khám bằng Laser: **2.000.000 VNĐ** (bật 3 - 5 tông chỉ trong 45 phút, không ê buốt)\n"
            . "• Bộ tẩy trắng tại nhà kèm khay cá nhân: **1.200.000 VNĐ**\n"
            . "🎁 Miễn phí cạo vôi răng và đánh bóng trước khi tẩy trắng!";
    }
    // 4.7 Orthodontics / Braces
    elseif (strpos($msgLower, 'niềng răng') !== false || strpos($msgLower, 'chỉnh nha') !== false || strpos($msgLower, 'invisalign') !== false || strpos($msgLower, 'mắc cài') !== false) {
        $aiResponse = "😁 **Dịch vụ Chỉnh nha - Niềng răng tại Nha Khoa Smile:**\n"
            . "• Niềng răng mắc cài kim loại: Từ 25.000.000 - 35.000.000 VNĐ\n"
            . "• Niềng răng mắc cài sứ thẩm mỹ: Từ 35.000.000 - 45.000.000 VNĐ\n"
            . "• Niềng răng trong suốt Invisalign (Mỹ): Từ 60.000.000 VNĐ\n"
            . "🎁 **Chính sách ưu đãi:** Hỗ trợ **Trả góp 0% lãi suất** (chỉ trả trước 30%), miễn phí chụp phim X-quang 3D và thăm khám 1:1 cùng Bác sĩ Chuyên khoa Chỉnh nha!";
    }
    // 4.8 Implant
    elseif (strpos($msgLower, 'implant') !== false || strpos($msgLower, 'trồng răng') !== false || strpos($msgLower, 'mất răng') !== false) {
        $aiResponse = "🦷 **Dịch vụ Cấy ghép Implant phục hình răng đã mất:**\n"
            . "• Implant Hàn Quốc (Osstem/Dentium): 13.000.000 - 16.000.000 VNĐ/trụ\n"
            . "• Implant Thụy Sĩ (Straumann Premium): Từ 24.000.000 VNĐ/trụ\n"
            . "• Thời gian bảo hành: Từ 10 năm đến **Trọn đời**\n"
            . "👨‍⚕️ Phụ trách chính bởi ThS.BS Lê Hoàng Nam - Chuyên gia cấy ghép Implant tu nghiệp Thụy Sĩ với hơn 15 năm kinh nghiệm!";
    }
    // 4.9 Dental Crowns & Veneers
    elseif (strpos($msgLower, 'bọc sứ') !== false || strpos($msgLower, 'răng sứ') !== false || strpos($msgLower, 'veneer') !== false) {
        $aiResponse = "✨ **Dịch vụ Bọc răng sứ thẩm mỹ & Mặt dán Veneer:**\n"
            . "• Răng sứ Katana / Zirconia (Đức): Từ 3.500.000 VNĐ/răng\n"
            . "• Răng sứ Cercon HT cao cấp: Từ 5.000.000 VNĐ/răng\n"
            . "• Mặt dán sứ Veneer Emax bảo tồn răng thật: Từ 6.500.000 VNĐ/răng\n"
            . "Chất liệu răng toàn sứ chính hãng, bảo hành điện tử 10 - 15 năm!";
    }
    // 4.10 Tooth Extraction
    elseif (strpos($msgLower, 'nhổ răng') !== false || strpos($msgLower, 'răng khôn') !== false || strpos($msgLower, 'nhổ') !== false) {
        $aiResponse = "🦷 **Nhổ răng & Nhổ răng khôn Piezotome không đau:**\n"
            . "• Nhổ răng thường / răng sữa: 150.000 - 300.000 VNĐ\n"
            . "• Nhổ răng khôn mọc lệch / mọc ngầm bằng Sóng siêu âm Piezotome: 1.500.000 - 3.000.000 VNĐ/răng\n"
            . "Quy trình nhẹ nhàng, không đau, hạn chế xâm lấn và lành thương cực nhanh!";
    }
    // 4.11 Price List Inquiry
    elseif (strpos($msgLower, 'giá') !== false || strpos($msgLower, 'chi phí') !== false || strpos($msgLower, 'bao nhiêu') !== false || strpos($msgLower, 'bảng giá') !== false) {
        $aiResponse = "📋 **Bảng giá niêm yết tham khảo tại Nha Khoa Smile:**\n"
            . "• Khám tổng quát & Chụp phim X-quang: Miễn phí\n"
            . "• Cạo vôi răng siêu âm: 250.000 VNĐ\n"
            . "• Trám răng Composite: 350.000 - 600.000 VNĐ/răng\n"
            . "• Tẩy trắng răng Laser Whitening: 2.000.000 VNĐ\n"
            . "• Bọc răng sứ cao cấp: Từ 3.500.000 VNĐ/răng\n"
            . "• Niềng răng Chỉnh nha: Từ 25.000.000 VNĐ (Có trả góp 0%)\n"
            . "• Cấy ghép Implant: Từ 13.000.000 VNĐ/trụ\n"
            . "Quý khách có thể xem chi tiết tại mục **Dịch Vụ** trên menu trang web!";
    }
    // 4.12 Appointment Booking Guide
    elseif (strpos($msgLower, 'đặt lịch') !== false || strpos($msgLower, 'hẹn khám') !== false || strpos($msgLower, 'đăng ký') !== false || strpos($msgLower, 'muốn khám') !== false) {
        $aiResponse = "📅 **Hướng dẫn đặt lịch khám nhanh:**\n"
            . "Quý khách có thể dễ dàng đặt lịch hẹn bằng các cách sau:\n"
            . "1. Truy cập trang **Đặt Lịch** (`/pages/booking.php`) trên menu chính.\n"
            . "2. Liên hệ trực tiếp Hotline: **1900 6868** hoặc **0912 345 678**.\n"
            . "Đội ngũ lễ tân sẽ liên hệ xác nhận lịch hẹn trong vòng 15 phút!";
    }
    // 4.13 Doctor Information
    elseif (strpos($msgLower, 'bác sĩ') !== false || strpos($msgLower, 'chuyên gia') !== false || strpos($msgLower, 'bác sỹ') !== false) {
        $aiResponse = "👨‍⚕️ **Đội ngũ Bác sĩ Chuyên gia Nha Khoa Smile:**\n"
            . "• **ThS.BS Nguyễn Văn An:** Trưởng khoa Nha khoa Tổng quát & Thẩm mỹ (15 năm kinh nghiệm).\n"
            . "• **BS.CKI Trần Thị Mai:** Chuyên gia Niềng răng - Chỉnh nha Invisalign (12 năm kinh nghiệm).\n"
            . "• **ThS.BS Lê Hoàng Nam:** Chuyên gia Cấy ghép Implant & Phục hình (15 năm kinh nghiệm).\n"
            . "• **BS. Phạm Thùy Linh:** Chuyên gia Bọc răng sứ & Thiết kế nụ cười Smile Design (7 năm kinh nghiệm).\n"
            . "• **BS.CKI Đỗ Minh Đức:** Chuyên gia Phẫu thuật & Nhổ răng khôn Piezotome (10 năm kinh nghiệm).";
    }
    // 4.14 Clinic Address, Working Hours & Info
    elseif (strpos($msgLower, 'địa chỉ') !== false || strpos($msgLower, 'ở đâu') !== false || strpos($msgLower, 'giờ làm') !== false || strpos($msgLower, 'mấy giờ') !== false || strpos($msgLower, 'hotline') !== false || strpos($msgLower, 'liên hệ') !== false || strpos($msgLower, 'trả góp') !== false || strpos($msgLower, 'bảo hành') !== false || strpos($msgLower, 'vô trùng') !== false) {
        $aiResponse = "📍 **Thông tin & Chính sách Phòng khám Nha Khoa Smile:**\n"
            . "• **Địa chỉ:** 123 Đường Nguyễn Trãi, Phường 2, Quận 5, TP. Hồ Chí Minh\n"
            . "• **Hotline 24/7:** 1900 6868 - 0912 345 678\n"
            . "• **Giờ làm việc:**\n"
            . "  - Thứ 2 - Thứ 7: 08:00 - 20:00 (làm thông trưa)\n"
            . "  - Chủ Nhật: 08:30 - 17:30\n"
            . "• **Ưu đãi:** Hỗ trợ Trả góp 0% lãi suất qua thẻ tín dụng hơn 25 ngân hàng. Vô trùng chuẩn ISO Class B.";
    }
    // 4.15 Default Friendly Fallback
    else {
        $aiResponse = "👋 Cảm ơn quý khách đã liên hệ **Nha Khoa Smile**!\n\n"
            . "Tôi là Trợ lý AI có thể hỗ trợ quý khách thông tin về:\n"
            . "• 🦷 Triệu chứng đau răng, sâu răng, răng khôn, tư vấn các dịch vụ nha khoa\n"
            . "• 💰 Bảng giá chi tiết (Niềng răng, Implant, Răng sứ, Tẩy trắng, Trám răng...)\n"
            . "• 👨‍⚕️ Thông tin & Lịch làm việc của Bác sĩ chuyên khoa\n"
            . "• 📅 Hướng dẫn Đặt lịch hẹn khám ưu tiên\n\n"
            . "Quý khách cần hỗ trợ dịch vụ nào ạ?";
    }
}

// 5. Save to database table chatbot_messages if DB connection is active
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, user_message, ai_response) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $userMessage, $aiResponse]);
    } catch (\Throwable $e) {
        // Silent fail for logging if table does not exist or db error occurs
    }
}

// 6. Return JSON response
echo json_encode([
    'success' => true,
    'reply'   => $aiResponse
], JSON_UNESCAPED_UNICODE);
    } else {
        $aiResponse = "👋 Cảm ơn quý khách đã liên hệ **Nha Khoa Smile**!\n"
            . "Tôi là Trợ lý AI có thể giải đáp thắc mắc về:\n"
            . "• 🦷 Triệu chứng đau răng, nhức răng, sâu răng, răng khôn\n"
            . "• 💰 Bảng giá chi phí dịch vụ (Niềng răng, Implant, Răng sứ, Tẩy trắng...)\n"
            . "• 👨‍⚕️ Thông tin Bác sĩ chuyên khoa\n"
            . "• 📅 Hướng dẫn Đặt lịch hẹn khám\n"
            . "Quý khách cần hỗ trợ thông tin nào ạ?";
    }
}

// 5. Save to database table chatbot_messages if DB connection is active
if ($pdo) {
    try {
        $stmt = $pdo->prepare("INSERT INTO chatbot_messages (session_id, user_message, ai_response) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $userMessage, $aiResponse]);
    } catch (\Throwable $e) {
        // Silent fail for logging if table does not exist or db error occurs
    }
}

// 6. Return JSON response
echo json_encode([
    'success' => true,
    'reply'   => $aiResponse
], JSON_UNESCAPED_UNICODE);

