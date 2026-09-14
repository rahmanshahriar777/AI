<!-- NeoERP AI Assistant Floating Widget & Modal -->
<div id="ai-assistant-wrapper">
    <!-- Floating Trigger Button -->
    <button type="button" class="btn btn-primary rounded-pill shadow-lg d-flex align-items-center gap-2"
            id="openAiAssistantBtn"
            data-bs-toggle="modal"
            data-bs-target="#aiAssistantModal"
            style="position: fixed; bottom: 24px; right: 24px; z-index: 1080; padding: 12px 20px; font-weight: 600; box-shadow: 0 8px 24px rgba(115, 103, 240, 0.4) !important;">
        <i class="ti tabler-robot fs-4"></i>
        <span>AI Chatbot</span>
    </button>

    <!-- Modal Dialog (Conversational Chatbot) -->
    <div class="modal fade" id="aiAssistantModal" tabindex="-1" aria-labelledby="aiAssistantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 bg-light d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2 bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                            <i class="ti tabler-robot fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold" id="aiAssistantModalLabel">NeoERP AI Chatbot</h5>
                            <small class="text-muted" style="font-size: 0.75rem;">Powered by Gemini 3.6 Flash &amp; OpenAI Failover</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('ai/chat') }}" class="btn btn-sm btn-outline-primary" title="Open Fullscreen Chat Workspace">
                            <i class="ti tabler-arrows-maximize me-1"></i> Full Page
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body p-0 d-flex flex-column" style="height: 520px;">
                    <!-- Chat Messages Stream -->
                    <div id="modal-chat-history" class="p-3 flex-grow-1 overflow-y-auto d-flex flex-column gap-3 bg-light" style="scroll-behavior: smooth;">
                        <!-- Initial Bot Greeting -->
                        <div class="d-flex flex-column align-self-start" style="max-width: 85%;">
                            <div class="p-3 bg-white rounded-3 shadow-sm border text-dark" style="border-bottom-left-radius: 4px !important; font-size: 0.95rem; line-height: 1.5;">
                                <div class="fw-bold text-primary small mb-1"><i class="ti tabler-sparkles me-1"></i> NeoERP Assistant</div>
                                👋 Hi <strong>{{ Auth::user()->name ?? 'there' }}</strong>! How can I assist you with your ERP tasks today?
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted d-block mb-1 fw-semibold">Quick Starters:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-xs btn-outline-primary modal-quick-btn" data-prompt="Give me a quick status overview of our open leads and enquiries.">
                                            📊 Leads Overview
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary modal-quick-btn" data-prompt="Draft a polite reminder email to a customer regarding an unpaid invoice.">
                                            ✉️ Overdue Reminder
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-info modal-quick-btn" data-prompt="How do I create a new stock requisition in NeoERP?">
                                            📦 Stock Guide
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted ms-2 mt-1" style="font-size: 0.72rem;">{{ now()->format('H:i') }}</small>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div id="modal-typing-indicator" class="d-none px-4 py-2 bg-light border-top">
                        <div class="d-inline-flex align-items-center gap-2 bg-white border px-3 py-1 rounded-pill shadow-sm">
                            <small class="text-muted fw-semibold">AI is typing...</small>
                            <span class="spinner-grow spinner-grow-sm text-primary" role="status"></span>
                            <small class="text-muted ms-1">(<span id="modal-countdown">{{ (int)(config('ai.timeout_ms', 30000)/1000) }}</span>s)</small>
                        </div>
                    </div>

                    <!-- Input Bar -->
                    <div class="p-3 bg-white border-top">
                        <div class="input-group">
                            <textarea id="modal-chat-input" class="form-control" rows="2" placeholder="Type a message or ask a business question... (Press Enter to send)"></textarea>
                            <button type="button" class="btn btn-primary px-3" id="modal-chat-send-btn">
                                <i class="ti tabler-send fs-5"></i>
                            </button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-muted" style="font-size: 0.75rem;">Press <kbd>Enter</kbd> to send, <kbd>Shift+Enter</kbd> for new line</small>
                            <button type="button" class="btn btn-xs btn-link text-muted p-0" id="modal-clear-history">Clear History</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const historyContainer = document.getElementById('modal-chat-history');
    const input = document.getElementById('modal-chat-input');
    const sendBtn = document.getElementById('modal-chat-send-btn');
    const typingIndicator = document.getElementById('modal-typing-indicator');
    const countdownSpan = document.getElementById('modal-countdown');
    const clearHistoryBtn = document.getElementById('modal-clear-history');

    const timeoutMs = {{ (int) config('ai.timeout_ms', 30000) }};
    const timeoutSeconds = Math.round(timeoutMs / 1000);
    const currentUserId = '{{ Auth::id() ?? "guest" }}';
    const modalStorageKey = 'neoerp_modal_chat_' + currentUserId;
    let modalHistory = [];

    // Detect user change and purge old session data if user changed
    try {
        const lastActiveUser = sessionStorage.getItem('neoerp_active_user_id');
        if (lastActiveUser && lastActiveUser !== currentUserId) {
            Object.keys(sessionStorage).forEach(key => {
                if (key.startsWith('neoerp_modal_chat') || key.startsWith('neoerp_chat_history')) {
                    sessionStorage.removeItem(key);
                }
            });
        }
        sessionStorage.setItem('neoerp_active_user_id', currentUserId);
        sessionStorage.removeItem('neoerp_modal_chat');
    } catch(e) {}

    // Load saved conversation for this authenticated user
    try {
        const saved = sessionStorage.getItem(modalStorageKey);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) {
                modalHistory = parsed;
                parsed.forEach(m => appendMessage(m.role, m.text, m.provider, m.latency, m.time, false));
            }
        }
    } catch(e) {}

    // Quick prompt buttons
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('modal-quick-btn')) {
            const prompt = e.target.getAttribute('data-prompt');
            if (input) {
                input.value = prompt;
                sendModalMessage();
            }
        }
    });

    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendModalMessage();
            }
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendModalMessage);
    }

    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener('click', function() {
            modalHistory = [];
            sessionStorage.removeItem(modalStorageKey);
            const items = historyContainer.querySelectorAll('.dynamic-chat-item');
            items.forEach(el => el.remove());
        });
    }

    async function sendModalMessage() {
        const message = input.value.trim();
        if (!message) return;

        const now = new Date();
        const timeStr = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        
        appendMessage('user', message, null, null, timeStr, true);
        modalHistory.push({ role: 'user', text: message });
        saveModalHistory();

        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        typingIndicator.classList.remove('d-none');
        historyContainer.scrollTop = historyContainer.scrollHeight;

        let secondsLeft = timeoutSeconds;
        countdownSpan.innerText = secondsLeft;
        const intervalId = setInterval(() => {
            secondsLeft--;
            countdownSpan.innerText = Math.max(0, secondsLeft);
            if (secondsLeft <= 0) clearInterval(intervalId);
        }, 1000);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeoutMs + 1000);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch('{{ route("ai.chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: message,
                    history: modalHistory.slice(-8),
                    module: 'floating_chatbot'
                }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            clearInterval(intervalId);

            const data = await res.json();

            if (res.ok && data.success) {
                appendMessage('assistant', data.text, data.providerUsed, data.latencyMs, data.timestamp || timeStr, true);
                modalHistory.push({ role: 'model', text: data.text });
                saveModalHistory();
            } else {
                appendMessage('assistant', data.errorMessage || 'AI assistant is temporarily unavailable.', null, null, timeStr, true, true);
            }
        } catch(err) {
            clearTimeout(timeoutId);
            clearInterval(intervalId);
            const msg = (err.name === 'AbortError')
                ? `Request timed out after ${timeoutSeconds}s. Please try a simpler request.`
                : 'AI assistant is temporarily unavailable.';
            appendMessage('assistant', msg, null, null, timeStr, true, true);
        } finally {
            typingIndicator.classList.add('d-none');
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
            historyContainer.scrollTop = historyContainer.scrollHeight;
        }
    }

    function appendMessage(role, text, provider, latency, time, scroll = true, isError = false) {
        const item = document.createElement('div');
        item.className = `dynamic-chat-item d-flex flex-column ${role === 'user' ? 'align-self-end' : 'align-self-start'}`;
        item.style.maxWidth = '85%';

        if (role === 'user') {
            item.innerHTML = `
                <div class="p-3 rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #7367f0 0%, #5e50ee 100%); border-bottom-right-radius: 4px !important; font-size: 0.95rem; line-height: 1.5;">
                    ${escapeHtml(text)}
                </div>
                <small class="text-muted text-end me-2 mt-1" style="font-size: 0.72rem;">${time}</small>
            `;
        } else {
            let adminBadge = '';
            if (provider) {
                adminBadge = `<span class="badge bg-label-info ms-2" style="font-size: 0.68rem;"><i class="ti tabler-cpu me-1"></i>${provider.toUpperCase()} • ${Math.round(latency || 0)}ms</span>`;
            }

            const formatted = isError
                ? `<span class="text-danger"><i class="ti tabler-alert-triangle me-1"></i>${escapeHtml(text)}</span>`
                : formatSimpleMarkdown(text);

            item.innerHTML = `
                <div class="p-3 bg-white rounded-3 shadow-sm border text-dark" style="border-bottom-left-radius: 4px !important; font-size: 0.95rem; line-height: 1.5;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-primary small"><i class="ti tabler-sparkles me-1"></i>NeoERP Bot</span>
                        <div>${adminBadge}</div>
                    </div>
                    <div>${formatted}</div>
                </div>
                <small class="text-muted ms-2 mt-1" style="font-size: 0.72rem;">${time}</small>
            `;
        }

        historyContainer.appendChild(item);
        if (scroll) {
            historyContainer.scrollTop = historyContainer.scrollHeight;
        }
    }

    function saveModalHistory() {
        try {
            sessionStorage.setItem(modalStorageKey, JSON.stringify(modalHistory.slice(-20)));
        } catch(e) {}
    }

    function escapeHtml(str) {
        return str.replace(/[&<>'"]/g, tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag));
    }

    function formatSimpleMarkdown(text) {
        let html = escapeHtml(text);
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        html = html.replace(/^\s*[-*]\s+(.*)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/s, '<ul class="mb-1 ps-3">$1</ul>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }
});
</script>
