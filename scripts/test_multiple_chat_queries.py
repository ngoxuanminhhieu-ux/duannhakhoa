import sys
import urllib.request
import json

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

url = "http://localhost:8090/chatbot/chat.php"
questions = [
    "địa chỉ nha khoa ở đâu",
    "Bác sĩ cấy ghép implant tại phòng khám là ai?",
    "Phòng khám có chương trình trả góp 0% không?",
    "Giá trám răng và lấy cao răng bao nhiêu tiền?"
]

for q in questions:
    print("=" * 60)
    print(f"❓ CÂU HỎI: {q}")
    print("=" * 60)
    req = urllib.request.Request(
        url,
        data=json.dumps({"message": q}).encode("utf-8"),
        headers={"Content-Type": "application/json"}
    )
    try:
        with urllib.request.urlopen(req, timeout=30) as response:
            res_text = response.read().decode("utf-8")
            parsed = json.loads(res_text)
            print(parsed.get("reply", res_text))
    except Exception as e:
        print("Lỗi:", e)
    print("\n")
