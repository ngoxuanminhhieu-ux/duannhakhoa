import sys
import urllib.request
import json

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

url = "http://localhost:8090/chatbot/chat.php"
data = {"message": "địa chỉ nha khoa ở đâu"}

req = urllib.request.Request(
    url,
    data=json.dumps(data).encode("utf-8"),
    headers={"Content-Type": "application/json"}
)

try:
    with urllib.request.urlopen(req, timeout=30) as response:
        res_text = response.read().decode("utf-8")
        parsed = json.loads(res_text)
        print("Reply from chat.php:")
        print(parsed.get("reply", res_text))
except Exception as e:
    print("Error:", e)
