# -*- coding: utf-8 -*-
"""
Script tạo toàn bộ tài nguyên hình ảnh, sơ đồ và background cho bài báo cáo PowerPoint Đồ án Nha khoa Tích Hợp AI.
Background được thiết kế theo ĐÚNG MẪU NGƯỜI DÙNG CUNG CẤP:
- Màu nền cơ sở: #E9EEF2 (xanh xám nhạt)
- Khối chữ nhật chính: #0F4C81 (Classic Navy Blue)
- Khối góc trái dưới: #0C3C69 (Dark Navy)
- Hình tròn lớn góc phải: #195A96 (Medium Blue)
- Khối bo góc nổi ở giữa: #4F81BD (Steel Blue Card)
- Toàn bộ font chữ sử dụng: Times New Roman
"""

import os
import math
import numpy as np
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
from PIL import Image, ImageDraw

ASSETS_DIR = r"c:\Duannhakhoa\presentation_assets"
os.makedirs(ASSETS_DIR, exist_ok=True)

# Cấu hình font Times New Roman chuẩn cho Matplotlib
plt.rcParams['font.sans-serif'] = ['Times New Roman', 'DejaVu Serif', 'Arial']
plt.rcParams['font.family'] = 'serif'
plt.rcParams['axes.unicode_minus'] = False

# Palette màu chuẩn từ mẫu ảnh của người dùng
C_BASE = (233, 238, 242)      # #E9EEF2
C_MAIN_RECT = (15, 76, 129)   # #0F4C81
C_DARK_RECT = (12, 60, 105)   # #0C3C69
C_CIRCLE = (25, 90, 150)      # #195A96
C_CARD = (79, 129, 189)       # #4F81BD

HEX_MAIN = "#0F4C81"
HEX_DARK = "#0C3C69"
HEX_CIRCLE = "#195A96"
HEX_CARD = "#4F81BD"
HEX_BASE = "#E9EEF2"

# ==============================================================================
# 1. TẠO HÌNH NỀN SLIDE THEO ĐÚNG MẪU USER (TITLE, CONTENT, ENDING)
# ==============================================================================
# ==============================================================================
def create_custom_backgrounds():
    w, h = 1920, 1080
    media_dir = os.path.join(ASSETS_DIR, "ictu_template_media")
    os.makedirs(media_dir, exist_ok=True)
    
    # Trích xuất media từ Phông pp-ictu.pptx nếu chưa có
    template_pptx = r"D:\Bao_cao_LuckyDental_LaTeX_bo_sung\lucky_work\Ph#U00f4ng pp-ictu.pptx"
    if os.path.exists(template_pptx):
        import zipfile
        with zipfile.ZipFile(template_pptx) as z:
            for info in z.infolist():
                if "media" in info.filename:
                    fname = os.path.basename(info.filename)
                    with open(os.path.join(media_dir, fname), "wb") as f_out:
                        f_out.write(z.read(info.filename))

    # --- A. TITLE BACKGROUND (Theo chuẩn Phông pp-ictu: image7.jpeg) ---
    title_img_path = os.path.join(media_dir, "image7.jpeg")
    if os.path.exists(title_img_path):
        title_base = Image.open(title_img_path).convert("RGB")
        bg_title = title_base.resize((w, h), Image.Resampling.LANCZOS)
    else:
        bg_title = Image.new("RGB", (w, h), (255, 255, 255))
        
    # Thêm 5 tab màu chuẩn ICTU ở mép đáy
    tabs = ["image2.png", "image3.png", "image4.png", "image5.png", "image6.png"]
    tab_w = w // len(tabs)
    tab_h = 8
    tab_y = h - tab_h
    for i, tab_name in enumerate(tabs):
        tab_p = os.path.join(media_dir, tab_name)
        if os.path.exists(tab_p):
            t_img = Image.open(tab_p).convert("RGB")
            t_w = tab_w if i < len(tabs)-1 else (w - i * tab_w)
            t_resized = t_img.resize((t_w, tab_h), Image.Resampling.NEAREST)
            bg_title.paste(t_resized, (i * tab_w, tab_y))

    bg_title.save(os.path.join(ASSETS_DIR, "bg_title_ictu.png"), "PNG")
    bg_title.save(os.path.join(ASSETS_DIR, "bg_title_custom.png"), "PNG")
    
    # --- B. CONTENT BACKGROUND (Theo chuẩn Phông pp-ictu Slide Master: White + Line 30 + Footer Banner image1.png) ---
    bg_content = Image.new("RGB", (w, h), (255, 255, 255))
    draw_c = ImageDraw.Draw(bg_content)
    
    # Line 30: Header separator line
    line_y = 109
    draw_c.line([(28, line_y), (w - 28, line_y)], fill=(6, 61, 121), width=2)
    
    # Footer banner (image1.png)
    footer_img_path = os.path.join(media_dir, "image1.png")
    if os.path.exists(footer_img_path):
        footer_img = Image.open(footer_img_path).convert("RGBA")
        footer_y = 964
        footer_h = h - footer_y
        footer_resized = footer_img.resize((w, footer_h), Image.Resampling.LANCZOS)
        bg_content.paste(footer_resized, (0, footer_y), footer_resized)
        
    # Bottom 5 color tabs
    for i, tab_name in enumerate(tabs):
        tab_p = os.path.join(media_dir, tab_name)
        if os.path.exists(tab_p):
            t_img = Image.open(tab_p).convert("RGB")
            t_w = tab_w if i < len(tabs)-1 else (w - i * tab_w)
            t_resized = t_img.resize((t_w, tab_h), Image.Resampling.NEAREST)
            bg_content.paste(t_resized, (i * tab_w, tab_y))
            
    bg_content.save(os.path.join(ASSETS_DIR, "bg_content_ictu.png"), "PNG")
    bg_content.save(os.path.join(ASSETS_DIR, "bg_content_custom.png"), "PNG")
    
    # --- C. ENDING BACKGROUND ---
    bg_end = bg_title.copy()
    bg_end.save(os.path.join(ASSETS_DIR, "bg_end_ictu.png"), "PNG")
    bg_end.save(os.path.join(ASSETS_DIR, "bg_end_custom.png"), "PNG")
    print("ICTU backgrounds created successfully!")

# ==============================================================================
# 2. BIỂU ĐỒ CỘT KHẢO SÁT HIỆN TRẠNG (SLIDE 6)
# ==============================================================================
def create_chart_survey(output_path):
    fig, ax = plt.subplots(figsize=(8, 4.6), dpi=200)
    
    categories = ['Khó tra cứu\nhồ sơ bệnh án', 'Trùng lịch hẹn\nbác sĩ khám', 'Khó thống kê\ndoanh thu', 'Tốn thời gian\ntư vấn sơ bộ']
    values = [4.4, 3.2, 3.8, 4.2]
    colors = [HEX_MAIN, HEX_CARD, HEX_DARK, HEX_CIRCLE]
    
    bars = ax.bar(categories, values, color=colors, width=0.45, edgecolor=HEX_DARK, linewidth=1.2, zorder=3)
    
    ax.set_ylim(0, 5.2)
    ax.set_ylabel('Mức độ khó khăn (Thang điểm 1 - 5)', fontsize=11, fontweight='bold', color='#1e293b')
    ax.grid(axis='y', linestyle='--', alpha=0.5, color='#cbd5e1', zorder=0)
    ax.set_facecolor('#ffffff')
    fig.patch.set_facecolor('#ffffff')
    
    for bar in bars:
        height = bar.get_height()
        ax.annotate(f'{height}',
                    xy=(bar.get_x() + bar.get_width() / 2, height),
                    xytext=(0, 4),
                    textcoords="offset points",
                    ha='center', va='bottom', fontsize=11, fontweight='bold', color='#1e293b')
        
    ax.tick_params(axis='x', labelsize=10.5, colors='#1e293b')
    ax.tick_params(axis='y', labelsize=10, colors='#475569')
    ax.spines['top'].set_visible(False)
    ax.spines['right'].set_visible(False)
    ax.spines['left'].set_color('#94a3b8')
    ax.spines['bottom'].set_color('#94a3b8')
    
    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 3. SƠ ĐỒ CÂY PHÂN CẤP CHỨC NĂNG (SLIDE 8)
# ==============================================================================
def create_tree_functions(output_path):
    fig, ax = plt.subplots(figsize=(10.5, 5.8), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    # Root box
    root_box = patches.FancyBboxPatch((33, 86), 34, 11, boxstyle="round,pad=0.5", 
                                      ec=HEX_MAIN, fc="#f8fafc", lw=1.8)
    ax.add_patch(root_box)
    ax.text(50, 91.5, "Hệ thống Quản lý Nha khoa\nTích hợp Trí tuệ Nhân tạo (AI)", ha="center", va="center", 
            fontsize=10.5, fontweight="bold", color=HEX_MAIN)
    
    modules = [
        ("Quản lý\nđặt lịch", ["Xem lịch trống", "Đặt lịch mới", "Sửa/Hủy lịch", "Xác nhận lịch"]),
        ("Quản lý\nkhách hàng", ["Đăng ký hồ sơ", "Tìm kiếm BN", "Cập nhật bệnh án", "Lịch sử khám"]),
        ("Quản lý\nbác sĩ", ["Danh sách BS", "Phân công ca", "Chuyên khoa", "Nghỉ phép"]),
        ("Quản lý\ndịch vụ", ["Bảng giá DV", "Thuốc/Vật tư", "Thêm dịch vụ", "Cập nhật giá"]),
        ("Báo cáo\nthống kê", ["Doanh thu ngày", "Doanh thu tháng", "Số ca khám", "Biểu đồ so sánh"]),
        ("AI Chatbot\ntư vấn (LLM)", ["Hỏi đáp bệnh lý", "Báo giá tự động", "Gợi ý đặt lịch", "Nhật ký hội thoại"]),
        ("Cấu hình\nhệ thống", ["Quản lý User", "Phân quyền", "Bảo mật CSDL", "Cấu hình giờ"])
    ]
    
    x_coords = [7, 21.3, 35.6, 50, 64.3, 78.6, 93]
    
    ax.plot([50, 50], [86, 76], color=HEX_MAIN, lw=1.5)
    ax.plot([7, 93], [76, 76], color=HEX_MAIN, lw=1.5)
    
    for i, (mod_title, sub_items) in enumerate(modules):
        x = x_coords[i]
        ax.plot([x, x], [76, 68], color=HEX_MAIN, lw=1.5)
        
        # Module box
        is_ai = ("AI Chatbot" in mod_title)
        mod_box = patches.FancyBboxPatch((x - 6.2, 58), 12.4, 10, boxstyle="round,pad=0.4",
                                         ec=HEX_MAIN, fc=(HEX_BASE if is_ai else "#ffffff"), lw=1.4)
        ax.add_patch(mod_box)
        ax.text(x, 63, mod_title, ha="center", va="center", fontsize=8.5, fontweight="bold", 
                color=(HEX_MAIN if is_ai else "#0f172a"))
        
        y_curr = 48
        for sub in sub_items:
            ax.plot([x, x], [y_curr + 4, y_curr + 6], color="#64748b", lw=1)
            sub_box = patches.FancyBboxPatch((x - 6.0, y_curr - 4), 12.0, 7.5, boxstyle="round,pad=0.3",
                                             ec=HEX_CARD if is_ai else "#64748b", fc="#ffffff", lw=1)
            ax.add_patch(sub_box)
            ax.text(x, y_curr - 0.25, sub, ha="center", va="center", fontsize=7.2, color="#0f172a")
            y_curr -= 11.5
            
    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 4. SƠ ĐỒ USE CASE TỔNG QUÁT & PHÂN RÃ (SLIDE 9)
# ==============================================================================
def create_diagram_usecase(output_path):
    fig, ax = plt.subplots(figsize=(10.5, 5.8), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    box_tq = patches.Rectangle((14, 5), 40, 90, ec=HEX_MAIN, fc="#ffffff", lw=1.5)
    ax.add_patch(box_tq)
    ax.text(34, 91, "Hệ thống quản lý nha khoa\ncó tích hợp AI", ha="center", va="center",
            fontsize=9.5, fontweight="bold", color=HEX_MAIN)
    
    # Left Actor (Khách hàng)
    ax.plot([7], [68], marker='o', markersize=12, color=HEX_MAIN, mfc="#ffffff", mew=2)
    ax.plot([7, 7], [62, 50], color=HEX_MAIN, lw=2)
    ax.plot([3, 11], [58, 58], color=HEX_MAIN, lw=2)
    ax.plot([7, 3], [50, 40], color=HEX_MAIN, lw=2)
    ax.plot([7, 11], [50, 40], color=HEX_MAIN, lw=2)
    ax.text(7, 35, "Bệnh nhân", ha="center", fontsize=9.5, fontweight="bold")
    
    # Right Actor (Nhân viên / Admin)
    ax.plot([7], [22], marker='o', markersize=12, color=HEX_MAIN, mfc="#ffffff", mew=2)
    ax.plot([7, 7], [16, 4], color=HEX_MAIN, lw=2)
    ax.plot([3, 11], [12, 12], color=HEX_MAIN, lw=2)
    ax.plot([7, 3], [4, -6], color=HEX_MAIN, lw=2)
    ax.plot([7, 11], [4, -6], color=HEX_MAIN, lw=2)
    ax.text(7, -11, "Bác sĩ / Admin", ha="center", fontsize=9, fontweight="bold")
    
    ucs_left = [
        ("Xem lịch khám trống", 80),
        ("Đặt lịch khám", 70),
        ("Đổi lịch hẹn", 60),
        ("Hủy lịch hẹn", 50),
        ("Tư vấn AI Chatbot (LLM)", 40),
        ("Tiếp nhận đặt lịch", 30),
        ("Quản lý thông tin BN", 20),
        ("Quản lý lịch khám / BS", 10)
    ]
    
    for uc_name, y_pos in ucs_left:
        ellipse = patches.Ellipse((34, y_pos), 24, 7, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
        ax.add_patch(ellipse)
        ax.text(34, y_pos, uc_name, ha="center", va="center", fontsize=8.2, color="#0f172a")
        if y_pos >= 40:
            ax.plot([11, 22], [55, y_pos], color="#64748b", lw=1)
        if y_pos <= 40:
            ax.plot([11, 22], [10, y_pos], color="#64748b", lw=1)
            
    # --- PHÂN RÃ UC01 (Top Right) ---
    box_uc01 = patches.Rectangle((58, 52), 40, 43, ec=HEX_MAIN, fc="#ffffff", lw=1.5)
    ax.add_patch(box_uc01)
    ax.text(78, 91.5, "Phân rã UC01 - Xem lịch khám trống", ha="center", va="center",
            fontsize=9, fontweight="bold", color=HEX_MAIN)
    
    ax.plot([54], [73], marker='o', markersize=9, color=HEX_MAIN, mfc="#ffffff", mew=1.8)
    ax.plot([54, 54], [68, 60], color=HEX_MAIN, lw=1.5)
    ax.plot([51, 57], [65, 65], color=HEX_MAIN, lw=1.5)
    ax.plot([54, 51], [60, 53], color=HEX_MAIN, lw=1.5)
    ax.plot([54, 57], [60, 53], color=HEX_MAIN, lw=1.5)
    ax.text(54, 49, "Bệnh nhân", ha="center", fontsize=8, fontweight="bold")
    
    el_main = patches.Ellipse((68, 73), 15, 6.5, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
    ax.add_patch(el_main)
    ax.text(68, 73, "Xem lịch khám", ha="center", va="center", fontsize=7.5)
    ax.plot([57, 60.5], [65, 73], color="#64748b", lw=1)
    
    subs_01 = [("Danh sách slot trống", 83), ("Chọn bác sĩ/chuyên khoa", 73), ("Chọn ngày khám", 63)]
    for sub_name, y_p in subs_01:
        el_s = patches.Ellipse((88, y_p), 18, 6, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
        ax.add_patch(el_s)
        ax.text(88, y_p, sub_name, ha="center", va="center", fontsize=7.2)
        ax.annotate("", xy=(79, y_p), xytext=(75.5, 73), arrowprops=dict(arrowstyle="->", color="#64748b", ls="--", lw=1))
        ax.text(77, (y_p + 73)/2 + 1, "«include»", fontsize=6.5, color="#475569", ha="center")
        
    # --- PHÂN RÃ UC02 (Bottom Right) ---
    box_uc02 = patches.Rectangle((58, 5), 40, 43, ec=HEX_MAIN, fc="#ffffff", lw=1.5)
    ax.add_patch(box_uc02)
    ax.text(78, 44.5, "Phân rã UC02 - Đặt lịch khám nha khoa", ha="center", va="center",
            fontsize=9, fontweight="bold", color=HEX_MAIN)
    
    ax.plot([54], [25], marker='o', markersize=9, color=HEX_MAIN, mfc="#ffffff", mew=1.8)
    ax.plot([54, 54], [20, 12], color=HEX_MAIN, lw=1.5)
    ax.plot([51, 57], [17, 17], color=HEX_MAIN, lw=1.5)
    ax.plot([54, 51], [12, 5], color=HEX_MAIN, lw=1.5)
    ax.plot([54, 57], [12, 5], color=HEX_MAIN, lw=1.5)
    ax.text(54, 1, "Bệnh nhân", ha="center", fontsize=8, fontweight="bold")
    
    el_main2 = patches.Ellipse((67, 25), 14, 6.5, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
    ax.add_patch(el_main2)
    ax.text(67, 25, "Đặt lịch khám", ha="center", va="center", fontsize=7.5)
    ax.plot([57, 60], [17, 25], color="#64748b", lw=1)
    
    subs_02 = [("Chọn ngày giờ đặt", 36), ("Chọn dịch vụ & BS", 27), ("Nhập thông tin bệnh nhân", 18), ("Hỏi tư vấn AI Chatbot", 9)]
    for sub_name, y_p in subs_02:
        el_s = patches.Ellipse((88, y_p), 18, 6, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
        ax.add_patch(el_s)
        ax.text(88, y_p, sub_name, ha="center", va="center", fontsize=7)
        ax.annotate("", xy=(79, y_p), xytext=(74, 25), arrowprops=dict(arrowstyle="->", color="#64748b", ls="--", lw=1))
        ax.text(76, (y_p + 25)/2 + 0.8, "«include»", fontsize=6.2, color="#475569", ha="center")

    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 5. BIỂU ĐỒ TRÌNH TỰ UC01 (SLIDE 10)
# ==============================================================================
def create_seq_diagram_xem_lich(output_path):
    fig, ax = plt.subplots(figsize=(10, 5.5), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    ax.text(50, 96, "UC01 - Xem lịch khám trống & Chatbot AI tư vấn", ha="center", fontsize=11, fontweight="bold", color=HEX_MAIN)
    
    objs = [
        ("Bệnh nhân", 15, True),
        ("Giao diện Lịch khám\n«Presentation»", 38, False),
        ("Bộ xử lý Controller\n«Business Layer»", 64, False),
        ("CSDL & AI Model\n«Data / LLM Engine»", 90, False)
    ]
    
    for name, x, is_actor in objs:
        if is_actor:
            ax.plot([x], [88], marker='o', markersize=10, color=HEX_MAIN, mfc="#ffffff", mew=1.8)
            ax.plot([x, x], [83, 76], color=HEX_MAIN, lw=1.5)
            ax.plot([x - 2.5, x + 2.5], [80, 80], color=HEX_MAIN, lw=1.5)
            ax.plot([x, x - 2.5], [76, 70], color=HEX_MAIN, lw=1.5)
            ax.plot([x, x + 2.5], [76, 70], color=HEX_MAIN, lw=1.5)
            ax.text(x, 65, name, ha="center", fontsize=8.5, fontweight="bold")
            ax.plot([x, x], [62, 10], color="#94a3b8", ls="--", lw=1.2)
        else:
            box = patches.FancyBboxPatch((x - 10, 78), 20, 10, boxstyle="round,pad=0.3", ec=HEX_MAIN, fc="#f8fafc", lw=1.2)
            ax.add_patch(box)
            ax.text(x, 83, name, ha="center", va="center", fontsize=8, fontweight="bold", color=HEX_MAIN)
            ax.plot([x, x], [78, 10], color="#94a3b8", ls="--", lw=1.2)
            box_b = patches.FancyBboxPatch((x - 10, 2), 20, 8, boxstyle="round,pad=0.3", ec=HEX_MAIN, fc="#f8fafc", lw=1.2)
            ax.add_patch(box_b)
            ax.text(x, 6, name.split("\n")[0], ha="center", va="center", fontsize=7.5, color=HEX_MAIN)

    ax.annotate("", xy=(38, 58), xytext=(15, 58), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(26.5, 60, "Truy cập trang / Hỏi AI Chatbot", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(15, 52), xytext=(38, 52), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(26.5, 53.5, "Gợi ý chọn dịch vụ & khung giờ", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(38, 46), xytext=(15, 46), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(26.5, 48, "Nhập ngày & Chọn chuyên khoa", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(64, 42), xytext=(38, 42), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(51, 43.5, "yêu cầu tìm slot trống (ngày, khoa)", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(90, 36), xytext=(64, 36), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(77, 37.5, "queryLichTrong & call_rag_model()", ha="center", fontsize=7.5, color="#0f172a")
    
    ax.annotate("", xy=(64, 30), xytext=(90, 30), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(77, 31.5, "danh sách kết quả & tư vấn AI", ha="center", fontsize=7.5, color="#0f172a")
    
    ax.annotate("", xy=(38, 24), xytext=(64, 24), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(51, 25.5, "trả về Danh sách slot trống", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(15, 18), xytext=(38, 18), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(26.5, 19.5, "Hiển thị danh sách bác sĩ & giờ khám trống", ha="center", fontsize=8, color="#0f172a")
    
    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 6. BIỂU ĐỒ TRÌNH TỰ UC05 (SLIDE 11)
# ==============================================================================
def create_seq_diagram_tiep_nhan(output_path):
    fig, ax = plt.subplots(figsize=(10, 5.5), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    ax.text(50, 96, "UC05 - Tiếp nhận & Xác nhận lịch hẹn khám (Admin)", ha="center", fontsize=11, fontweight="bold", color=HEX_MAIN)
    
    objs = [
        ("Nhân viên\n/ Bác sĩ", 15, True),
        ("Giao diện Quản trị\n«Presentation»", 40, False),
        ("Bộ xử lý Tiếp nhận\n«Business Layer»", 68, False),
        ("Cơ sở dữ liệu\n«Data Layer»", 90, False)
    ]
    
    for name, x, is_actor in objs:
        if is_actor:
            ax.plot([x], [88], marker='o', markersize=10, color=HEX_MAIN, mfc="#ffffff", mew=1.8)
            ax.plot([x, x], [83, 76], color=HEX_MAIN, lw=1.5)
            ax.plot([x - 2.5, x + 2.5], [80, 80], color=HEX_MAIN, lw=1.5)
            ax.plot([x, x - 2.5], [76, 70], color=HEX_MAIN, lw=1.5)
            ax.plot([x, x + 2.5], [76, 70], color=HEX_MAIN, lw=1.5)
            ax.text(x, 65, name, ha="center", fontsize=8.5, fontweight="bold")
            ax.plot([x, x], [62, 10], color="#94a3b8", ls="--", lw=1.2)
        else:
            box = patches.FancyBboxPatch((x - 10, 78), 20, 10, boxstyle="round,pad=0.3", ec=HEX_MAIN, fc="#f8fafc", lw=1.2)
            ax.add_patch(box)
            ax.text(x, 83, name, ha="center", va="center", fontsize=8, fontweight="bold", color=HEX_MAIN)
            ax.plot([x, x], [78, 10], color="#94a3b8", ls="--", lw=1.2)
            box_b = patches.FancyBboxPatch((x - 10, 2), 20, 8, boxstyle="round,pad=0.3", ec=HEX_MAIN, fc="#f8fafc", lw=1.2)
            ax.add_patch(box_b)
            ax.text(x, 6, name.split("\n")[0], ha="center", va="center", fontsize=7.5, color=HEX_MAIN)

    ax.annotate("", xy=(40, 58), xytext=(15, 58), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(27.5, 60, "Chọn chức năng Tiếp nhận đặt lịch", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(40, 51), xytext=(15, 51), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(27.5, 52.5, "Nhập thông tin BN & Yêu cầu đặt", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(68, 45), xytext=(40, 45), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(54, 46.5, "yêu cầu lập phiếu khám (data)", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(90, 39), xytext=(68, 39), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(79, 40.5, "lưu thông tin BN & phiếu khám", ha="center", fontsize=7.5, color="#0f172a")
    
    ax.annotate("", xy=(68, 33), xytext=(90, 33), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(79, 34.5, "xác nhận lưu thành công", ha="center", fontsize=7.5, color="#0f172a")
    
    ax.annotate("", xy=(90, 27), xytext=(68, 27), arrowprops=dict(arrowstyle="->", color="#0f172a", lw=1.2))
    ax.text(79, 28.5, "cập nhật trạng thái = 'confirmed'", ha="center", fontsize=7.2, color="#0f172a")
    
    ax.annotate("", xy=(40, 21), xytext=(68, 21), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(54, 22.5, "thông báo Tiếp nhận thành công", ha="center", fontsize=8, color="#0f172a")
    
    ax.annotate("", xy=(15, 15), xytext=(40, 15), arrowprops=dict(arrowstyle="->", color="#0f172a", ls="--", lw=1.2))
    ax.text(27.5, 16.5, "Hoàn tất tiếp nhận & In phiếu hẹn", ha="center", fontsize=8, color="#0f172a")
    
    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 7. SƠ ĐỒ LỚP CHI TIẾT (SLIDE 14)
# ==============================================================================
def create_class_diagram(output_path):
    fig, ax = plt.subplots(figsize=(10.5, 6), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    def draw_uml_class(x, y, w, h, title, fields, methods):
        box = patches.Rectangle((x, y), w, h, ec=HEX_MAIN, fc="#ffffff", lw=1.2)
        ax.add_patch(box)
        ax.plot([x, x + w], [y + h - 5.5, y + h - 5.5], color=HEX_MAIN, lw=1.2)
        ax.text(x + w/2, y + h - 3, title, ha="center", va="center", fontsize=8.5, fontweight="bold", color=HEX_MAIN)
        
        cur_y = y + h - 8.5
        for f in fields:
            ax.text(x + 1, cur_y, f, fontsize=7, color="#1e293b")
            cur_y -= 3.2
            
        sep_y = cur_y - 0.5
        ax.plot([x, x + w], [sep_y, sep_y], color=HEX_CARD, lw=1)
        
        cur_y = sep_y - 3
        for m in methods:
            ax.text(x + 1, cur_y, m, fontsize=7, color="#1e293b")
            cur_y -= 3.2
            
    draw_uml_class(4, 52, 23, 38, "BenhNhan", 
                   ["-MaBN: int", "-TenBN: string", "-SDT: string", "-Email: string", "-NgaySinh: Date"],
                   ["+dangKy()", "+datLich()", "+xemHoSo()", "+capNhatThongTin()"])
    
    draw_uml_class(37, 36, 26, 42, "LichHen", 
                   ["-MaLich: int", "-NgayKham: Date", "-GioKham: Time", "-TrangThai: string", "-GhiChu: string", "-MaBN: int", "-MaBS: int"],
                   ["+taoLichHen()", "+huyLich()", "+doiLich()", "+capNhatTrangThai()"])
    
    draw_uml_class(71, 55, 25, 36, "BacSi", 
                   ["-MaBS: int", "-TenBS: string", "-ChuyenKhoa: string", "-SoNamKN: int", "-TrangThai: string"],
                   ["+xemLichKham()", "+khamBenh()", "+keDon()", "+dangKyCaTruc()"])
    
    draw_uml_class(4, 8, 23, 34, "DichVu", 
                   ["-MaDV: int", "-TenDV: string", "-GiaDV: double", "-MoTa: string", "-ThoiGianThucHien: int"],
                   ["+themDichVu()", "+suaDichVu()", "+xoaDichVu()"])
    
    draw_uml_class(37, 3, 26, 27, "HoaDon", 
                   ["-MaHD: int", "-NgayLap: Date", "-TongTien: double", "-PhuongThuc: string", "-MaLich: int"],
                   ["+tinhTien()", "+thanhToan()", "+inHoaDon()"])
    
    draw_uml_class(71, 8, 25, 38, "NhanVien (Admin)", 
                   ["-MaNV: int", "-TenNV: string", "-TaiKhoan: string", "-MatKhau: string", "-VaiTro: string"],
                   ["+dangNhap()", "+quanLyLichKham()", "+quanLyBenhNhan()", "+thongKeDoanhThu()"])
    
    ax.plot([27, 37], [70, 70], color=HEX_MAIN, lw=1.2)
    ax.text(28.5, 71.5, "1", fontsize=7.5, fontweight="bold")
    ax.text(35, 71.5, "N", fontsize=7.5, fontweight="bold")
    
    ax.plot([71, 63], [70, 70], color=HEX_MAIN, lw=1.2)
    ax.text(69.5, 71.5, "1", fontsize=7.5, fontweight="bold")
    ax.text(64.5, 71.5, "N", fontsize=7.5, fontweight="bold")
    
    ax.plot([50, 50], [36, 30], color=HEX_MAIN, lw=1.2)
    ax.text(51.5, 34, "1", fontsize=7.5, fontweight="bold")
    ax.text(51.5, 31, "1", fontsize=7.5, fontweight="bold")
    
    ax.plot([27, 37], [25, 45], color=HEX_MAIN, lw=1.2)
    ax.text(28.5, 27, "1", fontsize=7.5, fontweight="bold")
    ax.text(35, 43, "N", fontsize=7.5, fontweight="bold")
    
    ax.plot([71, 63], [35, 45], color=HEX_MAIN, lw=1.2)
    ax.text(69.5, 36.5, "1", fontsize=7.5, fontweight="bold")
    ax.text(64.5, 43.5, "N", fontsize=7.5, fontweight="bold")

    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

# ==============================================================================
# 8. KIẾN TRÚC PHÂN TẦNG (SLIDE 15)
# ==============================================================================
def create_architecture_diagram(output_path):
    fig, ax = plt.subplots(figsize=(6.5, 6.2), dpi=200)
    ax.axis('off')
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    
    ax.plot([50], [94], marker='o', markersize=10, color=HEX_MAIN, mfc="#ffffff", mew=1.8)
    ax.plot([50, 50], [89, 82], color=HEX_MAIN, lw=1.5)
    ax.plot([47.5, 52.5], [86, 86], color=HEX_MAIN, lw=1.5)
    ax.plot([50, 47.5], [82, 76], color=HEX_MAIN, lw=1.5)
    ax.plot([50, 52.5], [82, 76], color=HEX_MAIN, lw=1.5)
    ax.text(50, 72, "Người dùng\n(Bệnh nhân & Bác sĩ/Admin)", ha="center", fontsize=8, fontweight="bold")
    ax.annotate("", xy=(50, 68), xytext=(50, 71), arrowprops=dict(arrowstyle="->", color=HEX_MAIN, lw=1.2))
    
    # Tier 1
    p1 = patches.FancyBboxPatch((15, 53), 70, 15, boxstyle="round,pad=0.5", ec=HEX_MAIN, fc="#eff6ff", lw=1.5)
    ax.add_patch(p1)
    ax.text(50, 64.5, "Tầng giao diện (Presentation Layer)", ha="center", fontsize=8.5, fontweight="bold", color=HEX_MAIN)
    ax.text(50, 58, "• HTML5, CSS3, Bootstrap 5, JavaScript Fetch API\n• Giao diện Web đặt lịch khám & Widget AI Chatbot tư vấn 24/7", 
            ha="center", fontsize=7.2, color="#1e293b")
    ax.annotate("", xy=(50, 48), xytext=(50, 53), arrowprops=dict(arrowstyle="->", color=HEX_MAIN, lw=1.2))
    
    # Tier 2
    p2 = patches.FancyBboxPatch((15, 33), 70, 15, boxstyle="round,pad=0.5", ec=HEX_CARD, fc="#f0fdf4", lw=1.5)
    ax.add_patch(p2)
    ax.text(50, 44.5, "Tầng nghiệp vụ & AI Engine (Business & AI Layer)", ha="center", fontsize=8.5, fontweight="bold", color="#15803d")
    ax.text(50, 38, "• Kiểm tra trùng lịch khám, phân quyền Session\n• Tích hợp AI RAG Pipeline (LLM API, System Prompt Nha Khoa)", 
            ha="center", fontsize=7.2, color="#1e293b")
    ax.annotate("", xy=(50, 28), xytext=(50, 33), arrowprops=dict(arrowstyle="->", color=HEX_MAIN, lw=1.2))
    
    # Tier 3
    p3 = patches.FancyBboxPatch((15, 14), 70, 14, boxstyle="round,pad=0.5", ec=HEX_DARK, fc="#fff7ed", lw=1.5)
    ax.add_patch(p3)
    ax.text(50, 24.5, "Tầng truy cập dữ liệu (Data Access Layer - DAL)", ha="center", fontsize=8.2, fontweight="bold", color=HEX_DARK)
    ax.text(50, 18.5, "• PDO MySQL / Query Builder / Prepared Statements\n• Ánh xạ thực thể: BenhNhan, BacSi, LichHen, DichVu", 
            ha="center", fontsize=7, color="#1e293b")
    ax.annotate("", xy=(50, 9), xytext=(50, 14), arrowprops=dict(arrowstyle="->", color=HEX_MAIN, lw=1.2))
    
    # Database
    p4 = patches.FancyBboxPatch((25, 0), 50, 9, boxstyle="round,pad=0.4", ec="#dc2626", fc="#fef2f2", lw=1.5)
    ax.add_patch(p4)
    ax.text(50, 4.5, "Cơ sở dữ liệu (MySQL 8.0 Database)\n(BenhNhan, BacSi, LichHen, DichVu, HoaDon)", 
            ha="center", va="center", fontsize=7.5, fontweight="bold", color="#b91c1c")

    plt.tight_layout()
    plt.savefig(output_path, bbox_inches='tight', dpi=200)
    plt.close()

if __name__ == "__main__":
    print("Generating custom backgrounds and assets...")
    create_custom_backgrounds()
    create_chart_survey(os.path.join(ASSETS_DIR, "chart_survey.png"))
    create_tree_functions(os.path.join(ASSETS_DIR, "tree_functions.png"))
    create_diagram_usecase(os.path.join(ASSETS_DIR, "diagram_usecase.png"))
    create_seq_diagram_xem_lich(os.path.join(ASSETS_DIR, "seq_xem_lich.png"))
    create_seq_diagram_tiep_nhan(os.path.join(ASSETS_DIR, "seq_tiep_nhan.png"))
    create_class_diagram(os.path.join(ASSETS_DIR, "diagram_class.png"))
    create_architecture_diagram(os.path.join(ASSETS_DIR, "diagram_architecture.png"))
    print("Custom assets generated successfully!")
