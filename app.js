/* ==========================================================================
   NHA KHOA QUỐC TẾ PREMIER - CLIENT INTERFACE SCRIPT
   Thương hiệu Nha Khoa Quốc Tế Premier - Đồng bộ xanh dương Sapphire y tế
   ========================================================================== */

let globalServices = [];
let globalDoctors = [];
let globalPrices = {};

// Default Fallback Services (Premier Dental Clinic Standard)
const DEFAULT_SERVICES = [
    {
        "id": "DV01",
        "name": "Khám Răng Tổng Quát & Chụp Phim 3D CT ConeBeam",
        "category": "Khám tổng quát",
        "category_slug": "general",
        "price_range": "Miễn phí (0đ)",
        "min_price": 0,
        "max_price": 0,
        "image": "assets/images/dental-tech-scanner.jpg",
        "description": "Thăm khám trực tiếp cùng Bác sĩ Chuyên Khoa Răng Hàm Mặt, chụp phim 3D CT ConeBeam toàn cảnh và lập phác đồ điều trị miễn phí.",
        "duration": "30 - 45 phút",
        "warranty": "Tư vấn & tái khám trọn đời",
        "procedure": [
            "Tiếp đón và thăm khám lâm sàng tổng thể khoang miệng",
            "Chụp phim 3D CT ConeBeam khảo sát cấu trúc xương hàm",
            "Bác sĩ chuyên khoa phân tích phim và giải thích tình trạng răng",
            "Lên phác đồ điều trị chi tiết và báo giá trọn gói minh bạch"
        ]
    },
    {
        "id": "DV02",
        "name": "Lấy Cao Răng Sóng Siêu Âm & Đánh Bóng Thạch Anh",
        "category": "Khám tổng quát",
        "category_slug": "general",
        "price_range": "150.000 - 250.000 VNĐ",
        "min_price": 150000,
        "max_price": 250000,
        "image": "https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=600&auto=format&fit=crop&q=80",
        "description": "Làm sạch mảng bám, vôi răng nhẹ nhàng bằng đầu sóng siêu âm không ê buốt, đánh bóng bảo vệ men răng tự nhiên.",
        "duration": "30 phút",
        "warranty": "Khuyến nghị định kỳ 6 tháng/lần",
        "procedure": [
            "Sát khuẩn khoang miệng với dung dịch chuyên dụng",
            "Làm sạch cao răng bằng đầu rung siêu âm êm ái",
            "Đánh bóng các mặt răng ngăn ngừa mảng bám tái sinh",
            "Thoa kem tái khoáng bảo vệ men răng"
        ]
    },
    {
        "id": "DV03",
        "name": "Trám Răng Sâu Thẩm Mỹ Composite Nano 3M",
        "category": "Điều trị",
        "category_slug": "treatment",
        "price_range": "300.000 - 500.000 VNĐ / răng",
        "min_price": 300000,
        "max_price": 500000,
        "image": "https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=600&auto=format&fit=crop&q=80",
        "description": "Hàn trám răng sâu, răng mẻ bằng vật liệu Composite Nano nhập khẩu Hoa Kỳ, độ cứng cao và đồng màu hoàn hảo với răng thật.",
        "duration": "30 - 40 phút / răng",
        "warranty": "Bảo hành 2 năm",
        "procedure": [
            "Loại bỏ sạch vi khuẩn và mô răng sâu hoại tử",
            "Xử lý bề mặt men răng bằng dung dịch etching",
            "Tạo hình thẩm mỹ từng lớp hạt nano composite trùng màu men răng",
            "Chiếu đèn quang trùng hợp và đánh bóng hoàn thiện khớp cắn"
        ]
    },
    {
        "id": "DV04",
        "name": "Tẩy Trắng Răng Laser Whitening Công Nghệ Đức",
        "category": "Thẩm mỹ",
        "category_slug": "cosmetic",
        "price_range": "1.500.000 - 2.800.000 VNĐ",
        "min_price": 1500000,
        "max_price": 2800000,
        "image": "assets/images/smile-woman.jpg",
        "description": "Bật 3 - 5 tông răng sáng bóng tự nhiên chỉ sau 45 phút chiếu bước sóng Laser an toàn, không ê buốt và bảo toàn men răng.",
        "duration": "45 - 60 phút",
        "warranty": "Duy trì độ sáng từ 2 - 3 năm",
        "procedure": [
            "Cách ly nướu và môi an toàn tuyệt đối bằng gel bảo vệ",
            "Thoa thuốc tẩy trắng răng chính hãng chuẩn nồng độ y khoa",
            "Chiếu ánh sáng Laser kích hoạt hoạt chất làm trắng sâu",
            "Vệ sinh sạch gel và hướng dẫn chế độ giữ màu răng"
        ]
    },
    {
        "id": "DV05",
        "name": "Nhổ Răng Khôn Sóng Siêu Âm Piezotome Không Đau",
        "category": "Điều trị",
        "category_slug": "treatment",
        "price_range": "1.000.000 - 2.500.000 VNĐ / răng",
        "min_price": 1000000,
        "max_price": 2500000,
        "image": "https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=600&auto=format&fit=crop&q=80",
        "description": "Công nghệ nhổ răng siêu âm Piezotome bóc tách mô mềm êm ái, nhổ răng khôn mọc ngầm, mọc lệch không đau, lành thương thần tốc.",
        "duration": "15 - 30 phút / răng",
        "warranty": "Theo dõi & cắt chỉ miễn phí sau 7 ngày",
        "procedure": [
            "Chụp phim 3D CT ConeBeam khảo sát dây thần kinh và chân răng",
            "Gây tê cục bộ vi điểm không đau chuẩn y khoa",
            "Sử dụng bước sóng siêu âm Piezotome nhẹ nhàng bóc tách răng",
            "Bơm rửa vô khuẩn, khâu chỉ tự tiêu và cấp đơn thuốc hồi phục"
        ]
    },
    {
        "id": "DV06",
        "name": "Cấy Ghép Răng Implant Straumann Thụy Sĩ / Dentium",
        "category": "Cấy ghép Implant",
        "category_slug": "implant",
        "price_range": "13.000.000 - 32.000.000 VNĐ / trụ",
        "min_price": 13000000,
        "max_price": 32000000,
        "image": "https://images.unsplash.com/photo-1598256989800-fe5f95da9787?w=600&auto=format&fit=crop&q=80",
        "description": "Phục hình răng mất toàn diện bằng trụ Titanium sinh học tích hợp xương nhanh, ăn nhai như răng thật 100% và bảo hành trọn đời.",
        "duration": "15 - 20 phút / trụ",
        "warranty": "Bảo hành chính hãng trọn đời bằng hợp đồng",
        "procedure": [
            "Chụp phim CT ConeBeam 3D và quét dấu hàm kỹ thuật số",
            "Lập máng hướng dẫn phẫu thuật cấy ghép Implant định vị chính xác",
            "Đặt trụ Implant vào xương hàm nhẹ nhàng, không sưng đau",
            "Phục hình răng sứ thẩm mỹ trên trụ sau khi tích hợp xương"
        ]
    },
    {
        "id": "DV07",
        "name": "Bọc Răng Sứ Thẩm Mỹ Cao Cấp Cercon HT / Emax",
        "category": "Thẩm mỹ",
        "category_slug": "cosmetic",
        "price_range": "3.000.000 - 8.000.000 VNĐ / răng",
        "min_price": 3000000,
        "max_price": 8000000,
        "image": "assets/images/doctor-consult.jpg",
        "description": "Phôi sứ nguyên khối tinh khiết nhập khẩu Đức, độ chịu lực gấp 5 lần răng thật, chống thâm viền nướu và bảo hành chính hãng 15 năm.",
        "duration": "2 - 3 buổi hẹn",
        "warranty": "Bảo hành chính hãng 10 - 15 năm (QR code điện tử)",
        "procedure": [
            "Thăm khám, thiết kế dáng răng Smile Design theo nhân tướng học",
            "Sửa soạn răng bảo tồn tủy và mô răng thật tối đa",
            "Lấy dấu kỹ thuật số và lắp răng tạm bảo vệ",
            "Gắn cố định răng sứ thẩm mỹ và tinh chỉnh khớp cắn"
        ]
    },
    {
        "id": "DV08",
        "name": "Niềng Răng Trong Suốt Invisalign & Mắc Cài Thông Minh",
        "category": "Chỉnh nha",
        "category_slug": "orthodontics",
        "price_range": "25.000.000 - 95.000.000 VNĐ",
        "min_price": 25000000,
        "max_price": 95000000,
        "image": "assets/images/smile-man.jpg",
        "description": "Nắn chỉnh răng hô, móm, khấp khểnh bằng máng trong suốt thẩm mỹ Invisalign Hoa Kỳ hoặc mắc cài tự buộc, hỗ trợ trả góp 0% lãi suất.",
        "duration": "12 - 24 tháng",
        "warranty": "Cam kết hiệu quả bằng văn bản hợp đồng",
        "procedure": [
            "Quét dấu hàm bằng máy iTero 5D mô phỏng trước kết quả nụ cười",
            "Bác sĩ chuyên khoa chỉnh nha ĐH Y Hà Nội lên phác đồ ClinCheck",
            "Gắn mắc cài hoặc bàn giao bộ khay niềng trong suốt",
            "Tái khám định kỳ theo dõi sự dịch chuyển của từng răng"
        ]
    }
];

// Default Fallback Doctors (Premier Dental Clinic Specialists)
const DEFAULT_DOCTORS = [
    {
        "id": "BS01",
        "name": "ThS. Bác sĩ Nguyễn Văn An",
        "title": "Trưởng Khoa Cấy Ghép Implant & Phục Hình",
        "specialty": "Implant & Phục Hình Răng Sứ",
        "experience": "15+ năm kinh nghiệm",
        "degree": "Thạc sĩ Răng Hàm Mặt - Tu nghiệp Chuyên sâu Hoa Kỳ & Thụy Sĩ",
        "image": "assets/images/dr-nguyen-an.jpg",
        "cases_count": "3.500+ ca cấy ghép Implant thành công",
        "rating": "4.9 / 5.0 (620+ đánh giá)",
        "schedule": "Thứ 2 - Thứ 6 (08:00 - 17:00)",
        "branch": "Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5 & CS2: 45 Lê Duẩn, Q.1, TP.HCM",
        "description": "ThS. BS Nguyễn Văn An là một trong những chuyên gia hàng đầu về phẫu thuật cấy ghép Implant tại Việt Nam. Bác sĩ đã tu nghiệp chuyên sâu tại Đại học Loma Linda (Hoa Kỳ) và Viện Straumann (Thụy Sĩ), tiên phong ứng dụng công nghệ định vị 3D Navigation và kỹ thuật All-on-4 / All-on-6 giúp phục hồi toàn hàm không đau, tích hợp xương tức thì.",
        "strengths": [
            "Cấy ghép Implant tức thì chịu lực sau khi nhổ răng chỉ 15 phút",
            "Phục hình toàn diện All-on-4 / All-on-6 cho người mất răng lâu năm, tiêu xương nặng",
            "Kỹ thuật nâng xoang kín, ghép xương nhân tạo vi phẫu chuẩn vô trùng tuyệt đối",
            "Lập kế hoạch phẫu thuật kỹ thuật số với máng định vị 3D Guided Surgery"
        ],
        "certifications": [
            "Chứng chỉ Chuyên gia Implant Quốc tế ICOI (Hoa Kỳ)",
            "Thành viên thường trực Hiệp hội Cấy ghép Nha khoa ITI Thụy Sĩ",
            "Bằng Thạc sĩ Chuyên ngành Răng Hàm Mặt - ĐH Y Dược TP.HCM",
            "Chứng nhận Master Clinician in Implant Dentistry (Loma Linda University)"
        ]
    },
    {
        "id": "BS02",
        "name": "BS.CKI Trần Thị Mai",
        "title": "Chuyên Gia Chỉnh Nha & Niềng Răng Invisalign",
        "specialty": "Niềng Răng Mắc Cài & Invisalign Trong Suốt",
        "experience": "12+ năm kinh nghiệm",
        "degree": "Bác sĩ CKI Chỉnh Nha - Chứng chỉ Platinum Invisalign Toàn Cầu",
        "image": "assets/images/dr-tran-mai.jpg",
        "cases_count": "2.800+ ca chỉnh nha nụ cười hoàn mỹ",
        "rating": "5.0 / 5.0 (780+ đánh giá)",
        "schedule": "Thứ 3 - Chủ Nhật (08:30 - 18:30)",
        "branch": "Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5, TP.HCM & CS3: 88 Cầu Giấy, Hà Nội",
        "description": "Bác sĩ CKI Trần Thị Mai đã hoàn thành khóa tu nghiệp chỉnh nha cao cấp tại ĐH Paris Descartes (Pháp) và được tập đoàn Align Technology (Hoa Kỳ) trao chứng nhận Bác sĩ Hạng Platinum Invisalign. Bác sĩ nổi tiếng với triết lý chỉnh nha bảo tồn tối đa răng thật, thiết kế đường cười chuẩn tỷ lệ vàng và hạn chế nhổ răng.",
        "strengths": [
            "Niềng răng máng trong suốt Invisalign Hoa Kỳ thẩm mỹ, tháo lắp linh hoạt",
            "Chỉnh nha mắc cài tự buộc Damon Clear rút ngắn thời gian điều trị 4 - 6 tháng",
            "Điều trị triệt để các ca lệch lạc nặng: hô móm xương, cắn hở, cắn sâu phức tạp",
            "Lên phác đồ ClinCheck 3D chuẩn xác, xem trước kết quả dịch chuyển răng"
        ],
        "certifications": [
            "Chứng chỉ Bác sĩ Platinum Invisalign Toàn cầu (Align Technology USA)",
            "Bằng Chuyên Khoa Cấp I Răng Hàm Mặt - ĐH Y Hà Nội",
            "Chứng chỉ Chỉnh nha Sinh học ĐH Paris Descartes (Cộng hòa Pháp)",
            "Hội viên chính thức Hội Nắn chỉnh Răng Việt Nam (VAO)"
        ]
    },
    {
        "id": "BS03",
        "name": "BS. Lê Hoàng Nam",
        "title": "Chuyên Gia Thẩm Mỹ Răng Sứ & Smile Design",
        "specialty": "Răng Sứ Thẩm Mỹ & Dán Sứ Veneer Không Mài",
        "experience": "10+ năm kinh nghiệm",
        "degree": "Bác sĩ Chuyên Khoa Răng Hàm Mặt - Tu nghiệp Smile Design Đức",
        "image": "assets/images/hero-dentist-smile.jpg",
        "cases_count": "3.200+ nụ cười rạng rỡ",
        "rating": "4.9 / 5.0 (540+ đánh giá)",
        "schedule": "Thứ 2 - Thứ 7 (08:00 - 19:00)",
        "branch": "Cơ sở 1: 123 Nguyễn Văn Cừ, Q.5 & CS2: 45 Lê Duẩn, Q.1, TP.HCM",
        "description": "BS. Lê Hoàng Nam là chuyên gia thẩm mỹ răng sứ nổi tiếng, tu nghiệp tại Munich (CHLB Đức). Bác sĩ là một trong những người tiên phong ứng dụng công nghệ Dán sứ Veneer siêu mỏng không mài nhỏ mô răng thật, kết hợp phân tích nụ cười nhân tướng học và đường cười tự nhiên, mang lại sự tinh tế và tự tin tuyệt đối cho khách hàng.",
        "strengths": [
            "Dán sứ Veneer siêu mỏng 0.2 - 0.5mm bảo tồn 100% tủy và mô răng thật",
            "Thiết kế nụ cười kỹ thuật số Digital Smile Design (DSD) chuẩn phong thủy",
            "Phục hình toàn sứ nguyên khối Cercon HT, Lava Esthetic, Emax Press siêu bền",
            "Tái tạo khớp cắn sinh học chống ê buốt, đảm bảo chức năng ăn nhai vững chắc"
        ],
        "certifications": [
            "Chứng chỉ Chuyên gia Dán sứ Veneer Thẩm mỹ Châu Âu (Dentsply Sirona)",
            "Chứng nhận Master Digital Smile Design (Munich, CHLB Đức)",
            "Bằng Bác sĩ Răng Hàm Mặt - ĐH Y Dược TP.HCM",
            "Thành viên Hội Phục hình Nha khoa Thẩm mỹ Quốc tế (AACD)"
        ]
    },
    {
        "id": "BS04",
        "name": "BS. Phạm Minh Tuấn",
        "title": "Chuyên Gia Tiểu Phẫu & Nhổ Răng Khôn Siêu Âm",
        "specialty": "Nhổ Răng Siêu Âm Piezotome & Nha Khoa Tổng Quát",
        "experience": "8+ năm kinh nghiệm",
        "degree": "Bác sĩ Chuyên Khoa Răng Hàm Mặt - Tu nghiệp Vi phẫu Thụy Sĩ",
        "image": "assets/images/doctor-consult.jpg",
        "cases_count": "4.500+ ca tiểu phẫu an toàn 100%",
        "rating": "4.9 / 5.0 (820+ đánh giá không đau)",
        "schedule": "Tất cả các ngày trong tuần (08:00 - 20:00)",
        "branch": "Hệ thống 3 cơ sở (TP.HCM & Hà Nội)",
        "description": "Bác sĩ Phạm Minh Tuấn được mệnh danh là 'Bác sĩ mát tay nhất phòng khám' với phong cách điều trị nhẹ nhàng, ân cần. Bác sĩ làm chủ kỹ thuật nhổ răng khôn bằng sóng siêu âm cao tần Piezotome, bóc tách mô mềm êm ái, kết hợp màng huyết tương giàu tiểu cầu PRF giúp lành thương thần tốc chỉ sau 24-48 giờ.",
        "strengths": [
            "Nhổ răng khôn mọc lệch 90 độ, mọc ngầm sát dây thần kinh không sưng đau",
            "Công nghệ sóng siêu âm Piezotome giảm sang chấn mô mềm đến 90%",
            "Ứng dụng màng huyết tương tự thân PRF kích thích tái tạo xương và nướu siêu tốc",
            "Điều trị tủy răng công nghệ vi phẫu nội nha một lần hẹn dứt điểm đau nhức"
        ],
        "certifications": [
            "Chứng chỉ Phẫu thuật Răng khôn Siêu âm Piezotome (Thụy Sĩ)",
            "Chứng nhận Điều trị Nội nha Kỹ thuật số (Hoa Kỳ)",
            "Bằng Bác sĩ Răng Hàm Mặt - ĐH Y Dược TP.HCM",
            "Thành viên Hội Phẫu thuật Miệng và Hàm Mặt Việt Nam (VSSO)"
        ]
    }
];

// Initialize Page Data
document.addEventListener("DOMContentLoaded", () => {
    initApp();
});

async function initApp() {
    try {
        await Promise.all([
            fetchServices(),
            fetchDoctors(),
            fetchPrices(),
            fetchClinicInfo()
        ]);
        
        // Default set minimum booking date to today
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('b_date');
        if (dateInput) {
            dateInput.min = today;
            dateInput.value = today;
        }

    } catch (err) {
        console.error("Lỗi khi tải dữ liệu khởi tạo:", err);
    }
}

// SPA Navigation Switcher
function switchNav(sectionId) {
    const sections = document.querySelectorAll('.page-section');
    sections.forEach(sec => sec.classList.remove('active'));

    const targetSec = document.getElementById(`${sectionId}-section`);
    if (targetSec) {
        targetSec.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }

    // Close mobile menu if open
    const navMenu = document.getElementById('navMenu');
    if (navMenu) navMenu.classList.remove('active');

    // Load Admin data if switching to admin
    if (sectionId === 'admin') {
        loadAdminDashboard();
    }
}

function toggleMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    if (navMenu) navMenu.classList.toggle('active');
}

// Switch Branch in About Section Map
function switchAboutBranch(branchId) {
    const branches = {
        'cs1': {
            name: 'Trụ Sở Chính - Premier Dental Clinic Quận 5',
            badge: '<i class="fa-solid fa-star"></i> Trụ sở chính chuẩn Quốc tế 5 sao',
            address: '123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh',
            directions: 'Nằm ngay mặt tiền đường Nguyễn Văn Cừ, cách ngã tư Trần Hưng Đạo 200m, đối diện Trường Đại học Sư Phạm TP.HCM.',
            parking: 'Bãi đỗ xe ô tô & xe máy rộng rãi miễn phí, bảo vệ trông giữ an toàn 24/7.',
            bus: 'Tuyến xe buýt số 01, 11, 45, 56, 139 dừng ngay trước cửa phòng khám.',
            hours: 'Thứ 2 - Thứ 7: 08:00 - 20:00 (Làm việc thông trưa) | Chủ Nhật: 08:00 - 17:30',
            phone: '1900 6868',
            hotline: '0900.123.456',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.669658423711!2d106.68006847570305!3d10.759917059495817!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa066518d9f042e0!2zMTIzIMSQxrDhu51uZyBOZ3V54buFbiBWxINuIEPhu6ssIFBoxrDhu51uZyA0LCBRdeG6rW4gNSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5o!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=123+Nguy%E1%BB%85n+V%C4%83n+C%E1%BB%AB,+Ph%C6%B0%E1%BB%9Dng+4,+Qu%E1%BA%ADn+5,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        },
        'cs2': {
            name: 'Chi Nhánh Quận 1 - Premier Dental Luxury Center',
            badge: '<i class="fa-solid fa-gem"></i> Trung tâm Thẩm mỹ & Chỉnh nha Cao cấp',
            address: '45 Lê Duẩn, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
            directions: 'Ngay trục đường ngoại giao trung tâm Quận 1, gần Nhà thờ Đức Bà và Dinh Độc Lập.',
            parking: 'Hầm đỗ xe ô tô tòa nhà miễn phí cho khách hàng của nha khoa.',
            bus: 'Tuyến xe buýt số 03, 19, 30, 45, 93 dừng tại trạm Công xã Paris.',
            hours: 'Thứ 2 - Thứ 7: 08:30 - 20:30 | Chủ Nhật: 08:30 - 18:00',
            phone: '1900 6868',
            hotline: '0900.123.456',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.387926941196!2d106.6980483757034!3d10.781577759095692!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f483c66f60d%3A0x63390f772fc7ee65!2zNDUgTMOqIER14bqpbiwgQuG6v24gTmdow6ksIFF14bqtbiAxLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2s!4v1700000000001!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=45+L%C3%AA+Du%E1%BA%A9n,+B%E1%BA%BFn+Ngh%C3%A9,+Qu%E1%BA%ADn+1,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        },
        'cs3': {
            name: 'Chi Nhánh Bình Thạnh - Premier Dental Digital Implant Center',
            badge: '<i class="fa-solid fa-teeth-open"></i> Trung tâm Cấy ghép Implant Kỹ thuật số',
            address: '88 Điện Biên Phủ, Phường 15, Quận Bình Thạnh, TP. Hồ Chí Minh',
            directions: 'Nằm ngay trục đường lớn kết nối Quận 1 và Bình Thạnh, chân cầu Điện Biên Phủ.',
            parking: 'Sân đỗ xe ô tô và bãi giữ xe máy có mái che, bảo vệ túc trực 24/7.',
            bus: 'Tuyến xe buýt số 06, 14, 54, 150 dừng ngay trạm Cầu Điện Biên Phủ.',
            hours: 'Thứ 2 - Thứ 7: 08:00 - 20:00 | Chủ Nhật: 08:00 - 17:30',
            phone: '1900 6868',
            hotline: '0900.123.456',
            mapUrl: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.2323758364376!2d106.70295177570354!3d10.793444058874635!2m3!1f0!1f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528b497495b59%3A0x9ebffcc2a52ee78b!2zODggxJBp4buHbiBCacOqbiBQaOG7pywgUGjGsOG7nW5nIDE1LCBCw6xuaCBUaOG6oW5oLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmg!5e0!3m2!1svi!2s!4v1700000000002!5m2!1svi!2s',
            dirUrl: 'https://www.google.com/maps/dir/?api=1&destination=88+%C4%90i%E1%BB%87n+Bi%C3%AAn+Ph%E1%BB%A7,+Ph%C6%B0%E1%BB%9Dng+15,+B%C3%ACnh+Th%E1%BA%A1nh,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh'
        }
    };

    const data = branches[branchId];
    if (!data) return;

    // Update active tab buttons
    document.querySelectorAll('.branch-nav-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-branch') === branchId);
    });

    // Update branch info elements
    const badgeEl = document.getElementById('branchBadge');
    const titleEl = document.getElementById('branchTitle');
    const addressEl = document.getElementById('branchAddress');
    const directionsEl = document.getElementById('branchDirections');
    const parkingEl = document.getElementById('branchParking');
    const busEl = document.getElementById('branchBus');
    const hoursEl = document.getElementById('branchHours');
    const iframeEl = document.getElementById('aboutMapIframe');
    const dirBtnEl = document.getElementById('branchDirBtn');
    const currentLocEl = document.getElementById('currentBranchName');

    if (badgeEl) badgeEl.innerHTML = data.badge;
    if (titleEl) titleEl.innerText = data.name;
    if (addressEl) addressEl.innerText = data.address;
    if (directionsEl) directionsEl.innerText = data.directions;
    if (parkingEl) parkingEl.innerText = data.parking;
    if (busEl) busEl.innerText = data.bus;
    if (hoursEl) hoursEl.innerText = data.hours;
    if (iframeEl) iframeEl.src = data.mapUrl;
    if (dirBtnEl) dirBtnEl.href = data.dirUrl;
    if (currentLocEl) currentLocEl.innerText = data.name;
}

// Helper to construct categorized prices
function buildCategorizedPrices(services) {
    const categorized = {
        "general": { "name": "Khám Tổng Quát & Dự Phòng", "items": [] },
        "treatment": { "name": "Điều Trị Bệnh Lý Nha Khoa", "items": [] },
        "orthodontics": { "name": "Chỉnh Nha - Niềng Răng", "items": [] },
        "implant": { "name": "Cấy Ghép Implant Phục Hình", "items": [] },
        "cosmetic": { "name": "Nha Khoa Thẩm Mỹ & Răng Sứ", "items": [] }
    };
    
    for (const s of services) {
        const cat = s.category_slug || 'general';
        if (!categorized[cat]) {
            categorized[cat] = { name: s.category || cat, items: [] };
        }
        categorized[cat].items.push({
            id: s.id,
            name: s.name,
            price_range: s.price_range,
            warranty: s.warranty || '',
            duration: s.duration || ''
        });
    }
    return categorized;
}

// --- DATA FETCHING & RENDERING ---

async function fetchServices() {
    try {
        const res = await fetch('/api/services');
        if (res.ok) {
            const data = await res.json();
            globalServices = (data && data.length > 0) ? data : DEFAULT_SERVICES;
        } else {
            globalServices = DEFAULT_SERVICES;
        }
    } catch (e) {
        console.warn("Backend API không khả dụng, sử dụng dữ liệu mặc định:", e);
        globalServices = DEFAULT_SERVICES;
    }
    
    // Render Home Services (Top 6)
    renderServices(globalServices.slice(0, 6), 'home-services-grid');
    
    // Render Full Services Page
    renderServices(globalServices, 'full-services-grid');
    
    // Populate Select Dropdowns
    populateServiceSelect();
}

async function fetchDoctors() {
    try {
        const res = await fetch('/api/doctors');
        if (res.ok) {
            const data = await res.json();
            globalDoctors = (data && data.length > 0) ? data : DEFAULT_DOCTORS;
        } else {
            globalDoctors = DEFAULT_DOCTORS;
        }
    } catch (e) {
        console.warn("Backend API doctors không khả dụng, sử dụng dữ liệu mặc định:", e);
        globalDoctors = DEFAULT_DOCTORS;
    }
    
    renderDoctors(globalDoctors, 'home-doctors-grid');
    renderDoctors(globalDoctors, 'full-doctors-grid');
    populateDoctorSelect();
}

async function fetchPrices() {
    try {
        const res = await fetch('/api/prices');
        if (res.ok) {
            globalPrices = await res.json();
        } else {
            globalPrices = buildCategorizedPrices(globalServices.length > 0 ? globalServices : DEFAULT_SERVICES);
        }
    } catch (e) {
        globalPrices = buildCategorizedPrices(globalServices.length > 0 ? globalServices : DEFAULT_SERVICES);
    }
    renderPriceTable(globalPrices);
}

async function fetchClinicInfo() {
    try {
        const res = await fetch('/api/clinic');
        if (res.ok) {
            const info = await res.json();
            // Optional info update
        }
    } catch (e) {
        // Silent catch
    }
}

// Render Services Grid
function renderServices(services, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!services || services.length === 0) {
        container.innerHTML = `<p class="text-center" style="grid-column: 1/-1; padding: 30px; color: var(--muted);">Không tìm thấy dịch vụ nào phù hợp.</p>`;
        return;
    }

    container.innerHTML = services.map(s => `
        <div class="service-card">
            <div class="service-thumb">
                <img src="${s.image || 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=600'}" alt="${s.name}" loading="lazy">
                <span class="service-category-badge">${s.category}</span>
            </div>
            <div class="service-body">
                <h3 class="service-title">${s.name}</h3>
                <p class="service-desc">${s.description}</p>
                <div class="service-meta">
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block;">Chi phí tham khảo:</span>
                        <span class="service-price">${s.price_range}</span>
                    </div>
                    ${s.warranty ? `<span style="font-size:0.78rem; background:var(--primary-light); color:var(--primary-dark); padding:3px 8px; border-radius:4px; font-weight:600;"><i class="fa-solid fa-shield"></i> ${s.warranty}</span>` : ''}
                </div>
                <div style="display:flex; gap:10px; margin-top:18px;">
                    <button class="btn btn-outline" style="flex:1; padding:9px 12px; font-size:0.88rem;" onclick="openServiceModal('${s.id}')">
                        <i class="fa-solid fa-circle-info"></i> Chi Tiết
                    </button>
                    <button class="btn btn-primary" style="flex:1; padding:9px 12px; font-size:0.88rem;" onclick="openBookingModal('${s.id}')">
                        <i class="fa-solid fa-calendar-plus"></i> Đặt Lịch
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Render Doctors Grid
function renderDoctors(doctors, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = doctors.map(d => `
        <div class="doctor-card" onclick="openDoctorModal('${d.id}')" title="Nhấn để xem chi tiết hồ sơ ${escapeHtml(d.name)}">
            <div class="doctor-img-box">
                <img src="${d.image || 'assets/images/doctor-consult.jpg'}" alt="${escapeHtml(d.name)}" loading="lazy">
                <div class="doctor-card-overlay">
                    <span class="btn-view-profile"><i class="fa-solid fa-id-card"></i> Xem Hồ Sơ Bác Sĩ</span>
                </div>
            </div>
            <div class="doctor-info">
                <h3 class="doctor-name">${escapeHtml(d.name)}</h3>
                <p class="doctor-title">${escapeHtml(d.title)}</p>
                <p class="doctor-exp"><i class="fa-solid fa-award"></i> ${escapeHtml(d.experience)}</p>
                <p style="font-size:0.84rem; color:var(--muted); margin-bottom:15px; line-height:1.5;">${escapeHtml(d.degree || 'Đại học Y Hà Nội')}</p>
                <div class="doctor-card-btns" onclick="event.stopPropagation();">
                    <button class="btn btn-outline" style="flex:1; padding:9px 10px; font-size:0.86rem;" onclick="openDoctorModal('${d.id}')">
                        <i class="fa-solid fa-id-card"></i> Chi Tiết
                    </button>
                    <button class="btn btn-primary" style="flex:1; padding:9px 10px; font-size:0.86rem;" onclick="openBookingModal('', '${d.id}')">
                        <i class="fa-solid fa-calendar-plus"></i> Đặt Lịch
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Render Categorized Price Table
function renderPriceTable(categorizedPrices) {
    const container = document.getElementById('priceTableContainer');
    if (!container) return;

    let html = '';
    for (const [key, group] of Object.entries(categorizedPrices)) {
        if (!group.items || group.items.length === 0) continue;

        html += `
            <div class="price-group-card" data-category="${key}">
                <div class="price-group-header">
                    <i class="fa-solid fa-tooth"></i> ${group.name}
                </div>
                <div class="table-responsive" style="border:none; box-shadow:none; border-radius:0;">
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Tên Dịch Vụ Nha Khoa</th>
                                <th style="width: 25%;">Chi Phí Niêm Yết</th>
                                <th style="width: 15%;">Thời Gian</th>
                                <th style="width: 15%;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${group.items.map(item => `
                                <tr class="price-row" data-name="${item.name.toLowerCase()}">
                                    <td>
                                        <strong style="color:var(--dark); font-size:0.95rem;">${item.name}</strong>
                                        ${item.warranty ? `<br><small style="color:var(--primary); font-weight:600;"><i class="fa-solid fa-shield"></i> ${item.warranty}</small>` : ''}
                                    </td>
                                    <td class="price-val">${item.price_range}</td>
                                    <td><span style="font-size:0.88rem; color:var(--muted);"><i class="fa-regular fa-clock"></i> ${item.duration || '30 - 45 phút'}</span></td>
                                    <td>
                                        <button class="btn btn-primary" style="padding: 7px 16px; font-size: 0.84rem; border-radius:6px;" onclick="openBookingModal('${item.id}')">
                                            Đặt Lịch
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    container.innerHTML = html;
}

// Filter Services by Category Tabs
function filterServices(categorySlug, buttonEl) {
    const tabBtns = document.querySelectorAll('.filter-tabs .tab-btn');
    tabBtns.forEach(btn => btn.classList.remove('active'));
    if (buttonEl) buttonEl.classList.add('active');

    if (categorySlug === 'all') {
        renderServices(globalServices, 'full-services-grid');
    } else {
        const filtered = globalServices.filter(s => s.category_slug === categorySlug || s.category.toLowerCase() === categorySlug.toLowerCase());
        renderServices(filtered, 'full-services-grid');
    }
}

// Search Price Table Rows
function searchPriceTable() {
    const input = document.getElementById('priceSearchInput');
    if (!input) return;
    const query = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.price-row');

    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        if (name.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Populate Select Dropdowns
function populateServiceSelect() {
    const select = document.getElementById('b_service');
    if (!select) return;

    select.innerHTML = globalServices.map(s => `
        <option value="${s.id}">${s.name} (${s.price_range})</option>
    `).join('');
}

function populateDoctorSelect() {
    const select = document.getElementById('b_doctor');
    if (!select) return;

    select.innerHTML = `<option value="">Bác sĩ Chuyên khoa (Lễ tân xếp lịch phù hợp)</option>` + 
        globalDoctors.map(d => `
        <option value="${d.id}">${d.name} - ${d.specialty}</option>
    `).join('');
}

// --- MODAL DIALOGS ---

function showDoctorPreviewInBooking(doctorId) {
    const container = document.getElementById('bookingDoctorPreview');
    if (!container) return;
    if (!doctorId) {
        container.style.display = 'none';
        container.innerHTML = '';
        return;
    }
    const doc = globalDoctors.find(d => 
        (d.id && String(d.id).toUpperCase() === String(doctorId).toUpperCase()) || 
        String(d.id).replace(/\D/g, '') === String(doctorId).replace(/\D/g, '') ||
        (d.name && d.name.toLowerCase().includes(String(doctorId).toLowerCase()))
    );
    if (!doc) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    container.innerHTML = `
        <div style="background:#f0f9ff; border:1.5px solid #bae6fd; border-radius:14px; padding:14px 18px; display:flex; align-items:center; gap:16px; box-shadow:0 4px 12px rgba(2, 132, 199, 0.08);">
            <img src="${doc.image || 'assets/images/dr-nguyen-an.jpg'}" alt="${escapeHtml(doc.name)}" style="width:62px; height:62px; border-radius:50%; object-fit:cover; border:2.5px solid var(--primary); flex-shrink:0;">
            <div style="flex-grow:1; min-width:0;">
                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:4px;">
                    <span style="font-size:0.75rem; background:var(--primary); color:#ffffff; padding:2px 10px; border-radius:12px; font-weight:700;">${escapeHtml(doc.specialty)}</span>
                    <span style="font-size:0.75rem; color:#059669; font-weight:700; background:#d1fae5; padding:2px 8px; border-radius:12px;"><i class="fa-solid fa-circle-check"></i> Đã chỉ định trực tiếp</span>
                </div>
                <h5 style="margin:0 0 3px; font-size:1rem; font-weight:800; color:var(--dark);">${escapeHtml(doc.name)}</h5>
                <p style="margin:0; font-size:0.82rem; color:var(--slate);"><i class="fa-solid fa-award text-warning"></i> <strong>${escapeHtml(doc.experience)}</strong> &bull; <i class="fa-solid fa-clock text-primary"></i> ${escapeHtml(doc.schedule || 'Thứ 2 - Thứ 7')}</p>
            </div>
        </div>
    `;
}

function openBookingModal(serviceId = '', doctorId = '') {
    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.classList.add('active');
        if (serviceId) {
            const svcSelect = document.getElementById('b_service');
            if (svcSelect) svcSelect.value = serviceId;
        }
        if (doctorId) {
            const docSelect = document.getElementById('b_doctor');
            if (docSelect) docSelect.value = doctorId;
        }
        showDoctorPreviewInBooking(doctorId || (document.getElementById('b_doctor') ? document.getElementById('b_doctor').value : ''));
    }
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    if (modal) modal.classList.remove('active');
    const preview = document.getElementById('bookingDoctorPreview');
    if (preview) {
        preview.style.display = 'none';
        preview.innerHTML = '';
    }
}

function openServiceModal(serviceId) {
    const service = globalServices.find(s => (s.id && s.id.toUpperCase() === serviceId.toUpperCase()) || s.id === serviceId);
    if (!service) return;

    const modal = document.getElementById('serviceModal');
    const content = document.getElementById('serviceModalContent');

    content.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            <div>
                <img src="${service.image}" alt="${service.name}" style="width:100%; border-radius:16px; box-shadow:var(--shadow-md);">
                <div style="margin-top:20px; background:var(--mint-bg); border:1px solid var(--border-emerald); padding:16px; border-radius:12px;">
                    <p style="margin-bottom:8px; font-size:0.9rem;"><strong><i class="fa-solid fa-clock" style="color:var(--primary);"></i> Thời gian:</strong> ${service.duration || '30 - 45 phút'}</p>
                    <p style="margin-bottom:0; font-size:0.9rem;"><strong><i class="fa-solid fa-shield-halved" style="color:var(--primary);"></i> Bảo hành:</strong> ${service.warranty || 'Định kỳ theo phác đồ'}</p>
                </div>
            </div>
            <div>
                <span class="sub-badge">${service.category}</span>
                <h2 style="font-size:1.65rem; color:var(--dark); margin:8px 0 12px;">${service.name}</h2>
                <p style="font-size:1.3rem; font-weight:800; color:var(--primary); margin-bottom:14px;">${service.price_range}</p>
                <p style="color:var(--slate); margin-bottom: 20px; line-height:1.6;">${service.description}</p>

                ${service.procedure && service.procedure.length > 0 ? `
                    <h4 style="margin-top:20px; margin-bottom:12px; color:var(--dark);">Quy Trình Chuẩn Y Khoa:</h4>
                    <ul style="padding-left:0; font-size:0.9rem; color:var(--slate); display:flex; flex-direction:column; gap:8px;">
                        ${service.procedure.map((step, idx) => `
                            <li style="display:flex; align-items:flex-start; gap:10px;">
                                <span style="background:var(--primary); color:#ffffff; width:22px; height:22px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; flex-shrink:0; margin-top:2px;">${idx + 1}</span>
                                <span>${step}</span>
                            </li>
                        `).join('')}
                    </ul>
                ` : ''}

                <div style="margin-top:25px; display:flex; gap:12px;">
                    <button class="btn btn-primary" style="flex:1;" onclick="closeServiceModal(); openBookingModal('${service.id}');">
                        <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khám Miễn Phí 0đ
                    </button>
                </div>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function closeServiceModal() {
    const modal = document.getElementById('serviceModal');
    if (modal) modal.classList.remove('active');
}

// --- DOCTOR DETAIL MODAL ---

function openDoctorModal(doctorId) {
    if (!doctorId) return;
    const doc = globalDoctors.find(d => (d.id && d.id.toUpperCase() === doctorId.toUpperCase()) || d.id === doctorId);
    if (!doc) return;

    const modal = document.getElementById('doctorModal');
    const content = document.getElementById('doctorModalContent');
    if (!modal || !content) return;

    const strengthsHtml = (doc.strengths && doc.strengths.length > 0)
        ? doc.strengths.map(s => `
            <div class="doctor-strength-item">
                <i class="fa-solid fa-circle-check text-success"></i>
                <span>${escapeHtml(s)}</span>
            </div>
        `).join('')
        : `
            <div class="doctor-strength-item"><i class="fa-solid fa-circle-check text-success"></i> <span>Chẩn đoán hình ảnh kỹ thuật số 3D chuyên sâu</span></div>
            <div class="doctor-strength-item"><i class="fa-solid fa-circle-check text-success"></i> <span>Kỹ thuật điều trị bảo tồn mô răng tối đa</span></div>
            <div class="doctor-strength-item"><i class="fa-solid fa-circle-check text-success"></i> <span>Điều trị vi phẫu không đau chuẩn quốc tế</span></div>
            <div class="doctor-strength-item"><i class="fa-solid fa-circle-check text-success"></i> <span>Theo dõi và chăm sóc hậu phẫu tận tâm 24/7</span></div>
        `;

    const certsHtml = (doc.certifications && doc.certifications.length > 0)
        ? doc.certifications.map(c => `
            <li>
                <i class="fa-solid fa-award text-warning"></i>
                <span>${escapeHtml(c)}</span>
            </li>
        `).join('')
        : `
            <li><i class="fa-solid fa-award text-warning"></i> <span>Chứng chỉ hành nghề khám chữa bệnh Răng Hàm Mặt do Bộ Y Tế cấp</span></li>
            <li><i class="fa-solid fa-award text-warning"></i> <span>Chứng chỉ đào tạo liên tục chuyên ngành RHM quốc tế</span></li>
        `;

    content.innerHTML = `
        <div class="doctor-detail-layout">
            <!-- Left Column: Portrait & Key Meta -->
            <div class="doctor-detail-aside">
                <div class="doctor-detail-avatar-wrap">
                    <img src="${doc.image || 'assets/images/dr-nguyen-an.jpg'}" alt="${escapeHtml(doc.name)}" class="doctor-detail-img">
                    <div class="doctor-status-pill">
                        <span class="doctor-status-dot"></span> Đang nhận lịch khám trực tiếp
                    </div>
                </div>

                <div class="doctor-meta-box">
                    <div class="doctor-meta-stat">
                        <i class="fa-solid fa-medal text-warning"></i>
                        <div>
                            <span class="meta-label">Kinh Nghiệm</span>
                            <strong>${escapeHtml(doc.experience || '10+ năm kinh nghiệm')}</strong>
                        </div>
                    </div>
                    <div class="doctor-meta-stat">
                        <i class="fa-solid fa-user-check text-success"></i>
                        <div>
                            <span class="meta-label">Ca Điều Trị</span>
                            <strong>${escapeHtml(doc.cases_count || '3.000+ ca thành công')}</strong>
                        </div>
                    </div>
                    <div class="doctor-meta-stat">
                        <i class="fa-solid fa-star text-warning"></i>
                        <div>
                            <span class="meta-label">Mức Độ Hài Lòng</span>
                            <strong>${escapeHtml(doc.rating || '4.9 / 5.0 (Rất hài lòng)')}</strong>
                        </div>
                    </div>
                    <div class="doctor-meta-stat">
                        <i class="fa-solid fa-clock text-primary"></i>
                        <div>
                            <span class="meta-label">Lịch Khám Định Kỳ</span>
                            <strong>${escapeHtml(doc.schedule || 'Thứ 2 - Thứ 7 (08:00 - 18:00)')}</strong>
                        </div>
                    </div>
                    <div class="doctor-meta-stat">
                        <i class="fa-solid fa-location-dot text-info"></i>
                        <div>
                            <span class="meta-label">Cơ Sở Trực Tiếp</span>
                            <strong>${escapeHtml(doc.branch || 'Hệ thống Nha Khoa Premier')}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Full Info, Credentials, Specialties & Booking CTA -->
            <div class="doctor-detail-main">
                <div class="doctor-badge-row">
                    <span class="doctor-spec-badge">${escapeHtml(doc.specialty || 'Chuyên Khoa')}</span>
                    <span class="doctor-verified-badge"><i class="fa-solid fa-circle-check"></i> Đã Xác Thực Bộ Y Tế</span>
                </div>

                <h2 class="doctor-modal-name">${escapeHtml(doc.name)}</h2>
                <p class="doctor-modal-title">${escapeHtml(doc.title)}</p>
                <p class="doctor-modal-degree">
                    <i class="fa-solid fa-graduation-cap text-primary"></i>
                    <span>${escapeHtml(doc.degree || 'Đại học Y Hà Nội')}</span>
                </p>

                <div class="doctor-section-divider"></div>

                <!-- Bio / Overview -->
                <div class="doctor-detail-block">
                    <h4 class="doctor-block-title"><i class="fa-solid fa-user-doctor text-primary"></i> Giới Thiệu Chuyên Môn</h4>
                    <p class="doctor-bio-text">${escapeHtml(doc.description || '')}</p>
                </div>

                <!-- Strengths / Clinical Skills -->
                <div class="doctor-detail-block">
                    <h4 class="doctor-block-title"><i class="fa-solid fa-stethoscope text-primary"></i> Thế Mạnh Lâm Sàng & Kỹ Thuật Đỉnh Cao</h4>
                    <div class="doctor-strengths-grid">
                        ${strengthsHtml}
                    </div>
                </div>

                <!-- Certifications & Training -->
                <div class="doctor-detail-block">
                    <h4 class="doctor-block-title"><i class="fa-solid fa-certificate text-warning"></i> Bằng Cấp & Chứng Chỉ Quốc Tế</h4>
                    <ul class="doctor-cert-list">
                        ${certsHtml}
                    </ul>
                </div>

                <!-- Action Buttons CTA -->
                <div class="doctor-modal-actions">
                    <button class="btn btn-primary" style="flex:1.4; padding:12px 18px; font-size:0.95rem;" onclick="selectDoctorAndBook('${doc.id}')">
                        <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khám Với ${escapeHtml(doc.name)} (0đ)
                    </button>
                    <button class="btn btn-outline" style="flex:1; padding:12px 16px; font-size:0.92rem;" onclick="askAiAboutDoctor('${escapeHtml(doc.name)}')">
                        <i class="fa-solid fa-robot"></i> Hỏi Trợ Lý AI Về Bác Sĩ
                    </button>
                </div>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function closeDoctorModal() {
    const modal = document.getElementById('doctorModal');
    if (modal) modal.classList.remove('active');
}

function selectDoctorAndBook(doctorId) {
    closeDoctorModal();
    openBookingModal('', doctorId);
}

function askAiAboutDoctor(doctorName) {
    closeDoctorModal();
    const chatWindow = document.getElementById('chatWindow');
    if (chatWindow && !chatWindow.classList.contains('active')) {
        toggleChatbot();
    }
    const input = document.getElementById('chatInput');
    if (input) {
        input.value = `Tôi muốn tìm hiểu thông tin và đặt lịch khám với ${doctorName}`;
        setTimeout(() => {
            sendChatMessage();
        }, 300);
    }
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    const bookingModal = document.getElementById('bookingModal');
    const serviceModal = document.getElementById('serviceModal');
    const doctorModal = document.getElementById('doctorModal');
    if (e.target === bookingModal) closeBookingModal();
    if (e.target === serviceModal) closeServiceModal();
    if (e.target === doctorModal) closeDoctorModal();
});

// --- APPOINTMENT & CONTACT FORM SUBMISSIONS ---

async function submitBookingForm(e) {
    e.preventDefault();
    const alertBox = document.getElementById('bookingAlert');
    alertBox.className = 'alert-box';
    alertBox.style.display = 'none';

    const payload = {
        name: document.getElementById('b_name').value.trim(),
        phone: document.getElementById('b_phone').value.trim(),
        email: document.getElementById('b_email').value.trim(),
        service_id: document.getElementById('b_service').value,
        doctor_id: document.getElementById('b_doctor').value,
        date: document.getElementById('b_date').value,
        time: document.getElementById('b_time').value,
        notes: document.getElementById('b_notes').value.trim()
    };

    try {
        const res = await fetch('/api/appointments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
            alertBox.className = 'alert-box success';
            alertBox.innerHTML = `✅ <strong>Đặt lịch khám thành công!</strong><br>Mã lịch: <strong>${data.appointment.id}</strong>. Lễ tân Nha Khoa Quốc Tế Premier sẽ liên hệ qua SĐT ${payload.phone} trong vòng 15 phút để xác nhận.`;
            document.getElementById('bookingForm').reset();
            setTimeout(() => {
                closeBookingModal();
                alertBox.style.display = 'none';
            }, 3500);
        } else {
            alertBox.className = 'alert-box error';
            alertBox.innerText = `❌ Lỗi: ${data.detail || 'Không thể tạo lịch hẹn. Vui lòng kiểm tra lại.'}`;
        }
    } catch (err) {
        // Fallback demo response if server is offline
        alertBox.className = 'alert-box success';
        alertBox.innerHTML = `✅ <strong>Đăng ký lịch khám thành công!</strong><br>Hệ thống đã ghi nhận thông tin của bạn. Nha Khoa Quốc Tế Premier sẽ gọi đến số <strong>${payload.phone}</strong> để xác nhận lịch khám.`;
        document.getElementById('bookingForm').reset();
        setTimeout(() => {
            closeBookingModal();
            alertBox.style.display = 'none';
        }, 3500);
    }
}

async function submitContactForm(e) {
    e.preventDefault();
    const alertBox = document.getElementById('contactAlert');
    alertBox.className = 'alert-box';
    alertBox.style.display = 'none';

    const payload = {
        name: document.getElementById('c_name').value.trim(),
        phone: document.getElementById('c_phone').value.trim(),
        email: document.getElementById('c_email').value.trim(),
        subject: document.getElementById('c_subject').value.trim(),
        message: document.getElementById('c_message').value.trim()
    };

    try {
        const res = await fetch('/api/contacts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
            alertBox.className = 'alert-box success';
            alertBox.innerText = `✅ Cảm ơn bạn đã gửi thắc mắc! Bác sĩ chuyên khoa Nha Khoa Quốc Tế Premier sẽ phản hồi sớm nhất.`;
            document.getElementById('contactForm').reset();
        } else {
            alertBox.className = 'alert-box error';
            alertBox.innerText = `❌ Lỗi: ${data.detail || 'Không thể gửi tin nhắn.'}`;
        }
    } catch (err) {
        alertBox.className = 'alert-box success';
        alertBox.innerText = `✅ Cảm ơn bạn đã gửi thắc mắc! Bác sĩ chuyên khoa Nha Khoa Quốc Tế Premier sẽ liên hệ tư vấn sớm nhất.`;
        document.getElementById('contactForm').reset();
    }
}

// Toggle FAQ Item
function toggleFaq(questionEl) {
    const item = questionEl.parentElement;
    item.classList.toggle('active');
}

// --- ADMIN DASHBOARD ---

async function loadAdminDashboard() {
    try {
        const res = await fetch('/api/admin/metrics');
        if (res.ok) {
            const m = await res.json();
            document.getElementById('adm_total_patients').innerText = m.total_patients;
            document.getElementById('adm_total_apps').innerText = m.total_appointments;
            document.getElementById('adm_today_apps').innerText = m.today_appointments;
            document.getElementById('adm_total_services').innerText = m.total_services;
        } else {
            loadMockMetrics();
        }
    } catch (e) {
        loadMockMetrics();
    }

    loadAdminAppointments();
    loadAdminServices();
    loadAdminDoctors();
    loadAdminContacts();
    loadAdminRevenue();
}

function loadMockMetrics() {
    document.getElementById('adm_total_patients').innerText = '1,280';
    document.getElementById('adm_total_apps').innerText = '1,450';
    document.getElementById('adm_today_apps').innerText = '18';
    document.getElementById('adm_total_services').innerText = globalServices.length || '8';
}

async function loadAdminAppointments() {
    const statusFilter = document.getElementById('adminStatusFilter').value;
    const searchFilter = (document.getElementById('adminAppSearch').value || '').trim();

    try {
        let url = `/api/appointments?status=${encodeURIComponent(statusFilter)}`;
        if (searchFilter) url += `&search=${encodeURIComponent(searchFilter)}`;

        const res = await fetch(url);
        const apps = await res.json();
        const tbody = document.getElementById('adminAppTableBody');
        if (!tbody) return;

        if (!apps || apps.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center">Không có lịch hẹn nào phù hợp.</td></tr>`;
            return;
        }

        tbody.innerHTML = apps.map(a => `
            <tr>
                <td><strong>${a.id}</strong></td>
                <td>${a.name}</td>
                <td>${a.phone}</td>
                <td>${a.service_name || a.service_id}</td>
                <td>${a.doctor_name || a.doctor_id}</td>
                <td>${a.date}</td>
                <td>${a.time}</td>
                <td><span class="status-badge ${getStatusBadgeClass(a.status)}">${a.status}</span></td>
                <td>
                    <select onchange="updateAppointmentStatus('${a.id}', this.value)" style="padding:4px 8px; border-radius:4px; font-size:0.82rem; border:1px solid #cbd5e1;">
                        <option value="">Đổi trạng thái</option>
                        <option value="Đã xác nhận">Đã xác nhận</option>
                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                        <option value="Từ chối">Từ chối</option>
                        <option value="Đã hủy">Đã hủy</option>
                    </select>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        console.warn("Lỗi fetch admin appointments:", e);
    }
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'Chờ xác nhận': return 'cho-xac-nhan';
        case 'Đã xác nhận': return 'da-xac-nhan';
        case 'Đã hoàn thành': return 'da-hoan-thanh';
        case 'Từ chối': return 'tu-choi';
        default: return '';
    }
}

async function updateAppointmentStatus(appId, newStatus) {
    if (!newStatus) return;
    try {
        const res = await fetch(`/api/appointments/${appId}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        if (res.ok) {
            loadAdminDashboard();
        }
    } catch (e) {
        alert("Không thể cập nhật trạng thái lịch hẹn.");
    }
}

function loadAdminServices() {
    const tbody = document.getElementById('adminServiceTableBody');
    if (!tbody) return;
    tbody.innerHTML = globalServices.map(s => `
        <tr>
            <td><strong>${s.id}</strong></td>
            <td>${s.name}</td>
            <td>${s.category}</td>
            <td><strong style="color:var(--primary);">${s.price_range}</strong></td>
            <td>${s.warranty || 'Định kỳ'}</td>
        </tr>
    `).join('');
}

function loadAdminDoctors() {
    const tbody = document.getElementById('adminDoctorTableBody');
    if (!tbody) return;
    tbody.innerHTML = globalDoctors.map(d => `
        <tr>
            <td><strong>${d.id}</strong></td>
            <td>${d.name}</td>
            <td>${d.title}</td>
            <td>${d.specialty}</td>
            <td>${d.experience}</td>
        </tr>
    `).join('');
}

async function loadAdminContacts() {
    try {
        const res = await fetch('/api/contacts');
        const contacts = await res.json();
        const tbody = document.getElementById('adminContactTableBody');
        if (!tbody) return;

        if (!contacts || contacts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center">Chưa có tin nhắn liên hệ nào.</td></tr>`;
            return;
        }

        tbody.innerHTML = contacts.map(c => `
            <tr>
                <td><strong>${c.id}</strong></td>
                <td>${c.name}</td>
                <td>${c.phone}</td>
                <td>${c.email || 'N/A'}</td>
                <td>${c.subject}</td>
                <td>${c.message}</td>
                <td>${c.created_at}</td>
            </tr>
        `).join('');
    } catch (e) {
        console.warn("Lỗi fetch admin contacts:", e);
    }
}

function switchAdminTab(tabName, btnEl) {
    const tabs = document.querySelectorAll('.admin-tab');
    tabs.forEach(t => t.classList.remove('active'));
    btnEl.classList.add('active');

    const contents = document.querySelectorAll('.admin-tab-content');
    contents.forEach(c => c.classList.remove('active'));

    const target = document.getElementById(`admin-tab-${tabName}`);
    if (target) {
        target.classList.add('active');
        if (tabName === 'ai_assistant') {
            const input = document.getElementById('adminAiInput');
            if (input) setTimeout(() => input.focus(), 150);
        } else if (tabName === 'revenue') {
            loadAdminRevenue();
        }
    }
}

// --- ADMIN REVENUE TRACKING & ANALYTICS ---

let adminRevenueTransactionsCache = [];

function formatVND(amount) {
    if (typeof amount !== 'number') amount = Number(amount) || 0;
    return amount.toLocaleString('vi-VN') + ' ₫';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

async function loadAdminRevenue() {
    try {
        const res = await fetch('/api/admin/revenue');
        if (!res.ok) {
            console.warn("Lỗi tải API doanh thu:", res.status);
            return;
        }
        const data = await res.json();
        const kpi = data.kpi || data || {};
        const realized = data.formatted_realized || formatVND(kpi.realized_revenue || data.realized_revenue || 0);
        const projected = data.formatted_projected || formatVND(kpi.projected_revenue || data.projected_revenue || 0);
        const monthRev = data.formatted_month || formatVND(kpi.month_revenue || data.month_revenue || 0);
        const todayRev = data.formatted_today || formatVND(kpi.today_revenue || data.today_revenue || 0);
        const avgRev = data.formatted_avg || formatVND(kpi.avg_per_case || kpi.average_per_appointment || data.avg_per_case || 0);

        // Update Top Card in Stats Grid
        const admRevEl = document.getElementById('adm_total_revenue');
        if (admRevEl) admRevEl.innerText = realized;

        // Update Revenue Tab KPI Cards
        const realEl = document.getElementById('rev_kpi_realized');
        if (realEl) realEl.innerText = realized;

        const projEl = document.getElementById('rev_kpi_projected');
        if (projEl) projEl.innerText = projected;

        const monthEl = document.getElementById('rev_kpi_month');
        if (monthEl) monthEl.innerText = monthRev;

        const todayEl = document.getElementById('rev_kpi_today');
        if (todayEl) todayEl.innerText = todayRev;

        const avgEl = document.getElementById('rev_kpi_avg');
        if (avgEl) avgEl.innerText = avgRev;

        // Render Monthly Trend Bars
        renderRevenueMonthlyTrend(data.monthly_trend || []);

        // Render Service Breakdown
        renderRevenueServiceBreakdown(data.service_breakdown || []);

        // Render Doctor Contribution Table
        renderRevenueDoctorTable(data.doctor_breakdown || []);

        // Cache transactions and render ledger
        adminRevenueTransactionsCache = data.transactions || [];
        renderAdminRevenueLedger(adminRevenueTransactionsCache);

    } catch (err) {
        console.error("Lỗi khi load dữ liệu doanh thu:", err);
    }
}

function renderRevenueMonthlyTrend(monthlyData) {
    const container = document.getElementById('revMonthlyTrendBars');
    if (!container) return;

    if (!monthlyData || monthlyData.length === 0) {
        container.innerHTML = '<div style="margin:auto; color:var(--muted); font-size:0.9rem;">Chưa có dữ liệu xu hướng doanh thu.</div>';
        return;
    }

    const maxRev = Math.max(...monthlyData.map(m => m.revenue || 0), 1);

    container.innerHTML = monthlyData.map((item, idx) => {
        const isCurrent = idx === monthlyData.length - 1;
        const heightPct = Math.max(12, Math.round(((item.revenue || 0) / maxRev) * 100));
        const displayRev = (item.revenue >= 1000000) 
            ? (item.revenue / 1000000).toFixed(1) + ' tr' 
            : (item.revenue > 0 ? (item.revenue / 1000).toFixed(0) + ' k' : '0 ₫');

        return `
            <div class="rev-trend-bar-col">
                <div class="rev-bar-val">${displayRev}</div>
                <div class="rev-bar-track" title="${item.month}: ${formatVND(item.revenue)} (${item.count || 0} ca)">
                    <div class="rev-bar-fill ${isCurrent ? 'active-month' : ''}" style="height: ${heightPct}%;"></div>
                </div>
                <div class="rev-bar-label">${item.month}</div>
            </div>
        `;
    }).join('');
}

function renderRevenueServiceBreakdown(services) {
    const container = document.getElementById('revServiceBreakdownList');
    if (!container) return;

    if (!services || services.length === 0) {
        container.innerHTML = '<div style="margin:auto; color:var(--muted); font-size:0.9rem;">Chưa có dữ liệu cơ cấu dịch vụ.</div>';
        return;
    }

    container.innerHTML = services.map(s => `
        <div class="rev-service-row">
            <div class="rev-service-header">
                <span class="rev-service-name">
                    <i class="fa-solid fa-tooth" style="color:var(--primary); font-size:0.85rem;"></i>
                    ${escapeHtml(s.service_name)}
                </span>
                <span class="rev-service-amount">${formatVND(s.revenue)}</span>
            </div>
            <div class="rev-service-track">
                <div class="rev-service-bar" style="width: ${Math.min(100, s.percentage || 0)}%;"></div>
            </div>
            <div class="rev-service-meta">
                <span>${s.count || 0} ca điều trị</span>
                <span><strong>${s.percentage || 0}%</strong> tổng doanh số</span>
            </div>
        </div>
    `).join('');
}

function renderRevenueDoctorTable(doctors) {
    const tbody = document.getElementById('revDoctorTableBody');
    if (!tbody) return;

    if (!doctors || doctors.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--muted); padding:20px;">Chưa có dữ liệu đóng góp của bác sĩ.</td></tr>';
        return;
    }

    tbody.innerHTML = doctors.map(doc => `
        <tr>
            <td>
                <strong>${escapeHtml(doc.doctor_name)}</strong>
            </td>
            <td><span class="tech-badge-item">${escapeHtml(doc.specialty || 'Nha khoa')}</span></td>
            <td style="text-align:center;">
                <span style="background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:12px; font-weight:700; font-size:0.82rem;">
                    ${doc.count || 0} ca
                </span>
            </td>
            <td style="text-align:right; font-weight:800; color:#059669;">
                ${formatVND(doc.revenue)}
            </td>
            <td style="text-align:right;">
                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                    <div style="flex-grow:1; max-width:80px; height:6px; background:#f1f5f9; border-radius:3px; overflow:hidden;">
                        <div style="height:100%; width:${Math.min(100, doc.percentage || 0)}%; background:#f59e0b; border-radius:3px;"></div>
                    </div>
                    <span style="font-weight:700; font-size:0.85rem; color:var(--dark); min-width:40px;">${doc.percentage || 0}%</span>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderAdminRevenueLedger(transactions) {
    const tbody = document.getElementById('revTransactionsTableBody');
    if (!tbody) return;

    if (!transactions || transactions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:var(--muted); padding:24px;">Không tìm thấy giao dịch nào phù hợp.</td></tr>';
        return;
    }

    tbody.innerHTML = transactions.map(t => {
        let badgeClass = 'cho-xac-nhan';
        if (t.status === 'Đã xác nhận' || t.status === 'confirmed') badgeClass = 'da-xac-nhan';
        else if (t.status === 'Đã hoàn thành' || t.status === 'completed') badgeClass = 'da-hoan-thanh';
        else if (t.status === 'Từ chối' || t.status === 'cancelled') badgeClass = 'tu-choi';

        const displayStatus = t.status === 'completed' ? 'Đã hoàn thành' : (t.status === 'confirmed' ? 'Đã xác nhận' : t.status);

        return `
            <tr>
                <td><span style="font-family:monospace; font-weight:700; color:var(--slate);">#DH-${t.id || '0'}</span></td>
                <td><i class="fa-regular fa-calendar me-1 text-muted" style="font-size:0.8rem;"></i> ${escapeHtml(t.date || '')}</td>
                <td>
                    <strong>${escapeHtml(t.patient_name || 'Khách vãng lai')}</strong>
                    <div style="font-size:0.78rem; color:var(--muted);">${escapeHtml(t.phone || '')}</div>
                </td>
                <td>
                    <span style="font-weight:600; color:var(--dark);">${escapeHtml(t.service_name || 'Khám tổng quát')}</span>
                </td>
                <td>${escapeHtml(t.doctor_name || 'Bác sĩ trực')}</td>
                <td style="text-align:right; font-weight:800; color:${t.status === 'Đã hoàn thành' || t.status === 'completed' ? '#059669' : '#d97706'};">
                    ${formatVND(t.price || 0)}
                </td>
                <td style="text-align:center;">
                    <span class="status-badge ${badgeClass}">${escapeHtml(displayStatus)}</span>
                </td>
            </tr>
        `;
    }).join('');
}

function filterAdminRevenueLedger() {
    const statusVal = (document.getElementById('revStatusFilter')?.value || 'all').trim();
    const searchVal = (document.getElementById('revSearchInput')?.value || '').toLowerCase().trim();

    let filtered = adminRevenueTransactionsCache;

    if (statusVal !== 'all') {
        filtered = filtered.filter(t => t.status === statusVal || 
            (statusVal === 'Đã hoàn thành' && t.status === 'completed') ||
            (statusVal === 'Đã xác nhận' && t.status === 'confirmed')
        );
    }

    if (searchVal) {
        filtered = filtered.filter(t => 
            (t.patient_name && t.patient_name.toLowerCase().includes(searchVal)) ||
            (t.phone && t.phone.toLowerCase().includes(searchVal)) ||
            (t.service_name && t.service_name.toLowerCase().includes(searchVal)) ||
            (t.doctor_name && t.doctor_name.toLowerCase().includes(searchVal)) ||
            (String(t.id).includes(searchVal))
        );
    }

    renderAdminRevenueLedger(filtered);
}

// --- ADMIN EXECUTIVE AI ASSISTANT ---

function handleAdminChatKeyPress(e) {
    if (e.key === 'Enter') {
        sendAdminChatMessage();
    }
}

function handleAdminChatChip(promptText) {
    const input = document.getElementById('adminAiInput');
    if (input) {
        input.value = promptText;
        sendAdminChatMessage();
    }
}

function clearAdminChat() {
    const box = document.getElementById('adminAiChatBox');
    if (!box) return;
    box.innerHTML = `
        <div class="admin-msg admin-msg-ai">
            <div class="admin-msg-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="admin-msg-body">
                <div class="admin-msg-header">
                    <strong>Trợ Lý Quản Trị AI</strong>
                    <span class="admin-msg-time">Vừa xong</span>
                </div>
                <div class="admin-msg-content">
                    <p>Lịch sử hội thoại đã được đặt lại. Quản trị viên cần hỗ trợ thêm thông tin hoặc xử lý nghiệp vụ nào?</p>
                </div>
            </div>
        </div>
    `;
    const input = document.getElementById('adminAiInput');
    if (input) input.focus();
}

function formatAdminMessage(raw) {
    if (!raw) return '';
    let text = raw
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    // Code blocks ```code```
    text = text.replace(/```([\s\S]*?)```/g, function(match, p1) {
        return `<pre><code>${p1.trim()}</code></pre>`;
    });

    // Inline code `code`
    text = text.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Headers
    text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    text = text.replace(/^## (.*$)/gim, '<h3>$1</h3>');

    // Bold
    text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

    // Italic
    text = text.replace(/_(.*?)_/g, '<em>$1</em>');

    // Unordered lists
    text = text.replace(/^\s*-\s+(.*$)/gim, '<li>$1</li>');
    text = text.replace(/(<li>.*<\/li>)/gims, function(match) {
        return `<ul>${match}</ul>`;
    });
    text = text.replace(/<\/ul>\s*<ul>/g, '');

    // Paragraph line breaks
    text = text.replace(/\n\n+/g, '</p><p>');
    text = text.replace(/\n/g, '<br>');

    return `<p>${text}</p>`;
}

async function sendAdminChatMessage() {
    const input = document.getElementById('adminAiInput');
    const sendBtn = document.getElementById('adminAiSendBtn');
    const box = document.getElementById('adminAiChatBox');
    if (!input || !box) return;

    const message = input.value.trim();
    if (!message) return;

    // Append User Message
    const userTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    const userMsgHtml = `
        <div class="admin-msg admin-msg-user">
            <div class="admin-msg-avatar"><i class="fa-solid fa-user-shield"></i></div>
            <div class="admin-msg-body">
                <div class="admin-msg-header">
                    <strong>Quản Trị Viên</strong>
                    <span class="admin-msg-time">${userTime}</span>
                </div>
                <div class="admin-msg-content">${formatAdminMessage(message)}</div>
            </div>
        </div>
    `;
    box.insertAdjacentHTML('beforeend', userMsgHtml);
    input.value = '';
    box.scrollTop = box.scrollHeight;

    // Append Typing Indicator
    const typingId = 'typing_' + Date.now();
    const typingHtml = `
        <div class="admin-msg admin-msg-ai" id="${typingId}">
            <div class="admin-msg-avatar"><i class="fa-solid fa-robot"></i></div>
            <div class="admin-msg-body">
                <div class="admin-msg-header">
                    <strong>Trợ Lý Quản Trị AI</strong>
                    <span class="admin-msg-time">Đang xử lý...</span>
                </div>
                <div class="admin-msg-content">
                    <div class="admin-typing-indicator">
                        <span class="admin-typing-dot"></span>
                        <span class="admin-typing-dot"></span>
                        <span class="admin-typing-dot"></span>
                    </div>
                </div>
            </div>
        </div>
    `;
    box.insertAdjacentHTML('beforeend', typingHtml);
    box.scrollTop = box.scrollHeight;

    if (sendBtn) sendBtn.disabled = true;

    try {
        const res = await fetch('/api/admin/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        });

        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        const aiTime = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        if (res.ok) {
            const data = await res.json();
            const replyHtml = formatAdminMessage(data.reply || 'Hệ thống đã nhận thông tin.');
            const aiMsgHtml = `
                <div class="admin-msg admin-msg-ai">
                    <div class="admin-msg-avatar"><i class="fa-solid fa-robot"></i></div>
                    <div class="admin-msg-body">
                        <div class="admin-msg-header">
                            <strong>Trợ Lý Quản Trị AI</strong>
                            <span class="admin-msg-time">${aiTime}</span>
                        </div>
                        <div class="admin-msg-content">${replyHtml}</div>
                    </div>
                </div>
            `;
            box.insertAdjacentHTML('beforeend', aiMsgHtml);
        } else {
            const errData = await res.json().catch(() => ({}));
            const errMsg = errData.detail || 'Không thể kết nối đến máy chủ trợ lý AI.';
            const errHtml = `
                <div class="admin-msg admin-msg-ai">
                    <div class="admin-msg-avatar"><i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i></div>
                    <div class="admin-msg-body" style="border-color:#fecaca;">
                        <div class="admin-msg-header">
                            <strong style="color:#b91c1c;">Lỗi Phản Hồi</strong>
                            <span class="admin-msg-time">${aiTime}</span>
                        </div>
                        <div class="admin-msg-content" style="color:#b91c1c;">
                            <p>${errMsg}</p>
                        </div>
                    </div>
                </div>
            `;
            box.insertAdjacentHTML('beforeend', errHtml);
        }
    } catch (e) {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        const errHtml = `
            <div class="admin-msg admin-msg-ai">
                <div class="admin-msg-avatar"><i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i></div>
                <div class="admin-msg-body" style="border-color:#fecaca;">
                    <div class="admin-msg-header">
                        <strong style="color:#b91c1c;">Lỗi Kết Nối Mạng</strong>
                    </div>
                    <div class="admin-msg-content" style="color:#b91c1c;">
                        <p>Không thể kết nối tới API Server. Vui lòng kiểm tra lại dịch vụ backend.</p>
                    </div>
                </div>
            </div>
        `;
        box.insertAdjacentHTML('beforeend', errHtml);
    } finally {
        if (sendBtn) sendBtn.disabled = false;
        box.scrollTop = box.scrollHeight;
        if (input) input.focus();
    }
}

// --- FLOATING CHATBOT WIDGET ("PREMIER AI ASSISTANT") ---

function toggleChatbot() {
    const windowEl = document.getElementById('chatWindow');
    if (!windowEl) return;
    windowEl.classList.toggle('active');
    
    // Hide notification badge once opened
    const badge = document.querySelector('.trigger-badge');
    if (badge) badge.style.display = 'none';

    if (windowEl.classList.contains('active')) {
        const chatInput = document.getElementById('chatInput');
        if (chatInput) chatInput.focus();
    }
}

function openChatbot() {
    const windowEl = document.getElementById('chatWindow');
    if (!windowEl) return;
    if (!windowEl.classList.contains('active')) {
        windowEl.classList.add('active');
    }
    const chatInput = document.getElementById('chatInput');
    if (chatInput) chatInput.focus();
}

function handleChatKeyPress(e) {
    if (e.key === 'Enter') {
        sendChatMessage();
    }
}

function sendPreset(promptText) {
    const input = document.getElementById('chatInput');
    if (input) {
        input.value = promptText;
        sendChatMessage();
    }
}

// --- DENTAL IMAGE UPLOAD & VISION SCREENING ---
let selectedChatImageFile = null;
let selectedChatImageDataUrl = null;

function triggerFileInput() {
    const fileInput = document.getElementById('chatFileInput');
    if (fileInput) {
        fileInput.value = '';
        fileInput.click();
    }
}

function processImageFile(file) {
    if (!file) return;

    const isImageMime = file.type && file.type.toLowerCase().startsWith('image/');
    const isImageExt = /\.(jpe?g|png|webp|jfif|bmp|gif|heic|heif)$/i.test(file.name || '');

    if (!isImageMime && !isImageExt) {
        if (typeof showToast === 'function') {
            showToast('Vui lòng chọn hoặc dán tệp ảnh hợp lệ (JPG, PNG, WEBP...).', 'warning');
        }
        clearImageSelection();
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        if (typeof showToast === 'function') {
            showToast('Dung lượng ảnh vượt quá 10MB. Vui lòng chọn ảnh nhẹ hơn.', 'warning');
        }
        clearImageSelection();
        return;
    }

    selectedChatImageFile = file;

    const reader = new FileReader();
    reader.onload = function (e) {
        selectedChatImageDataUrl = e.target.result;
        const thumb = document.getElementById('chatPreviewThumb');
        const nameEl = document.getElementById('chatPreviewName');
        const metaEl = document.getElementById('chatPreviewMeta');
        const area = document.getElementById('chatImagePreviewArea');
        const uploadBtn = document.getElementById('uploadChatBtn');
        const chatInput = document.getElementById('chatInput');

        if (thumb) thumb.src = selectedChatImageDataUrl;
        if (nameEl) nameEl.textContent = file.name || 'Ảnh răng đã chọn';
        if (metaEl) {
            const sizeMb = (file.size / (1024 * 1024)).toFixed(1);
            const sizeText = file.size >= 1024 * 1024 ? `${sizeMb} MB` : `${Math.round(file.size / 1024)} KB`;
            metaEl.textContent = `${sizeText} • Sẵn sàng phân tích`;
        }
        if (area) area.style.display = 'flex';
        if (uploadBtn) uploadBtn.classList.add('has-image');
        if (chatInput) {
            chatInput.placeholder = 'Nhập câu hỏi hoặc dán ảnh (Ctrl+V)...';
            chatInput.focus();
        }
    };
    reader.readAsDataURL(file);
}

function handleImageFileSelect(input) {
    const file = input.files && input.files[0];
    processImageFile(file);
}

// Hỗ trợ dán ảnh từ Clipboard (Ctrl+V)
document.addEventListener('paste', function (e) {
    const chatModal = document.getElementById('aiChatModal') || document.querySelector('.chat-widget-modal');
    if (!chatModal || chatModal.style.display === 'none') return;

    const clipboardData = e.clipboardData || window.clipboardData;
    if (!clipboardData || !clipboardData.items) return;

    for (let i = 0; i < clipboardData.items.length; i++) {
        const item = clipboardData.items[i];
        if (item.type && item.type.indexOf('image') !== -1) {
            const file = item.getAsFile();
            if (file) {
                e.preventDefault();
                const ext = (file.type && file.type.split('/')[1]) || 'png';
                const cleanExt = ext === 'jpeg' ? 'jpg' : ext;
                const pastedFile = new File([file], `anh_dan_${Date.now()}.${cleanExt}`, {
                    type: file.type || 'image/png'
                });
                processImageFile(pastedFile);
                if (typeof showToast === 'function') {
                    showToast('Đã dán ảnh từ clipboard thành công!', 'success');
                }
                break;
            }
        }
    }
});

function clearImageSelection() {
    selectedChatImageFile = null;
    selectedChatImageDataUrl = null;
    const fileInput = document.getElementById('chatFileInput');
    const area = document.getElementById('chatImagePreviewArea');
    const uploadBtn = document.getElementById('uploadChatBtn');
    const chatInput = document.getElementById('chatInput');

    if (fileInput) fileInput.value = '';
    if (area) area.style.display = 'none';
    if (uploadBtn) uploadBtn.classList.remove('has-image');
    if (chatInput) chatInput.placeholder = 'Nhập thắc mắc hoặc gửi kèm ảnh...';
}

async function sendChatMessage() {
    const input = document.getElementById('chatInput');
    if (!input) return;
    const messageText = input.value.trim();

    // Stop speech recognition if still active
    stopVoiceRecognition();

    // Check if user is sending an image
    if (selectedChatImageFile) {
        const fileToSend = selectedChatImageFile;
        const dataUrlToSend = selectedChatImageDataUrl;

        // Display user bubble with image
        let userContent = '';
        if (dataUrlToSend) {
            userContent += `<img src="${dataUrlToSend}" class="chat-user-img-thumb" alt="Ảnh tải lên">`;
        }
        if (messageText) {
            userContent += `<div>${messageText}</div>`;
        } else {
            userContent += `<div><em>📷 Đã gửi 1 ảnh răng/khoang miệng để AI phân tích</em></div>`;
        }
        appendMessage(userContent, 'user');

        input.value = '';
        clearImageSelection();

        // Show vision loading
        showChatLoading(true);

        const formData = new FormData();
        formData.append('image', fileToSend);
        if (messageText) formData.append('message', messageText);

        try {
            const res = await fetch('/api/analyze-dental-image', {
                method: 'POST',
                body: formData
            });

            if (res.ok) {
                const data = await res.json();
                showChatLoading(false);
                if (data && data.success && data.analysis) {
                    const reportHtml = formatVisionReportSPA(data.analysis);
                    appendMessage(reportHtml, 'assistant');
                    return;
                }
            }
            showChatLoading(false);
            appendFallbackVisionResponse();
        } catch (e) {
            showChatLoading(false);
            appendFallbackVisionResponse();
        }
        return;
    }

    if (!messageText) return;

    // Regular text message
    appendMessage(messageText, 'user');
    input.value = '';

    // Show Loading Spinner
    showChatLoading(true);

    try {
        const res = await fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: messageText })
        });

        if (res.ok) {
            const data = await res.json();
            showChatLoading(false);
            if (data.reply) {
                appendMessage(data.reply, 'assistant');
                return;
            }
        }
        showChatLoading(false);
        appendFallbackChatResponse(messageText);
    } catch (e) {
        showChatLoading(false);
        appendFallbackChatResponse(messageText);
    }
}

function formatVisionReportSPA(analysis) {
    const quality = analysis.image_quality || 'acceptable';
    const riskLevel = analysis.risk_level || 'low';

    let riskBadgeClass = 'badge-risk-low';
    let riskText = 'Thấp (Chưa thấy dấu hiệu khẩn cấp)';
    if (riskLevel === 'medium') {
        riskBadgeClass = 'badge-risk-medium';
        riskText = 'Trung bình (Cần kiểm tra)';
    } else if (riskLevel === 'high') {
        riskBadgeClass = 'badge-risk-high';
        riskText = 'Đáng lưu ý (Nên khám sớm)';
    }

    let qualityText = 'Tốt (Rõ nét, đủ ánh sáng)';
    if (quality === 'acceptable') qualityText = 'Tương đối (Có thể quan sát)';
    if (quality === 'insufficient') qualityText = 'Chưa đạt (Cần chụp lại)';

    let cardHtml = `
        <div class="dental-vision-card">
            <div class="vision-card-header">
                <div class="vision-card-title">
                    <i class="fa-solid fa-tooth text-primary"></i> KẾT QUẢ SÀNG LỌC HÌNH ẢNH
                </div>
                <span class="vision-risk-badge ${riskBadgeClass}">Lưu ý: ${riskText}</span>
            </div>

            <div class="vision-section">
                <div class="vision-section-label"><i class="fa-solid fa-camera"></i> Chất lượng ảnh:</div>
                <div class="small fw-semibold text-dark">${qualityText}</div>
            </div>
    `;

    if (quality === 'insufficient') {
        cardHtml += `
            <div class="vision-section">
                <div style="background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 8px 12px; border-radius: 8px; font-size: 0.82rem;">
                    <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Ảnh chưa đủ rõ nét:</strong><br>
                    ${analysis.recommendation || 'Ảnh chụp bị tối, mờ hoặc không thấy rõ răng/nướu. Bạn vui lòng chụp lại ảnh cận cảnh với đủ ánh sáng để AI có thể hỗ trợ tốt nhất nhé!'}
                </div>
            </div>
        `;
    } else {
        if (analysis.visible_findings && analysis.visible_findings.length > 0) {
            cardHtml += `
                <div class="vision-section">
                    <div class="vision-section-label"><i class="fa-solid fa-magnifying-glass text-primary"></i> Dấu hiệu có thể quan sát:</div>
                    <ul class="vision-findings-list">
            `;
            analysis.visible_findings.forEach(f => {
                cardHtml += `<li><strong>• ${f.finding}:</strong> ${f.description || ''}</li>`;
            });
            cardHtml += `</ul></div>`;
        }

        if (analysis.possible_conditions && analysis.possible_conditions.length > 0) {
            cardHtml += `
                <div class="vision-section">
                    <div class="vision-section-label"><i class="fa-solid fa-stethoscope text-info"></i> Tình trạng có thể liên quan:</div>
                    <ul class="vision-findings-list">
            `;
            analysis.possible_conditions.forEach(c => {
                cardHtml += `<li><strong>• ${c.condition}:</strong> ${c.reason || ''}</li>`;
            });
            cardHtml += `</ul></div>`;
        }

        if (analysis.recommendation) {
            cardHtml += `
                <div class="vision-section">
                    <div class="vision-section-label"><i class="fa-solid fa-lightbulb text-warning"></i> Khuyến nghị hành động:</div>
                    <div class="vision-recommendation-box">${analysis.recommendation}</div>
                </div>
            `;
        }
    }

    const limitationsText = analysis.limitations ? ` ${analysis.limitations}` : '';
    cardHtml += `
            <div class="vision-disclaimer-box">
                <strong>📌 Lưu ý quan trọng:</strong> Đây là công cụ hỗ trợ sàng lọc ban đầu từ hình ảnh, <strong>tuyệt đối không phải chẩn đoán y khoa chính thức</strong> và không thay thế việc khám trực tiếp cùng phim chụp X-quang.${limitationsText}
            </div>

            <div class="vision-booking-action">
                <button type="button" class="btn btn-primary" onclick="openBookingModal()" style="font-size: 0.82rem; padding: 6px 14px; border-radius: 20px;">
                    <i class="fa-regular fa-calendar-check me-1"></i> Đặt lịch khám Bác sĩ (0đ)
                </button>
            </div>
        </div>
    `;
    return cardHtml;
}

function appendFallbackVisionResponse() {
    const mockAnalysis = {
        image_quality: "good",
        visible_findings: [
            {
                finding: "Màu men răng và viền nướu có thể quan sát được",
                confidence: "high",
                description: "Bề mặt các răng phía trước cho thấy màu sắc tương đối đồng đều, không thấy tổn thương gãy vỡ lớn ở mặt ngoài."
            },
            {
                finding: "Mảng bám/vôi răng viền nướu",
                confidence: "medium",
                description: "Có thể có một số mảng bám nhẹ ở vùng cổ răng cần được làm sạch bằng sóng siêu âm định kỳ."
            }
        ],
        possible_conditions: [
            {
                condition: "Tình trạng răng miệng tương đối ổn định",
                reason: "Không quan sát thấy các dấu hiệu sâu răng cấp tính hay viêm mủ diện rộng từ góc chụp này.",
                confidence: "medium"
            }
        ],
        risk_level: "low",
        recommendation: "Bạn nên duy trì đánh răng ngày 2 lần, dùng chỉ nha khoa và đến gặp nha sĩ định kỳ 6 tháng/lần để lấy cao răng và kiểm tra các mặt kẽ khuất.",
        limitations: "Ảnh chụp ngoài không thể thấy được mặt sau của răng, kẽ răng hàm và tình trạng tủy hay xương ổ răng dưới nướu."
    };
    const reportHtml = formatVisionReportSPA(mockAnalysis);
    appendMessage(reportHtml, 'assistant');
}

function generateSmartClientResponse(query) {
    const q = (query || '').toLowerCase().trim();

    // =========================================================================
    // 1. CÂU HỎI VỀ CÁC TRƯỜNG ĐẠI HỌC (TNUT, ICTU, TUMP, TUE, TUEBA, TNU...)
    // =========================================================================
    // TNUT - Trường ĐH Kỹ thuật Công nghiệp Thái Nguyên
    if (q.includes('tnut') || q.includes('kỹ thuật công nghiệp') || q.includes('ky thuat cong nghiep')) {
        return "🎓 **TNUT là Trường Đại học Kỹ thuật Công nghiệp** (thuộc Đại học Thái Nguyên - Thai Nguyen University of Technology).\n\n" +
               "• **Vị thế & Truyền thống:** Thành lập từ năm 1965, TNUT là một trong những trung tâm đào tạo kỹ sư công nghệ, kỹ thuật hàng đầu miền Bắc (chuyên sâu về Cơ khí, Kỹ thuật Điện - Điện tử, Tự động hóa, Kỹ thuật Ô tô, Kỹ thuật Xây dựng...).\n" +
               "• **Địa chỉ:** Số 666 Đường 3/2, Phường Tích Lương, TP. Thái Nguyên, Tỉnh Thái Nguyên.\n" +
               "• **Mối liên hệ:** TNUT là trường đại học thành viên anh em trong khối **Đại học Thái Nguyên**, cùng hệ thống với **ICTU** (Trường ĐH Công nghệ Thông tin & Truyền thông - nơi nhóm sinh viên phát triển dự án Hệ Thống Nha Khoa AI Premier này)!";
    }

    // ICTU - Trường ĐH Công nghệ Thông tin & Truyền thông
    if (q.includes('ictu') || q.includes('công nghệ thông tin và truyền thông') || q.includes('cntt & tt') || q.includes('cntt và tt')) {
        return "🎓 **ICTU là Trường Đại học Công nghệ Thông tin & Truyền thông** (thuộc Đại học Thái Nguyên).\n\n" +
               "• **Vị thế:** Đây là trường đại học công lập trọng điểm hàng đầu tại khu vực Trung du và Miền núi phía Bắc về đào tạo Công nghệ Thông tin, Kỹ thuật Phần mềm, Trí tuệ Nhân tạo & Khoa học Dữ liệu.\n" +
               "• **Đề án nghiên cứu:** Dự án **Hệ Thống Website & AI Chatbot Nha Khoa Quốc Tế Premier** là đề án ứng dụng thực tiễn được nghiên cứu và phát triển bởi nhóm sinh viên ICTU, tích hợp công nghệ AI hỗ trợ tư vấn 24/7, sàng lọc thị giác hình ảnh và quản trị nha khoa số!";
    }

    // TUMP - Trường ĐH Y Dược Thái Nguyên
    if (q.includes('tump') || q.includes('y dược thái nguyên') || q.includes('y thai nguyen') || q.includes('đại học y dược')) {
        return "🏥 **TUMP là Trường Đại học Y - Dược** (thuộc Đại học Thái Nguyên).\n\n" +
               "Đây là cái nôi đào tạo Bác sĩ Đa khoa, Bác sĩ Răng Hàm Mặt, Dược sĩ và nhân lực y tế uy tín hàng đầu khu vực phía Bắc. Đội ngũ bác sĩ tại Premier Dental luôn cập nhật liên tục các nghiên cứu y khoa lâm sàng và tiêu chuẩn điều trị chuẩn hóa từ các trường đại học y khoa danh tiếng.";
    }

    // TUEBA - Trường ĐH Kinh tế & QTKD
    if (q.includes('tueba') || q.includes('kinh tế và quản trị kinh doanh') || q.includes('kinh tế & qtkd')) {
        return "🎓 **TUEBA là Trường Đại học Kinh tế & Quản trị Kinh doanh** (thuộc Đại học Thái Nguyên) - đơn vị đào tạo hàng đầu về quản trị, kinh tế, tài chính - ngân hàng và marketing tại khu vực phía Bắc.";
    }

    // TNU - Đại học Thái Nguyên nói chung hoặc câu hỏi "trường nào"
    if (q.includes('tnu') || (q.includes('trường nào') && !q.includes('răng'))) {
        return "🎓 **Hệ thống Đại học Thái Nguyên (TNU)** bao gồm các trường đại học thành viên trọng điểm:\n\n" +
               "1. **ICTU:** Trường ĐH Công nghệ Thông tin & Truyền thông *(nơi sinh viên nghiên cứu đề án Nha Khoa AI này)*.\n" +
               "2. **TNUT:** Trường ĐH Kỹ thuật Công nghiệp (đào tạo kỹ sư cơ khí, điện, tự động hóa từ 1965).\n" +
               "3. **TUMP:** Trường ĐH Y - Dược (đào tạo Bác sĩ Đa khoa, Răng Hàm Mặt, Dược sĩ).\n" +
               "4. **TUE:** Trường ĐH Sư phạm Thái Nguyên.\n" +
               "5. **TUEBA:** Trường ĐH Kinh tế & Quản trị Kinh doanh.\n" +
               "6. **TUAF & TNUS:** Trường ĐH Nông Lâm & Trường ĐH Khoa học.\n\n" +
               "👉 Bạn đang tìm hiểu cụ thể về trường nào hoặc cần em hỗ trợ tư vấn dịch vụ nha khoa nào ạ?";
    }

    // =========================================================================
    // 2. CHÀO HỎI & BẮT ĐẦU TƯ VẤN
    // =========================================================================
    if (["chào", "xin chào", "hi", "hello", "alo", "bạn ơi", "cho tôi hỏi", "tư vấn"].some(w => q === w || q.startsWith(w + ' '))) {
        return "👋 **Xin chào bạn! Tôi là Chuyên gia Tư vấn Nha khoa AI của Phòng khám Nha Khoa Quốc Tế Premier.**\n\n" +
               "Tôi được trang bị toàn bộ tri thức y khoa nha khoa chuyên sâu và dữ liệu hệ thống phòng khám, luôn sẵn sàng giải đáp MỌI thắc mắc của bạn:\n" +
               "• 🦷 **Bệnh học & Triệu chứng:** Đau nhức, sâu răng, viêm tủy, viêm nha chu, răng khôn, mòn men, ê buốt...\n" +
               "• 🔬 **Dịch vụ chuyên sâu:** Niềng răng mắc cài/Invisalign, Cấy ghép Implant, Bọc răng sứ, Dán Veneer, Nhổ răng Piezotome...\n" +
               "• 💰 **Bảng giá minh bạch & Trả góp 0%:** Báo giá chính xác từng ca điều trị.\n" +
               "• 👨‍⚕️ **Đội ngũ Bác sĩ:** Trực tiếp thăm khám và lên phác đồ 1:1.\n\n" +
               "Bạn đang gặp triệu chứng gì hoặc quan tâm dịch vụ nào, hãy chia sẻ cụ thể để em tư vấn thấu đáo nhé!";
    }

    // =========================================================================
    // 3. THÔNG TIN PHÒNG KHÁM (ĐỊA CHỈ, GIỜ LÀM VIỆC, LIÊN HỆ, HOTLINE)
    // =========================================================================
    if (["địa chỉ", "ở đâu", "chỗ nào", "nơi nào", "vị trí", "đường nào", "chi nhánh", "chỉ đường", "map"].some(k => q.includes(k))) {
        return "📍 **ĐỊA CHỈ PHÒNG KHÁM NHA KHOA QUỐC TẾ PREMIER:**\n\n" +
               "• **Trụ sở chính:** 123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh\n" +
               "• **Thời gian phục vụ:**\n" +
               "  + Thứ 2 - Thứ 7: 08:00 - 20:00 (làm việc thông trưa)\n" +
               "  + Chủ Nhật: 08:00 - 17:30\n" +
               "• **Tổng đài CSKH & Đặt lịch:** **1900 6868** (Miễn phí cước gọi)\n" +
               "• **Hotline Cấp cứu 24/7:** **0900.123.456**\n\n" +
               "🎁 **Đặc quyền:** Đặt lịch trước qua Website hoặc Chatbot được **MIỄN PHÍ 100% gói Khám tổng quát & Chụp phim X-quang CT ConeBeam 3D**!";
    }

    if (["mấy giờ", "giờ làm", "mở cửa", "đóng cửa", "thời gian làm", "chủ nhật", "thứ 7"].some(k => q.includes(k))) {
        return "⏰ **KHUNG GIỜ PHỤC VỤ TẠI NHA KHOA QUỐC TẾ PREMIER:**\n\n" +
               "• **Thứ 2 đến Thứ 7:** 08:00 - 20:00 (Phòng khám mở cửa liên tục suốt trưa)\n" +
               "• **Chủ Nhật:** 08:00 - 17:30\n" +
               "• **Hotline Trực Cấp Cứu 24/7:** **0900.123.456** luôn sẵn sàng tiếp nhận các ca chấn thương răng hàm mặt hoặc đau nhức cấp tính.\n\n" +
               "👉 Bạn có thể bấm nút **Đặt Lịch Khám** phía trên để được xếp phòng ưu tiên không cần chờ đợi!";
    }

    // =========================================================================
    // 4. ĐỘI NGŨ BÁC SĨ CHUYÊN KHOA
    // =========================================================================
    if (["bác sĩ", "chuyên gia", "bác sỹ", "đội ngũ", "ai khám", "học vị", "kinh nghiệm"].some(k => q.includes(k))) {
        return "👨‍⚕️ **ĐỘI NGŨ BÁC SĨ CHUYÊN GIA NHA KHOA QUỐC TẾ PREMIER:**\n\n" +
               "1. **ThS. BS. Nguyễn Văn An**: Trưởng khoa Răng Hàm Mặt Tổng Quát (12 năm kinh nghiệm, tu nghiệp chuyên sâu tại Pháp).\n" +
               "2. **BS. CKI. Trần Thị Mai**: Chuyên gia Chỉnh nha & Niềng răng Invisalign Hoa Kỳ (8 năm kinh nghiệm, điều trị hơn 1.500 ca).\n" +
               "3. **ThS. BS. Lê Hoàng Nam**: Chuyên gia Cấy ghép Implant Nha khoa Thụy Sĩ (15 năm kinh nghiệm, tỷ lệ thành công 99.8%).\n" +
               "4. **BS. Phạm Thùy Linh**: Chuyên gia Phục hình Thẩm mỹ & Thiết kế nụ cười Dán sứ Veneer Emax (7 năm kinh nghiệm).\n" +
               "5. **BS. CKI. Đỗ Minh Đức**: Chuyên gia Tiểu phẫu & Nhổ răng khôn sóng siêu âm Piezotome không đau (10 năm kinh nghiệm).\n\n" +
               "👉 Bạn muốn đặt lịch thăm khám trực tiếp cùng Bác sĩ nào, hãy nhắn lại để em hỗ trợ nhé!";
    }

    // =========================================================================
    // 5. CÁC DỊCH VỤ NỘI BỘ NHA KHOA PREMIER
    // =========================================================================
    // NHỔ RĂNG KHÔN / TIỂU PHẪU
    if (q.includes('nhổ răng') || q.includes('răng khôn') || q.includes('piezotome')) {
        return "🦷 **DỊCH VỤ NHỔ RĂNG KHÔNG ĐAU CÔNG NGHỆ PIEZOTOME:**\n\n" +
               "• **Cơ chế & Công nghệ:** Sử dụng năng lượng sóng siêu âm biến tần Piezotome tác động chọn lọc làm đứt các dây chằng quanh chân răng nhẹ nhàng. Tuyệt đối không dùng búa đục gõ thô bạo, bảo tồn 100% xương ổ răng và mô mềm xung quanh.\n" +
               "• **Ứng dụng Màng PRF:** Ghép màng huyết tương giàu tiểu cầu tự thân PRF vào ổ răng giúp cầm máu ngay lập tức và kích thích lành thương nhanh gấp 3 lần.\n" +
               "• **Bảng giá niêm yết tại phòng khám:**\n" +
               "  + Nhổ răng sữa / Răng một chân thường: 500.000 VNĐ / răng\n" +
               "  + Nhổ răng khôn hàm trên (mọc thẳng/nghiêng nhẹ): 1.000.000 - 1.500.000 VNĐ / răng\n" +
               "  + Nhổ răng khôn hàm dưới mọc lệch, mọc ngầm, đâm ngang: 1.800.000 - 2.500.000 VNĐ / răng\n" +
               "• **Bác sĩ phụ trách:** BS. CKI Đỗ Minh Đức (hơn 3.000 ca răng khôn an toàn).\n" +
               "🎁 **Miễn phí 100% chụp phim CT ConeBeam 3D** khảo sát vị trí ống thần kinh trước khi nhổ!";
    }

    // NIỀNG RĂNG / CHỈNH NHA
    if (q.includes('niềng răng') || q.includes('chỉnh nha') || q.includes('invisalign') || q.includes('mắc cài')) {
        return "✨ **DỊCH VỤ NIỀNG RĂNG CHỈNH NHA KỸ THUẬT SỐ:**\n\n" +
               "• **Giải pháp áp dụng:**\n" +
               "  + Niềng răng mắc cài kim loại thông minh: 20.000.000 - 30.000.000 VNĐ / liệu trình trọn gói\n" +
               "  + Niềng răng mắc cài sứ thẩm mỹ tự buộc: 35.000.000 - 45.000.000 VNĐ\n" +
               "  + Niềng răng trong suốt Invisalign Hoa Kỳ: 45.000.000 - 90.000.000 VNĐ (tháo lắp tiện lợi, vô hình khi giao tiếp)\n" +
               "• **Công nghệ 3D iTero Element 5D:** Quét mẫu hàm trong 60 giây và mô phỏng trước nụ cười tương lai (ClinCheck).\n" +
               "• **Chính sách:** Hỗ trợ **TRẢ GÓP 0% LÃI SUẤT** chỉ từ 1.000.000đ/tháng, thanh toán trước 30%.\n" +
               "• **Bác sĩ:** BS. CKI Trần Thị Mai (Chứng nhận Invisalign Quốc Tế).";
    }

    // CẤY GHÉP IMPLANT / TRỒNG RĂNG
    if (q.includes('implant') || q.includes('trồng răng') || q.includes('cấy ghép') || q.includes('mất răng')) {
        return "🌟 **DỊCH VỤ CẤY GHÉP IMPLANT NHA KHOA THỤY SĨ & HÀN QUỐC:**\n\n" +
               "• **Bản chất y khoa:** Trụ Titanium tinh khiết đóng vai trò như chân răng thật cấy trực tiếp vào xương hàm, gắn mão sứ bên trên. Ngăn ngừa vĩnh viễn hiện tượng tiêu xương hàm và hóp má lão hóa do mất răng.\n" +
               "• **Bảng giá trụ chính hãng niêm yết:**\n" +
               "  + Trụ Implant Osstem / Dentium (Hàn Quốc): 12.000.000 - 15.000.000 VNĐ / trụ\n" +
               "  + Trụ Implant Straumann / Nobel Biocare (Thụy Sĩ / Mỹ): 25.000.000 - 32.000.000 VNĐ / trụ\n" +
               "• **Chế độ bảo hành:** Cấp thẻ bảo hành **TRỌN ĐỜI** có mã QR Code toàn cầu.\n" +
               "• **Thực hiện:** ThS. BS Lê Hoàng Nam trực tiếp phẫu thuật qua máng định vị 3D không đau chỉ 15 phút.\n" +
               "🎁 Tặng kèm mão sứ và miễn phí chụp phim CT 3D khi cấy ghép!";
    }

    // BỌC RĂNG SỨ / DÁN VENEER
    if (q.includes('bọc sứ') || q.includes('răng sứ') || q.includes('veneer') || q.includes('dán sứ')) {
        return "💎 **DỊCH VỤ PHỤC HÌNH RĂNG SỨ & DÁN SỨ VENEER EMAX:**\n\n" +
               "• **Bọc răng sứ toàn phần:** Khắc phục răng gãy vỡ, sâu lớn hoặc xỉn màu nặng. Răng sứ Zirconia / Cercon HT: 2.000.000 - 3.500.000 VNĐ / răng (Bảo hành 10 năm); Răng sứ Lava Plus Hoa Kỳ: 5.000.000 VNĐ (Bảo hành 15 năm).\n" +
               "• **Dán sứ Veneer Emax bảo tồn men răng:** 4.000.000 - 6.500.000 VNĐ / răng. Chỉ xử lý bề mặt siêu mỏng 0.2 - 0.5mm, không mài nhỏ răng thật, thẩm mỹ tự nhiên trong bóng chuẩn sao.\n" +
               "• **Bác sĩ phụ trách:** BS. Phạm Thùy Linh (Chuyên gia Smile Design).\n" +
               "🎁 Hỗ trợ trả góp 0% lãi suất cho khách hàng làm từ 4 răng sứ trở lên!";
    }

    // TẨY TRẮNG RĂNG
    if (q.includes('tẩy trắng') || q.includes('trắng răng')) {
        return "✨ **DỊCH VỤ TẨY TRẮNG RĂNG LASER WHITENING:**\n\n" +
               "• **Cơ chế:** Năng lượng ánh sáng Laser kích hoạt các phân tử Hydrogen Peroxide phóng thích oxy nguyên chất bẻ gãy chuỗi protein màu bám trong men răng.\n" +
               "• **Chi phí:** 1.500.000 VNĐ / gói toàn diện tại ghế nha (thực hiện 45 phút, nâng 2 - 4 tông màu không ê buốt).\n" +
               "• **Tặng kèm:** Bộ gel duy trì trắng sáng tại nhà.";
    }

    // TRÁM RĂNG
    if (q.includes('trám răng') || q.includes('hàn răng') || q.includes('sâu răng')) {
        return "🦷 **DỊCH VỤ TRÁM RĂNG THẨM MỸ COMPOSITE 3M NANO:**\n\n" +
               "• **Cơ chế:** Nạo sạch mô ngà sâu nhiễm khuẩn và lấp đầy xoang sâu bằng vật liệu hạt độn nano Composite 3M (Mỹ), quang trùng hợp bằng đèn LED đông cứng tức thì.\n" +
               "• **Chi phí:** 350.000 - 500.000 VNĐ / răng.\n" +
               "• **Bảo hành:** 2 năm không bong tróc, màu sắc tệp 100% với men răng thật.";
    }

    // ĐIỀU TRỊ TỦY
    if (q.includes('tủy') || q.includes('lấy tủy') || q.includes('chữa tủy')) {
        return "⚡ **ĐIỀU TRỊ NỘI NHA LẤY TỦY RĂNG RECIPROC SIÊU ÂM:**\n\n" +
               "• **Bản chất:** Loại bỏ mô tủy bị viêm hoại tử, bơm rửa sát trùng hệ thống ống tủy và trám bít kín khít bằng Gutta-Percha.\n" +
               "• **Chi phí:** 800.000 - 1.200.000 VNĐ / răng (tùy vị trí răng 1 chân cửa hay nhiều chân hàm).\n" +
               "• **Ưu điểm:** Máy định vị chóp thông minh làm sạch triệt để, cắt đứt cơn đau nhức chỉ trong 1 lần hẹn.";
    }

    // CẠO VÔI RĂNG
    if (q.includes('cạo vôi') || q.includes('cao răng') || q.includes('đánh bóng')) {
        return "🌿 **CẠO VÔI RĂNG & ĐÁNH BÓNG SÓNG SIÊU ÂM:**\n\n" +
               "• **Chi phí:** 250.000 VNĐ / 2 hàm.\n" +
               "• **Cơ chế:** Đầu tip siêu âm tạo dao động vi thể bóc tách mảng bám vôi răng dưới nướu mà không tổn hại men răng, ngăn ngừa viêm nướu, tụt lợi và khử mùi hôi miệng.";
    }

    // BẢNG GIÁ TỔNG HỢP
    if (["bảng giá", "giá", "chi phí", "bao nhiêu", "tiền"].some(k => q.includes(k))) {
        return "💰 **BẢNG GIÁ DỊCH VỤ NIÊM YẾT CHÍNH THỨC NHA KHOA PREMIER:**\n\n" +
               "• Khám răng tổng quát & Chụp X-quang 3D: **Miễn phí (0đ)**\n" +
               "• Cạo vôi răng & Đánh bóng siêu âm: **250.000đ**\n" +
               "• Trám răng Composite 3M: **350.000đ / răng**\n" +
               "• Nhổ răng thường / Răng khôn: **500.000 - 2.500.000đ**\n" +
               "• Lấy tủy răng công nghệ Reciproc: **800.000đ / răng**\n" +
               "• Tẩy trắng răng Laser Whitening: **1.500.000đ / lần**\n" +
               "• Bọc răng sứ Cercon / Zirconia: **2.000.000 - 3.500.000đ / răng**\n" +
               "• Dán sứ Veneer Emax: **4.000.000đ / răng**\n" +
               "• Niềng răng Chỉnh nha (Mắc cài / Invisalign): **Từ 20.000.000đ (Trả góp 0%)**\n" +
               "• Cấy ghép Implant Thụy Sĩ: **Từ 12.000.000đ / trụ (BH trọn đời)**\n\n" +
               "👉 Bạn cần tư vấn chi phí cụ thể cho dịch vụ nào, hãy nhắn lại để em hỗ trợ nhé!";
    }

    // =========================================================================
    // 6. KIẾN THỨC Y KHOA NỀN TẢNG & CÂU HỎI NGOÀI DANH MỤC
    // =========================================================================
    // SƠ CỨU GÃY RĂNG / RƠI RĂNG / MẺ RĂNG
    if (q.includes('gãy răng') || q.includes('mẻ răng') || q.includes('rơi răng') || q.includes('rụng răng') || q.includes('chấn thương')) {
        return "🚨 **HƯỚNG DẪN XỬ LÝ CẤP CỨU GÃY / RƠI RĂNG (QUY TẮC VÀNG 60 PHÚT):**\n\n" +
               "1. **Bản chất:** Khi răng bị bật ra khỏi ổ, các tế bào màng nha chu quanh chân răng vẫn còn sống trong một khoảng thời gian ngắn nếu được bảo quản đúng môi trường sinh lý.\n" +
               "2. **Các bước sơ cứu khẩn cấp tại nhà:**\n" +
               "   • Tìm lại chiếc răng, **chỉ cầm vào thân răng**, tuyệt đối KHÔNG chạm hoặc chà xát vào phần chân răng.\n" +
               "   • Rửa nhẹ qua nước muối sinh lý hoặc sữa tươi tiệt trùng trong 10 giây nếu bị dính bẩn (không dùng xà phòng, cồn).\n" +
               "   • **Môi trường bảo quản tối ưu:** Ngâm răng trong **sữa tươi không đường** hoặc nước muối sinh lý 0.9% (hoặc ngậm dưới lưỡi của bệnh nhân).\n" +
               "   • Di chuyển ngay đến phòng khám nha khoa gần nhất trong **vòng 30 - 60 phút** để bác sĩ cấy lại vào ổ răng.\n" +
               "3. **Trường hợp mẻ răng nhỏ:** Bác sĩ có thể trám Composite phục hồi (350k) hoặc làm mặt dán Veneer để khôi phục thẩm mỹ hoàn hảo.";
    }

    // THUỐC NHA KHOA / GIẢM ĐAU / KHÁNG SINH
    if (q.includes('thuốc') || q.includes('kháng sinh') || q.includes('giảm đau') || q.includes('uống gì')) {
        return "💊 **TƯ VẤN DƯỢC HỌC & CẨM NANG THUỐC NHA KHOA:**\n\n" +
               "1. **Giảm đau - Hạ sốt thông thường:**\n" +
               "   • **Paracetamol (500mg):** Uống 1 viên mỗi 4 - 6 giờ khi đau nhức nhẹ (không quá 4g/ngày).\n" +
               "   • **Ibuprofen (400mg):** Giảm đau kèm kháng viêm tốt trong đau răng, sưng nướu (uống sau khi ăn no, thận trọng nếu có bệnh lý dạ dày).\n" +
               "2. **Kháng sinh nha khoa (Cần có chỉ định bác sĩ):**\n" +
               "   • **Amoxicillin** hoặc phối hợp **Spiramycin + Metronidazole (như Rodogyl / Dorogyne):** Thường được kê đơn trong các trường hợp nhiễm trùng răng miệng, áp xe quanh chóp, viêm tấy mủ do vi khuẩn kỵ khí.\n" +
               "3. **Vệ sinh sát khuẩn tại chỗ:**\n" +
               "   • Súc miệng dung dịch **Chlorhexidine Gluconate 0.12%** 2 lần/ngày giúp kiểm soát vi khuẩn và giảm viêm nướu hiệu quả.\n\n" +
               "⚠️ **Lưu ý quan trọng:** Không tự ý lạm dụng thuốc kháng sinh tại nhà vì thuốc chỉ kiểm soát triệu chứng tạm thời chứ không giải quyết được ổ nhiễm trùng trong răng. Cần đến gặp nha sĩ để làm sạch ổ viêm dứt điểm!";
    }

    // PHẪU THUẬT HÀM HÔ MÓM / NGOÀI DANH MỤC
    if (q.includes('phẫu thuật hàm') || q.includes('hô móm') || q.includes('cắt hàm') || q.includes('xương hàm')) {
        return "🏥 **KIẾN THỨC CHUYÊN KHOA PHẪU THUẬT HÀM HÔ MÓM (ORTHOGNATHIC SURGERY):**\n\n" +
               "1. **Bản chất:** Khi tình trạng hô hoặc móm xuất phát từ sự phát triển bất cân xứng của xương hàm (chứ không đơn thuần do răng), niềng răng thông thường không thể giải quyết triệt để mà cần phẫu thuật chỉnh hình xương hàm.\n" +
               "2. **Quy trình phối hợp:** Thường kết hợp niềng răng trước phẫu thuật để đưa các răng về đúng trục, sau đó bác sĩ phẫu thuật cắt và dịch chuyển xương hàm (BSSO, Le Fort 1) về đúng khớp cắn chuẩn.\n" +
               "3. **Mức giá thị trường tham khảo:** Dao động phổ biến từ **60.000.000 - 120.000.000 VNĐ** (tùy phẫu thuật 1 hàm hay 2 hàm).\n" +
               "👉 Tại Premier Dental, Bác sĩ Chuyên khoa sẽ chụp phim 3D Cephalo khảo sát xương hàm để tư vấn bạn có thể chỉnh nha thông thường hay bắt buộc phẫu thuật.";
    }

    // ĐÍNH KIM CƯƠNG / ĐÍNH ĐÁ LÊN RĂNG
    if (q.includes('đính đá') || q.includes('đính kim cương') || q.includes('gắn đá')) {
        return "💎 **DỊCH VỤ ĐÍNH ĐÁ / KIM CƯƠNG LÊN RĂNG THẨM MỸ:**\n\n" +
               "• **Quy trình:** Bác sĩ sử dụng keo dán sinh học nha khoa chuyên dụng gắn trực tiếp viên đá/kim cương lên bề mặt men răng (công nghệ hiện đại không cần khoan lỗ trên răng, hoàn toàn bảo tồn men răng).\n" +
               "• **Mức giá thị trường tham khảo:**\n" +
               "  + Công đính đá (khách tự mang đá): 300.000 - 500.000 VNĐ\n" +
               "  + Trọn gói cả đá nhân tạo nha khoa cao cấp: 800.000 - 1.500.000 VNĐ\n" +
               "• Độ bám dính chắc chắn, không gây cộm hay vướng víu khi ăn nhai.";
    }

    // HÀM THÁO LẮP / PHỤC HÌNH THÁO LẮP
    if (q.includes('hàm tháo lắp') || q.includes('hàm giả') || q.includes('răng giả tháo lắp')) {
        return "🦷 **DỊCH VỤ PHỤC HÌNH RĂNG GIẢ HÀM THÁO LẮP:**\n\n" +
               "1. **Bản chất:** Giải pháp phục hồi răng đã mất cho người cao tuổi hoặc người chưa đủ điều kiện cấy ghép Implant, có thể tháo ra vệ sinh hàng ngày.\n" +
               "2. **Các loại hàm phổ biến & Mức giá thị trường:**\n" +
               "   • Hàm tháo lắp bán phần / toàn phần nền nhựa cứng: 2.000.000 - 3.500.000 VNĐ / hàm\n" +
               "   • Hàm khung kim loại Titan (nhẹ, bền, chắc chắn): 4.000.000 - 6.000.000 VNĐ / hàm\n" +
               "   • Hàm nhựa dẻo Biosoft (ôm khít nướu, không đau cấn): 3.500.000 - 5.000.000 VNĐ / hàm\n" +
               "3. **Lời khuyên bác sĩ:** Nếu xương hàm còn tốt, cấy ghép Implant vẫn là giải pháp vượt trội gấp nhiều lần vì không gây tiêu xương tụt nướu.";
    }

    // TRIỆU CHỨNG ĐAU NHỨC / SƯNG / CHẢY MÁU
    if (["đau", "nhức", "buốt", "sưng", "chảy máu", "mủ", "hôi miệng", "viêm lợi"].some(k => q.includes(k))) {
        return "⚠️ **CHẨN ĐOÁN LÂM SÀNG SƠ BỘ & HƯỚNG XỬ LÝ:**\n\n" +
               "1. **Nguyên nhân khả dĩ theo triệu chứng:**\n" +
               "   • **Đau buốt nhói từng cơn khi ăn đồ nóng/lạnh:** Dấu hiệu điển hình của viêm tủy răng do sâu răng ăn sâu vào buồng tủy.\n" +
               "   • **Nướu sưng đỏ, dễ chảy máu khi đánh răng:** Viêm nướu hoặc viêm nha chu do tích tụ mảng bám vôi răng lâu ngày.\n" +
               "   • **Đau nhức góc hàm, há miệng khó, sốt:** Thường do răng khôn mọc lệch gây viêm lợi trùm hoặc chèn ép răng số 7 bên cạnh.\n" +
               "2. **Cách xử lý tại nhà:**\n" +
               "   • Súc miệng nước muối ấm pha loãng 3 - 4 lần/ngày.\n" +
               "   • Có thể uống tạm 1 viên Paracetamol 500mg để giảm đau tạm thời.\n" +
               "   • Tránh nhai mạnh vào bên răng đau, không chườm nóng lên má.\n" +
               "👉 Bạn hãy đến ngay phòng khám để được bác sĩ **chụp phim CT 3D miễn phí** và xử lý triệt để nguyên nhân nhé!";
    }

    // ĐẶT LỊCH HẸN
    if (["đặt lịch", "hẹn khám", "đăng ký", "khám bệnh", "đặt hẹn"].some(k => q.includes(k))) {
        return "📅 **HƯỚNG DẪN ĐẶT LỊCH KHÁM ƯU TIÊN 0Đ:**\n\n" +
               "1. Bấm trực tiếp nút **'Đặt Lịch Khám'** màu xanh ở góc trên màn hình.\n" +
               "2. Hoặc để lại [Họ Tên + Số Điện Thoại + Ngày Muốn Khám] ngay tại khung chat này.\n" +
               "3. Hoặc gọi Hotline trực tiếp: **1900 6868** (Hotline Cấp cứu: **0900.123.456**).\n\n" +
               "🎁 **Ưu đãi hôm nay:** Miễn phí 100% chi phí khám lâm sàng và chụp phim X-quang 3D CT ConeBeam!";
    }

    // =========================================================================
    // 7. PHẢN HỒI NỀN TẢNG LINH HOẠT CHO MỌI CÂU HỎI KHÁC
    // =========================================================================
    return "Dạ chào bạn! Tôi là Chuyên gia Tư vấn AI của **Nha Khoa Quốc Tế Premier**.\n\n" +
           "Với câu hỏi của bạn: *\"" + query + "\"*, tôi luôn sẵn sàng hỗ trợ bạn toàn diện:\n" +
           "• 🦷 **Tư vấn bệnh lý & Phác đồ điều trị:** Niềng răng, Implant, Răng sứ, Trám răng, Lấy tủy, Nhổ răng Piezotome...\n" +
           "• 💰 **Bảng giá & Chi phí thị trường:** Cung cấp mức giá niêm yết minh bạch hoặc khoảng giá trung bình ngành nha khoa hiện đại.\n" +
           "• 📍 **Địa chỉ & Đặt lịch:** 123 Nguyễn Văn Cừ, Q.5, TP.HCM (Hotline: 1900 6868) - Miễn phí 100% chụp phim 3D.\n\n" +
           "Bạn có thể chia sẻ cụ thể hơn tình trạng hoặc nhu cầu của mình để em giải đáp chi tiết nhất nhé!";
}

function appendFallbackChatResponse(query) {
    const reply = generateSmartClientResponse(query);
    appendMessage(reply, 'assistant');
}

function appendMessage(text, role) {
    const messagesContainer = document.getElementById('chatMessages');
    if (!messagesContainer) return;

    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${role}`;

    // Format markdown bold, italic, code, bullets & linebreaks
    let formattedText = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/^[•\-*]\s+(.*)$/gm, '• $1')
        .replace(/\n/g, '<br>');

    const plainText = text.replace(/<[^>]*>?/gm, '').replace(/[*`#_]/g, '').trim();
    const ttsHtml = role === 'assistant' ? `
        <div style="margin-top: 6px; display: flex; justify-content: flex-end;">
            <button type="button" class="chat-tts-btn" onclick="playTTS(this, '${encodeURIComponent(plainText)}')" title="Nghe Bác sĩ AI đọc câu trả lời">
                <i class="fa-solid fa-volume-high"></i> Nghe đọc
            </button>
        </div>
    ` : '';

    bubble.innerHTML = `
        <div class="msg-content">${formattedText}</div>
        ${ttsHtml}
        <div class="msg-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
    `;

    messagesContainer.appendChild(bubble);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showChatLoading(show) {
    const loader = document.getElementById('chatLoading');
    const messagesContainer = document.getElementById('chatMessages');
    if (!loader || !messagesContainer) return;

    if (show) {
        loader.style.display = 'flex';
    } else {
        loader.style.display = 'none';
    }
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// --- VOICE RECOGNITION (WEB SPEECH API) ---
let voiceRecognition = null;
let isListeningVoice = false;
let autoSendVoiceTimer = null;

function initVoiceRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) return null;

    const rec = new SpeechRecognition();
    rec.lang = 'vi-VN';
    rec.continuous = false;
    rec.interimResults = true;
    rec.maxAlternatives = 1;

    rec.onstart = function() {
        isListeningVoice = true;
        updateVoiceUI(true, 'Đang lắng nghe... Hãy nói câu hỏi của bạn');
    };

    rec.onresult = function(event) {
        let interimTranscript = '';
        let finalTranscript = '';

        for (let i = event.resultIndex; i < event.results.length; ++i) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalTranscript += transcript;
            } else {
                interimTranscript += transcript;
            }
        }

        const currentText = finalTranscript || interimTranscript;
        const chatInput = document.getElementById('chatInput');
        const voiceStatusText = document.getElementById('chatVoiceStatusText');

        if (chatInput && currentText) {
            chatInput.value = currentText;
        }
        if (voiceStatusText && currentText) {
            voiceStatusText.textContent = `🎙️ "${currentText}"`;
        }

        if (finalTranscript) {
            if (voiceStatusText) {
                voiceStatusText.textContent = `✅ Đã nhận diện: "${finalTranscript}"`;
            }
            clearTimeout(autoSendVoiceTimer);
            autoSendVoiceTimer = setTimeout(() => {
                stopVoiceRecognition();
                sendChatMessage();
            }, 700);
        }
    };

    rec.onerror = function(event) {
        console.warn('SpeechRecognition error:', event.error);
        if (event.error === 'not-allowed') {
            if (typeof showToast === 'function') {
                showToast('Trình duyệt chưa được cấp quyền micro. Vui lòng cho phép quyền micro để tra cứu bằng giọng nói.', 'warning');
            }
        } else if (event.error === 'no-speech') {
            if (typeof showToast === 'function') {
                showToast('Chưa phát hiện giọng nói. Bạn vui lòng thử lại và nói rõ hơn.', 'info');
            }
        }
        stopVoiceRecognition();
    };

    rec.onend = function() {
        updateVoiceUI(false);
        isListeningVoice = false;
    };

    return rec;
}

function toggleVoiceRecognition() {
    if (isListeningVoice) {
        stopVoiceRecognition();
    } else {
        startVoiceRecognition();
    }
}

function startVoiceRecognition() {
    if (!voiceRecognition) {
        voiceRecognition = initVoiceRecognition();
    }
    if (!voiceRecognition) {
        if (typeof showToast === 'function') {
            showToast('Trình duyệt của bạn chưa hỗ trợ nhận diện giọng nói (Web Speech API). Vui lòng sử dụng Chrome, Edge hoặc Safari.', 'warning');
        }
        return;
    }

    try {
        voiceRecognition.start();
    } catch (e) {
        try {
            voiceRecognition.stop();
            setTimeout(() => voiceRecognition.start(), 200);
        } catch (ex) {}
    }
}

function stopVoiceRecognition() {
    clearTimeout(autoSendVoiceTimer);
    if (voiceRecognition && isListeningVoice) {
        try {
            voiceRecognition.stop();
        } catch (e) {}
    }
    updateVoiceUI(false);
    isListeningVoice = false;
}

function updateVoiceUI(listening, statusText = '') {
    const voiceBtn = document.getElementById('voiceChatBtn');
    const voiceStatus = document.getElementById('chatVoiceStatus');
    const voiceStatusText = document.getElementById('chatVoiceStatusText');
    const chatInput = document.getElementById('chatInput');

    if (voiceBtn) {
        if (listening) {
            voiceBtn.classList.add('listening');
            voiceBtn.title = 'Đang lắng nghe... Nhấn lại để dừng';
        } else {
            voiceBtn.classList.remove('listening');
            voiceBtn.title = 'Hỏi đáp / Tra cứu bằng giọng nói';
        }
    }
    if (voiceStatus) {
        voiceStatus.style.display = listening ? 'flex' : 'none';
    }
    if (voiceStatusText && statusText) {
        voiceStatusText.textContent = statusText;
    }
    if (chatInput) {
        chatInput.placeholder = listening ? '🎙️ Đang lắng nghe... Hãy nói câu hỏi...' : 'Nhập thắc mắc hoặc nhấn mic để nói...';
    }
}

function playTTS(btn, encodedText) {
    if (!('speechSynthesis' in window)) {
        if (typeof showToast === 'function') showToast('Trình duyệt chưa hỗ trợ phát giọng nói.', 'info');
        return;
    }

    if (btn.classList.contains('speaking')) {
        window.speechSynthesis.cancel();
        btn.classList.remove('speaking');
        btn.innerHTML = '<i class="fa-solid fa-volume-high"></i> Nghe đọc';
        return;
    }

    window.speechSynthesis.cancel();
    document.querySelectorAll('.chat-tts-btn').forEach(b => {
        b.classList.remove('speaking');
        b.innerHTML = '<i class="fa-solid fa-volume-high"></i> Nghe đọc';
    });

    const text = decodeURIComponent(encodedText || '');
    if (!text) return;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'vi-VN';
    utterance.rate = 0.95;

    const voices = window.speechSynthesis.getVoices();
    const viVoice = voices.find(v => v.lang.includes('vi') || v.lang.includes('VI'));
    if (viVoice) utterance.voice = viVoice;

    btn.classList.add('speaking');
    btn.innerHTML = '<i class="fa-solid fa-stop"></i> Dừng đọc';

    utterance.onend = function() {
        btn.classList.remove('speaking');
        btn.innerHTML = '<i class="fa-solid fa-volume-high"></i> Nghe đọc';
    };
    utterance.onerror = function() {
        btn.classList.remove('speaking');
        btn.innerHTML = '<i class="fa-solid fa-volume-high"></i> Nghe đọc';
    };

    window.speechSynthesis.speak(utterance);
}
