<!-- Global Google Gemini AI ERP Assistant Modal -->
<div class="modal fade" id="geminiAssistantModal" tabindex="-1" aria-labelledby="geminiAssistantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white p-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-white bg-opacity-20 rounded-circle text-white">
                        <i class="ti tabler-sparkles fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold" id="geminiAssistantModalLabel">NeoERP Gemini Copilot</h5>
                        <small class="text-white-50">Powered by Google Gemini Free Tier AI</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Chat History Area -->
            <div class="modal-body p-3" style="height: 460px; overflow-y: auto; background-color: #f8f9fa;" id="geminiChatBody">
                <!-- Welcome greeting -->
                <div class="d-flex mb-3 gap-2">
                    <div class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-primary text-white"><i class="ti tabler-robot"></i></span>
                    </div>
                    <div class="p-3 rounded-3 bg-white border text-dark shadow-sm" style="max-width: 85%;">
                        <p class="mb-1 fw-semibold text-primary">Hello {{ auth()->user()->name ?? 'there' }}! 👋</p>
                        <p class="mb-2 small">I am your NeoERP AI Assistant powered by Google Gemini. Here are a few ways I can help you today:</p>
                        <div class="d-flex flex-wrap gap-1">
                            <button class="btn btn-xs btn-outline-primary" onclick="quickGeminiPrompt('How do I qualify and prioritize inbound leads effectively?')">Qualify Leads</button>
                            <button class="btn btn-xs btn-outline-primary" onclick="quickGeminiPrompt('Draft a professional quotation follow-up message.')">Quotation Follow-up</button>
                            <button class="btn btn-xs btn-outline-primary" onclick="quickGeminiPrompt('What are key H&S precautions for working at height?')">H&S Precautions</button>
                            <button class="btn btn-xs btn-outline-primary" onclick="quickGeminiPrompt('Suggest ways to improve field engineer schedule efficiency.')">Schedule Efficiency</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Input Footer -->
            <div class="modal-footer bg-white border-top p-3">
                <form id="geminiChatForm" onsubmit="sendGeminiChatMessage(event)" class="w-100">
                    <div class="input-group">
                        <input type="text" class="form-control" id="geminiChatInput" placeholder="Ask Gemini AI anything about ERP tasks, quotes, leads, or safety..." autocomplete="off">
                        <button class="btn btn-primary px-3" type="submit" id="geminiChatSendBtn">
                            <i class="ti tabler-send"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let geminiChatHistory = [];

    function openAIAssistant() {
        const modalEl = document.getElementById('geminiAssistantModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => {
                document.getElementById('geminiChatInput')?.focus();
            }, 500);
        }
    }

    function quickGeminiPrompt(promptText) {
        document.getElementById('geminiChatInput').value = promptText;
        document.getElementById('geminiChatForm').dispatchEvent(new Event('submit'));
    }

    function sendGeminiChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('geminiChatInput');
        const sendBtn = document.getElementById('geminiChatSendBtn');
        const chatBody = document.getElementById('geminiChatBody');
        const message = input.value.trim();

        if (!message) return;

        // Append user message to UI
        const userHtml = `
            <div class="d-flex justify-content-end mb-3 gap-2">
                <div class="p-3 rounded-3 bg-primary text-white shadow-sm" style="max-width: 85%;">
                    <p class="mb-0 small">${escapeHtml(message)}</p>
                </div>
                <div class="avatar avatar-sm flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-secondary text-white"><i class="ti tabler-user"></i></span>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', userHtml);
        input.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;

        // Append loading state
        const loadingId = 'gemini-loading-' + Date.now();
        const loadingHtml = `
            <div class="d-flex mb-3 gap-2" id="${loadingId}">
                <div class="avatar avatar-sm flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-primary text-white"><i class="ti tabler-robot"></i></span>
                </div>
                <div class="p-3 rounded-3 bg-white border text-muted shadow-sm small">
                    <span class="spinner-border spinner-border-sm me-2 text-primary"></span> Gemini is thinking...
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', loadingHtml);
        chatBody.scrollTop = chatBody.scrollHeight;

        sendBtn.disabled = true;
        const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch("{{ route('gemini.chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': metaToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                message: message,
                history: geminiChatHistory
            })
        })
        .then(async res => {
            const isJson = res.headers.get('content-type')?.includes('application/json');
            const data = isJson ? await res.json() : null;
            if (!res.ok) {
                const errorMsg = (data && (data.message || data.error)) || (res.status === 401 ? 'Session expired. Please refresh and log in.' : (res.status === 419 ? 'CSRF token expired. Please refresh the page.' : `Server Error (${res.status})`));
                throw new Error(errorMsg);
            }
            return data || { success: false, error: 'Invalid response from server.' };
        })
        .then(res => {
            sendBtn.disabled = false;
            const loader = document.getElementById(loadingId);
            if (loader) loader.remove();

            if (res.success) {
                geminiChatHistory.push({ role: 'user', text: message });
                geminiChatHistory.push({ role: 'model', text: res.reply });

                const botHtml = `
                    <div class="d-flex mb-3 gap-2">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-primary text-white"><i class="ti tabler-robot"></i></span>
                        </div>
                        <div class="p-3 rounded-3 bg-white border text-dark shadow-sm" style="max-width: 85%;">
                            <div class="small" style="white-space: pre-wrap;">${escapeHtml(res.reply)}</div>
                        </div>
                    </div>
                `;
                chatBody.insertAdjacentHTML('beforeend', botHtml);
            } else {
                const errorHtml = `
                    <div class="d-flex mb-3 gap-2">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-danger text-white"><i class="ti tabler-alert-triangle"></i></span>
                        </div>
                        <div class="p-3 rounded-3 bg-white border border-danger text-danger shadow-sm small">
                            ${res.error || res.message || 'Unable to generate reply.'}
                        </div>
                    </div>
                `;
                chatBody.insertAdjacentHTML('beforeend', errorHtml);
            }
            chatBody.scrollTop = chatBody.scrollHeight;
        })
        .catch(err => {
            sendBtn.disabled = false;
            const loader = document.getElementById(loadingId);
            if (loader) loader.remove();
            chatBody.insertAdjacentHTML('beforeend', `<div class="alert alert-danger small">${err.message}</div>`);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
