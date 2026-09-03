@extends('layouts.master')

@section('title', 'Gemini AI Hub & Workflows')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Banner -->
    <div class="card mb-4 bg-primary text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #6e48aa 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-white bg-opacity-10 rounded-3 text-white">
                        <i class="ti tabler-sparkles fs-1"></i>
                    </div>
                    <div>
                        <h3 class="text-white mb-1 fw-bold">Google Gemini AI Hub</h3>
                        <p class="text-white-50 mb-0">Automate ERP workflows, analyze leads, draft smart emails, and build instant quotation scopes using Gemini Free Tier AI.</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#settingsModal">
                        <i class="ti tabler-settings me-1"></i> API Settings
                    </button>
                    <button type="button" class="btn btn-outline-light" onclick="testGeminiConnection()">
                        <i class="ti tabler-plug-connected me-1"></i> Test Connection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status & Quota Cards -->
    <div class="row g-4 mb-4">
        <!-- Connection Status -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 fw-semibold">Integration Status</h5>
                        @if($isConfigured)
                            <span class="badge bg-label-success"><i class="ti tabler-circle-check me-1"></i> Configured</span>
                        @else
                            <span class="badge bg-label-warning"><i class="ti tabler-alert-circle me-1"></i> Key Needed</span>
                        @endif
                    </div>
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Model:</span>
                            <span class="fw-bold text-primary">{{ $currentModel }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <span class="text-muted">API Key:</span>
                            <span class="font-monospace text-dark">{{ $apiKeyMasked ?? 'Not set in .env' }}</span>
                        </div>
                    </div>
                    <div id="connectionStatusAlert" class="alert alert-secondary py-2 px-3 mb-0 small d-flex align-items-center gap-2">
                        <i class="ti tabler-info-circle"></i>
                        <span>Click "Test Connection" to verify Google AI Studio API access.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Free Tier Info -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0 fw-semibold">Free Tier Allocation</h5>
                        <span class="badge bg-label-info">Google AI Studio</span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted"><i class="ti tabler-clock-hour-4 me-1 text-primary"></i> Requests Per Minute:</span>
                            <span class="fw-bold">15 RPM</span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted"><i class="ti tabler-calendar-event me-1 text-success"></i> Requests Per Day:</span>
                            <span class="fw-bold">1,500 RPD</span>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted"><i class="ti tabler-coin me-1 text-warning"></i> Cost:</span>
                            <span class="badge bg-success">100% Free Tier</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Quick AI Copilot Info -->
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 bg-gradient">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0 fw-semibold">Global AI Copilot</h5>
                            <i class="ti tabler-robot text-primary fs-3"></i>
                        </div>
                        <p class="text-muted small mb-3">
                            The ERP Assistant is available across all pages via the top navigation bar or floating trigger.
                        </p>
                    </div>
                    <button class="btn btn-primary w-100" onclick="openAIAssistant()">
                        <i class="ti tabler-messages me-1"></i> Open AI Assistant
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Workflows Section -->
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom bg-transparent">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-lead">
                        <i class="ti tabler-user-search me-1"></i> Lead & Enquiry Scoring
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-email">
                        <i class="ti tabler-mail-fast me-1"></i> Smart Email Drafter
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-scope">
                        <i class="ti tabler-file-certificate me-1"></i> Quotation Scope Generator
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-safety">
                        <i class="ti tabler-shield-check me-1"></i> RAMS Safety Suggester
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-custom">
                        <i class="ti tabler-prompt me-1"></i> Custom Prompt
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content">
                <!-- Tab 1: Lead Analysis -->
                <div class="tab-pane fade show active" id="tab-lead" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-input-check text-primary me-1"></i> Test Lead / Customer Enquiry Data</h6>
                            <form id="leadAnalysisForm" onsubmit="submitLeadAnalysis(event)">
                                <div class="mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="la_name" value="Apex Commercial Facilities" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label">Service / Job Type</label>
                                        <select class="form-select" id="la_job_type">
                                            @foreach($jobTypes as $jt)
                                                <option value="{{ $jt->name }}">{{ $jt->name }}</option>
                                            @endforeach
                                            <option value="HVAC Installation & Maintenance" selected>HVAC Installation & Maintenance</option>
                                            <option value="Electrical Repair">Electrical Repair</option>
                                            <option value="Commercial Plumbing">Commercial Plumbing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Customer Inquiry / Requirements</label>
                                    <textarea class="form-control" id="la_desc" rows="4" required>We need an urgent complete HVAC diagnostic and retrofitting for our 3-floor office building in Central London. Air handling units on floor 2 are making rattling sounds and heating is inconsistent. We need work completed by next Friday before board inspection.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnAnalyzeLead">
                                    <i class="ti tabler-sparkles me-1"></i> Analyze Lead with Gemini AI
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-brain text-primary me-1"></i> AI Intelligence & Qualification Output</h6>
                            <div id="leadResultContainer" class="p-3 bg-light rounded-3 border" style="min-height: 380px;">
                                <div class="text-center text-muted py-5" id="leadPlaceholder">
                                    <i class="ti tabler-robot fs-1 mb-2"></i>
                                    <p>Submit lead data on the left to see instant AI qualification, urgency analysis, and actionable next steps.</p>
                                </div>
                                <div id="leadResultContent" class="d-none">
                                    <!-- Dynamic content rendered via JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Smart Email Drafter -->
                <div class="tab-pane fade" id="tab-email" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-mail-spark text-primary me-1"></i> Email Generation Parameters</h6>
                            <form id="emailDraftForm" onsubmit="submitEmailDraft(event)">
                                <div class="mb-3">
                                    <label class="form-label">Recipient Name</label>
                                    <input type="text" class="form-control" id="ed_name" value="Mr. David Miller" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Purpose / Intent</label>
                                    <select class="form-select" id="ed_intent">
                                        <option value="Acknowledge inquiry and request site visit appointment" selected>Acknowledge inquiry & request site visit</option>
                                        <option value="Quotation follow-up and offer discount/terms">Quotation follow-up & offer terms</option>
                                        <option value="Job completion sign-off and feedback request">Job completion sign-off & feedback</option>
                                        <option value="Payment reminder and invoice dispatch">Payment reminder & invoice dispatch</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Context / Specific Points to Include</label>
                                    <textarea class="form-control" id="ed_details" rows="4">Thank customer for inquiring about the office HVAC overhaul. Propose Wednesday 10:00 AM or Thursday 2:00 PM for our senior engineer inspection. Mention that there is no charge for the initial site survey.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnDraftEmail">
                                    <i class="ti tabler-sparkles me-1"></i> Draft Email with Gemini AI
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0"><i class="ti tabler-mail-check text-primary me-1"></i> Generated Email Draft</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnCopyEmail" onclick="copyEmailDraft()">
                                    <i class="ti tabler-copy me-1"></i> Copy to Clipboard
                                </button>
                            </div>
                            <div id="emailResultContainer" class="p-3 bg-light rounded-3 border" style="min-height: 380px;">
                                <div class="text-center text-muted py-5" id="emailPlaceholder">
                                    <i class="ti tabler-mail-plus fs-1 mb-2"></i>
                                    <p>Fill in parameters on the left and click "Draft Email" to generate an executive-ready communication.</p>
                                </div>
                                <div id="emailResultContent" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label small text-muted">Subject Line:</label>
                                        <input type="text" class="form-control fw-bold" id="emailDraftSubject" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label small text-muted">Email Body Preview:</label>
                                        <div class="p-3 bg-white border rounded" id="emailDraftBody"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Scope Generator -->
                <div class="tab-pane fade" id="tab-scope" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-file-description text-primary me-1"></i> Quotation Details</h6>
                            <form id="scopeForm" onsubmit="submitScopeGen(event)">
                                <div class="mb-3">
                                    <label class="form-label">Client Name</label>
                                    <input type="text" class="form-control" id="sc_client" value="Kensington Tower Management" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Project Title</label>
                                    <input type="text" class="form-control" id="sc_title" value="Commercial LED Lighting & Emergency Power Retrofit" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Work Requirements</label>
                                    <textarea class="form-control" id="sc_desc" rows="4">Replace 120 fluorescent tube fixtures with high-efficiency commercial LED panels across stairwells and basement parking. Install 15 battery-backed emergency exit signs with automated testing compliance.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnGenScope">
                                    <i class="ti tabler-sparkles me-1"></i> Generate Scope of Work
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0"><i class="ti tabler-list-check text-primary me-1"></i> Structured Scope & Deliverables</h6>
                                <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnCopyScope" onclick="copyScopeOutput()">
                                    <i class="ti tabler-copy me-1"></i> Copy Text
                                </button>
                            </div>
                            <div id="scopeResultContainer" class="p-3 bg-light rounded-3 border" style="min-height: 380px;">
                                <div class="text-center text-muted py-5" id="scopePlaceholder">
                                    <i class="ti tabler-file-text fs-1 mb-2"></i>
                                    <p>Click "Generate Scope of Work" to build a structured specification suitable for inserting directly into quotations.</p>
                                </div>
                                <div id="scopeResultContent" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Safety Checklist -->
                <div class="tab-pane fade" id="tab-safety" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-shield text-primary me-1"></i> Job Site Details</h6>
                            <form id="safetyForm" onsubmit="submitSafetyGen(event)">
                                <div class="mb-3">
                                    <label class="form-label">Job Category</label>
                                    <input type="text" class="form-control" id="sf_job_type" value="Roofing Solar Panel Installation & Wiring" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Job Work Description</label>
                                    <textarea class="form-control" id="sf_desc" rows="4">Mounting 24 solar photovoltaic panels on a 2-storey pitched tile roof, routing DC cabling through attic space, and installing inverter in ground floor utility room.</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnGenSafety">
                                    <i class="ti tabler-shield-check me-1"></i> Generate RAMS Safety Checklist
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-alert-triangle text-warning me-1"></i> RAMS Risk Assessment & Controls</h6>
                            <div id="safetyResultContainer" class="p-3 bg-light rounded-3 border" style="min-height: 380px;">
                                <div class="text-center text-muted py-5" id="safetyPlaceholder">
                                    <i class="ti tabler-shield-half fs-1 mb-2"></i>
                                    <p>Submit job category and work description to produce instant health & safety hazard controls and required PPE checklists.</p>
                                </div>
                                <div id="safetyResultContent" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Custom Prompt -->
                <div class="tab-pane fade" id="tab-custom" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-terminal-2 text-primary me-1"></i> Prompt Playground</h6>
                            <div class="mb-3">
                                <label class="form-label">System or User Prompt</label>
                                <textarea class="form-control" id="custom_prompt" rows="6" placeholder="Ask Gemini any ERP operational question, data analysis request, or draft task...">Summarize 5 best practices for scheduling emergency trade engineers in a commercial facilities ERP to minimize travel time and maximize first-time fix rates.</textarea>
                            </div>
                            <button type="button" class="btn btn-primary w-100" id="btnCustomPrompt" onclick="submitCustomPrompt()">
                                <i class="ti tabler-send me-1"></i> Execute Prompt
                            </button>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="fw-bold mb-3"><i class="ti tabler-code text-primary me-1"></i> Gemini Response</h6>
                            <div id="customResultContainer" class="p-3 bg-light rounded-3 border" style="min-height: 250px;">
                                <div class="text-center text-muted py-5" id="customPlaceholder">
                                    <p>Prompt response will appear here in real time.</p>
                                </div>
                                <div id="customResultContent" class="d-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gemini Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="ti tabler-settings text-primary me-2"></i> Gemini AI Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="geminiSettingsForm" onsubmit="saveGeminiSettings(event)">
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="ti tabler-key me-1"></i> You can obtain a free API key at 
                        <a href="https://aistudio.google.com/app/apikey" target="_blank" class="fw-bold text-decoration-underline">Google AI Studio</a>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Google Gemini API Key</label>
                        <input type="password" class="form-control font-monospace" id="settings_api_key" placeholder="Enter your AI Studio API key (AIzaSy...)">
                        <small class="text-muted">Leave empty to keep existing key from .env</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model</label>
                        <select class="form-select" id="settings_model">
                            <option value="gemini-3.7-flash" {{ $currentModel == 'gemini-3.7-flash' ? 'selected' : '' }}>gemini-3.7-flash (Recommended: Ultra Fast & Intelligent)</option>
                            <option value="gemini-3.6-flash" {{ $currentModel == 'gemini-3.6-flash' ? 'selected' : '' }}>gemini-3.6-flash (Fast & Stable)</option>
                            <option value="gemini-flash-latest" {{ $currentModel == 'gemini-flash-latest' ? 'selected' : '' }}>gemini-flash-latest (Latest Flash)</option>
                            <option value="gemini-3.1-pro-preview" {{ $currentModel == 'gemini-3.1-pro-preview' ? 'selected' : '' }}>gemini-3.1-pro-preview (Deep Reasoning)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveSettings">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function safeFetchJson(url, options = {}) {
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };

        const res = await fetch(url, {
            ...options,
            headers: {
                ...defaultHeaders,
                ...(options.headers || {})
            }
        });

        const isJson = res.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await res.json() : null;

        if (!res.ok) {
            const errorMsg = (data && (data.message || data.error)) || (res.status === 401 ? 'Your session has expired. Please refresh and log in.' : (res.status === 419 ? 'CSRF token expired. Please refresh the page.' : `Server Error (${res.status})`));
            throw new Error(errorMsg);
        }

        return data || { success: false, message: 'Invalid response from server' };
    }

    function testGeminiConnection() {
        const statusAlert = document.getElementById('connectionStatusAlert');
        statusAlert.className = 'alert alert-info py-2 px-3 mb-0 small d-flex align-items-center gap-2';
        statusAlert.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Testing connection to Google Gemini API...';

        safeFetchJson("{{ route('gemini.test') }}", {
            method: 'POST',
            body: JSON.stringify({})
        })
        .then(data => {
            if (data.success) {
                statusAlert.className = 'alert alert-success py-2 px-3 mb-0 small d-flex align-items-center gap-2';
                statusAlert.innerHTML = `<i class="ti tabler-check"></i> <strong>Online:</strong> ${data.message} <br><em>"${data.sample_response || ''}"</em>`;
                if (typeof toastr !== 'undefined') toastr.success('Gemini API is connected successfully!');
            } else {
                statusAlert.className = 'alert alert-danger py-2 px-3 mb-0 small d-flex align-items-center gap-2';
                statusAlert.innerHTML = `<i class="ti tabler-alert-triangle"></i> <strong>Error:</strong> ${data.message || data.error}`;
                if (typeof toastr !== 'undefined') toastr.error(data.message || data.error);
            }
        })
        .catch(err => {
            statusAlert.className = 'alert alert-danger py-2 px-3 mb-0 small';
            statusAlert.innerHTML = `<i class="ti tabler-alert-triangle"></i> Connection error: ${err.message}`;
        });
    }

    function saveGeminiSettings(e) {
        e.preventDefault();
        const apiKey = document.getElementById('settings_api_key').value;
        const model = document.getElementById('settings_model').value;
        const btn = document.getElementById('btnSaveSettings');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        safeFetchJson("{{ route('gemini.save-settings') }}", {
            method: 'POST',
            body: JSON.stringify({ api_key: apiKey, model: model })
        })
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = 'Save Settings';
            if (data.success) {
                if (typeof toastr !== 'undefined') toastr.success(data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
                if (modal) modal.hide();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                if (typeof toastr !== 'undefined') toastr.error(data.message || data.error);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = 'Save Settings';
            alert('Error saving settings: ' + err.message);
        });
    }

    function submitLeadAnalysis(e) {
        e.preventDefault();
        const btn = document.getElementById('btnAnalyzeLead');
        const placeholder = document.getElementById('leadPlaceholder');
        const resultContainer = document.getElementById('leadResultContent');
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Analyzing with Gemini AI...';
        placeholder.innerHTML = '<div class="spinner-border text-primary my-4"></div><p class="text-muted">Evaluating requirements, calculating qualification score, and detecting urgency...</p>';
        placeholder.classList.remove('d-none');
        resultContainer.classList.add('d-none');

        const payload = {
            customer_name: document.getElementById('la_name').value,
            job_type: document.getElementById('la_job_type').value,
            description: document.getElementById('la_desc').value,
        };

        safeFetchJson("{{ route('gemini.analyze-lead') }}", {
            method: 'POST',
            body: JSON.stringify(payload)
        })
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Analyze Lead with Gemini AI';

            if (!res.success) {
                placeholder.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to analyze lead.'}</div>`;
                return;
            }

            placeholder.classList.add('d-none');
            resultContainer.classList.remove('d-none');

            const data = res.data || {};
            const score = data.qualification_score || 80;
            let scoreBadge = 'bg-success';
            if (score < 50) scoreBadge = 'bg-danger';
            else if (score < 75) scoreBadge = 'bg-warning text-dark';

            let reqList = (data.key_requirements || []).map(r => `<li>${r}</li>`).join('');
            let actionList = (data.action_items || []).map(a => `<li><i class="ti tabler-check text-success me-1"></i>${a}</li>`).join('');

            resultContainer.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge ${scoreBadge} fs-6 px-3 py-2">Qualification Score: ${score}/100</span>
                    <span class="badge bg-label-danger fs-6 px-3 py-2">Urgency: ${data.urgency || 'Normal'}</span>
                    <span class="badge bg-label-info fs-6 px-3 py-2">Complexity: ${data.estimated_complexity || 'Medium'}</span>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-1">Executive Summary:</h6>
                    <p class="bg-white p-3 rounded border mb-0 text-dark">${data.summary || 'Summary generated.'}</p>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded border h-100">
                            <h6 class="fw-bold mb-2 text-primary"><i class="ti tabler-list-details me-1"></i> Key Requirements:</h6>
                            <ul class="mb-0 ps-3 small text-dark">${reqList || '<li>Custom service requirements</li>'}</ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-white p-3 rounded border h-100">
                            <h6 class="fw-bold mb-2 text-success"><i class="ti tabler-list-check me-1"></i> Recommended Actions:</h6>
                            <ul class="list-unstyled mb-0 small text-dark">${actionList || '<li>Follow up with customer</li>'}</ul>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Analyze Lead with Gemini AI';
            placeholder.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }

    function submitEmailDraft(e) {
        e.preventDefault();
        const btn = document.getElementById('btnDraftEmail');
        const placeholder = document.getElementById('emailPlaceholder');
        const resultContainer = document.getElementById('emailResultContent');
        const copyBtn = document.getElementById('btnCopyEmail');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Drafting Email...';
        placeholder.innerHTML = '<div class="spinner-border text-primary my-4"></div><p class="text-muted">Generating personalized, executive-ready customer reply...</p>';
        placeholder.classList.remove('d-none');
        resultContainer.classList.add('d-none');
        copyBtn.classList.add('d-none');

        const payload = {
            recipient_name: document.getElementById('ed_name').value,
            intent: document.getElementById('ed_intent').value,
            enquiry_details: document.getElementById('ed_details').value
        };

        safeFetchJson("{{ route('gemini.draft-email') }}", {
            method: 'POST',
            body: JSON.stringify(payload)
        })
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Draft Email with Gemini AI';

            if (!res.success) {
                placeholder.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to generate email draft.'}</div>`;
                return;
            }

            placeholder.classList.add('d-none');
            resultContainer.classList.remove('d-none');
            copyBtn.classList.remove('d-none');

            const data = res.data || {};
            document.getElementById('emailDraftSubject').value = data.subject || 'Your Service Request';
            document.getElementById('emailDraftBody').innerHTML = data.email_body_html || `<p>${data.email_body_plain || res.raw_text}</p>`;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Draft Email with Gemini AI';
            placeholder.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }

    function copyEmailDraft() {
        const bodyText = document.getElementById('emailDraftBody').innerText;
        navigator.clipboard.writeText(bodyText).then(() => {
            if (typeof toastr !== 'undefined') toastr.success('Email draft copied to clipboard!');
            else alert('Copied to clipboard!');
        });
    }

    function submitScopeGen(e) {
        e.preventDefault();
        const btn = document.getElementById('btnGenScope');
        const placeholder = document.getElementById('scopePlaceholder');
        const resultContainer = document.getElementById('scopeResultContent');
        const copyBtn = document.getElementById('btnCopyScope');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating Scope...';
        placeholder.innerHTML = '<div class="spinner-border text-primary my-4"></div><p class="text-muted">Drafting structured project deliverables and technical specifications...</p>';
        placeholder.classList.remove('d-none');
        resultContainer.classList.add('d-none');
        copyBtn.classList.add('d-none');

        const payload = {
            customer: document.getElementById('sc_client').value,
            title: document.getElementById('sc_title').value,
            description: document.getElementById('sc_desc').value
        };

        safeFetchJson("{{ route('gemini.generate-scope') }}", {
            method: 'POST',
            body: JSON.stringify(payload)
        })
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Generate Scope of Work';

            if (!res.success) {
                placeholder.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to generate scope.'}</div>`;
                return;
            }

            placeholder.classList.add('d-none');
            resultContainer.classList.remove('d-none');
            copyBtn.classList.remove('d-none');

            const data = res.data || {};
            let scopeItemsHtml = (data.scope_items || []).map(s => `
                <div class="mb-2 p-2 bg-white rounded border">
                    <strong class="text-primary">${s.title || 'Phase'}:</strong>
                    <p class="mb-1 small">${s.description || ''}</p>
                    ${s.estimated_materials ? `<small class="text-muted"><strong>Materials:</strong> ${s.estimated_materials}</small>` : ''}
                </div>
            `).join('');

            resultContainer.innerHTML = `
                <div class="mb-3">
                    <h6 class="fw-bold mb-1">Project Overview:</h6>
                    <p class="p-2 bg-white rounded border small text-dark">${data.project_overview || 'Comprehensive scope overview.'}</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-1">Itemized Deliverables:</h6>
                    ${scopeItemsHtml}
                </div>
                <div class="row g-2 small">
                    <div class="col-md-6">
                        <div class="p-2 bg-white rounded border">
                            <strong class="text-success">Inclusions:</strong>
                            <ul class="mb-0 ps-3">${(data.inclusions || []).map(i => `<li>${i}</li>`).join('')}</ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-white rounded border">
                            <strong class="text-danger">Exclusions:</strong>
                            <ul class="mb-0 ps-3">${(data.exclusions || []).map(e => `<li>${e}</li>`).join('')}</ul>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-sparkles me-1"></i> Generate Scope of Work';
            placeholder.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }

    function copyScopeOutput() {
        const text = document.getElementById('scopeResultContent').innerText;
        navigator.clipboard.writeText(text).then(() => {
            if (typeof toastr !== 'undefined') toastr.success('Scope content copied!');
            else alert('Copied!');
        });
    }

    function submitSafetyGen(e) {
        e.preventDefault();
        const btn = document.getElementById('btnGenSafety');
        const placeholder = document.getElementById('safetyPlaceholder');
        const resultContainer = document.getElementById('safetyResultContent');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Evaluating Hazards...';
        placeholder.innerHTML = '<div class="spinner-border text-primary my-4"></div><p class="text-muted">Generating RAMS controls, PPE requirements, and emergency precautions...</p>';
        placeholder.classList.remove('d-none');
        resultContainer.classList.add('d-none');

        const payload = {
            job_type: document.getElementById('sf_job_type').value,
            job_description: document.getElementById('sf_desc').value
        };

        safeFetchJson("{{ route('gemini.generate-safety') }}", {
            method: 'POST',
            body: JSON.stringify(payload)
        })
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-shield-check me-1"></i> Generate RAMS Safety Checklist';

            if (!res.success) {
                placeholder.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to generate safety checklist.'}</div>`;
                return;
            }

            placeholder.classList.add('d-none');
            resultContainer.classList.remove('d-none');

            const data = res.data || {};
            let hazardList = (data.identified_hazards || []).map(h => `
                <tr class="small">
                    <td><strong>${h.hazard || 'Hazard'}</strong></td>
                    <td><span class="badge bg-label-warning">${h.risk_level || 'Medium'}</span></td>
                    <td>${h.mitigation || 'Control measure'}</td>
                </tr>
            `).join('');

            let ppeBadges = (data.required_ppe || []).map(p => `<span class="badge bg-primary me-1 mb-1"><i class="ti tabler-shield-check me-1"></i>${p}</span>`).join('');
            let checklistItems = (data.pre_job_checklist || []).map(c => `<li><i class="ti tabler-checkbox text-primary me-1"></i>${c}</li>`).join('');

            resultContainer.innerHTML = `
                <div class="mb-3">
                    <strong class="d-block mb-1">Required PPE:</strong>
                    <div class="d-flex flex-wrap">${ppeBadges}</div>
                </div>
                <div class="table-responsive mb-3 bg-white rounded border">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Hazard Identified</th>
                                <th>Risk</th>
                                <th>Mitigation / Safe Method</th>
                            </tr>
                        </thead>
                        <tbody>${hazardList}</tbody>
                    </table>
                </div>
                <div class="p-3 bg-white rounded border small">
                    <strong class="text-dark d-block mb-2">Pre-Job Verification Checklist:</strong>
                    <ul class="list-unstyled mb-0">${checklistItems}</ul>
                </div>
            `;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-shield-check me-1"></i> Generate RAMS Safety Checklist';
            placeholder.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }

    function submitCustomPrompt() {
        const prompt = document.getElementById('custom_prompt').value;
        const btn = document.getElementById('btnCustomPrompt');
        const placeholder = document.getElementById('customPlaceholder');
        const resultContainer = document.getElementById('customResultContent');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Executing...';
        placeholder.innerHTML = '<div class="spinner-border text-primary my-4"></div>';
        placeholder.classList.remove('d-none');
        resultContainer.classList.add('d-none');

        safeFetchJson("{{ route('gemini.chat') }}", {
            method: 'POST',
            body: JSON.stringify({ message: prompt })
        })
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-send me-1"></i> Execute Prompt';

            if (!res.success) {
                placeholder.innerHTML = `<div class="alert alert-danger">${res.error || 'Error running prompt.'}</div>`;
                return;
            }

            placeholder.classList.add('d-none');
            resultContainer.classList.remove('d-none');
            resultContainer.innerHTML = `<div class="p-3 bg-white rounded border text-dark" style="white-space: pre-wrap;">${res.reply}</div>`;
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti tabler-send me-1"></i> Execute Prompt';
            placeholder.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
        });
    }
</script>
@endsection
