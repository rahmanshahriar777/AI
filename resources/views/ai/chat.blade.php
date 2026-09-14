@extends('layouts.master')

@section('title', 'AI Chatbot')

@section('styles')
@parent
<style>
    .chat-history-wrapper {
        height: 520px;
        overflow-y: auto;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        scroll-behavior: smooth;
    }
    .chat-bubble {
        max-width: 80%;
        padding: 0.85rem 1.15rem;
        border-radius: 1rem;
        position: relative;
        word-wrap: break-word;
        line-height: 1.6;
        font-size: 0.95rem;
    }
    .chat-bubble-user {
        align-self: flex-end;
        background: linear-gradient(135deg, #7367f0 0%, #5e50ee 100%);
        color: #ffffff;
        border-bottom-right-radius: 0.2rem;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.25);
    }
    .chat-bubble-assistant {
        align-self: flex-start;
        background: #ffffff;
        color: #2f2b3d;
        border: 1px solid #e7e7e8;
        border-bottom-left-radius: 0.2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .chat-bubble-assistant pre {
        background: #282a36;
        color: #f8f8f2;
        padding: 0.75rem;
        border-radius: 0.35rem;
        overflow-x: auto;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .chat-bubble-assistant code {
        background: rgba(115, 103, 240, 0.08);
        color: #7367f0;
        padding: 0.15rem 0.35rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    .chat-bubble-assistant pre code {
        background: transparent;
        color: inherit;
        padding: 0;
    }
    .typing-dots span {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #7367f0;
        margin: 0 2px;
        animation: typingBounce 1.4s infinite ease-in-out both;
    }
    .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
    .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    .prompt-chip {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
        background: #ffffff;
    }
    .prompt-chip:hover {
        background: #f0f0ff;
        border-color: #7367f0;
        transform: translateY(-1px);
        color: #7367f0;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb & Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="ti tabler-robot fs-2 text-primary"></i> NeoERP AI Chatbot
            </h4>
            <span class="text-muted">Enterprise conversational assistant with real-time ERP operational intelligence.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-success d-flex align-items-center gap-1 py-2 px-3">
                <i class="ti tabler-circle-check fs-6"></i> Gemini 3.6 Flash (Active)
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-chat-btn">
                <i class="ti tabler-trash me-1"></i> Clear Chat
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Sidebar: ERP Insights & Prompt Starters -->
        <div class="col-lg-4 col-md-5">
            <!-- ERP Live Status Widget -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-label-primary py-3 d-flex align-items-center justify-content-between">
                    <span class="fw-bold"><i class="ti tabler-database me-1"></i> Live ERP Context</span>
                    <span class="badge bg-primary rounded-pill">Real-time</span>
                </div>
                <div class="card-body pt-3 pb-2">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="ti tabler-users me-2 text-primary"></i>Total Customers</span>
                            <span class="fw-bold">{{ $stats['customers'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="ti tabler-message-2-question me-2 text-warning"></i>Open Enquiries</span>
                            <span class="badge bg-label-warning rounded-pill">{{ $stats['open_enquiries'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="ti tabler-target me-2 text-info"></i>Active Leads</span>
                            <span class="badge bg-label-info rounded-pill">{{ $stats['active_leads'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted"><i class="ti tabler-briefcase me-2 text-success"></i>In-Progress Jobs</span>
                            <span class="badge bg-label-success rounded-pill">{{ $stats['active_jobs'] ?? 0 }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted"><i class="ti tabler-box me-2 text-secondary"></i>Stock Items</span>
                            <span class="fw-bold">{{ $stats['stock_items'] ?? 0 }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Action Starters -->
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-bold"><i class="ti tabler-sparkles me-1 text-warning"></i> Suggested Prompts</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 rounded prompt-chip" data-prompt="Summarize the current status of all open customer enquiries and suggest next actions.">
                            <div class="fw-semibold small"><i class="ti tabler-message-question me-1 text-primary"></i> Enquiry Status Summary</div>
                            <small class="text-muted">Analyze all pending client enquiries</small>
                        </div>
                        <div class="p-2 rounded prompt-chip" data-prompt="Draft a polite email to follow up on a formal quotation sent to a client last week.">
                            <div class="fw-semibold small"><i class="ti tabler-mail me-1 text-info"></i> Quotation Follow-up Email</div>
                            <small class="text-muted">Professional sales draft</small>
                        </div>
                        <div class="p-2 rounded prompt-chip" data-prompt="How do I create a job schedule and assign workers and vehicles in NeoERP?">
                            <div class="fw-semibold small"><i class="ti tabler-calendar-event me-1 text-success"></i> Job Scheduling Guide</div>
                            <small class="text-muted">ERP workflow walkthrough</small>
                        </div>
                        <div class="p-2 rounded prompt-chip" data-prompt="What are the essential vehicle safety checklist steps before dispatching a driver?">
                            <div class="fw-semibold small"><i class="ti tabler-shield-check me-1 text-warning"></i> Fleet & Safety Checklist</div>
                            <small class="text-muted">Health & safety guidance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Main: Conversational Chat Interface -->
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm d-flex flex-column" style="min-height: 650px;">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                            <i class="ti tabler-robot fs-4"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block leading-tight">NeoERP AI Bot</span>
                            <small class="text-muted">Connected to Gemini 3.6 Flash with OpenAI failover</small>
                        </div>
                    </div>

                    @if(Auth::check() && method_exists(Auth::user(), 'hasAnyRole') && Auth::user()->hasAnyRole(['super_admin', 'admin']))
                        <span class="badge bg-label-secondary" title="Admin Cost & Telemetry Control">
                            <i class="ti tabler-shield me-1"></i> Admin Telemetry Enabled
                        </span>
                    @endif
                </div>

                <!-- Chat Stream Area -->
                <div class="card-body p-0 d-flex flex-column flex-grow-1">
                    <div class="chat-history-wrapper flex-grow-1 m-3" id="chat-history">
                        <!-- Initial Welcome Message -->
                        <div class="chat-bubble chat-bubble-assistant">
                            <div class="fw-bold mb-1 text-primary d-flex align-items-center gap-1">
                                <i class="ti tabler-sparkles"></i> NeoERP AI Assistant
                            </div>
                            <div>
                                👋 Hello <strong>{{ Auth::user()->name ?? 'there' }}</strong>! I am your AI assistant for NeoERP.
                                <br><br>
                                I can assist you with:
                                <ul class="mb-2 ps-3">
                                    <li>Analyzing and summarizing customer leads and enquiries</li>
                                    <li>Drafting professional emails, letters, and client follow-ups</li>
                                    <li>Guiding you through quotations, job workflows, and stock requisitions</li>
                                    <li>Checking fleet inspection and health & safety requirements</li>
                                </ul>
                                Feel free to pick a prompt on the left or type any question below!
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                                <small class="text-muted">{{ now()->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Typing Indicator (Hidden by default) -->
                    <div class="px-4 py-2 d-none" id="typing-indicator">
                        <div class="d-inline-flex align-items-center gap-2 bg-light border py-1 px-3 rounded-pill">
                            <small class="text-muted fw-semibold">NeoERP AI is thinking</small>
                            <div class="typing-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <small class="text-muted ms-2">(<span id="chat-timer">{{ (int)(config('ai.timeout_ms', 30000)/1000) }}</span>s)</small>
                        </div>
                    </div>

                    <!-- Message Input Bar -->
                    <div class="p-3 border-top bg-white">
                        <form id="chat-form" onsubmit="return false;">
                            <div class="input-group">
                                <textarea class="form-control" id="chat-input" rows="2" placeholder="Ask anything about leads, customers, jobs, or draft an email... (Press Enter to send)"></textarea>
                                <button class="btn btn-primary px-4 d-flex align-items-center" type="button" id="send-chat-btn">
                                    <i class="ti tabler-send fs-5 me-1"></i> Send
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Press <kbd>Enter</kbd> to send, <kbd>Shift + Enter</kbd> for a new line.</small>
                                <span id="char-counter" class="text-muted small">0 / 4000</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatHistory = document.getElementById('chat-history');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-chat-btn');
    const typingIndicator = document.getElementById('typing-indicator');
    const chatTimer = document.getElementById('chat-timer');
    const clearBtn = document.getElementById('clear-chat-btn');
    const charCounter = document.getElementById('char-counter');

    const timeoutMs = {{ (int) config('ai.timeout_ms', 30000) }};
    const timeoutSeconds = Math.round(timeoutMs / 1000);

    const currentUserId = '{{ Auth::id() ?? "guest" }}';
    const pageStorageKey = 'neoerp_chat_history_' + currentUserId;

    // In-memory conversation history
    let conversationHistory = [];

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
        sessionStorage.removeItem('neoerp_chat_history');
    } catch(e) {}

    // Load from sessionStorage if available for this specific user
    try {
        const saved = sessionStorage.getItem(pageStorageKey);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed) && parsed.length > 0) {
                conversationHistory = parsed;
                parsed.forEach(msg => appendBubble(msg.role, msg.text, msg.provider, msg.latency, msg.time, false));
            }
        }
    } catch(e) {}

    // Character counter
    chatInput.addEventListener('input', function() {
        charCounter.innerText = `${this.value.length} / 4000`;
    });

    // Enter to submit
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    // Quick prompt chips
    document.querySelectorAll('.prompt-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const prompt = this.getAttribute('data-prompt');
            chatInput.value = prompt;
            chatInput.focus();
            sendMessage();
        });
    });

    // Clear chat
    clearBtn.addEventListener('click', function() {
        if (confirm('Clear the conversation history?')) {
            conversationHistory = [];
            sessionStorage.removeItem(pageStorageKey);
            location.reload();
        }
    });

    async function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        // Append user message
        const now = new Date();
        const timeString = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
        appendBubble('user', text, null, null, timeString, true);

        conversationHistory.push({ role: 'user', text: text });
        chatInput.value = '';
        charCounter.innerText = '0 / 4000';
        chatInput.disabled = true;
        sendBtn.disabled = true;

        // Show typing indicator with countdown
        typingIndicator.classList.remove('d-none');
        chatHistory.scrollTop = chatHistory.scrollHeight;

        let secondsLeft = timeoutSeconds;
        chatTimer.innerText = secondsLeft;
        const timerInterval = setInterval(() => {
            secondsLeft--;
            chatTimer.innerText = Math.max(0, secondsLeft);
            if (secondsLeft <= 0) clearInterval(timerInterval);
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
                    message: text,
                    history: conversationHistory.slice(-8),
                    module: 'chatbot'
                }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            clearInterval(timerInterval);

            const data = await res.json();

            if (res.ok && data.success) {
                appendBubble('assistant', data.text, data.providerUsed, data.latencyMs, data.timestamp || timeString, true);
                conversationHistory.push({ role: 'model', text: data.text });
                saveHistory();
            } else {
                appendBubble('assistant', data.errorMessage || 'AI assistant is temporarily unavailable.', null, null, timeString, true, true);
            }
        } catch (err) {
            clearTimeout(timeoutId);
            clearInterval(timerInterval);

            const msg = (err.name === 'AbortError')
                ? `Request timed out after ${timeoutSeconds} seconds. Please try asking a more focused question.`
                : 'AI assistant is temporarily unavailable.';
            appendBubble('assistant', msg, null, null, timeString, true, true);
        } finally {
            typingIndicator.classList.add('d-none');
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
    }

    function appendBubble(role, content, provider, latency, time, scroll = true, isError = false) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble chat-bubble-${role}`;

        if (role === 'user') {
            bubble.innerHTML = `
                <div>${escapeHtml(content)}</div>
                <div class="text-end mt-1"><small style="opacity: 0.8; font-size: 0.75rem;">${time}</small></div>
            `;
        } else {
            let adminBadge = '';
            if (provider) {
                adminBadge = `<span class="badge bg-label-info ms-2" style="font-size: 0.7rem;"><i class="ti tabler-cpu me-1"></i>${provider.toUpperCase()} • ${Math.round(latency || 0)}ms</span>`;
            }

            const formattedContent = isError
                ? `<span class="text-danger"><i class="ti tabler-alert-triangle me-1"></i>${escapeHtml(content)}</span>`
                : formatMarkdown(content);

            bubble.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-primary small"><i class="ti tabler-robot me-1"></i>NeoERP Bot</span>
                    <div>${adminBadge}</div>
                </div>
                <div>${formattedContent}</div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top">
                    <small class="text-muted" style="font-size: 0.75rem;">${time}</small>
                    <button type="button" class="btn btn-xs btn-outline-secondary copy-msg-btn" title="Copy text" style="padding: 2px 6px;">
                        <i class="ti tabler-copy" style="font-size: 0.75rem;"></i>
                    </button>
                </div>
            `;

            bubble.querySelector('.copy-msg-btn')?.addEventListener('click', function() {
                navigator.clipboard.writeText(content).then(() => {
                    this.innerHTML = '<i class="ti tabler-check text-success" style="font-size: 0.75rem;"></i>';
                    setTimeout(() => { this.innerHTML = '<i class="ti tabler-copy" style="font-size: 0.75rem;"></i>'; }, 2000);
                });
            });
        }

        chatHistory.appendChild(bubble);
        if (scroll) {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
    }

    function saveHistory() {
        try {
            sessionStorage.setItem(pageStorageKey, JSON.stringify(conversationHistory.slice(-20)));
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

    function formatMarkdown(text) {
        // Safe lightweight markdown parser for chatbot responses
        let html = escapeHtml(text);

        // Bold
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Italic
        html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Inline code
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');
        // Bullet list
        html = html.replace(/^\s*[-*]\s+(.*)$/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
        // Line breaks
        html = html.replace(/\n/g, '<br>');

        return html;
    }
});
</script>
@endsection
