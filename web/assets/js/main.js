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
      appendMessage('bot', '<i class="fas fa-ellipsis-h fa-pulse me-2"></i> AI đang suy nghĩ...', loadingId);

      // Determine Chatbot API path based on location
      const chatApiPath = window.location.pathname.includes('/pages/') 
        ? '../chatbot/chat.php' 
        : (window.location.pathname.includes('/admin/') ? '../chatbot/chat.php' : 'chatbot/chat.php');

      fetch(chatApiPath, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
      })
      .then(response => response.json())
      .then(data => {
        // Remove loading message
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();

        if (data.success && data.reply) {
          appendMessage('bot', data.reply);
        } else {
          appendMessage('bot', data.error || 'Xin lỗi, không thể kết nối đến AI. Vui lòng thử lại sau.');
        }
      })
      .catch(err => {
        const loadingElement = document.getElementById(loadingId);
        if (loadingElement) loadingElement.remove();
        appendMessage('bot', 'Đã xảy ra lỗi kết nối. Vui lòng thử lại sau!');
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

      // Format line breaks
      msgDiv.innerHTML = text.replace(/\n/g, '<br>');

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
