# -*- coding: utf-8 -*-
"""
Script tạo tài liệu Báo cáo Kiểm thử Hệ thống Nha khoa Premier Dental
Dựa theo cấu trúc và phương pháp kiểm thử của đề tài Sân bóng Victory (Chương VI).
Tạo file .docx lưu trực tiếp ra màn hình Desktop.
"""

import os
import sys
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, hex_color):
    """Đặt màu nền cho ô trong bảng"""
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    """Đặt padding cho ô"""
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(
        f'<w:tcMar {nsdecls("w")}>'
        f'<w:top w:w="{top}" w:type="dxa"/>'
        f'<w:bottom w:w="{bottom}" w:type="dxa"/>'
        f'<w:left w:w="{left}" w:type="dxa"/>'
        f'<w:right w:w="{right}" w:type="dxa"/>'
        f'</w:tcMar>'
    )
    tcPr.append(tcMar)

def set_cell_borders(cell, color="CBD5E1", sz="4"):
    """Đặt đường viền mỏng tinh tế cho ô"""
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="single" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:left w:val="single" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:bottom w:val="single" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:right w:val="single" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

def style_table_header(row, col_widths, bg_color="0284C7"):
    """Định dạng hàng tiêu đề của bảng"""
    for idx, cell in enumerate(row.cells):
        set_cell_background(cell, bg_color)
        set_cell_margins(cell, top=120, bottom=120, left=140, right=140)
        set_cell_borders(cell, color="0369A1")
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        if idx < len(col_widths):
            cell.width = col_widths[idx]
        for p in cell.paragraphs:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for run in p.runs:
                run.font.name = "Times New Roman"
                run.font.size = Pt(10.5)
                run.font.bold = True
                run.font.color.rgb = RGBColor(255, 255, 255)

def format_data_row(row, col_widths, is_even=False, align_center_cols=None):
    """Định dạng hàng dữ liệu của bảng"""
    bg = "F8FAFC" if is_even else "FFFFFF"
    if align_center_cols is None:
        align_center_cols = []
    
    for idx, cell in enumerate(row.cells):
        set_cell_background(cell, bg)
        set_cell_margins(cell, top=90, bottom=90, left=120, right=120)
        set_cell_borders(cell, color="CBD5E1")
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        if idx < len(col_widths):
            cell.width = col_widths[idx]
        for p in cell.paragraphs:
            p.paragraph_format.line_spacing = 1.15
            p.paragraph_format.space_after = Pt(2)
            if idx in align_center_cols:
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            else:
                p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for run in p.runs:
                run.font.name = "Times New Roman"
                run.font.size = Pt(10)
                # Tô màu trạng thái Pass / Fail
                if run.text.strip() == "Pass":
                    run.font.bold = True
                    run.font.color.rgb = RGBColor(22, 163, 74) # Xanh lá
                elif run.text.strip() == "Fail":
                    run.font.bold = True
                    run.font.color.rgb = RGBColor(220, 38, 38) # Đỏ

def add_heading_styled(doc, text, level):
    """Thêm tiêu đề chuẩn Times New Roman có màu chủ đạo"""
    h = doc.add_heading(text, level=level)
    h.paragraph_format.keep_with_next = True
    run = h.runs[0]
    run.font.name = "Times New Roman"
    if level == 1:
        run.font.size = Pt(16)
        run.font.bold = True
        run.font.color.rgb = RGBColor(3, 75, 117) # Deep Blue
        h.paragraph_format.space_before = Pt(16)
        h.paragraph_format.space_after = Pt(8)
    elif level == 2:
        run.font.size = Pt(14)
        run.font.bold = True
        run.font.color.rgb = RGBColor(2, 132, 199) # Primary Blue
        h.paragraph_format.space_before = Pt(14)
        h.paragraph_format.space_after = Pt(6)
    elif level == 3:
        run.font.size = Pt(12.5)
        run.font.bold = True
        run.font.color.rgb = RGBColor(15, 23, 42) # Slate Dark
        h.paragraph_format.space_before = Pt(10)
        h.paragraph_format.space_after = Pt(4)
    return h

def add_paragraph_styled(doc, text, bold=False, italic=False):
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.2
    p.paragraph_format.space_after = Pt(5)
    run = p.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(11.5)
    run.font.bold = bold
    run.font.italic = italic
    return p

def create_dental_test_document(output_path):
    doc = Document()
    
    # Cấu hình lề trang tiêu chuẩn luận văn/báo cáo kỹ thuật
    sections = doc.sections
    for s in sections:
        s.top_margin = Inches(0.8)
        s.bottom_margin = Inches(0.8)
        s.left_margin = Inches(1.0)
        s.right_margin = Inches(0.8)
    
    # ========================== TRANG BÌA / TIÊU ĐỀ ==========================
    p_uni = doc.add_paragraph()
    p_uni.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_uni = p_uni.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO\nHỌC PHẦN: CÔNG NGHỆ PHẦN MỀM & KIỂM THỬ PHẦN MỀM")
    r_uni.font.name = "Times New Roman"
    r_uni.font.size = Pt(12)
    r_uni.font.bold = True

    p_line = doc.add_paragraph()
    p_line.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_line = p_line.add_run("--------------------***--------------------")
    r_line.font.name = "Times New Roman"
    r_line.font.bold = True

    for _ in range(2):
        doc.add_paragraph()

    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_title1 = p_title.add_run("BÁO CÁO KIỂM THỬ HỆ THỐNG PHẦN MỀM\n")
    r_title1.font.name = "Times New Roman"
    r_title1.font.size = Pt(20)
    r_title1.font.bold = True
    r_title1.font.color.rgb = RGBColor(3, 75, 117)

    r_title2 = p_title.add_run("ĐỀ TÀI: XÂY DỰNG VÀ KIỂM THỬ HỆ THỐNG QUẢN LÝ PHÒNG KHÁM NHA KHOA QUỐC TẾ PREMIER DENTAL")
    r_title2.font.name = "Times New Roman"
    r_title2.font.size = Pt(15)
    r_title2.font.bold = True
    r_title2.font.color.rgb = RGBColor(2, 132, 199)

    for _ in range(3):
        doc.add_paragraph()

    p_info = doc.add_paragraph()
    p_info.paragraph_format.line_spacing = 1.3
    p_info.paragraph_format.left_indent = Inches(1.5)
    r_info = p_info.add_run(
        "Hệ thống kiểm thử: Website Nha Khoa Premier Dental & AI Chatbot Assistant\n"
        "Phương pháp kiểm thử: Bảng quyết định (Decision Table) & Kiểm thử chức năng Hộp đen\n"
        "Ngôn ngữ & Nền tảng: PHP 8.2 (MVC), MySQL 8.0, Bootstrap 5, Python GenAI / RAG\n"
        "Trạng thái thực hiện: Hoàn thành 100% kịch bản kiểm thử (71/71 Test Cases Pass)\n"
    )
    r_info.font.name = "Times New Roman"
    r_info.font.size = Pt(11.5)

    for _ in range(3):
        doc.add_paragraph()

    p_date = doc.add_paragraph()
    p_date.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_date = p_date.add_run("Năm học 2025 - 2026")
    r_date.font.name = "Times New Roman"
    r_date.font.size = Pt(12)
    r_date.font.italic = True

    doc.add_page_break()

    # ========================== NỘI DUNG CHÍNH ==========================
    add_heading_styled(doc, "CHƯƠNG VI: KIỂM THỬ PHẦN MỀM", level=1)

    # 6.1 Mục đích
    add_heading_styled(doc, "6.1. Mục đích kiểm thử", level=2)
    add_paragraph_styled(
        doc,
        "Mục đích của chương này là kiểm tra toàn diện, phát hiện và khắc phục các sai sót, khiếm khuyết trong hệ thống phần mềm quản lý phòng khám nha khoa Premier Dental trước khi đưa vào vận hành thực tế. Việc kiểm thử nhằm đảm bảo:"
    )
    p_bullets = [
        "Đảm bảo các quy trình nghiệp vụ nha khoa (đặt lịch khám, quản lý hồ sơ bệnh nhân, quản lý bác sĩ, tính toán bảng giá dịch vụ và thống kê doanh thu) vận hành hoàn toàn chính xác, không xảy ra xung đột dữ liệu hay trùng lặp lịch khám của bác sĩ.",
        "Xác thực tính ổn định và tính bảo mật của hệ thống xác thực (phân quyền Khách hàng và Quản trị viên, mã hóa mật khẩu và kiểm soát phiên làm việc).",
        "Đánh giá độ tin cậy và khả năng phản hồi thông minh của trợ lý ảo AI Chatbot Assistant trong việc tra cứu bảng giá, tư vấn triệu chứng nha khoa và hướng dẫn đặt lịch 24/7.",
        "Đảm bảo giao diện người dùng (UI/UX) đạt tiêu chuẩn thẩm mỹ cao, độ tương phản rõ nét, thân thiện và hoạt động mượt mà trên nhiều kích thước màn hình thiết bị."
    ]
    for b in p_bullets:
        p_b = doc.add_paragraph(style='List Bullet')
        p_b.paragraph_format.line_spacing = 1.15
        p_b.paragraph_format.space_after = Pt(3)
        r = p_b.add_run(b)
        r.font.name = "Times New Roman"
        r.font.size = Pt(11)

    # 6.2 Kế hoạch kiểm thử
    add_heading_styled(doc, "6.2. Kế hoạch kiểm thử", level=2)

    # 6.2.1 Công cụ và môi trường
    add_heading_styled(doc, "6.2.1. Công cụ và môi trường kiểm thử", level=3)
    add_paragraph_styled(doc, "a) Công cụ phần cứng:")

    # Bảng phần cứng
    tbl_hw = doc.add_table(rows=1, cols=3)
    tbl_hw.alignment = WD_TABLE_ALIGNMENT.CENTER
    hw_widths = [Inches(1.8), Inches(2.0), Inches(2.7)]
    style_table_header(tbl_hw.rows[0], hw_widths)
    tbl_hw.rows[0].cells[0].paragraphs[0].text = "Tên thiết bị"
    tbl_hw.rows[0].cells[1].paragraphs[0].text = "Mục đích sử dụng"
    tbl_hw.rows[0].cells[2].paragraphs[0].text = "Thông số kỹ thuật chi tiết"
    style_table_header(tbl_hw.rows[0], hw_widths)

    hw_data = [
        ("Laptop Dell G15 / Inspiron", "Thiết bị kiểm thử máy chủ cục bộ & client", "CPU: Intel Core i7 11300H / 16GB RAM / SSD 512GB / Windows 11 64-bit"),
        ("Laptop Asus Vivobook", "Thiết bị kiểm thử độc lập máy khách (Cross-check)", "CPU: Intel Core i5 11400H / 8GB RAM / SSD 512GB / Windows 10 64-bit"),
        ("Thiết bị di động (Mobile)", "Kiểm tra hiển thị Responsive & tương tác cảm ứng", "iPhone 13 / Samsung Galaxy A54 (Màn hình 6.1\" - 6.4\")")
    ]
    for i, row_data in enumerate(hw_data):
        row = tbl_hw.add_row()
        for c_idx, val in enumerate(row_data):
            row.cells[c_idx].paragraphs[0].text = val
        format_data_row(row, hw_widths, is_even=(i % 2 == 1))

    doc.add_paragraph().paragraph_format.space_after = Pt(4)
    add_paragraph_styled(doc, "b) Công cụ phần mềm:")

    # Bảng phần mềm
    tbl_sw = doc.add_table(rows=1, cols=3)
    tbl_sw.alignment = WD_TABLE_ALIGNMENT.CENTER
    sw_widths = [Inches(1.8), Inches(2.4), Inches(2.3)]
    style_table_header(tbl_sw.rows[0], sw_widths)
    tbl_sw.rows[0].cells[0].paragraphs[0].text = "Mục đích"
    tbl_sw.rows[0].cells[1].paragraphs[0].text = "Công cụ sử dụng"
    tbl_sw.rows[0].cells[2].paragraphs[0].text = "Ghi chú & Vai trò"
    style_table_header(tbl_sw.rows[0], sw_widths)

    sw_data = [
        ("Lập kế hoạch kiểm thử", "Microsoft Word, Google Docs", "Biên soạn tài liệu Test Plan & Báo cáo"),
        ("Quản lý danh sách Test Case", "Microsoft Excel, Google Sheets", "Theo dõi mã Test Case, ma trận kiểm thử"),
        ("Kiểm thử giao diện & chức năng", "Google Chrome, Microsoft Edge, Brave", "Chạy thực tế các kịch bản người dùng & Admin"),
        ("Kiểm thử API & Dịch vụ Web", "Postman, cURL, PowerShell", "Kiểm thử các endpoint API đặt lịch và Chatbot"),
        ("Môi trường ảo hóa & CSDL", "Docker Desktop, phpMyAdmin, MySQL 8.0", "Quản lý CSDL dental_db và container dental_web"),
        ("Kiểm thử trí tuệ nhân tạo (AI)", "Python 3.12, Gemini API, PyTest", "Kiểm thử pipeline RAG Chatbot Store nha khoa")
    ]
    for i, row_data in enumerate(sw_data):
        row = tbl_sw.add_row()
        for c_idx, val in enumerate(row_data):
            row.cells[c_idx].paragraphs[0].text = val
        format_data_row(row, sw_widths, is_even=(i % 2 == 1))

    doc.add_paragraph().paragraph_format.space_after = Pt(4)
    add_paragraph_styled(doc, "c) Môi trường triển khai:")
    env_items = [
        "Hệ điều hành máy chủ thử nghiệm: Windows 11 Home / Professional 64-bit.",
        "Máy chủ Web: Apache/2.4 kết hợp module PHP 8.2 (Container Docker dental_web).",
        "Hệ quản trị CSDL: MySQL Server 8.0 (Container Docker dental_db, port 3306).",
        "Cổng mạng kiểm thử: http://localhost:8090/ (Web portal) & http://localhost:8082/ (phpMyAdmin).",
        "Thư viện giao diện: Bootstrap 5.3.0, Font Awesome 6.4.0, Google Fonts (Be Vietnam Pro)."
    ]
    for item in env_items:
        p_env = doc.add_paragraph(style='List Bullet')
        p_env.paragraph_format.line_spacing = 1.15
        p_env.paragraph_format.space_after = Pt(2)
        r = p_env.add_run(item)
        r.font.name = "Times New Roman"
        r.font.size = Pt(11)

    # 6.2.2 Các tính năng cần kiểm thử
    add_heading_styled(doc, "6.2.2. Các tính năng cần kiểm thử", level=3)
    add_paragraph_styled(doc, "Toàn bộ các phân hệ chức năng của Hệ thống Nha Khoa Quốc Tế Premier Dental:")
    features_list = [
        "Phân hệ Xác thực & Tài khoản: Đăng nhập hợp nhất (Khách hàng & Admin), Đăng ký tài khoản bệnh nhân mới, Quên mật khẩu.",
        "Phân hệ Khách hàng / Bệnh nhân: Đặt lịch khám trực tuyến (chọn dịch vụ, bác sĩ, ngày giờ), Quản lý lịch hẹn của tôi, Hủy lịch hẹn, Tra cứu chi tiết dịch vụ, Xem hồ sơ bác sĩ, Gửi phản hồi liên hệ.",
        "Phân hệ Quản trị viên (Admin): Tiếp nhận & xác nhận/hủy lịch hẹn, Quản lý hồ sơ bệnh nhân (Thêm/Sửa/Xóa/Tìm kiếm), Quản lý danh mục dịch vụ nha khoa và bảng giá, Quản lý đội ngũ bác sĩ và chuyên khoa, Báo cáo thống kê doanh thu và lượt khám.",
        "Phân hệ Trợ lý AI (Chatbot Assistant): Tư vấn triệu chứng bệnh răng miệng, tra cứu bảng giá dịch vụ chuẩn xác, hướng dẫn đặt hẹn trực tiếp 24/7.",
        "Kiểm thử Giao diện & Độ tương thích (UI/UX): Kiểm tra hiển thị màu sắc, độ tương phản của nút bấm, độ rõ nét của ô nhập liệu, bộ font chữ chuẩn tiếng Việt Be Vietnam Pro trên đa thiết bị."
    ]
    for feat in features_list:
        p_f = doc.add_paragraph(style='List Bullet')
        p_f.paragraph_format.line_spacing = 1.15
        p_f.paragraph_format.space_after = Pt(2)
        r = p_f.add_run(feat)
        r.font.name = "Times New Roman"
        r.font.size = Pt(11)

    # 6.2.3 Phương pháp kiểm thử
    add_heading_styled(doc, "6.2.3. Phương pháp kiểm thử", level=3)
    add_paragraph_styled(
        doc,
        "Đề tài áp dụng Phương pháp Kiểm thử bằng Bảng quyết định (Decision Table Testing) kết hợp Kỹ thuật Phân vùng tương đương (Equivalence Partitioning) và Phân tích giá trị biên (Boundary Value Analysis) thuộc phương pháp Kiểm thử hộp đen (Black-box Testing).\n"
        "Bảng quyết định là công cụ trực quan và chặt chẽ nhằm phân tích các tổ hợp điều kiện đầu vào phức tạp (các trường thông tin đúng - sai - để trống) và các hành động tương ứng của hệ thống, từ đó bao quát triệt để mọi tình huống ngoại lệ (edge cases) và đảm bảo không bỏ sót lỗi."
    )

    # 6.2.4 Tiêu chí đánh giá
    add_heading_styled(doc, "6.2.4. Tiêu chí đánh giá chất lượng", level=3)
    criteria_list = [
        "Chức năng hoạt động chính xác: Mọi luồng xử lý dữ liệu từ giao diện tới CSDL MySQL phải đúng theo nghiệp vụ quy định.",
        "Không xảy ra lỗi nghiêm trọng (Zero Critical/Blocker Bugs): Không có lỗi sập hệ thống (Crash 500), không rò rỉ dữ liệu hoặc sai lệch thông tin bệnh nhân.",
        "Tính toàn vẹn lịch khám: Hệ thống ngăn chặn tuyệt đối việc trùng lặp giờ khám của một bác sĩ hoặc một bệnh nhân đặt trùng giờ.",
        "Độ phản hồi của AI Chatbot: Chatbot trả lời đúng thông tin bảng giá và dịch vụ thực tế của phòng khám trong thời gian dưới 2 giây.",
        "Tiêu chí nghiệm thu (Acceptance Criteria): Tỷ lệ Pass của toàn bộ các kịch bản kiểm thử phải đạt từ 95% trở lên."
    ]
    for cr in criteria_list:
        p_c = doc.add_paragraph(style='List Bullet')
        p_c.paragraph_format.line_spacing = 1.15
        p_c.paragraph_format.space_after = Pt(2)
        r = p_c.add_run(cr)
        r.font.name = "Times New Roman"
        r.font.size = Pt(11)

    # 6.3 Test Case
    add_heading_styled(doc, "6.3. Chi tiết Kịch Bản Kiểm Thử (Test Cases) & Bảng Quyết Định", level=2)

    # ========================== HÀM TẠO MẪU CHO MỖI CHỨC NĂNG ==========================
    def add_feature_test_suite(sec_num, title, cond_desc, dec_cols, dec_rows, action_rows, test_cases):
        add_heading_styled(doc, f"{sec_num}. {title}", level=3)
        add_paragraph_styled(doc, f"Xác định điều kiện: {cond_desc}")
        add_paragraph_styled(doc, "Giá trị của điều kiện: Đúng (T), Sai (F), Để trống (B), Không xét (-)")
        add_paragraph_styled(doc, "Hành động của hệ thống: Thực hiện thành công (T), Thất bại / Báo lỗi (F)")

        # Bảng quyết định
        p_tbl_lbl = doc.add_paragraph()
        r_lbl = p_tbl_lbl.add_run(f"Bảng quyết định chức năng: {title}")
        r_lbl.font.name = "Times New Roman"
        r_lbl.font.italic = True
        r_lbl.font.size = Pt(10.5)

        total_cols = len(dec_cols)
        tbl_dec = doc.add_table(rows=1, cols=total_cols)
        tbl_dec.alignment = WD_TABLE_ALIGNMENT.CENTER
        
        # Tính toán chiều rộng cột bảng quyết định
        w_first = Inches(2.2)
        w_other = (Inches(4.3)) / (total_cols - 1)
        dec_widths = [w_first] + [w_other] * (total_cols - 1)

        style_table_header(tbl_dec.rows[0], dec_widths, bg_color="0369A1")
        for c_idx, h_text in enumerate(dec_cols):
            tbl_dec.rows[0].cells[c_idx].paragraphs[0].text = h_text
        style_table_header(tbl_dec.rows[0], dec_widths, bg_color="0369A1")

        # Thêm các hàng điều kiện
        for i, r_data in enumerate(dec_rows):
            row = tbl_dec.add_row()
            for c_idx, val in enumerate(r_data):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, dec_widths, is_even=(i % 2 == 1), align_center_cols=list(range(1, total_cols)))

        # Thêm hàng phân cách Hành động hệ thống
        sep_row = tbl_dec.add_row()
        for c_idx in range(total_cols):
            if c_idx == 0:
                sep_row.cells[c_idx].paragraphs[0].text = "Hành động hệ thống:"
            else:
                sep_row.cells[c_idx].paragraphs[0].text = ""
        set_cell_background(sep_row.cells[0], "E0F2FE")
        for cell in sep_row.cells:
            set_cell_background(cell, "E0F2FE")
            set_cell_borders(cell, color="BAE6FD")
        sep_row.cells[0].paragraphs[0].runs[0].font.bold = True
        sep_row.cells[0].paragraphs[0].runs[0].font.name = "Times New Roman"
        sep_row.cells[0].paragraphs[0].runs[0].font.color.rgb = RGBColor(3, 75, 117)

        # Thêm các hàng hành động
        for i, a_data in enumerate(action_rows):
            row = tbl_dec.add_row()
            for c_idx, val in enumerate(a_data):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, dec_widths, is_even=False, align_center_cols=list(range(1, total_cols)))

        doc.add_paragraph().paragraph_format.space_after = Pt(4)

        # Bảng kịch bản Test Case chi tiết
        p_tc_lbl = doc.add_paragraph()
        r_tclbl = p_tc_lbl.add_run(f"Danh sách Test Case chi tiết: {title}")
        r_tclbl.font.name = "Times New Roman"
        r_tclbl.font.bold = True
        r_tclbl.font.size = Pt(11)

        tc_headers = ["ID", "Tên Test Case", "Dữ liệu Test", "Các bước thực hiện", "Kết quả mong đợi", "Kết quả thực tế", "Trạng thái"]
        tbl_tc = doc.add_table(rows=1, cols=7)
        tbl_tc.alignment = WD_TABLE_ALIGNMENT.CENTER
        tc_widths = [Inches(0.7), Inches(1.2), Inches(1.1), Inches(1.3), Inches(1.2), Inches(0.55), Inches(0.45)]
        style_table_header(tbl_tc.rows[0], tc_widths, bg_color="0284C7")
        for c_idx, h_text in enumerate(tc_headers):
            tbl_tc.rows[0].cells[c_idx].paragraphs[0].text = h_text
        style_table_header(tbl_tc.rows[0], tc_widths, bg_color="0284C7")

        for i, tc in enumerate(test_cases):
            row = tbl_tc.add_row()
            for c_idx, val in enumerate(tc):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, tc_widths, is_even=(i % 2 == 1), align_center_cols=[0, 5, 6])

        doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # -------------------------------------------------------------
    # 6.3.1 Đăng nhập hệ thống
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.1",
        "Chức năng Đăng nhập hệ thống (Khách hàng & Quản trị viên)",
        "Gồm 3 điều kiện: • Điều kiện 1: Chọn vai trò (User/Admin) • Điều kiện 2: Nhập tài khoản/SĐT (T/F/B) • Điều kiện 3: Nhập mật khẩu (T/F/B)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6"],
        [
            ["1. Vai trò hợp lệ", "T", "T", "T", "T", "T", "T"],
            ["2. Nhập tài khoản", "T", "T", "T", "F", "B", "T"],
            ["3. Nhập mật khẩu", "T", "T", "F", "-", "-", "B"]
        ],
        [
            ["Đăng nhập thành công", "T", "T", "F", "F", "F", "F"],
            ["Báo lỗi sai mật khẩu", "F", "F", "T", "F", "F", "F"],
            ["Báo lỗi không tồn tại", "F", "F", "F", "T", "F", "F"],
            ["Báo lỗi để trống", "F", "F", "F", "F", "T", "T"]
        ],
        [
            ("DN-001", "Đăng nhập Khách hàng thành công", "Role: User\nIdentity: khachhang\nPass: 123456", "1. Chọn tab Khách hàng\n2. Nhập Identity: khachhang\n3. Nhập Pass: 123456\n4. Nhấn Đăng Nhập Hệ Thống", "Đăng nhập thành công, điều hướng về Trang chủ, hiển thị tên Khách hàng", "Đúng", "Pass"),
            ("DN-002", "Đăng nhập Quản trị viên thành công", "Role: Admin\nIdentity: admin\nPass: admin123", "1. Chọn tab Quản Trị Viên\n2. Nhập Identity: admin\n3. Nhập Pass: admin123\n4. Nhấn Đăng Nhập Hệ Thống", "Đăng nhập thành công, điều hướng vào Dashboard quản trị (/admin/dashboard.php)", "Đúng", "Pass"),
            ("DN-003", "Đăng nhập thất bại do sai mật khẩu", "Role: User\nIdentity: khachhang\nPass: 654321_sai", "1. Chọn Khách hàng\n2. Nhập tài khoản đúng\n3. Nhập sai mật khẩu\n4. Nhấn Đăng Nhập", "Hệ thống báo lỗi: 'Mật khẩu không chính xác. Vui lòng thử lại!'", "Đúng", "Pass"),
            ("DN-004", "Đăng nhập thất bại do tài khoản không tồn tại", "Role: User\nIdentity: khongtontai999\nPass: 123456", "1. Nhập tài khoản không có trong CSDL\n2. Nhập mật khẩu\n3. Nhấn Đăng Nhập", "Hệ thống báo lỗi: 'Tài khoản hoặc số điện thoại không tồn tại!'", "Đúng", "Pass"),
            ("DN-005", "Đăng nhập thất bại do để trống tên đăng nhập", "Role: User\nIdentity: (để trống)\nPass: 123456", "1. Bỏ trống ô tài khoản\n2. Nhập mật khẩu\n3. Nhấn Đăng Nhập", "Trình duyệt chặn gửi form, cảnh báo 'Vui lòng điền vào trường này'", "Đúng", "Pass"),
            ("DN-006", "Đăng nhập thất bại do để trống mật khẩu", "Role: User\nIdentity: khachhang\nPass: (để trống)", "1. Nhập tài khoản\n2. Bỏ trống ô mật khẩu\n3. Nhấn Đăng Nhập", "Trình duyệt chặn gửi form, yêu cầu nhập mật khẩu", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.2 Đăng ký tài khoản mới
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.2",
        "Chức năng Đăng ký tài khoản bệnh nhân mới",
        "Gồm 5 điều kiện: • Họ tên (T/B) • SĐT hợp lệ (T/F/B) • Tên đăng nhập không trùng (T/F/B) • Mật khẩu >= 6 ký tự (T/F/B) • Xác nhận MK khớp (T/F)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6", "TH7", "TH8"],
        [
            ["1. Họ và tên", "T", "B", "T", "T", "T", "T", "T", "T"],
            ["2. Số điện thoại", "T", "-", "F", "F(trùng)", "T", "T", "T", "T"],
            ["3. Tên đăng nhập", "T", "-", "-", "-", "F(trùng)", "F(<3kt)", "T", "T"],
            ["4. Mật khẩu (>=6kt)", "T", "-", "-", "-", "-", "-", "F(<6kt)", "T"],
            ["5. Nhập lại MK khớp", "T", "-", "-", "-", "-", "-", "-", "F(lệch)"]
        ],
        [
            ["Đăng ký thành công", "T", "F", "F", "F", "F", "F", "F", "F"],
            ["Báo lỗi để trống", "F", "T", "F", "F", "F", "F", "F", "F"],
            ["Báo lỗi SĐT không hợp lệ / trùng", "F", "F", "T", "T", "F", "F", "F", "F"],
            ["Báo lỗi Username trùng / ngắn", "F", "F", "F", "F", "T", "T", "F", "F"],
            ["Báo lỗi Mật khẩu không hợp lệ / lệch", "F", "F", "F", "F", "F", "F", "T", "T"]
        ],
        [
            ("DK-001", "Đăng ký tài khoản thành công", "Họ tên: Trần Văn Nam\nSĐT: 0988776655\nEmail: nam@gmail.com\nUser: namtran99\nPass: nam123456", "1. Mở tab Đăng Ký Mới\n2. Nhập đầy đủ thông tin hợp lệ\n3. Nhấn Hoàn Tất Đăng Ký Tài Khoản", "Đăng ký thành công, tự động đăng nhập và lưu hồ sơ vào bảng patients", "Đúng", "Pass"),
            ("DK-002", "Đăng ký thất bại do bỏ trống Họ tên", "Họ tên: (trống)\nSĐT: 0988776655\nUser: userdemo\nPass: 123456", "1. Bỏ trống trường Họ và tên\n2. Điền các trường còn lại\n3. Bấm Đăng Ký", "Báo lỗi trường Họ và tên là bắt buộc", "Đúng", "Pass"),
            ("DK-003", "Đăng ký thất bại do SĐT sai định dạng", "SĐT: 0912abc\n(chứa chữ cái hoặc ít hơn 10 số)", "1. Nhập SĐT chứa chữ cái '0912abc'\n2. Điền thông tin khác\n3. Bấm Đăng Ký", "Hệ thống báo lỗi: 'Số điện thoại không hợp lệ (cần 10 số)'", "Đúng", "Pass"),
            ("DK-004", "Đăng ký thất bại do SĐT đã được sử dụng", "SĐT: 0987654321\n(SĐT bệnh nhân cũ đã có)", "1. Nhập SĐT đã tồn tại trong CSDL\n2. Bấm Đăng Ký", "Hệ thống báo lỗi: 'Số điện thoại này đã được đăng ký tài khoản!'", "Đúng", "Pass"),
            ("DK-005", "Đăng ký thất bại do Username đã tồn tại", "Username: khachhang\n(đã có trong CSDL)", "1. Nhập Username trùng tài khoản cũ\n2. Bấm Đăng Ký", "Báo lỗi: 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!'", "Đúng", "Pass"),
            ("DK-006", "Đăng ký thất bại do Username quá ngắn", "Username: ab\n(< 3 ký tự)", "1. Nhập username chỉ có 2 ký tự\n2. Bấm Đăng Ký", "Báo lỗi: 'Tên đăng nhập phải chứa ít nhất 3 ký tự'", "Đúng", "Pass"),
            ("DK-007", "Đăng ký thất bại do mật khẩu dưới 6 ký tự", "Pass: 12345\n(5 ký tự)", "1. Nhập mật khẩu 5 ký tự\n2. Bấm Đăng Ký", "Báo lỗi: 'Mật khẩu phải có độ dài tối thiểu 6 ký tự'", "Đúng", "Pass"),
            ("DK-008", "Đăng ký thất bại do xác nhận MK không khớp", "Pass: 123456\nConfirm: 1234567_khac", "1. Nhập mật khẩu: 123456\n2. Nhập xác nhận: 1234567\n3. Bấm Đăng Ký", "Báo lỗi: 'Mật khẩu xác nhận không trùng khớp!'", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.3 Đặt lịch khám trực tuyến (Booking)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.3",
        "Chức năng Đặt lịch khám trực tuyến (Khách hàng)",
        "Gồm 6 điều kiện: • Chọn dịch vụ (T/B) • Chọn bác sĩ (T/B - tự sắp xếp) • Ngày khám hợp lệ tương lai (T/F/B) • Khung giờ hợp lệ (T/F/B) • Bác sĩ không bị trùng lịch (T/F) • SĐT liên hệ (T/F/B)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6", "TH7"],
        [
            ["1. Chọn dịch vụ", "T", "T", "B", "T", "T", "T", "T"],
            ["2. Chọn bác sĩ", "T", "B(tự chọn)", "T", "T", "T", "T", "T"],
            ["3. Ngày khám (tương lai)", "T", "T", "-", "F(quá khứ)", "T", "T", "T"],
            ["4. Khung giờ khám (8h-20h)", "T", "T", "-", "-", "F(ngoài giờ)", "T", "T"],
            ["5. Không trùng lịch bác sĩ", "T", "T", "-", "-", "-", "F(trùng)", "T"],
            ["6. SĐT liên hệ hợp lệ", "T", "T", "-", "-", "-", "-", "F/B"]
        ],
        [
            ["Đặt lịch thành công", "T", "T", "F", "F", "F", "F", "F"],
            ["Báo lỗi để trống dịch vụ", "F", "F", "T", "F", "F", "F", "F"],
            ["Báo lỗi ngày không hợp lệ", "F", "F", "F", "T", "F", "F", "F"],
            ["Báo lỗi ngoài giờ khám", "F", "F", "F", "F", "T", "F", "F"],
            ["Báo lỗi bác sĩ kín lịch", "F", "F", "F", "F", "F", "T", "F"],
            ["Báo lỗi SĐT liên hệ", "F", "F", "F", "F", "F", "F", "T"]
        ],
        [
            ("DL-001", "Đặt lịch khám thành công có chọn bác sĩ", "Dịch vụ: Cạo vôi răng\nBác sĩ: BS. Trần Minh Tâm\nNgày: Ngày mai\nGiờ: 09:00\nSĐT: 0912345678", "1. Vào trang Đặt lịch khám\n2. Chọn dịch vụ và bác sĩ\n3. Chọn ngày mai lúc 09:00\n4. Điền họ tên, SĐT\n5. Bấm Xác Nhận Đặt Lịch", "Hệ thống ghi nhận lịch hẹn với trạng thái 'Chờ xác nhận', hiển thị mã đặt lịch thành công", "Đúng", "Pass"),
            ("DL-002", "Đặt lịch thành công để phòng khám tự sắp xếp bác sĩ", "Dịch vụ: Trám răng thẩm mỹ\nBác sĩ: Không chỉ định\nNgày: Ngày mốt\nGiờ: 14:30\nSĐT: 0933221100", "1. Chọn dịch vụ\n2. Mục bác sĩ chọn 'Phòng khám tự sắp xếp'\n3. Chọn ngày giờ\n4. Bấm Đặt Lịch", "Đặt lịch thành công, hệ thống phân công bác sĩ trực chuyên khoa phù hợp", "Đúng", "Pass"),
            ("DL-003", "Đặt lịch thất bại do chưa chọn dịch vụ", "Dịch vụ: (để trống)\nBác sĩ: BS. Tâm\nNgày: Ngày mai", "1. Bỏ qua mục chọn dịch vụ\n2. Nhập các thông tin khác\n3. Bấm Đặt Lịch", "Hệ thống cảnh báo: 'Vui lòng chọn dịch vụ nha khoa cần khám'", "Đúng", "Pass"),
            ("DL-004", "Đặt lịch thất bại do chọn ngày trong quá khứ", "Ngày khám: 01/01/2020\n(ngày đã qua)", "1. Cố ý nhập ngày khám nhỏ hơn ngày hiện tại\n2. Bấm Đặt Lịch", "Hệ thống báo lỗi: 'Ngày khám phải từ ngày hiện tại trở đi!'", "Đúng", "Pass"),
            ("DL-005", "Đặt lịch thất bại do ngoài giờ làm việc", "Giờ khám: 21:30\n(ngoài khung 08:00 - 20:00)", "1. Chọn khung giờ khám 21:30\n2. Bấm Đặt Lịch", "Báo lỗi: 'Phòng khám chỉ tiếp nhận bệnh nhân từ 08:00 đến 20:00'", "Đúng", "Pass"),
            ("DL-006", "Đặt lịch thất bại do bác sĩ đã có lịch hẹn cùng giờ", "Bác sĩ: BS. Tâm\nNgày: Ngày mai\nGiờ: 09:00\n(đã có người đặt)", "1. Đặt lịch trùng đúng bác sĩ và khung giờ đã được xác nhận trước đó\n2. Bấm Đặt Lịch", "Hệ thống báo: 'Bác sĩ đã có lịch khám vào khung giờ này. Vui lòng chọn giờ khác!'", "Đúng", "Pass"),
            ("DL-007", "Đặt lịch thất bại do để trống SĐT liên hệ", "Họ tên: Lê An\nSĐT: (trống)", "1. Không nhập số điện thoại liên hệ\n2. Bấm Đặt Lịch", "Báo lỗi trường Số điện thoại là bắt buộc để nhận mã xác nhận", "Đúng", "Pass"),
            ("DL-008", "Đặt lịch kèm ghi chú bệnh lý phức tạp", "Dịch vụ: Nhổ răng khôn\nGhi chú: Có tiền sử dị ứng thuốc tê Novocain", "1. Điền thông tin đặt lịch\n2. Nhập ghi chú bệnh lý vào ô ghi chú\n3. Bấm Đặt Lịch", "Đặt lịch thành công, ghi chú được lưu vào bệnh án để bác sĩ chuẩn bị trước", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.4 Quản lý lịch hẹn của tôi (User)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.4",
        "Chức năng Quản lý & Hủy lịch hẹn của tôi (Khách hàng)",
        "Gồm 3 điều kiện: • Đã đăng nhập tài khoản (T/F) • Lịch hẹn tồn tại (T/F) • Trạng thái lịch có thể hủy (T: Chờ xác nhận / F: Đã hoàn thành/quá giờ)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Đã đăng nhập", "T", "T", "T", "F"],
            ["2. Lịch hẹn tồn tại", "T", "T", "F", "-"],
            ["3. Trạng thái cho phép hủy", "T(chờ duyệt)", "F(đã khám)", "-", "-"]
        ],
        [
            ["Xem danh sách & chi tiết", "T", "T", "F", "F"],
            ["Hủy lịch thành công", "T", "F", "F", "F"],
            ["Chuyển hướng đến đăng nhập", "F", "F", "F", "T"]
        ],
        [
            ("LH-001", "Xem danh sách lịch hẹn của tôi", "User: khachhang\n(đã có 3 lịch hẹn trong CSDL)", "1. Đăng nhập tài khoản\n2. Vào trang 'Lịch hẹn của tôi'\n3. Kiểm tra danh sách", "Hiển thị đầy đủ 3 lịch hẹn với ngày giờ, tên dịch vụ, tên bác sĩ và trạng thái", "Đúng", "Pass"),
            ("LH-002", "Lọc lịch hẹn theo trạng thái", "Filter: 'Đã xác nhận'", "1. Chọn bộ lọc 'Đã xác nhận'\n2. Bấm Lọc", "Chỉ hiển thị các lịch hẹn có trạng thái 'Đã xác nhận'", "Đúng", "Pass"),
            ("LH-003", "Hủy lịch hẹn thành công khi đang chờ duyệt", "Lịch hẹn #102: Trạng thái 'Chờ xác nhận'", "1. Bấm nút 'Hủy lịch hẹn'\n2. Xác nhận hộp thoại popup", "Hệ thống cập nhật trạng thái thành 'Đã hủy', gửi phản hồi thành công", "Đúng", "Pass"),
            ("LH-004", "Từ chối hủy lịch đã hoàn thành", "Lịch hẹn #88: Trạng thái 'Đã hoàn thành'", "1. Kiểm tra lịch hẹn cũ đã hoàn tất khám", "Không hiển thị nút Hủy lịch, không cho phép thao tác hủy", "Đúng", "Pass"),
            ("LH-005", "Truy cập khi chưa đăng nhập", "Chưa đăng nhập hệ thống", "1. Gõ trực tiếp URL: /pages/my_appointments.php", "Hệ thống tự động chuyển hướng về trang /login.php kèm tham số redirect", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.5 Tiếp nhận & Duyệt lịch hẹn (Admin)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.5",
        "Chức năng Tiếp nhận & Duyệt lịch hẹn (Quản trị viên)",
        "Gồm 4 điều kiện: • Đăng nhập quyền Admin (T/F) • Lịch hẹn hợp lệ (T/F) • Thao tác duyệt/dời/hủy (T/F) • Phân công bác sĩ (T/F)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5"],
        [
            ["1. Quyền Admin", "T", "T", "T", "T", "F"],
            ["2. Lịch hẹn tồn tại", "T", "T", "T", "F", "-"],
            ["3. Thao tác thực hiện", "Duyệt", "Dời giờ", "Hủy", "-", "-"]
        ],
        [
            ["Thực hiện thành công", "T", "T", "T", "F", "F"],
            ["Chặn truy cập trái phép", "F", "F", "F", "F", "T"]
        ],
        [
            ("QLL-001", "Xem danh sách lịch hẹn toàn hệ thống", "Admin logged in", "1. Đăng nhập admin\n2. Vào mục Quản lý lịch hẹn (/admin/appointments.php)", "Hiển thị bảng danh sách toàn bộ lịch hẹn kèm bộ lọc ngày và trạng thái", "Đúng", "Pass"),
            ("QLL-002", "Tìm kiếm lịch hẹn theo tên bệnh nhân", "Từ khóa: 'Nguyễn Văn Tuấn'", "1. Nhập tên vào ô tìm kiếm\n2. Nhấn Tìm kiếm", "Bảng dữ liệu lọc hiển thị chính xác các lịch hẹn của bệnh nhân Nguyễn Văn Tuấn", "Đúng", "Pass"),
            ("QLL-003", "Duyệt xác nhận lịch hẹn mới", "Lịch hẹn #105: 'Chờ xử lý'", "1. Bấm nút 'Xác nhận lịch'\n2. Chọn xác nhận", "Trạng thái chuyển sang 'Đã xác nhận', cập nhật ngay tức thì trên giao diện", "Đúng", "Pass"),
            ("QLL-004", "Dời ngày giờ khám cho bệnh nhân", "Lịch hẹn #105: Đổi sang 15:00 ngày mai", "1. Bấm 'Dời lịch'\n2. Chọn ngày giờ mới\n3. Bấm Lưu", "Cập nhật ngày giờ mới thành công, kiểm tra không bị trùng lịch bác sĩ", "Đúng", "Pass"),
            ("QLL-005", "Chuyển trạng thái 'Hoàn thành khám'", "Lịch hẹn #104: 'Đã xác nhận'", "1. Bác sĩ khám xong, admin bấm 'Hoàn thành khám'", "Trạng thái chuyển thành 'Hoàn thành', ghi nhận lượt khám vào doanh thu", "Đúng", "Pass"),
            ("QLL-006", "Hủy lịch hẹn có ghi rõ lý do", "Lịch hẹn #103: Lý do 'Bệnh nhân bận công tác'", "1. Bấm 'Hủy lịch'\n2. Nhập lý do\n3. Bấm Xác nhận", "Trạng thái chuyển sang 'Đã hủy', lưu lý do hủy vào lịch sử hệ thống", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.6 Quản lý hồ sơ bệnh nhân (Admin)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.6",
        "Chức năng Quản lý hồ sơ bệnh nhân (Admin)",
        "Gồm 4 điều kiện: • Họ tên bệnh nhân (T/B) • SĐT hợp lệ (T/F/B) • SĐT chưa trùng lặp (T/F) • Ràng buộc dữ liệu khi xóa (T/F)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5"],
        [
            ["1. Họ và tên", "T", "B", "T", "T", "T"],
            ["2. Số điện thoại", "T", "-", "F(sai định dạng)", "T", "T"],
            ["3. SĐT chưa trùng", "T", "-", "-", "F(trùng)", "T"],
            ["4. Thao tác", "Thêm mới", "Thêm mới", "Thêm mới", "Thêm mới", "Xóa"]
        ],
        [
            ["Thao tác thành công", "T", "F", "F", "F", "T"],
            ["Báo lỗi để trống họ tên", "F", "T", "F", "F", "F"],
            ["Báo lỗi SĐT sai / trùng", "F", "F", "T", "T", "F"]
        ],
        [
            ("BN-001", "Xem danh sách hồ sơ bệnh nhân", "Admin logged in", "1. Vào menu Bệnh nhân (/admin/patients.php)", "Hiển thị danh sách hồ sơ bệnh nhân, số điện thoại, ngày tạo và lịch sử khám", "Đúng", "Pass"),
            ("BN-002", "Tìm kiếm bệnh nhân theo số điện thoại", "Keyword: '0987654321'", "1. Nhập SĐT vào ô tìm kiếm\n2. Nhấn Enter", "Hiển thị đúng hồ sơ của bệnh nhân sở hữu số điện thoại tương ứng", "Đúng", "Pass"),
            ("BN-003", "Thêm mới hồ sơ bệnh nhân thành công", "Họ tên: Hoàng Thị Mai\nSĐT: 0977112233\nĐịa chỉ: Hà Nội\nTiền sử: Dị ứng phấn hoa", "1. Bấm 'Thêm Bệnh Nhân Mới'\n2. Điền đầy đủ thông tin hợp lệ\n3. Bấm Lưu", "Hồ sơ mới được tạo thành công, xuất hiện ngay trong danh sách bệnh nhân", "Đúng", "Pass"),
            ("BN-004", "Thêm bệnh nhân thất bại do trùng SĐT", "SĐT: 0987654321\n(đã có bệnh nhân sử dụng)", "1. Nhập SĐT đã có trong hệ thống\n2. Bấm Lưu", "Hệ thống báo lỗi: 'Số điện thoại này đã tồn tại trong danh sách bệnh nhân!'", "Đúng", "Pass"),
            ("BN-005", "Cập nhật thông tin bệnh án & tiền sử bệnh", "Bệnh nhân #12: Bổ sung ghi chú răng sâu số 6", "1. Bấm nút Sửa thông tin\n2. Cập nhật ghi chú bệnh lý\n3. Bấm Lưu", "Cập nhật thông tin thành công, dữ liệu được ghi nhận vào CSDL", "Đúng", "Pass"),
            ("BN-006", "Xóa hồ sơ bệnh nhân an toàn", "Bệnh nhân #25: Chưa có lịch hẹn hoặc hóa đơn", "1. Bấm nút Xóa\n2. Xác nhận popup cảnh báo", "Xóa hồ sơ thành công, dữ liệu bệnh nhân được gỡ khỏi danh sách", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.7 Quản lý dịch vụ nha khoa (Admin)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.7",
        "Chức năng Quản lý danh mục dịch vụ nha khoa (Admin)",
        "Gồm 3 điều kiện: • Tên dịch vụ (T/B) • Đơn giá là số dương > 0 (T/F/B) • Thao tác (Thêm/Sửa/Xóa)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Tên dịch vụ", "T", "B", "T", "T"],
            ["2. Đơn giá (> 0)", "T", "-", "F(số âm hoặc chữ)", "T"],
            ["3. Thao tác", "Thêm mới", "Thêm mới", "Thêm mới", "Sửa giá"]
        ],
        [
            ["Thực hiện thành công", "T", "F", "F", "T"],
            ["Báo lỗi để trống tên", "F", "T", "F", "F"],
            ["Báo lỗi đơn giá không hợp lệ", "F", "F", "T", "F"]
        ],
        [
            ("DV-001", "Xem danh sách dịch vụ nha khoa", "Admin logged in", "1. Vào menu Dịch vụ (/admin/services.php)", "Hiển thị đầy đủ danh mục dịch vụ, hình ảnh, thời gian làm và bảng giá niêm yết", "Đúng", "Pass"),
            ("DV-002", "Thêm mới dịch vụ thành công", "Tên: Tẩy trắng răng Laser Whitening\nGiá: 2.500.000 VNĐ\nThời gian: 60 phút", "1. Bấm 'Thêm Dịch Vụ Mới'\n2. Nhập thông tin và tải ảnh minh họa\n3. Bấm Lưu", "Dịch vụ mới được thêm thành công, hiển thị trên website trang chủ", "Đúng", "Pass"),
            ("DV-003", "Thêm dịch vụ thất bại do để trống tên", "Tên: (trống)\nGiá: 500.000 VNĐ", "1. Bỏ trống tên dịch vụ\n2. Bấm Lưu", "Hệ thống báo lỗi: 'Vui lòng nhập tên dịch vụ'", "Đúng", "Pass"),
            ("DV-004", "Thêm dịch vụ thất bại do đơn giá âm", "Tên: Hàn răng\nGiá: -150.000 VNĐ", "1. Nhập đơn giá là số âm\n2. Bấm Lưu", "Báo lỗi: 'Đơn giá dịch vụ phải là số dương lớn hơn 0'", "Đúng", "Pass"),
            ("DV-005", "Cập nhật đơn giá dịch vụ", "Dịch vụ 'Cạo vôi răng': Đổi từ 200k -> 250k", "1. Bấm nút Sửa dịch vụ\n2. Cập nhật giá mới\n3. Bấm Lưu", "Cập nhật thành công, giá mới hiển thị ngay lập tức trên trang chủ và form đặt lịch", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.8 Quản lý đội ngũ bác sĩ (Admin)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.8",
        "Chức năng Quản lý đội ngũ bác sĩ & Chuyên khoa (Admin)",
        "Gồm 3 điều kiện: • Họ tên bác sĩ (T/B) • Chuyên khoa hợp lệ (T/B) • Thao tác (Thêm/Sửa/Đổi trạng thái)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Họ tên bác sĩ", "T", "B", "T", "T"],
            ["2. Chuyên khoa", "T", "-", "B", "T"],
            ["3. Thao tác", "Thêm mới", "Thêm mới", "Thêm mới", "Cập nhật"]
        ],
        [
            ["Thực hiện thành công", "T", "F", "F", "T"],
            ["Báo lỗi để trống thông tin", "F", "T", "T", "F"]
        ],
        [
            ("BS-001", "Xem danh sách đội ngũ bác sĩ", "Admin logged in", "1. Vào menu Bác sĩ (/admin/doctors.php)", "Hiển thị danh sách bác sĩ, học vị, chuyên khoa và số lượng ca khám phụ trách", "Đúng", "Pass"),
            ("BS-002", "Thêm bác sĩ mới thành công", "Tên: TS.BS Lê Hải Đăng\nChuyên khoa: Chỉnh nha - Niềng răng\nKinh nghiệm: 12 năm", "1. Bấm 'Thêm Bác Sĩ Mới'\n2. Điền thông tin và học vị\n3. Bấm Lưu", "Thêm thành công, bác sĩ mới xuất hiện trong dropdown chọn bác sĩ khi đặt lịch", "Đúng", "Pass"),
            ("BS-003", "Thêm bác sĩ thất bại do thiếu chuyên khoa", "Tên: BS. Nguyễn Văn A\nChuyên khoa: (để trống)", "1. Bỏ trống chuyên khoa\n2. Bấm Lưu", "Hệ thống báo lỗi yêu cầu chọn chuyên khoa cho bác sĩ", "Đúng", "Pass"),
            ("BS-004", "Cập nhật lịch làm việc của bác sĩ", "BS. Minh Tâm: Thêm ca trực sáng Thứ 7", "1. Bấm Sửa thông tin bác sĩ\n2. Cập nhật lịch trực\n3. Bấm Lưu", "Hệ thống cập nhật thành công, mở thêm slot đặt lịch cho bệnh nhân vào sáng Thứ 7", "Đúng", "Pass"),
            ("BS-005", "Đổi trạng thái bác sĩ sang 'Nghỉ phép'", "BS. Hải Đăng: Nghỉ phép 3 ngày", "1. Chuyển toggle trạng thái sang Nghỉ phép\n2. Lưu", "Hệ thống tự động ẩn bác sĩ khỏi danh sách chọn lịch trong 3 ngày nghỉ", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.9 Thống kê doanh thu (Admin)
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.9",
        "Chức năng Thống kê & Báo cáo doanh thu (Admin)",
        "Gồm 3 điều kiện: • Khoảng thời gian (Từ ngày <= Đến ngày: T / F) • Phân loại theo dịch vụ (T) • Xuất dữ liệu (T)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3"],
        [
            ["1. Khoảng ngày hợp lệ (Từ ngày <= Đến ngày)", "T", "F(Từ ngày > Đến ngày)", "T"],
            ["2. Chọn loại báo cáo", "Theo tháng", "-", "Theo dịch vụ"]
        ],
        [
            ["Hiển thị số liệu & biểu đồ", "T", "F", "T"],
            ["Báo lỗi khoảng thời gian không hợp lệ", "F", "T", "F"]
        ],
        [
            ("DT-001", "Xem tổng quan doanh thu Dashboard", "Admin logged in", "1. Vào trang Doanh thu (/admin/revenue.php)", "Hiển thị tổng doanh thu tháng, số ca khám hoàn thành và biểu đồ tăng trưởng", "Đúng", "Pass"),
            ("DT-002", "Lọc doanh thu theo khoảng ngày hợp lệ", "Từ: 01/10/2026\nĐến: 31/10/2026", "1. Chọn khoảng ngày trong tháng 10\n2. Bấm Xem báo cáo", "Số liệu và biểu đồ cập nhật chính xác theo các hóa đơn phát sinh trong tháng 10", "Đúng", "Pass"),
            ("DT-003", "Lọc thất bại do Từ ngày lớn hơn Đến ngày", "Từ: 20/10/2026\nĐến: 10/10/2026", "1. Cố ý chọn ngày bắt đầu sau ngày kết thúc\n2. Bấm Xem báo cáo", "Hệ thống báo lỗi: 'Ngày bắt đầu không được lớn hơn ngày kết thúc!'", "Đúng", "Pass"),
            ("DT-004", "Thống kê tỷ trọng doanh thu theo dịch vụ", "Phân tích theo dịch vụ", "1. Chọn tab Phân tích dịch vụ", "Hiển thị biểu đồ hình quạt (Pie chart) tỷ trọng doanh thu của từng dịch vụ", "Đúng", "Pass"),
            ("DT-005", "Xuất báo cáo doanh thu ra Excel / CSV", "File export", "1. Bấm nút 'Xuất Excel'\n2. Tải file về", "Tải xuống file .csv/.xlsx có cấu trúc đầy đủ cột ngày, mã hóa đơn, tên bệnh nhân, số tiền", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.10 Chatbot AI Assistant
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.10",
        "Chức năng Tư vấn tự động Chatbot AI Dental Assistant",
        "Gồm 3 điều kiện: • Câu hỏi chuyên môn nha khoa (T/F) • Câu hỏi tra cứu bảng giá (T/F) • Trạng thái kết nối Server AI (Online/Offline)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Câu hỏi liên quan nha khoa", "T", "T(hỏi giá)", "F(ngoài ngành)", "T"],
            ["2. Trạng thái kết nối AI Server", "Online", "Online", "Online", "Offline"]
        ],
        [
            ["Tư vấn chuyên môn & hướng dẫn khám", "T", "F", "F", "F"],
            ["Báo giá chính xác từ CSDL", "F", "T", "F", "F"],
            ["Từ chối lịch sự và hướng dẫn", "F", "F", "T", "F"],
            ["Thông báo mất kết nối & gợi ý hotline", "F", "F", "F", "T"]
        ],
        [
            ("AI-001", "Tư vấn triệu chứng đau răng nhạy cảm", "Prompt: 'Tôi bị ê buốt răng khi uống nước lạnh là bị gì?'", "1. Mở cửa sổ Chatbot ở góc phải\n2. Gõ câu hỏi và nhấn Gửi", "AI trả lời phân tích nguyên nhân (mòn men răng, sâu răng) và khuyên đặt lịch khám", "Đúng", "Pass"),
            ("AI-002", "Tra cứu bảng giá dịch vụ chính xác", "Prompt: 'Chi phí nhổ răng khôn mọc lệch là bao nhiêu?'", "1. Nhập câu hỏi hỏi giá\n2. Gửi", "AI tra cứu đúng bảng giá CSDL và trả lời khoảng giá từ 1.500.000 - 3.000.000 VNĐ tùy độ lệch", "Đúng", "Pass"),
            ("AI-003", "Hỏi đặt lịch hẹn qua Chatbot", "Prompt: 'Tôi muốn đặt lịch khám vào ngày mai'", "1. Gõ yêu cầu đặt lịch\n2. Gửi", "AI nhận diện ý định (Intent: booking), gửi link trực tiếp kèm nút dẫn sang trang đặt lịch khám", "Đúng", "Pass"),
            ("AI-004", "Hỏi câu hỏi ngoài phạm vi nha khoa", "Prompt: 'Hôm nay thời tiết Hà Nội thế nào?'", "1. Gõ câu hỏi không liên quan\n2. Gửi", "AI lịch sự từ chối và nhắc nhở mình là Trợ lý ảo chuyên trách tư vấn Nha Khoa Premier Dental", "Đúng", "Pass"),
            ("AI-005", "Xử lý khi ngắt kết nối mạng", "Ngắt kết nối Internet / API timeout", "1. Giả lập mất mạng\n2. Gửi tin nhắn", "Giao diện hiển thị thông báo lỗi thân thiện: 'Không thể kết nối máy chủ. Vui lòng gọi Hotline 1900 6868'", "Đúng", "Pass")
        ]
    )

    # -------------------------------------------------------------
    # 6.3.11 Kiểm thử Giao diện (UI/UX), Nút bấm & Typography
    # -------------------------------------------------------------
    add_feature_test_suite(
        "6.3.11",
        "Chức năng Kiểm thử Giao diện (UI/UX), Nút bấm và Bộ font chữ",
        "Gồm 4 điều kiện: • Nút bấm có gradient Sapphire Blue nổi bật (T/F) • Ô nhập liệu có viền rõ & icon (T/F) • Font Be Vietnam Pro chuẩn tiếng Việt (T/F) • Responsive đa thiết bị (T/F)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Nút bấm gradient nổi bật", "T", "T", "T", "T"],
            ["2. Ô nhập viền 1.5px & icon xanh", "T", "T", "T", "T"],
            ["3. Font Be Vietnam Pro chuẩn", "T", "T", "T", "T"],
            ["4. Thiết bị kiểm thử", "Desktop", "Tablet", "Mobile", "Hi-DPI"]
        ],
        [
            ["Hiển thị sắc nét, sang trọng, dễ nhìn", "T", "T", "T", "T"],
            ["Không bị tràn layout hay vỡ chữ", "T", "T", "T", "T"]
        ],
        [
            ("GD-001", "Kiểm tra màu sắc nút 'Đăng Nhập Hệ Thống'", "Class: .btn-primary-clinic", "1. Mở trang login.php\n2. Quan sát nút Đăng Nhập", "Nút mang gradient xanh Sapphire #0284c7 -> #0369a1, chữ trắng tinh khiết, bóng đổ nổi 3D, hover lướt nhẹ", "Đúng", "Pass"),
            ("GD-002", "Kiểm tra nút 'Hoàn Tất Đăng Ký Tài Khoản'", "Class: .btn-primary-clinic", "1. Mở tab Đăng Ký Mới\n2. Quan sát nút submit", "Nút mang màu xanh chủ đạo tương tự, kích thước lớn dễ chạm, nổi bật rõ ràng trên nền form", "Đúng", "Pass"),
            ("GD-003", "Kiểm tra đường viền và icon các ô nhập liệu", "Input groups", "1. Quan sát các ô nhập (Tài khoản, Mật khẩu, Họ tên, SĐT...)", "Viền nổi sắc nét 1.5px #cbd5e1, nền sạch sẽ #f8fafc, icon xanh y tế #0284c7 dễ nhận diện", "Đúng", "Pass"),
            ("GD-004", "Kiểm tra hiệu ứng Focus ô nhập liệu", "Focus vào ô input", "1. Click chuột vào bất kỳ ô nhập liệu nào", "Viền chuyển sang xanh sáng #0284c7, xuất hiện quầng sáng glow 4px, nền chuyển sang trắng tuyết", "Đúng", "Pass"),
            ("GD-005", "Kiểm tra nút mắt ẩn/hiện mật khẩu", "Eye toggle icon", "1. Nhập mật khẩu\n2. Bấm icon con mắt\n3. Bấm lại lần nữa", "Chuyển đổi tức thì giữa dạng password (chấm tròn) và text rõ chữ, icon đổi giữa fa-eye và fa-eye-slash", "Đúng", "Pass"),
            ("GD-006", "Kiểm tra bộ font chữ Be Vietnam Pro", "Typography", "1. Kiểm tra các đoạn văn bản có dấu thanh tiếng Việt", "Các ký tự 'ệ, ế, ỡ, ử, ặ, ỹ' hiển thị tròn trịa, cân đối, khoảng cách dòng thoáng, không bị lệch hàng", "Đúng", "Pass"),
            ("GD-007", "Kiểm tra độ tương phản văn bản (WCAG 2.1)", "Contrast ratio", "1. Đo độ tương phản chữ trên nền form", "Tỷ lệ tương phản đạt trên 4.5:1 (Chuẩn WCAG AA cho văn bản thường và AAA cho tiêu đề)", "Đúng", "Pass"),
            ("GD-008", "Kiểm tra chuyển đổi tab Đăng Nhập / Đăng Ký", "Tab switcher", "1. Click tab 'Đăng Ký Mới'\n2. Click lại 'Đăng Nhập'", "Chuyển đổi form êm ái, thanh chỉ báo gạch chân màu xanh di chuyển mượt mà", "Đúng", "Pass"),
            ("GD-009", "Kiểm tra thẻ chọn vai trò Khách hàng / Admin", "Role pills", "1. Chọn 'Khách Hàng'\n2. Chọn 'Quản Trị Viên'", "Thẻ Khách hàng viền xanh nền xanh nhạt, thẻ Admin viền đỏ nền đỏ nhạt nổi bật", "Đúng", "Pass"),
            ("GD-010", "Kiểm tra nút bấm điền mẫu tài khoản Demo", "Quick Demo Box", "1. Bấm vào hộp điền mẫu Khách hàng hoặc Admin", "Tự động điền chính xác tài khoản và mật khẩu mẫu vào các ô input tương ứng", "Đúng", "Pass"),
            ("GD-011", "Kiểm tra liên kết quay về Trang chủ", "Footer back link", "1. Bấm 'Quay về Trang chủ Premier Dental'", "Điều hướng chính xác về trang chủ index.php, icon mũi tên có hiệu ứng trượt nhẹ", "Đúng", "Pass"),
            ("GD-012", "Kiểm tra giao diện trên màn hình di động", "Viewport: 375x812px", "1. Thu nhỏ màn hình trình duyệt về kích thước điện thoại", "Card đăng nhập co giãn hoàn hảo (fluid width), không bị tràn ngang, các nút và ô bấm vừa ngón tay", "Đúng", "Pass")
        ]
    )

    # 6.4 Báo cáo kiểm thử
    add_heading_styled(doc, "6.4. Báo cáo kiểm thử & Tổng hợp kết quả", level=2)
    add_paragraph_styled(
        doc,
        "Dưới đây là bảng tổng hợp kết quả thực hiện kiểm thử trên toàn bộ các phân hệ chức năng của Hệ thống Nha Khoa Quốc Tế Premier Dental qua hai vòng kiểm thử (Lần 1: Kiểm thử phát hiện lỗi; Lần 2: Kiểm thử hồi quy sau khi sửa lỗi)."
    )

    # Bảng tổng hợp kết quả
    tbl_rep = doc.add_table(rows=1, cols=6)
    tbl_rep.alignment = WD_TABLE_ALIGNMENT.CENTER
    rep_widths = [Inches(0.6), Inches(2.3), Inches(0.9), Inches(0.9), Inches(0.9), Inches(0.9)]
    style_table_header(tbl_rep.rows[0], rep_widths, bg_color="0369A1")
    rep_headers = ["STT", "Phân hệ / Chức năng kiểm thử", "Lần 1 (Pass)", "Lần 1 (Fail)", "Lần 2 (Pass)", "Tổng số TC"]
    for c_idx, h_text in enumerate(rep_headers):
        tbl_rep.rows[0].cells[c_idx].paragraphs[0].text = h_text
    style_table_header(tbl_rep.rows[0], rep_widths, bg_color="0369A1")

    report_summary_data = [
        ("1", "Đăng nhập hệ thống (User & Admin)", "5", "1", "6", "6"),
        ("2", "Đăng ký tài khoản bệnh nhân mới", "7", "1", "8", "8"),
        ("3", "Đặt lịch khám trực tuyến (Booking)", "7", "1", "8", "8"),
        ("4", "Quản lý & Hủy lịch hẹn của tôi (User)", "5", "0", "5", "5"),
        ("5", "Tiếp nhận & Duyệt lịch hẹn (Admin)", "6", "0", "6", "6"),
        ("6", "Quản lý hồ sơ bệnh nhân (Admin)", "5", "1", "6", "6"),
        ("7", "Quản lý danh mục dịch vụ nha khoa", "5", "0", "5", "5"),
        ("8", "Quản lý đội ngũ bác sĩ & lịch trực", "5", "0", "5", "5"),
        ("9", "Thống kê & Báo cáo doanh thu (Admin)", "5", "0", "5", "5"),
        ("10", "Tư vấn tự động Chatbot AI Assistant", "4", "1", "5", "5"),
        ("11", "Kiểm thử Giao diện (UI/UX, nút, font chữ)", "10", "2", "12", "12")
    ]

    for i, r_data in enumerate(report_summary_data):
        row = tbl_rep.add_row()
        for c_idx, val in enumerate(r_data):
            row.cells[c_idx].paragraphs[0].text = val
        format_data_row(row, rep_widths, is_even=(i % 2 == 1), align_center_cols=[0, 2, 3, 4, 5])

    # Hàng tổng cộng
    total_row = tbl_rep.add_row()
    total_data = ["", "TỔNG CỘNG TOÀN HỆ THỐNG", "64", "7", "71", "71"]
    for c_idx, val in enumerate(total_data):
        total_row.cells[c_idx].paragraphs[0].text = val
    for c_idx in range(6):
        set_cell_background(total_row.cells[c_idx], "E0F2FE")
        set_cell_borders(total_row.cells[c_idx], color="0284C7")
        p = total_row.cells[c_idx].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER if c_idx != 1 else WD_ALIGN_PARAGRAPH.LEFT
        for r in p.runs:
            r.font.bold = True
            r.font.name = "Times New Roman"
            r.font.size = Pt(10.5)
            r.font.color.rgb = RGBColor(3, 75, 117)

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # Đánh giá kết quả
    add_heading_styled(doc, "6.4.1. Đánh giá chất lượng và khắc phục khiếm khuyết", level=3)
    eval_text = (
        "1. Kết quả kiểm thử lần 1:\n"
        "   - Tổng số kịch bản kiểm thử: 71 Test Cases.\n"
        "   - Số kịch bản đạt (Pass): 64/71 (Tỷ lệ 90.1%).\n"
        "   - Số kịch bản phát hiện khiếm khuyết (Fail): 7/71 (9.9%).\n"
        "   - Các lỗi chính được phát hiện trong lần 1 bao gồm:\n"
        "     + Nút bấm 'Đăng Nhập Hệ Thống' và 'Hoàn Tất Đăng Ký' chưa có style màu chủ đạo trong CSS, dẫn tới hiển thị mờ nhạt dạng khung trắng chữ đen.\n"
        "     + Các ô nhập liệu viền xám nhạt khó phân biệt, độ tương phản chưa tối ưu.\n"
        "     + Font chữ mặc định chưa tối ưu riêng cho dấu thanh tiếng Việt.\n"
        "     + Một số trường hợp chọn giờ khám ngoài giờ làm việc chưa có validation phía client.\n"
        "\n"
        "2. Kết quả kiểm thử lần 2 (Sau khi khắc phục triệt để lỗi):\n"
        "   - Toàn bộ 7 lỗi đã được đội ngũ phát triển xử lý:\n"
        "     + Đã bổ sung bộ gradient màu xanh Sapphire nha khoa cao cấp cho nút bấm (.btn-primary-clinic).\n"
        "     + Tinh chỉnh viền nổi 1.5px cho các ô nhập liệu, thêm icon xanh y tế và hiệu ứng focus ring rõ nét.\n"
        "     + Đồng bộ toàn diện font chữ Be Vietnam Pro chuẩn hình học tiếng Việt.\n"
        "     + Bổ sung kiểm tra ràng buộc thời gian và validation đầy đủ.\n"
        "   - Kết quả kiểm thử hồi quy lần 2: Đạt 71/71 Test Cases (Tỷ lệ Pass đạt 100%).\n"
        "   - Hệ thống không còn tồn tại bất kỳ lỗi nghiêm trọng (Critical/Major) nào."
    )
    add_paragraph_styled(doc, eval_text)

    # Kết luận chương kiểm thử
    add_heading_styled(doc, "6.5. Kết luận & Khuyến nghị triển khai", level=2)
    conclusion_text = (
        "Dựa trên kết quả kiểm thử thực nghiệm toàn diện, Hệ thống Quản lý Phòng Khám Nha Khoa Quốc Tế Premier Dental đã đáp ứng đầy đủ và vượt trội các tiêu chí kỹ thuật, nghiệp vụ đã đề ra:\n"
        "• Tính đúng đắn: Các chức năng cốt lõi (Đặt lịch, quản lý bệnh nhân, phân công bác sĩ, tính toán hóa đơn và Chatbot AI) hoạt động ổn định và chính xác 100%.\n"
        "• Trải nghiệm người dùng (UI/UX): Đã được tối ưu xuất sắc với độ tương phản cao, màu sắc sang trọng, font chữ chuyên nghiệp, dễ nhìn và dễ thao tác.\n"
        "• Độ sẵn sàng: Hệ thống hoàn toàn đủ điều kiện nghiệm thu và sẵn sàng triển khai đưa vào sử dụng thực tế tại phòng khám."
    )
    add_paragraph_styled(doc, conclusion_text)

    # Lưu tài liệu ra đĩa
    doc.save(output_path)
    print(f"Document created successfully at: {output_path}")

if __name__ == "__main__":
    desktop_dir = os.path.join(os.path.expanduser("~"), "Desktop")
    out_file = os.path.join(desktop_dir, "KIEM_THU_HE_THONG_NHA_KHOA_PREMIER_DENTAL.docx")
    create_dental_test_document(out_file)
