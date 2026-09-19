import streamlit as st
from pipeline import CustomerServicePipeline

# Cấu hình giao diện Streamlit
st.set_page_config(
    page_title="TechStore AI Assistant",
    page_icon="🤖",
    layout="wide",
    initial_sidebar_state="expanded"
)

# Custom CSS cho giao diện thêm đẹp mắt và hiện đại
st.markdown("""
<style>
    .main-title {
        font-size: 2.2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0px;
    }
    .sub-title {
        color: #666;
        font-size: 1.05rem;
        margin-bottom: 25px;
    }
    .stSidebar {
        background-color: #f8f9fa;
    }
    .log-card {
        background-color: #e3f2fd;
        border-left: 4px solid #1e88e5;
        padding: 10px 14px;
        border-radius: 4px;
        margin-bottom: 10px;
        font-size: 0.88rem;
        font-family: monospace;
    }
</style>
""", unsafe_allow_html=True)

# Khởi tạo Pipeline
@st.cache_resource
def get_pipeline():
    return CustomerServicePipeline()

pipeline = get_pipeline()

# Khởi tạo session state
if "messages" not in st.session_state:
    st.session_state.messages = []

if "pipeline_logs" not in st.session_state:
    st.session_state.pipeline_logs = []

# --- SIDEBAR: HIỂN THỊ LOGS PIPELINE & CẤU HÌNH ---
with st.sidebar:
    st.markdown("### ⚙️ Pipeline Execution Logs")
    st.caption("Nhật ký xử lý tuần tự qua các mắt xích của AI Agent:")
    
    if st.session_state.pipeline_logs:
        for log in st.session_state.pipeline_logs:
            if "Step 1" in log:
                st.success(f"🎯 {log}")
            elif "Step 2" in log:
                st.warning(f"🛠️ {log}")
            elif "Step 3" in log:
                st.info(f"✨ {log}")
            else:
                st.code(log)
    else:
        st.info("Chưa có lượt chạy Pipeline nào. Hãy gửi câu hỏi ở khung chat bên phải!")

    st.divider()
    
    st.markdown("### 💡 Câu hỏi gợi ý thử nghiệm:")
    if st.button("💻 1. Tìm laptop RTX dưới 20 triệu", use_container_width=True):
        st.session_state.preset_prompt = "Cho tôi xem laptop card RTX dưới 20 triệu"
    if st.button("💰 2. Tính tiền 2 chiếc Lenovo LOQ P01", use_container_width=True):
        st.session_state.preset_prompt = "Tôi muốn mua 2 chiếc Lenovo LOQ P01 thì hết bao nhiêu tiền?"
    if st.button("📋 3. Hỏi chính sách bảo hành & đổi trả", use_container_width=True):
        st.session_state.preset_prompt = "Chính sách đổi trả và bảo hành máy tại cửa hàng như thế nào?"

    st.divider()
    if st.button("🗑️ Xóa lịch sử hội thoại", use_container_width=True):
        st.session_state.messages = []
        st.session_state.pipeline_logs = []
        st.rerun()

# --- MAIN CONTENT ---
st.markdown('<p class="main-title">🤖 Chatbot AI Tư Vấn Khách Hàng TechStore</p>', unsafe_allow_html=True)
st.markdown('<p class="sub-title">Ứng dụng AI Agent tư vấn bán hàng theo quy trình Pipeline xử lý từng bước (Sequential Pipeline Architecture)</p>', unsafe_allow_html=True)

# Hiển thị lịch sử chat
for msg in st.session_state.messages:
    with st.chat_message(msg["role"]):
        st.write(msg["content"])

# Xử lý input khi người dùng nhập câu hỏi hoặc chọn preset prompt
prompt_input = st.chat_input("Nhập câu hỏi (VD: Cho tôi xem laptop card RTX dưới 20 triệu)...")

# Check nếu có preset prompt
if "preset_prompt" in st.session_state and st.session_state.preset_prompt:
    prompt = st.session_state.preset_prompt
    st.session_state.preset_prompt = None
else:
    prompt = prompt_input

if prompt:
    # 1. Thêm câu hỏi vào lịch sử chat
    st.session_state.messages.append({"role": "user", "content": prompt})
    with st.chat_message("user"):
        st.write(prompt)

    # 2. Xử lý qua Pipeline
    with st.chat_message("assistant"):
        with st.spinner("Đang đẩy dữ liệu qua đường ống Pipeline (Intent → Tool/RAG → Format)..."):
            answer, logs = pipeline.run_pipeline(prompt)
            st.write(answer)
            
            # Cập nhật state
            st.session_state.messages.append({"role": "assistant", "content": answer})
            st.session_state.pipeline_logs = logs
            st.rerun()
