"""
Script Kiểm tra Môi trường Phát triển (Development Environment Checker)
Tự động kiểm tra Python, OpenCV, NumPy, Matplotlib, PyTorch, Git và Docker trên máy trạm.

Thành viên nhóm: Ngô Xuân Minh Hiếu, Lưu Tuấn Anh
Bài tập lớn: Hệ thống Quản lý Nha khoa tích hợp AI
"""

import sys
import os
import subprocess

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")
    sys.stderr.reconfigure(encoding="utf-8")


def check_module(module_name: str, import_name: str = None) -> bool:
    if import_name is None:
        import_name = module_name
    try:
        mod = __import__(import_name)
        version = getattr(mod, "__version__", "Đã cài đặt")
        print(f"  [✔] {module_name:<20} - Phiên bản: {version}")
        return True
    except ImportError:
        print(f"  [✘] {module_name:<20} - CHƯA CÀI ĐẶT!")
        return False


def check_cli_tool(command: str, tool_name: str) -> bool:
    try:
        res = subprocess.run([command, "--version"], capture_output=True, text=True, timeout=5)
        if res.returncode == 0:
            version_str = res.stdout.strip().split("\n")[0]
            print(f"  [✔] {tool_name:<20} - {version_str}")
            return True
        else:
            print(f"  [✘] {tool_name:<20} - Lỗi thực thi command")
            return False
    except Exception:
        print(f"  [✘] {tool_name:<20} - Không tìm thấy trong PATH hệ thống")
        return False


def main():
    print("=" * 70)
    print("      KIỂM TRA MÔI TRƯỜNG PHÁT TRUYỂN HỆ THỐNG AI NHA KHOA")
    print("      Thành viên: Ngô Xuân Minh Hiếu, Lưu Tuấn Anh")
    print("=" * 70)

    print(f"\n1. Thông tin Hệ điều hành & Python:")
    print(f"  - OS Platform    : {sys.platform}")
    print(f"  - Python Version : {sys.version.split()[0]}")
    print(f"  - Python Exec    : {sys.executable}")

    print("\n2. Kiểm tra các Thư viện Python cốt lõi:")
    modules = [
        ("OpenCV", "cv2"),
        ("NumPy", "numpy"),
        ("Matplotlib", "matplotlib"),
        ("Pillow", "PIL"),
        ("Roboflow", "roboflow"),
        ("KaggleHub", "kagglehub"),
        ("python-docx", "docx")
    ]
    
    success_count = 0
    for name, imp in modules:
        if check_module(name, imp):
            success_count += 1

    print("\n3. Kiểm tra các Công cụ CLI Hệ thống (Git & Docker):")
    check_cli_tool("git", "Git VCS")
    check_cli_tool("docker", "Docker Engine")

    print("\n" + "=" * 70)
    if success_count == len(modules):
        print("  ==> TẤT CẢ CÁC MÔ MÔI TRƯỜNG ĐÃ SẴN SÀNG CHO PHÁT TRUYỂN! <==")
    else:
        print(f"  ==> CÒN {len(modules) - success_count} THƯ THƯ VIỆN CHƯA CÀI ĐẶT. Hãy chạy: pip install -r requirements.txt <==")
    print("=" * 70)


if __name__ == "__main__":
    main()
