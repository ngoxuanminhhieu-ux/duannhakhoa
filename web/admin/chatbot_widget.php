<!-- Floating Admin AI Chatbot Widget -->
<style>
/* Admin Chatbot Floating Button & Window */
.admin-chatbot-toggler {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #071527 0%, #0c4a6e 50%, #0284c7 100%);
    border: 2px solid #38bdf8;
    border-radius: 50%;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(2, 132, 199, 0.45);
    z-index: 1060;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-chatbot-toggler:hover {
    transform: scale(1.08) translateY(-3px);
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.6);
    border-color: #f59e0b;
}

.admin-chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #f59e0b;
    color: #0f172a;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 10px;
    border: 2px solid #ffffff;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.admin-pulse-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid #38bdf8;
    animation: adminPulse 2.2s infinite;
    pointer-events: none;
}

@keyframes adminPulse {
    0% { transform: scale(0.95); opacity: 0.9; }
    70% { transform: scale(1.4); opacity: 0; }
    100% { transform: scale(1.4); opacity: 0; }
}

/* Chatbot Floating Window */
.admin-chatbot-window {
    position: fixed;
    bottom: 105px;
    right: 30px;
    width: 420px;
    max-width: calc(100vw - 40px);
    height: 580px;
    max-height: calc(100vh - 130px);
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 20px 45px rgba(7, 21, 39, 0.3);
    border: 1px solid #bae6fd;
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 1060;
    animation: adminSlideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-chatbot-window.active {
    display: flex;
}

@keyframes adminSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Header */
.admin-chat-header {
    background: linear-gradient(135deg, #071527 0%, #0c4a6e 60%, #0284c7 100%);
    padding: 16px 20px;
    color: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #38bdf8;
}

.admin-chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.admin-chat-avatar {
    width: 42px;
    height: 42px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid #38bdf8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #38bdf8;
}

.admin-chat-title {
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
}

.admin-chat-status {
    font-size: 0.75rem;
    color: #bae6fd;
    display: flex;
    align-items: center;
    gap: 5px;
}

.admin-chat-status::before {
    content: '';
    display: inline-block;
    width: 7px;
    height: 7px;
    background: #22c55e;
    border-radius: 50%;
}

.admin-chat-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}

.admin-chat-btn {
    background: rgba(255, 255, 255, 0.12);
    border: none;
    color: #ffffff;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    text-decoration: none;
}

.admin-chat-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* Messages Stream */
.admin-chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.admin-msg-item {
    display: flex;
    gap: 10px;
    max-width: 88%;
}

.admin-msg-item.bot {
    align-self: flex-start;
}

.admin-msg-item.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.admin-msg-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
}

.admin-msg-item.bot .admin-msg-avatar {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
}

.admin-msg-item.user .admin-msg-avatar {
    background: #0f172a;
    color: #ffffff;
}

.admin-msg-bubble {
    padding: 12px 16px;
    border-radius: 14px;
    font-size: 0.88rem;
    line-height: 1.6;
    word-break: break-word;
}

.admin-msg-item.bot .admin-msg-bubble {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-top-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}

.admin-msg-item.user .admin-msg-bubble {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border-top-right-radius: 4px;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}

.admin-msg-bubble pre {
    background: #0f172a;
    color: #38bdf8;
    padding: 8px 10px;
    border-radius: 6px;
    font-size: 0.78rem;
    overflow-x: auto;
    margin: 8px 0;
}

.admin-msg-bubble strong {
    color: #0f172a;
}
.admin-msg-item.user .admin-msg-bubble strong {
    color: #ffffff;
}

/* Quick Chips */
.admin-quick-chips {
    padding: 8px 14px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
}

.admin-quick-chips::-webkit-scrollbar {
    display: none;
}

.admin-chip-btn {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #0284c7;
    padding: 5px 11px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.admin-chip-btn:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

/* Input Area */
.admin-chat-input-bar {
    padding: 12px 14px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    align-items: center;
}

.admin-chat-input {
    flex-grow: 1;
    border: 1px solid #cbd5e1;
    border-radius: 25px;
    padding: 10px 16px;
    font-size: 0.88rem;
    outline: none;
    transition: all 0.2s;
}

.admin-chat-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.admin-chat-send-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border: none;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}

.admin-chat-send-btn:hover {
    transform: scale(1.05);
    background: #0284c7;
}

.admin-chat-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Typing indicator */
.admin-typing-dots {
    display: inline-flex;
    gap: 4px;
    padding: 4px 8px;
}
.admin-typing-dots span {
    width: 6px;
    height: 6px;
    background: #0284c7;
    border-radius: 50%;
    animation: adminTyping 1.4s infinite ease-in-out both;
}
.admin-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.admin-typing-dots span:nth-child(2) { animation-delay: -0.16s; }
@keyframes adminTyping {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
</style>

<!-- Widget HTML -->
<div class="admin-chatbot-toggler" id="adminChatbotToggler" title="Trợ lý AI Quản Trị Premier Dental">
    <div class="admin-pulse-ring"></div>
    <span class="admin-chatbot-badge">AI</span>
    <i class="fas fa-robot"></i>
</div>

<div class="admin-chatbot-window" id="adminChatbotWindow">
    <div class="admin-chat-header">
        <div class="admin-chat-header-info">
            <div class="admin-chat-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div>
                <h6 class="admin-chat-title">Trợ Lý AI Quản Trị Premier</h6>
                <div class="admin-chat-status">Trực tuyến • Hỗ trợ vận hành 24/7</div>
            </div>
        </div>
        <div class="admin-chat-actions">
            <a href="ai_assistant.php" class="admin-chat-btn" title="Mở trang Trợ lý AI toàn màn hình">
                <i class="fas fa-expand-alt"></i>
            </a>
            <button type="button" class="admin-chat-btn" id="adminClearChatBtn" title="Xóa lịch sử trò chuyện">
                <i class="fas fa-trash-alt"></i>
            </button>
            <button type="button" class="admin-chat-btn" id="adminCloseChatBtn" title="Đóng cửa sổ">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="admin-chat-messages" id="adminChatMessages">
        <div class="admin-msg-item bot">
            <div class="admin-msg-avatar"><i class="fas fa-robot"></i></div>
            <div class="admin-msg-bubble">
                Xin chào Quản trị viên! Tôi là <strong>Trợ lý AI Điều Hành Premier Dental</strong>.<br><br>
                Tôi có thể hỗ trợ bạn:<br>
                • 📊 <strong>Báo cáo số liệu hôm nay</strong> (lịch hẹn, bệnh nhân mới)<br>
                • ⏳ <strong>Tra cứu ca khám chờ duyệt</strong><br>
                • 🔍 <strong>Tìm kiếm hồ sơ bệnh nhân</strong> theo tên hoặc SĐT<br>
                • 👨‍⚕️ <strong>Xem danh sách bác sĩ & lịch trực</strong><br>
                • 📝 <strong>Soạn tin nhắn SMS/Zalo nhắc hẹn tự động</strong><br>
                • 💡 <strong>Quy trình & hướng dẫn hậu phẫu chuẩn y khoa</strong><br><br>
                Bạn cần kiểm tra hoặc xử lý công việc gì ngay bây giờ?
            </div>
        </div>
    </div>

    <!-- Quick Chips -->
    <div class="admin-quick-chips" id="adminQuickChips">
        <button type="button" class="admin-chip-btn" data-query="Thống kê tình hình phòng khám hôm nay">📊 Thống kê hôm nay</button>
        <button type="button" class="admin-chip-btn" data-query="Danh sách lịch hẹn chờ duyệt">⏳ Lịch chờ duyệt</button>
        <button type="button" class="admin-chip-btn" data-query="Danh sách bác sĩ và lịch trực">👨‍⚕️ Bác sĩ trực</button>
        <button type="button" class="admin-chip-btn" data-query="Mẫu tin nhắn nhắc hẹn bệnh nhân">📝 Soạn tin nhắc hẹn</button>
        <button type="button" class="admin-chip-btn" data-query="Hướng dẫn chăm sóc hậu phẫu cấy Implant và nhổ răng">💡 Hướng dẫn hậu phẫu</button>
    </div>

    <div class="admin-chat-input-bar">
        <input type="text" class="admin-chat-input" id="adminChatInput" placeholder="Hỏi số liệu, lịch hẹn, bệnh nhân, bác sĩ..." autocomplete="off">
        <button type="button" class="admin-chat-send-btn" id="adminChatSendBtn" title="Gửi tin nhắn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
(function() {
    const toggler = document.getElementById('adminChatbotToggler');
    const windowEl = document.getElementById('adminChatbotWindow');
    const closeBtn = document.getElementById('adminCloseChatBtn');
    const clearBtn = document.getElementById('adminClearChatBtn');
    const inputEl = document.getElementById('adminChatInput');
    const sendBtn = document.getElementById('adminChatSendBtn');
    const msgBox = document.getElementById('adminChatMessages');
    const chipsBox = document.getElementById('adminQuickChips');

    if (!toggler || !windowEl) return;

    // Toggle window
    toggler.addEventListener('click', () => {
        windowEl.classList.toggle('active');
        if (windowEl.classList.contains('active')) {
            inputEl.focus();
            scrollToBottom();
        }
    });

    closeBtn.addEventListener('click', () => {
        windowEl.classList.remove('active');
    });

    clearBtn.addEventListener('click', () => {
        if (confirm('Bạn có muốn xóa toàn bộ tin nhắn hiện tại?')) {
            msgBox.innerHTML = `
                <div class="admin-msg-item bot">
                    <div class="admin-msg-avatar"><i class="fas fa-robot"></i></div>
                    <div class="admin-msg-bubble">
                        Đã làm mới cuộc hội thoại. Tôi sẵn sàng hỗ trợ bạn quản lý phòng khám!
                    </div>
                </div>
            `;
        }
    });

    // Handle Quick Chips
    chipsBox.addEventListener('click', (e) => {
        const btn = e.target.closest('.admin-chip-btn');
        if (!btn) return;
        const query = btn.getAttribute('data-query');
        if (query) {
            sendMessage(query);
        }
    });

    // Send Message
    function sendMessage(text) {
        const msg = text || inputEl.value.trim();
        if (!msg) return;

        // Append user bubble
        appendMessage('user', msg);
        if (!text) inputEl.value = '';

        // Show typing indicator
        const typingId = 'typing_' + Date.now();
        const typingEl = document.createElement('div');
        typingEl.className = 'admin-msg-item bot';
        typingEl.id = typingId;
        typingEl.innerHTML = `
            <div class="admin-msg-avatar"><i class="fas fa-robot"></i></div>
            <div class="admin-msg-bubble">
                <div class="admin-typing-dots"><span></span><span></span><span></span></div>
            </div>
        `;
        msgBox.appendChild(typingEl);
        scrollToBottom();

        // Send to backend
        sendBtn.disabled = true;

        fetch('admin_chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: msg })
        })
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById(typingId);
            if (el) el.remove();

            if (data && data.success && data.reply) {
                appendMessage('bot', data.reply);
            } else {
                appendMessage('bot', '⚠️ ' + (data.error || 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.'));
            }
        })
        .catch(err => {
            const el = document.getElementById(typingId);
            if (el) el.remove();
            appendMessage('bot', '⚠️ Không thể kết nối đến máy chủ: ' + err.message);
        })
        .finally(() => {
            sendBtn.disabled = false;
            inputEl.focus();
        });
    }

    sendBtn.addEventListener('click', () => sendMessage());
    inputEl.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function appendMessage(sender, text) {
        const item = document.createElement('div');
        item.className = 'admin-msg-item ' + sender;

        const avatar = document.createElement('div');
        avatar.className = 'admin-msg-avatar';
        avatar.innerHTML = sender === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user-shield"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'admin-msg-bubble';

        // Simple markdown parsing
        let formatted = escapeHtml(text);
        // Code blocks
        formatted = formatted.replace(/```([a-z]*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
        // Bold
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Italic
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Links
        formatted = formatted.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" class="text-primary">$1</a>');
        // Line breaks
        formatted = formatted.replace(/\n/g, '<br>');

        bubble.innerHTML = formatted;

        item.appendChild(avatar);
        item.appendChild(bubble);
        msgBox.appendChild(item);
        scrollToBottom();
    }

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function scrollToBottom() {
        setTimeout(() => {
            msgBox.scrollTop = msgBox.scrollHeight;
        }, 50);
    }
})();
</script>
