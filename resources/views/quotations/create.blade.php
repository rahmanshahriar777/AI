
    <script>
        const fullToolbar = [
            [{
                header: [1, 2, false]
            }],
            ['bold', 'italic', 'underline'],
            ['code-block', 'blockquote'],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            ['link', 'image'],
            ['clean']
        ];

        $(document).ready(function() {
            @foreach ($quotationTemplate->sections as $qts)
                @if ($qts->section_type == 'textbox')
                    const quill_{{ $qts->id }} = new Quill('#{{ $qts->section_slug }}', {
                        placeholder: 'Type here...',
                        modules: {
                            syntax: true,
                            toolbar: fullToolbar
                        },
                        theme: 'snow'
                    });

                    // Sync content before form submission
                    $('form').on('submit', function() {
                        $('#input-{{ $qts->section_slug }}').val(quill_{{ $qts->id }}.root.innerHTML);
                    });
                @endif
            @endforeach

            $('input[type="file"][id^="attachments-"]').on('change', function(event){
                loadFile(event);
            });

            
            $('#quotationFormSubmit').on('click', function(event){
                $('#quotationForm').submit();
            });
                
            // Bind to the form's submit event
$('#quotationForm').on('submit', function(e) {

    // Prevent the default form submission
    e.preventDefault();

    if (!this.checkValidity()) {
        this.querySelector(".form-control:invalid").focus();
        return;
    }
    
    // Get the form and its data
    var $form = $(this);
    //var formData = $form.serialize();
    var formData = new FormData(this);
    var url = $form.attr('action');

    // Perform the AJAX request
    $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        dataType: 'json', // Expecting a JSON response from the server
        cache: false,
        //contentType: 'multipart/form-data',
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.success) {
                //   // Close the modal on success (using Bootstrap 5 JS syntax)
                //   var myModalEl = document.getElementById('myFormModal');
                //   var modal = bootstrap.Modal.getInstance(myModalEl);
                //   modal.hide();

                // Optional: Show a success alert on the main page
                alert('Form submitted successfully!');
                // Optional: Update a part of your page, e.g., a table
                // $('#myTable').load('myTable.php'); 

                //setTimeout(function(){
                    window.location.reload();
                //}, 1000);

            } else {
                // Handle validation errors or other server-side failures
                alert('Error: ' + response.message);
                // You could also dynamically update specific fields with error messages
            }
        },
        error: function(xhr, status, error) {
            // Handle network errors
            console.error("Sorry, there was a problem: " + error);
            alert("An error occurred. Please try again.");
        }
    });
});

});
    </script>

    <script>
    var loadFile = function(event) {
        const sectionId = event.target.id.split('-')[1];
        const previewContainer = document.getElementById(`preview-${sectionId}`);
        previewContainer.innerHTML = ''; // Clear previous previews
        var file = event.target.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.height = '80px';
                img.style.border = '1px solid #ccc';
                img.style.padding = '2px';
                img.style.borderRadius = '4px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    };
    </script>
    
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-6">
            <!-- Quotation Templates -->
            <div class="col-12">
                <div class="card1">
                    

                    <div class="card-body">
                        <form id="quotationForm" action="{{ route('quotations.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="features-icon-wrapper row gx-0 gy-6 g-sm-12">

                                {{-- Lead Info --}}
                                <div class="col-lg-4 col-sm-6 text-center features-icon-box">
                                    <div class="avatar avatar-xl mb-2" style="margin-left:41%;">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="icon-base ti tabler-circle-dotted-letter-l icon-xl"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-2">Lead Name: {{ $leaddata->lead_name }}</h5>
                                    <p class="features-icon-description">
                                        <strong>Lead Details:</strong>
                                        {{ \Illuminate\Support\Str::limit(strip_tags($leaddata->lead_description), 30, '...') }}
                                    </p>
                                    <p><strong>Template Name:</strong> {{ $quotationTemplate->template_name }}</p>
                                </div>

                                {{-- Customer Info --}}
                                <div class="col-lg-4 col-sm-6 features-icon-box">
                                    <h5 class="mb-2">Company: {{ $customerdata->company_name }}</h5>
                                    <ul class="list-unstyled my-3 py-1">
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-user icon-lg"></i>
                                            <span class="fw-medium mx-2">Contact Person:</span>
                                            <span>{{ $customerdata->contact_firstname }}
                                                {{ $customerdata->contact_lastname }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-phone-call icon-lg"></i>
                                            <span class="fw-medium mx-2">Phone:</span>
                                            <span>{{ $customerdata->contact_phone }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-messages icon-lg"></i>
                                            <span class="fw-medium mx-2">Mobile:</span>
                                            <span>{{ $customerdata->contact_mobile }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-mail icon-lg"></i>
                                            <span class="fw-medium mx-2">Email:</span>
                                            <span>{{ $customerdata->contact_email }}</span>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Site Address --}}
                                <div class="col-lg-4 col-sm-6 features-icon-box">
                                    <h5 class="mb-2">Site Address</h5>
                                    <ul class="list-unstyled my-3 py-1">
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-map-pin icon-lg"></i>
                                            <span class="fw-medium mx-2">Address:</span>
                                            <span>{{ $leaddata->leadaddress->address ?? 'N/A' }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-map-pin icon-lg"></i>
                                            <span class="fw-medium mx-2">County:</span>
                                            <span>{{ $leaddata->leadaddress->county ?? 'N/A' }}</span>
                                        </li>
                                        <li class="d-flex align-items-center mb-3">
                                            <i class="icon-base ti tabler-map-pin icon-lg"></i>
                                            <span class="fw-medium mx-2">Country:</span>
                                            <span>{{ $leaddata->leadaddress->country ?? 'N/A' }}</span>
                                        </li>
                                    </ul>
                                </div>

                            </div>


                            <div class="row g-6">
                                <div class="col-md-4 mb-6">
                                    <label for="quotationVersion" class="form-label">Quotation Version</label>
                                    <input type="text" class="form-control" id="quotationVersion"
                                        placeholder="version 1.0" name="quotation_version" required />
                                </div>
                                <div class="col-md-4 mb-6">
                                    <label for="html5-date-input" class="form-label">Valid Until</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="date" id="html5-date-input"
                                            name="quotation_date" min="{{ date('Y-m-d') }}" required />
                                    </div>
                                </div>
                                <div class="col-md-4 mb-6">
                                    <label for="html5-date-input" class="form-label">Approximate Cost (£)</label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="number" id="html5-date-input"
                                            name="quotation_amount" min="0" step="0.1" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row g-6">
                                @foreach ($quotationTemplate->sections as $qts)
                                    @if ($qts->section_type == 'textbox')
                                        <div class="col-md-12 mb-6">
                                            <label class="form-label"
                                                for="{{ $qts->section_slug }}">{{ $qts->section_name }}</label>

                                            {{-- Hidden input to store the HTML from Quill --}}
                                            <input type="hidden" name="{{ $qts->section_slug }}"
                                                id="input-{{ $qts->section_slug }}">

                                            {{-- Div for the Quill editor --}}
                                            <div id="{{ $qts->section_slug }}" class="form-control"
                                                style="min-height: 120px;">
                                                {!! $qts->samples->first() ? $qts->samples->first()->sample_source : '' !!}
                                            </div>
                                        </div>

                                        @if ($qts->has_attachments)
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label"
                                                    for="attachments-{{ $qts->id }}">Attachments (Multiple
                                                    Images)</label>
                                                <input type="file" class="form-control"
                                                    id="attachments-{{ $qts->id }}"
                                                    name="attachments[{{ $qts->section_slug }}][]" accept="image/*"
                                                    multiple >

                                                {{-- Preview container --}}
                                                <div class="image-preview mt-2 d-flex flex-wrap gap-2"
                                                    id="preview-{{ $qts->id }}"></div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>

                            <input type="hidden" name="lead_id" value="{{ $leaddata->id }}">
                            <input type="hidden" name="customer_id" value="{{ $customerdata->id }}">
                            <input type="hidden" name="quotation_template_id" value="{{ $quotationTemplate->id }}">
                            <input type="hidden" name="job_type_id" value="{{ $quotationTemplate->job_type_id }}">

                            <!-- <div class="pt-6">
                                <button type="submit" class="btn btn-primary me-4">Submit</button>
                                <button type="reset" class="btn btn-label-secondary">Cancel</button>
                            </div> -->
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Quotation Templates -->
        </div>
    </div>
