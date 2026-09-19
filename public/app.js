/* ==========================================================================
   NHA KHOA QUỐC TẾ PREMIER CLINIC - JAVASCRIPT LOGIC
   ========================================================================== */

let globalServices = [];
let globalDoctors = [];
let globalPrices = {};

// Initialize Page Data
document.addEventListener("DOMContentLoaded", () => {
    initApp();
});

async function initApp() {
    try {
        await Promise.all([
            fetchServices(),
            fetchDoctors(),
            fetchPrices(),
            fetchClinicInfo()
        ]);
        
        // Default set minimum booking date to today
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('b_date');
        if (dateInput) {
            dateInput.min = today;
            dateInput.value = today;
        }

    } catch (err) {
        console.error("Lỗi khi tải dữ liệu khởi tạo:", err);
    }
}

// SPA Navigation Switcher
function switchNav(sectionId) {
    const sections = document.querySelectorAll('.page-section');
    sections.forEach(sec => sec.classList.remove('active'));

    const targetSec = document.getElementById(`${sectionId}-section`);
    if (targetSec) {
        targetSec.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => link.classList.remove('active'));
    
    const activeLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }

    // Close mobile menu if open
    document.getElementById('navMenu').classList.remove('active');

    // Load Admin data if switching to admin
    if (sectionId === 'admin') {
        loadAdminDashboard();
    }
}

function toggleMobileMenu() {
    document.getElementById('navMenu').classList.toggle('active');
}

// --- DATA FETCHING & RENDERING ---

async function fetchServices() {
    try {
        const res = await fetch('/api/services');
        globalServices = await res.json();
        
        // Render Home Services (Top 6)
        renderServices(globalServices.slice(0, 6), 'home-services-grid');
        
        // Render Full Services Page
        renderServices(globalServices, 'full-services-grid');
        
        // Populate Select Dropdowns
        populateServiceSelect();
    } catch (e) {
        console.error("Lỗi fetch services:", e);
    }
}

async function fetchDoctors() {
    try {
        const res = await fetch('/api/doctors');
        globalDoctors = await res.json();
        
        renderDoctors(globalDoctors, 'home-doctors-grid');
        renderDoctors(globalDoctors, 'full-doctors-grid');
        populateDoctorSelect();
    } catch (e) {
        console.error("Lỗi fetch doctors:", e);
    }
}

async function fetchPrices() {
    try {
        const res = await fetch('/api/prices');
        globalPrices = await res.json();
        renderPriceTable(globalPrices);
    } catch (e) {
        console.error("Lỗi fetch prices:", e);
    }
}

async function fetchClinicInfo() {
    try {
        const res = await fetch('/api/clinic');
        const info = await res.json();
        // Update header or footer if needed
    } catch (e) {
        console.error("Lỗi fetch clinic info:", e);
    }
}

// Render Services Grid
function renderServices(services, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!services || services.length === 0) {
        container.innerHTML = `<p class="text-center">Không tìm thấy dịch vụ nào phù hợp.</p>`;
        return;
    }

    container.innerHTML = services.map(s => `
        <div class="service-card">
            <div class="service-thumb">
                <img src="${s.image || 'https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=600'}" alt="${s.name}">
                <span class="service-category-badge">${s.category}</span>
            </div>
            <div class="service-body">
                <h3 class="service-title">${s.name}</h3>
                <p class="service-desc">${s.description}</p>
                <div class="service-meta">
                    <div>
                        <span class="service-price">${s.price_range}</span>
                    </div>
                </div>
                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button class="btn btn-outline" style="flex:1; padding:8px;" onclick="openServiceModal('${s.id}')">
                        <i class="fa-solid fa-circle-info"></i> Chi Tiết
                    </button>
                    <button class="btn btn-primary" style="flex:1; padding:8px;" onclick="openBookingModal('${s.id}')">
                        <i class="fa-solid fa-calendar-plus"></i> Đặt Lịch
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Render Doctors Grid
function renderDoctors(doctors, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = doctors.map(d => `
        <div class="doctor-card">
            <div class="doctor-img-box">
                <img src="${d.image || 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?w=600'}" alt="${d.name}">
            </div>
            <div class="doctor-info">
                <h3 class="doctor-name">${d.name}</h3>
                <p class="doctor-title">${d.title}</p>
                <p class="doctor-exp"><i class="fa-solid fa-award"></i> ${d.experience}</p>
                <button class="btn btn-outline btn-block" style="padding:8px 12px; font-size:0.88rem;" onclick="openBookingModal('', '${d.id}')">
                    <i class="fa-solid fa-user-doctor"></i> Đặt Lịch Với BS
                </button>
            </div>
        </div>
    `).join('');
}

// Render Categorized Price Table
function renderPriceTable(categorizedPrices) {
    const container = document.getElementById('priceTableContainer');
    if (!container) return;

    let html = '';
    for (const [key, group] of Object.entries(categorizedPrices)) {
        if (!group.items || group.items.length === 0) continue;

        html += `
            <div class="price-group-card" data-category="${key}">
                <div class="price-group-header">
                    <i class="fa-solid fa-tooth"></i> ${group.name}
                </div>
                <table class="price-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Tên Dịch Vụ Nha Khoa</th>
                            <th style="width: 25%;">Chi Phí Tham Khảo</th>
                            <th style="width: 15%;">Thời Gian</th>
                            <th style="width: 15%;">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${group.items.map(item => `
                            <tr class="price-row" data-name="${item.name.toLowerCase()}">
                                <td>
                                    <strong>${item.name}</strong>
                                    ${item.warranty ? `<br><small style="color:#64748B;"><i class="fa-solid fa-shield"></i> ${item.warranty}</small>` : ''}
                                </td>
                                <td class="price-val">${item.price_range}</td>
                                <td>${item.duration || '30-45 phút'}</td>
                                <td>
                                    <button class="btn btn-primary" style="padding: 6px 14px; font-size: 0.82rem;" onclick="openBookingModal('${item.id}')">
                                        Đặt Lịch
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }

    container.innerHTML = html;
}

// Filter Services by Category Tabs
function filterServices(categorySlug, buttonEl) {
    const tabBtns = document.querySelectorAll('.filter-tabs .tab-btn');
    tabBtns.forEach(btn => btn.classList.remove('active'));
    if (buttonEl) buttonEl.classList.add('active');

    if (categorySlug === 'all') {
        renderServices(globalServices, 'full-services-grid');
    } else {
        const filtered = globalServices.filter(s => s.category_slug === categorySlug || s.category.toLowerCase() === categorySlug.toLowerCase());
        renderServices(filtered, 'full-services-grid');
    }
}

// Search Price Table Rows
function searchPriceTable() {
    const input = document.getElementById('priceSearchInput');
    const query = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('.price-row');

    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        if (name.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Populate Select Dropdowns
function populateServiceSelect() {
    const select = document.getElementById('b_service');
    if (!select) return;

    select.innerHTML = globalServices.map(s => `
        <option value="${s.id}">${s.name} (${s.price_range})</option>
    `).join('');
}

function populateDoctorSelect() {
    const select = document.getElementById('b_doctor');
    if (!select) return;

    select.innerHTML = globalDoctors.map(d => `
        <option value="${d.id}">${d.name} - ${d.specialty}</option>
    `).join('');
}

// --- MODAL DIALOGS ---

function openBookingModal(serviceId = '', doctorId = '') {
    const modal = document.getElementById('bookingModal');
    if (modal) {
        modal.classList.add('active');
        if (serviceId) {
            document.getElementById('b_service').value = serviceId;
        }
        if (doctorId) {
            document.getElementById('b_doctor').value = doctorId;
        }
    }
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    if (modal) modal.classList.remove('active');
}

function openServiceModal(serviceId) {
    const service = globalServices.find(s => s.id.upper() === serviceId.upper() || s.id === serviceId);
    if (!service) return;

    const modal = document.getElementById('serviceModal');
    const content = document.getElementById('serviceModalContent');

    content.innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
            <div>
                <img src="${service.image}" alt="${service.name}" style="width:100%; border-radius:16px;">
            </div>
            <div>
                <span class="sub-badge">${service.category}</span>
                <h2>${service.name}</h2>
                <p style="font-size:1.2rem; font-weight:800; color:var(--primary); margin: 10px 0;">${service.price_range}</p>
                <p style="color:var(--muted); margin-bottom: 20px;">${service.description}</p>
                
                <p><strong><i class="fa-solid fa-clock"></i> Thời gian:</strong> ${service.duration || '30-45 phút'}</p>
                <p><strong><i class="fa-solid fa-shield"></i> Bảo hành:</strong> ${service.warranty || 'Định kỳ tái khám'}</p>

                ${service.procedure ? `
                    <h4 style="margin-top:20px; margin-bottom:10px;">Quy Trình Thực Hiện:</h4>
                    <ul style="padding-left:20px; font-size:0.9rem; color:var(--slate);">
                        ${service.procedure.map(p => `<li style="margin-bottom:6px;">${p}</li>`).join('')}
                    </ul>
                ` : ''}

                <button class="btn btn-primary btn-block margin-top-lg" onclick="closeServiceModal(); openBookingModal('${service.id}');">
                    <i class="fa-solid fa-calendar-check"></i> Đặt Lịch Khám Dịch Vụ Này
                </button>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.remove('active');
}

// --- FORM SUBMISSIONS ---

async function submitBookingForm(e) {
    e.preventDefault();
    const alertBox = document.getElementById('bookingAlert');
    alertBox.className = 'alert-box';
    alertBox.style.display = 'none';

    const payload = {
        name: document.getElementById('b_name').value.trim(),
        phone: document.getElementById('b_phone').value.trim(),
        email: document.getElementById('b_email').value.trim(),
        service_id: document.getElementById('b_service').value,
        doctor_id: document.getElementById('b_doctor').value,
        date: document.getElementById('b_date').value,
        time: document.getElementById('b_time').value,
        notes: document.getElementById('b_notes').value.trim()
    };

    try {
        const res = await fetch('/api/appointments', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (res.ok && data.success) {
            alertBox.className = 'alert-box success';
            alertBox.innerHTML = `🎉 ${data.message} Vui lòng lưu mã <strong>[${data.appointment_id}]</strong> để kiểm tra.`;
            document.getElementById('bookingForm').reset();
            setTimeout(() => {
                closeBookingModal();
            }, 3000);
        } else {
            alertBox.className = 'alert-box error';
            alertBox.innerText = `⚠️ Lỗi: ${data.detail || 'Không thể đặt lịch. Vui lòng thử lại!'}`;
        }
    } catch (err) {
        alertBox.className = 'alert-box error';
        alertBox.innerText = `⚠️ Có lỗi kết nối máy chủ. Vui lòng thử lại sau!`;
    }
}

async function submitContactForm(e) {
    e.preventDefault();
    const alertBox = document.getElementById('contactAlert');
    alertBox.className = 'alert-box';

    const payload = {
        name: document.getElementById('c_name').value.trim(),
        phone: document.getElementById('c_phone').value.trim(),
        email: document.getElementById('c_email').value.trim(),
        subject: document.getElementById('c_subject').value.trim(),
        message: document.getElementById('c_message').value.trim()
    };

    try {
        const res = await fetch('/api/contacts', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok) {
            alertBox.className = 'alert-box success';
            alertBox.innerText = `✅ ${data.message}`;
            document.getElementById('contactForm').reset();
        } else {
            alertBox.className = 'alert-box error';
            alertBox.innerText = `⚠️ Không thể gửi thông tin. Vui lòng kiểm tra lại.`;
        }
    } catch (err) {
        alertBox.className = 'alert-box error';
        alertBox.innerText = `⚠️ Có lỗi kết nối máy chủ.`;
    }
}

// --- ADMIN DASHBOARD ---

async function loadAdminDashboard() {
    try {
        // Fetch Metrics
        const statsRes = await fetch('/api/stats');
        const stats = await statsRes.json();

        document.getElementById('adm_total_patients').innerText = stats.total_patients || 0;
        document.getElementById('adm_total_apps').innerText = stats.total_appointments || 0;
        document.getElementById('adm_today_apps').innerText = stats.today_appointments || 0;
        document.getElementById('adm_total_services').innerText = stats.total_services || 0;

        // Fetch Appointments
        loadAdminAppointments();
        loadAdminServices();
        loadAdminDoctors();
        loadAdminContacts();
    } catch (e) {
        console.error("Lỗi load admin dashboard:", e);
    }
}

async function loadAdminAppointments() {
    const status = document.getElementById('adminStatusFilter').value;
    const search = document.getElementById('adminAppSearch').value;

    let url = `/api/appointments?status=${status}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    try {
        const res = await fetch(url);
        const apps = await res.json();
        const tbody = document.getElementById('adminAppTableBody');

        if (!apps || apps.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center">Không có lịch hẹn nào.</td></tr>`;
            return;
        }

        tbody.innerHTML = apps.map(a => `
            <tr>
                <td><strong>${a.id}</strong></td>
                <td>${a.name}</td>
                <td>${a.phone}</td>
                <td>${a.service_name}</td>
                <td>${a.doctor_name}</td>
                <td>${a.date}</td>
                <td>${a.time}</td>
                <td>
                    <span class="status-badge ${getStatusClass(a.status)}">${a.status}</span>
                </td>
                <td>
                    <select style="padding:4px 8px; font-size:0.8rem;" onchange="updateAppStatus('${a.id}', this.value)">
                        <option value="">-- Đổi trạng thái --</option>
                        <option value="Đã xác nhận">Đã xác nhận</option>
                        <option value="Đã hoàn thành">Đã hoàn thành</option>
                        <option value="Từ chối">Từ chối</option>
                        <option value="Chờ xác nhận">Chờ xác nhận</option>
                    </select>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        console.error("Lỗi fetch admin appointments:", e);
    }
}

function getStatusClass(status) {
    if (status === 'Đã xác nhận') return 'confirmed';
    if (status === 'Đã hoàn thành') return 'completed';
    if (status === 'Từ chối') return 'cancelled';
    return 'pending';
}

async function updateAppStatus(appId, newStatus) {
    if (!newStatus) return;
    try {
        const res = await fetch(`/api/appointments/${appId}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        });
        if (res.ok) {
            loadAdminDashboard();
        }
    } catch (e) {
        alert("Không thể cập nhật trạng thái lịch hẹn.");
    }
}

function loadAdminServices() {
    const tbody = document.getElementById('adminServiceTableBody');
    if (!tbody) return;
    tbody.innerHTML = globalServices.map(s => `
        <tr>
            <td><strong>${s.id}</strong></td>
            <td>${s.name}</td>
            <td>${s.category}</td>
            <td><strong style="color:var(--primary);">${s.price_range}</strong></td>
            <td>${s.warranty || 'Định kỳ'}</td>
        </tr>
    `).join('');
}

function loadAdminDoctors() {
    const tbody = document.getElementById('adminDoctorTableBody');
    if (!tbody) return;
    tbody.innerHTML = globalDoctors.map(d => `
        <tr>
            <td><strong>${d.id}</strong></td>
            <td>${d.name}</td>
            <td>${d.title}</td>
            <td>${d.specialty}</td>
            <td>${d.experience}</td>
        </tr>
    `).join('');
}

async function loadAdminContacts() {
    try {
        const res = await fetch('/api/contacts');
        const contacts = await res.json();
        const tbody = document.getElementById('adminContactTableBody');
        if (!tbody) return;

        if (!contacts || contacts.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center">Chưa có tin nhắn liên hệ nào.</td></tr>`;
            return;
        }

        tbody.innerHTML = contacts.map(c => `
            <tr>
                <td><strong>${c.id}</strong></td>
                <td>${c.name}</td>
                <td>${c.phone}</td>
                <td>${c.email || 'N/A'}</td>
                <td>${c.subject}</td>
                <td>${c.message}</td>
                <td>${c.created_at}</td>
            </tr>
        `).join('');
    } catch (e) {
        console.error("Lỗi fetch admin contacts:", e);
    }
}

function switchAdminTab(tabName, btnEl) {
    const tabs = document.querySelectorAll('.admin-tab');
    tabs.forEach(t => t.classList.remove('active'));
    btnEl.classList.add('active');

    const contents = document.querySelectorAll('.admin-tab-content');
    contents.forEach(c => c.classList.remove('active'));

    const target = document.getElementById(`admin-tab-${tabName}`);
    if (target) target.classList.add('active');
}

// --- FLOATING CHATBOT WIDGET ("NHA KHOA AI ASSISTANT") ---

function toggleChatbot() {
    const windowEl = document.getElementById('chatWindow');
    windowEl.classList.toggle('active');
    
    // Hide notification badge once opened
    const badge = document.querySelector('.trigger-badge');
    if (badge) badge.style.display = 'none';

    if (windowEl.classList.contains('active')) {
        document.getElementById('chatInput').focus();
    }
}

function openChatbot() {
    const windowEl = document.getElementById('chatWindow');
    if (!windowEl.classList.contains('active')) {
        windowEl.classList.add('active');
    }
    document.getElementById('chatInput').focus();
}

function handleChatKeyPress(e) {
    if (e.key === 'Enter') {
        sendChatMessage();
    }
}

function sendPreset(promptText) {
    document.getElementById('chatInput').value = promptText;
    sendChatMessage();
}

async function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const messageText = input.value.trim();
    if (!messageText) return;

    // Append User Message to UI
    appendMessage(messageText, 'user');
    input.value = '';

    // Show Loading Spinner
    showChatLoading(true);

    try {
        const res = await fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: messageText })
        });

        const data = await res.json();
        showChatLoading(false);

        if (res.ok && data.reply) {
            appendMessage(data.reply, 'assistant');
        } else {
            appendMessage("⚠️ Rất tiếc, trợ lý AI hiện đang bận. Quý khách vui lòng gọi Hotline 1900 6868 để được hỗ trợ trực tiếp!", 'assistant');
        }
    } catch (e) {
        showChatLoading(false);
        appendMessage("⚠️ Lỗi kết nối đến máy chủ AI Chatbot.", 'assistant');
    }
}

function appendMessage(text, role) {
    const messagesContainer = document.getElementById('chatMessages');
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${role}`;

    // Format markdown bold & linebreaks
    let formattedText = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\n/g, '<br>');

    bubble.innerHTML = `
        <div class="msg-content">${formattedText}</div>
        <div class="msg-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
    `;

    messagesContainer.appendChild(bubble);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showChatLoading(show) {
    const loader = document.getElementById('chatLoading');
    const messagesContainer = document.getElementById('chatMessages');
    if (show) {
        loader.classList.add('active');
    } else {
        loader.classList.remove('active');
    }
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
