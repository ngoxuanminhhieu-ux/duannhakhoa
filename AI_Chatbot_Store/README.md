# 🤖 TechStore AI Assistant (AI Chatbot Tư Vấn Khách Hàng)

Dự án **Chatbot AI Tư Vấn Khách Hàng TechStore** được lập trình theo kiến trúc **Sequential Execution Pipeline** & **RAG Vector Database (ChromaDB)** dựa trên tài liệu bài giảng.

---

## 📁 Cấu trúc thư mục Dự án

```text
AI_Chatbot_Store/
├── products.json           # CSDL sản phẩm có cấu trúc (ID, Giá, Tồn kho, GPU, Ưu đãi)
├── chinh_sach_cua_hang.txt # CSDL chính sách phi cấu trúc (Đổi trả, Bảo hành, Vận chuyển)
├── tools.py                # Module định nghĩa các hàm tra cứu CSDL & schema Tool Call
├── build_rag.py            # Script Vector hóa và nạp dữ liệu chính sách vào ChromaDB
├── pipeline.py             # Đường ống xử lý 6 bước (Intent -> Tool Execution -> Format)
├── app.py                  # Giao diện Web Chatbot Streamlit tích hợp Sidebar Logs
├── test_pipeline.py        # Script kiểm thử tự động toàn bộ luồng Pipeline
├── .env                    # Cấu hình API Key (OpenAI / Gemini)
└── requirements.txt        # Các thư viện phụ thuộc
```

---

## 🚀 Hướng dẫn Chạy ứng dụng trong VS Code

### Cách 1: Chạy trực tiếp từ nút Debug / Run trong VS Code (Khuyên dùng)
1. Mở dự án trong **VS Code**.
2. Nhấn phím `F5` hoặc vào tab **Run & Debug** (Ctrl+Shift+D).
3. Chọn cấu hình:
   - `🚀 Run TechStore AI Chatbot (Streamlit Web)` -> Trình duyệt web sẽ tự động mở giao diện Chatbot.
   - `⚡ Build RAG Vector DB (ChromaDB)` -> Nạp dữ liệu Vector DB.

### Cách 2: Chạy qua Terminal của VS Code
Mở Terminal trong VS Code (`Ctrl + ~`) và gõ các lệnh sau:

```bash
# 1. Chuyển vào thư mục dự án
cd AI_Chatbot_Store

# 2. (Tùy chọn) Nạp dữ liệu chính sách vào CSDL Vector ChromaDB
python build_rag.py

# 3. Khởi chạy giao diện Chatbot trên trình duyệt
streamlit run app.py
```

### Cách 3: Kiểm thử tự động trên Terminal (Headless CLI Test)
```bash
python test_pipeline.py
```

---

## 🎯 Các kịch bản chạy mẫu (Test Cases)

1. **Tra cứu sản phẩm:**
   - *Hỏi:* "Cho tôi xem laptop card RTX dưới 20 triệu"
   - *Log Sidebar:* `[Step 1 - Intent Router]: Detected intent = SEARCH`
   - *Log Sidebar:* `[Step 2 - Tool Call]: Executing search_products with args {'keyword': 'rtx', 'max_price': 20000000.0}`

2. **Tính tiền đơn hàng:**
   - *Hỏi:* "Tôi muốn mua 2 chiếc Lenovo LOQ P01 thì hết bao nhiêu tiền?"
   - *Log Sidebar:* `[Step 1 - Intent Router]: Detected intent = CALCULATE`
   - *Log Sidebar:* `[Step 2 - Tool Call]: Executing calculate_total with args {'product_id': 'P01', 'quantity': 2}`

3. **Tra cứu chính sách RAG:**
   - *Hỏi:* "Chính sách bảo hành và đổi trả của cửa hàng như thế nào?"
   - *Log Sidebar:* `[Step 1 - Intent Router]: Detected intent = POLICY`
   - *Log Sidebar:* `[Step 2 - Tool Call]: Executing search_policies with args {'query': '...'}`
