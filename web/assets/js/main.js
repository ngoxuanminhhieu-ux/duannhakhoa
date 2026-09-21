/* ==========================================================================
   Dental Clinic Frontend JavaScript
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

  // 1. Chatbot Widget Toggle
  const chatbotToggler = document.getElementById('chatbotToggler');
  const chatbotWindow = document.getElementById('chatbotWindow');
  const closeChatbot = document.getElementById('closeChatbot');
  const chatbotInput = document.getElementById('chatbotInput');
  const sendChatbotBtn = document.getElementById('sendChatbotBtn');
  const chatbotMessages = document.getElementById('chatbotMessages');

  if (chatbotToggler && chatbotWindow) {
    chatbotToggler.addEventListener('click', function () {
      chatbotWindow.classList.toggle('active');
      if (chatbotWindow.classList.contains('active')) {
        chatbotInput.focus();
      }
    });

    if (closeChatbot) {
      closeChatbot.addEventListener('click', function () {
        chatbotWindow.classList.remove('active');
      });
    }

    // Send chatbot message handler
    function sendChatMessage() {
      const message = chatbotInput.value.trim();
      if (!message) return;

      // Append User message
      appendMessage('user', message);
      chatbotInput.value = '';

      // Append Loading indicator
      const loadingId = 'loading-' + Date.now();
      appendMessage('bot', '<i class="fas fa-spinner fa-spin me-2"></i> AI đang suy nghĩ...', loadingId);

      // Determine Chatbot API path based on location or global config
      let chatApiPath = window.CHAT_API_PATH || 'chatbot/chat.php';
      const pathname = window.location.pathname;
      if (!window.CHAT_API_PATH) {
        if (pathname.includes('/pages/') || pathname.includes('/admin/')) {
          chatApiPath = '../chatbot/chat.php';
        } else if (pathname.endsWith('/') || pathname.endsWith('/index.php')) {
          chatApiPath = 'chatbot/chat.php';
        }
      }

      fetch(chatApiPath, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        // Remove loading message
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();

        if (data && data.reply) {
          appendMessage('bot', data.reply);
        } else if (data && data.error) {
          appendMessage('bot', '⚠️ ' + data.error);
        } else {
          appendMessage('bot', 'Xin lỗi, không nhận được phản hồi từ trợ lý AI. Vui lòng thử lại sau.');
        }
      })
      .catch(err => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();
        appendMessage('bot', '⚠️ Đã xảy ra lỗi kết nối. Vui lòng kiểm tra lại mạng hoặc thử lại sau!');
      });
    }

    if (sendChatbotBtn) {
      sendChatbotBtn.addEventListener('click', sendChatMessage);
    }

    if (chatbotInput) {
      chatbotInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          sendChatMessage();
        }
      });
    }

    function appendMessage(sender, text, customId = null) {
      const msgDiv = document.createElement('div');
      msgDiv.classList.add('chat-msg', sender);
      if (customId) msgDiv.id = customId;

      if (customId && text.includes('fa-spinner')) {
        msgDiv.innerHTML = text;
      } else {
        // Format markdown bold, italic, code, bullets & line breaks
        let formattedText = text
          .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
          .replace(/\*(.*?)\*/g, '<em>$1</em>')
          .replace(/`([^`]+)`/g, '<code>$1</code>')
          .replace(/^[•\-*]\s+(.*)$/gm, '• $1')
          .replace(/\n/g, '<br>');

        msgDiv.innerHTML = formattedText;
      }

      chatbotMessages.appendChild(msgDiv);
      chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
  }

  // 2. Smooth Scroll for internal hash links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId !== '#') {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });

  // 3. Client Form Validation Helper
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      const phoneInput = document.getElementById('phone');
      const emailInput = document.getElementById('email');

      if (phoneInput) {
        const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;
        if (!phoneRegex.test(phoneInput.value.trim())) {
          alert('Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09).');
          e.preventDefault();
          phoneInput.focus();
          return false;
        }
      }
    });
  }

});
