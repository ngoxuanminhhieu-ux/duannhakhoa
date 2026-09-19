"""
Script tự động chuyển đổi Báo cáo Thiết lập Môi trường Phát triển, Git Flow & Coding Convention thành tệp Microsoft Word (.docx) chuyên nghiệp.

Thành viên nhóm: Ngô Xuân Minh Hiếu, Lưu Tuấn Anh
Bài tập lớn: Hệ thống Quản lý Nha khoa tích hợp AI Diagnostics
"""

import os
import sys

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")
    sys.stderr.reconfigure(encoding="utf-8")

import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls


def set_cell_background(cell, fill_hex: str):
    shading_xml = f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>'
    cell._tc.get_or_add_tcPr().append(parse_xml(shading_xml))


def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)


def add_heading_styled(doc, text, level):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Times New Roman"
    
    if level == 1:
        run.font.size = Pt(15)
        run.font.color.rgb = RGBColor(15, 44, 89) # Deep Navy
    elif level == 2:
        run.font.size = Pt(13)
        run.font.color.rgb = RGBColor(30, 78, 140)
    elif level == 3:
        run.font.size = Pt(12)
        run.font.color.rgb = RGBColor(50, 50, 50)
    return p


def create_env_report_word(output_docx_path: str):
    doc = docx.Document()
    
    # Lề 1 inch (2.54 cm)
    for section in doc.sections:
        section.top_margin = Inches(1)
        section.bottom_margin = Inches(1)
        section.left_margin = Inches(1)
        section.right_margin = Inches(1)

    normal_style = doc.styles['Normal']
    normal_style.font.name = 'Times New Roman'
    normal_style.font.size = Pt(12)
    normal_style.font.color.rgb = RGBColor(40, 40, 40)
    normal_style.paragraph_format.line_spacing = 1.25
    normal_style.paragraph_format.space_after = Pt(6)

    # --- BÌA BÁO CÁO ---
    title_p = doc.add_paragraph()
    title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    
    run_sub = title_p.add_run("BÀI TẬP LỚN HỌC PHẦN: THỊ GIÁC MÁY TÍNH & TRÍ TUỆ NHÂN TẠO\n")
    run_sub.font.size = Pt(13)
    run_sub.font.bold = True
    run_sub.font.color.rgb = RGBColor(100, 100, 100)

    run_title = title_p.add_run("BÁO CÁO MÔI TRƯỜNG PHÁT TRUYỂN, GIT FLOW & CODING CONVENTION\n")
    run_title.font.size = Pt(18)
    run_title.font.bold = True
    run_title.font.color.rgb = RGBColor(15, 44, 89)

    run_subtitle = title_p.add_run("ĐỀ TÀI: HỆ THỐNG QUẢN LÝ NHA KHOA TÍCH HỢP AI DIAGNOSTICS")
    run_subtitle.font.size = Pt(13.5)
    run_subtitle.font.bold = True
    run_subtitle.font.color.rgb = RGBColor(192, 57, 43)

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

    # Bảng thông tin nhóm
    info_table = doc.add_table(rows=4, cols=2)
    info_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    
    info_data = [
        ("Thành viên nhóm:", "1. Ngô Xuân Minh Hiếu\n2. Lưu Tuấn Anh"),
        ("Mục tiêu nhiệm vụ:", "Thiết lập Môi trường phát triển chung (VS Code, Docker), quy chuẩn Git Flow và Coding Convention PEP 8"),
        ("Công nghệ sử dụng:", "Git, GitHub, Docker, VS Code, Python 3.10+, Black, Flake8, OpenCV"),
        ("Trạng thái hoàn thành:", "100% - Đã cấu hình và tạo bộ công cụ kiểm thử tự động")
    ]
    
    for idx, (label, val) in enumerate(info_data):
        row = info_table.rows[idx]
        cell_lbl, cell_val = row.cells[0], row.cells[1]
        
        cell_lbl.width = Inches(2.2)
        cell_val.width = Inches(4.3)
        
        p_l = cell_lbl.paragraphs[0]
        r_l = p_l.add_run(label)
        r_l.bold = True
        r_l.font.color.rgb = RGBColor(15, 44, 89)
        p_l.paragraph_format.space_after = Pt(2)

        p_v = cell_val.paragraphs[0]
        r_v = p_v.add_run(val)
        p_v.paragraph_format.space_after = Pt(2)
        
        set_cell_background(cell_lbl, "F0F4F8")
        set_cell_background(cell_val, "FAFAFA")
        set_cell_margins(cell_lbl, 80, 80, 100, 100)
        set_cell_margins(cell_val, 80, 80, 100, 100)

    doc.add_paragraph().paragraph_format.space_after = Pt(10)

    # --- 1. QUY CHUẨN GIT FLOW ---
    add_heading_styled(doc, "1. QUY CHUẨN QUẢN LÝ MÃ NGUỒN GIT FLOW", level=1)
    doc.add_paragraph("Để đảm bảo quá trình làm việc nhóm giữa Ngô Xuân Minh Hiếu và Lưu Tuấn Anh diễn ra mượt mà, không bị xung đột mã nguồn (merge conflicts), nhóm áp dụng quy trình Git Flow tinh gọn với 2 nhánh dài hạn:")

    branches = [
        ("nhánh main: ", "Chứa mã nguồn ổn định nhất, dùng để demo và nộp bài. Tuyệt đối KHÔNG commit trực tiếp vào nhánh này."),
        ("nhánh develop: ", "Nhánh tích hợp mã nguồn chính của nhóm. Tất cả các tính năng hoàn thành sẽ gộp vào đây."),
        ("nhánh feature/<tên-tính-năng>: ", "Tạo riêng cho từng tính năng (ví dụ: feature/caries-detection, feature/wisdom-tooth)."),
        ("nhánh bugfix/<tên-lỗi>: ", "Dùng để xử lý các lỗi phát sinh trong quá trình phát triển.")
    ]
    for b_name, b_desc in branches:
        bp = doc.add_paragraph(style='List Bullet')
        r = bp.add_run(b_name)
        r.bold = True
        r.font.color.rgb = RGBColor(15, 44, 89)
        bp.add_run(b_desc)

    add_heading_styled(doc, "1.1 Quy chuẩn Tin nhắn Commit (Conventional Commits)", level=2)
    doc.add_paragraph("Mọi commit của thành viên phải tuân thủ cú pháp: <type>(<scope>): <mô tả ngắn>")
    
    commit_types = [
        ("feat: ", "Thêm tính năng mới (ví dụ: feat(opencv): thêm thuật toán CLAHE)"),
        ("fix: ", "Sửa lỗi (ví dụ: fix(gui): sửa lỗi hiển thị ảnh X-quang)"),
        ("docs: ", "Cập nhật tài liệu báo cáo (ví dụ: docs(report): cập nhật file Word)"),
        ("style: ", "Định dạng mã nguồn với Black/Flake8"),
        ("chore: ", "Cấu hình hệ thống, Dockerfile, requirements.txt")
    ]
    for c_type, c_desc in commit_types:
        bp = doc.add_paragraph(style='List Bullet')
        r = bp.add_run(c_type)
        r.bold = True
        bp.add_run(c_desc)

    # --- 2. QUY CHUẨN CODING CONVENTION ---
    add_heading_styled(doc, "2. QUY CHUẨN LẬP TRÌNH PYTHON (CODING CONVENTION - PEP 8)", level=1)
    rules = [
        ("Đặt tên File/Hàm/Biến: ", "Sử dụng kiểu snake_case (ví dụ: preprocessing.py, apply_clahe(), image_width)."),
        ("Đặt tên Lớp (Class): ", "Sử dụng kiểu PascalCase (ví dụ: DentalImageProcessor, CariesYoloDetector)."),
        ("Đặt tên Hằng số: ", "Sử dụng UPPER_SNAKE_CASE (ví dụ: DEFAULT_IMAGE_SIZE = (640, 640))."),
        ("Độ dài dòng tối đa: ", "120 ký tự (Line Length = 120)."),
        ("Type Hinting: ", "Bắt buộc khai báo kiểu dữ liệu cho tham số và giá trị trả về của hàm.")
    ]
    for r_title, r_desc in rules:
        bp = doc.add_paragraph(style='List Bullet')
        r = bp.add_run(r_title)
        r.bold = True
        bp.add_run(r_desc)

    # --- 3. DOCKER & MÔI TRƯỜNG PHÁT TRUYỂN ---
    add_heading_styled(doc, "3. MÔI TRƯỜNG PHÁT TRUYỂN ĐỒNG BỘ (VS CODE & DOCKER)", level=1)
    doc.add_paragraph("Để đảm bảo ứng dụng chạy đồng nhất trên máy tính của cả 2 thành viên mà không bị lỗi môi trường Windows/Linux, nhóm đã xây dựng Docker Container chứa sẵn Python 3.10, OpenCV và PyTorch.")

    # Bảng danh sách các tệp cấu hình môi trường
    config_table = doc.add_table(rows=7, cols=2)
    config_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    
    cfg_headers = ["Tệp Cấu Hình", "Vai Trò & Chức Năng Trong Dự Án"]
    hdr_row = config_table.rows[0]
    for idx, h_text in enumerate(cfg_headers):
        cell = hdr_row.cells[idx]
        set_cell_background(cell, "0F2C59")
        set_cell_margins(cell, 100, 100, 100, 100)
        p = cell.paragraphs[0]
        run = p.add_run(h_text)
        run.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)
        run.font.size = Pt(10.5)

    configs_data = [
        ("Dockerfile", "Đóng gói ứng dụng Python 3.10-slim kết hợp OpenCV hệ thống (libgl1-mesa-glx)."),
        ("docker-compose.yml", "Quản lý dịch vụ Container và mount volume mã nguồn cục bộ."),
        (".gitignore", "Chặn đưa lên Git các file dữ liệu nặng (*.zip, data/), file model (*.pt) và tệp tạm."),
        (".editorconfig", "Đảm bảo độ rộng Tab 4 space, xuống dòng LF và mã hóa UTF-8 giữa các máy."),
        (".vscode/settings.json", "Cấu hình tự động format code bằng Black và xóa khoảng trắng dư thừa khi lưu."),
        (".github/workflows/ci.yml", "Tự động chạy kiểm thử Linting Flake8 và Black format trên GitHub Actions.")
    ]

    for r_idx, (file_name, file_desc) in enumerate(configs_data):
        row = config_table.rows[r_idx + 1]
        bg_color = "F9FAFB" if r_idx % 2 == 1 else "FFFFFF"
        
        cell_fn, cell_fd = row.cells[0], row.cells[1]
        cell_fn.width = Inches(2.2)
        cell_fd.width = Inches(4.3)
        
        set_cell_background(cell_fn, bg_color)
        set_cell_background(cell_fd, bg_color)
        set_cell_margins(cell_fn, 80, 80, 80, 80)
        set_cell_margins(cell_fd, 80, 80, 80, 80)
        
        p1 = cell_fn.paragraphs[0]
        r1 = p1.add_run(file_name)
        r1.bold = True
        r1.font.size = Pt(9.5)
        
        p2 = cell_fd.paragraphs[0]
        r2 = p2.add_run(file_desc)
        r2.font.size = Pt(9.5)

    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # --- 4. HƯỚNG DẪN KIỂM TRA MÔI TRƯỜNG ---
    add_heading_styled(doc, "4. BỘ CÔNG CỤ KIỂM TRA MÔI TRƯỜNG TỰ ĐỘNG", level=1)
    doc.add_paragraph("Sinh viên đã viết script Python scripts/check_env.py để tự động quét và kiểm tra hệ thống. Thành viên nhóm chỉ cần thực thi lệnh:")
    
    code_p = doc.add_paragraph()
    set_cell_background(config_table.rows[0].cells[0], "0F2C59") # dummy call to ensure function works
    r_code = code_p.add_run("python scripts/check_env.py")
    r_code.font.name = "Consolas"
    r_code.font.size = Pt(10.5)
    r_code.font.bold = True

    # Chữ ký nhóm
    doc.add_paragraph().paragraph_format.space_after = Pt(25)
    sig_p = doc.add_paragraph()
    sig_p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    sig_run1 = sig_p.add_run("Thành viên nhóm thực hiện\n\n\n\n")
    sig_run1.font.bold = True
    sig_run2 = sig_p.add_run("Ngô Xuân Minh Hiếu & Lưu Tuấn Anh")
    sig_run2.font.bold = True
    sig_run2.font.size = Pt(12.5)

    doc.save(output_docx_path)
    print(f"=== Đã tạo thành công tệp Microsoft Word báo cáo môi trường tại: {output_docx_path} ===")


if __name__ == "__main__":
    output_path = "BAO_CAO_THIET_LAP_MOI_TRUONG.docx"
    create_env_report_word(output_path)
