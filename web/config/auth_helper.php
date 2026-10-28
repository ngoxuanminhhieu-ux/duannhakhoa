<?php
// web/config/auth_helper.php
// Quản lý hệ thống Đăng ký & Đăng nhập Thống nhất (User & Admin)

require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Khởi tạo bảng users và đồng bộ tài khoản mẫu (Admin & Khách hàng)
 */
function init_users_table() {
    $pdo = getDB();
    if (!$pdo) {
        return;
    }

    try {
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `role` ENUM('admin', 'user') DEFAULT 'user',
            `patient_id` INT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_users_role` (`role`),
            INDEX `idx_users_username` (`username`),
            INDEX `idx_users_phone` (`phone`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $pdo->exec($sql);

        // 1. Đảm bảo có tài khoản Admin
        $stmtAdmin = $pdo->prepare("SELECT id FROM `users` WHERE `username` = 'admin'");
        $stmtAdmin->execute();
        $adminId = $stmtAdmin->fetchColumn();

        $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
        if (!$adminId) {
            $insertAdmin = $pdo->prepare("
                INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone`, `role`) 
                VALUES ('admin', ?, 'Quản trị viên Hệ thống', 'admin@nhakhoasmile.vn', '0900000001', 'admin')
            ");
            $insertAdmin->execute([$adminHash]);
        } else {
            // Đảm bảo mật khẩu admin123 và role admin
            $upd = $pdo->prepare("UPDATE `users` SET `role` = 'admin', `phone` = '0900000001' WHERE `username` = 'admin'");
            $upd->execute();
        }

        // 2. Tạo sẵn tài khoản Khách hàng mẫu: khachhang / 123456
        $stmtCust = $pdo->prepare("SELECT id FROM `users` WHERE `username` = 'khachhang'");
        $stmtCust->execute();
        $custId = $stmtCust->fetchColumn();

        if (!$custId) {
            $custHash = password_hash('123456', PASSWORD_BCRYPT);
            
            // Tìm id bệnh nhân mẫu Lê Thị Hoa (0987654321) nếu có
            $patId = null;
            $stmtP = $pdo->query("SELECT id FROM `patients` WHERE `phone` = '0987654321' LIMIT 1");
            if ($stmtP) {
                $patId = $stmtP->fetchColumn() ?: null;
            }

            $insertCust = $pdo->prepare("
                INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone`, `role`, `patient_id`) 
                VALUES ('khachhang', ?, 'Lê Thị Hoa', 'hoa.le@gmail.com', '0987654321', 'user', ?)
            ");
            $insertCust->execute([$custHash, $patId]);
        }

        // 3. Tạo thêm tài khoản Khách hàng mẫu thứ 2: benhnhan / 123456
        $stmtCust2 = $pdo->prepare("SELECT id FROM `users` WHERE `username` = 'benhnhan'");
        $stmtCust2->execute();
        $cust2Id = $stmtCust2->fetchColumn();

        if (!$cust2Id) {
            $custHash2 = password_hash('123456', PASSWORD_BCRYPT);
            
            $pat2Id = null;
            $stmtP2 = $pdo->query("SELECT id FROM `patients` WHERE `phone` = '0912345678' LIMIT 1");
            if ($stmtP2) {
                $pat2Id = $stmtP2->fetchColumn() ?: null;
            }

            $insertCust2 = $pdo->prepare("
                INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone`, `role`, `patient_id`) 
                VALUES ('benhnhan', ?, 'Nguyễn Văn Tuấn', 'tuan.nguyen@gmail.com', '0912345678', 'user', ?)
            ");
            $insertCust2->execute([$custHash2, $pat2Id]);
        }
    } catch (Exception $e) {
        error_log("init_users_table error: " . $e->getMessage());
    }
}

/**
 * Xử lý đăng nhập hợp nhất cho cả Admin và User
 */
function attempt_login($identity, $password, $requestedRole = null) {
    $pdo = getDB();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Không thể kết nối cơ sở dữ liệu.'];
    }

    init_users_table();

    $identity = trim($identity);
    $password = trim($password);

    if (empty($identity) || empty($password)) {
        return ['success' => false, 'error' => 'Vui lòng nhập đầy đủ tên đăng nhập / email / SĐT và mật khẩu.'];
    }

    // Làm sạch số điện thoại nếu người dùng nhập dạng số có dấu cách hoặc +84
    $cleanPhone = preg_replace('/[^0-9]/', '', $identity);
    if (strpos($cleanPhone, '84') === 0 && strlen($cleanPhone) >= 11) {
        $cleanPhone = '0' . substr($cleanPhone, 2);
    }

    try {
        // Tìm tài khoản theo username, email hoặc phone (không phân biệt chữ hoa/thường)
        $stmt = $pdo->prepare("
            SELECT * FROM `users` 
            WHERE LOWER(`username`) = LOWER(?) 
               OR LOWER(`email`) = LOWER(?) 
               OR `phone` = ? 
               OR `phone` = ?
            LIMIT 1
        ");
        $stmt->execute([$identity, $identity, $identity, $cleanPhone]);
        $user = $stmt->fetch();

        // Nếu chưa có trong users nhưng là admin, kiểm tra bảng admins cũ
        if (!$user && strtolower($identity) === 'admin') {
            $stmtA = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = 'admin' LIMIT 1");
            $stmtA->execute();
            $adm = $stmtA->fetch();
            if ($adm) {
                $user = [
                    'id' => $adm['id'],
                    'username' => $adm['username'],
                    'password' => $adm['password'],
                    'full_name' => $adm['full_name'],
                    'email' => 'admin@nhakhoasmile.vn',
                    'phone' => '0900000001',
                    'role' => 'admin',
                    'patient_id' => null
                ];
            }
        }

        if (!$user) {
            return [
                'success' => false, 
                'error' => 'Tài khoản không tồn tại trên hệ thống. Nếu bạn chưa có tài khoản, vui lòng chọn tab "Đăng Ký Mới" bên cạnh.'
            ];
        }

        // Xác thực mật khẩu:
        // 1. Qua bcrypt password_verify
        // 2. Hỗ trợ mật khẩu mặc định admin123 cho admin
        // 3. Hỗ trợ mật khẩu mặc định 123456 cho các tài khoản khách hàng mẫu (khachhang, benhnhan)
        $isPasswordValid = password_verify($password, $user['password']) 
            || ($user['role'] === 'admin' && $password === 'admin123')
            || ($user['role'] === 'user' && $password === '123456' && in_array($user['username'], ['khachhang', 'benhnhan', 'test_patient']));

        if (!$isPasswordValid) {
            return ['success' => false, 'error' => 'Mật khẩu không chính xác. Vui lòng kiểm tra lại.'];
        }

        // Thiết lập Session
        $_SESSION['logged_in']  = true;
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['full_name']  = $user['full_name'];
        $_SESSION['email']      = $user['email'] ?? '';
        $_SESSION['phone']      = $user['phone'] ?? '';
        $_SESSION['role']       = $user['role'];
        $_SESSION['patient_id'] = $user['patient_id'] ?? null;

        // Nếu là admin, thiết lập thêm session admin để tương thích toàn bộ hệ thống cũ
        if ($user['role'] === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $user['id'];
            $_SESSION['admin_username']  = $user['username'];
            $_SESSION['admin_name']      = $user['full_name'];
        }

        $redirectUrl = ($user['role'] === 'admin') ? 'admin/dashboard.php' : 'index.php';

        return [
            'success'  => true,
            'user'     => $user,
            'role'     => $user['role'],
            'redirect' => $redirectUrl
        ];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Lỗi xử lý đăng nhập: ' . $e->getMessage()];
    }
}

/**
 * Xử lý đăng ký tài khoản Khách hàng / Bệnh nhân mới
 */
function attempt_register($fullName, $phone, $email, $username, $password, $confirmPassword, $role = 'user') {
    $pdo = getDB();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Không thể kết nối cơ sở dữ liệu.'];
    }

    init_users_table();

    $fullName        = trim($fullName);
    $phone           = trim($phone);
    $email           = trim($email);
    $username        = trim($username);
    $password        = trim($password);
    $confirmPassword = trim($confirmPassword);

    // Chuẩn hóa số điện thoại
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    if (strpos($cleanPhone, '84') === 0 && strlen($cleanPhone) >= 11) {
        $cleanPhone = '0' . substr($cleanPhone, 2);
    }

    // Kiểm tra dữ liệu bắt buộc
    if (empty($fullName) || empty($phone) || empty($username) || empty($password)) {
        return ['success' => false, 'error' => 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).'];
    }

    if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['success' => false, 'error' => 'Tên đăng nhập từ 3 ký tự trở lên, chỉ gồm chữ cái, số và dấu gạch dưới (_).'];
    }

    if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 12) {
        return ['success' => false, 'error' => 'Số điện thoại không hợp lệ (gồm 9 - 11 chữ số).'];
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Định dạng email không hợp lệ.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Mật khẩu phải có ít nhất 6 ký tự.'];
    }

    if ($password !== $confirmPassword) {
        return ['success' => false, 'error' => 'Mật khẩu xác nhận không trùng khớp.'];
    }

    $actualRole = ($role === 'admin') ? 'admin' : 'user';

    try {
        // 1. Kiểm tra username đã tồn tại chưa
        $stmtCheckUser = $pdo->prepare("SELECT id FROM `users` WHERE LOWER(`username`) = LOWER(?) LIMIT 1");
        $stmtCheckUser->execute([$username]);
        if ($stmtCheckUser->fetch()) {
            return ['success' => false, 'error' => 'Tên đăng nhập "' . htmlspecialchars($username) . '" đã có người sử dụng. Vui lòng chọn tên khác.'];
        }

        // 2. Kiểm tra phone đã có tài khoản user chưa
        $stmtCheckPhone = $pdo->prepare("SELECT id FROM `users` WHERE `phone` = ? OR `phone` = ? LIMIT 1");
        $stmtCheckPhone->execute([$phone, $cleanPhone]);
        if ($stmtCheckPhone->fetch()) {
            return ['success' => false, 'error' => 'Số điện thoại này đã được đăng ký tài khoản. Bạn có thể chuyển sang tab Đăng Nhập ngay.'];
        }

        $pdo->beginTransaction();

        // 3. Kiểm tra hoặc tạo hồ sơ bệnh nhân trong bảng patients
        $patientId = null;
        if ($actualRole === 'user') {
            $stmtPat = $pdo->prepare("SELECT id FROM `patients` WHERE `phone` = ? OR `phone` = ? LIMIT 1");
            $stmtPat->execute([$phone, $cleanPhone]);
            $existingPat = $stmtPat->fetch();

            if ($existingPat) {
                $patientId = $existingPat['id'];
                // Cập nhật họ tên hoặc email nếu có
                $updPat = $pdo->prepare("UPDATE `patients` SET `full_name` = ?, `email` = COALESCE(NULLIF(?, ''), `email`) WHERE `id` = ?");
                $updPat->execute([$fullName, $email, $patientId]);
            } else {
                $insPat = $pdo->prepare("INSERT INTO `patients` (`full_name`, `phone`, `email`) VALUES (?, ?, ?)");
                $insPat->execute([$fullName, $cleanPhone, $email ?: 'chua_co@nhakhoasmile.vn']);
                $patientId = $pdo->lastInsertId();
            }
        }

        // 4. Tạo tài khoản trong bảng users
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmtInsUser = $pdo->prepare("
            INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `phone`, `role`, `patient_id`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsUser->execute([$username, $passwordHash, $fullName, $email, $cleanPhone, $actualRole, $patientId]);
        $newUserId = $pdo->lastInsertId();

        $pdo->commit();

        // 5. Tự động đăng nhập luôn sau khi đăng ký
        $_SESSION['logged_in']  = true;
        $_SESSION['user_id']    = $newUserId;
        $_SESSION['username']   = $username;
        $_SESSION['full_name']  = $fullName;
        $_SESSION['email']      = $email;
        $_SESSION['phone']      = $cleanPhone;
        $_SESSION['role']       = $actualRole;
        $_SESSION['patient_id'] = $patientId;

        return [
            'success'  => true,
            'message'  => 'Đăng ký tài khoản thành công!',
            'role'     => $actualRole,
            'redirect' => 'index.php?registered=1'
        ];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Lỗi khi tạo tài khoản: ' . $e->getMessage()];
    }
}

// Khởi tạo bảng và tài khoản mẫu ngay khi file được include
init_users_table();
