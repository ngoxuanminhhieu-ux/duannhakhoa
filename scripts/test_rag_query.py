import os
import sys
import json

if sys.platform == "win32":
    sys.stdout.reconfigure(encoding="utf-8")

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
AI_CHATBOT_DIR = os.path.join(BASE_DIR, "AI_Chatbot_Store")
VECTOR_DB_DIR = os.path.join(AI_CHATBOT_DIR, "my_vector_db")

import chromadb
from chromadb.utils import embedding_functions

client = chromadb.PersistentClient(path=VECTOR_DB_DIR)
embedding_func = embedding_functions.DefaultEmbeddingFunction()

col = client.get_collection("dental_knowledge_bigdata", embedding_function=embedding_func)

queries = [
    "Tôi bị đau buốt răng dữ dội lên tận đầu vào ban đêm là bị gì?",
    "Mất răng lâu năm tiêu xương thì có cấy ghép Implant được không?",
    "Răng khôn mọc lệch 90 độ có biến chứng gì nguy hiểm?",
    "Bọc răng sứ có bị đen viền nướu không?"
]

print("=" * 60)
print("TEST TRUY VẤN TRI THỨC TỪ CSDL VECTOR CHROMADB:")
print("=" * 60)

for q in queries:
    print(f"\n❓ Câu hỏi: {q}")
    results = col.query(query_texts=[q], n_results=2)
    for i, (doc, meta, dist) in enumerate(zip(results["documents"][0], results["metadatas"][0], results["distances"][0]), 1):
        print(f"  [Top {i} (Distance: {dist:.4f})] - Chủ đề: {meta.get('topic')}")
        snippet = doc.replace('\n', ' ')[:150]
        print(f"    Trích dẫn: {snippet}...")
