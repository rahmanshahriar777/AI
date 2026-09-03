<!-- Reusable Gemini AI Workflow Modals -->

<!-- 1. AI Lead & Enquiry Analysis Modal -->
<div class="modal fade" id="geminiLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti tabler-sparkles fs-4"></i>
                    <h5 class="modal-title text-white mb-0 fw-bold">Gemini AI Lead Intelligence</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="geminiLeadModalBody">
                <div class="text-center py-5" id="geminiLeadLoading">
                    <div class="spinner-border text-primary mb-3"></div>
                    <p class="text-muted mb-0">Analyzing lead requirements, qualification metrics, and recommended actions...</p>
                </div>
                <div id="geminiLeadContent" class="d-none"></div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 2. AI Smart Email Reply Modal -->
<div class="modal fade" id="geminiEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti tabler-mail-spark fs-4"></i>
                    <h5 class="modal-title text-white mb-0 fw-bold">Gemini AI Smart Reply Drafter</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email Purpose / Tone:</label>
                    <div class="input-group">
                        <select class="form-select" id="geminiEmailIntentSelect">
                            <option value="Acknowledge inquiry and propose site visit date" selected>Acknowledge & Propose Site Visit</option>
                            <option value="Send formal quotation cover letter and explain next steps">Quotation Cover Letter</option>
                            <option value="Follow up on pending quotation with special assistance">Quotation Follow-Up</option>
                            <option value="Job completion confirmation and thank you">Job Completion & Thank You</option>
                        </select>
                        <button class="btn btn-primary" type="button" onclick="regenerateEmailDraft()">
                            <i class="ti tabler-reload me-1"></i> Generate
                        </button>
                    </div>
                </div>
                <div class="text-center py-4" id="geminiEmailLoading">
                    <div class="spinner-border text-primary mb-2"></div>
                    <p class="text-muted small">Generating tailored response...</p>
                </div>
                <div id="geminiEmailContent" class="d-none">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject:</label>
                        <input type="text" class="form-control fw-bold" id="geminiWorkflowSubject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Message Body:</label>
                        <div class="p-3 bg-white border rounded" id="geminiWorkflowBody" style="min-height: 150px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="copyWorkflowEmail()">
                    <i class="ti tabler-copy me-1"></i> Copy Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentWorkflowContext = {};

    function triggerLeadAIAnalysis(name, jobType, description, leadId = null, enquiryId = null) {
        currentWorkflowContext = { customer_name: name, job_type: jobType, description: description, lead_id: leadId, enquiry_id: enquiryId };
        const modalEl = document.getElementById('geminiLeadModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        const loading = document.getElementById('geminiLeadLoading');
        const content = document.getElementById('geminiLeadContent');
        loading.classList.remove('d-none');
        content.classList.add('d-none');

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch("{{ route('gemini.analyze-lead') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(currentWorkflowContext)
        })
        .then(res => res.json())
        .then(res => {
            loading.classList.add('d-none');
            content.classList.remove('d-none');

            if (!res.success) {
                content.innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to analyze.'}</div>`;
                return;
            }

            const data = res.data || {};
            const score = data.qualification_score || 80;
            let badgeClass = score >= 75 ? 'bg-success' : (score >= 50 ? 'bg-warning text-dark' : 'bg-danger');

            let reqs = (data.key_requirements || []).map(r => `<li>${r}</li>`).join('');
            let actions = (data.action_items || []).map(a => `<li><i class="ti tabler-check text-success me-1"></i>${a}</li>`).join('');

            content.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <span class="badge ${badgeClass} fs-6 px-3 py-2">Score: ${score}/100</span>
                    <span class="badge bg-label-danger fs-6 px-3 py-2">Urgency: ${data.urgency || 'Medium'}</span>
                    <span class="badge bg-label-primary fs-6 px-3 py-2">Sentiment: ${data.sentiment || 'Positive'}</span>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-1">Executive Summary:</h6>
                    <div class="p-3 bg-light rounded border text-dark">${data.summary || 'Summary'}</div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-primary mb-2">Key Requirements:</h6>
                            <ul class="mb-0 ps-3 small text-dark">${reqs || '<li>Standard service request</li>'}</ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <h6 class="fw-bold text-success mb-2">Recommended Actions:</h6>
                            <ul class="list-unstyled mb-0 small text-dark">${actions || '<li>Contact client</li>'}</ul>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            loading.classList.add('d-none');
            content.classList.remove('d-none');
            content.innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
        });
    }

    function triggerEmailAIDraft(name, email, description) {
        currentWorkflowContext = { recipient_name: name, recipient_email: email, enquiry_details: description };
        const modalEl = document.getElementById('geminiEmailModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        regenerateEmailDraft();
    }

    function regenerateEmailDraft() {
        const loading = document.getElementById('geminiEmailLoading');
        const content = document.getElementById('geminiEmailContent');
        loading.classList.remove('d-none');
        content.classList.add('d-none');

        const intent = document.getElementById('geminiEmailIntentSelect').value;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch("{{ route('gemini.draft-email') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                recipient_name: currentWorkflowContext.recipient_name || 'Customer',
                recipient_email: currentWorkflowContext.recipient_email || '',
                intent: intent,
                enquiry_details: currentWorkflowContext.enquiry_details || ''
            })
        })
        .then(res => res.json())
        .then(res => {
            loading.classList.add('d-none');
            content.classList.remove('d-none');

            if (!res.success) {
                document.getElementById('geminiWorkflowBody').innerHTML = `<div class="alert alert-danger">${res.error || 'Failed to draft email.'}</div>`;
                return;
            }

            const data = res.data || {};
            document.getElementById('geminiWorkflowSubject').value = data.subject || 'Follow up regarding your service request';
            document.getElementById('geminiWorkflowBody').innerHTML = data.email_body_html || `<p>${data.email_body_plain || res.raw_text}</p>`;
        })
        .catch(err => {
            loading.classList.add('d-none');
            content.classList.remove('d-none');
            document.getElementById('geminiWorkflowBody').innerHTML = `<div class="alert alert-danger">${err.message}</div>`;
        });
    }

    function copyWorkflowEmail() {
        const text = document.getElementById('geminiWorkflowBody').innerText;
        navigator.clipboard.writeText(text).then(() => {
            if (typeof toastr !== 'undefined') toastr.success('Email content copied!');
            else alert('Copied!');
        });
    }
</script>
