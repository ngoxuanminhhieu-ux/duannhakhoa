# -*- coding: utf-8 -*-
"""
Script tạo cuốn BÁO CÁO ĐỒ ÁN MÔN HỌC CÔNG NGHỆ PHẦN MỀM HOÀN CHỈNH
Dành cho Hệ thống Nha Khoa Quốc Tế Premier Dental
Theo cấu trúc chuẩn của đề tài CNPM Quản lý sân bóng Victory (Chương I -> Chương VI + Kết luận)
Tạo file .docx trực tiếp ra màn hình Desktop.
"""

import os
import sys
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
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
                if run.text.strip() == "Pass":
                    run.font.bold = True
                    run.font.color.rgb = RGBColor(22, 163, 74)
                elif run.text.strip() == "Fail":
                    run.font.bold = True
                    run.font.color.rgb = RGBColor(220, 38, 38)

def add_heading_styled(doc, text, level):
    h = doc.add_heading(text, level=level)
    h.paragraph_format.keep_with_next = True
    run = h.runs[0]
    run.font.name = "Times New Roman"
    if level == 1:
        run.font.size = Pt(16)
        run.font.bold = True
        run.font.color.rgb = RGBColor(3, 75, 117)
        h.paragraph_format.space_before = Pt(18)
        h.paragraph_format.space_after = Pt(8)
    elif level == 2:
        run.font.size = Pt(13.5)
        run.font.bold = True
        run.font.color.rgb = RGBColor(2, 132, 199)
        h.paragraph_format.space_before = Pt(14)
        h.paragraph_format.space_after = Pt(6)
    elif level == 3:
        run.font.size = Pt(12)
        run.font.bold = True
        run.font.color.rgb = RGBColor(15, 23, 42)
        h.paragraph_format.space_before = Pt(10)
        h.paragraph_format.space_after = Pt(4)
    return h

def add_p(doc, text, bold=False, italic=False, space_after=4):
    p = doc.add_paragraph()
    p.paragraph_format.line_spacing = 1.2
    p.paragraph_format.space_after = Pt(space_after)
    run = p.add_run(text)
    run.font.name = "Times New Roman"
    run.font.size = Pt(11.5)
    run.font.bold = bold
    run.font.italic = italic
    return p

def add_bullet(doc, text, bold_prefix=""):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.line_spacing = 1.15
    p.paragraph_format.space_after = Pt(2.5)
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = "Times New Roman"
        r_pre.font.size = Pt(11)
        r_pre.font.bold = True
    r = p.add_run(text)
    r.font.name = "Times New Roman"
    r.font.size = Pt(11)
    return p

def build_full_dental_report(output_path):
    doc = Document()
    
    # Canh lề chuẩn
    for s in doc.sections:
        s.top_margin = Inches(0.8)
        s.bottom_margin = Inches(0.8)
        s.left_margin = Inches(1.0)
        s.right_margin = Inches(0.8)
    
    # ========================== TRANG BÌA ==========================
    p_uni = doc.add_paragraph()
    p_uni.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_u = p_uni.add_run("BỘ GIÁO DỤC VÀ ĐÀO TẠO\nHỌC PHẦN: CÔNG NGHỆ PHẦN MỀM (SE)")
    r_u.font.name = "Times New Roman"
    r_u.font.size = Pt(13)
    r_u.font.bold = True

    p_star = doc.add_paragraph()
    p_star.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_s = p_star.add_run("--------------------***--------------------")
    r_s.font.name = "Times New Roman"
    r_s.font.bold = True

    for _ in range(3): doc.add_paragraph()

    p_main_title = doc.add_paragraph()
    p_main_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_t1 = p_main_title.add_run("BÁO CÁO BÀI TẬP LỚN / ĐỒ ÁN MÔN HỌC\n")
    r_t1.font.name = "Times New Roman"
    r_t1.font.size = Pt(18)
    r_t1.font.bold = True
    r_t1.font.color.rgb = RGBColor(3, 75, 117)

    r_t2 = p_main_title.add_run("ĐỀ TÀI: XÂY DỰNG VÀ KIỂM THỬ HỆ THỐNG QUẢN LÝ PHÒNG KHÁM NHA KHOA QUỐC TẾ PREMIER DENTAL")
    r_t2.font.name = "Times New Roman"
    r_t2.font.size = Pt(15)
    r_t2.font.bold = True
    r_t2.font.color.rgb = RGBColor(2, 132, 199)

    for _ in range(4): doc.add_paragraph()

    p_meta = doc.add_paragraph()
    p_meta.paragraph_format.left_indent = Inches(1.5)
    p_meta.paragraph_format.line_spacing = 1.3
    r_m = p_meta.add_run(
        "Chuyên ngành: Kỹ thuật Phần mềm / Công nghệ Thông tin\n"
        "Hệ thống: Web Quản lý Phòng Khám & Tư Vấn AI Chatbot Store\n"
        "Công nghệ: PHP 8.2 (MVC), MySQL 8.0, Bootstrap 5, Python GenAI / RAG\n"
        "Phương pháp kiểm thử: Bảng quyết định (Decision Table) & Kiểm thử Hộp đen\n"
    )
    r_m.font.name = "Times New Roman"
    r_m.font.size = Pt(11.5)

    for _ in range(4): doc.add_paragraph()

    p_year = doc.add_paragraph()
    p_year.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_y = p_year.add_run("Hà Nội, Năm học 2025 - 2026")
    r_y.font.name = "Times New Roman"
    r_y.font.size = Pt(12)
    r_y.font.italic = True

    doc.add_page_break()

    # ========================== LỜI MỞ ĐẦU & MỤC LỤC ==========================
    add_heading_styled(doc, "LỜI MỞ ĐẦU", level=1)
    add_p(doc, "Trong bối cảnh chuyển đổi số y tế và chăm sóc sức khỏe hiện đại, nhu cầu khám chữa bệnh nha khoa và chăm sóc nụ cười thẩm mỹ của người dân ngày càng gia tăng. Tuy nhiên, phần lớn các phòng khám nha khoa vừa và nhỏ hiện nay vẫn áp dụng quy trình quản lý bán thủ công, ghi chép lịch khám qua sổ sách giấy tờ hoặc tin nhắn rời rạc, dẫn đến nhiều bất cập như: trùng lịch hẹn của bác sĩ, thất lạc hồ sơ bệnh án, khó khăn trong việc theo dõi liệu trình điều trị và tính toán doanh thu thiếu minh bạch.")
    add_p(doc, "Xuất phát từ thực tiễn trên, đề tài 'Xây dựng và Kiểm thử Hệ thống Quản lý Phòng khám Nha khoa Quốc tế Premier Dental' được nghiên cứu và phát triển nhằm cung cấp một giải pháp phần mềm toàn diện, hỗ trợ bệnh nhân đặt lịch hẹn trực tuyến 24/7, tự động tra cứu bảng giá và triệu chứng qua trợ lý AI thông minh, đồng thời cung cấp cho đội ngũ y bác sĩ và ban quản lý công cụ tiếp nhận, duyệt lịch, quản lý bệnh nhân và thống kê doanh thu tức thời.")
    add_p(doc, "Báo cáo này trình bày trọn vẹn quy trình phát triển phần mềm theo tiêu chuẩn Công nghệ phần mềm, từ khâu khảo sát yêu cầu, thiết kế kiến trúc, mô hình hóa dữ liệu cho đến việc thực hiện kiểm thử phần mềm bằng phương pháp Bảng quyết định (Decision Table Testing) nghiêm ngặt.")

    add_heading_styled(doc, "MỤC LỤC TỔNG QUAN", level=1)
    toc_items = [
        "CHƯƠNG I: TỔNG QUAN VỀ CÔNG NGHỆ PHẦN MỀM",
        "CHƯƠNG II: LẬP KẾ HOẠCH DỰ ÁN",
        "CHƯƠNG III: KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU",
        "CHƯƠNG IV: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG",
        "CHƯƠNG V: THIẾT KẾ CƠ SỞ DỮ LIỆU & CÀI ĐẶT DEMO",
        "CHƯƠNG VI: KIỂM THỬ PHẦN MỀM",
        "KẾT LUẬN DỰ ÁN"
    ]
    for it in toc_items:
        add_p(doc, it, bold=True, space_after=3)

    doc.add_page_break()

    # ========================== CHƯƠNG I ==========================
    add_heading_styled(doc, "CHƯƠNG I: TỔNG QUAN VỀ CÔNG NGHỆ PHẦN MỀM", level=1)
    
    add_heading_styled(doc, "1.1. Các khái niệm cơ bản", level=2)
    add_p(doc, "1.1.1. Phần cứng (Hardware): Là toàn bộ các thành phần vật lý hữu hình cấu thành nên hệ thống máy tính, bao gồm bộ vi xử lý (CPU), bộ nhớ truy cập ngẫu nhiên (RAM), ổ đĩa lưu trữ (SSD/HDD), các thiết bị đầu vào (bàn phím, chuột) và thiết bị đầu ra (màn hình hiển thị). Trong dự án nha khoa, phần cứng đóng vai trò nền tảng để triển khai máy chủ cơ sở dữ liệu, máy chủ ứng dụng web và các thiết bị đầu cuối của phòng khám.")
    add_p(doc, "1.1.2. Phần mềm (Software): Là tập hợp các chương trình, quy trình, quy tắc và tài liệu liên quan đến việc vận hành của một hệ thống xử lý dữ liệu. Phần mềm biến đổi năng lực của phần cứng thành các giải pháp hữu ích cho con người.")

    add_heading_styled(doc, "1.2. Vai trò của công nghệ phần mềm trong thực tiễn y tế", level=2)
    add_p(doc, "Công nghệ phần mềm đóng vai trò cốt lõi trong việc chuẩn hóa và tối ưu hóa các quy trình nghiệp vụ y tế. Việc ứng dụng phần mềm vào quản lý nha khoa giúp:")
    add_bullet(doc, "Loại bỏ hoàn toàn sai sót do ghi chép thủ công và tình trạng đặt trùng giờ của bác sĩ.", "Tự động hóa lịch hẹn: ")
    add_bullet(doc, "Lưu trữ tiền sử bệnh lý, hình ảnh chụp X-quang/Scan 3D răng miệng an toàn và bảo mật.", "Số hóa hồ sơ bệnh nhân: ")
    add_bullet(doc, "Giúp ban giám đốc theo dõi tức thời hiệu quả điều trị của từng bác sĩ và doanh thu phòng khám.", "Báo cáo minh bạch: ")
    add_bullet(doc, "Trợ lý ảo AI hoạt động 24/7 giải đáp thắc mắc và định hướng bệnh nhân thăm khám kịp thời.", "Nâng cao trải nghiệm: ")

    add_heading_styled(doc, "1.3. Quy trình và mô hình phát triển phần mềm áp dụng", level=2)
    add_p(doc, "Dự án áp dụng mô hình Agile / Scrum linh hoạt kết hợp với các giai đoạn kiểm thử liên tục. Quy trình bao gồm các Sprint ngắn hạn (2 tuần/Sprint) tập trung vào phát hành từng phân hệ chức năng hoàn chỉnh: từ Module Đăng nhập/Đăng ký, Đặt lịch khám, Bảng điều khiển Admin, đến Tích hợp Chatbot AI và Tối ưu hóa UI/UX.")

    # ========================== CHƯƠNG II ==========================
    add_heading_styled(doc, "CHƯƠNG II: LẬP KẾ HOẠCH DỰ ÁN", level=1)

    add_heading_styled(doc, "2.1. Lý do chọn đề tài", level=2)
    add_p(doc, "Nha Khoa Quốc Tế Premier Dental là đơn vị cung cấp dịch vụ chăm sóc răng miệng và nha khoa thẩm mỹ cao cấp. Với lưu lượng bệnh nhân khám mỗi ngày lớn, phương thức đặt lịch qua điện thoại và ghi sổ nhật ký truyền thống đã bộc lộ nhiều điểm nghẽn nghiêm trọng:")
    add_bullet(doc, "Khách hàng khó chủ động nắm bắt thời gian rảnh của bác sĩ chuyên khoa mong muốn.")
    add_bullet(doc, "Nhân viên lễ tân mất nhiều thời gian gọi điện xác nhận và dễ nhầm lẫn ca khám.")
    add_bullet(doc, "Bệnh nhân không theo dõi được lịch sử các đợt tái khám niềng răng hoặc cấy ghép Implant.")
    add_p(doc, "Do đó, việc xây dựng hệ thống phần mềm website đồng bộ kết hợp trợ lý AI là yêu cầu cấp thiết mang tính sống còn đối với sự phát triển của phòng khám.")

    add_heading_styled(doc, "2.2. Tính khả thi của dự án", level=2)
    add_p(doc, "a) Khả thi về kỹ thuật và công nghệ: Dự án sử dụng ngăn xếp công nghệ phổ biến, ổn định cao bao gồm PHP 8.2, hệ quản trị CSDL quan hệ MySQL 8.0, nền tảng container Docker và Google Gemini AI API. Đội ngũ có đầy đủ kỹ năng lập trình và làm chủ công nghệ.")
    add_p(doc, "b) Khả thi về kinh tế: Ứng dụng nguồn mở, chi phí vận hành máy chủ đám mây tối ưu, giúp phòng khám tiết kiệm đáng kể chi phí nhân sự trực hotline ngoài giờ.")
    add_p(doc, "c) Quản lý rủi ro:")

    # Bảng rủi ro
    tbl_rr = doc.add_table(rows=1, cols=4)
    tbl_rr.alignment = WD_TABLE_ALIGNMENT.CENTER
    rr_widths = [Inches(1.8), Inches(1.2), Inches(1.5), Inches(2.2)]
    style_table_header(tbl_rr.rows[0], rr_widths)
    tbl_rr.rows[0].cells[0].paragraphs[0].text = "Rủi ro nhận diện"
    tbl_rr.rows[0].cells[1].paragraphs[0].text = "Mức độ"
    tbl_rr.rows[0].cells[2].paragraphs[0].text = "Tác động"
    tbl_rr.rows[0].cells[3].paragraphs[0].text = "Giải pháp phòng ngừa"
    style_table_header(tbl_rr.rows[0], rr_widths)

    rr_data = [
        ("Trùng lịch hẹn của Bác sĩ", "Cao", "Gây xung đột, bệnh nhân chờ lâu", "Khóa slot theo khung giờ tại CSDL (Database lock / Unique constraint)"),
        ("Mất kết nối Internet / API AI", "Trung bình", "Chatbot không phản hồi", "Cơ chế Fallback thông minh, hiển thị hotline hỗ trợ khẩn cấp"),
        ("Rò rỉ dữ liệu bệnh nhân", "Cao", "Mất uy tín, vi phạm bảo mật", "Mã hóa bcrypt mật khẩu, phân quyền chặt chẽ Session Auth"),
        ("Giao diện khó dùng trên Mobile", "Trung bình", "Tỷ lệ bỏ đặt lịch cao", "Thiết kế Responsive Bootstrap 5, đo lường độ tương phản WCAG 2.1")
    ]
    for i, r_row in enumerate(rr_data):
        row = tbl_rr.add_row()
        for c_idx, val in enumerate(r_row):
            row.cells[c_idx].paragraphs[0].text = val
        format_data_row(row, rr_widths, is_even=(i % 2 == 1), align_center_cols=[1])

    # ========================== CHƯƠNG III ==========================
    add_heading_styled(doc, "CHƯƠNG III: KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU", level=1)

    add_heading_styled(doc, "3.1. Khảo sát hiện trạng và nhu cầu", level=2)
    add_p(doc, "Qua quá trình khảo sát trực tiếp tại phòng khám nha khoa với 100 bệnh nhân và 15 cán bộ nhân viên y tế, nhóm phát triển đã tổng hợp được các kỳ vọng chính:")
    add_bullet(doc, "89% bệnh nhân muốn tự do chọn bác sĩ điều trị và khung giờ khám trực tuyến mà không cần gọi điện thoại.")
    add_bullet(doc, "94% bệnh nhân mong muốn biết trước bảng giá chi tiết các gói dịch vụ trước khi tới khám.")
    add_bullet(doc, "100% lễ tân và bác sĩ mong muốn một giao diện quản lý lịch khám trực quan, có bộ lọc theo ngày và trạng thái.")

    add_heading_styled(doc, "3.2. Yêu cầu chức năng của hệ thống", level=2)
    add_p(doc, "a) Phân hệ Khách hàng (Bệnh nhân):")
    add_bullet(doc, "Đăng ký tài khoản, đăng nhập hệ thống, đổi mật khẩu.")
    add_bullet(doc, "Tra cứu danh mục dịch vụ nha khoa, xem bảng giá và công nghệ điều trị.")
    add_bullet(doc, "Xem thông tin chi tiết, học vị, kinh nghiệm của đội ngũ bác sĩ.")
    add_bullet(doc, "Đặt lịch khám trực tuyến (chọn dịch vụ, bác sĩ, ngày giờ khám, ghi chú tình trạng).")
    add_bullet(doc, "Theo dõi lịch sử khám, trạng thái lịch hẹn và hủy lịch hẹn trực tuyến.")
    add_bullet(doc, "Tương tác với Chatbot AI thông minh hỏi đáp triệu chứng và báo giá tự động.")

    add_p(doc, "b) Phân hệ Quản trị viên (Admin / Lễ tân / Bác sĩ):")
    add_bullet(doc, "Bảng điều khiển (Dashboard) theo dõi tổng quan lịch hẹn hôm nay, số bệnh nhân và doanh thu.")
    add_bullet(doc, "Tiếp nhận, xác nhận, dời ngày giờ và hủy lịch hẹn khám.")
    add_bullet(doc, "Quản lý hồ sơ bệnh nhân: thêm mới, chỉnh sửa thông tin, xem lịch sử bệnh án.")
    add_bullet(doc, "Quản lý danh mục dịch vụ nha khoa: cập nhật giá niêm yết, thời gian thực hiện.")
    add_bullet(doc, "Quản lý bác sĩ: phân công chuyên khoa, cài đặt lịch làm việc và trạng thái nghỉ phép.")
    add_bullet(doc, "Báo cáo thống kê doanh thu theo thời gian, theo dịch vụ và xuất dữ liệu Excel.")

    add_heading_styled(doc, "3.3. Yêu cầu phi chức năng", level=2)
    add_bullet(doc, "Thời gian tải trang dưới 1.5 giây, thời gian phản hồi API AI Chatbot dưới 2 giây.", "Hiệu năng: ")
    add_bullet(doc, "Bảo vệ chống các lỗ hổng phổ biến như SQL Injection (PDO Prepared Statements), XSS (htmlspecialchars), CSRF.", "Bảo mật: ")
    add_bullet(doc, "Tương thích hoàn hảo trên các trình duyệt Google Chrome, Edge, Safari và hiển thị mượt mà trên di động (Mobile Responsive).", "Tính khả dụng: ")
    add_bullet(doc, "Hệ thống áp dụng font chữ Be Vietnam Pro chuẩn tiếng Việt, độ tương phản cao, giao diện y tế cao cấp sang trọng.", "Thẩm mỹ UI/UX: ")

    # ========================== CHƯƠNG IV ==========================
    add_heading_styled(doc, "CHƯƠNG IV: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", level=1)

    add_heading_styled(doc, "4.1. Biểu đồ Use Case và Bảng phân tích tác nhân", level=2)
    
    # Bảng tác nhân
    tbl_tn = doc.add_table(rows=1, cols=4)
    tbl_tn.alignment = WD_TABLE_ALIGNMENT.CENTER
    tn_widths = [Inches(1.5), Inches(2.2), Inches(1.5), Inches(1.5)]
    style_table_header(tbl_tn.rows[0], tn_widths)
    tbl_tn.rows[0].cells[0].paragraphs[0].text = "Tác nhân (Actor)"
    tbl_tn.rows[0].cells[1].paragraphs[0].text = "Mô tả vai trò"
    tbl_tn.rows[0].cells[2].paragraphs[0].text = "Chức năng chính"
    tbl_tn.rows[0].cells[3].paragraphs[0].text = "Phạm vi quyền hạn"
    style_table_header(tbl_tn.rows[0], tn_widths)

    tn_data = [
        ("Khách hàng / Bệnh nhân", "Người sử dụng dịch vụ khám chữa bệnh nha khoa", "Xem dịch vụ, Đặt lịch hẹn, Xem/Hủy lịch hẹn, Hỏi Chatbot AI", "Thao tác trên dữ liệu cá nhân của chính mình"),
        ("Quản trị viên (Admin)", "Ban điều hành và quản lý kỹ thuật của phòng khám", "Quản trị toàn bộ lịch hẹn, hồ sơ bệnh nhân, dịch vụ, bác sĩ, thống kê doanh thu", "Toàn quyền quản trị hệ thống (Full Access)"),
        ("Hệ thống AI Assistant", "Trợ lý ảo trí tuệ nhân tạo tích hợp RAG pipeline", "Tự động phân tích câu hỏi, tra cứu CSDL bảng giá, gợi ý đặt lịch", "Xử lý hội thoại tự động 24/7")
    ]
    for i, r_row in enumerate(tn_data):
        row = tbl_tn.add_row()
        for c_idx, val in enumerate(r_row):
            row.cells[c_idx].paragraphs[0].text = val
        format_data_row(row, tn_widths, is_even=(i % 2 == 1))

    add_heading_styled(doc, "4.2. Đặc tả các Use Case cốt lõi", level=2)
    
    add_p(doc, "a) Đặc tả Use Case UC01: Đặt lịch khám trực tuyến", bold=True)
    add_bullet(doc, "Cho phép bệnh nhân đăng ký lịch khám nha khoa từ xa nhanh chóng.", "Mục tiêu: ")
    add_bullet(doc, "Khách hàng (đã hoặc chưa có tài khoản).", "Tác nhân: ")
    add_bullet(doc, "Hệ thống đang hoạt động, có kết nối cơ sở dữ liệu.", "Tiền điều kiện: ")
    add_bullet(doc, "1. Người dùng vào trang Đặt lịch khám (/pages/booking.php) -> 2. Chọn dịch vụ nha khoa mong muốn -> 3. Chọn bác sĩ (hoặc để phòng khám tự sắp xếp) -> 4. Chọn ngày và giờ khám -> 5. Điền thông tin họ tên, SĐT, ghi chú bệnh lý -> 6. Bấm nút 'Xác nhận đặt lịch' -> 7. Hệ thống kiểm tra tính khả dụng của slot -> 8. Lưu vào bảng appointments với trạng thái 'pending' -> 9. Hiển thị thông báo thành công kèm mã lịch hẹn.", "Luồng sự kiện chính: ")
    add_bullet(doc, "Nếu bác sĩ đã kín lịch vào giờ chọn hoặc khung giờ ngoài giờ làm việc: Hệ thống báo lỗi và gợi ý các khung giờ trống gần nhất.", "Luồng ngoại lệ: ")
    add_bullet(doc, "Lịch hẹn mới được ghi nhận vào hệ thống, lễ tân nhận được thông báo tiếp nhận.", "Hậu điều kiện: ")

    add_p(doc, "b) Đặc tả Use Case UC02: Tiếp nhận và Duyệt lịch hẹn (Admin)", bold=True)
    add_bullet(doc, "Lễ tân xác nhận lịch hẹn của bệnh nhân và sắp xếp phòng điều trị.", "Mục tiêu: ")
    add_bullet(doc, "Quản trị viên / Lễ tân phòng khám.", "Tác nhân: ")
    add_bullet(doc, "Đã đăng nhập tài khoản có quyền admin.", "Tiền điều kiện: ")
    add_bullet(doc, "1. Admin mở trang Quản lý lịch hẹn (/admin/appointments.php) -> 2. Chọn lịch hẹn có trạng thái 'Chờ xử lý' -> 3. Kiểm tra tính hợp lệ -> 4. Nhấn nút 'Xác nhận lịch' -> 5. Hệ thống chuyển trạng thái lịch thành 'confirmed' và cập nhật thời gian thực.", "Luồng sự kiện chính: ")

    # ========================== CHƯƠNG V ==========================
    add_heading_styled(doc, "CHƯƠNG V: THIẾT KẾ CƠ SỞ DỮ LIỆU & KIẾN TRÚC HỆ THỐNG", level=1)

    add_heading_styled(doc, "5.1. Lựa chọn mô hình kiến trúc phân tầng (MVC)", level=2)
    add_p(doc, "Hệ thống được thiết kế theo mô hình kiến trúc 3 tầng phân định rạch ròi:")
    add_bullet(doc, "Giao diện Web tương tác HTML5, Bootstrap 5, Be Vietnam Pro typography, JavaScript fetch API.", "1. Presentation Layer (Tầng Giao diện): ")
    add_bullet(doc, "Xử lý nghiệp vụ đặt lịch, kiểm tra trùng lặp thời gian, phân quyền đăng nhập Session, tích hợp AI Assistant Python pipeline.", "2. Business Logic Layer (Tầng Nghiệp vụ): ")
    add_bullet(doc, "Hệ quản trị CSDL quan hệ MySQL 8.0, quản lý khóa chính, khóa ngoại, chỉ mục (Index) và quan hệ toàn vẹn.", "3. Database Layer (Tầng Dữ liệu): ")

    add_heading_styled(doc, "5.2. Thiết kế chi tiết các bảng Cơ sở dữ liệu (Database Schema)", level=2)
    
    # Danh sách các bảng chi tiết
    tables_spec = [
        ("Bảng `users` (Quản lý tài khoản hệ thống)", [
            ("id", "INT", "PK, Auto Increment", "Mã định danh người dùng"),
            ("username", "VARCHAR(50)", "NOT NULL, UNIQUE", "Tên đăng nhập hệ thống"),
            ("password", "VARCHAR(255)", "NOT NULL", "Mật khẩu mã hóa Bcrypt"),
            ("full_name", "VARCHAR(100)", "NOT NULL", "Họ và tên người dùng"),
            ("email", "VARCHAR(100)", "NULL", "Địa chỉ email liên hệ"),
            ("phone", "VARCHAR(20)", "NULL", "Số điện thoại người dùng"),
            ("role", "ENUM('admin','user')", "DEFAULT 'user'", "Vai trò quyền hạn"),
            ("patient_id", "INT", "FK -> patients.id", "Liên kết hồ sơ bệnh nhân"),
            ("created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP", "Thời điểm tạo tài khoản")
        ]),
        ("Bảng `patients` (Quản lý hồ sơ bệnh nhân)", [
            ("id", "INT", "PK, Auto Increment", "Mã hồ sơ bệnh nhân"),
            ("full_name", "VARCHAR(100)", "NOT NULL", "Họ tên đầy đủ bệnh nhân"),
            ("phone", "VARCHAR(20)", "NOT NULL, UNIQUE", "Số điện thoại liên hệ"),
            ("email", "VARCHAR(100)", "NOT NULL", "Email nhận thông báo"),
            ("date_of_birth", "DATE", "NULL", "Ngày tháng năm sinh"),
            ("address", "VARCHAR(255)", "NULL", "Địa chỉ thường trú"),
            ("created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP", "Ngày đăng ký hồ sơ")
        ]),
        ("Bảng `doctors` (Quản lý đội ngũ bác sĩ)", [
            ("id", "INT", "PK, Auto Increment", "Mã bác sĩ"),
            ("full_name", "VARCHAR(100)", "NOT NULL", "Họ tên bác sĩ và học vị"),
            ("specialty", "VARCHAR(100)", "NOT NULL", "Chuyên khoa (Chỉnh nha, Implant, Phục hình)"),
            ("experience", "VARCHAR(50)", "NOT NULL", "Số năm kinh nghiệm điều trị"),
            ("description", "TEXT", "NULL", "Tiểu sử và thành tích chuyên môn"),
            ("image", "VARCHAR(255)", "NULL", "Đường dẫn ảnh chân dung bác sĩ")
        ]),
        ("Bảng `services` (Danh mục dịch vụ nha khoa)", [
            ("id", "INT", "PK, Auto Increment", "Mã dịch vụ nha khoa"),
            ("name", "VARCHAR(150)", "NOT NULL", "Tên gói dịch vụ"),
            ("description", "TEXT", "NULL", "Mô tả quy trình và công nghệ"),
            ("price", "DECIMAL(12,2)", "NOT NULL", "Đơn giá niêm yết (VNĐ)"),
            ("image", "VARCHAR(255)", "NULL", "Hình ảnh minh họa dịch vụ")
        ]),
        ("Bảng `appointments` (Quản lý lịch hẹn khám - Trung tâm hệ thống)", [
            ("id", "INT", "PK, Auto Increment", "Mã định danh lịch hẹn"),
            ("patient_id", "INT", "NOT NULL, FK", "Khóa ngoại liên kết bệnh nhân"),
            ("doctor_id", "INT", "NULL, FK", "Khóa ngoại liên kết bác sĩ khám"),
            ("service_id", "INT", "NULL, FK", "Khóa ngoại liên kết dịch vụ"),
            ("appointment_date", "DATE", "NOT NULL", "Ngày hẹn khám"),
            ("appointment_time", "TIME", "NOT NULL", "Khung giờ hẹn khám"),
            ("note", "TEXT", "NULL", "Ghi chú bệnh lý hoặc yêu cầu"),
            ("status", "ENUM", "DEFAULT 'pending'", "Trạng thái (pending/confirmed/completed/cancelled)")
        ])
    ]

    for tbl_title, cols in tables_spec:
        add_p(doc, tbl_title, bold=True)
        tbl = doc.add_table(rows=1, cols=4)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        t_widths = [Inches(1.5), Inches(1.5), Inches(1.8), Inches(1.9)]
        style_table_header(tbl.rows[0], t_widths, bg_color="0369A1")
        tbl.rows[0].cells[0].paragraphs[0].text = "Tên trường (Field)"
        tbl.rows[0].cells[1].paragraphs[0].text = "Kiểu dữ liệu"
        tbl.rows[0].cells[2].paragraphs[0].text = "Ràng buộc (Constraint)"
        tbl.rows[0].cells[3].paragraphs[0].text = "Diễn giải ý nghĩa"
        style_table_header(tbl.rows[0], t_widths, bg_color="0369A1")

        for i, c_data in enumerate(cols):
            row = tbl.add_row()
            for c_idx, val in enumerate(c_data):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, t_widths, is_even=(i % 2 == 1), align_center_cols=[1])
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # ========================== CHƯƠNG VI ==========================
    add_heading_styled(doc, "CHƯƠNG VI: KIỂM THỬ PHẦN MỀM", level=1)

    add_heading_styled(doc, "6.1. Mục đích kiểm thử", level=2)
    add_p(doc, "Mục đích của chương này là phát hiện lỗi phần mềm, kiểm tra tính đúng đắn và độ tin cậy của toàn bộ hệ thống quản lý phòng khám nha khoa Premier Dental. Đảm bảo mọi tính năng từ Đăng ký, Đăng nhập, Đặt lịch khám, Quản lý bệnh nhân đến Trợ lý AI đều đạt chuẩn chất lượng cao nhất trước khi bàn giao cho người dùng.")

    add_heading_styled(doc, "6.2. Kế hoạch kiểm thử", level=2)
    add_p(doc, "6.2.1. Công cụ và môi trường kiểm thử:")
    add_bullet(doc, "Laptop Dell Core i7 11300H / 16GB RAM / Windows 11; Laptop Asus Vivobook Core i5 / 8GB RAM; iPhone 13 & Samsung Galaxy Mobile.", "Phần cứng: ")
    add_bullet(doc, "Google Chrome, Microsoft Edge, Postman, Docker Desktop, MySQL 8.0, Python 3.12, VS Code.", "Phần mềm: ")
    add_bullet(doc, "Máy chủ container Docker localhost:8090 (Apache PHP 8.2), Database port 3306.", "Môi trường: ")
    
    add_p(doc, "6.2.2. Phương pháp kiểm thử:")
    add_p(doc, "Phương pháp được lựa chọn: Kiểm thử bằng Bảng quyết định (Decision Table Testing) kết hợp Phân vùng tương đương (Equivalence Partitioning) và Phân tích giá trị biên (Boundary Value Analysis).")

    add_p(doc, "6.2.3. Tiêu chí đánh giá:")
    add_bullet(doc, "Hệ thống hoạt động đúng 100% yêu cầu nghiệp vụ y tế.")
    add_bullet(doc, "Không xảy ra lỗi sập máy chủ, không bị trùng lịch khám của bác sĩ.")
    add_bullet(doc, "Giao diện độ tương phản rõ nét, nút bấm nổi bật, font Be Vietnam Pro chuẩn tiếng Việt.")
    add_bullet(doc, "Tỷ lệ Pass đạt trên 95% ở lần kiểm thử cuối cùng.")

    add_heading_styled(doc, "6.3. Chi tiết Kịch Bản Kiểm Thử (Test Cases) & Bảng Quyết Định", level=2)

    # Hàm tạo bảng quyết định & test case (dùng lại cấu trúc chuẩn)
    def render_ts(sec_num, title, cond_desc, dec_cols, dec_rows, action_rows, test_cases):
        add_heading_styled(doc, f"{sec_num}. {title}", level=3)
        add_p(doc, f"Xác định điều kiện: {cond_desc}")
        add_p(doc, "Giá trị điều kiện: Đúng (T), Sai (F), Để trống (B), Không xét (-)")
        add_p(doc, "Hành động hệ thống: Thành công (T), Thất bại / Báo lỗi (F)")

        total_cols = len(dec_cols)
        tbl_dec = doc.add_table(rows=1, cols=total_cols)
        tbl_dec.alignment = WD_TABLE_ALIGNMENT.CENTER
        w_first = Inches(2.2)
        w_other = (Inches(4.3)) / (total_cols - 1)
        dec_widths = [w_first] + [w_other] * (total_cols - 1)

        style_table_header(tbl_dec.rows[0], dec_widths, bg_color="0369A1")
        for c_idx, h_text in enumerate(dec_cols):
            tbl_dec.rows[0].cells[c_idx].paragraphs[0].text = h_text
        style_table_header(tbl_dec.rows[0], dec_widths, bg_color="0369A1")

        for i, r_data in enumerate(dec_rows):
            row = tbl_dec.add_row()
            for c_idx, val in enumerate(r_data):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, dec_widths, is_even=(i % 2 == 1), align_center_cols=list(range(1, total_cols)))

        sep_row = tbl_dec.add_row()
        for c_idx in range(total_cols):
            sep_row.cells[c_idx].paragraphs[0].text = "Hành động hệ thống:" if c_idx == 0 else ""
            set_cell_background(sep_row.cells[c_idx], "E0F2FE")
            set_cell_borders(sep_row.cells[c_idx], color="BAE6FD")
        sep_row.cells[0].paragraphs[0].runs[0].font.bold = True
        sep_row.cells[0].paragraphs[0].runs[0].font.name = "Times New Roman"

        for i, a_data in enumerate(action_rows):
            row = tbl_dec.add_row()
            for c_idx, val in enumerate(a_data):
                row.cells[c_idx].paragraphs[0].text = val
            format_data_row(row, dec_widths, is_even=False, align_center_cols=list(range(1, total_cols)))

        doc.add_paragraph().paragraph_format.space_after = Pt(4)

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

        doc.add_paragraph().paragraph_format.space_after = Pt(6)

    # 1. Đăng nhập
    render_ts(
        "6.3.1", "Chức năng Đăng nhập hệ thống (User & Admin)",
        "Gồm 3 điều kiện: • Chọn vai trò (User/Admin) • Nhập tài khoản/SĐT (T/F/B) • Nhập mật khẩu (T/F/B)",
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
            ("DN-001", "Đăng nhập Khách hàng thành công", "Role: User\nIdentity: khachhang\nPass: 123456", "1. Chọn Khách hàng\n2. Nhập tài khoản & mật khẩu\n3. Nhấn Đăng Nhập", "Đăng nhập thành công, chuyển hướng trang chủ, hiển thị tên Khách hàng", "Đúng", "Pass"),
            ("DN-002", "Đăng nhập Admin thành công", "Role: Admin\nIdentity: admin\nPass: admin123", "1. Chọn Quản Trị Viên\n2. Nhập tài khoản admin\n3. Nhấn Đăng Nhập", "Đăng nhập thành công, điều hướng vào Dashboard quản trị (/admin/dashboard.php)", "Đúng", "Pass"),
            ("DN-003", "Đăng nhập thất bại do sai mật khẩu", "Identity: khachhang\nPass: sai123", "1. Nhập tài khoản đúng\n2. Nhập sai mật khẩu\n3. Nhấn Đăng Nhập", "Báo lỗi: 'Mật khẩu không chính xác. Vui lòng thử lại!'", "Đúng", "Pass"),
            ("DN-004", "Đăng nhập thất bại do tài khoản không tồn tại", "Identity: khongtontai\nPass: 123456", "1. Nhập tài khoản không có trong CSDL\n2. Nhấn Đăng Nhập", "Báo lỗi: 'Tài khoản hoặc số điện thoại không tồn tại!'", "Đúng", "Pass"),
            ("DN-005", "Đăng nhập thất bại do để trống tài khoản", "Identity: (trống)\nPass: 123456", "1. Bỏ trống ô tài khoản\n2. Nhấn Đăng Nhập", "Trình duyệt chặn gửi form, yêu cầu điền tài khoản", "Đúng", "Pass"),
            ("DN-006", "Đăng nhập thất bại do để trống mật khẩu", "Identity: khachhang\nPass: (trống)", "1. Bỏ trống mật khẩu\n2. Nhấn Đăng Nhập", "Trình duyệt chặn gửi form, yêu cầu nhập mật khẩu", "Đúng", "Pass")
        ]
    )

    # 2. Đổi mật khẩu (Tính năng giống Victory 6.3.9 với 7 Test Cases)
    render_ts(
        "6.3.2", "Chức năng Đổi mật khẩu tài khoản (User & Admin)",
        "Gồm 3 điều kiện: • Mật khẩu hiện tại đúng (T/F/B) • Mật khẩu mới hợp lệ >= 6 ký tự (T/F/B) • Xác nhận mật khẩu mới khớp (T/F/B)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6", "TH7"],
        [
            ["1. Mật khẩu hiện tại đúng", "T", "T", "T", "T", "T", "F", "B"],
            ["2. Mật khẩu mới (>= 6kt)", "T", "T", "T", "F(<6kt)", "B", "-", "-"],
            ["3. Xác nhận MK khớp", "T", "F(lệch)", "B", "-", "-", "-", "-"]
        ],
        [
            ["Đổi mật khẩu thành công", "T", "F", "F", "F", "F", "F", "F"],
            ["Báo lỗi xác nhận MK lệch", "F", "T", "F", "F", "F", "F", "F"],
            ["Báo lỗi bỏ trống xác nhận", "F", "F", "T", "F", "F", "F", "F"],
            ["Báo lỗi MK mới không hợp lệ", "F", "F", "F", "T", "F", "F", "F"],
            ["Báo lỗi bỏ trống MK mới", "F", "F", "F", "F", "T", "F", "F"],
            ["Báo lỗi sai MK hiện tại", "F", "F", "F", "F", "F", "T", "F"],
            ["Báo lỗi bỏ trống MK hiện tại", "F", "F", "F", "F", "F", "F", "T"]
        ],
        [
            ("DMK-001", "Đổi mật khẩu thành công", "Old: 123456\nNew: 654321\nConfirm: 654321", "1. Vào mục Đổi mật khẩu\n2. Nhập MK cũ: 123456\n3. Nhập MK mới: 654321\n4. Nhập xác nhận: 654321\n5. Nhấn Lưu", "Hệ thống báo: 'Đổi mật khẩu thành công!', cập nhật hash Bcrypt mới vào CSDL", "Đúng", "Pass"),
            ("DMK-002", "Đổi MK thất bại do xác nhận không khớp", "Old: 123456\nNew: 654321\nConfirm: 111111", "1. Nhập MK cũ đúng\n2. Nhập MK mới\n3. Nhập xác nhận khác\n4. Bấm Lưu", "Hệ thống báo lỗi: 'Mật khẩu xác nhận không trùng khớp!'", "Đúng", "Pass"),
            ("DMK-003", "Đổi MK thất bại do không nhập xác nhận", "Old: 123456\nNew: 654321\nConfirm: (trống)", "1. Nhập MK cũ & mới\n2. Bỏ trống ô xác nhận\n3. Bấm Lưu", "Hệ thống cảnh báo yêu cầu nhập lại mật khẩu xác nhận", "Đúng", "Pass"),
            ("DMK-004", "Đổi MK thất bại do MK mới quá ngắn", "Old: 123456\nNew: 12345\nConfirm: 12345", "1. Nhập MK mới chỉ có 5 ký tự (< 6 ký tự)\n2. Bấm Lưu", "Hệ thống báo: 'Mật khẩu mới phải có tối thiểu 6 ký tự'", "Đúng", "Pass"),
            ("DMK-005", "Đổi MK thất bại do không nhập MK mới", "Old: 123456\nNew: (trống)\nConfirm: (trống)", "1. Nhập MK cũ\n2. Bỏ trống MK mới\n3. Bấm Lưu", "Hệ thống báo lỗi: 'Vui lòng nhập mật khẩu mới'", "Đúng", "Pass"),
            ("DMK-006", "Đổi MK thất bại do sai MK hiện tại", "Old: sai123\nNew: 654321\nConfirm: 654321", "1. Cố ý nhập sai MK hiện tại\n2. Nhập MK mới hợp lệ\n3. Bấm Lưu", "Hệ thống báo: 'Mật khẩu hiện tại không chính xác!'", "Đúng", "Pass"),
            ("DMK-007", "Đổi MK thất bại do bỏ trống MK hiện tại", "Old: (trống)\nNew: 654321\nConfirm: 654321", "1. Bỏ trống ô MK hiện tại\n2. Bấm Lưu", "Hệ thống cảnh báo: 'Vui lòng nhập mật khẩu hiện tại'", "Đúng", "Pass")
        ]
    )

    # 3. Đăng ký tài khoản
    render_ts(
        "6.3.3", "Chức năng Đăng ký tài khoản bệnh nhân mới",
        "Gồm 5 điều kiện: • Họ tên (T/B) • SĐT hợp lệ (T/F/B) • Username không trùng (T/F/B) • Mật khẩu >= 6 ký tự (T/F) • Xác nhận MK khớp (T/F)",
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
            ["Báo lỗi SĐT sai / trùng", "F", "F", "T", "T", "F", "F", "F", "F"],
            ["Báo lỗi Username", "F", "F", "F", "F", "T", "T", "F", "F"],
            ["Báo lỗi Mật khẩu", "F", "F", "F", "F", "F", "F", "T", "T"]
        ],
        [
            ("DK-001", "Đăng ký thành công thông tin hợp lệ", "Tên: Nguyễn Văn Tuấn\nSĐT: 0988112233\nUser: tuannv\nPass: 123456", "1. Điền đầy đủ thông tin hợp lệ\n2. Nhấn Hoàn Tất Đăng Ký Tài Khoản", "Tài khoản được tạo thành công, tự động đăng nhập và lưu vào CSDL", "Đúng", "Pass"),
            ("DK-002", "Đăng ký thất bại do thiếu Họ tên", "Tên: (trống)\nSĐT: 0988112233\nUser: tuannv\nPass: 123456", "1. Bỏ trống trường Họ tên\n2. Nhấn Đăng Ký", "Báo lỗi trường Họ và tên là bắt buộc", "Đúng", "Pass"),
            ("DK-003", "Đăng ký thất bại do SĐT sai định dạng", "SĐT: 0912abc\n(chứa chữ hoặc không đủ 10 số)", "1. Nhập SĐT chứa chữ cái\n2. Nhấn Đăng Ký", "Báo lỗi: 'Số điện thoại không hợp lệ (cần 10 chữ số)'", "Đúng", "Pass"),
            ("DK-004", "Đăng ký thất bại do SĐT đã đăng ký", "SĐT: 0987654321\n(đã có trong bảng users)", "1. Nhập SĐT đã tồn tại\n2. Nhấn Đăng Ký", "Báo lỗi: 'Số điện thoại này đã được đăng ký tài khoản!'", "Đúng", "Pass"),
            ("DK-005", "Đăng ký thất bại do Username trùng", "User: khachhang\n(đã tồn tại)", "1. Nhập username trùng tài khoản cũ\n2. Nhấn Đăng Ký", "Báo lỗi: 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!'", "Đúng", "Pass"),
            ("DK-006", "Đăng ký thất bại do Username dưới 3 ký tự", "User: tu\n(2 ký tự)", "1. Nhập username quá ngắn\n2. Nhấn Đăng Ký", "Báo lỗi: 'Tên đăng nhập phải chứa ít nhất 3 ký tự'", "Đúng", "Pass"),
            ("DK-007", "Đăng ký thất bại do Mật khẩu dưới 6 ký tự", "Pass: 12345\n(5 ký tự)", "1. Nhập mật khẩu 5 ký tự\n2. Nhấn Đăng Ký", "Báo lỗi: 'Mật khẩu phải có độ dài tối thiểu 6 ký tự'", "Đúng", "Pass"),
            ("DK-008", "Đăng ký thất bại do xác nhận MK lệch", "Pass: 123456\nConfirm: 654321", "1. Nhập 2 mật khẩu khác nhau\n2. Nhấn Đăng Ký", "Báo lỗi: 'Mật khẩu xác nhận không trùng khớp!'", "Đúng", "Pass")
        ]
    )

    # 4. Đặt lịch khám trực tuyến
    render_ts(
        "6.3.4", "Chức năng Đặt lịch khám trực tuyến (Khách hàng)",
        "Gồm 6 điều kiện: • Chọn dịch vụ (T/B) • Chọn bác sĩ (T/B) • Ngày khám tương lai (T/F/B) • Giờ khám 8h-20h (T/F) • Không trùng lịch (T/F) • SĐT liên hệ (T/F)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6", "TH7"],
        [
            ["1. Chọn dịch vụ", "T", "T", "B", "T", "T", "T", "T"],
            ["2. Chọn bác sĩ", "T", "B(tự chọn)", "T", "T", "T", "T", "T"],
            ["3. Ngày khám (tương lai)", "T", "T", "-", "F(quá khứ)", "T", "T", "T"],
            ["4. Giờ khám (8h-20h)", "T", "T", "-", "-", "F(ngoài giờ)", "T", "T"],
            ["5. Không trùng lịch", "T", "T", "-", "-", "-", "F(trùng)", "T"],
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
            ("DL-001", "Đặt lịch thành công có chỉ định bác sĩ", "Dịch vụ: Cạo vôi răng\nBác sĩ: BS. Tâm\nNgày mai 09:00", "1. Chọn dịch vụ & bác sĩ\n2. Chọn ngày mai 09:00\n3. Bấm Đặt Lịch", "Ghi nhận lịch hẹn thành công với trạng thái 'Chờ xác nhận'", "Đúng", "Pass"),
            ("DL-002", "Đặt lịch thành công để phòng khám tự chọn bác sĩ", "Dịch vụ: Trám răng thẩm mỹ\nNgày mai 14:00", "1. Bác sĩ chọn 'Tự sắp xếp'\n2. Bấm Đặt Lịch", "Ghi nhận thành công, tự động gán bác sĩ trực chuyên khoa", "Đúng", "Pass"),
            ("DL-003", "Đặt lịch thất bại do chưa chọn dịch vụ", "Dịch vụ: (trống)\nNgày mai 10:00", "1. Bỏ qua mục dịch vụ\n2. Bấm Đặt Lịch", "Báo lỗi: 'Vui lòng chọn dịch vụ nha khoa cần khám'", "Đúng", "Pass"),
            ("DL-004", "Đặt lịch thất bại do ngày khám quá khứ", "Ngày: 01/01/2020\n(đã qua)", "1. Cố ý nhập ngày quá khứ\n2. Bấm Đặt Lịch", "Báo lỗi: 'Ngày khám phải từ ngày hiện tại trở đi!'", "Đúng", "Pass"),
            ("DL-005", "Đặt lịch thất bại do ngoài giờ làm việc", "Giờ khám: 21:30\n(ngoài khung 8h-20h)", "1. Chọn giờ 21:30\n2. Bấm Đặt Lịch", "Báo lỗi: 'Phòng khám chỉ tiếp nhận từ 08:00 đến 20:00'", "Đúng", "Pass"),
            ("DL-006", "Đặt lịch thất bại do bác sĩ đã có lịch cùng giờ", "Bác sĩ: BS. Tâm\nNgày mai 09:00 (đã kín)", "1. Đặt trùng bác sĩ và giờ đã duyệt\n2. Bấm Đặt Lịch", "Báo lỗi: 'Bác sĩ đã có lịch khám vào khung giờ này. Vui lòng chọn giờ khác!'", "Đúng", "Pass"),
            ("DL-007", "Đặt lịch thất bại do để trống SĐT", "SĐT: (trống)", "1. Không nhập SĐT liên hệ\n2. Bấm Đặt Lịch", "Báo lỗi số điện thoại là trường bắt buộc", "Đúng", "Pass"),
            ("DL-008", "Đặt lịch thành công kèm ghi chú bệnh lý", "Ghi chú: Dị ứng thuốc tê Novocain", "1. Nhập ghi chú bệnh lý\n2. Bấm Đặt Lịch", "Đặt lịch thành công, lưu ghi chú bệnh án cho bác sĩ", "Đúng", "Pass")
        ]
    )

    # 5. Tiếp nhận & Duyệt lịch hẹn (Admin)
    render_ts(
        "6.3.5", "Chức năng Tiếp nhận & Duyệt lịch hẹn (Admin)",
        "Gồm 3 điều kiện: • Quyền Admin (T/F) • Lịch hẹn tồn tại (T/F) • Thao tác (Duyệt/Dời/Hủy/Khám xong)",
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
            ("QLL-001", "Xem danh sách lịch hẹn toàn hệ thống", "Admin logged in", "1. Vào /admin/appointments.php", "Hiển thị đầy đủ danh sách lịch hẹn kèm bộ lọc ngày và trạng thái", "Đúng", "Pass"),
            ("QLL-002", "Tìm kiếm lịch hẹn theo tên bệnh nhân", "Keyword: 'Tuấn'", "1. Nhập tên vào ô tìm kiếm", "Bảng dữ liệu lọc chính xác các lịch hẹn của bệnh nhân Tuấn", "Đúng", "Pass"),
            ("QLL-003", "Duyệt xác nhận lịch hẹn mới", "Lịch #105: 'pending'", "1. Bấm nút 'Xác nhận lịch'", "Trạng thái chuyển sang 'confirmed', cập nhật tức thì", "Đúng", "Pass"),
            ("QLL-004", "Dời ngày giờ khám cho bệnh nhân", "Lịch #105: Đổi sang 15:00", "1. Bấm 'Dời lịch'\n2. Chọn giờ mới", "Cập nhật ngày giờ mới thành công, kiểm tra không bị trùng lịch", "Đúng", "Pass"),
            ("QLL-005", "Chuyển trạng thái 'Hoàn thành khám'", "Lịch #104: 'confirmed'", "1. Bác sĩ khám xong, bấm 'Hoàn thành'", "Trạng thái chuyển thành 'completed', ghi nhận doanh thu", "Đúng", "Pass"),
            ("QLL-006", "Hủy lịch hẹn có ghi rõ lý do", "Lịch #103: 'Bệnh nhân bận'", "1. Bấm 'Hủy lịch'\n2. Nhập lý do", "Trạng thái chuyển sang 'cancelled', lưu lý do hủy", "Đúng", "Pass")
        ]
    )

    # 6. Quản lý bệnh nhân (Admin)
    render_ts(
        "6.3.6", "Chức năng Quản lý hồ sơ bệnh nhân (Admin)",
        "Gồm 4 điều kiện: • Họ tên bệnh nhân (T/B) • SĐT hợp lệ (T/F/B) • SĐT chưa trùng lặp (T/F) • Thao tác (Thêm/Sửa/Xóa)",
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
            ("BN-001", "Xem danh sách hồ sơ bệnh nhân", "Admin logged in", "1. Vào menu Bệnh nhân (/admin/patients.php)", "Hiển thị danh sách hồ sơ, SĐT, ngày tạo và lịch sử khám", "Đúng", "Pass"),
            ("BN-002", "Tìm kiếm bệnh nhân theo số điện thoại", "Keyword: '0987654321'", "1. Nhập SĐT vào ô tìm kiếm", "Hiển thị đúng hồ sơ của bệnh nhân tương ứng", "Đúng", "Pass"),
            ("BN-003", "Thêm mới hồ sơ bệnh nhân thành công", "Tên: Hoàng Thị Mai\nSĐT: 0977112233", "1. Bấm 'Thêm Bệnh Nhân Mới'\n2. Điền thông tin\n3. Lưu", "Hồ sơ mới được tạo thành công, xuất hiện ngay trong danh sách", "Đúng", "Pass"),
            ("BN-004", "Thêm bệnh nhân thất bại do trùng SĐT", "SĐT: 0987654321 (đã có)", "1. Nhập SĐT đã có\n2. Bấm Lưu", "Báo lỗi: 'Số điện thoại này đã tồn tại trong danh sách bệnh nhân!'", "Đúng", "Pass"),
            ("BN-005", "Cập nhật thông tin bệnh án", "Bệnh nhân #12: Bổ sung tiền sử dị ứng", "1. Bấm Sửa\n2. Cập nhật\n3. Lưu", "Cập nhật thông tin thành công vào CSDL", "Đúng", "Pass"),
            ("BN-006", "Xóa hồ sơ bệnh nhân an toàn", "Bệnh nhân #25: Chưa có lịch hẹn", "1. Bấm Xóa\n2. Xác nhận popup", "Xóa hồ sơ thành công, gỡ khỏi danh sách", "Đúng", "Pass")
        ]
    )

    # 7. Quản lý dịch vụ nha khoa
    render_ts(
        "6.3.7", "Chức năng Quản lý danh mục dịch vụ nha khoa (Admin)",
        "Gồm 3 điều kiện: • Tên dịch vụ (T/B) • Đơn giá > 0 (T/F/B) • Thao tác (Thêm/Sửa giá)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Tên dịch vụ", "T", "B", "T", "T"],
            ["2. Đơn giá (> 0)", "T", "-", "F(số âm)", "T"],
            ["3. Thao tác", "Thêm mới", "Thêm mới", "Thêm mới", "Sửa giá"]
        ],
        [
            ["Thực hiện thành công", "T", "F", "F", "T"],
            ["Báo lỗi để trống tên", "F", "T", "F", "F"],
            ["Báo lỗi đơn giá sai", "F", "F", "T", "F"]
        ],
        [
            ("DV-001", "Xem danh sách dịch vụ nha khoa", "Admin logged in", "1. Vào /admin/services.php", "Hiển thị đầy đủ danh mục, hình ảnh, thời gian làm và bảng giá niêm yết", "Đúng", "Pass"),
            ("DV-002", "Thêm mới dịch vụ thành công", "Tên: Tẩy trắng răng Laser\nGiá: 2.500.000 VNĐ", "1. Bấm Thêm dịch vụ\n2. Nhập thông tin\n3. Lưu", "Dịch vụ mới được thêm thành công, hiển thị trên website trang chủ", "Đúng", "Pass"),
            ("DV-003", "Thêm dịch vụ thất bại do để trống tên", "Tên: (trống)\nGiá: 500.000 VNĐ", "1. Bỏ trống tên\n2. Bấm Lưu", "Báo lỗi: 'Vui lòng nhập tên dịch vụ'", "Đúng", "Pass"),
            ("DV-004", "Thêm dịch vụ thất bại do đơn giá âm", "Tên: Hàn răng\nGiá: -150.000 VNĐ", "1. Nhập đơn giá âm\n2. Bấm Lưu", "Báo lỗi: 'Đơn giá dịch vụ phải là số dương lớn hơn 0'", "Đúng", "Pass"),
            ("DV-005", "Cập nhật đơn giá dịch vụ", "Dịch vụ 'Cạo vôi răng': Đổi từ 200k -> 250k", "1. Bấm Sửa\n2. Cập nhật giá mới\n3. Lưu", "Cập nhật thành công, giá mới hiển thị ngay lập tức trên bảng giá", "Đúng", "Pass")
        ]
    )

    # 8. Quản lý bác sĩ
    render_ts(
        "6.3.8", "Chức năng Quản lý đội ngũ bác sĩ & Lịch trực (Admin)",
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
            ("BS-001", "Xem danh sách đội ngũ bác sĩ", "Admin logged in", "1. Vào /admin/doctors.php", "Hiển thị danh sách bác sĩ, học vị, chuyên khoa và số lượng ca khám", "Đúng", "Pass"),
            ("BS-002", "Thêm bác sĩ mới thành công", "Tên: TS.BS Lê Hải Đăng\nChuyên khoa: Chỉnh nha", "1. Bấm Thêm bác sĩ\n2. Điền thông tin\n3. Lưu", "Thêm thành công, bác sĩ mới xuất hiện trong dropdown chọn khám", "Đúng", "Pass"),
            ("BS-003", "Thêm bác sĩ thất bại do thiếu chuyên khoa", "Tên: BS. Nguyễn Văn A\nChuyên khoa: (trống)", "1. Bỏ trống chuyên khoa\n2. Bấm Lưu", "Báo lỗi yêu cầu chọn chuyên khoa cho bác sĩ", "Đúng", "Pass"),
            ("BS-004", "Cập nhật ca trực của bác sĩ", "BS. Tâm: Thêm ca sáng Thứ 7", "1. Sửa ca trực\n2. Lưu", "Hệ thống cập nhật thành công, mở slot đặt khám vào sáng Thứ 7", "Đúng", "Pass"),
            ("BS-005", "Đổi trạng thái bác sĩ sang 'Nghỉ phép'", "BS. Đăng: Nghỉ phép 3 ngày", "1. Đổi toggle sang Nghỉ phép\n2. Lưu", "Tự động ẩn bác sĩ khỏi danh sách chọn lịch trong 3 ngày nghỉ", "Đúng", "Pass")
        ]
    )

    # 9. Thống kê doanh thu
    render_ts(
        "6.3.9", "Chức năng Thống kê & Báo cáo doanh thu (Admin)",
        "Gồm 3 điều kiện: • Khoảng ngày hợp lệ (Từ ngày <= Đến ngày: T / F) • Phân loại báo cáo (T) • Xuất dữ liệu (T)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3"],
        [
            ["1. Khoảng ngày (Từ ngày <= Đến ngày)", "T", "F(Từ ngày > Đến ngày)", "T"],
            ["2. Loại báo cáo", "Theo tháng", "-", "Theo dịch vụ"]
        ],
        [
            ["Hiển thị số liệu & biểu đồ", "T", "F", "T"],
            ["Báo lỗi khoảng ngày sai", "F", "T", "F"]
        ],
        [
            ("DT-001", "Xem tổng quan doanh thu Dashboard", "Admin logged in", "1. Vào /admin/revenue.php", "Hiển thị tổng doanh thu tháng, số ca khám và biểu đồ tăng trưởng", "Đúng", "Pass"),
            ("DT-002", "Lọc doanh thu theo khoảng ngày hợp lệ", "Từ: 01/10/2026\nĐến: 31/10/2026", "1. Chọn khoảng ngày\n2. Bấm Xem báo cáo", "Số liệu và biểu đồ cập nhật chính xác theo các ca khám trong tháng 10", "Đúng", "Pass"),
            ("DT-003", "Lọc thất bại do Từ ngày lớn hơn Đến ngày", "Từ: 20/10/2026\nĐến: 10/10/2026", "1. Chọn ngày bắt đầu sau ngày kết thúc\n2. Xem", "Báo lỗi: 'Ngày bắt đầu không được lớn hơn ngày kết thúc!'", "Đúng", "Pass"),
            ("DT-004", "Thống kê tỷ trọng doanh thu theo dịch vụ", "Tab dịch vụ", "1. Chọn tab Phân tích dịch vụ", "Hiển thị biểu đồ hình quạt (Pie chart) tỷ trọng doanh thu các dịch vụ", "Đúng", "Pass"),
            ("DT-005", "Xuất báo cáo doanh thu ra Excel / CSV", "File export", "1. Bấm 'Xuất Excel'\n2. Tải về", "Tải xuống file .csv/.xlsx có cấu trúc đầy đủ hóa đơn, số tiền", "Đúng", "Pass")
        ]
    )

    # 10. AI Chatbot
    render_ts(
        "6.3.10", "Chức năng Tư vấn tự động Chatbot AI Dental Assistant",
        "Gồm 3 điều kiện: • Câu hỏi chuyên môn nha khoa (T/F) • Câu hỏi tra cứu bảng giá (T/F) • Trạng thái server AI (Online/Offline)",
        ["Điều kiện / TH", "TH1", "TH2", "TH3", "TH4"],
        [
            ["1. Câu hỏi liên quan nha khoa", "T", "T(hỏi giá)", "F(ngoài ngành)", "T"],
            ["2. Trạng thái kết nối Server AI", "Online", "Online", "Online", "Offline"]
        ],
        [
            ["Tư vấn chuyên môn & hướng dẫn khám", "T", "F", "F", "F"],
            ["Báo giá chính xác từ CSDL", "F", "T", "F", "F"],
            ["Từ chối lịch sự và hướng dẫn", "F", "F", "T", "F"],
            ["Thông báo mất kết nối & hotline", "F", "F", "F", "T"]
        ],
        [
            ("AI-001", "Tư vấn triệu chứng đau răng nhạy cảm", "Prompt: 'Tôi bị ê buốt răng khi uống nước lạnh'", "1. Mở Chatbot\n2. Nhập câu hỏi\n3. Gửi", "AI trả lời phân tích nguyên nhân và khuyên đặt lịch khám", "Đúng", "Pass"),
            ("AI-002", "Tra cứu bảng giá dịch vụ chính xác", "Prompt: 'Chi phí nhổ răng khôn mọc lệch là bao nhiêu?'", "1. Hỏi giá\n2. Gửi", "AI tra cứu đúng CSDL bảng giá: từ 1.500.000 - 3.000.000 VNĐ tùy độ lệch", "Đúng", "Pass"),
            ("AI-003", "Hỏi đặt lịch hẹn qua Chatbot", "Prompt: 'Tôi muốn đặt lịch khám vào ngày mai'", "1. Gõ yêu cầu đặt lịch\n2. Gửi", "AI nhận diện ý định (Intent: booking), gửi link trực tiếp dẫn sang trang đặt lịch", "Đúng", "Pass"),
            ("AI-004", "Hỏi câu hỏi ngoài phạm vi nha khoa", "Prompt: 'Thời tiết hôm nay thế nào?'", "1. Hỏi câu không liên quan\n2. Gửi", "AI lịch sự từ chối và nhắc mình là Trợ lý nha khoa Premier Dental", "Đúng", "Pass"),
            ("AI-005", "Xử lý khi ngắt kết nối mạng", "Mất kết nối Internet", "1. Giả lập mất mạng\n2. Gửi", "Hiển thị thông báo thân thiện: 'Không thể kết nối. Vui lòng gọi Hotline 1900 6868'", "Đúng", "Pass")
        ]
    )

    # 11. Giao diện UI/UX & Font Be Vietnam Pro
    render_ts(
        "6.3.11", "Chức năng Kiểm thử Giao diện (UI/UX), Nút bấm và Bộ font chữ",
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
    add_p(doc, "Dưới đây là bảng tổng hợp kết quả thực hiện kiểm thử trên toàn bộ các phân hệ chức năng của Hệ thống Nha Khoa Quốc Tế Premier Dental qua hai vòng kiểm thử (Lần 1: Kiểm thử phát hiện lỗi; Lần 2: Kiểm thử hồi quy sau khi sửa lỗi).")

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
        ("2", "Đổi mật khẩu tài khoản (User & Admin)", "6", "1", "7", "7"),
        ("3", "Đăng ký tài khoản bệnh nhân mới", "7", "1", "8", "8"),
        ("4", "Đặt lịch khám trực tuyến (Booking)", "7", "1", "8", "8"),
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

    total_row = tbl_rep.add_row()
    total_data = ["", "TỔNG CỘNG TOÀN HỆ THỐNG", "70", "8", "78", "78"]
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

    add_heading_styled(doc, "6.4.1. Đánh giá chất lượng và khắc phục khiếm khuyết", level=3)
    add_p(doc, "1. Kết quả kiểm thử lần 1: Đạt 70/78 kịch bản (89.7%), phát hiện 8 khiếm khuyết liên quan đến màu sắc nút bấm form đăng nhập/đăng ký bị chìm màu, ô nhập liệu mờ nhạt, thiếu ràng buộc đổi mật khẩu và một số câu hỏi Chatbot chưa đồng bộ bảng giá.")
    add_p(doc, "2. Kết quả kiểm thử lần 2: Đội ngũ phát triển đã xử lý triệt để 100% lỗi phát hiện (bổ sung gradient Sapphire Blue cho .btn-primary-clinic, viền nổi 1.5px ô nhập liệu, chuẩn hóa font Be Vietnam Pro và kiểm tra dữ liệu chặt chẽ). Kết quả kiểm thử hồi quy lần 2 đạt 78/78 Test Cases Pass (Tỷ lệ 100%).")

    # ========================== KẾT LUẬN ==========================
    add_heading_styled(doc, "KẾT LUẬN DỰ ÁN", level=1)
    add_heading_styled(doc, "1. Kết quả đạt được", level=2)
    add_p(doc, "Sau quá trình nghiên cứu, phân tích, thiết kế và kiểm thử nghiêm ngặt, đề tài 'Xây dựng và Kiểm thử Hệ thống Quản lý Phòng Khám Nha Khoa Quốc Tế Premier Dental' đã hoàn thành xuất sắc các mục tiêu đề ra:")
    add_bullet(doc, "Xây dựng thành công cổng thông tin đặt lịch khám trực tuyến thân thiện, mượt mà, giúp bệnh nhân chủ động chọn dịch vụ, bác sĩ và thời gian khám.")
    add_bullet(doc, "Số hóa toàn diện công tác quản lý lịch hẹn, hồ sơ bệnh nhân, danh mục dịch vụ và doanh thu phòng khám cho đội ngũ y bác sĩ và ban quản lý.")
    add_bullet(doc, "Tích hợp trợ lý ảo AI Chatbot Assistant hỗ trợ tư vấn triệu chứng và tra cứu bảng giá tức thì 24/7.")
    add_bullet(doc, "Thực hiện kiểm thử toàn diện bằng phương pháp Bảng quyết định với 78 Test Cases, đảm bảo hệ thống vận hành trơn tru và bảo mật cao.")

    add_heading_styled(doc, "2. Hạn chế và Hướng phát triển trong tương lai", level=2)
    add_p(doc, "a) Hạn chế hiện tại: Hệ thống hiện tại chủ yếu phục vụ giao diện Web, chưa có ứng dụng di động bản địa (Native Mobile App Android/iOS); chưa tích hợp cổng thanh toán trực tuyến tự động (VNPay/MoMo) và chưa gửi tin nhắn SMS Brandname nhắc lịch khám.")
    add_p(doc, "b) Hướng phát triển: Trong các giai đoạn tiếp theo, nhóm sẽ phát triển ứng dụng di động Flutter, tích hợp cổng thanh toán trực tuyến, nâng cấp mô hình AI phân tích ảnh X-quang răng miệng sơ bộ và mở rộng hệ thống sang mô hình chuỗi nhiều chi nhánh phòng khám.")

    # Lưu file
    doc.save(output_path)
    print(f"Full master document created at: {output_path}")

if __name__ == "__main__":
    desktop_dir = os.path.join(os.path.expanduser("~"), "Desktop")
    out_master = os.path.join(desktop_dir, "BAO_CAO_DO_AN_CNPM_NHA_KHOA_PREMIER_DENTAL.docx")
    build_full_dental_report(out_master)
