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

    query_str = (str(specialty) + " " + str(name)).strip().lower()
    
    # Extract specialty keywords if full sentence provided
    spec_kw = ""
    for kw in ["chỉnh nha", "niềng răng", "implant", "tổng quát", "thẩm mỹ", "nhổ răng", "trẻ em"]:
        if kw in query_str:
            spec_kw = kw
            break

    for d in doctors:
        d_name = d.get("name", "").lower()
        d_spec = d.get("specialty", "").lower()
        d_title = d.get("title", "").lower()

        match = False
        if spec_kw:
            if spec_kw in d_spec or spec_kw in d_title or spec_kw in d_name:
                match = True
        elif query_str:
            if any(w in d_name or w in d_spec or w in d_title for w in query_str.split() if len(w) > 2):
                match = True
        else:
            match = True

        if match:
            results.append({
                "id": d["id"],
                "name": d["name"],
                "title": d["title"],
                "specialty": d["specialty"],
                "experience": d["experience"],
                "schedule": d.get("schedule", "")
            })

    # Fallback to returning all doctors if no specific match was found
    if not results:
        results = [{
            "id": d["id"],
            "name": d["name"],
            "title": d["title"],
            "specialty": d["specialty"],
            "experience": d["experience"],
            "schedule": d.get("schedule", "")
        } for d in doctors]

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
    """Tra cứu quy định khám, bảo hành, vô trùng, trả góp 0%, địa chỉ, giờ làm việc từ CSDL chính sách phòng khám."""
    query_lower = query.lower()

    if os.path.exists(POLICY_FILE):
        try:
            with open(POLICY_FILE, "r", encoding="utf-8") as f:
                content = f.read()
                lines = [l.strip() for l in content.split("\n") if l.strip() and not l.startswith("=")]

            # Topic-based Section Matching
            matched_lines = []

            # Address & Operating Hours
            if any(term in query_lower for term in ["địa chỉ", "ở đâu", "giờ làm", "mấy giờ", "mở cửa", "đóng cửa", "hotline", "sđt", "điện thoại", "liên hệ", "vị trí"]):
                matched_lines.extend([l for l in lines if any(k in l.lower() for k in ["địa chỉ", "hotline", "giờ làm việc", "thứ 2", "chủ nhật", "đặt lịch"])])

            # Payment & 0% Installment
            if any(term in query_lower for term in ["trả góp", "thanh toán", "thẻ", "bảo hiểm", "vat", "hóa đơn", "tiền mặt", "chuyển khoản"]):
                matched_lines.extend([l for l in lines if any(k in l.lower() for k in ["trả góp", "thanh toán", "bảo hiểm", "thẻ tín dụng", "trả trước"])])

            # Warranty Policies
            if any(term in query_lower for term in ["bảo hành", "trọn đời", "thẻ bảo hành", "qr code"]):
                matched_lines.extend([l for l in lines if any(k in l.lower() for k in ["bảo hành", "bọc răng sứ", "implant", "trám răng", "niềng răng"])])

            # Sterilization & Safety
            if any(term in query_lower for term in ["vô trùng", "an toàn", "dụng cụ", "iso", "lò hấp", "lây nhiễm"]):
                matched_lines.extend([l for l in lines if any(k in l.lower() for k in ["vô trùng", "dụng cụ", "iso", "ghế nha khoa"])])

            # Consultation & X-ray
            if any(term in query_lower for term in ["miễn phí", "x-quang", "panorama", "phác đồ"]):
                matched_lines.extend([l for l in lines if any(k in l.lower() for k in ["miễn phí", "x-quang", "phác đồ", "tư vấn"])])

            # Generic Scoring Fallback if specific section match wasn't triggered or gave few results
            if len(matched_lines) < 2:
                stop_words = {"phòng", "khám", "nha", "khoa", "cho", "tôi", "hỏi", "về", "là", "gì", "được", "không", "thế", "nào", "có", "này", "của"}
                q_words = [w for w in query_lower.replace("?", "").replace(",", "").split() if len(w) > 2 and w not in stop_words]

                scored_lines = []
                for line in lines:
                    line_l = line.lower()
                    score = sum(1 for w in q_words if w in line_l)
                    if score > 0:
                        scored_lines.append((score, line))

                scored_lines.sort(key=lambda x: x[0], reverse=True)
                matched_lines = [item[1] for item in scored_lines[:5]]

            if matched_lines:
                # Deduplicate preserving order
                seen = set()
                unique_matched = [x for x in matched_lines if not (x in seen or seen.add(x))]
                return json.dumps({"retrieved_policies": unique_matched[:6]}, ensure_ascii=False, indent=2)

            return json.dumps({"retrieved_policies": lines[4:12]}, ensure_ascii=False, indent=2)
        except Exception:
            pass

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
