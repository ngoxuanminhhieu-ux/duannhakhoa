<?php
// web/data/doctors_detail_data.php
/**
 * Dữ liệu hồ sơ chi tiết Đội Ngũ Bác Sĩ Chuyên Khoa - Nha Khoa Quốc Tế Premier
 */

function get_all_doctors_details() {
    return [
        1 => [
            'id' => 1,
            'full_name' => 'ThS. BS. Nguyễn Văn An',
            'title' => 'Trưởng Khoa Cấy Ghép Implant & Phục Hình',
            'specialty' => 'Implant & Phục Hình Răng Sứ',
            'experience' => '15+ năm kinh nghiệm',
            'degree' => 'Thạc sĩ Răng Hàm Mặt - Tu nghiệp Chuyên sâu Hoa Kỳ & Thụy Sĩ',
            'image' => 'assets/images/dr-nguyen-an.jpg',
            'cases_count' => '3.500+ ca cấy ghép Implant thành công',
            'rating' => '4.9 / 5.0 (620+ đánh giá)',
            'schedule' => 'Thứ 2 - Thứ 6 (08:00 - 17:00)',
            'branch' => 'Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5 & CS2: 45 Lê Duẩn, Q.1, TP.HCM',
            'description' => 'ThS. BS Nguyễn Văn An là một trong những chuyên gia hàng đầu về phẫu thuật cấy ghép Implant tại Việt Nam. Bác sĩ đã tu nghiệp chuyên sâu tại Đại học Loma Linda (Hoa Kỳ) và Viện Straumann (Thụy Sĩ), tiên phong ứng dụng công nghệ định vị 3D Navigation và kỹ thuật All-on-4 / All-on-6 giúp phục hồi toàn hàm không đau, tích hợp xương tức thì.',
            'strengths' => [
                'Cấy ghép Implant tức thì chịu lực sau khi nhổ răng chỉ 15 phút',
                'Phục hình toàn diện All-on-4 / All-on-6 cho người mất răng lâu năm, tiêu xương nặng',
                'Kỹ thuật nâng xoang kín, ghép xương nhân tạo vi phẫu chuẩn vô trùng tuyệt đối',
                'Lập kế hoạch phẫu thuật kỹ thuật số với máng định vị 3D Guided Surgery'
            ],
            'certifications' => [
                'Chứng chỉ Chuyên gia Implant Quốc tế ICOI (Hoa Kỳ)',
                'Thành viên thường trực Hiệp hội Cấy ghép Nha khoa ITI Thụy Sĩ',
                'Bằng Thạc sĩ Chuyên ngành Răng Hàm Mặt - ĐH Y Dược TP.HCM',
                'Chứng nhận Master Clinician in Implant Dentistry (Loma Linda University)'
            ]
        ],
        2 => [
            'id' => 2,
            'full_name' => 'BS. CKI. Trần Thị Mai',
            'title' => 'Chuyên Gia Chỉnh Nha & Niềng Răng Invisalign',
            'specialty' => 'Niềng Răng Mắc Cài & Invisalign Trong Suốt',
            'experience' => '12+ năm kinh nghiệm',
            'degree' => 'Bác sĩ CKI Chỉnh Nha - Chứng chỉ Platinum Invisalign Toàn Cầu',
            'image' => 'assets/images/dr-tran-mai.jpg',
            'cases_count' => '2.800+ ca chỉnh nha nụ cười hoàn mỹ',
            'rating' => '5.0 / 5.0 (780+ đánh giá)',
            'schedule' => 'Thứ 3 - Chủ Nhật (08:30 - 18:30)',
            'branch' => 'Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5, TP.HCM & CS3: 88 Cầu Giấy, Hà Nội',
            'description' => 'Bác sĩ CKI Trần Thị Mai đã hoàn thành khóa tu nghiệp chỉnh nha cao cấp tại ĐH Paris Descartes (Pháp) và được tập đoàn Align Technology (Hoa Kỳ) trao chứng nhận Bác sĩ Hạng Platinum Invisalign. Bác sĩ nổi tiếng với triết lý chỉnh nha bảo tồn tối đa răng thật, thiết kế đường cười chuẩn tỷ lệ vàng và hạn chế nhổ răng.',
            'strengths' => [
                'Niềng răng máng trong suốt Invisalign Hoa Kỳ thẩm mỹ, tháo lắp linh hoạt',
                'Chỉnh nha mắc cài tự buộc Damon Clear rút ngắn thời gian điều trị 4 - 6 tháng',
                'Điều trị triệt để các ca lệch lạc nặng: hô móm xương, cắn hở, cắn sâu phức tạp',
                'Lên phác đồ ClinCheck 3D chuẩn xác, xem trước kết quả dịch chuyển răng'
            ],
            'certifications' => [
                'Chứng chỉ Bác sĩ Platinum Invisalign Toàn cầu (Align Technology USA)',
                'Bằng Chuyên Khoa Cấp I Răng Hàm Mặt - ĐH Y Hà Nội',
                'Chứng chỉ Chỉnh nha Sinh học ĐH Paris Descartes (Cộng hòa Pháp)',
                'Hội viên chính thức Hội Nắn chỉnh Răng Việt Nam (VAO)'
            ]
        ],
        3 => [
            'id' => 3,
            'full_name' => 'ThS. BS. Lê Hoàng Nam',
            'title' => 'Chuyên Gia Thẩm Mỹ Răng Sứ & Smile Design',
            'specialty' => 'Răng Sứ Thẩm Mỹ & Dán Sứ Veneer Không Mài',
            'experience' => '10+ năm kinh nghiệm',
            'degree' => 'Bác sĩ Chuyên Khoa Răng Hàm Mặt - Tu nghiệp Smile Design Đức',
            'image' => 'assets/images/dr-le-hoang-nam.jpg',
            'cases_count' => '3.200+ nụ cười rạng rỡ',
            'rating' => '4.9 / 5.0 (540+ đánh giá)',
            'schedule' => 'Thứ 2 - Thứ 7 (08:00 - 19:00)',
            'branch' => 'Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5 & CS2: 45 Lê Duẩn, Q.1, TP.HCM',
            'description' => 'BS. Lê Hoàng Nam là chuyên gia thẩm mỹ răng sứ nổi tiếng, tu nghiệp tại Munich (CHLB Đức). Bác sĩ là một trong những người tiên phong ứng dụng công nghệ Dán sứ Veneer siêu mỏng không mài nhỏ mô răng thật, kết hợp phân tích nụ cười nhân tướng học và đường cười tự nhiên, mang lại sự tinh tế và tự tin tuyệt đối cho khách hàng.',
            'strengths' => [
                'Dán sứ Veneer siêu mỏng 0.2 - 0.5mm bảo tồn 100% tủy và mô răng thật',
                'Thiết kế nụ cười kỹ thuật số Digital Smile Design (DSD) chuẩn phong thủy',
                'Phục hình toàn sứ nguyên khối Cercon HT, Lava Esthetic, Emax Press siêu bền',
                'Tái tạo khớp cắn sinh học chống ê buốt, đảm bảo chức năng ăn nhai vững chắc'
            ],
            'certifications' => [
                'Chứng chỉ Chuyên gia Dán sứ Veneer Thẩm mỹ Châu Âu (Dentsply Sirona)',
                'Chứng nhận Master Digital Smile Design (Munich, CHLB Đức)',
                'Bằng Bác sĩ Răng Hàm Mặt - ĐH Y Dược TP.HCM',
                'Thành viên Hội Phục hình Nha khoa Thẩm mỹ Quốc tế (AACD)'
            ]
        ],
        4 => [
            'id' => 4,
            'full_name' => 'BS. Phạm Thùy Linh',
            'title' => 'Chuyên Gia Nha Khoa Thẩm Mỹ & Phục Hình Sứ',
            'specialty' => 'Nha Khoa Thẩm Mỹ & Phục Hình Sứ',
            'experience' => '7+ năm kinh nghiệm',
            'degree' => 'Bác sĩ Chuyên Khoa Răng Hàm Mặt - Tu nghiệp Thẩm mỹ Nhật Bản',
            'image' => 'assets/images/dr-pham-thuy-linh.jpg',
            'cases_count' => '2.100+ ca thẩm mỹ nụ cười',
            'rating' => '4.9 / 5.0 (480+ đánh giá)',
            'schedule' => 'Thứ 2 - Thứ 7 (08:30 - 18:00)',
            'branch' => 'Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5, TP.HCM',
            'description' => 'Bác sĩ Phạm Thùy Linh chuyên sâu về thiết kế nụ cười Smile Design, dán sứ Veneer và bọc răng sứ thẩm mỹ bảo tồn răng thật tối đa. Luôn lắng nghe, thấu hiểu tâm lý và sở thích form răng của từng khách hàng.',
            'strengths' => [
                'Thiết kế nụ cười tinh tế, hài hòa với đường nét khuôn mặt Á Đông',
                'Phục hình răng sứ thẩm mỹ không thâm viền nướu, màu sắc trong bóng tự nhiên',
                'Điều trị tủy răng và bảo tồn răng thật tổn thương phức tạp',
                'Tẩy trắng răng công nghệ Laser Whitening bật tone an toàn tuyệt đối'
            ],
            'certifications' => [
                'Chứng chỉ Phục hình Thẩm mỹ ĐH Y Dược TP.HCM',
                'Chứng nhận Smile Design & Veneer Artistry (Tokyo, Nhật Bản)',
                'Hội viên Hội Răng Hàm Mặt Việt Nam (VDA)'
            ]
        ],
        5 => [
            'id' => 5,
            'full_name' => 'BS. CKI. Đỗ Minh Đức',
            'title' => 'Chuyên Gia Tiểu Phẫu & Nhổ Răng Khôn Siêu Âm',
            'specialty' => 'Phẫu Thuật Tiểu Phẫu & Nhổ Răng Khôn',
            'experience' => '10+ năm kinh nghiệm',
            'degree' => 'Bác sĩ CKI Răng Hàm Mặt - Tu nghiệp Vi phẫu Thụy Sĩ',
            'image' => 'assets/images/dr-do-minh-duc.jpg',
            'cases_count' => '5.200+ ca nhổ răng an toàn',
            'rating' => '5.0 / 5.0 (910+ đánh giá không đau)',
            'schedule' => 'Tất cả các ngày trong tuần (08:00 - 20:00)',
            'branch' => 'Hệ thống 3 cơ sở (TP.HCM & Hà Nội)',
            'description' => 'Bác sĩ CKI Đỗ Minh Đức là chuyên gia hàng đầu về nhổ răng khôn bằng máy Piezotome không đau, an toàn tuyệt đối. Chuyên sâu xử lý các ca răng lệch 90 độ, mọc ngầm phức tạp sát dây thần kinh, kết hợp màng huyết tương PRF giúp lành thương nhanh chóng.',
            'strengths' => [
                'Nhổ răng khôn mọc lệch, mọc ngầm bằng công nghệ siêu âm Piezotome êm ái',
                'Ứng dụng màng huyết tương PRF giảm sưng đau, lành thương siêu tốc sau 24h',
                'Phẫu thuật cắt chóp răng và loại bỏ nang xương hàm an toàn tuyệt đối',
                'Kỹ thuật gây tê vi điểm không đau chuẩn tiêu chuẩn Châu Âu'
            ],
            'certifications' => [
                'Bằng Chuyên Khoa Cấp I Răng Hàm Mặt - ĐH Y Dược TP.HCM',
                'Chứng chỉ Phẫu thuật Siêu âm Piezotome (Thụy Sĩ)',
                'Thành viên Hội Phẫu thuật Miệng và Hàm Mặt Việt Nam (VSSO)'
            ]
        ]
    ];
}

function get_doctors_for_view() {
    global $pdo;
    $doctors = [];
    if (!empty($pdo)) {
        try {
            $stmt = $pdo->query("SELECT * FROM doctors ORDER BY id ASC");
            $db_doctors = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (!empty($db_doctors)) {
                $details_map = get_all_doctors_details();
                foreach ($db_doctors as $doc) {
                    $id = (int)$doc['id'];
                    $detail = $details_map[$id] ?? null;
                    $merged = $doc;
                    $merged['full_name'] = $doc['full_name'] ?? ($detail['full_name'] ?? '');
                    $merged['specialty'] = $doc['specialty'] ?? ($detail['specialty'] ?? '');
                    $merged['experience'] = $doc['experience'] ?? ($detail['experience'] ?? '');
                    $merged['description'] = !empty($doc['description']) ? $doc['description'] : ($detail['description'] ?? '');
                    $merged['image'] = !empty($doc['image']) ? $doc['image'] : ($detail['image'] ?? 'assets/images/dr-nguyen-an.jpg');
                    $merged['title'] = $detail['title'] ?? 'Bác Sĩ Chuyên Khoa Răng Hàm Mặt';
                    $merged['degree'] = $detail['degree'] ?? 'Đại học Y Dược chính quy';
                    $merged['cases_count'] = $detail['cases_count'] ?? '2.500+ ca thành công';
                    $merged['rating'] = $detail['rating'] ?? '4.9 / 5.0 (Rất hài lòng)';
                    $merged['schedule'] = $detail['schedule'] ?? 'Thứ 2 - Thứ 7 (08:00 - 18:00)';
                    $merged['branch'] = $detail['branch'] ?? 'Hệ thống Nha Khoa Premier';
                    $merged['strengths'] = $detail['strengths'] ?? [
                        'Khám & chẩn đoán hình ảnh kỹ thuật số 3D',
                        'Điều trị chuẩn y khoa, bảo tồn răng thật tối đa',
                        'Quy trình vô trùng tuyệt đối tiêu chuẩn Châu Âu'
                    ];
                    $merged['certifications'] = $detail['certifications'] ?? [
                        'Chứng chỉ hành nghề khám chữa bệnh Răng Hàm Mặt - Bộ Y Tế',
                        'Chứng nhận đào tạo liên tục chuyên ngành RHM'
                    ];
                    $doctors[] = $merged;
                }
            }
        } catch (\Throwable $e) {
            $doctors = [];
        }
    }
    
    // Fallback if database is empty or not connected
    if (empty($doctors)) {
        $doctors = array_values(get_all_doctors_details());
    }
    return $doctors;
}
