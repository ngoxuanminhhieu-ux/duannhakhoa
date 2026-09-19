# QUY CHUẨN GIT FLOW, DOCKER & CODING CONVENTION DÀNH CHO NHÓM

**Dự án:** Hệ thống Quản lý Nha khoa tích hợp AI Diagnostics  
**Thành viên thực hiện:** Ngô Xuân Minh Hiếu, Lưu Tuấn Anh  
**Cập nhật lần cuối:** 2026  

---

## 1. QUY CHUẨN GIT FLOW (GIT BRANCHING STRATEGY)

Nhóm áp dụng mô hình **Git Flow tinh gọn** với 2 nhánh chính (`main`, `develop`) và các nhánh tính năng ngắn hạn.

```
(main)      ─────────────────────────────────● (Production Release)
                ▲                       ▲
                │ Merge (Pull Request)   │ Merge
(develop)   ────●───────●───────────────●──── (Integration / Dev)
                │       ▲               ▲
                │       │ Merge         │ Merge
(feature/*)     └─●───●─┘               └─●───● (Feature Branch)
```

### 1.1 Quy định các Nhánh (Branches)
1. **`main`**: Nhánh mã nguồn ổn định nhất, sẵn sàng demo / nộp bài. Tuyệt đối **KHÔNG commit trực tiếp** vào nhánh này.
2. **`develop`**: Nhánh tích hợp mã nguồn chính của nhóm. Các tính năng sau khi hoàn thành sẽ được tạo Pull Request (PR) để gộp vào `develop`.
3. **`feature/<tên-tính-năng>`**: Nhánh phát triển tính năng mới. 
   - Ví dụ: `feature/caries-detection`, `feature/wisdom-tooth-classification`, `feature/opencv-clahe`.
4. **`bugfix/<tên-lỗi>`**: Nhánh sửa lỗi phát sinh trong quá trình thử nghiệm.
   - Ví dụ: `bugfix/fix-resize-padding`, `bugfix/unicode-encoding-win32`.
5. **`hotfix/<tên-lỗi-gấp>`**: Nhánh sửa lỗi khẩn cấp trực tiếp từ `main`.

### 1.2 Quy tắc đặt tên Commit (Conventional Commits Standard)
Cú pháp commit bắt buộc:  
`<type>(<scope>): <mô tả ngắn bằng tiếng Việt hoặc tiếng Anh>`

Các loại `<type>` quy định:
- `feat`: Thêm tính năng mới (Feature).
- `fix`: Sửa lỗi (Bug fix).
- `docs`: Cập nhật tài liệu (Documentation - Readme, Báo cáo).
- `style`: Định dạng code (Khoảng trắng, format Black, không ảnh hưởng logic).
- `refactor`: Tối ưu / tái cấu trúc mã nguồn mà không làm thay đổi tính năng.
- `test`: Thêm hoặc chỉnh sửa các unit test.
- `chore`: Cập nhật cấu hình build, dependencies, Dockerfile, `.gitignore`.

**Ví dụ Commit đúng chuẩn:**
```bash
git commit -m "feat(preprocessing): bổ sung thuật toán CLAHE tăng cường độ tương phản X-quang"
git commit -m "fix(eda): sửa lỗi UnicodeEncodeError trên hệ điều hành Windows"
git commit -m "docs(report): cập nhật báo cáo tuần 1 và file Word"
git commit -m "chore(docker): thêm Dockerfile và docker-compose.yml cho nhóm"
```

---

## 2. QUY CHUẨN LẬP TRÌNH PYTHON (CODING CONVENTIONS - PEP 8)

Mã nguồn Python trong dự án phải tuân thủ nghiêm ngặt chuẩn **PEP 8** và được cấu hình định dạng tự động với **Black** và **Flake8**.

### 2.1 Đặt tên (Naming Conventions)
- **Tên Module / File Python:** Sử dụng chữ thường, phân cách bởi dấu gạch dưới `snake_case`. (Ví dụ: `preprocessing.py`, `eda_visualization.py`).
- **Tên Hàm / Biến:** Sử dụng `snake_case`. (Ví dụ: `apply_clahe()`, `image_path`, `tooth_confidence`).
- **Tên Lớp (Class):** Sử dụng `PascalCase`. (Ví dụ: `DentalDatasetLoader`, `CariesYoloDetector`).
- **Tên Hằng số (Constant):** Sử dụng chữ in hoa `UPPER_SNAKE_CASE`. (Ví dụ: `DEFAULT_IMAGE_SIZE = (640, 640)`, `DISEASE_COLOR_MAP`).

### 2.2 Định dạng & Type Hinting
- Độ dài dòng tối đa (Line Length): **120 ký tự**.
- Sử dụng **Type Hinting** cho tất cả các định nghĩa hàm:
```python
def apply_clahe(image: np.ndarray, clip_limit: float = 2.5) -> np.ndarray:
    """Áp dụng thuật toán CLAHE lên ảnh X-quang."""
    ...
```
- Docstring chuẩn Google format hoặc Sphinx format cho từng hàm/lớp.

---

## 3. CẤU HÌNH MÔI TRƯỜNG PHÁT TRUYỂN CHUNG (VS CODE & DOCKER)

### 3.1 VS Code Extensions bắt buộc
Tất cả thành viên (Ngô Xuân Minh Hiếu, Lưu Tuấn Anh) cần cài đặt các tiện ích mở rộng sau trong VS Code (Xem [.vscode/extensions.json](file:///c:/Duannhakhoa/.vscode/extensions.json)):
1. **Python** (`ms-python.python`)
2. **Pylance** (`ms-python.vscode-pylance`)
3. **Black Formatter** (`ms-python.black-formatter`)
4. **Flake8** (`ms-python.flake8`)
5. **Docker** (`ms-azuretools.vscode-docker`)
6. **GitLens** (`eamodio.gitlens`)

### 3.2 Khởi chạy với Docker Container
Để đảm bảo code chạy hoàn toàn giống nhau trên máy của tất cả thành viên không bị lỗi môi trường Windows/Linux:

**Lệnh build và khởi chạy Docker:**
```bash
docker-compose up --build
```

---

## 4. QUY TRÌNH PHỐI HỢP NHÓM (WORKFLOW CHO HIẾU & TUẤN ANH)

1. **Trước khi bắt đầu làm task mới:**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/ten-tinh-nang
   ```
2. **Trong quá trình làm việc:**
   - Commit thường xuyên với tin nhắn commit rõ ràng theo chuẩn.
   - Chạy `python scripts/check_env.py` để kiểm tra môi trường.
3. **Sau khi hoàn thành task:**
   ```bash
   git push origin feature/ten-tinh-nang
   ```
   - Tạo **Pull Request (PR)** trên GitHub từ `feature/ten-tinh-nang` sang `develop`.
   - Người còn lại trong nhóm sẽ review code trước khi gộp (Merge).
