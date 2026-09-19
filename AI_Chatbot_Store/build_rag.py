import os
import chromadb
from chromadb.utils import embedding_functions

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
POLICY_FILE = os.path.join(BASE_DIR, "chinh_sach_nha_khoa.txt")
VECTOR_DB_DIR = os.path.join(BASE_DIR, "my_vector_db")

def build_vector_db():
    print("[RAG Builder] Dang nap du lieu chinh sach phong kham nha khoa vao ChromaDB...")
    
    if not os.path.exists(POLICY_FILE):
        print(f"[RAG Builder] Khong tim thấy file {POLICY_FILE}")
        return

    with open(POLICY_FILE, "r", encoding="utf-8") as f:
        content = f.read()

    # Chia đoạn theo phần
    sections = [sec.strip() for sec in content.split("====================================================================") if sec.strip()]
    documents = []
    metadatas = []
    ids = []

    doc_idx = 1
    for sec in sections:
        lines = [l.strip() for l in sec.split("\n") if l.strip()]
        if not lines:
            continue
        title = lines[0]
        body = "\n".join(lines[1:]) if len(lines) > 1 else title
        
        documents.append(f"{title}\n{body}")
        metadatas.append({"title": title})
        ids.append(f"policy_doc_{doc_idx}")
        doc_idx += 1

    # Tạo ChromaDB client
    chroma_client = chromadb.PersistentClient(path=VECTOR_DB_DIR)
    
    # Khởi tạo embedding function bằng sentence-transformers
    embedding_func = embedding_functions.SentenceTransformerEmbeddingFunction(
        model_name="all-MiniLM-L6-v2"
    )

    # Thêm collection
    try:
        chroma_client.delete_collection(name="dental_policies")
    except Exception:
        pass

    collection = chroma_client.create_collection(
        name="dental_policies",
        embedding_function=embedding_func
    )

    collection.add(
        documents=documents,
        metadatas=metadatas,
        ids=ids
    )

    print(f"[RAG Builder] Da nap thanh cong {len(documents)} muc chinh sach nha khoa vao Vector DB at: {VECTOR_DB_DIR}")

if __name__ == "__main__":
    build_vector_db()
