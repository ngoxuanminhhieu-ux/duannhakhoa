# Website Quản Lý Và Giới Thiệu Phòng Khám Nha Khoa Hiện Đại

Hệ thống website phòng khám nha khoa hiện đại, chuyên nghiệp, responsive được xây dựng bằng **PHP 8.2**, **MySQL 8.0**, **Bootstrap 5**, **Docker Desktop** và tích hợp **AI Chatbot tư vấn qua OpenAI API**.

---

## 🌟 Tính Năng Nổi Bật

1. **Giao diện Khách hàng / Bệnh nhân**:
   - **Trang chủ**: Hero section cuốn hút, giới thiệu phòng khám, 8 dịch vụ nổi bật, 5 bác sĩ chuyên khoa, quy trình 5 bước, đánh giá khách hàng & CTA banner.
   - **Trang Giới thiệu**: Tầm nhìn, sứ mệnh, giá trị cốt lõi & cơ sở vật chất.
   - **Trang Dịch vụ**: Danh sách bảng giá dịch vụ niêm yết rõ ràng.
   - **Trang Bác sĩ**: Hồ sơ bác sĩ, chuyên khoa, kinh nghiệm.
   - **Trang Đặt lịch**: Form đăng ký trực tuyến lưu trực tiếp vào CSDL MySQL với kiểm tra định dạng email và số điện thoại.
   - **Trang Liên hệ**: Sơ đồ vị trí và form góp ý.

2. **AI Chatbot Nha Khoa (Floating Widget)**:
   - Bong bóng chat nổi góc phải màn hình 24/7.
   - Gọi API OpenAI (Backend cURL), nạp System Prompt chuẩn tư vấn nha khoa an toàn.
   - Hướng dẫn bệnh nhân đặt lịch tại `/pages/booking.php`.
   - Có cơ chế **Fallback thông minh** tự động phản hồi nếu chưa nạp API key hoặc API gián đoạn.

3. **Trang Quản trị Admin Panel**:
   - URL: `/admin/login.php` (Đăng nhập bảo mật session & mật khẩu Bcrypt).
   - **Dashboard**: Thống kê KPI (Số bệnh nhân, số lịch hẹn, lịch hẹn hôm nay, số bác sĩ, số dịch vụ) và bảng lịch hẹn mới nhất.
   - **Quản lý Bệnh nhân**: CRUD (Xem, Thêm, Sửa, Xóa, Tìm kiếm).
   - **Quản lý Lịch hẹn**: Lọc theo Ngày/Trạng thái, Tìm kiếm, Đổi trạng thái (`pending`, `confirmed`, `completed`, `cancelled`).
   - **Quản lý Bác sĩ**: CRUD đội ngũ bác sĩ.
   - **Quản lý Dịch vụ**: CRUD dịch vụ nha khoa & bảng giá.

---

## 📁 Cấu Trúc Thư Mục Dự Án

```
dental-clinic/
│
├── docker-compose.yml
├── Dockerfile
├── .env
├── .env.example
├── .gitignore
│
├── database/
│   └── init.sql
│
├── web/
│   ├── index.php
│   ├── config/
│   │   └── database.php
│   │
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   └── main.js
│   │   └── images/
│   │
│   ├── includes/
│   │   ├── header.php
│   │   └── footer.php
│   │
│   ├── pages/
│   │   ├── about.php
│   │   ├── services.php
│   │   ├── doctors.php
│   │   ├── booking.php
│   │   └── contact.php
│   │
│   ├── chatbot/
│   │   └── chat.php
│   │
│   └── admin/
│       ├── auth.php
│       ├── login.php
│       ├── dashboard.php
│       ├── patients.php
│       ├── appointments.php
│       ├── doctors.php
│       ├── services.php
│       └── logout.php
│
└── README.md
```

---

## 🛠️ Hướng Dẫn Cài Đặt & Khởi Chạy Bằng Visual Studio Code

### Bước 1: Yêu cầu chuẩn bị
- Đã cài **Visual Studio Code**.
- Đã cài **Docker Desktop** (và Docker Compose).

### Bước 2: Mở project trong VS Code
Mở VS Code, chọn **File > Open Folder** và chọn thư mục project `dental-clinic`.

### Bước 3: Cấu hình biến môi trường (`.env`)
Tạo file `.env` từ file mẫu `.env.example`:
```env
DB_HOST=db
DB_NAME=dental_clinic
DB_USER=dental_user
DB_PASSWORD=dental_password
OPENAI_API_KEY=sk-your_actual_openai_api_key_here
```

### Bước 4: Khởi chạy dự án với Docker Compose
Mở Terminal trong VS Code (`Ctrl + ~` hoặc `Terminal > New Terminal`) và gõ lệnh:

```bash
docker compose up -d --build
```

### Bước 5: Kiểm tra các Container đang chạy
```bash
docker compose ps
```
Bạn sẽ thấy 2 container hoạt động:
- `dental_web`: Chạy PHP 8.2 Apache trên cổng `8090`.
- `dental_db`: Chạy MySQL 8.0 trên cổng `3306`.

---

## 🌐 Đường Dẫn Truy Cập Ứng Dụng

- **Website Khách hàng**: [http://localhost:8090](http://localhost:8090)
- **Trang Quản trị Admin**: [http://localhost:8090/admin/login.php](http://localhost:8090/admin/login.php)

### Tài khoản Admin Mặc Định:
- **Username**: `admin`
- **Password**: `admin123`

---

## 🤖 Cấu Hướng Dẫn Tích Hợp OpenAI API Key

1. Đăng ký/Đăng nhập tài khoản tại [OpenAI Platform](https://platform.openai.com/).
2. Tạo Secret Key tại mục API Keys.
3. Dán key vào file `.env`:
   ```env
   OPENAI_API_KEY=sk-proj-xxxx...
   ```
4. Khởi động lại container web:
   ```bash
   docker compose restart web
   ```

*Lưu ý: Nếu chưa nhập API Key, Chatbot vẫn tự động phản hồi bằng dữ liệu mảng fallback thông minh mà không bị ngắt quãng!*

---

## 🛠️ Các Lệnh Docker Thường Dùng

- **Xem nhật ký Log**:
  ```bash
  docker compose logs -f
  ```
- **Dừng và xóa Container**:
  ```bash
  docker compose down
  ```
- **Reset làm sạch Cơ sở dữ liệu MySQL**:
  ```bash
  docker compose down -v
  docker compose up -d --build
  ```

---

## 🔒 Bảo Mật & Quy Chuẩn Code

- Sử dụng **PDO Prepared Statements** triệt để chống tấn công SQL Injection.
- Mã hóa mật khẩu Admin bằng hàm `password_hash()` (Bcrypt).
- Bảo vệ Endpoint Chatbot bằng cách gọi OpenAI ở Backend PHP, không để lộ API Key trên JavaScript Frontend.
- Chống lạm dụng bằng validate số điện thoại/email cả ở Client và Server.
