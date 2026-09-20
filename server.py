import json
import os
import sys
import random
import datetime
from fastapi import FastAPI, HTTPException, Query, Body
from fastapi.staticfiles import StaticFiles
from fastapi.responses import FileResponse, JSONResponse
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import Optional, List

# Add AI_Chatbot_Store to sys.path
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
AI_CHATBOT_DIR = os.path.join(BASE_DIR, "AI_Chatbot_Store")
if AI_CHATBOT_DIR not in sys.path:
    sys.path.insert(0, AI_CHATBOT_DIR)

from pipeline import CustomerServicePipeline
import tools

# Initialize FastAPI App
app = FastAPI(
    title="Nha Khoa Quốc Tế Premier - REST API & AI Chatbot Server",
    description="Hệ thống Backend Quản lý Nha Khoa & AI Assistant Server",
    version="2.0.0"
)

# Enable CORS for local development
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize AI Pipeline
pipeline_instance = CustomerServicePipeline()

# Pydantic Schemas for Request Validation
class ChatRequest(BaseModel):
    message: str = Field(..., description="Câu hỏi hoặc yêu cầu của bệnh nhân")

class AppointmentRequest(BaseModel):
    name: str = Field(..., min_length=2, description="Họ và tên bệnh nhân")
    phone: str = Field(..., min_length=8, description="Số điện thoại liên hệ")
    email: Optional[str] = ""
    service_id: Optional[str] = "DV01"
    doctor_id: Optional[str] = "BS01"
    date: Optional[str] = ""
    time: Optional[str] = "09:00"
    notes: Optional[str] = ""

class AppointmentStatusUpdate(BaseModel):
    status: str = Field(..., description="Trạng thái mới: 'Đã xác nhận', 'Từ chối', 'Đã hoàn thành', 'Đã hủy', 'Chờ xác nhận'")

class ContactRequest(BaseModel):
    name: str
    phone: str
    email: Optional[str] = ""
    subject: Optional[str] = ""
    message: str

class ServiceSchema(BaseModel):
    id: Optional[str] = None
    name: str
    category: str
    category_slug: str
    price_range: str
    min_price: float
    max_price: float
    image: Optional[str] = ""
    description: str
    duration: Optional[str] = "30-60 phút"
    warranty: Optional[str] = ""
    procedure: Optional[List[str]] = []

class DoctorSchema(BaseModel):
    id: Optional[str] = None
    name: str
    title: str
    specialty: str
    experience: str
    degree: str
    image: Optional[str] = ""
    description: str
    schedule: Optional[str] = "Thứ 2 - Thứ 7"

# --- API ENDPOINTS ---

@app.post("/api/chat")
async def chat_endpoint(req: ChatRequest):
    """API Chatbot AI Pipeline 3 bước dành cho Nha Khoa"""
    if not req.message or not req.message.strip():
        raise HTTPException(status_code=400, detail="Nội dung câu hỏi không được để trống")
    
    reply, logs = pipeline_instance.run_pipeline(req.message.strip())
    return {
        "reply": reply,
        "logs": logs
    }

@app.get("/api/services")
async def get_services(category: Optional[str] = None, search: Optional[str] = None):
    """Lấy danh sách dịch vụ nha khoa"""
    services = tools.load_services()
    if category and category != "all":
        services = [s for s in services if s.get("category_slug") == category or s.get("category").lower() == category.lower()]
    if search:
        search_lower = search.lower()
        services = [s for s in services if search_lower in s.get("name", "").lower() or search_lower in s.get("description", "").lower()]
    return services

@app.get("/api/services/{service_id}")
async def get_service_detail(service_id: str):
    """Lấy chi tiết 1 dịch vụ nha khoa"""
    services = tools.load_services()
    for s in services:
        if s.get("id").upper() == service_id.upper():
            return s
    raise HTTPException(status_code=404, detail=f"Không tìm thấy dịch vụ với mã {service_id}")

@app.post("/api/services")
async def create_service(svc: ServiceSchema):
    """Thêm dịch vụ nha khoa mới (Admin)"""
    services = tools.load_services()
    if not svc.id:
        svc.id = f"DV{len(services) + 1:02d}"
    
    new_service = svc.dict()
    services.append(new_service)
    
    with open(os.path.join(tools.DATA_DIR, "services.json"), "w", encoding="utf-8") as f:
        json.dump(services, f, ensure_ascii=False, indent=2)
    return new_service

@app.put("/api/services/{service_id}")
async def update_service(service_id: str, svc: ServiceSchema):
    """Cập nhật dịch vụ nha khoa (Admin)"""
    services = tools.load_services()
    updated = False
    for i, s in enumerate(services):
        if s.get("id").upper() == service_id.upper():
            svc_dict = svc.dict()
            svc_dict["id"] = s["id"]
            services[i] = svc_dict
            updated = True
            break
    if not updated:
        raise HTTPException(status_code=404, detail="Dịch vụ không tồn tại")
    
    with open(os.path.join(tools.DATA_DIR, "services.json"), "w", encoding="utf-8") as f:
        json.dump(services, f, ensure_ascii=False, indent=2)
    return {"message": "Cập nhật dịch vụ thành công"}

@app.delete("/api/services/{service_id}")
async def delete_service(service_id: str):
    """Xóa dịch vụ nha khoa (Admin)"""
    services = tools.load_services()
    services = [s for s in services if s.get("id").upper() != service_id.upper()]
    with open(os.path.join(tools.DATA_DIR, "services.json"), "w", encoding="utf-8") as f:
        json.dump(services, f, ensure_ascii=False, indent=2)
    return {"message": "Xóa dịch vụ thành công"}

@app.get("/api/doctors")
async def get_doctors(specialty: Optional[str] = None):
    """Lấy danh sách bác sĩ chuyên khoa"""
    doctors = tools.load_doctors()
    if specialty and specialty != "all":
        doctors = [d for d in doctors if specialty.lower() in d.get("specialty", "").lower()]
    return doctors

@app.get("/api/doctors/{doctor_id}")
async def get_doctor_detail(doctor_id: str):
    """Lấy chi tiết thông tin bác sĩ"""
    doctors = tools.load_doctors()
    for d in doctors:
        if d.get("id").upper() == doctor_id.upper():
            return d
    raise HTTPException(status_code=404, detail="Không tìm thấy bác sĩ")

@app.post("/api/doctors")
async def create_doctor(doc: DoctorSchema):
    """Thêm bác sĩ mới (Admin)"""
    doctors = tools.load_doctors()
    if not doc.id:
        doc.id = f"BS{len(doctors) + 1:02d}"
    
    new_doctor = doc.dict()
    doctors.append(new_doctor)
    with open(os.path.join(tools.DATA_DIR, "doctors.json"), "w", encoding="utf-8") as f:
        json.dump(doctors, f, ensure_ascii=False, indent=2)
    return new_doctor

@app.get("/api/prices")
async def get_prices():
    """Lấy bảng giá dịch vụ phân loại chi tiết"""
    services = tools.load_services()
    categorized = {
        "general": {"name": "Khám Tổng Quát & Dự Phòng", "items": []},
        "treatment": {"name": "Điều Trị Bệnh Lý Nha Khoa", "items": []},
        "orthodontics": {"name": "Chỉnh Nha - Niềng Răng", "items": []},
        "implant": {"name": "Cấy Ghép Implant Phục Hình", "items": []},
        "cosmetic": {"name": "Nha Khoa Thẩm Mỹ", "items": []}
    }
    
    for s in services:
        cat_slug = s.get("category_slug", "general")
        if cat_slug not in categorized:
            categorized[cat_slug] = {"name": s.get("category"), "items": []}
        categorized[cat_slug]["items"].append({
            "id": s["id"],
            "name": s["name"],
            "price_range": s["price_range"],
            "warranty": s.get("warranty", ""),
            "duration": s.get("duration", "")
        })
    return categorized

@app.post("/api/appointments")
async def create_appointment(app_req: AppointmentRequest):
    """Tạo lịch hẹn khám mới"""
    if not app_req.name or not app_req.phone:
        raise HTTPException(status_code=400, detail="Họ tên và Số điện thoại là bắt buộc")
    
    # Get Service Name and Doctor Name
    services = tools.load_services()
    doctors = load_doctors_list()

    service_name = "Khám răng tổng quát & Tư vấn AI"
    for s in services:
        if s["id"].upper() == (app_req.service_id or "").upper():
            service_name = s["name"]
            break

    doctor_name = "Bác sĩ Chuyên khoa (Lễ tân xếp lịch)"
    for d in doctors:
        if d["id"].upper() == (app_req.doctor_id or "").upper():
            doctor_name = d["name"]
            break

    app_id = f"NK-{random.randint(100000, 999999)}"
    date_str = app_req.date if app_req.date else (datetime.date.today() + datetime.timedelta(days=1)).strftime("%Y-%m-%d")
    time_str = app_req.time if app_req.time else "09:00"

    new_app = {
        "id": app_id,
        "name": app_req.name.strip(),
        "phone": app_req.phone.strip(),
        "email": app_req.email.strip() if app_req.email else "",
        "service_id": app_req.service_id or "DV01",
        "service_name": service_name,
        "doctor_id": app_req.doctor_id or "BS01",
        "doctor_name": doctor_name,
        "date": date_str,
        "time": time_str,
        "notes": app_req.notes.strip() if app_req.notes else "Đặt lịch qua website",
        "status": "Chờ xác nhận",
        "created_at": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }

    appointments = tools.load_appointments()
    appointments.insert(0, new_app)
    tools.save_appointments(appointments)

    return {
        "success": True,
        "appointment_id": app_id,
        "appointment": new_app,
        "message": f"Đặt lịch thành công! Mã lịch hẹn của bạn là [{app_id}]."
    }

@app.get("/api/appointments")
async def get_appointments(status: Optional[str] = None, search: Optional[str] = None):
    """Lấy danh sách lịch hẹn bệnh nhân cho Admin"""
    appointments = tools.load_appointments()
    if status and status != "all":
        appointments = [a for a in appointments if a.get("status") == status]
    if search:
        s_lower = search.lower()
        appointments = [a for a in appointments if s_lower in a.get("name", "").lower() or s_lower in a.get("phone", "").lower() or s_lower in a.get("id", "").lower()]
    return appointments

@app.patch("/api/appointments/{app_id}/status")
async def update_appointment_status(app_id: str, payload: AppointmentStatusUpdate):
    """Cập nhật trạng thái lịch hẹn (Admin)"""
    appointments = tools.load_appointments()
    found = False
    for a in appointments:
        if a.get("id").upper() == app_id.upper():
            a["status"] = payload.status
            found = True
            break
    if not found:
        raise HTTPException(status_code=404, detail="Không tìm thấy lịch hẹn")
    
    tools.save_appointments(appointments)
    return {"success": True, "message": f"Đã chuyển trạng thái lịch hẹn {app_id} sang '{payload.status}'"}

@app.post("/api/contacts")
async def create_contact(contact: ContactRequest):
    """Tiếp nhận thông tin liên hệ từ khách hàng"""
    contacts_file = os.path.join(tools.DATA_DIR, "contacts.json")
    contacts = []
    if os.path.exists(contacts_file):
        with open(contacts_file, "r", encoding="utf-8") as f:
            contacts = json.load(f)

    new_c = {
        "id": f"CT-{len(contacts) + 1:03d}",
        "name": contact.name,
        "phone": contact.phone,
        "email": contact.email,
        "subject": contact.subject or "Thắc mắc chung",
        "message": contact.message,
        "created_at": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    contacts.insert(0, new_c)

    with open(contacts_file, "w", encoding="utf-8") as f:
        json.dump(contacts, f, ensure_ascii=False, indent=2)

    return {"success": True, "message": "Gửi thông tin liên hệ thành công. Chúng tôi sẽ phản hồi trong 24h!"}

@app.get("/api/contacts")
async def get_contacts():
    """Danh sách tin nhắn liên hệ (Admin)"""
    contacts_file = os.path.join(tools.DATA_DIR, "contacts.json")
    if os.path.exists(contacts_file):
        with open(contacts_file, "r", encoding="utf-8") as f:
            return json.load(f)
    return []

@app.get("/api/stats")
async def get_admin_stats():
    """Thống kê chỉ số cho Admin Dashboard"""
    appointments = tools.load_appointments()
    services = tools.load_services()
    doctors = tools.load_doctors()
    
    today_str = datetime.date.today().strftime("%Y-%m-%d")
    today_apps = [a for a in appointments if a.get("date") == today_str]
    pending_apps = [a for a in appointments if a.get("status") == "Chờ xác nhận"]
    confirmed_apps = [a for a in appointments if a.get("status") == "Đã xác nhận"]

    # Unique patients
    unique_phones = set(a.get("phone") for a in appointments if a.get("phone"))

    return {
        "total_patients": len(unique_phones) or len(appointments),
        "total_appointments": len(appointments),
        "today_appointments": len(today_apps),
        "pending_appointments": len(pending_apps),
        "confirmed_appointments": len(confirmed_apps),
        "total_services": len(services),
        "total_doctors": len(doctors)
    }

@app.get("/api/clinic")
async def get_clinic_info():
    """Thông tin phòng khám"""
    clinic_file = os.path.join(tools.DATA_DIR, "clinic_info.json")
    if os.path.exists(clinic_file):
        with open(clinic_file, "r", encoding="utf-8") as f:
            return json.load(f)
    return {
        "name": "Nha Khoa Quốc Tế Premier Clinic",
        "phone": "0900.123.456",
        "hotline": "1900 6868",
        "address": "123 Đường Nguyễn Văn Cừ, Phường 4, Quận 5, TP. Hồ Chí Minh"
    }

def load_doctors_list():
    return tools.load_doctors()

# Static Files Serving for Web Application
PUBLIC_DIR = os.path.join(BASE_DIR, "public")
os.makedirs(PUBLIC_DIR, exist_ok=True)

app.mount("/static", StaticFiles(directory=PUBLIC_DIR), name="static")

@app.get("/{full_path:path}")
async def serve_frontend(full_path: str):
    """Phục vụ file giao diện web HTML5/CSS3/JS"""
    file_path = os.path.join(PUBLIC_DIR, full_path)
    if full_path and os.path.exists(file_path) and os.path.isfile(file_path):
        return FileResponse(file_path)
    
    # Fallback to index.html for Single Page Application
    index_file = os.path.join(PUBLIC_DIR, "index.html")
    if os.path.exists(index_file):
        return FileResponse(index_file)
    return JSONResponse({"message": "Nha Khoa API Server is running. Please add public/index.html!"})

if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8000))
    print(f"[Server] Dang khoi chay Nha Khoa Premier Web Server tai http://0.0.0.0:{port} ...")
    uvicorn.run("server:app", host="0.0.0.0", port=port)
