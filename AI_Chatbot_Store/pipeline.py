import json
import os
import re
import datetime

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
        greeting_words = ["chào", "chao", "xin chào", "xin chao", "hello", "alo", "bạn ơi", "ban oi"]
        is_greeting = any(w in query_lower for w in greeting_words) or bool(re.search(r"\bhi\b", query_lower, re.IGNORECASE))
        has_dental_or_service = any(k in query_lower for k in ["giá", "gia", "chi phí", "chi phi", "bao nhiêu", "bao nhieu", "tiền", "tien", "bác sĩ", "bac si", "đặt lịch", "dat lich", "địa chỉ", "dia chi", "niềng", "nieng", "implant", "sứ", "su", "nhổ", "nho", "trám", "tram", "tủy", "tuy"])
        if is_greeting and not has_dental_or_service:
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
GENERAL_CHAT:
Các câu hỏi ngoài lề, kiến thức đời sống, khoa học, ngày tháng, thời gian, lịch sử, toán học, văn hóa hoặc trò chuyện tự do không liên quan đến phòng khám nha khoa.

Chỉ trả về JSON đúng định dạng:

{{"intent": "SERVICE"}}

Không thêm giải thích.
"""

                # Use fast and high-quota Gemini model
                model_name = "gemini-3.6-flash"
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
                    "GENERAL_CHAT"
                ).upper()

                valid_intents = {
                    "GREETING",
                    "SERVICE",
                    "PRICE",
                    "DOCTOR",
                    "BOOKING",
                    "POLICY",
                    "GENERAL_CHAT"
                }

                if intent not in valid_intents:
                    intent = "GENERAL_CHAT"

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
        # FALLBACK: RULE ENGINE (HỖ TRỢ CẢ CÓ DẤU & KHÔNG DẤU)
        # =====================================================
        import unicodedata
        query_plain = "".join(c for c in unicodedata.normalize('NFKD', query_lower) if not unicodedata.combining(c)).replace('đ', 'd').replace('Đ', 'D')
        query_search = f"{query_lower} {query_plain}"

        if any(
            term in query_search
            for term in [
                "đặt lịch", "dat lich",
                "hẹn khám", "hen kham",
                "đăng ký khám", "dang ky kham",
                "tư vấn trực tiếp", "tu van truc tiep",
                "đặt chỗ", "dat cho",
                "muốn khám", "muon kham"
            ]
        ):
            intent = "BOOKING"

        elif any(
            term in query_search
            for term in [
                "bác sĩ", "bac si",
                "chuyên gia", "chuyen gia",
                "bác sỹ", "bac sy",
                "học vị", "hoc vi",
                "kinh nghiệm", "kinh nghiem",
                "ai khám", "ai kham"
            ]
        ):
            intent = "DOCTOR"

        elif any(
            term in query_search
            for term in [
                "bao nhiêu", "bao nhieu",
                "giá", "gia",
                "chi phí", "chi phi",
                "bảng giá", "bang gia",
                "tổng tiền", "tong tien",
                "nhiêu tiền", "nhieu tien",
                "trả góp", "tra gop"
            ]
        ):
            intent = "PRICE"

        elif any(
            term in query_search
            for term in [
                "địa chỉ", "dia chi",
                "chính sách", "chinh sach",
                "bảo hành", "bao hanh",
                "giờ làm", "gio lam",
                "vô trùng", "vo trung",
                "mấy giờ", "may gio",
                "ở đâu", "o dau",
                "mở cửa", "mo cua",
                "liên hệ", "lien he",
                "hotline"
            ]
        ):
            intent = "POLICY"

        elif any(w in query_search for w in ["ictu", "đại học thái nguyên", "dai hoc thai nguyen", "trường nào", "truong nao", "cntt", "tnut", "tump", "tueba"]):
            intent = "UNIVERSITY"

        elif any(w in query_search for w in ["bạn là ai", "ban la ai", "mày là ai", "may la ai", "tên gì", "ten gi", "giới thiệu", "gioi thieu", "chatbot là gì", "chatbot la gi", "ai tạo ra", "ai tao ra", "em là ai", "em la ai"]):
            intent = "IDENTITY"

        elif any(w in query_search for w in ["thời tiết", "thoi tiet", "mưa", "mua", "nắng", "nang", "nhiệt độ", "nhiet do", "hôm nay thế nào", "hom nay the nao"]):
            intent = "WEATHER"

        elif any(w in query_search for w in ["cảm ơn", "cam on", "thanks", "thank you", "tạm biệt", "tam biet", "bye"]):
            intent = "GRATITUDE"

        elif any(re.search(rf"\b{re.escape(w)}\b", query_search, re.IGNORECASE) for w in ["chào", "chao", "hi", "hello", "xin chào", "xin chao", "alo", "hey"]) and not any(k in query_search for k in ["giá", "gia", "tiền", "tien", "chi phí", "chi phi", "nhổ", "nho", "trám", "tram", "sứ", "su", "niềng", "nieng", "implant", "bác sĩ", "bac si"]):
            intent = "GREETING"

        else:
            dental_keywords = [
                "răng", "rang", "nướu", "nuou", "lợi", "loi", "tủy", "tuy", "implant", "niềng", "nieng", "mắc cài", "mac cai", "invisalign",
                "nhổ", "nho", "trám", "tram", "hàn", "han", "sứ", "su", "veneer", "tẩy trắng", "tay trang", "cạo vôi", "cao voi", "cao răng", "cao rang",
                "khám", "kham", "bác sĩ", "bac si", "bác sỹ", "bac sy", "khôn", "khon", "áp xe", "ap xe", "ê buốt", "e buot", "lung lay", "sâu", "sau",
                "chỉnh nha", "chinh nha", "nội nha", "noi nha", "dịch vụ", "dich vu", "chi phí", "chi phi", "giá", "gia", "bảng giá", "bang gia", "tiền", "tien"
            ]
            if any(k in query_search for k in dental_keywords):
                intent = "SERVICE"
            else:
                intent = "GENERAL_CHAT"

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
        # GENERAL CHAT (MỌI CÂU HỎI NGOÀI LỀ NHƯ CHATGPT / GEMINI)
        # =====================================================
        if intent == "GENERAL_CHAT":
            self.logs.append(f"[Step 2 - General Chat LLM]: Tra loi cau hoi '{query}' bang Gemini LLM")
            if self.client:
                for m in ["gemini-3.6-flash", "gemini-3.5-flash"]:
                    try:
                        prompt = (
                            "Ban la Tro ly AI Thong thai cua Nha Khoa Quoc Te Premier Clinic. "
                            "Hay tra loi cau hoi sau cua nguoi dung mot cach chinh xac, thong thai, tu nhien, huu ich va day du (nhu ChatGPT / Gemini):\n\n"
                            f"Cau hoi cua nguoi dung: \"{query}\"\n\n"
                            "Yeu cau: Tra loi truc tiep bang tieng Viet chuan. Neu hoi ve nam hien tai, hay tra loi chinh xac nam 2026."
                        )
                        if hasattr(self.client, 'models'):
                            resp = self.client.models.generate_content(model=m, contents=prompt)
                        else:
                            model = self.client.GenerativeModel(m)
                            resp = model.generate_content(prompt)
                        if resp and resp.text:
                            return json.dumps({"type": "direct_reply", "message": resp.text.strip()}, ensure_ascii=False)
                    except Exception as e:
                        continue

            # Local fallback neu khong co mang hoac loi LLM
            if any(w in query_lower for w in ["năm nay", "năm bao", "năm nào", "hôm nay là"]):
                current_year = datetime.datetime.now().year
                return json.dumps({
                    "type": "direct_reply",
                    "message": f"Dạ năm nay là năm **{current_year}** ạ! 😊\n\nChúc bạn một năm mới an lành, nhiều niềm vui và luôn tự tin với nụ cười của mình! Bạn cần em hỗ trợ thêm thông tin gì không ạ?"
                }, ensure_ascii=False)

            return json.dumps({
                "type": "direct_reply",
                "message": (
                    f"Dạ về câu hỏi *\"{query}\"*, tôi là Trợ lý AI của Nha Khoa Quốc Tế Premier. "
                    "Tôi luôn sẵn lòng giải đáp mọi thắc mắc đời sống cũng như tư vấn chuyên sâu về sức khỏe răng miệng, thẩm mỹ nụ cười và đặt lịch khám miễn phí cùng Bác sĩ Chuyên khoa!"
                )
            }, ensure_ascii=False)

        # =====================================================
        # IDENTITY & UNIVERSITY & OUTSIDE TOPICS
        # =====================================================
        if intent == "UNIVERSITY":
            self.logs.append("[Step 2 - Tool]: University / ICTU intent detected")
            return json.dumps({
                "type": "direct_reply",
                "message": (
                    "🎓 **ICTU là Trường Đại học Công nghệ Thông tin & Truyền thông** (thuộc Đại học Thái Nguyên).\n\n"
                    "Đây là trường đại học công lập trọng điểm hàng đầu tại khu vực Trung du và Miền núi phía Bắc về đào tạo Công nghệ Thông tin, Kỹ thuật Phần mềm và Trí tuệ Nhân tạo.\n\n"
                    "📌 Dự án **Hệ Thống Website & AI Chatbot Nha Khoa Quốc Tế Premier** là đề án ứng dụng thực tiễn được nghiên cứu và phát triển bởi sinh viên ICTU, tích hợp công nghệ AI tư vấn 24/7, RAG tri thức y khoa và sàng lọc nha khoa số!"
                )
            }, ensure_ascii=False)

        if intent == "IDENTITY":
            self.logs.append("[Step 2 - Tool]: Identity intent detected")
            return json.dumps({
                "type": "direct_reply",
                "message": (
                    "👋 Xin chào bạn! Tôi là **Chuyên gia Tư vấn AI Toàn năng của Nha Khoa Quốc Tế Premier Dental Clinic**.\n\n"
                    "Tôi được phát triển bởi nhóm sinh viên **Trường Đại học Công nghệ Thông tin & Truyền thông (ICTU) - Đại học Thái Nguyên**, ứng dụng mô hình RAG kết hợp Tri thức Y khoa Nha khoa chuyên sâu.\n\n"
                    "🌟 **Tôi có thể hỗ trợ bạn:**\n"
                    "• 🩺 Tư vấn chẩn đoán ban đầu mọi triệu chứng răng miệng (đau nhức, sâu răng, ê buốt, viêm nướu...)\n"
                    "• 🦷 Hướng dẫn chi tiết các dịch vụ: Trồng răng Implant, Niềng răng Chỉnh nha, Bọc răng sứ, Nhổ răng khôn Piezotome, Lấy tủy vi phẫu...\n"
                    "• 💰 Tra cứu Bảng giá niêm yết chính thức & Chính sách Trả góp 0% lãi suất\n"
                    "• 👨‍⚕️ Cung cấp hồ sơ đội ngũ Bác sĩ chuyên khoa\n"
                    "• 📅 Hỗ trợ đặt lịch khám ưu tiên miễn phí 100% chụp X-quang 3D CT ConeBeam.\n\n"
                    "Bạn cần tôi giải đáp thắc mắc gì hôm nay?"
                )
            }, ensure_ascii=False)

        if intent == "WEATHER":
            self.logs.append("[Step 2 - Tool]: Weather intent detected")
            return json.dumps({
                "type": "direct_reply",
                "message": (
                    "🌤️ Thời tiết có thể thay đổi thất thường theo mùa, bạn nhớ chú ý giữ gìn sức khỏe, uống đủ nước và giữ ấm cổ họng nhé!\n\n"
                    "💡 *Mẹo chăm sóc răng miệng khi giao mùa:* Khi trời trở lạnh, những răng bị mòn men hoặc sâu ngà rất dễ bị ê buốt. Bạn nên dùng nước ấm và kem đánh răng chuyên dụng chống ê buốt.\n\n"
                    "Tôi là Trợ lý AI Nha Khoa Premier, bạn cần tư vấn vấn đề gì về răng miệng hôm nay không ạ?"
                )
            }, ensure_ascii=False)

        if intent == "GRATITUDE":
            self.logs.append("[Step 2 - Tool]: Gratitude intent detected")
            return json.dumps({
                "type": "direct_reply",
                "message": (
                    "Dạ không có gì ạ! Rất vui được hỗ trợ và đồng hành cùng bạn chăm sóc nụ cười sáng khỏe! 😊\n\n"
                    "Nếu cần thêm bất kỳ thông tin nào về chăm sóc răng miệng hay đặt lịch khám, bạn cứ nhắn tin cho em bất cứ lúc nào nhé. Chúc bạn một ngày thật nhiều niềm vui và luôn tự tin với nụ cười của mình! ✨"
                )
            }, ensure_ascii=False)

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

        else: # SERVICE & PRICE & CLINICAL KNOWLEDGE intent
            keyword = ""
            for kw in ["nhổ răng khôn", "niềng răng", "implant", "trám răng", "nhổ răng", "tẩy trắng", "bọc sứ", "răng sứ", "cạo vôi", "cao răng", "tủy", "trẻ em", "khám"]:
                if kw in query_lower:
                    keyword = kw
                    break

            # Kiểm tra xem câu hỏi có chứa triệu chứng hoặc kiến thức y khoa chuyên sâu không
            clinical_terms = [
                "đau", "nhức", "buốt", "sưng", "tủy", "chảy máu", "nha chu", "tiêu xương",
                "viêm", "mủ", "khôn", "lệch", "ngầm", "thuốc", "kháng sinh", "kiêng",
                "sau khi", "bao lâu", "nguy hiểm", "biến chứng", "có nên", "như thế nào",
                "veneer", "bọc sứ", "implant", "invisalign", "cạo vôi", "men răng"
            ]
            has_clinical_interest = any(term in query_lower for term in clinical_terms)

            if has_clinical_interest:
                self.logs.append(f"[Step 2 - RAG BigData]: Truy vấn CSDL Vector ChromaDB cho '{query}'")
                med_raw = tools.search_dental_knowledge(query, top_k=2)
                try:
                    med_data = json.loads(med_raw)
                except Exception:
                    med_data = {}

                services_raw = tools.search_services(keyword=keyword)
                try:
                    services_data = json.loads(services_raw)
                except Exception:
                    services_data = []

                combined_payload = {
                    "query": query,
                    "retrieved_medical_knowledge": med_data.get("retrieved_medical_knowledge", []),
                    "services": services_data[:3]
                }
                return json.dumps(combined_payload, ensure_ascii=False, indent=2)

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

                model_name = "gemini-3.6-flash"
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
            if data.get("type") in ["greeting", "direct_reply"] or (data.get("message") and "Xin chào" in data.get("message", "")):
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

            elif "retrieved_medical_knowledge" in data:
                med_list = data["retrieved_medical_knowledge"]
                services = data.get("services", [])

                answer = "🩺 **TƯ VẤN Y KHOA NHA KHOA CHUYÊN SÂU:**\n\n"
                if med_list:
                    for item in med_list:
                        topic = item.get("topic", "Y khoa Nha khoa")
                        content = item.get("clinical_knowledge", "")
                        answer += f"📌 **Chuyên đề: {topic}**\n{content}\n\n"

                if services:
                    answer += "🏥 **DỊCH VỤ ĐIỀU TRỊ TƯƠNG ỨNG TẠI PHÒNG KHÁM PREMIER:**\n"
                    for s in services:
                        answer += f"• **{s['name']}**: Chi phí tham khảo **{s['price_range']}** ({s.get('warranty', 'Khám & tư vấn miễn phí')})\n"
                    answer += "\n"

                answer += "💡 *Lời khuyên Bác sĩ: Mọi triệu chứng đau nhức hoặc bất thường răng miệng cần được Bác sĩ kiểm tra lâm sàng trực tiếp kết hợp chụp X-quang 3D để lên phác đồ chuẩn xác. Quý khách vui lòng cung cấp Tên + SĐT để phòng khám hỗ trợ đặt lịch khám miễn phí!*"
                return answer

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

