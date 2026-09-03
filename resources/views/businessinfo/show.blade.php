@extends('layouts.master')

@section('title', 'Show Business Info')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script>
        function checkaddress() {
            let postcode = $('#modalAddressPostcode').val().replace(/\s/g, '');

            if (!postcode) {
                toastr.error("Please enter a postcode.");
                $('#modalAddressPostcode').focus();
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
                        $('#modalAddressAddress').val("No address found.");
                        return;
                    }

                    let suggestions = response.address.addresses;

                    if (suggestions.length === 0) {
                        $('#modalAddressAddress').val("No address found.");
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

        function deletebusinessinfoAddress(addressid) {
            if (confirm('Are you sure you want to delete this businessinfo? This action cannot be undone.')) {
                const businessinfoId = {{ $businessinfo->id }}; // Pass server-side ID to JS
                $.ajax({
                    url: `/businessinfo/deleteaddress/${addressid}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success('Address deleted successfully.');
                        window.location.href = `/businessinfo/${businessinfoId}`; // Redirect to businessinfo list
                    },
                    error: function(xhr) {
                        let message = 'Delete failed.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    }
                });
            }
        };
        $(document).ready(function() {
            // Event listener to handle clicking on a suggestion (run this once on page load)
            $(document).on('click', '.suggestion-item', function(e) {
                e.preventDefault();
                let selectedAddress = $(this).text();
                let posttown = $('#posttown').val();
                $.ajax({
                    url: `/checkposttown/${posttown}`,
                    method: 'GET',
                    success: function(response) {
                        if (response && response.address) {
                            $('#modalAddressAddress').val(selectedAddress);
                            $('#modalAddressCounty').val(response.address.county);
                            $('#modalAddressCountry').val(response.address.country);
                            $('#address-suggestions').hide();
                        } else {
                            $('#modalAddressAddress').val("Error fetching address.");
                        }
                    },
                    error: function() {
                        $('#modalAddressAddress').val("Error fetching address.");
                    }
                });


            });

            // Hide suggestions when clicking outside
            $(document).click(function(event) {
                if (!$(event.target).closest('#basic-default-postcode, #address-suggestions').length) {
                    $('#address-suggestions').hide();
                }
            });

            // Handle form submission for editing user
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize();
                const businessinfoId = {{ $businessinfo->id }}; // Pass server-side ID to JS

                $.ajax({
                    url: `/businessinfo/${businessinfoId}/update-basic-info`, // Update to your route
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    beforeSend: function() {
                        // Optional: show loader or disable submit button
                    },
                    success: function(response) {
                        // Handle success (e.g., close modal, show success message)
                        toastr.success('businessinfo details updated successfully.');
                        $('#editUser').modal('hide');
                        location.reload(); // This will reload the entire page
                    },
                    error: function(xhr) {
                        // Handle errors
                        let errors = xhr.responseJSON.errors;
                        let message = 'Update failed.';

                        if (errors) {
                            message = Object.values(errors).flat().join('\n');
                        }
                        toastr.error(message);
                    }
                });
            });


            $('#addNewAddress').on('show.bs.modal', function(event) {
                const button = $(document.activeElement); // works with buttons clicked
                const addressType = button.data('address-type'); // get data-address-type

                if (addressType) {
                    $('#businessinfoaddresstype').val(addressType);
                }
            });

            $('#addNewAddress').on('hidden.bs.modal', function() {
                $('#businessinfoaddresstype').val(''); // optional: clear value
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#useraccessForm').on('submit', function(e) {
                e.preventDefault();

                let formData = new FormData(this);
                let submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: "{{ route('users.store') }}", // ✅ Your Laravel route to handle creation
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // ✅ Success message
                            toastr.success('businessinfo details updated successfully.');


                            // Close modal and reset form
                            $('#useraccess').modal('hide');
                            $('#useraccessForm')[0].reset();

                            // (Optional) Reload a table if you're using DataTables
                            if ($('.datatables-ajax').length) {
                                $('.datatables-ajax').DataTable().ajax.reload(null, false);
                            }
                            location.reload(); // This will reload the entire page
                        } else {
                            toastr.error(response.message || 'Something went wrong!');
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        let message = "An error occurred while creating the user.";

                        if (errors) {
                            message = Object.values(errors).flat().join("\n");
                        }

                        toastr.error(message);
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text('Submit');
                    }
                });
            });
        });
    </script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />

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
            position: relative;
            z-index: 9999;
            background-color: rgb(255, 157, 157);
            color: rgb(0, 0, 0);
            width: 85%;
            margin-left: 2%;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-sm-row align-items-center justify-content-sm-between mb-6 text-center text-sm-start gap-2">
            <div class="mb-2 mb-sm-0">
                <h4 class="mb-1">{{ $businessinfo->business_name }} Details</h4>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- businessinfo-detail Sidebar -->
            <div class="col-xl-12 col-lg-12 col-md-12 order-1 order-md-0">
                <!-- businessinfo-detail Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        
                        <div class="card mb-6">
                            <div class="card-header header-elements pb-4 border-bottom text-capitalize mb-4">
                                <h5 class="mb-0 me-2">Business Info</h5>

                                <div class="card-header-elements ms-auto">
                                    <a href="{{ route('businessinfo.edit', $businessinfo->id) }}" class="btn btn-xs btn-primary waves-effect waves-light">
                                        <span class="icon-base ti tabler-edit icon-xs me-1"></span>Edit Details
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-container row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-6">
                                            <li class="mb-2">
                                                <span class="h6 me-1">Business Name:</span>
                                                <span>{{ $businessinfo->business_name }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Business Short Name:</span>
                                                <span>{{ $businessinfo->business_short_name ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Business Domain:</span>
                                                <span>{{ $businessinfo->business_domain ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Business Start Date:</span>
                                                <span>{{ $businessinfo->business_start_date ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Allowed File Types:</span>
                                                <span>{{ $businessinfo->business_allowed_file_types ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Address:</span>
                                                <span>{{ $businessinfo->business_address ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Address2:</span>
                                                <span>{{ $businessinfo->business_address2 ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">City:</span>
                                                <span>{{ $businessinfo->business_city ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">State:</span>
                                                <span>{{ $businessinfo->business_state ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Zip:</span>
                                                <span>{{ $businessinfo->business_zip ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Country:</span>
                                                <span>{{ $businessinfo->business_country ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Phone:</span>
                                                <span>{{ $businessinfo->business_phone ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Email:</span>
                                                <span>{{ $businessinfo->business_email ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">Website:</span>
                                                <span>{{ $businessinfo->business_website ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">BIN:</span>
                                                <span>{{ $businessinfo->business_registration_number ?? '' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <span class="h6 me-1">VAT:</span>
                                                <span>{{ $businessinfo->business_registration_number2 ?? '' }}</span>
                                            </li>
                                            
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                         <span class="h6 me-1">Logo:</span><img id="previewLogo" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;" src="{{ $businessinfo->business_logo }}" />
                                         </div>
                                         <div>
                                            <span class="h6 me-1">Logo Dark:</span><img id="previewLogoDark" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;" src="{{ $businessinfo->business_logo_dark }}" />
                                         </div>
                                         <div>
                                            <span class="h6 me-1">Favicon:</span><img id="previewFavicon" class="img-thumbnail mt-2 d-none1" style="max-height: 120px;" src="{{ $businessinfo->business_favicon }}" />
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-6">
                            <div class="card-header header-elements">
                                <h5 class="mb-0 me-2">Branding Info</h5>

                                <div class="card-header-elements ms-auto">
                                    <a href="{{ route('businessinfo.edit', $businessinfo->id) }}" class="btn btn-xs btn-primary waves-effect waves-light">
                                        <span class="icon-base ti tabler-edit icon-xs me-1"></span>Edit Details
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-container">
                                    <ul class="list-unstyled mb-6">
                                        <li class="mb-2">
                                            <span class="h6 me-1">Brand Logo On Top:</span>
                                            <span>{!! $businessinfo->business_brand_logo_on_top_left ?? ''  !!}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Brand Credit On Footer Enable?:</span>
                                            <span>{{ $businessinfo->business_brand_credit_on_footer_enable ? 'Yes' : 'No' }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Brand Credit On Footer:</span>
                                            <span>{!! $businessinfo->business_brand_credit_on_footer ?? ''  !!}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /businessinfo-detail Card -->
            </div>
            <!--/ businessinfo Sidebar -->
        </div>

        
    </div>
    <!-- / Content -->

@endsection
