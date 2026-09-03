@extends('layouts.blank')

@section('title', 'Create Enquiry')
@section('scripts')
    @parent
    <script>
        function checkaddress() {
            let postcode = $('#basic-default-postcode').val().replace(/\s/g, '');

            if (!postcode) {
                toastr.error("Please enter a postcode.");
                return;
            }

            $('#loading-spinner').show();
            $('#address-suggestions').hide().empty(); // Clear old suggestions

            $.ajax({
                url: `/checkpostcode/${postcode}`,
                method: 'GET',
                success: function(response) {
                    $('#loading-spinner').hide();

                    if (!response || response.error || !response.address || !Array.isArray(response.address
                            .addresses)) {
                        $('#basic-default-address').val("No address found.");
                        return;
                    }

                    let suggestions = response.address.addresses;

                    if (suggestions.length === 0) {
                        $('#basic-default-address').val("No address found.");
                        return;
                    }

                    let suggestionList = suggestions.map(address =>
                        `<a href="#" class="list-group-item list-group-item-action suggestion-item">${address}</a>`
                    ).join('');

                    $('#address-suggestions').html(suggestionList).show();
                    $('#posttown').val(response.address.post_town);
                },
                error: function() {
                    $('#loading-spinner').hide();
                    $('#address-suggestions').hide();
                    $('#basic-default-address').val("Error fetching address.");
                }
            });
        }
        $(document).ready(function() {
            // Event listener to handle clicking on a suggestion (run this once on page load)
            $(document).on('click', '.suggestion-item', function(e) {
                e.preventDefault();
                let selectedAddress = $(this).text();
                let posttown = $('#posttown').val();
                $.ajax({
                    url: `/checkposttown/${posttown}?a=${selectedAddress}`,
                    method: 'GET',
                    success: function(response) {
                        if (response && response.address) {
                            $('#basic-default-address').val(selectedAddress);
                            $('#basic-default-county').val(response.address.county);
                            $('#basic-default-country').val(response.address.country);
                            $('#address-suggestions').hide();

                            if(response.company_name){
                                if($('input[name="company"]').val()==''){
                                   $('input[name="company"]').val(response.company_name).focus();
                                }
                            }
                            
                        } else {
                            $('#basic-default-address').val("Error fetching address.");
                        }
                    },
                    error: function() {
                        $('#basic-default-address').val("Error fetching address.");
                    }
                });


            });

            // Hide suggestions when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#basic-default-postcode, #address-suggestions').length) {
                    $('#address-suggestions').hide();
                }
            });
        });
    </script>

    <script>
        $('#queryForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'), // Adjust as needed
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    
                    window.scrollTo(0, 0);
                    window.parent.scrollTo(0, 0);

                    $('#queryForm').trigger('reset');
                    grecaptcha.reset();
                    clearImagePreview();

                    toastr.success("Your query has been submitted successfully!", '', { "positionClass": "toast-top-left" });
                    setTimeout(() => {
                        //window.location.reload();
                        
                        //window.location.href = '{{ route('query') }}?s=success';
                        //window.location.href = '{{ route('query') }}';

                    }, 1000);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message ||
                        "An error occurred while submitting the form.", '', { "positionClass": "toast-bottom-left" });
                    //console.log(xhr.responseText);
                    //$('input[name="firstname"]').focus();
                    //window.scrollTo(0, 0);

                    grecaptcha.reset();
                    
                }
            });
        });
    </script>
    <script>
        // Try to get the referrer or parent domain (if allowed)
        try {
            const referrer = document.referrer || window.top.document.referrer;
            document.getElementById('enquiry_source').value = referrer || 'unknown';
        } catch (e) {
            document.getElementById('enquiry_source').value = 'unknown';
        }
    </script>
    <script>
        function previewFile(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.querySelector(previewId);
                    img.src = e.target.result;
                    img.classList.remove("d-none");
                };
                reader.readAsDataURL(file);
            }
        }
        function clearImagePreview(){
            $('#preview1,#preview2,#preview3').attr('src', '');
        }
    </script>

@endsection

@section('styles')
    @parent
    <style>
        .suggestion-item {
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }

        #address-suggestions {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            position: absolute;
            z-index: 9999;
            background-color: rgb(255, 157, 157);
            color: rgb(0, 0, 0);
            margin-top: -10px;
        }
        .required:after {
            content:" *";
            color: red;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="row" style="max-width: 500px;">
        <div class="col-xl">

            <div class="card shadow-lg rounded-2xl border-0 overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Submit Your Roofing Enquiry</h4>
                </div>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="card-body p-4">
                    <form id="queryForm" class="" enctype="multipart/form-data" novalidate_skip
                        action="{{ route('query.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6 col-6">
                                <label for="firstname" class="form-label fw-bold required">First Name</label>
                                <input id="firstname" type="text" class="form-control" name="firstname" placeholder="John" required />
                            </div>
                            <div class="col-md-6 col-6">
                                <label for="lastname" class="form-label fw-bold">Last Name</label>
                                <input id="lastname" type="text" class="form-control" name="lastname" placeholder="Doe" />
                            </div>
                            <div class="col-md-12 col-12">
                                <label for="company" class="form-label fw-bold required">Company</label>
                                <input id="company" type="text" class="form-control" name="company" placeholder="ACME Inc."
                                    required />
                            </div>
                            <div class="col-md-6 col-6">
                                <label for="phone"  class="form-label fw-bold">Phone No</label>
                                <input id="phone"  type="text" name="phone" class="form-control" placeholder="{{ simple_format_phone('') }}"
                                    maxlength="20" pattern1="\d{11}" inputmode="numeric"
                                    oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);"  />

                                    <div class="form-text">Phone or Mobile is required</div>
                            </div>
                            <div class="col-md-6 col-6">
                                <label for="mobile"  class="form-label fw-bold">Mobile No</label>
                                <input id="mobile" type="text" name="mobile" class="form-control" placeholder="{{ simple_format_phone('') }}"
                                    maxlength="20" pattern1="\d{11}" inputmode="numeric"
                                    oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);"  />
                                
                            </div>
                            <div class="col-md-12 col-12">
                                <label for="email" class="form-label fw-bold required">Email</label>
                                <input id="email" type="email" class="form-control" name="email" placeholder="example@email.com"
                                    required />
                                <!-- <div class="form-text">You can use letters, numbers & periods</div> -->
                            </div>
                            <div class="col-12">
                                <label for="jobdescription" class="form-label fw-bold required">Description of Job</label>
                                <textarea id="jobdescription" class="form-control" placeholder="Hi, My roof needs some care." name="jobdescription" rows="3" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="basic-default-postcode required">Postcode</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="basic-default-postcode" name="postcode"
                                        placeholder="WR11 7EJ" aria-label="WR11 7EJ" aria-describedby="button-addon2"
                                        oninput="this.value = this.value.toUpperCase()" required />
                                    <button class="btn btn-outline-primary" type="button" id="button-addon2"
                                        onclick="checkaddress()">Check</button>
                                </div>
                                <div id="loading-spinner" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Searching...
                                </div>

                                <!-- Suggestions Container -->
                                <div id="address-suggestions" class="list-group mt-1" style="display:none;"></div>
                                <input type="hidden" id="posttown" value="" />
                            </div>
                            <div class="col-12">
                                <label for="basic-default-address" class="form-label fw-bold required">Address</label>
                                <textarea id="basic-default-address" class="form-control" name="address" rows="2" required></textarea>
                            </div>
                            <div class="col-md-6 col-6">
                                <label for="basic-default-county" class="form-label fw-bold ">County</label>
                                <input type="text" id="basic-default-county" name="county" class="form-control"
                                    placeholder="Worcestershire"  />
                            </div>
                            <div class="col-md-6 col-6">
                                <label for="basic-default-country" class="form-label fw-bold ">Country</label>
                                <input type="text" id="basic-default-country" name="country" class="form-control"
                                    placeholder="England"  />
                            </div>
                            <div class="col-md-12">
                                <label for="photo1" class="form-label fw-bold required">Photo 1</label>
                                <input id="photo1" type="file" class="form-control" name="photo1" accept=".jpg,.jpeg,.png"
                                    required onchange="previewFile(this, '#preview1')" />
                                <img id="preview1" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                            </div>
                            <div class="col-md-12">
                                <label for="photo2" class="form-label fw-bold">Photo 2</label>
                                <input id="photo2" type="file" class="form-control" name="photo2" accept=".jpg,.jpeg,.png"
                                    onchange="previewFile(this, '#preview2')" />
                                <img id="preview2" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                            </div>
                            <div class="col-md-12">
                                <label for="photo3" class="form-label fw-bold">Photo 3</label>
                                <input id="photo3" type="file" class="form-control" name="photo3" accept=".jpg,.jpeg,.png"
                                    onchange="previewFile(this, '#preview3')" />
                                <img id="preview3" class="img-thumbnail mt-2 d-none" style="max-height: 120px;" />
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="enquiry_source" id="enquiry_source">
                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}
                                {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::renderJs() !!}
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <button type="submit" class="btn btn-primary w-50 me-2">
                                    <span id="submit-text">SUBMIT</span>
                                    <span id="submit-spinner" class="spinner-border spinner-border-sm d-none"
                                        role="status" aria-hidden="true"></span>
                                </button>
                                <button type="reset" class="btn btn-secondary w-50" onclick="clearImagePreview()">RESET</button>
                            </div>
                        </div>
                    </form>
                    <hr class="my-4" />
                    <div>
                        <h5 class="card-title">Before you upload your roofing photos:</h5>
                        <p class="card-text">Please review these tips to ensure a smooth upload:</p>
                        <ul class="list-unstyled ps-2">
                            <li>✔️ Ensure you know where your photos are stored on your computer.</li>
                            <li>✔️ Rename your photos so you can identify them easily.</li>
                            <li>✔️ Crop/resize photos if possible for clarity.</li>
                            <li>✔️ Take photos of problem areas and include access points.</li>
                            <li><strong>📌 Note:</strong> We can receive up to <strong>10MB total</strong> photo size.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- / Content -->



@endsection
