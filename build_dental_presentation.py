# -*- coding: utf-8 -*-
"""
Script tạo file PowerPoint (.pptx) báo cáo hoàn chỉnh về Đồ án Phòng Khám Nha Khoa Tích Hợp AI
Áp dụng 100% THEO MẪU BACKGROUND NGƯỜI DÙNG CUNG CẤP:
- Màu nền cơ sở: #E9EEF2
- Khối chữ nhật chính: #0F4C81 (Classic Navy Blue)
- Khối góc trái dưới: #0C3C69 (Dark Navy)
- Hình tròn lớn góc phải: #195A96 (Medium Blue)
- Khối bo góc nổi ở giữa: #4F81BD (Steel Blue Card)
- Toàn bộ font chữ sử dụng: Times New Roman
- Đề tài: Hệ thống quản lý nha khoa có tích hợp AI
- Môn học: BÁO CÁO MÔN: ỨNG DỤNG TRÍ TUỆ NHÂN TẠO
- Sinh viên thực hiện: Nhóm 1 (Tạ Thị Thúy Quỳnh, Ngô Xuân Minh Hiếu, Lưu Tuấn Anh)
"""

import os
import pptx
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE
from pptx.dml.color import RGBColor

ASSETS_DIR = r"c:\Duannhakhoa\presentation_assets"
OUTPUT_PPTX = r"c:\Duannhakhoa\BAO_CAO_DU_AN_NHA_KHOA_ICTU.pptx"
DESKTOP_PPTX = r"C:\Users\Admin\Desktop\BAO_CAO_DU_AN_NHA_KHOA_ICTU.pptx"

def create_presentation():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6] # blank layout
    
    font_main = "Times New Roman"
    
    # Paths to assets (Theo chuẩn Phông pp-ictu)
    bg_title_path = os.path.join(ASSETS_DIR, "bg_title_ictu.png")
    bg_content_path = os.path.join(ASSETS_DIR, "bg_content_ictu.png")
    bg_end_path = os.path.join(ASSETS_DIR, "bg_end_ictu.png")
    
    # Helper to add standard header & footer for content slides
    def setup_content_slide(title_text, slide_num):
        slide = prs.slides.add_slide(blank_layout)
        slide.shapes.add_picture(bg_content_path, Inches(0), Inches(0), prs.slide_width, prs.slide_height)
            
        # Add Header Title (Màu xanh thương hiệu ICTU #063D79 trên nền trắng)
        tb_title = slide.shapes.add_textbox(Inches(0.8), Inches(0.12), Inches(11.8), Inches(0.62))
        tf = tb_title.text_frame
        tf.word_wrap = True
        tf.vertical_anchor = MSO_ANCHOR.MIDDLE
        p = tf.paragraphs[0]
        p.text = title_text
        p.font.name = font_main
        p.font.size = Pt(24)
        p.font.bold = True
        p.font.color.rgb = RGBColor(6, 61, 121)
        
        # Add Footer: Subject on left (tránh logo ICTU), Topic in center, Slide number on right
        tb_left = slide.shapes.add_textbox(Inches(2.0), Inches(6.85), Inches(4.2), Inches(0.4))
        tf_l = tb_left.text_frame
        tf_l.vertical_anchor = MSO_ANCHOR.MIDDLE
        p_l = tf_l.paragraphs[0]
        p_l.text = "Môn: Ứng dụng Trí tuệ nhân tạo - Nhóm 1"
        p_l.font.name = font_main
        p_l.font.size = Pt(11)
        p_l.font.color.rgb = RGBColor(255, 255, 255)
        
        tb_center = slide.shapes.add_textbox(Inches(6.3), Inches(6.85), Inches(4.5), Inches(0.4))
        tf_c = tb_center.text_frame
        tf_c.vertical_anchor = MSO_ANCHOR.MIDDLE
        p_c = tf_c.paragraphs[0]
        p_c.alignment = PP_ALIGN.CENTER
        p_c.text = "Hệ thống quản lý nha khoa có tích hợp AI"
        p_c.font.name = font_main
        p_c.font.size = Pt(11)
        p_c.font.italic = True
        p_c.font.color.rgb = RGBColor(224, 242, 254)
        
        tb_page = slide.shapes.add_textbox(Inches(11.0), Inches(6.85), Inches(1.8), Inches(0.4))
        tf_p = tb_page.text_frame
        tf_p.vertical_anchor = MSO_ANCHOR.MIDDLE
        p_p = tf_p.paragraphs[0]
        p_p.alignment = PP_ALIGN.RIGHT
        p_p.text = f"‹#{slide_num}›"
        p_p.font.name = font_main
        p_p.font.size = Pt(11.5)
        p_p.font.bold = True
        p_p.font.color.rgb = RGBColor(255, 255, 255)
        
        return slide

    # ==============================================================================
    # SLIDE 1: TRANG BÌA (THEO CHUẨN PHÔNG PP-ICTU)
    # ==============================================================================
    slide1 = prs.slides.add_slide(blank_layout)
    slide1.shapes.add_picture(bg_title_path, Inches(0), Inches(0), prs.slide_width, prs.slide_height)
    
    # Title Section in Center (Nổi bật trên nền trống đồng Đông Sơn, cách logo trường ở góc trên)
    tb_main = slide1.shapes.add_textbox(Inches(1.0), Inches(1.8), Inches(11.333), Inches(2.0))
    tf_m = tb_main.text_frame
    tf_m.word_wrap = True
    
    p_bc = tf_m.paragraphs[0]
    p_bc.alignment = PP_ALIGN.CENTER
    p_bc.text = "BÁO CÁO MÔN: ỨNG DỤNG TRÍ TUỆ NHÂN TẠO"
    p_bc.font.name = font_main
    p_bc.font.size = Pt(32)
    p_bc.font.bold = True
    p_bc.font.color.rgb = RGBColor(6, 61, 121)
    
    p_dt = tf_m.add_paragraph()
    p_dt.alignment = PP_ALIGN.CENTER
    p_dt.space_before = Pt(12)
    p_dt.text = "Đề tài: Hệ thống quản lý nha khoa có tích hợp AI"
    p_dt.font.name = font_main
    p_dt.font.size = Pt(25)
    p_dt.font.bold = True
    p_dt.font.color.rgb = RGBColor(185, 39, 0)
    
    # Khối thẻ thông tin sinh viên thực hiện bo góc viền xanh ICTU trang nhã
    card_shape = slide1.shapes.add_shape(
        MSO_SHAPE.ROUNDED_RECTANGLE,
        Inches(3.2), Inches(4.3), Inches(6.933), Inches(2.3)
    )
    card_shape.fill.solid()
    card_shape.fill.fore_color.rgb = RGBColor(255, 255, 255)
    card_shape.line.color.rgb = RGBColor(6, 61, 121)
    card_shape.line.width = Pt(1.5)
    
    tf_sv = card_shape.text_frame
    tf_sv.word_wrap = True
    tf_sv.vertical_anchor = MSO_ANCHOR.MIDDLE
    
    sv_list = [
        "Sinh viên thực hiện: Nhóm 1",
        "Tạ Thị Thúy Quỳnh (Trưởng nhóm)",
        "Ngô Xuân Minh Hiếu",
        "Lưu Tuấn Anh"
    ]
    for i, line in enumerate(sv_list):
        p_s = tf_sv.paragraphs[0] if i == 0 else tf_sv.add_paragraph()
        p_s.alignment = PP_ALIGN.LEFT
        p_s.line_spacing = 1.25
        p_s.space_after = Pt(3)
        r = p_s.add_run()
        r.text = ("   • " if i > 0 else "   ") + line
        r.font.name = font_main
        r.font.size = Pt(17 if i == 0 else 16)
        r.font.bold = (i == 0 or "(Trưởng nhóm)" in line)
        if i == 0:
            r.font.color.rgb = RGBColor(6, 61, 121)
        else:
            r.font.color.rgb = RGBColor(30, 41, 59)

    # ==============================================================================
    # SLIDE 2: NỘI DUNG CHÍNH
    # ==============================================================================
    slide2 = setup_content_slide("Nội dung chính", 2)
    
    tb2 = slide2.shapes.add_textbox(Inches(1.2), Inches(1.8), Inches(11.0), Inches(4.8))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    
    agenda = [
        "1. Tổng quan về công nghệ phần mềm & Trí tuệ nhân tạo (AI)",
        "2. Lập kế hoạch dự án",
        "3. Khảo sát và phân tích yêu cầu",
        "4. Phân tích và thiết kế hệ thống",
        "5. Thiết kế dữ liệu và cài đặt demo",
        "6. Kiểm thử phần mềm & Đánh giá mô hình AI"
    ]
    for i, it in enumerate(agenda):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.line_spacing = 1.3
        p.space_after = Pt(14)
        r = p.add_run()
        r.text = it
        r.font.name = font_main
        r.font.size = Pt(21)
        r.font.bold = True
        r.font.color.rgb = RGBColor(15, 23, 42)

    # ==============================================================================
    # SLIDE 3: 1. TỔNG QUAN VỀ CNPM & TRÍ TUỆ NHÂN TẠO
    # ==============================================================================
    slide3 = setup_content_slide("1. TỔNG QUAN VỀ CÔNG NGHỆ PHẦN MỀM & AI", 3)
    
    tb3 = slide3.shapes.add_textbox(Inches(1.2), Inches(1.75), Inches(11.0), Inches(5.0))
    tf3 = tb3.text_frame
    tf3.word_wrap = True
    
    sections = [
        ("1.1. Phần mềm (Software): Nền tảng web quản lý phòng khám", 20, True, 10, 0),
        ("1.2. Phần cứng (Hardware): Máy chủ CSDL và thiết bị đầu cuối y tế", 20, True, 10, 0),
        ("1.3. Các mô hình phát triển phần mềm", 20, True, 6, 0),
        ("   a. Mô hình thác nước (Waterfall Model - Áp dụng trong đồ án)", 18, False, 5, 20),
        ("   b. Mô hình mẫu thử (Prototyping Model)", 18, False, 5, 20),
        ("   c. Mô hình phát triển dựa trên thành phần (CBSE)", 18, False, 5, 20),
        ("   d. Mô hình xoắn ốc (Spiral Model)", 18, False, 10, 20),
        ("1.4. Ứng dụng Trí tuệ nhân tạo (AI) trong nha khoa hiện đại", 20, True, 6, 0),
        ("   • Trợ lý AI Chatbot RAG (Retrieval-Augmented Generation) tư vấn 24/7", 18, False, 5, 20),
        ("   • Tự động phân tích triệu chứng và gợi ý chuyên khoa phù hợp", 18, False, 5, 20)
    ]
    for i, (txt, sz, bold, sp_after, left_indent) in enumerate(sections):
        p = tf3.paragraphs[0] if i == 0 else tf3.add_paragraph()
        p.line_spacing = 1.2
        p.space_after = Pt(sp_after)
        if left_indent > 0:
            p.level = 1
        r = p.add_run()
        r.text = txt
        r.font.name = font_main
        r.font.size = Pt(sz)
        r.font.bold = bold
        r.font.color.rgb = RGBColor(15, 23, 42)

    # ==============================================================================
    # SLIDE 4: 2. LẬP KẾ HOẠCH - 2.1. LÝ DO CHỌN ĐỀ TÀI
    # ==============================================================================
    slide4 = setup_content_slide("2. LẬP KẾ HOẠCH", 4)
    
    tb4_sub = slide4.shapes.add_textbox(Inches(1.0), Inches(1.5), Inches(11.3), Inches(0.6))
    p4_s = tb4_sub.text_frame.paragraphs[0]
    p4_s.text = "2.1. Lý do chọn đề tài"
    p4_s.font.name = font_main
    p4_s.font.size = Pt(22)
    p4_s.font.bold = True
    p4_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb4 = slide4.shapes.add_textbox(Inches(1.2), Inches(2.2), Inches(11.0), Inches(4.5))
    tf4 = tb4.text_frame
    tf4.word_wrap = True
    
    reasons = [
        "Nhu cầu chăm sóc răng miệng & thẩm mỹ nụ cười ngày càng tăng cao, nhiều phòng khám nha khoa hiện đại được mở ra.",
        "Việc quản lý hiện nay chủ yếu thủ công (sổ sách, Excel, tin nhắn) nên dễ sai sót, trùng lịch hẹn bác sĩ và mất nhiều thời gian.",
        "Khó theo dõi lịch trực của đội ngũ bác sĩ, khó quản lý hồ sơ bệnh án, danh mục dịch vụ và thống kê doanh thu phòng khám.",
        "Cần xây dựng hệ thống đặt lịch và quản lý phòng khám nha khoa có tích hợp Trí tuệ nhân tạo (AI Assistant) hỗ trợ 24/7.",
        "Hệ thống giúp tự động hóa quản lý, nâng cao hiệu quả hoạt động, loại bỏ trùng lịch và nâng cao trải nghiệm người bệnh."
    ]
    for i, r_text in enumerate(reasons):
        p = tf4.paragraphs[0] if i == 0 else tf4.add_paragraph()
        p.line_spacing = 1.25
        p.space_after = Pt(12)
        r_bullet = p.add_run()
        r_bullet.text = "•  "
        r_bullet.font.name = font_main
        r_bullet.font.size = Pt(18)
        r_bullet.font.bold = True
        r_bullet.font.color.rgb = RGBColor(15, 76, 129)
        
        r_body = p.add_run()
        r_body.text = r_text
        r_body.font.name = font_main
        r_body.font.size = Pt(18)
        r_body.font.color.rgb = RGBColor(30, 41, 59)

    # ==============================================================================
    # SLIDE 5: 2. LẬP KẾ HOẠCH - 2.2. SƠ LƯỢC VỀ DỰ ÁN
    # ==============================================================================
    slide5 = setup_content_slide("2. LẬP KẾ HOẠCH", 5)
    
    tb5_sub = slide5.shapes.add_textbox(Inches(1.0), Inches(1.5), Inches(11.3), Inches(0.6))
    p5_s = tb5_sub.text_frame.paragraphs[0]
    p5_s.text = "2.2. Sơ lược về dự án"
    p5_s.font.name = font_main
    p5_s.font.size = Pt(22)
    p5_s.font.bold = True
    p5_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb5 = slide5.shapes.add_textbox(Inches(1.2), Inches(2.2), Inches(11.0), Inches(4.5))
    tf5 = tb5.text_frame
    tf5.word_wrap = True
    
    overview = [
        ("- Dự án: ", "Hệ thống quản lý nha khoa có tích hợp AI"),
        ("- Mô hình sử dụng: ", "Mô hình thác nước (Waterfall Model) kết hợp thử nghiệm AI Prompting"),
        ("- Thời gian: ", "9 tuần"),
        ("- Công việc: ", ""),
        ("   • Giai đoạn 1: ", "Thu thập yêu cầu và phân tích đặc tả yêu cầu hệ thống"),
        ("   • Giai đoạn 2: ", "Phân tích, thiết kế hệ thống"),
        ("   • Giai đoạn 3: ", "Cài đặt & tích hợp AI Chatbot RAG Pipeline"),
        ("   • Giai đoạn 4: ", "Kiểm thử và triển khai"),
        ("- Kinh phí: ", "75 000 000 (VNĐ)")
    ]
    for i, (pre, val) in enumerate(overview):
        p = tf5.paragraphs[0] if i == 0 else tf5.add_paragraph()
        p.line_spacing = 1.2
        p.space_after = Pt(8 if pre.startswith("   •") else 12)
        r1 = p.add_run()
        r1.text = pre
        r1.font.name = font_main
        r1.font.size = Pt(18)
        r1.font.bold = True
        r1.font.color.rgb = RGBColor(15, 76, 129)
        
        if val:
            r2 = p.add_run()
            r2.text = val
            r2.font.name = font_main
            r2.font.size = Pt(18)
            r2.font.color.rgb = RGBColor(30, 41, 59)

    # ==============================================================================
    # SLIDE 6: 3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU - 3.1. KHẢO SÁT
    # ==============================================================================
    slide6 = setup_content_slide("3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU", 6)
    
    tb6_sub = slide6.shapes.add_textbox(Inches(1.0), Inches(1.4), Inches(11.3), Inches(0.5))
    p6_s = tb6_sub.text_frame.paragraphs[0]
    p6_s.text = "3.1. Khảo sát"
    p6_s.font.name = font_main
    p6_s.font.size = Pt(22)
    p6_s.font.bold = True
    p6_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb6_desc = slide6.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(11.3), Inches(0.7))
    p6_d = tb6_desc.text_frame.paragraphs[0]
    p6_d.text = "- Qua khảo sát thực tế tại phòng khám nha khoa, nhóm nhận thấy việc quản lý theo phương pháp thủ công còn tồn tại nhiều hạn chế như sau:"
    p6_d.font.name = font_main
    p6_d.font.size = Pt(16.5)
    p6_d.font.color.rgb = RGBColor(30, 41, 59)
    
    chart_path = os.path.join(ASSETS_DIR, "chart_survey.png")
    if os.path.exists(chart_path):
        slide6.shapes.add_picture(chart_path, Inches(2.6), Inches(2.7), Inches(8.0), Inches(4.1))

    # ==============================================================================
    # SLIDE 7: 3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU - 3.2. NHU CẦU NGƯỜI DÙNG
    # ==============================================================================
    slide7 = setup_content_slide("3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU", 7)
    
    tb7_sub = slide7.shapes.add_textbox(Inches(1.0), Inches(1.5), Inches(11.3), Inches(0.6))
    p7_s = tb7_sub.text_frame.paragraphs[0]
    p7_s.text = "3.2. Nhu cầu người dùng"
    p7_s.font.name = font_main
    p7_s.font.size = Pt(22)
    p7_s.font.bold = True
    p7_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb7 = slide7.shapes.add_textbox(Inches(1.2), Inches(2.3), Inches(11.0), Inches(4.2))
    tf7 = tb7.text_frame
    tf7.word_wrap = True
    
    user_needs = [
        ("Chủ phòng khám: ", "Cần quản lý minh bạch doanh thu, theo dõi số ca khám, hiệu suất bác sĩ và quản trị dịch vụ toàn diện."),
        ("Nhân viên / Bác sĩ: ", "Cần công cụ hỗ trợ tiếp nhận ca khám, duyệt lịch, xem slot theo ngày, thao tác nhanh và giảm sai sót."),
        ("Khách hàng / Bệnh nhân: ", "Cần tiện lợi, chủ động tra cứu bảng giá, chọn bác sĩ, đặt lịch khám 24/7 và được trợ lý AI tư vấn tức thời.")
    ]
    for i, (role, need) in enumerate(user_needs):
        p = tf7.paragraphs[0] if i == 0 else tf7.add_paragraph()
        p.line_spacing = 1.3
        p.space_after = Pt(20)
        
        r_bullet = p.add_run()
        r_bullet.text = "•  "
        r_bullet.font.name = font_main
        r_bullet.font.size = Pt(19)
        r_bullet.font.bold = True
        r_bullet.font.color.rgb = RGBColor(15, 76, 129)
        
        r1 = p.add_run()
        r1.text = role
        r1.font.name = font_main
        r1.font.size = Pt(19)
        r1.font.bold = True
        r1.font.color.rgb = RGBColor(15, 23, 42)
        
        r2 = p.add_run()
        r2.text = need
        r2.font.name = font_main
        r2.font.size = Pt(19)
        r2.font.color.rgb = RGBColor(30, 41, 59)

    # ==============================================================================
    # SLIDE 8: 3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU - 3.3. ĐỀ XUẤT HỆ THỐNG MỚI
    # ==============================================================================
    slide8 = setup_content_slide("3. KHẢO SÁT VÀ PHÂN TÍCH YÊU CẦU", 8)
    
    tb8_sub = slide8.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(6.0), Inches(0.45))
    p8_s = tb8_sub.text_frame.paragraphs[0]
    p8_s.text = "3.3. Đề xuất hệ thống mới"
    p8_s.font.name = font_main
    p8_s.font.size = Pt(20)
    p8_s.font.bold = True
    p8_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb8_req = slide8.shapes.add_textbox(Inches(0.8), Inches(1.75), Inches(5.8), Inches(5.2))
    tf8 = tb8_req.text_frame
    tf8.word_wrap = True
    
    def add_sec_hdr(tf, title):
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.space_after = Pt(2)
        r = p.add_run()
        r.text = title
        r.font.name = font_main
        r.font.size = Pt(14)
        r.font.bold = True
        r.font.color.rgb = RGBColor(15, 76, 129)

    def add_sec_item(tf, text):
        p = tf.add_paragraph()
        p.line_spacing = 1.15
        p.space_after = Pt(2.5)
        p.level = 1
        r = p.add_run()
        r.text = f"• {text}"
        r.font.name = font_main
        r.font.size = Pt(11)
        r.font.color.rgb = RGBColor(30, 41, 59)
        
    add_sec_hdr(tf8, "Yêu cầu chức năng")
    add_sec_item(tf8, "Quản lý dịch vụ nha khoa: thêm, sửa, xóa, tra cứu thông tin & bảng giá.")
    add_sec_item(tf8, "Đặt lịch khám và tra cứu lịch trống theo bác sĩ chuyên khoa.")
    add_sec_item(tf8, "Quản lý danh sách lịch hẹn, duyệt, hủy và cập nhật trạng thái ca khám.")
    add_sec_item(tf8, "Thống kê lượt khám, doanh thu và tích hợp AI Chatbot tư vấn 24/7.")
    
    add_sec_hdr(tf8, "Yêu cầu phi chức năng")
    add_sec_item(tf8, "Hệ thống hoạt động ổn định, chính xác, tính sẵn sàng cao.")
    add_sec_item(tf8, "Xử lý nhanh, phản hồi < 1.5s, AI API < 2s.")
    add_sec_item(tf8, "Bảo mật dữ liệu bệnh nhân, mã hóa mật khẩu Bcrypt.")
    add_sec_item(tf8, "Giao diện dễ sử dụng, thân thiện chuẩn Responsive.")
    
    add_sec_hdr(tf8, "Yêu cầu miền")
    add_sec_item(tf8, "Mỗi bác sĩ có chuyên khoa, ca trực và trạng thái làm việc.")
    add_sec_item(tf8, "Không được đặt trùng một bác sĩ trong cùng khung giờ khám.")
    add_sec_item(tf8, "Bệnh nhân phải nhập đầy đủ họ tên, SĐT khi đặt lịch khám.")
    
    tree_path = os.path.join(ASSETS_DIR, "tree_functions.png")
    if os.path.exists(tree_path):
        slide8.shapes.add_picture(tree_path, Inches(6.8), Inches(1.5), Inches(6.2), Inches(5.3))

    # ==============================================================================
    # SLIDE 9: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - 4.1. USE CASE TỔNG QUÁT & PHÂN RÃ
    # ==============================================================================
    slide9 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 9)
    
    tb9_sub = slide9.shapes.add_textbox(Inches(0.8), Inches(1.28), Inches(11.5), Inches(0.45))
    p9_s = tb9_sub.text_frame.paragraphs[0]
    p9_s.text = "4.1. Use case tổng quát & phân rã"
    p9_s.font.name = font_main
    p9_s.font.size = Pt(21)
    p9_s.font.bold = True
    p9_s.font.color.rgb = RGBColor(15, 76, 129)
    
    uc_path = os.path.join(ASSETS_DIR, "diagram_usecase.png")
    if os.path.exists(uc_path):
        slide9.shapes.add_picture(uc_path, Inches(1.5), Inches(1.75), Inches(10.3), Inches(5.2))

    # ==============================================================================
    # SLIDE 10: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - 4.2. BIỂU ĐỒ TRÌNH TỰ (XEM LỊCH)
    # ==============================================================================
    slide10 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 10)
    
    tb10_sub = slide10.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(5.0), Inches(0.8))
    tf10 = tb10_sub.text_frame
    p10_1 = tf10.paragraphs[0]
    p10_1.text = "4.2. Biểu đồ trình tự"
    p10_1.font.name = font_main
    p10_1.font.size = Pt(20)
    p10_1.font.bold = True
    p10_1.font.color.rgb = RGBColor(15, 76, 129)
    
    p10_2 = tf10.add_paragraph()
    p10_2.text = "• Xem lịch khám trống & Hỏi đáp AI"
    p10_2.font.name = font_main
    p10_2.font.size = Pt(17)
    p10_2.font.bold = True
    p10_2.font.color.rgb = RGBColor(79, 129, 189)
    
    seq1_path = os.path.join(ASSETS_DIR, "seq_xem_lich.png")
    if os.path.exists(seq1_path):
        slide10.shapes.add_picture(seq1_path, Inches(4.5), Inches(1.5), Inches(8.3), Inches(5.3))

    # ==============================================================================
    # SLIDE 11: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - BIỂU ĐỒ TRÌNH TỰ (TIẾP NHẬN)
    # ==============================================================================
    slide11 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 11)
    
    tb11_sub = slide11.shapes.add_textbox(Inches(0.8), Inches(1.4), Inches(4.5), Inches(0.8))
    p11_s = tb11_sub.text_frame.paragraphs[0]
    p11_s.text = "• Tiếp nhận đặt lịch"
    p11_s.font.name = font_main
    p11_s.font.size = Pt(20)
    p11_s.font.bold = True
    p11_s.font.color.rgb = RGBColor(15, 76, 129)
    
    seq2_path = os.path.join(ASSETS_DIR, "seq_tiep_nhan.png")
    if os.path.exists(seq2_path):
        slide11.shapes.add_picture(seq2_path, Inches(4.5), Inches(1.4), Inches(8.3), Inches(5.4))

    # ==============================================================================
    # SLIDE 12: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - 4.3. SƠ ĐỒ HOẠT ĐỘNG (XEM LỊCH)
    # ==============================================================================
    slide12 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 12)
    
    tb12_sub = slide12.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(5.0), Inches(0.8))
    tf12 = tb12_sub.text_frame
    p12_1 = tf12.paragraphs[0]
    p12_1.text = "4.3. Sơ đồ hoạt động"
    p12_1.font.name = font_main
    p12_1.font.size = Pt(20)
    p12_1.font.bold = True
    p12_1.font.color.rgb = RGBColor(15, 76, 129)
    
    p12_2 = tf12.add_paragraph()
    p12_2.text = "• Xem lịch khám trống"
    p12_2.font.name = font_main
    p12_2.font.size = Pt(17)
    p12_2.font.bold = True
    p12_2.font.color.rgb = RGBColor(79, 129, 189)
    
    act1_path = os.path.join(ASSETS_DIR, "act_xem_lich.png")
    if os.path.exists(act1_path):
        slide12.shapes.add_picture(act1_path, Inches(4.5), Inches(1.5), Inches(8.3), Inches(5.3))

    # ==============================================================================
    # SLIDE 13: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - SƠ ĐỒ HOẠT ĐỘNG (ĐẶT LỊCH)
    # ==============================================================================
    slide13 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 13)
    
    tb13_sub = slide13.shapes.add_textbox(Inches(0.8), Inches(1.4), Inches(4.5), Inches(0.8))
    p13_s = tb13_sub.text_frame.paragraphs[0]
    p13_s.text = "• Đặt lịch khám"
    p13_s.font.name = font_main
    p13_s.font.size = Pt(20)
    p13_s.font.bold = True
    p13_s.font.color.rgb = RGBColor(15, 76, 129)
    
    act2_path = os.path.join(ASSETS_DIR, "act_dat_lich.png")
    if os.path.exists(act2_path):
        slide13.shapes.add_picture(act2_path, Inches(4.5), Inches(1.4), Inches(8.3), Inches(5.4))

    # ==============================================================================
    # SLIDE 14: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - 4.4. SƠ ĐỒ LỚP (CLASS DIAGRAM)
    # ==============================================================================
    slide14 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 14)
    
    tb14_sub = slide14.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(4.5), Inches(0.5))
    p14_s = tb14_sub.text_frame.paragraphs[0]
    p14_s.text = "4.4. Sơ đồ lớp:"
    p14_s.font.name = font_main
    p14_s.font.size = Pt(21)
    p14_s.font.bold = True
    p14_s.font.color.rgb = RGBColor(15, 76, 129)
    
    class_path = os.path.join(ASSETS_DIR, "diagram_class.png")
    if os.path.exists(class_path):
        slide14.shapes.add_picture(class_path, Inches(4.2), Inches(1.4), Inches(8.7), Inches(5.4))

    # ==============================================================================
    # SLIDE 15: 4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG - 4.5. LỰA CHỌN MÔ HÌNH KIẾN TRÚC
    # ==============================================================================
    slide15 = setup_content_slide("4. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", 15)
    
    tb15_sub = slide15.shapes.add_textbox(Inches(0.8), Inches(1.35), Inches(5.5), Inches(0.9))
    tf15 = tb15_sub.text_frame
    p15_1 = tf15.paragraphs[0]
    p15_1.text = "4.5. Lựa chọn mô hình kiến trúc"
    p15_1.font.name = font_main
    p15_1.font.size = Pt(20)
    p15_1.font.bold = True
    p15_1.font.color.rgb = RGBColor(15, 76, 129)
    
    p15_2 = tf15.add_paragraph()
    p15_2.text = "Sử dụng kiến trúc phân tầng tích hợp AI"
    p15_2.font.name = font_main
    p15_2.font.size = Pt(17)
    p15_2.font.bold = True
    p15_2.font.color.rgb = RGBColor(79, 129, 189)
    
    arch_path = os.path.join(ASSETS_DIR, "diagram_architecture.png")
    if os.path.exists(arch_path):
        slide15.shapes.add_picture(arch_path, Inches(6.0), Inches(1.4), Inches(6.8), Inches(5.4))

    # ==============================================================================
    # SLIDE 16: 5. THIẾT KẾ CSDL & CÀI ĐẶT DEMO - 5.1. THIẾT KẾ CẤU TRÚC CƠ SỞ DỮ LIỆU
    # ==============================================================================
    slide16 = setup_content_slide("5. THIẾT KẾ CSDL & CÀI ĐẶT DEMO", 16)
    
    tb16_sub = slide16.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(5.5), Inches(0.5))
    p16_s = tb16_sub.text_frame.paragraphs[0]
    p16_s.text = "5.1. Thiết kế cấu trúc cơ sở dữ liệu"
    p16_s.font.name = font_main
    p16_s.font.size = Pt(20)
    p16_s.font.bold = True
    p16_s.font.color.rgb = RGBColor(15, 76, 129)
    
    erd_path = os.path.join(ASSETS_DIR, "diagram_erd.png")
    if os.path.exists(erd_path):
        slide16.shapes.add_picture(erd_path, Inches(4.5), Inches(1.4), Inches(8.4), Inches(5.4))

    # ==============================================================================
    # SLIDE 17: 5. THIẾT KẾ CSDL & CÀI ĐẶT DEMO - 5.2. THIẾT KẾ GIAO DIỆN
    # ==============================================================================
    slide17 = setup_content_slide("5. THIẾT KẾ CSDL & CÀI ĐẶT DEMO", 17)
    
    tb17_sub = slide17.shapes.add_textbox(Inches(0.8), Inches(1.25), Inches(5.5), Inches(0.75))
    tf17 = tb17_sub.text_frame
    p17_1 = tf17.paragraphs[0]
    p17_1.text = "5.2. Thiết kế giao diện"
    p17_1.font.name = font_main
    p17_1.font.size = Pt(19)
    p17_1.font.bold = True
    p17_1.font.color.rgb = RGBColor(15, 76, 129)
    
    p17_2 = tf17.add_paragraph()
    p17_2.text = "   ◦ Một số giao diện demo"
    p17_2.font.name = font_main
    p17_2.font.size = Pt(16)
    p17_2.font.color.rgb = RGBColor(71, 85, 105)
    
    ui_path = os.path.join(ASSETS_DIR, "mockup_booking_ui.png")
    if os.path.exists(ui_path):
        slide17.shapes.add_picture(ui_path, Inches(0.8), Inches(2.1), Inches(6.2), Inches(4.8))
        
    tb17_desc = slide17.shapes.add_textbox(Inches(7.2), Inches(1.3), Inches(5.8), Inches(5.6))
    tf17_d = tb17_desc.text_frame
    tf17_d.word_wrap = True
    
    def add_p_desc(tf, text, sz=14, bold=False, sp=4):
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.line_spacing = 1.18
        p.space_after = Pt(sp)
        r = p.add_run()
        r.text = text
        r.font.name = font_main
        r.font.size = Pt(sz)
        r.font.bold = bold
        r.font.color.rgb = RGBColor(15, 23, 42)
        
    add_p_desc(tf17_d, "Mô tả:", 18, True, 4)
    add_p_desc(tf17_d, "Giao diện: Đặt lịch khám của tôi & AI Chatbot", 16, True, 2)
    add_p_desc(tf17_d, "Dành cho khách hàng thực hiện đặt lịch khám & nhận tư vấn.", 14, False, 8)
    
    add_p_desc(tf17_d, "Hiển thị:", 16, True, 2)
    add_p_desc(tf17_d, "• Form nhập thông tin bệnh nhân", 13.5, False, 2)
    add_p_desc(tf17_d, "• Thông tin dịch vụ, bác sĩ chuyên khoa, ngày, giờ khám", 13.5, False, 2)
    add_p_desc(tf17_d, "• Cửa sổ AI Chatbot tư vấn triệu chứng và chi phí 24/7", 13.5, False, 8)
    
    add_p_desc(tf17_d, "Chức năng:", 16, True, 2)
    add_p_desc(tf17_d, "• Nhập họ tên, số điện thoại, chọn dịch vụ...", 13.5, False, 2)
    add_p_desc(tf17_d, "• Hỏi đáp tự động với AI về phác đồ và chi phí", 13.5, False, 2)
    add_p_desc(tf17_d, "• Gửi yêu cầu đặt lịch khám", 13.5, False, 8)
    
    add_p_desc(tf17_d, "Mục đích: tạo đơn đặt khám nhanh chóng và chính xác, loại bỏ thời gian chờ đợi tại phòng khám.", 14, True, 4)

    # ==============================================================================
    # SLIDE 18: 6. KIỂM THỬ - 6.1. KẾ HOẠCH KIỂM THỬ
    # ==============================================================================
    slide18 = setup_content_slide("6. KIỂM THỬ HỆ THỐNG", 18)
    
    tb18_sub = slide18.shapes.add_textbox(Inches(0.8), Inches(1.35), Inches(11.5), Inches(0.5))
    p18_s = tb18_sub.text_frame.paragraphs[0]
    p18_s.text = "6.1. Kế hoạch kiểm thử"
    p18_s.font.name = font_main
    p18_s.font.size = Pt(21)
    p18_s.font.bold = True
    p18_s.font.color.rgb = RGBColor(15, 76, 129)
    
    tb18 = slide18.shapes.add_textbox(Inches(1.0), Inches(1.95), Inches(11.3), Inches(4.9))
    tf18 = tb18.text_frame
    tf18.word_wrap = True
    
    def add_kt_line(tf, text, sz=16, bold=False, sp=4, left_in=0, color=RGBColor(30, 41, 59)):
        p = tf.add_paragraph() if tf.paragraphs[0].text else tf.paragraphs[0]
        p.line_spacing = 1.2
        p.space_after = Pt(sp)
        if left_in > 0:
            p.level = 1
        r = p.add_run()
        r.text = text
        r.font.name = font_main
        r.font.size = Pt(sz)
        r.font.bold = bold
        r.font.color.rgb = color
        
    add_kt_line(tf18, "Các tính năng cần kiểm thử:", 18, True, 6, 0, RGBColor(15, 76, 129))
    add_kt_line(tf18, "• Tất cả các chức năng của hệ thống phòng khám nha khoa:", 17, True, 4, 15)
    add_kt_line(tf18, "- Đăng nhập & Đăng ký tài khoản", 16, False, 3, 30)
    add_kt_line(tf18, "- Quản lý dịch vụ nha khoa & bảng giá", 16, False, 3, 30)
    add_kt_line(tf18, "- Quản lý đặt lịch khám & Duyệt ca khám ...", 16, False, 8, 30)
    
    add_kt_line(tf18, "• Giao diện phần mềm & Pipeline Trí tuệ nhân tạo (AI):", 17, True, 4, 15)
    add_kt_line(tf18, "- Hiển thị đúng nội dung chuyên môn nha khoa", 16, False, 3, 30)
    add_kt_line(tf18, "- Khả năng phản hồi thông minh, chính xác của Chatbot AI", 16, False, 3, 30)
    add_kt_line(tf18, "- Dễ sử dụng, thân thiện người dùng trên máy tính & điện thoại ...", 16, False, 14, 30)
    
    add_kt_line(tf18, "Phương pháp kiểm thử", 18, True, 6, 0, RGBColor(15, 76, 129))
    add_kt_line(tf18, "Phương pháp được lựa chọn: Kiểm thử bằng Bảng quyết định (Decision Table)", 17, True, 4, 15, RGBColor(15, 23, 42))

    # ==============================================================================
    # SLIDE 19: 6. KIỂM THỬ - 6.2. TEST CASE (ADMIN)
    # ==============================================================================
    slide19 = setup_content_slide("6. KIỂM THỬ HỆ THỐNG", 19)
    
    tb19_sub = slide19.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(11.5), Inches(0.9))
    tf19 = tb19_sub.text_frame
    p19_1 = tf19.paragraphs[0]
    p19_1.text = "6.2. Test case:"
    p19_1.font.name = font_main
    p19_1.font.size = Pt(21)
    p19_1.font.bold = True
    p19_1.font.color.rgb = RGBColor(15, 76, 129)
    
    p19_2 = tf19.add_paragraph()
    p19_2.text = "Chức năng: Cập nhật & Duyệt lịch hẹn khám (Admin):"
    p19_2.font.name = font_main
    p19_2.font.size = Pt(18)
    p19_2.font.bold = True
    p19_2.font.color.rgb = RGBColor(79, 129, 189)
    
    tc_ui_path = os.path.join(ASSETS_DIR, "mockup_admin_testcase.png")
    if os.path.exists(tc_ui_path):
        slide19.shapes.add_picture(tc_ui_path, Inches(2.2), Inches(2.35), Inches(8.8), Inches(4.5))

    # ==============================================================================
    # SLIDE 20: 6. KIỂM THỬ - BẢNG QUYẾT ĐỊNH (DECISION TABLE)
    # ==============================================================================
    slide20 = setup_content_slide("6. KIỂM THỬ HỆ THỐNG", 20)
    
    tb20_sub = slide20.shapes.add_textbox(Inches(0.8), Inches(1.3), Inches(6.0), Inches(0.45))
    p20_s = tb20_sub.text_frame.paragraphs[0]
    p20_s.text = "Bảng quyết định:"
    p20_s.font.name = font_main
    p20_s.font.size = Pt(21)
    p20_s.font.bold = True
    p20_s.font.color.rgb = RGBColor(15, 76, 129)
    
    table_shape = slide20.shapes.add_table(rows=8, cols=7, left=Inches(0.8), top=Inches(1.85), width=Inches(5.6), height=Inches(4.8))
    table = table_shape.table
    
    table.columns[0].width = Inches(2.0)
    for c in range(1, 7):
        table.columns[c].width = Inches(0.6)
        
    table_data = [
        ["Điều kiện", "TH1", "TH2", "TH3", "TH4", "TH5", "TH6"],
        ["Nhập họ tên BN", "T", "T", "T", "T", "F", "B"],
        ["Chọn dịch vụ", "T", "T", "T", "B", "-", "-"],
        ["Chọn ngày khám", "T", "T", "F", "-", "-", "-"],
        ["Chọn giờ (8h-20h)", "T", "T", "-", "-", "-", "-"],
        ["Không trùng lịch", "T", "F", "-", "-", "-", "-"],
        ["Hành động hệ thống", "", "", "", "", "", ""],
        ["Đặt lịch thành công", "T", "F", "F", "F", "F", "F"]
    ]
    
    for r_idx, row_values in enumerate(table_data):
        for c_idx, val in enumerate(row_values):
            cell = table.cell(r_idx, c_idx)
            cell.text = val
            p = cell.text_frame.paragraphs[0]
            p.font.name = font_main
            p.font.size = Pt(11)
            
            if c_idx == 0:
                p.alignment = PP_ALIGN.LEFT
            else:
                p.alignment = PP_ALIGN.CENTER
                
            if r_idx == 0:
                p.font.bold = True
                p.font.color.rgb = RGBColor(255, 255, 255)
                cell.fill.solid()
                cell.fill.fore_color.rgb = RGBColor(15, 76, 129)
            elif r_idx == 6:
                p.font.bold = True
                p.font.color.rgb = RGBColor(15, 23, 42)
                cell.fill.solid()
                cell.fill.fore_color.rgb = RGBColor(233, 238, 242)
            else:
                p.font.color.rgb = RGBColor(15, 23, 42)
                cell.fill.solid()
                if r_idx % 2 == 1:
                    cell.fill.fore_color.rgb = RGBColor(248, 250, 252)
                else:
                    cell.fill.fore_color.rgb = RGBColor(255, 255, 255)
                    
            if r_idx == 7:
                p.font.bold = True
                if val == "T":
                    p.font.color.rgb = RGBColor(22, 163, 74)
                elif val == "F":
                    p.font.color.rgb = RGBColor(220, 38, 38)
                    
    err_path = os.path.join(ASSETS_DIR, "mockup_validation_error.png")
    if os.path.exists(err_path):
        slide20.shapes.add_picture(err_path, Inches(6.7), Inches(1.85), Inches(5.8), Inches(4.8))

    # ==============================================================================
    # SLIDE 21: CẢM ƠN (ENDING SLIDE THEO CHUẨN PHÔNG PP-ICTU)
    # ==============================================================================
    slide21 = prs.slides.add_slide(blank_layout)
    slide21.shapes.add_picture(bg_end_path, Inches(0), Inches(0), prs.slide_width, prs.slide_height)
    
    tb21 = slide21.shapes.add_textbox(Inches(1.0), Inches(1.8), Inches(11.333), Inches(2.0))
    tf21 = tb21.text_frame
    tf21.word_wrap = True
    
    p21 = tf21.paragraphs[0]
    p21.alignment = PP_ALIGN.CENTER
    p21.text = "CẢM ƠN THẦY CÔ VÀ CÁC BẠN\nĐÃ LẮNG NGHE!"
    p21.font.name = font_main
    p21.font.size = Pt(35)
    p21.font.bold = True
    p21.font.color.rgb = RGBColor(6, 61, 121)
    
    p21_sub = tf21.add_paragraph()
    p21_sub.alignment = PP_ALIGN.CENTER
    p21_sub.space_before = Pt(12)
    p21_sub.text = "Đề tài: Hệ thống quản lý nha khoa có tích hợp AI"
    p21_sub.font.name = font_main
    p21_sub.font.size = Pt(23)
    p21_sub.font.bold = True
    p21_sub.font.color.rgb = RGBColor(185, 39, 0)
    
    # Bottom Card
    card_end = slide21.shapes.add_shape(
        MSO_SHAPE.ROUNDED_RECTANGLE,
        Inches(3.2), Inches(4.3), Inches(6.933), Inches(2.2)
    )
    card_end.fill.solid()
    card_end.fill.fore_color.rgb = RGBColor(255, 255, 255)
    card_end.line.color.rgb = RGBColor(6, 61, 121)
    card_end.line.width = Pt(1.5)
    
    tf21_c = card_end.text_frame
    tf21_c.word_wrap = True
    tf21_c.vertical_anchor = MSO_ANCHOR.MIDDLE
    
    p_c1 = tf21_c.paragraphs[0]
    p_c1.alignment = PP_ALIGN.CENTER
    p_c1.text = "BÁO CÁO MÔN: ỨNG DỤNG TRÍ TUỆ NHÂN TẠO"
    p_c1.font.name = font_main
    p_c1.font.size = Pt(18)
    p_c1.font.bold = True
    p_c1.font.color.rgb = RGBColor(6, 61, 121)
    
    p_c2 = tf21_c.add_paragraph()
    p_c2.alignment = PP_ALIGN.CENTER
    p_c2.space_before = Pt(8)
    p_c2.text = "Sinh viên thực hiện: Nhóm 1\n(Tạ Thị Thúy Quỳnh - Ngô Xuân Minh Hiếu - Lưu Tuấn Anh)"
    p_c2.font.name = font_main
    p_c2.font.size = Pt(16)
    p_c2.font.color.rgb = RGBColor(30, 41, 59)

    # Save presentation
    prs.save(OUTPUT_PPTX)
    print(f"Presentation saved to: {OUTPUT_PPTX}")
    
    # Save a fresh copy to Desktop as well
    desktop_new_pptx = r"C:\Users\Admin\Desktop\BAO_CAO_DU_AN_NHA_KHOA_ICTU.pptx"
    try:
        prs.save(desktop_new_pptx)
        print(f"Updated Desktop file: {desktop_new_pptx}")
    except PermissionError:
        alt_path = r"C:\Users\Admin\Desktop\BAO_CAO_DU_AN_NHA_KHOA_ICTU_MOI.pptx"
        prs.save(alt_path)
        print(f"File was locked, saved alternate copy to: {alt_path}")

if __name__ == "__main__":
    create_presentation()
