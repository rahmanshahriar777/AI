<!-- Reject mail Modal -->
<div class="modal-onboarding modal fade animate__animated" id="rejectEnquiryModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <strong class="onboarding-title text-body">Reject enquiry</strong>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-4">
                            <div class="input-group">
                                <select class="form-select" id="mailTemplateSelect"
                                    aria-label="Choose mail template">
                                    <option selected disabled>Choose mail template</option>
                                    @foreach ($mailtemplates as $mt)
                                        <option value="{{ $mt->id }}">{{ $mt->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="btn btn-outline-primary waves-effect"
                                    type="button" onclick="selectMailTemplate()">
                                    Select Mail
                                </button>

                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label for="sendtoemail" class="form-label">Send
                                            to</label>
                                        <input type="email" class="form-control"
                                            id="sendtoemail" placeholder="name@example.com"
                                            name="sendtoemail"
                                            value="{{ $enquiry->customer->contact_email }}">
                                    </div>
                                    <div class="mb-4">
                                        <label for="mailsubject"
                                            class="form-label">Subject</label>
                                        <input class="form-control" type="text"
                                            id="mailsubject" aria-label="readonly input example"
                                            name="mailsubject"
                                            value="Enquiry {{ $enquiry->enquiry_name }} has been rejected">
                                    </div>
                                    <div class="form-control p-0">
                                        <div class="border-0 pb-6" id="mailmessage"></div>
                                    </div>
                                    <input type="hidden" id="mailmessage_input"
                                        name="mailmessage">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" onclick="return updateEnquiryStatus('{{ $enquiry->id }}', 'rejected')">Submit</button>
            </div>
        </div>
    </div>
</div>
<!--/ Reject mail Modal -->

<script>
    let quillm = '';

    document.addEventListener("DOMContentLoaded", function (event) {
        quillm = new Quill('#mailmessage', {
            theme: 'snow',
            placeholder: 'Write your message...',
        });
    });

    function updateEnquiryStatus(enquiryId, status){
        const htmlContent = quillm.root.innerHTML.trim();
        const textContent = quillm.getText().trim();

        if (textContent === '') {
            toastr.error('Message is required.');
            return;
        }

        var sendtoemail = $('#sendtoemail').val();
        var mailsubject = $('#mailsubject').val();

        $.ajax({
            url: `/enquiry/updatestatus/${enquiryId}`,
            type: 'POST',
            data: {
                status: status,
                sendtoemail: sendtoemail,
                mailsubject: mailsubject,
                mailmessage: htmlContent,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(`Status updated successfully to "${status}"`);
                    
                    setTimeout(function(){
                        //window.location.reload(); 
                        window.location.href = document.referrer;
                    }, 1000);
                } else {
                    toastr.error('Error: ' + response.message);
                }
            },
            error: function (xhr) {
                let error_message = "Something went wrong.";
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    error_message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                toastr.error(error_message);
            }
        });
    }
    
    function selectMailTemplate() {
        const templateId = document.getElementById('mailTemplateSelect').value;
        if (!templateId) return;

        $.ajax({
            url: `/mail-template/${templateId}/enquiry/{{ $enquiry->id }}`,
            method: 'GET',
            success: function (res) {
                quillm.root.innerHTML = res.message;
            },
            error: function () {
                console.error('Failed to load mail template.');
                quillm.root.innerHTML = '';
                toastr.error('Failed to load mail template.');
            }
        });
    }

</script>