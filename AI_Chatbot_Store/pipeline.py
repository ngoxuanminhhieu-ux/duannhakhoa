import json
import os
import re

from dotenv import load_dotenv

try:
    from google import genai
except ImportError:
    try:
        import google.generativeai as genai
    except ImportError:
        genai = None

import tools


# Tải cấu hình từ file .env
load_dotenv()


class CustomerServicePipeline:
    def __init__(self):
        self.logs = []

        # Lấy Gemini API Key từ file .env
        self.api_key = os.getenv("GEMINI_API_KEY", "").strip() or os.getenv("OPENAI_API_KEY", "").strip()

        self.client = None

        # Khởi tạo Gemini nếu có API key và thư viện
        if self.api_key and genai is not None:
            try:
                if hasattr(genai, 'Client'):
                    self.client = genai.Client(api_key=self.api_key)
                elif hasattr(genai, 'configure'):
                    genai.configure(api_key=self.api_key)
                    self.client = genai
            except Exception as e:
                self.logs.append(
                    f"[Gemini]: Không thể khởi tạo Gemini Client: {str(e)}"
                )
                self.client = None

    # =========================================================
    # STEP 1: INTENT ROUTER
    # =========================================================
    def step1_intent_router(self, query):
        """Step 1: Phân loại ý định (Intent) của khách hàng."""

        query_lower = query.lower().strip()

        # Quick Greeting Filter before model or complex rules
        if any(w in query_lower for w in ["chào", "xin chào", "hi", "hello", "alo", "bạn ơi", "cho tôi hỏi"]) and not any(k in query_lower for k in ["giá", "chi phí", "bao nhiêu", "bác sĩ", "đặt lịch", "địa chỉ", "niềng", "implant", "sứ", "nhổ"]):
            self.logs.append("[Step 1 - Rule Engine]: Detected intent = GREETING")
            return "GREETING"

        # Call Gemini if client is active
        if self.client:
            try:
                prompt = f"""
Phân tích ý định của khách hàng trong câu hỏi dưới đây.

Câu hỏi:
"{query}"

Chỉ được chọn MỘT trong 6 intent sau:

GREETING:
Lời chào hỏi xã giao, làm quen hoặc yêu cầu bắt đầu tư vấn.

SERVICE:
Hỏi về dịch vụ nha khoa hoặc quy trình điều trị.

PRICE:
Hỏi về giá tiền, chi phí, bảng giá hoặc trả góp.

DOCTOR:
Hỏi về bác sĩ, chuyên gia, chuyên khoa, kinh nghiệm hoặc lịch khám.

BOOKING:
Muốn đặt lịch khám hoặc đăng ký khám.

POLICY:
Hỏi về chính sách, bảo hành, địa chỉ, giờ làm việc,
vô trùng hoặc các thông tin chung của phòng khám.

Chỉ trả về JSON đúng định dạng:

{{"intent": "SERVICE"}}

Không thêm giải thích.
"""

                # Use standard Gemini model
                model_name = "gemini-2.5-flash"
                if hasattr(self.client, 'models'):
                    response = self.client.models.generate_content(
                        model=model_name,
                        contents=prompt
                    )
                else:
                    model = self.client.GenerativeModel(model_name)
                    response = model.generate_content(prompt)

                text = response.text.strip()

                text = re.sub(r"```json\s*", "", text)
                text = re.sub(r"```\s*", "", text)

                intent_data = json.loads(text)

                intent = intent_data.get(
                    "intent",
                    "SERVICE"
                ).upper()

                valid_intents = {
                    "GREETING",
                    "SERVICE",
                    "PRICE",
                    "DOCTOR",
                    "BOOKING",
                    "POLICY"
                }

                if intent not in valid_intents:
                    intent = "SERVICE"

                self.logs.append(
                    f"[Step 1 - Gemini Intent Router]: "
                    f"Detected intent = {intent}"
                )

                return intent

            except Exception as e:
                self.logs.append(
                    f"[Step 1 - Gemini]: Lỗi, "
                    f"chuyển sang Rule Engine ({str(e)})"
                )

        # =====================================================
        # FALLBACK: RULE ENGINE
        # =====================================================

        if any(
            term in query_lower
            for term in [
                "đặt lịch",
                "hẹn khám",
                "đăng ký khám",
                "tư vấn trực tiếp",
                "đặt chỗ",
                "muốn khám"
            ]
        ):
            intent = "BOOKING"

        elif any(
            term in query_lower
            for term in [
                "bao nhiêu",
                "giá",
                "chi phí",
                "bảng giá",
                "tổng tiền",
                "nhiêu tiền",
                "trả góp"
            ]
        ):
            intent = "PRICE"

        elif any(
            term in query_lower
            for term in [
                "bác sĩ",
                "chuyên gia",
                "bác sỹ",
                "học vị",
                "kinh nghiệm",
                "ai khám"
            ]
        ):
            intent = "DOCTOR"

        elif any(
            term in query_lower
            for term in [
                "địa chỉ",
                "chính sách",
                "bảo hành",
                "giờ làm",
                "vô trùng",
                "mấy giờ",
                "ở đâu",
                "mở cửa",
                "liên hệ",
                "hotline"
            ]
        ):
            intent = "POLICY"

        elif any(w in query_lower for w in ["chào", "hi", "hello", "xin chào", "tư vấn"]):
            intent = "GREETING"

        else:
            intent = "SERVICE"

        self.logs.append(
            f"[Step 1 - Rule Engine]: Detected intent = {intent}"
        )

        return intent

    # =========================================================
    # STEP 2: TOOL / RAG
    # =========================================================
    def step2_execute_tool(self, query, intent):
        """Step 2: Python gọi trực tiếp các Tool / RAG."""

        query_lower = query.lower()

        # =====================================================
        # GREETING
        # =====================================================
        if intent == "GREETING":
            self.logs.append("[Step 2 - Tool]: Greeting intent detected")
            return json.dumps({
                "type": "greeting",
                "message": (
                    "👋 **Xin chào! Tôi là Trợ Lý AI của Nha Khoa Quốc Tế Premier Clinic.**\n\n"
                    "Tôi có thể hỗ trợ bạn:\n"
                    "• 🦷 **Tư vấn dịch vụ nha khoa** (Niềng răng, Implant, Răng sứ, Tẩy trắng, Nhổ răng khôn...)\n"
                    "• 💰 **Bảng giá chi phí điều trị**\n"
                    "• 👨‍⚕️ **Thông tin Bác sĩ chuyên khoa**\n"
                    "• 📋 **Chính sách bảo hành & Trả góp 0%**\n"
                    "• 📅 **Đăng ký Đặt lịch khám miễn phí**\n\n"
                    "Bạn cần tư vấn dịch vụ nào ạ?"
                )
            }, ensure_ascii=False)

        # =====================================================
        # BOOKING
        # =====================================================
        if intent == "BOOKING":

            phone_match = re.search(
                r"\b(0[35789][0-9]{8})\b",
                query
            )

            name_match = re.search(
                r"(?:tên(?:\s+tôi)?(?:\s+là)?|tôi\s+là)\s+"
                r"([A-ZÀ-Ỹa-zà-ỹĐđ\s]+?)"
                r"(?=\s*,|\s+số\s+điện\s+thoại|\s+SĐT|"
                r"\s+ngày|\s+lúc|$)",
                query,
                re.IGNORECASE
            )

            date_match = re.search(
                r"\b(0?[1-9]|[12][0-9]|3[01])/"
                r"(0?[1-9]|1[0-2])/"
                r"(20[0-9]{2})\b",
                query
            )

            time_match = re.search(
                r"\b([01]?[0-9]|2[0-3]):([0-5][0-9])\b",
                query
            )

            if phone_match:
                phone = phone_match.group(1)

                name = (
                    name_match.group(1).strip()
                    if name_match
                    else "Khách Hàng AI"
                )

                appointment_date = ""

                if date_match:
                    day = date_match.group(1).zfill(2)
                    month = date_match.group(2).zfill(2)
                    year = date_match.group(3)

                    appointment_date = (
                        f"{year}-{month}-{day}"
                    )

                appointment_time = ""

                if time_match:
                    appointment_time = time_match.group(0)

                self.logs.append(
                    f"[Step 2 - Tool]: book_appointment_tool "
                    f"cho {name} - {phone} - "
                    f"{appointment_date} {appointment_time}"
                )

                return tools.book_appointment_tool(
                    name=name,
                    phone=phone,
                    date=appointment_date,
                    time=appointment_time,
                    notes=query
                )

            else:
                self.logs.append(
                    "[Step 2 - Tool]: Booking intent detected - "
                    "Yêu cầu thông tin SĐT và Tên"
                )

                return json.dumps(
                    {
                        "status": "need_info",
                        "message": (
                            "Vui lòng cung cấp Họ tên và Số điện thoại "
                            "để em hỗ trợ đặt lịch khám ngay."
                        )
                    },
                    ensure_ascii=False
                )

        # =====================================================
        # CÁC TOOL KHÁC (DOCTOR, POLICY, SERVICE, PRICE)
        # =====================================================
        if intent == "DOCTOR":
            self.logs.append(f"[Step 2 - Tool]: search_doctors với query = '{query}'")
            return tools.search_doctors(specialty=query, name=query)

        elif intent == "POLICY":
            self.logs.append(f"[Step 2 - Tool]: search_policies với query = '{query}'")
            return tools.search_policies(query=query)

        else: # SERVICE & PRICE intent
            keyword = ""
            for kw in ["nhổ răng khôn", "niềng răng", "implant", "trám răng", "nhổ răng", "tẩy trắng", "bọc sứ", "răng sứ", "cạo vôi", "cao răng", "tủy", "trẻ em", "khám"]:
                if kw in query_lower:
                    keyword = kw
                    break
            self.logs.append(f"[Step 2 - Tool]: search_services với keyword = '{keyword}'")
            return tools.search_services(keyword=keyword)

    # =========================================================
    # STEP 3: GEMINI FORMAT / VALIDATION
    # =========================================================
    def step3_validate_and_format(self, query, raw_data):
        """
        Step 3:
        Gemini tạo câu trả lời cuối cùng dựa trên dữ liệu thật
        lấy từ tools.py.
        """

        if self.client:
            try:
                system_instruction = """
Bạn là "Nha Khoa AI Assistant" - Trợ lý AI của Nha Khoa Quốc Tế Premier Clinic.

QUY TẮC PHẢN HỒI:
1. Trả lời bằng tiếng Việt lịch sự, ân cần, ngắn gọn và dễ hiểu.
2. Không đưa ra chẩn đoán y khoa chắc chắn. Nếu khách hàng mô tả đau, sưng, chảy máu hoặc triệu chứng bất thường, khuyến nghị khách hàng đến gặp bác sĩ trực tiếp.
3. CHỈ sử dụng thông tin có trong dữ liệu được cung cấp.
4. Không tự bịa giá, bác sĩ, chính sách, lịch làm việc hoặc thông tin phòng khám.
5. Nếu dữ liệu không có thông tin cần thiết, hãy nói rõ rằng chưa có dữ liệu thay vì tự suy đoán.
6. Giá dịch vụ chỉ mang tính tham khảo. Chi phí chính xác cần được bác sĩ kiểm tra trực tiếp.
7. Nếu phù hợp, hướng dẫn khách hàng đặt lịch.
"""

                prompt = f"""
{system_instruction}

DỮ LIỆU TỪ CSDL / TOOL:
{raw_data}

CÂU HỎI KHÁCH HÀNG:
"{query}"

Hãy tạo câu trả lời cuối cùng cho khách hàng.
"""

                model_name = "gemini-2.5-flash"
                if hasattr(self.client, 'models'):
                    response = self.client.models.generate_content(
                        model=model_name,
                        contents=prompt
                    )
                else:
                    model = self.client.GenerativeModel(model_name)
                    response = model.generate_content(prompt)

                answer = response.text.strip()

                self.logs.append(
                    "[Step 3 - Gemini]: Generated final answer."
                )

                return answer

            except Exception as e:
                self.logs.append(
                    f"[Step 3 - Gemini]: Lỗi, "
                    f"chuyển sang Local Formatter ({str(e)})"
                )

        # =====================================================
        # FALLBACK LOCAL FORMATTER
        # =====================================================

        try:
            data = json.loads(raw_data)
        except Exception:
            data = raw_data

        if isinstance(data, dict):
            if data.get("type") == "greeting" or data.get("message") and "Xin chào" in data.get("message", ""):
                return data["message"]

            elif data.get("need_info"):
                return (
                    f"📋 {data['message']}\n\n"
                    "*Ví dụ: 'Tôi tên Nguyễn Văn A, SĐT 0912345678 muốn đăng ký niềng răng'*"
                )

            elif data.get("success"):
                return (
                    "🎉 **ĐẶT LỊCH KHÁM THÀNH CÔNG!**\n\n"
                    f"• **Mã lịch hẹn:** `{data['appointment_id']}`\n"
                    f"• **Khách hàng:** {data['name']}\n"
                    f"• **Số điện thoại:** {data['phone']}\n"
                    f"• **Dịch vụ:** {data['service_name']}\n"
                    f"• **Bác sĩ phụ trách:** {data['doctor_name']}\n"
                    f"• **Thời gian hẹn:** {data['time']} ngày {data['date']}\n\n"
                    "📞 Đội ngũ lễ tân của Nha Khoa Premier sẽ gọi điện xác nhận trong ít phút. Cảm ơn quý khách!"
                )

            elif "retrieved_policies" in data:
                answer = "🏥 **THÔNG TIN & CHÍNH SÁCH PHÒNG KHÁM PREMIER CLINIC:**\n\n"
                policies = data["retrieved_policies"]
                if isinstance(policies, list):
                    for pol in policies:
                        answer += f"• {pol}\n"
                else:
                    answer += f"• {policies}\n"
                return answer

            elif "error" in data:
                return f"⚠️ {data['error']}"

        if isinstance(data, list):
            if not data:
                return (
                    "🦷 Phòng khám Nha Khoa Premier có các dịch vụ như khám răng tổng quát, lấy cao răng, trám răng, "
                    "nhổ răng khôn, điều trị tủy, niềng răng, cấy ghép Implant, bọc răng sứ và tẩy trắng răng. "
                    "Bạn cần tư vấn dịch vụ nào ạ?"
                )

            if data and "experience" in data[0]:
                answer = f"👨‍⚕️ **ĐỘI NGŨ BÁC SĨ CHUYÊN GIA NHA KHOA PREMIER ({len(data)} bác sĩ):**\n\n"
                for d in data:
                    answer += (
                        f"🔹 **{d['name']}** ({d['title']})\n"
                        f"   • **Chuyên khoa:** {d['specialty']}\n"
                        f"   • **Kinh nghiệm:** {d['experience']}\n"
                        f"   • **Lịch làm việc:** {d.get('schedule', 'Thứ 2 - Thứ 7')}\n\n"
                    )
                answer += "Quý khách có thể đặt lịch khám trực tiếp với bác sĩ mình mong muốn!"
                return answer
            else:
                answer = f"🦷 **DANH SÁCH DỊCH VỤ NHA KHOA PREMIER ({len(data)} dịch vụ phù hợp):**\n\n"
                for s in data:
                    answer += (
                        f"🔹 **{s['name']}** (Mã: `{s['id']}`)\n"
                        f"   • **Danh mục:** {s['category']}\n"
                        f"   • **Chi phí tham khảo:** **{s['price_range']}**\n"
                        f"   • **Bảo hành / Tái khám:** {s['warranty']}\n"
                        f"   • **Mô tả:** {s['description']}\n\n"
                    )
                answer += "💡 *Lưu ý: Mức giá trên mang tính chất tham khảo. Chi phí chính xác sẽ được Bác sĩ báo cụ thể sau khi kiểm tra trực tiếp.*"
                return answer

        self.logs.append("[Step 3 - Local Formatter]: Generated final answer.")
        return str(raw_data)

    # =========================================================
    # RUN PIPELINE
    # =========================================================
    def run_pipeline(self, query):
        """Điều phối Pipeline 3 bước cho Nha Khoa Chatbot."""

        self.logs = []

        # STEP 1
        intent = self.step1_intent_router(query)

        # STEP 2
        raw_data = self.step2_execute_tool(
            query,
            intent
        )

        # STEP 3
        final_answer = self.step3_validate_and_format(
            query,
            raw_data
        )

        return final_answer, self.logs

