import os
import sys

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
AI_CHATBOT_DIR = os.path.join(BASE_DIR, "AI_Chatbot_Store")
VECTOR_DB_DIR = os.path.join(AI_CHATBOT_DIR, "my_vector_db")

try:
    import chromadb
    client = chromadb.PersistentClient(path=VECTOR_DB_DIR)
    cols = client.list_collections()
    print("Collections in ChromaDB:", [c.name for c in cols], flush=True)
    for col in cols:
        print(f"Collection '{col.name}' count: {col.count()}", flush=True)
except Exception as e:
    print(f"Error querying ChromaDB: {e}", flush=True)
