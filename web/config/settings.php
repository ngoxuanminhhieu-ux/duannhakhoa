<?php
// web/config/settings.php
// Quản lý cấu hình hệ thống & AI Chatbot lưu trữ trong Database (hoặc fallback qua .env)

require_once __DIR__ . '/database.php';

/**
 * Đảm bảo bảng system_settings tồn tại và có dữ liệu mặc định
 */
function init_settings_table() {
    $pdo = getDB();
    if (!$pdo) {
        return;
    }

    try {
        $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
            `setting_key` VARCHAR(100) PRIMARY KEY,
            `setting_value` TEXT,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sql);

        // Kiểm tra xem đã có dữ liệu chưa
        $stmt = $pdo->query("SELECT COUNT(*) FROM `system_settings`");
        $count = (int)$stmt->fetchColumn();

        if ($count === 0) {
            $defaultKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? '');
            $defaultPrompt = "Bạn là Trợ lý AI Tư vấn Chuyên nghiệp của Nha Khoa Quốc Tế Premier Dental Clinic.

THÔNG TIN PHÒNG KHÁM TỪ CƠ SỞ DỮ LIỆU:
- Địa chỉ: 123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh
- Hotline CSKH & Đặt lịch: 1900 6868 (Miễn phí cước gọi)
- Hotline Cấp cứu 24/7: 0900.123.456
- Email: contact@premierdental.vn
- Thời gian làm việc:
  + Thứ 2 - Thứ 7: 08:00 - 20:00 (làm việc thông trưa)
  + Chủ Nhật: 08:00 - 17:30

CHÍNH SÁCH ƯU ĐÃI & BẢO HÀNH:
- Miễn phí 100% khám lâm sàng và chụp phim X-quang 3D toàn cảnh khi đặt hẹn online.
- Hỗ trợ trả góp 0% lãi suất cho Niềng răng & Cấy ghép Implant (trả trước từ 30%).
- Bảo hành Trụ Implant trọn đời chính hãng (Straumann, Osstem).
- Bảo hành Bọc răng sứ từ 10 - 15 năm (Cercon HT, Emax, Lava).
- Bảo hành Trám răng thẩm mỹ Composite 2 năm.

DANH MỤC DỊCH VỤ NIÊM YẾT:
1. DỊCH VỤ TỔNG QUÁT: Khám răng (Miễn phí), Cạo vôi răng siêu âm (250.000đ).
2. NHA KHOA ĐIỀU TRỊ: Trám răng Composite (350.000đ), Nhổ răng Piezotome (500.000đ), Lấy tủy răng (800.000đ).
3. NHA KHOA THẨM MỸ: Tẩy trắng răng Laser (1.500.000đ), Bọc răng sứ Cercon/Zirconia (2.000.000đ), Dán sứ Veneer Emax (4.000.000đ).
4. CHỈNH NHA & CẤY GHÉP: Niềng răng mắc cài/Invisalign (từ 20.000.000đ), Cấy ghép Implant Thụy Sĩ/Hàn Quốc (từ 12.000.000đ).

ĐỘI NGŨ BÁC SĨ CHUYÊN KHOA:
- ThS. BS. Nguyễn Văn An: Răng Hàm Mặt Tổng Quát (12 năm kinh nghiệm).
- BS. CKI. Trần Thị Mai: Chuyên gia Niềng răng & Invisalign (8 năm kinh nghiệm).
- ThS. BS. Lê Hoàng Nam: Chuyên gia Cấy ghép Implant Nha khoa (15 năm kinh nghiệm).
- BS. Phạm Thùy Linh: Chuyên gia Răng sứ & Dán sứ Veneer (7 năm kinh nghiệm).
- BS. CKI. Đỗ Minh Đức: Chuyên gia Nhổ răng khôn Piezotome & Tiểu phẫu (10 năm kinh nghiệm).

QUY TẮC BẮT BUỘC:
- Khi khách hỏi địa chỉ, trả lời chính xác: 123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP.HCM kèm hotline 1900 6868. Tuyệt đối không dùng placeholder [Số nhà, Tên đường].
- Tư vấn tận tâm, chu đáo, chuẩn y khoa và hướng dẫn đặt lịch hẹn.";

            $defaults = [
                'ai_enabled'          => '1',
                'gemini_api_key'      => $defaultKey,
                'gemini_model'        => 'gemini-3.6-flash',
                'gemini_temperature'  => '0.7',
                'gemini_max_tokens'   => '2048',
                'ai_system_prompt'    => $defaultPrompt
            ];

            $insertStmt = $pdo->prepare("INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES (?, ?)");
            foreach ($defaults as $k => $v) {
                $insertStmt->execute([$k, $v]);
            }
        }
    } catch (Exception $e) {
        // Soft fail
        error_log("init_settings_table error: " . $e->getMessage());
    }
}

/**
 * Lấy giá trị cài đặt theo key
 */
function get_setting($key, $default = null) {
    $pdo = getDB();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT `setting_value` FROM `system_settings` WHERE `setting_key` = ? LIMIT 1");
            $stmt->execute([$key]);
            $val = $stmt->fetchColumn();
            if ($val !== false && $val !== null) {
                // Tự động nâng cấp model đã bị Google khai tử sang gemini-3.6-flash
                if ($key === 'gemini_model' && in_array($val, ['gemini-2.5-flash', 'gemini-2.0-flash', 'gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.5-flash-lite'])) {
                    $val = 'gemini-3.6-flash';
                    set_setting('gemini_model', 'gemini-3.6-flash');
                }
                return $val;
            }
        } catch (Exception $e) {
            // Nếu bảng chưa có, thử init
            init_settings_table();
        }
    }

    // Fallback qua env nếu là key
    if ($key === 'gemini_api_key') {
        $envKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? '');
        if (!empty($envKey)) return $envKey;
    }

    if ($key === 'gemini_model') {
        return $default ?: 'gemini-3.6-flash';
    }

    return $default;
}

/**
 * Lưu một giá trị cài đặt
 */
function set_setting($key, $value) {
    $pdo = getDB();
    if (!$pdo) {
        return false;
    }

    try {
        init_settings_table();
        $stmt = $pdo->prepare("
            INSERT INTO `system_settings` (`setting_key`, `setting_value`) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)
        ");
        return $stmt->execute([$key, (string)$value]);
    } catch (Exception $e) {
        error_log("set_setting error: " . $e->getMessage());
        return false;
    }
}

/**
 * Lấy tất cả cài đặt dưới dạng mảng key => value
 */
function get_all_settings() {
    init_settings_table();
    $pdo = getDB();
    $settings = [
        'ai_enabled'         => '1',
        'gemini_api_key'     => getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ''),
        'gemini_model'       => 'gemini-3.6-flash',
        'gemini_temperature' => '0.7',
        'gemini_max_tokens'  => '1024',
        'ai_system_prompt'   => ''
    ];

    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT `setting_key`, `setting_value` FROM `system_settings`");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            if ($rows) {
                $settings = array_merge($settings, $rows);
            }
        } catch (Exception $e) {
            // Soft fail
        }
    }

    return $settings;
}

// Khởi tạo bảng ngay khi require nếu có kết nối DB
init_settings_table();
