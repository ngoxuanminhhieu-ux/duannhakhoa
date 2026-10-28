import os
import sys
import base64
import json
import urllib.request
import urllib.error

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

# Tạo 1 ảnh PNG 100x100 trắng giả lập để test
from PIL import Image
import io

img = Image.new('RGB', (100, 100), color = 'red')
buf = io.BytesIO()
img.save(buf, format='JPEG')
jpeg_bytes = buf.getvalue()
base64_img = base64.b64encode(jpeg_bytes).decode('utf-8')

env_key = os.getenv("GEMINI_API_KEY", "").strip()
keys = [k for k in [env_key] if k]

models = [
    "gemini-3.8-flash",
    "gemini-3.6-flash",
    "gemini-2.5-flash",
    "gemini-1.5-flash"
]

for key_idx, key in enumerate(keys, 1):
    print(f"\n--- TESTING KEY #{key_idx}: {key[:15]}... ---")
    for model in models:
        url = f"https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent?key={key}"
        payload = {
            "contents": [
                {
                    "role": "user",
                    "parts": [
                        {"text": "Phân tích ảnh răng này"},
                        {
                            "inline_data": {
                                "mime_type": "image/jpeg",
                                "data": base64_img
                            }
                        }
                    ]
                }
            ],
            "generationConfig": {
                "temperature": 0.2,
                "maxOutputTokens": 2048,
                "response_mime_type": "application/json"
            }
        }
        
        req = urllib.request.Request(
            url,
            data=json.dumps(payload).encode('utf-8'),
            headers={"Content-Type": "application/json"}
        )
        try:
            with urllib.request.urlopen(req, timeout=15) as res:
                body = res.read().decode('utf-8')
                print(f"  [SUCCESS] Model {model} returned HTTP {res.status}!")
                print(f"    Sample output: {body[:120]}...")
        except urllib.error.HTTPError as e:
            err_body = e.read().decode('utf-8', errors='replace')
            print(f"  [HTTP ERROR {e.code}] Model {model}: {err_body[:200]}")
        except Exception as e:
            print(f"  [ERROR] Model {model}: {e}")
