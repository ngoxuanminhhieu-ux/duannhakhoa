# HƯỚNG DẪN KHỞI TẠO DỰ ÁN CHO THÀNH VIÊN NHÓM

Xin chào **Ngô Xuân Minh Hiếu** và **Lưu Tuấn Anh**! Dưới đây là hướng dẫn nhanh 5 phút để đưa dự án **Hệ thống Quản lý Nha khoa tích hợp AI** lên máy trạm phát triển của bạn.

---

## 🚀 1. Tải Mã Nguồn & Thiết lập Môi trường Cục bộ

### Bước 1: Clone Repository về máy
```bash
git clone https://github.com/your-username/Duannhakhoa.git
cd Duannhakhoa
```

### Bước 2: Tạo Môi trường ảo Python (Virtual Environment)
```bash
# Trên Windows
python -m venv .venv
.venv\Scripts\activate

# Trên Linux/macOS
python3 -m venv .venv
source .venv/bin/activate
```

### Bước 3: Cài đặt các Thư viện Phụ thuộc
```bash
python -m pip install --upgrade pip
pip install -r requirements.txt
```

---

## 🛠️ 2. Chạy Kiểm tra Môi trường Tự động

Chạy script kiểm tra hệ thống:
```bash
python scripts/check_env.py
```
Nếu script báo màu xanh tất cả các mục (Python, OpenCV, NumPy, Matplotlib, PyTorch, Docker) nghĩa là máy của bạn đã sẵn sàng!

---

## 🐳 3. Khởi chạy với Docker (Khuyên dùng)

Nếu bạn không muốn cài đặt các gói phụ thuộc trên máy thật, bạn có thể chạy ngay bằng Docker:

```bash
docker-compose up --build
```
Dịch vụ AI Nha Khoa sẽ tự động chạy trong môi trường Container cách ly hoàn toàn.

---

## 📌 4. Quy tắc Đóng góp Mã nguồn (Contribution Workflow)

1. **Luôn checkout từ nhánh `develop`:**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/ten-tinh-nang-cua-ban
   ```
2. **Tuân thủ tin nhắn Commit:**
   - `feat:` thêm tính năng mới
   - `fix:` sửa lỗi
   - `docs:` cập nhật báo cáo/tài liệu
3. **Trước khi push code:** Đảm bảo mã nguồn được format sạch sẽ bằng `black src/`.
