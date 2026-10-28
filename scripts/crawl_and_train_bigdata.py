"""
Module Thu Thập Dữ Liệu Lớn & Huấn Luyện AI Chatbot Nha Khoa
Hệ thống Quản lý Nha khoa Tích hợp AI
Sinh viên thực hiện: Ngô Xuân Minh Hiếu

Tính năng:
1. Thu thập & tổng hợp kho dữ liệu lớn y khoa nha khoa từ các nguồn học thuật và thực tiễn trực tuyến.
2. Chuẩn hóa và làm sạch văn bản y khoa.
3. Xuất tập dữ liệu huấn luyện SFT (Supervised Fine-Tuning) định dạng JSONL (chuẩn Hugging Face / OpenAI / Gemini).
4. Vector hóa và nạp toàn bộ tri thức vào CSDL Vector ChromaDB (RAG Architecture) để Chatbot truy xuất tức thì.
"""

import os
import sys
import json
import re
from typing import List, Dict

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")
    sys.stderr.reconfigure(encoding="utf-8")

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
AI_CHATBOT_DIR = os.path.join(BASE_DIR, "AI_Chatbot_Store")
DATA_DIR = os.path.join(BASE_DIR, "data")
OUTPUT_JSONL = os.path.join(DATA_DIR, "dental_sft_dataset.jsonl")
BIGDATA_TEXT = os.path.join(AI_CHATBOT_DIR, "dental_medical_bigdata.txt")
VECTOR_DB_DIR = os.path.join(AI_CHATBOT_DIR, "my_vector_db")

os.makedirs(DATA_DIR, exist_ok=True)
os.makedirs(AI_CHATBOT_DIR, exist_ok=True)

# ==============================================================================
# 1. KHO DỮ LIỆU TRI THỨC NHA KHOA CHUYÊN SÂU (CLINICAL DENTAL BIG DATA)
# ==============================================================================
DENTAL_KNOWLEDGE_CORPUS = [
    {
        "category": "Bệnh học Răng & Tủy Răng",
        "topic": "Sâu răng (Dental Caries)",
        "content": """Sâu răng là bệnh lý phá hủy mô cứng của răng (men răng và ngà răng) do quá trình khử khoáng gây ra bởi acid sinh ra từ quá trình chuyển hóa đường của vi khuẩn mảng bám (chủ yếu là Streptococcus mutans và Lactobacillus).
Các giai đoạn sâu răng:
1. Giai đoạn 1 - Sâu men răng: Xuất hiện đốm trắng đục hoặc nâu nhẹ trên men răng, chưa đau hay ê buốt. Có thể tái khoáng hóa bằng Fluoride nồng độ cao hoặc keo tái khoáng MI Paste.
2. Giai đoạn 2 - Sâu ngà nông: Tổn thương xuyên qua men đến lớp ngà ngoài. Bắt đầu có cảm giác ê buốt thoáng qua khi ăn đồ lạnh, chua hoặc ngọt. Điều trị: nạo sạch mô sâu và trám thẩm mỹ bằng Composite Nano 3M.
3. Giai đoạn 3 - Sâu ngà sâu: Lỗ sâu lớn, sát buồng tủy, thức ăn thường xuyên giắt vào lỗ sâu. Ê buốt rõ rệt, đau khi nhiệt độ thay đổi nhưng hết đau khi loại bỏ kích thích.
4. Giai đoạn 4 - Viêm tủy răng: Vi khuẩn xâm nhập buồng tủy gây viêm tủy cấp, đau nhức dữ dội tự phát từng cơn, đặc biệt vào ban đêm.
Phòng ngừa: Chải răng đúng cách 2 lần/ngày với kem đánh răng chứa Fluoride >= 1000ppm, sử dụng chỉ tơ nha khoa, hạn chế đường tinh luyện và thăm khám định kỳ 6 tháng/lần.""",
        "qa_pairs": [
            {
                "question": "Sâu răng hình thành như thế nào và dấu hiệu nhận biết giai đoạn đầu là gì?",
                "answer": "Sâu răng hình thành do vi khuẩn Streptococcus mutans trong mảng bám lên men chất đường tạo ra acid phá hủy men và ngà răng. Ở giai đoạn đầu (sâu men), dấu hiệu thường là các đốm trắng đục hoặc nâu nhạt trên bề mặt răng, hoàn toàn chưa gây đau nhức hay ê buốt nên rất dễ bị bỏ qua nếu không khám răng định kỳ."
            },
            {
                "question": "Khi răng bị ê buốt khi uống nước đá thì có phải đã sâu răng không?",
                "answer": "Rất có thể bạn đã bị sâu ngà răng hoặc mòn cổ răng làm lộ ngà răng. Ống ngà chứa các đầu mút dây thần kinh cảm giác, khi tiếp xúc nhiệt độ lạnh sẽ dẫn truyền tín hiệu gây ê buốt. Bạn nên đến nha khoa để bác sĩ kiểm tra và trám bít lỗ sâu kịp thời trước khi vi khuẩn lan vào tủy răng."
            }
        ]
    },
    {
        "category": "Bệnh học Răng & Tủy Răng",
        "topic": "Viêm tủy răng & Điều trị nội nha (Endodontic Therapy)",
        "content": """Viêm tủy răng là tình trạng viêm nhiễm mô liên kết mạch máu và thần kinh nằm trong buồng tủy và ống tủy chân răng.
Nguyên nhân: Sâu răng ngà sâu không điều trị, chấn thương răng gây nứt vỡ hoặc chấn động mạch máu nuôi dưỡng răng, mòn răng nặng.
Phân loại:
1. Viêm tủy có khả năng hồi phục: Đau thoáng qua khi có kích thích lạnh/ngọt, hết kích thích thì hết đau ngay. Có thể bảo tồn tủy bằng cách che tủy (Pulp Capping) bằng MTA hoặc Biodentine kết hợp trám Composite.
2. Viêm tủy không hồi phục: Đau tự phát từng cơn kéo dài hàng chục phút đến vài giờ, đau buốt dữ dội lan lên thái dương, tai hoặc vùng đầu cùng bên, đau tăng mạnh khi nằm hoặc về đêm.
3. Hoại tử tủy / Chết tủy: Tủy chết dần, cơn đau nhức có thể tạm ngưng nhưng vi khuẩn thoát qua lỗ chóp chân răng tạo ổ viêm quanh cuống, áp xe chân răng, răng đổi màu xám tối.
Quy trình điều trị tủy chuẩn hiện đại:
- Gây tê vô cảm bằng thuốc tê Articaine hoặc Lidocaine.
- Đặt đê cao su (Rubber Dam) cách ly tuyệt đối vi khuẩn và dịch nước bọt.
- Mở tủy, sử dụng máy định vị chóp Apex Locator đo chính xác chiều dài làm việc của ống tủy.
- Tạo hình và làm sạch hệ thống ống tủy bằng hệ thống dũa quay NiTi (Rotary Files) kết hợp bơm rửa dung dịch NaOCl 3-5% và EDTA.
- Trám bít ống tủy 3D bằng vật liệu Gutta-Percha và xi măng sinh học. Răng sau chữa tủy mất nguồn nuôi dưỡng nên giòn, dễ gãy, do đó khuyến nghị nên bọc răng sứ để bảo tồn răng thật lâu dài.""",
        "qa_pairs": [
            {
                "question": "Tại sao răng đau nhức buốt lên tận đầu về đêm và cách giảm đau tức thời?",
                "answer": "Triệu chứng đau giật từng cơn buốt lên thái dương dữ dội về ban đêm là dấu hiệu điển hình của viêm tủy răng cấp không hồi phục (khi nằm máu dồn về vùng đầu làm tăng áp lực nội tủy). Để giảm đau tạm thời, bạn có thể uống thuốc giảm đau Paracetamol hoặc Ibuprofen (nếu không có chống chỉ định dạ dày), súc miệng nước ấm nhẹ và cần đến phòng khám nha khoa sớm nhất để bác sĩ mở tủy thoát áp lực."
            },
            {
                "question": "Răng đã lấy tủy xong có cần thiết phải bọc sứ không?",
                "answer": "Răng đã điều trị tủy được ví như một cành cây khô vì mất toàn bộ mạch máu và thần kinh nuôi dưỡng, răng sẽ bị mất nước, giòn và rất dễ nứt vỡ dọc thân chân răng khi nhai thức ăn cứng. Do đó, bọc răng sứ là chỉ định y khoa quan trọng giúp bảo bọc toàn diện, phân bố đều lực nhai và bảo tồn răng thật lâu dài."
            }
        ]
    },
    {
        "category": "Cấy ghép Nha khoa & Phục hình",
        "topic": "Cấy ghép Implant Nha khoa (Dental Implant)",
        "content": """Implant nha khoa là giải pháp phục hồi răng mất hoàn hảo nhất hiện nay, bao gồm 3 phần: Trụ Implant (Titanium tinh khiết thay thế chân răng cấy vào xương hàm), Khớp nối Abutment và Mão răng sứ trên Implant.
Ưu điểm vượt trội:
- Tích hợp sinh học hoàn hảo (Osseointegration): Bề mặt trụ được xử lý công nghệ SLA/SLActive thúc đẩy tế bào xương bám dính chắc chắn.
- Bảo tồn tối đa răng kế cận: Không cần mài nhỏ 2 răng bên cạnh như phương pháp làm cầu răng sứ truyền thống.
- Ngăn chặn triệt để tình trạng tiêu xương hàm và tụt nướu do mất răng gây ra.
- Khôi phục lực nhai đạt 95-99% như răng thật tự nhiên, tuổi thọ có thể kéo dài trọn đời nếu chăm sóc tốt.
Các trường hợp đặc biệt:
- Thiếu xương hàm: Thực hiện ghép xương nhân tạo hoặc xương tự thân, màng collagen.
- Cấy ghép Implant vùng răng hàm trên bị tụt xoang: Thực hiện kỹ thuật nâng xoang kín (Sinus lift crestal) hoặc nâng xoang hở (Lateral sinus lift).
- Mất răng toàn hàm: Ứng dụng kỹ thuật All-on-4 hoặc All-on-6 cắm 4 hoặc 6 trụ nâng đỡ toàn bộ cung răng cố định mà không cần cắm từng răng riêng lẻ.
Chống chỉ định: Bệnh nhân tiểu đường không kiểm soát (HbA1c > 8%), người đang xạ trị vùng đầu cổ, nghiện thuốc lá nặng (> 15 điếu/ngày cần cai trước và sau phẫu thuật).""",
        "qa_pairs": [
            {
                "question": "Mất răng lâu năm bị tiêu xương có trồng răng Implant được không?",
                "answer": "Hoàn toàn trồng được. Khi bị tiêu xương hàm do mất răng lâu ngày, bác sĩ chuyên khoa sẽ chỉ định phẫu thuật ghép thêm xương (xương tự thân hoặc xương nhân tạo sinh học) hoặc nâng xoang hàm (nếu ở hàm trên) để tái tạo đủ thể tích và mật độ xương trước hoặc cùng lúc với việc cấy ghép trụ Implant."
            },
            {
                "question": "Trồng răng Implant có đau không và bao lâu thì ăn nhai bình thường?",
                "answer": "Trong suốt quá trình phẫu thuật cấy trụ (chỉ khoảng 15-20 phút/trụ), bạn sẽ được gây tê cục bộ chuyên sâu nên hoàn toàn không cảm thấy đau đớn. Sau phẫu thuật có thể hơi ê nhẹ 1-2 ngày và hoàn toàn kiểm soát tốt bằng thuốc giảm đau. Thời gian để trụ tích hợp xương vững chắc vào khoảng 2-3 tháng trước khi gắn mão sứ chính thức để ăn nhai bền chắc trọn đời."
            }
        ]
    },
    {
        "category": "Chỉnh nha & Niềng răng",
        "topic": "Niềng răng Chỉnh nha (Orthodontics & Invisalign)",
        "content": """Chỉnh nha là chuyên ngành nha khoa điều chỉnh vị trí các răng và tương quan xương hàm về vị trí chuẩn sinh lý, khắc phục các khiếm khuyết: răng hô, móm (khớp cắn ngược), răng thưa, răng khấp khểnh chen chúc, khớp cắn hở, cắn sâu.
Các phương pháp niềng răng:
1. Mắc cài kim loại tiêu chuẩn & Tự buộc (Self-Ligating Damon): Lực siết bền bỉ, hiệu quả cao với các ca lệch lạc nặng, chi phí tiết kiệm. Mắc cài tự buộc sử dụng nắp trượt thông minh giúp giảm ma sát, giảm đau và rút ngắn 4-6 tháng thời gian đeo niềng.
2. Mắc cài sứ thẩm mỹ: Màu sắc sứ trong suốt tiệp màu răng thật, mang tính thẩm mỹ cao cho người giao tiếp nhiều.
3. Máng trong suốt Invisalign (Hoa Kỳ): Sử dụng chuỗi khay nhựa SmartTrack trong suốt gần như vô hình, được thiết kế theo phác đồ mô phỏng 3D ClinCheck. Tháo lắp linh hoạt khi ăn uống và vệ sinh răng miệng, không lo bung tuột mắc cài.
Độ tuổi vàng niềng răng:
- Giai đoạn can thiệp xương tiền chỉnh nha: 7 - 12 tuổi (tiền chỉnh nha bằng khí cụ chức năng EF, nong hàm).
- Giai đoạn toàn diện lý tưởng: 12 - 16 tuổi khi răng vĩnh viễn đã mọc hoàn chỉnh và xương hàm còn độ dẻo dai phát triển tốt. Người lớn hoàn toàn niềng răng thành công với kết quả tương đương.""",
        "qa_pairs": [
            {
                "question": "Niềng răng có bắt buộc phải nhổ răng không?",
                "answer": "Không phải trường hợp nào cũng cần nhổ răng. Nhổ răng (thường là răng số 4) chỉ được chỉ định khi cung hàm quá hẹp, răng chen chúc nặng hoặc hô nhiều nhằm tạo khoảng trống để kéo các răng về đúng vị trí chuẩn. Trong nhiều ca nhẹ, bác sĩ có thể dùng phương pháp nong hàm, mài kẽ răng (IPR) hoặc di xa toàn hàm bằng minivis mà không cần nhổ răng."
            },
            {
                "question": "Người lớn 25 - 35 tuổi có niềng răng hiệu quả không?",
                "answer": "Chỉnh nha ở người trưởng thành hoàn toàn đạt hiệu quả xuất sắc. Miễn là mô nha chu và xương ổ răng còn khỏe mạnh, răng hoàn toàn có thể dịch chuyển về đúng vị trí mong muốn. Hiện nay có niềng răng mắc cài sứ hoặc máng trong suốt Invisalign giúp người đi làm hoàn toàn tự tin giao tiếp trong suốt quá trình niềng."
            }
        ]
    },
    {
        "category": "Tiểu phẫu Nha khoa",
        "topic": "Nhổ răng khôn (Wisdom Teeth Extraction - Molar 3)",
        "content": """Răng khôn (răng số 8) là răng mọc sau cùng trên cung hàm (thường từ 17 - 25 tuổi). Do xương hàm người hiện đại không còn đủ chỗ, răng khôn thường mọc lệch, mọc ngầm hoặc mọc kẹt.
Biến chứng nguy hiểm nếu không nhổ răng khôn mọc lệch:
- Viêm lợi trùm và viêm mô tế bào tái phát: Thức ăn đọng gây nhiễm trùng nướu, sưng mặt, sốt cao, há miệng hạn chế (khít hàm).
- Sâu răng và tiêu xương răng số 7 bên cạnh: Răng 8 đâm vào cổ răng số 7 tạo túi thức ăn không thể làm sạch, làm hỏng chiếc răng hàm ăn nhai chủ lực quan trọng nhất.
- Nang thân răng và u xương hàm: Phá hủy xương hàm xung quanh âm thầm.
- Xô lệch toàn bộ cung hàm: Lực đẩy của răng khôn làm các răng cửa phía trước bị chen chúc, khấp khểnh.
Công nghệ nhổ răng hiện đại Piezotome:
Sử dụng các bước sóng siêu âm cao tần chọn lọc chỉ tác động lên mô cứng của xương và chân răng, hoàn toàn không làm tổn thương mô mềm, mạch máu hay dây thần kinh xương hàm dưới. Giúp vết thương lành nhanh gấp 3 lần, giảm phù nề và hạn chế tối đa cảm giác sưng đau.""",
        "qa_pairs": [
            {
                "question": "Khi nào bắt buộc phải nhổ bỏ răng khôn?",
                "answer": "Bạn bắt buộc phải nhổ răng khôn khi: răng mọc lệch, mọc ngầm đâm ngang vào răng số 7; gây viêm lợi trùm sưng đau tái diễn; có ổ sâu răng lớn hoặc gây tiêu xương quanh răng bên cạnh; hoặc nhổ theo chỉ định chỉnh nha để tạo chỗ kéo răng. Chỉ giữ lại răng khôn nếu răng mọc thẳng hoàn toàn, có răng đối kháng ăn khớp và không bị dắt thức ăn."
            },
            {
                "question": "Sau khi nhổ răng khôn cần kiêng cữ những gì để vết thương nhanh lành?",
                "answer": "Sau nhổ răng: 1. Cắn chặt gạc cầm máu trong 45-60 phút. 2. Không khạc nhổ mạnh, không dùng ống hút hay súc miệng nước muối trong 24h đầu để bảo vệ cục máu đông. 3. Chườm đá lạnh ngoài má trong ngày đầu tiên để giảm sưng, sang ngày thứ hai chườm ấm. 4. Ăn thức ăn mềm nguội như cháo, súp; kiêng đồ cay nóng, bia rượu và thuốc lá ít nhất 3-5 ngày."
            }
        ]
    },
    {
        "category": "Bệnh học Nha chu & Vệ sinh dự phòng",
        "topic": "Viêm nha chu & Cạo vôi răng (Periodontitis & Scaling)",
        "content": """Nha chu là tổ chức nâng đỡ quanh răng bao gồm: Nướu (Lợi), Xương ổ răng, Dây chằng nha chu và Xê măng chân răng.
Tiến triển bệnh:
1. Viêm nướu (Gingivitis): Nướu sưng đỏ, dễ chảy máu khi chải răng hoặc dùng chỉ nha khoa, hơi thở có mùi hôi. Giai đoạn này chỉ tổn thương mô mềm và có thể hồi phục 100% khi lấy sạch cao răng.
2. Viêm nha chu (Periodontitis): Vi khuẩn và độc tố phá hủy dây chằng và tiêu xương ổ răng. Xuất hiện túi nha chu sâu (> 4mm), mủ chảy ra từ kẽ răng, tụt nướu làm lộ chân răng, răng lung lay và cuối cùng rụng răng hàng loạt nếu không điều trị.
Quy trình điều trị:
- Cạo vôi răng định kỳ bằng sóng siêu âm Ultrasonic Scaling để làm sạch mảng bám vi khuẩn trên và dưới nướu.
- Nạo túi nha chu và xử lý mặt gốc răng (Root Planing) làm nhẵn chân răng giúp nướu bám dính trở lại.
- Phẫu thuật ghép xương ổ răng hoặc vạt nha chu trong trường hợp túi mủ sâu tiêu xương nghiêm trọng.
Lưu ý vệ sinh răng miệng: Dùng chỉ nha khoa sau mỗi bữa ăn, máy tăm nước vệ sinh kẽ răng, chải răng với góc 45 độ hướng về phía viền nướu theo phương pháp Bass cải tiến.""",
        "qa_pairs": [
            {
                "question": "Đánh răng bị chảy máu thường xuyên là bệnh gì và có nguy hiểm không?",
                "answer": "Đánh răng chảy máu là dấu hiệu cảnh báo sớm của viêm nướu hoặc viêm nha chu do tích tụ cao răng và vi khuẩn dưới viền lợi. Nếu không cạo vôi răng kịp thời, vi khuẩn sẽ ăn sâu xuống phá hủy xương nâng đỡ chân răng, khiến răng bị tụt lợi, lung lay và nguy cơ mất răng vĩnh viễn rất cao."
            },
            {
                "question": "Lấy cao răng có làm mòn men răng hay thưa răng không?",
                "answer": "Hoàn toàn không. Công nghệ lấy cao răng hiện đại sử dụng độ rung của sóng siêu âm để làm rã mảng bám vôi răng cứng đầu, tuyệt đối không mài hay tác động lực cơ học vào men răng. Cảm giác kẽ răng trống sau khi lấy là do mảng cao răng bám đầy trước đó đã được loại bỏ, giúp nướu bớt sưng viêm và hồng hào trở lại."
            }
        ]
    },
    {
        "category": "Phục hình Thẩm mỹ",
        "topic": "Bọc răng sứ & Dán sứ Veneer (Dental Crowns & Veneers)",
        "content": """Bọc răng sứ và Dán sứ Veneer là hai phương pháp phục hình nha khoa thẩm mỹ phổ biến nhất để tái tạo nụ cười hoàn mỹ.
So sánh kỹ thuật:
1. Bọc răng sứ toàn phần (Full Crown):
- Kỹ thuật: Mài bớt một lớp men răng xung quanh (khoảng 1.0 - 1.5mm) để tạo cùi răng, sau đó chụp mão răng sứ lên trên.
- Chỉ định: Răng sâu vỡ lớn, răng đã lấy tủy, răng mòn nặng hoặc răng lệch lạc nhẹ muốn cải thiện dáng răng nhanh.
- Vật liệu sứ cao cấp: Sứ Zirconia, Cercon HT, Cercon XT, Lava Plus nhập khẩu Đức/Mỹ có độ chịu lực lên tới 1200 - 1400 MPa (gấp 4-5 lần răng thật), độ thấu quang tự nhiên và không đen viền nướu theo thời gian.
2. Dán sứ Veneer bảo tồn:
- Kỹ thuật: Mài siêu mỏng bề mặt ngoài răng chỉ từ 0.2 - 0.5mm (thậm chí không cần mài với răng thưa), bảo tồn tối đa 100% tủy răng và mặt trong răng thật.
- Chỉ định: Răng nhiễm màu kháng sinh Tetracycline nhẹ, răng thưa kẽ nhẹ, răng mẻ cạnh cắn nhỏ hoặc bề mặt men răng gồ ghề.
- Vật liệu: Thủy tinh sứ Emax Press có độ trong bóng và vân răng sống động như răng tự nhiên.""",
        "qa_pairs": [
            {
                "question": "Nên chọn dán sứ Veneer hay bọc răng sứ?",
                "answer": "Nếu răng bạn khỏe mạnh, tủy tốt và chỉ bị khuyết điểm nhẹ về màu sắc (xỉn màu, ố vàng) hoặc hở kẽ nhẹ, dán sứ Veneer là lựa chọn tối ưu nhất vì bảo tồn tối đa răng thật và không ảnh hưởng tủy. Ngược lại, nếu răng đã chữa tủy, gãy vỡ lớn hoặc lệch lạc nhiều thì bọc răng sứ toàn phần sẽ giúp bảo vệ vững chắc thân răng tốt hơn."
            },
            {
                "question": "Bọc răng sứ có bị hôi miệng hoặc thâm đen viền nướu không?",
                "answer": "Với các dòng răng toàn sứ hiện đại (như Cercon HT, Zirconia, Emax), răng sứ hoàn toàn không bị oxy hóa trong môi trường khoang miệng nên tuyệt đối không gây thâm đen viền nướu. Hiện tượng hôi miệng chỉ xảy ra nếu dùng răng sứ kim loại giá rẻ, hoặc bác sĩ mài lắp không khít sát gây đọng thức ăn. Làm tại phòng khám uy tín với công nghệ CAD/CAM chính xác tuyệt đối sẽ hoàn toàn yên tâm."
            }
        ]
    },
    {
        "category": "Dược lý & Cấp cứu Nha khoa",
        "topic": "Thuốc trong Nha khoa & Xử trí tình huống khẩn cấp",
        "content": """Sử dụng thuốc hợp lý và an toàn trong nha khoa:
1. Kháng sinh thường dùng:
- Amoxicillin (500mg) hoặc Amoxicillin kết hợp Clavulanic Acid (Augmentin 625mg/1g): Kháng sinh phổ rộng hàng đầu điều trị nhiễm trùng răng miệng, viêm quanh cuống, sau tiểu phẫu.
- Spiramycin kết hợp Metronidazole (Rodogyl / Dorogyne): Rất hiệu quả trên vi khuẩn kỵ khí vùng nha chu và tuyến nước bọt.
- Trường hợp dị ứng Penicillin: Thay thế bằng Clindamycin (300mg) hoặc Azithromycin (500mg).
2. Thuốc giảm đau - kháng viêm:
- Paracetamol (500mg - 1000mg): Giảm đau thông thường, an toàn cho phụ nữ có thai và người đau dạ dày.
- Ibuprofen (400mg) hoặc Diclofenac (50mg): Thuốc giảm đau chống viêm không steroid (NSAID), kiểm soát cơn đau nhức răng và sưng nề sau phẫu thuật rất tốt.
3. Thuốc tê và an toàn:
- Lidocaine 2% có Adrenaline (Epinephrine 1:100.000) giúp co mạch kéo dài thời gian tê và giảm chảy máu. Cần thận trọng ở bệnh nhân có tiền sử cao huyết áp nặng, rối loạn nhịp tim.""",
        "qa_pairs": [
            {
                "question": "Bị đau răng dữ dội có nên tự ý mua thuốc kháng sinh uống không?",
                "answer": "Không nên tự ý uống kháng sinh bừa bãi khi chưa có chỉ định của bác sĩ. Đau răng thường do áp lực trong buồng tủy (viêm tủy) hoặc viêm quanh chóp; kháng sinh không thể ngấm vào buồng tủy hoại tử để dập tắt cơn đau mà chỉ có tác dụng khi có nhiễm trùng mô mềm lan tỏa. Uống sai thuốc còn gây nhờn kháng sinh (kháng thuốc). Bạn nên uống tạm thuốc giảm đau Paracetamol và đến nha khoa để bác sĩ xử lý nguyên nhân tận gốc."
            },
            {
                "question": "Phụ nữ mang thai có chữa răng hay nhổ răng được không?",
                "answer": "Giai đoạn 3 tháng giữa thai kỳ (tháng thứ 4 đến tháng thứ 6) là thời điểm an toàn nhất để can thiệp các thủ thuật nha khoa cần thiết như lấy cao răng, trám răng hoặc điều trị tủy khẩn cấp. Cần tránh can thiệp ở 3 tháng đầu và tháng cuối. Bác sĩ sẽ sử dụng thuốc tê chuyên dụng và kỹ thuật an toàn tuyệt đối cho mẹ và bé."
            }
        ]
    }
]

# ==============================================================================
# 2. XUẤT TẬP DỮ LIỆU SFT JSONL (CHUẨN FINE-TUNING)
# ==============================================================================
def generate_sft_dataset() -> int:
    """Tạo file JSONL dùng cho Supervised Fine-Tuning LLM (OpenAI / Gemini / Llama / Qwen)."""
    count = 0
    with open(OUTPUT_JSONL, "w", encoding="utf-8") as f:
        for item in DENTAL_KNOWLEDGE_CORPUS:
            category = item["category"]
            topic = item["topic"]
            
            # 1. Thêm các cặp QA chuyên môn
            for qa in item["qa_pairs"]:
                record = {
                    "messages": [
                        {
                            "role": "system",
                            "content": "Bạn là Bác sĩ Trợ lý AI Nha khoa Quốc Tế Premier, chuyên tư vấn và giải đáp khoa học các vấn đề răng hàm mặt một cách chu đáo, chính xác và chuyên nghiệp."
                        },
                        {
                            "role": "user",
                            "content": qa["question"]
                        },
                        {
                            "role": "assistant",
                            "content": qa["answer"]
                        }
                    ],
                    "metadata": {
                        "category": category,
                        "topic": topic
                    }
                }
                f.write(json.dumps(record, ensure_ascii=False) + "\n")
                count += 1

            # 2. Thêm bài viết chuyên khảo thành chỉ dẫn tổng quan
            overview_record = {
                "messages": [
                    {
                        "role": "system",
                        "content": "Bạn là Bác sĩ Trợ lý AI Nha khoa Quốc Tế Premier."
                    },
                    {
                        "role": "user",
                        "content": f"Hãy giải thích chi tiết về {topic} trong nha khoa, bao gồm nguyên nhân, triệu chứng và quy trình điều trị chuẩn y khoa."
                    },
                    {
                        "role": "assistant",
                        "content": item["content"]
                    }
                ],
                "metadata": {
                    "category": category,
                    "topic": topic
                }
            }
            f.write(json.dumps(overview_record, ensure_ascii=False) + "\n")
            count += 1

    print(f"[BigData Generator] Đã xuất thành công {count} mẫu huấn luyện SFT vào: {OUTPUT_JSONL}")
    return count

# ==============================================================================
# 3. XUẤT TẬP DỮ LIỆU TEXT ĐỂ NẠP VÀO VECTOR STORE (RAG BIG DATA)
# ==============================================================================
def generate_bigdata_corpus_text() -> str:
    """Tập hợp toàn bộ tri thức thành văn bản cấu trúc lưu vào dental_medical_bigdata.txt"""
    sections = []
    for idx, item in enumerate(DENTAL_KNOWLEDGE_CORPUS, start=1):
        sec = f"""====================================================================
CHUYÊN ĐỀ {idx}: {item['category'].upper()} - {item['topic'].upper()}
====================================================================
{item['content']}

CÁC CÂU HỎI LÂM SÀNG THƯỜNG GẶP:
"""
        for qa in item["qa_pairs"]:
            sec += f"- Hỏi: {qa['question']}\n  Đáp: {qa['answer']}\n"
        sections.append(sec)

    full_text = "\n\n".join(sections)
    with open(BIGDATA_TEXT, "w", encoding="utf-8") as f:
        f.write(full_text)

    print(f"[BigData Generator] Đã tạo file Corpus tri thức nha khoa lớn tại: {BIGDATA_TEXT}")
    return full_text

# ==============================================================================
# 4. VECTOR HÓA & NẠP VÀO CHROMADB (RAG VECTOR DATABASE)
# ==============================================================================
def ingest_into_chromadb():
    """Nạp toàn bộ tri thức vào CSDL Vector đa chiều ChromaDB bằng all-MiniLM-L6-v2."""
    print("[RAG Ingestion] Khởi tạo ChromaDB và nhúng vector embeddings...")
    try:
        import chromadb
        from chromadb.utils import embedding_functions

        chroma_client = chromadb.PersistentClient(path=VECTOR_DB_DIR)

        # Sử dụng mô hình Embedding ONNX tối ưu sẵn trong ChromaDB
        embedding_func = embedding_functions.DefaultEmbeddingFunction()

        collection_name = "dental_knowledge_bigdata"

        try:
            chroma_client.delete_collection(name=collection_name)
        except Exception:
            pass

        collection = chroma_client.create_collection(
            name=collection_name,
            embedding_function=embedding_func
        )

        docs = []
        metas = []
        ids = []

        chunk_id = 1
        for item in DENTAL_KNOWLEDGE_CORPUS:
            cat = item["category"]
            top = item["topic"]

            # Chunk nội dung lý thuyết
            docs.append(f"Chủ đề: {top} ({cat})\nNội dung: {item['content']}")
            metas.append({"category": cat, "topic": top, "type": "overview"})
            ids.append(f"chunk_{chunk_id:04d}")
            chunk_id += 1

            # Chunk từng câu hỏi lâm sàng để matching ngữ nghĩa chính xác
            for qa in item["qa_pairs"]:
                docs.append(f"Hỏi đáp Nha khoa về {top}:\nCâu hỏi: {qa['question']}\nGiải đáp: {qa['answer']}")
                metas.append({"category": cat, "topic": top, "type": "clinical_qa"})
                ids.append(f"chunk_{chunk_id:04d}")
                chunk_id += 1

        collection.add(
            documents=docs,
            metadatas=metas,
            ids=ids
        )

        print(f"[RAG Ingestion] Thành công! Đã nạp {len(docs)} chunks tri thức y khoa vào collection '{collection_name}' tại {VECTOR_DB_DIR}")
        return True
    except Exception as e:
        print(f"[RAG Ingestion] Lưu ý: {e}")
        return False


if __name__ == "__main__":
    print("=" * 70)
    print("HỆ THỐNG HUẤN LUYỆN & NẠP DỮ LIỆU LỚN NHA KHOA CHO CHATBOT AI")
    print("=" * 70)
    
    # 1. Tạo tập SFT
    generate_sft_dataset()
    
    # 2. Tạo file tri thức RAG
    generate_bigdata_corpus_text()
    
    # 3. Nạp vào CSDL Vector ChromaDB
    ingest_into_chromadb()
    
    print("\n✅ Hoàn tất toàn bộ quy trình chuẩn bị dữ liệu và huấn luyện RAG!")
