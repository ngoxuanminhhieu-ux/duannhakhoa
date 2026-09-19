import json
import os
import re
from dotenv import load_dotenv
import tools

# Tải cấu hình từ file .env
load_dotenv()

class CustomerServicePipeline:
    def __init__(self):
        self.logs = []
        # Tải API Key từ .env (hỗ trợ OPENAI_API_KEY hoặc GEMINI_API_KEY)
        self.api_key = os.getenv("OPENAI_API_KEY", "").strip() or os.getenv("GEMINI_API_KEY", "").strip()
        self.client = None

        # Kiểm tra xem API key có hợp lệ cho OpenAI không
        if self.api_key and self.api_key.startswith("sk-") and not self.api_key.startswith("sk-proj-dinh-gemini"):
            try:
                from openai import OpenAI
                self.client = OpenAI(api_key=self.api_key)
            except Exception:
                self.client = None

    def step1_intent_router(self, query):
        """Step 1: Intent Classification Router cho Nha Khoa"""
        query_lower = query.lower()

        # Gọi LLM nếu có client OpenAI
        if self.client:
            try:
                prompt = (
                    f"Phân tích ý định của câu hỏi khách hàng sau: '{query}'. "
                    f"Trả về JSON duy nhất có dạng {{\"intent\": \"VAL\"}} với VAL thuộc 1 trong các loại: "
                    f"'SERVICE' (Hỏi về dịch vụ nha khoa, quy trình), "
                    f"'PRICE' (Hỏi giá tiền, chi phí), "
                    f"'DOCTOR' (Hỏi bác sĩ, chuyên gia, lịch khám), "
                    f"'BOOKING' (Muốn đặt lịch khám, đăng ký hẹn), hoặc "
                    f"'POLICY' (Hỏi chính sách bảo hành, địa chỉ, giờ làm việc, trả góp 0%, vô trùng)."
                )
                res = self.client.chat.completions.create(
                    model="gpt-4o-mini",
                    messages=[{"role": "user", "content": prompt}],
                    response_format={"type": "json_object"}
                )
                intent_data = json.loads(res.choices[0].message.content)
                intent = intent_data.get("intent", "SERVICE")
                self.logs.append(f"[Step 1 - Intent Router]: Detected intent = {intent}")
                return intent
            except Exception as e:
                self.logs.append(f"[Step 1 - Intent Router]: LLM fallback to Rule Engine ({str(e)})")

        # Local Rule-based Engine
        if any(term in query_lower for term in ["đặt lịch", "hẹn khám", "đăng ký khám", "tư vấn trực tiếp", "đặt chỗ", "muốn khám"]):
            intent = "BOOKING"
        elif any(term in query_lower for term in ["bao nhiêu", "giá", "chi phí", "bảng giá", "tổng tiền", "nhiêu tiền", "trả góp"]):
            intent = "PRICE"
        elif any(term in query_lower for term in ["bác sĩ", "chuyên gia", "bác sỹ", "học vị", "kinh nghiệm", "ai khám"]):
            intent = "DOCTOR"
        elif any(term in query_lower for term in ["địa chỉ", "chính sách", "bảo hành", "giờ làm", "vô trùng", "mấy giờ", "ở đâu", "mở cửa"]):
            intent = "POLICY"
        else:
            intent = "SERVICE"

        self.logs.append(f"[Step 1 - Intent Router]: Detected intent = {intent}")
        return intent

    def step2_execute_tool(self, query, intent):
        """Step 2: Tool Execution & RAG Data Retrieval Step"""
        # Gọi LLM với Tool Call nếu có OpenAI client
        if self.client:
            try:
                messages = [
                    {
                        "role": "system", 
                        "content": (
                            "Bạn là Trợ lý AI của Nha Khoa Quốc Tế Premier Clinic. "
                            "Hãy gọi đúng Tool tra cứu CSDL hoặc Đặt lịch hẹn."
                        )
                    },
                    {"role": "user", "content": query}
                ]
                res = self.client.chat.completions.create(
                    model="gpt-4o-mini",
                    messages=messages,
                    tools=tools.TOOLS_SCHEMA
                )
                msg = res.choices[0].message
                if msg.tool_calls:
                    tool_call = msg.tool_calls[0]
                    fn_name = tool_call.function.name
                    fn_args = json.loads(tool_call.function.arguments)
                    self.logs.append(f"[Step 2 - Tool Call]: Executing {fn_name} với tham số {fn_args}")

                    if fn_name == "search_services":
                        data = tools.search_services(**fn_args)
                    elif fn_name == "search_doctors":
                        data = tools.search_doctors(**fn_args)
                    elif fn_name == "book_appointment_tool":
                        data = tools.book_appointment_tool(**fn_args)
                    elif fn_name == "search_policies":
                        data = tools.search_policies(**fn_args)
                    else:
                        data = "{}"
                    return data
            except Exception as e:
                self.logs.append(f"[Step 2 - Tool Execution]: LLM Tool Call fallback ({str(e)})")

        # Local Direct Tool Execution
        query_lower = query.lower()

        if intent == "BOOKING":
            # Kiểm tra xem có SĐT và Tên không
            phone_match = re.search(r'\b(0[3|5|7|8|9][0-9]{8})\b', query)
            name_match = re.search(r'(tôi là|tên tôi là|tên là)\s+([A-ZÀ-Ỹa-zà-ỹ\s]+)', query, re.IGNORECASE)

            if phone_match:
                phone = phone_match.group(1)
                name = name_match.group(2).strip() if name_match else "Khách Hàng AI"
                self.logs.append(f"[Step 2 - Tool Call]: Executing book_appointment_tool cho {name} - {phone}")
                return tools.book_appointment_tool(name=name, phone=phone, notes=query)
            else:
                self.logs.append("[Step 2 - Tool Call]: Booking intent detected - Yêu cầu thông tin SĐT và Tên")
                return json.dumps({
                    "need_info": True,
                    "message": "Quý khách vui lòng cung cấp **Họ tên** và **Số điện thoại** để Nha Khoa AI Assistant hỗ trợ ghi nhận lịch hẹn khám ngay lập tức!"
                }, ensure_ascii=False)

        elif intent == "DOCTOR":
            self.logs.append(f"[Step 2 - Tool Call]: Executing search_doctors với query '{query}'")
            return tools.search_doctors(name=query, specialty=query)

        elif intent == "POLICY":
            self.logs.append(f"[Step 2 - Tool Call]: Executing search_policies với query '{query}'")
            return tools.search_policies(query=query)

        else: # SERVICE & PRICE intent
            keyword = ""
            for kw in ["niềng răng", "implant", "trám răng", "nhổ răng", "răng khôn", "tấy trắng", "bọc sứ", "răng sứ", "lấy cao răng", "tủy", "trẻ em"]:
                if kw in query_lower:
                    keyword = kw
                    break
            self.logs.append(f"[Step 2 - Tool Call]: Executing search_services với keyword '{keyword}'")
            return tools.search_services(keyword=keyword)

    def step3_validate_and_format(self, query, raw_data):
        """Step 3: Validation & Formatting Step (Zero-Hallucination & Medical Disclaimer)"""
        if self.client:
            try:
                system_instruction = (
                    "Bạn là 'Nha Khoa AI Assistant' - Trợ lý AI chuyên nghiệp của Nha Khoa Quốc Tế Premier Clinic.\n"
                    "QUY TẮC PHẢN HỒI:\n"
                    "1. Trả lời bằng tiếng Việt lịch sự, ân cần, ngắn gọn và dễ hiểu.\n"
                    "2. Không đưa ra chẩn đoán y khoa chắc chắn. Khi người dùng tả triệu chứng đau nhức/sưng tấy, hãy khuyến nghị họ đến gặp bác sĩ trực tiếp.\n"
                    "3. Dựa chính xác vào CSDL được cung cấp bên dưới, không bịa đặt bảng giá hay chính sách ngoài CSDL.\n"
                    "4. Luôn ghi rõ mức giá mang tính chất tham khảo, chi phí chính xác cần được Bác sĩ kiểm tra trực tiếp.\n"
                    "5. Hướng dẫn khách hàng đặt lịch khám nhanh."
                )
                prompt = f"Dữ liệu CSDL nha khoa:\n{raw_data}\n\nCâu hỏi khách hàng: '{query}'"
                res = self.client.chat.completions.create(
                    model="gpt-4o-mini",
                    messages=[
                        {"role": "system", "content": system_instruction},
                        {"role": "user", "content": prompt}
                    ]
                )
                self.logs.append("[Step 3 - Validation & Format]: Generated final answer via LLM.")
                return res.choices[0].message.content
            except Exception as e:
                self.logs.append(f"[Step 3 - Validation & Format]: LLM format fallback ({str(e)})")

        # Local Formatter Engine
        try:
            data = json.loads(raw_data)
        except Exception:
            data = raw_data

        if isinstance(data, dict):
            if data.get("need_info"):
                return f" 📋 {data['message']}\n\n*Ví dụ: 'Tôi tên Nguyễn Văn A, SĐT 0912345678 muốn đăng ký niềng răng'*."
            elif data.get("success"):
                return (
                    f" 🎉 **ĐẶT LỊCH KHÁM THÀNH CÔNG!**\n\n"
                    f"• **Mã lịch hẹn:** `{data['appointment_id']}`\n"
                    f"• **Khách hàng:** {data['name']}\n"
                    f"• **Số điện thoại:** {data['phone']}\n"
                    f"• **Dịch vụ:** {data['service_name']}\n"
                    f"• **Bác sĩ phụ trách:** {data['doctor_name']}\n"
                    f"• **Thời gian hẹn:** {data['time']} ngày {data['date']}\n\n"
                    f" 📞 Đội ngũ lễ tân của Nha Khoa Premier sẽ gọi điện xác nhận trong 15 phút. Cảm ơn quý khách!"
                )
            elif "retrieved_policies" in data:
                answer = " 🏥 **THÔNG TIN & CHÍNH SÁCH PHÒNG KHÁM PREMIER CLINIC:**\n\n"
                policies = data["retrieved_policies"]
                if isinstance(policies, list):
                    for pol in policies[:5]:
                        answer += f"• {pol}\n"
                else:
                    answer += f"• {policies}\n"
                answer += "\n Quý khách có thể nhấn nút **Đặt lịch ngay** để trải nghiệm dịch vụ nha khoa chuẩn quốc tế!"
                return answer
            elif "error" in data:
                return f" ⚠️ {data['error']}"

        if isinstance(data, list):
            if not data:
                return " 🦷 Phòng khám Nha Khoa Premier có đầy đủ các dịch vụ: Khám răng tổng quát, Lấy cao răng, Trám răng, Nhổ răng khôn Piezotome, Điều trị tủy, Niềng răng mắc cài/Invisalign, Cấy ghép Implant, Bọc răng sứ và Tẩy trắng răng Laser. Bạn cần tư vấn dịch vụ nào ạ?"

            # Check if doctor list or service list
            if data and "experience" in data[0]:
                answer = f" 👨‍⚕️ **ĐỘI NGŨ BÁC SĨ CHUYÊN GIA NHA KHOA PREMIER ({len(data)} bác sĩ):**\n\n"
                for d in data:
                    answer += f"🔹 **{d['name']}** ({d['title']})\n"
                    answer += f"   • **Chuyên khoa:** {d['specialty']}\n"
                    answer += f"   • **Kinh nghiệm:** {d['experience']}\n"
                    answer += f"   • **Lịch làm việc:** {d.get('schedule', 'Thứ 2 - Thứ 7')}\n\n"
                answer += "Quý khách có thể đặt lịch khám trực tiếp với bác sĩ mình mong muốn!"
                return answer

            else:
                answer = f" 🦷 **DANH SÁCH DỊCH VỤ NHA KHOA PREMIER ({len(data)} dịch vụ phù hợp):**\n\n"
                for s in data:
                    answer += f"🔹 **{s['name']}** (Mã: `{s['id']}`)\n"
                    answer += f"   • **Danh mục:** {s['category']}\n"
                    answer += f"   • **Chi phí tham khảo:** **{s['price_range']}**\n"
                    answer += f"   • **Bảo hành / Tái khám:** {s['warranty']}\n"
                    answer += f"   • **Mô tả:** {s['description']}\n\n"
                answer += " 💡 *Lưu ý: Mức giá trên mang tính chất tham khảo. Chi phí chính xác sẽ được Bác sĩ báo cụ thể sau khi kiểm tra phim X-quang trực tiếp miễn phí!*"
                return answer

        self.logs.append("[Step 3 - Validation & Format]: Generated final answer successfully.")
        return str(raw_data)

    def run_pipeline(self, query):
        """Điều phối luồng Pipeline 3 bước cho Nha Khoa Chatbot"""
        self.logs = []
        intent = self.step1_intent_router(query)
        raw_data = self.step2_execute_tool(query, intent)
        final_answer = self.step3_validate_and_format(query, raw_data)
        return final_answer, self.logs
