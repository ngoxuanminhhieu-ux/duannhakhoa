<?php
// web/admin/admin_chat_api.php
// API Trợ lý AI Quản Trị dành riêng cho tài khoản Admin

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Kiểm tra quyền Admin
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$adminName = $_SESSION['admin_name'] ?? 'Quản trị viên';

// Đọc dữ liệu đầu vào
$inputRaw = file_get_contents('php://input');
$data = json_decode($inputRaw, true);

if (!isset($data['message']) || empty(trim($data['message']))) {
    echo json_encode([
        'success' => false,
        'error' => 'Nội dung tin nhắn không được để trống.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userMessage = trim($data['message']);
$lowerMsg = mb_strtolower($userMessage, 'UTF-8');

function stripVN($str) {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", 'y', $str);
    $str = preg_replace("/(đ)/u", 'd', $str);
    return mb_strtolower($str, 'UTF-8');
}
$normMsg = stripVN($lowerMsg);

// Kết nối CSDL
$pdo = getDB();

$today = date('Y-m-d');
$total_patients = 0;
$total_appointments = 0;
$today_appointments = [];
$pending_appointments = [];
$doctors_list = [];
$services_list = [];

if ($pdo) {
    try {
        $total_patients = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
        $total_appointments = (int)$pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();

        // Lịch hẹn hôm nay
        $stmtToday = $pdo->prepare("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.notes,
                   p.full_name as patient_name, p.phone as patient_phone,
                   d.full_name as doctor_name, s.name as service_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            LEFT JOIN doctors d ON a.doctor_id = d.id
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.appointment_date = ?
            ORDER BY a.appointment_time ASC
        ");
        $stmtToday->execute([$today]);
        $today_appointments = $stmtToday->fetchAll(PDO::FETCH_ASSOC);

        // Lịch hẹn chờ xác nhận (pending)
        $stmtPending = $pdo->query("
            SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                   p.full_name as patient_name, p.phone as patient_phone,
                   d.full_name as doctor_name, s.name as service_name
            FROM appointments a
            JOIN patients p ON a.patient_id = p.id
            LEFT JOIN doctors d ON a.doctor_id = d.id
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.status = 'pending'
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
            LIMIT 10
        ");
        $pending_appointments = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

        // Bác sĩ
        $stmtDoc = $pdo->query("SELECT id, full_name, specialty, experience FROM doctors ORDER BY id ASC");
        $doctors_list = $stmtDoc->fetchAll(PDO::FETCH_ASSOC);

        // Dịch vụ
        $stmtSvc = $pdo->query("SELECT id, name, price FROM services ORDER BY id ASC");
        $services_list = $stmtSvc->fetchAll(PDO::FETCH_ASSOC);

    } catch (\Throwable $e) {
        // Fallback gracefully
    }
}

// 1. Xử lý các intent nghiệp vụ quản trị nhanh (Instant Rule Engine)
$reply = "";

// 0. BÁO CÁO DOANH THU & TÀI CHÍNH
if (strpos($lowerMsg, 'doanh thu') !== false || strpos($normMsg, 'doanh thu') !== false || strpos($lowerMsg, 'tài chính') !== false || strpos($normMsg, 'tai chinh') !== false || strpos($lowerMsg, 'doanh số') !== false || strpos($normMsg, 'doanh so') !== false || strpos($lowerMsg, 'tiền thu') !== false || strpos($normMsg, 'tien thu') !== false) {
    $realized = 0;
    $projected = 0;
    $todayRev = 0;
    $monthRev = 0;
    $completedCount = 0;
    if ($pdo) {
        $r = $pdo->query("
            SELECT 
                SUM(CASE WHEN a.status = 'completed' THEN s.price ELSE 0 END) as realized,
                SUM(CASE WHEN a.status = 'confirmed' THEN s.price ELSE 0 END) as projected,
                SUM(CASE WHEN a.status = 'completed' AND a.appointment_date = CURRENT_DATE() THEN s.price ELSE 0 END) as today_rev,
                SUM(CASE WHEN a.status = 'completed' AND MONTH(a.appointment_date) = MONTH(CURRENT_DATE()) AND YEAR(a.appointment_date) = YEAR(CURRENT_DATE()) THEN s.price ELSE 0 END) as month_rev,
                COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed_cnt
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.id
        ")->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $realized = (float)($r['realized'] ?? 0);
            $projected = (float)($r['projected'] ?? 0);
            $todayRev = (float)($r['today_rev'] ?? 0);
            $monthRev = (float)($r['month_rev'] ?? 0);
            $completedCount = (int)($r['completed_cnt'] ?? 0);
        }
    }
    $formattedDate = date('d/m/Y');
    $reply = "💰 **BÁO CÁO TÀI CHÍNH & DOANH THU PHÒNG KHÁM ({$formattedDate}):**\n\n";
    $reply .= "- **Tổng thực thu (Đã khám xong):** **" . number_format($realized, 0, ',', '.') . " ₫** (từ {$completedCount} ca điều trị)\n";
    $reply .= "- **Doanh thu dự kiến (Đã xác nhận đặt lịch):** **" . number_format($projected, 0, ',', '.') . " ₫**\n";
    $reply .= "- **Doanh thu tháng này:** **" . number_format($monthRev, 0, ',', '.') . " ₫**\n";
    $reply .= "- **Doanh thu thực thu hôm nay:** **" . number_format($todayRev, 0, ',', '.') . " ₫**\n\n";
    $reply .= "👉 *Quản trị viên có thể xem biểu đồ trực quan, cơ cấu dịch vụ và bảng xếp hạng bác sĩ tại trang [Theo Dõi Doanh Thu](revenue.php).*";

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'revenue'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// A. THỐNG KÊ HÔM NAY / TỔNG QUAN
if (strpos($lowerMsg, 'thống kê') !== false || strpos($normMsg, 'thong ke') !== false || strpos($lowerMsg, 'hôm nay') !== false || strpos($normMsg, 'hom nay') !== false || strpos($lowerMsg, 'tổng quan') !== false || strpos($normMsg, 'tong quan') !== false || strpos($lowerMsg, 'báo cáo') !== false || strpos($normMsg, 'bao cao') !== false) {
    $todayCount = count($today_appointments);
    $pendingCount = count($pending_appointments);
    $formattedDate = date('d/m/Y');

    $reply = "📊 **BÁO CÁO NHANH TÌNH HÌNH PHÒNG KHÁM ({$formattedDate}):**\n\n";
    $reply .= "- **Tổng số bệnh nhân trong hệ thống:** {$total_patients} người\n";
    $reply .= "- **Tổng số lượt lịch hẹn:** {$total_appointments} ca\n";
    $reply .= "- **Số lịch khám trong ngày hôm nay:** **{$todayCount} ca**\n";
    $reply .= "- **Lịch hẹn đang chờ duyệt:** **{$pendingCount} ca**\n";
    $reply .= "- **Đội ngũ Bác sĩ phụ trách:** " . count($doctors_list) . " chuyên gia\n";
    $reply .= "- **Danh mục dịch vụ y tế:** " . count($services_list) . " dịch vụ\n\n";

    if ($todayCount > 0) {
        $reply .= "📋 **Chi tiết các ca khám hôm nay:**\n";
        foreach ($today_appointments as $idx => $app) {
            $time = substr($app['appointment_time'], 0, 5);
            $doc = $app['doctor_name'] ?? 'Chưa phân bổ';
            $svc = $app['service_name'] ?? 'Khám tổng quát';
            $stt = $app['status'] == 'confirmed' ? '✅ Đã xác nhận' : ($app['status'] == 'completed' ? '🌟 Hoàn tất' : '⏳ Chờ duyệt');
            $reply .= ($idx + 1) . ". **{$time}** - {$app['patient_name']} ({$app['patient_phone']})\n   ↳ *{$svc}* | BS: {$doc} | {$stt}\n";
        }
    } else {
        $reply .= "ℹ️ *Hôm nay chưa có lịch hẹn nào mới được đặt trước.*\n";
    }

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'stats'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// B. LỊCH HẸN CHỜ XÁC NHẬN (PENDING)
if (strpos($lowerMsg, 'chờ duyệt') !== false || strpos($normMsg, 'cho duyet') !== false || strpos($lowerMsg, 'chờ xác nhận') !== false || strpos($normMsg, 'cho xac nhan') !== false || strpos($lowerMsg, 'pending') !== false) {
    $pendingCount = count($pending_appointments);
    if ($pendingCount === 0) {
        $reply = "🎉 **Tuyệt vời!** Hiện tại không có lịch hẹn nào đang ở trạng thái **Chờ xác nhận**.\nToàn bộ lịch hẹn đã được xử lý.";
    } else {
        $reply = "⏳ **DANH SÁCH LỊCH HẸN CHỜ XÁC NHẬN ({$pendingCount} CA):**\n\n";
        foreach ($pending_appointments as $idx => $app) {
            $date = date('d/m/Y', strtotime($app['appointment_date']));
            $time = substr($app['appointment_time'], 0, 5);
            $svc = $app['service_name'] ?? 'Khám & Tư vấn';
            $doc = $app['doctor_name'] ?? 'Chưa xếp BS';
            $reply .= "**#" . $app['id'] . "** - **" . $app['patient_name'] . "** (" . $app['patient_phone'] . ")\n";
            $reply .= "📅 Ngày hẹn: {$date} lúc {$time}\n";
            $reply .= "🦷 Dịch vụ: {$svc} | Bác sĩ: {$doc}\n";
            $reply .= "👉 *Bạn có thể vào trang [Quản lý Lịch hẹn](appointments.php) để duyệt hoặc đổi trạng thái.*\n\n";
        }
    }

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'pending_list'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// C. TÌM KIẾM BỆNH NHÂN (THEO TÊN HOẶC SỐ ĐIỆN THOẠI)
if (preg_match('/(tìm|tra cứu|bệnh nhân|sđt|số điện thoại|khách hàng)\s+([0-9a-zA-Z\s\p{L}]+)/u', $userMessage, $matches)) {
    $keyword = trim($matches[2]);
    if (strlen($keyword) >= 3 && $pdo) {
        try {
            $stmtSearch = $pdo->prepare("
                SELECT p.*, COUNT(a.id) as total_visits
                FROM patients p
                LEFT JOIN appointments a ON p.id = a.patient_id
                WHERE p.full_name LIKE ? OR p.phone LIKE ?
                GROUP BY p.id
                LIMIT 5
            ");
            $like = "%{$keyword}%";
            $stmtSearch->execute([$like, $like]);
            $foundPatients = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($foundPatients)) {
                $reply = "🔍 **KẾT QUẢ TÌM KIẾM BỆNH NHÂN ('{$keyword}'):**\n\n";
                foreach ($foundPatients as $pat) {
                    $reply .= "👤 **" . htmlspecialchars($pat['full_name']) . "** (Mã: #" . $pat['id'] . ")\n";
                    $reply .= "- 📞 Số điện thoại: **" . htmlspecialchars($pat['phone']) . "**\n";
                    $reply .= "- 📧 Email: " . ($pat['email'] ? htmlspecialchars($pat['email']) : 'Chưa có') . "\n";
                    $reply .= "- 🏥 Số lần đến khám: **" . $pat['total_visits'] . " lần**\n";
                    $reply .= "- 📅 Ngày tạo hồ sơ: " . date('d/m/Y', strtotime($pat['created_at'])) . "\n\n";
                }
                echo json_encode(['success' => true, 'reply' => $reply, 'action' => 'patient_search'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        } catch (\Throwable $e) {}
    }
}

// D. SOẠN TIN NHẮN NHẮC HẸN (SMS / ZALO MẪU)
if (strpos($lowerMsg, 'nhắc hẹn') !== false || strpos($normMsg, 'nhac hen') !== false || strpos($lowerMsg, 'nhắc lịch') !== false || strpos($normMsg, 'nhac lich') !== false || strpos($lowerMsg, 'soạn tin') !== false || strpos($normMsg, 'soan tin') !== false || strpos($lowerMsg, 'tin nhắn') !== false || strpos($normMsg, 'tin nhan') !== false || strpos($lowerMsg, 'mẫu tin') !== false || strpos($normMsg, 'mau tin') !== false) {
    $reply = "📝 **MẪU TIN NHẮN SMS / ZALO NHẮC LỊCH HẸN KHÁM DÀNH CHO LỄ TÂN:**\n\n";
    $reply .= "```text\n";
    $reply .= "[NHA KHOA PREMIER] Kinh chao Quy khach [Ten_Benh_Nhan],\n";
    $reply .= "Phong kham xin tran trong nhac lich hen kham rang cua Quy khach vao luc [Gio_Hen] ngay [Ngay_Hen] cung Bac si [Ten_Bac_Si].\n";
    $reply .= "Dia chi: 123 Nguyen Van Cu, P.4, Q.5, TP.HCM.\n";
    $reply .= "Hotline ho tro: 1900 6868.\n";
    $reply .= "Rat han hanh duoc don tiep va phung su Quy khach!\n";
    $reply .= "```\n\n";
    $reply .= "💡 **Mẹo:** Bạn chỉ cần điền tên khách, ngày giờ hẹn và gửi qua Zalo ZNS hoặc SMS Brandname để giảm tỷ lệ vắng mặt xuống dưới 2%.";

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'reminder_template'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// E. HƯỚNG DẪN HẬU PHẪU / CHĂM SÓC RĂNG MIỆNG
if (strpos($lowerMsg, 'hậu phẫu') !== false || strpos($normMsg, 'hau phau') !== false || strpos($lowerMsg, 'sau nhổ') !== false || strpos($normMsg, 'sau nho') !== false || strpos($lowerMsg, 'sau cấy') !== false || strpos($normMsg, 'sau cay') !== false || strpos($lowerMsg, 'sau khi làm') !== false || strpos($lowerMsg, 'chăm sóc sau') !== false || strpos($normMsg, 'dan do') !== false || strpos($lowerMsg, 'dặn dò') !== false) {
    $reply = "💡 **QUY TRÌNH HƯỚNG DẪN CHĂM SÓC HẬU PHẪU CHUẨN Y KHOA:**\n\n";
    $reply .= "1. **Sau cấy ghép Implant & Tiểu phẫu răng khôn Piezotome:**\n";
    $reply .= "   - Cắn gạc cố định tại vết thương trong 45 – 60 phút.\n";
    $reply .= "   - Chườm lạnh bên ngoài má 15 phút/lần trong 24 giờ đầu để giảm sưng; từ ngày thứ 2 chườm ấm.\n";
    $reply .= "   - Tuyệt đối không khạc nhổ mạnh, không dùng ống hút, không súc miệng bằng nước muối tự pha trong 24h đầu.\n";
    $reply .= "   - Uống thuốc đúng liều theo đơn Bác sĩ đã kê; ăn thức ăn mềm, nguội.\n\n";
    $reply .= "2. **Sau bọc răng sứ & dán sứ Veneer:**\n";
    $reply .= "   - Kiêng cắn vật cứng (đá lạnh, xương, nắp chai) trong tuần đầu.\n";
    $reply .= "   - Dùng chỉ nha khoa và tăm nước vệ sinh kẽ răng hàng ngày.\n";
    $reply .= "   - Tái khám định kỳ 6 tháng/lần để kiểm tra khớp cắn và bảo dưỡng bề mặt sứ.\n\n";
    $reply .= "📞 *Hotline cấp cứu nha khoa 24/7: 0900.123.456*";

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'care_guide'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// F. DANH SÁCH BÁC SĨ & LỊCH TRỰC
if (strpos($lowerMsg, 'bác sĩ') !== false || strpos($normMsg, 'bac si') !== false || strpos($lowerMsg, 'bác sỹ') !== false || strpos($normMsg, 'bac sy') !== false || strpos($lowerMsg, 'lịch trực') !== false || strpos($normMsg, 'lich truc') !== false) {
    $reply = "👨‍⚕️ **DANH SÁCH ĐỘI NGŨ BÁC SĨ CHUYÊN KHOA PREMIER:**\n\n";
    foreach ($doctors_list as $doc) {
        $reply .= "- **" . $doc['full_name'] . "**\n";
        $reply .= "  ↳ Chuyên khoa: *" . $doc['specialty'] . "* | Kinh nghiệm: " . $doc['experience'] . "\n";
    }
    $reply .= "\n⏰ **Thời gian trực:** 08:00 - 20:00 (Thứ 2 - Thứ 7), 08:00 - 17:30 (Chủ Nhật).";

    echo json_encode([
        'success' => true,
        'reply' => $reply,
        'action' => 'doctors_list'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 2. NẾU CÓ GEMINI API KEY: GỌI GEMINI VỚI CONTEXT DỮ LIỆU THỰC TẾ
$apiKey = get_setting('gemini_api_key', getenv('GEMINI_API_KEY') ?: '');
$modelName = get_setting('ai_model', 'gemini-3.6-flash');

if (!empty($apiKey)) {
    $systemContext = "Bạn là Trợ lý AI Quản trị cấp cao (Admin AI Executive Assistant) của Nha Khoa Quốc Tế Premier Dental Clinic.
Người trò chuyện với bạn là Quản trị viên/Bác sĩ trưởng phòng khám: {$adminName}.

DỮ LIỆU THỜI GIAN THỰC TỪ CƠ SỞ DỮ LIỆU PHÒNG KHÁM:
- Ngày hôm nay: " . date('d/m/Y') . "
- Tổng số bệnh nhân: {$total_patients}
- Tổng số lượt lịch hẹn: {$total_appointments}
- Số lịch khám hôm nay: " . count($today_appointments) . " ca
- Số lịch chờ duyệt: " . count($pending_appointments) . " ca
- Danh sách Bác sĩ: " . json_encode(array_map(function($d) { return $d['full_name'] . ' (' . $d['specialty'] . ')'; }, $doctors_list), JSON_UNESCAPED_UNICODE) . "
- Dịch vụ tiêu biểu: Niềng răng mắc cài/Invisalign, Cấy ghép Implant Straumann, Dán sứ Veneer Emax, Nhổ răng Piezotome, Tẩy trắng răng Laser.

YÊU CẦU TRẢ LỜI:
1. Đóng vai trò trợ lý chuyên nghiệp, lịch thiệp, tôn trọng người quản lý.
2. Trả lời chính xác, thông minh, ngắn gọn, có cấu trúc bullet point hoặc bảng nếu cần thiết.
3. Nếu người dùng hỏi về thống kê, quản lý nhân sự, lịch khám, dịch vụ, tài chính hay chăm sóc khách hàng, hãy đưa ra câu trả lời thực tế và gợi ý giải pháp tối ưu.";

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

    $genConfig = [
        'temperature' => 0.6,
        'maxOutputTokens' => 1200
    ];

    $payload = json_encode([
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $systemContext . "\n\nCÂU HỎI TỪ QUẢN TRỊ VIÊN: " . $userMessage]
                ]
            ]
        ],
        'generationConfig' => $genConfig
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $resData = json_decode($response, true);
        $aiText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if (!empty($aiText)) {
            echo json_encode([
                'success' => true,
                'reply' => $aiText,
                'source' => 'gemini-ai'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

// 3. Fallback câu trả lời mặc định nếu không khớp intent cụ thể và không có Gemini
$defaultReply = "Xin chào **{$adminName}**! Tôi là **Trợ lý AI Quản Trị Premier**.\n\n";
$defaultReply .= "Tôi có thể hỗ trợ bạn:\n";
$defaultReply .= "- 📊 Báo cáo thống kê số liệu hôm nay (`📊 Thống kê hôm nay`)\n";
$defaultReply .= "- ⏳ Danh sách lịch hẹn cần duyệt (`⏳ Lịch hẹn chờ duyệt`)\n";
$defaultReply .= "- 🔍 Tra cứu hồ sơ bệnh nhân theo tên hoặc SĐT (`🔍 Tìm bệnh nhân`)\n";
$defaultReply .= "- 👨‍⚕️ Thông tin bác sĩ và lịch trực (`👨‍⚕️ Bác sĩ trực`)\n";
$defaultReply .= "- 📝 Soạn tin nhắn SMS/Zalo nhắc hẹn (`📝 Mẫu tin nhắn nhắc hẹn`)\n";
$defaultReply .= "- 💡 Quy trình và hướng dẫn y khoa hậu phẫu (`💡 Hướng dẫn hậu phẫu`)\n\n";
$defaultReply .= "Bạn cần tra cứu hoặc xử lý nghiệp vụ gì hôm nay?";

echo json_encode([
    'success' => true,
    'reply' => $defaultReply,
    'source' => 'system-assistant'
], JSON_UNESCAPED_UNICODE);
