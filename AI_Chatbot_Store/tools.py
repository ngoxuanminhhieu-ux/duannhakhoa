import json
import os
import random
import datetime

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.join(BASE_DIR, "data")

SERVICES_FILE = os.path.join(DATA_DIR, "services.json")
DOCTORS_FILE = os.path.join(DATA_DIR, "doctors.json")
APPOINTMENTS_FILE = os.path.join(DATA_DIR, "appointments.json")
CONTACTS_FILE = os.path.join(DATA_DIR, "contacts.json")
POLICY_FILE = os.path.join(BASE_DIR, "chinh_sach_nha_khoa.txt")
VECTOR_DB_DIR = os.path.join(BASE_DIR, "my_vector_db")

def load_services():
    if os.path.exists(SERVICES_FILE):
        with open(SERVICES_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return []

def load_doctors():
    if os.path.exists(DOCTORS_FILE):
        with open(DOCTORS_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return []

def load_appointments():
    if os.path.exists(APPOINTMENTS_FILE):
        with open(APPOINTMENTS_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    return []

def save_appointments(appointments):
    os.makedirs(DATA_DIR, exist_ok=True)
    with open(APPOINTMENTS_FILE, "w", encoding="utf-8") as f:
        json.dump(appointments, f, ensure_ascii=False, indent=2)

def search_services(keyword="", category="", max_price=None):
    """Tra cứu dịch vụ nha khoa theo từ khóa (niềng răng, bọc sứ, nhổ răng...), phân loại hoặc giá tối đa."""
    services = load_services()
    results = []

    if keyword and str(keyword).lower() in ["none", "null", "undefined"]:
        keyword = ""
    if category and str(category).lower() in ["none", "null", "undefined"]:
        category = ""

    if max_price is not None:
        try:
            max_price = float(max_price)
        except (ValueError, TypeError):
            max_price = None

    kw = str(keyword).strip().lower() if keyword else ""
    cat = str(category).strip().lower() if category else ""

    for s in services:
        s_name = s.get("name", "").lower()
        s_desc = s.get("description", "").lower()
        s_cat = s.get("category", "").lower()
        s_min_p = s.get("min_price", 0)

        match_kw = (kw in s_name or kw in s_desc or kw in s_cat) if kw else True
        match_cat = (cat in s_cat or cat in s.get("category_slug", "").lower()) if cat else True
        match_price = (s_min_p <= max_price) if max_price is not None else True

        if match_kw and match_cat and match_price:
            results.append({
                "id": s["id"],
                "name": s["name"],
                "category": s["category"],
                "price_range": s["price_range"],
                "description": s["description"],
                "warranty": s.get("warranty", ""),
                "duration": s.get("duration", "")
            })

    return json.dumps(results, ensure_ascii=False, indent=2)

def search_doctors(specialty="", name=""):
    """Tra cứu danh sách bác sĩ nha khoa theo chuyên khoa hoặc tên."""
    doctors = load_doctors()
    results = []

    sp = str(specialty).strip().lower() if specialty and str(specialty).lower() not in ["none", "null"] else ""
    nm = str(name).strip().lower() if name and str(name).lower() not in ["none", "null"] else ""

    for d in doctors:
        d_name = d.get("name", "").lower()
        d_spec = d.get("specialty", "").lower()
        d_title = d.get("title", "").lower()

        match_sp = (sp in d_spec or sp in d_title) if sp else True
        match_nm = (nm in d_name) if nm else True

        if match_sp and match_nm:
            results.append({
                "id": d["id"],
                "name": d["name"],
                "title": d["title"],
                "specialty": d["specialty"],
                "experience": d["experience"],
                "schedule": d.get("schedule", "")
            })

    return json.dumps(results, ensure_ascii=False, indent=2)

def book_appointment_tool(name="", phone="", service_id="", doctor_id="", date="", time="", notes=""):
    """Hỗ trợ tạo lịch hẹn khám bệnh trực tiếp qua chatbot."""
    if not name or not phone:
        return json.dumps({
            "error": "Vui lòng cung cấp đầy đủ Họ tên và Số điện thoại để hệ thống đặt lịch hẹn cho bạn."
        }, ensure_ascii=False)

    appointments = load_appointments()
    app_id = f"NK-{random.randint(100000, 999999)}"

    # Find service name & doctor name if IDs passed
    services = load_services()
    doctors = load_doctors()

    service_name = "Khám răng tổng quát & Tư vấn AI"
    for s in services:
        if s["id"].upper() == service_id.upper() or service_id.lower() in s["name"].lower():
            service_name = s["name"]
            service_id = s["id"]
            break

    doctor_name = "Bác sĩ Chuyên khoa (Hệ thống phân công)"
    for d in doctors:
        if d["id"].upper() == doctor_id.upper() or doctor_id.lower() in d["name"].lower():
            doctor_name = d["name"]
            doctor_id = d["id"]
            break

    if not date:
        date = (datetime.date.today() + datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    if not time:
        time = "09:00"

    new_app = {
        "id": app_id,
        "name": name,
        "phone": phone,
        "email": "",
        "service_id": service_id,
        "service_name": service_name,
        "doctor_id": doctor_id,
        "doctor_name": doctor_name,
        "date": date,
        "time": time,
        "notes": notes or "Đặt lịch qua AI Chatbot Assistant",
        "status": "Chờ xác nhận",
        "created_at": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }

    appointments.append(new_app)
    save_appointments(appointments)

    return json.dumps({
        "success": True,
        "appointment_id": app_id,
        "name": name,
        "phone": phone,
        "service_name": service_name,
        "doctor_name": doctor_name,
        "date": date,
        "time": time,
        "status": "Chờ xác nhận",
        "message": f"Đặt lịch khám thành công! Mã lịch hẹn của bạn là [{app_id}]. Nhân viên phòng khám sẽ liên hệ qua SĐT {phone} để xác nhận trong ít phút."
    }, ensure_ascii=False, indent=2)

def search_policies(query):
    """Tra cứu quy định khám, bảo hành, vô trùng, trả góp 0% từ CSDL chính sách phòng khám."""
    try:
        import chromadb
        from chromadb.utils import embedding_functions

        if os.path.exists(VECTOR_DB_DIR):
            chroma_client = chromadb.PersistentClient(path=VECTOR_DB_DIR)
            embedding_func = embedding_functions.SentenceTransformerEmbeddingFunction(model_name="all-MiniLM-L6-v2")
            collection = chroma_client.get_collection(
                name="dental_policies",
                embedding_function=embedding_func
            )
            results = collection.query(
                query_texts=[query],
                n_results=2
            )
            if results and "documents" in results and results["documents"]:
                retrieved_docs = results["documents"][0]
                return json.dumps({"retrieved_policies": retrieved_docs}, ensure_ascii=False, indent=2)
    except Exception:
        pass

    if os.path.exists(POLICY_FILE):
        with open(POLICY_FILE, "r", encoding="utf-8") as f:
            lines = [l.strip() for l in f.readlines() if l.strip()]

        matched_lines = []
        q_words = query.lower().split()
        for line in lines:
            if any(term in line.lower() for term in q_words if len(term) > 2):
                matched_lines.append(line)

        if not matched_lines:
            matched_lines = lines[:15]

        return json.dumps({"retrieved_policies": matched_lines}, ensure_ascii=False, indent=2)

    return json.dumps({"error": "Không tìm thấy CSDL chính sách phòng khám"}, ensure_ascii=False)

TOOLS_SCHEMA = [
    {
        "type": "function",
        "function": {
            "name": "search_services",
            "description": "Tra cứu dịch vụ nha khoa, bảng giá, thời gian thực hiện và chế độ bảo hành.",
            "parameters": {
                "type": "object",
                "properties": {
                    "keyword": {"type": "string", "description": "Từ khóa tìm dịch vụ (VD: 'niềng răng', 'implant', 'tẩy trắng', 'nhổ răng')"},
                    "category": {"type": "string", "description": "Danh mục dịch vụ (VD: 'Chỉnh nha', 'Implant', 'Thẩm mỹ', 'Điều trị')"},
                    "max_price": {"type": "number", "description": "Mức giá tối đa mong muốn (VNĐ)"}
                }
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "search_doctors",
            "description": "Tra cứu thông tin đội ngũ bác sĩ chuyên khoa, bằng cấp, kinh nghiệm và lịch khám.",
            "parameters": {
                "type": "object",
                "properties": {
                    "specialty": {"type": "string", "description": "Chuyên khoa của bác sĩ (VD: 'Implant', 'Chỉnh nha', 'Nha khoa trẻ em')"},
                    "name": {"type": "string", "description": "Tên bác sĩ muốn tìm"}
                }
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "book_appointment_tool",
            "description": "Đăng ký lịch hẹn khám răng cho bệnh nhân khi đã có đủ thông tin Họ tên và Số điện thoại.",
            "parameters": {
                "type": "object",
                "properties": {
                    "name": {"type": "string", "description": "Họ và tên bệnh nhân"},
                    "phone": {"type": "string", "description": "Số điện thoại liên hệ"},
                    "service_id": {"type": "string", "description": "Tên hoặc mã dịch vụ đăng ký khám"},
                    "doctor_id": {"type": "string", "description": "Mã hoặc tên bác sĩ mong muốn khám"},
                    "date": {"type": "string", "description": "Ngày hẹn (định dạng YYYY-MM-DD)"},
                    "time": {"type": "string", "description": "Giờ hẹn (VD: '09:00', '14:30')"},
                    "notes": {"type": "string", "description": "Ghi chú triệu chứng hoặc yêu cầu đặc biệt"}
                },
                "required": ["name", "phone"]
            }
        }
    },
    {
        "type": "function",
        "function": {
            "name": "search_policies",
            "description": "Tra cứu thông tin chính sách bảo hành, quy trình vô trùng, thanh toán trả góp 0%, địa chỉ, giờ làm việc của phòng khám.",
            "parameters": {
                "type": "object",
                "properties": {
                    "query": {"type": "string", "description": "Nội dung câu hỏi về chính sách hoặc thông tin phòng khám"}
                },
                "required": ["query"]
            }
        }
    }
]
