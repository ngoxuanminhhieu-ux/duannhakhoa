import sys
import os
import json
import urllib.request
import urllib.error

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

img_path = r"c:\Duannhakhoa\web\assets\images\smile-woman.jpg"
if not os.path.exists(img_path):
    print("File not found:", img_path)
    sys.exit(1)

with open(img_path, "rb") as f:
    image_bytes = f.read()

boundary = '----WebKitFormBoundary7MA4YWxkTrZu0gW'
body = (
    f'--{boundary}\r\n'
    'Content-Disposition: form-data; name="image"; filename="smile-woman.jpg"\r\n'
    'Content-Type: image/jpeg\r\n\r\n'
).encode('utf-8') + image_bytes + (
    f'\r\n--{boundary}\r\n'
    'Content-Disposition: form-data; name="message"\r\n\r\n'
    'Bác sĩ kiểm tra giúp răng tôi có đều và có cần niềng răng hay làm trắng không'
    f'\r\n--{boundary}--\r\n'
).encode('utf-8')

url = "http://localhost:8090/api/analyze-dental-image.php"
req = urllib.request.Request(
    url,
    data=body,
    headers={
        'Content-Type': f'multipart/form-data; boundary={boundary}'
    }
)

print(f"Gửi ảnh smile-woman.jpg tới API Vision ({len(image_bytes)} bytes)...")
try:
    with urllib.request.urlopen(req, timeout=40) as res:
        res_text = res.read().decode('utf-8')
        print(f"Status Code: {res.status}")
        data = json.loads(res_text)
        print("KẾT QUẢ SÀNG LỌC HÌNH ẢNH NHA KHOA THÀNH CÔNG:")
        print(json.dumps(data, ensure_ascii=False, indent=2))
except urllib.error.HTTPError as e:
    err_body = e.read().decode('utf-8', errors='replace')
    print(f"HTTP ERROR {e.code}: {err_body}")
except Exception as e:
    print(f"ERROR: {e}")
