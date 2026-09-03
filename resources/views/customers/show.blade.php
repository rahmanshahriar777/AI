@extends('layouts.master')

@section('title', 'Customer')

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

        function deleteCustomerAddress(addressid) {
            if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                const customerId = {{ $customer->id }}; // Pass server-side ID to JS
                $.ajax({
                    url: `/customer/deleteaddress/${addressid}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        let message = 'Address deleted successfully.';
                        if (response.message) {
                            message = response.message;
                        }
                        toastr.success(message);
                        setTimeout(() => {
                            window.location.href = `/customers/${customerId}`; // Redirect to customers list
                        }, 1000);
                        
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
        }

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
                const customerId = {{ $customer->id }}; // Pass server-side ID to JS

                $.ajax({
                    url: `/customers/${customerId}/update-basic-info`, // Update to your route
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
                        toastr.success('Customer details updated successfully.');
                        $('#editUser').modal('hide');
                        setTimeout(function(){
                            location.reload(); // This will reload the entire page
                        },1000);
                        
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

            // Handle form submission for editing user
            $('#editFinForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize();
                const customerId = {{ $customer->id }}; // Pass server-side ID to JS

                $.ajax({
                    url: $(this).attr('action'), // Update to your route
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
                        //toastr.success('Customer details updated successfully.');
                        toastr.success(response.message);
                        $('#editFininfo').modal('hide');
                        setTimeout(function(){
                            location.reload(); // This will reload the entire page
                        },1000);
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
                const addressId = button.data('address-id'); // get data-address-type

                if (addressType) {
                    $('#customeraddresstype').val(addressType);
                }
                else{
                    $('#customeraddresstype').val('');
                }

                if(addressId){
                    $('.address-title.mb-2').hide();
                    $('.address-title.mb-3').show();
                    $('#customeraddressid').val(addressId);
                    $.ajax({
                        url: `/customer/address-get/${addressId}`, // Update to your route
                        type: 'GET',
                        data: null,
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').val()
                        },
                        beforeSend: function() {
                            // Optional: show loader or disable submit button
                        },
                        success: function(response) {
                            if(response.id){
                                $('#modalAddressFirstName').val(response.contact_firstname);
                                $('#modalAddressLastName').val(response.contact_lastname);
                                $('#modalAddressemail').val(response.contact_email);
                                $('#modalAddressPhone').val(response.contact_phone);
                                $('#modalAddressMobile').val(response.contact_mobile);
                                $('#modalAddressPostcode').val(response.postcode);
                                $('#modalAddressAddress').val(response.address);
                                $('#modalAddressCounty').val(response.county);
                                $('#modalAddressCountry').val(response.country);
                            }
                        },
                        error: function(xhr) {
                            // Handle errors
                            let errors = xhr.responseJSON.errors;
                            let message = 'Update failed.';

                            if (errors) {
                                message = Object.values(errors).flat().join('\n');
                            }
                            toastr.error(message);
                            $('#addNewAddress').modal('hide');
                        }
                    });
                }
                else{
                    $('#customeraddressid').val('');
                    $('.address-title.mb-2').show();
                    $('.address-title.mb-3').hide();
                }

            });

            $('#addNewAddress').on('hidden.bs.modal', function() {
                $('#customeraddresstype').val(''); // optional: clear value
                $('#customeraddressid').val('');

                $('#modalAddressFirstName').val('');
                $('#modalAddressLastName').val('');
                $('#modalAddressemail').val('');
                $('#modalAddressPhone').val('');
                $('#modalAddressMobile').val('');
                $('#modalAddressPostcode').val('');
                $('#modalAddressAddress').val('');
                $('#modalAddressCounty').val('');
                $('#modalAddressCountry').val('');

                $('.address-title.mb-2').show();
                $('.address-title.mb-3').hide();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#useraccessForm').on('submit', function(e) {
                let type = 'POST';
                let url = '{{ route('users.store') }}';
                @if ($useraccess)
                    url = '{{ route('users.update', $useraccess->id) }}';
                @endif

                e.preventDefault();

                let formData = new FormData(this);
                let submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: url, // ✅ Your Laravel route to handle creation
                    type: type,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // ✅ Success message
                            toastr.success(response.message || 'User access updated successfully.');


                            // Close modal and reset form
                            $('#useraccess').modal('hide');
                            $('#useraccessForm')[0].reset();

                            // (Optional) Reload a table if you're using DataTables
                            if ($('.datatables-ajax').length) {
                                $('.datatables-ajax').DataTable().ajax.reload(null, false);
                            }
                            setTimeout(function(){
                                location.reload(); // This will reload the entire page
                            },1000);
                            
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
    
    
        $(document).on('click', '.delete-customer', function() {
            let customerId = $(this).data('id');
            let url = `/customers/${customerId}`;

            if (confirm("Are you sure you want to delete this customer?")) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        
                        setTimeout(function() {
                            window.location.href = '/customers';
                        }, 1000); // 1000 milliseconds = 1 second
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            }
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
                <h4 class="mb-1">Customer Details</h4>
            </div>
            <button type="button" class="btn btn-label-danger delete-customer" data-id="{{ $customer->id }}">Delete Customer</button>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Customer-detail Sidebar -->
            <div class="col-xl-4 col-lg-4 col-md-4 order-1 order-md-0">
                <!-- Customer-detail Card -->
                <div class="card mb-6">
                    <div class="card-body pt-12">
                        <div class="customer-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="customer-info text-center mb-6">
                                    <h4 class="mb-0">{{ $customer->company_name }}</h4>
                                    @if ($useraccess)
                                        <span class="badge bg-label-success">Username: {{ $useraccess->email }}</span>
                                        <span class="badge bg-label-success">Password: ******</span>
                                    @endif
                                    <a href="javascript:;" class="btn btn-xs btn-primary waves-effect waves-light"
                                        data-bs-target="#useraccess" data-bs-toggle="modal">
                                        <span class="icon-base ti tabler-edit icon-xs me-1"></span>User Access
                                    </a>

                                </div>
                            </div>
                        </div>
                        <!-- User Access Modal -->
                        <div class="modal fade" id="useraccess" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <div class="text-center mb-6">
                                            <h4 class="mb-2">User Access Information</h4>
                                        </div>
                                        <form id="useraccessForm" class="row g-6" onsubmit="return false">
                                            @csrf
                                            @if ($useraccess)
                                            @method('PUT')
                                            @endif
                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="fullname">Full Name</label>
                                                <input type="text" id="fullname" name="name"
                                                    class="form-control"
                                                    value="{{ $customer->contact_firstname . ' ' . $customer->contact_lastname }}" readonly />
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="useraccessEmail">Email</label>
                                                <input type="text" id="useraccessEmail" name="email"
                                                    class="form-control" placeholder="example@domain.com"
                                                    value="{{ $customer->contact_email }}" readonly />
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="useraccessPassword">Password</label>
                                                <input type="text" id="useraccessPassword" name="password"
                                                    class="form-control" placeholder="Enter Password" required />
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label" for="confirmPassword">Confirm Password</label>
                                                <input type="text" id="confirmPassword" name="confirm-password"
                                                    class="form-control" placeholder="Enter Password" required />
                                            </div>

                                            <div class="col-12 text-center">
                                                <input type="hidden" name="roles" value="customer" />
                                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                                <button type="reset" class="btn btn-label-secondary"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/ User Access Modal -->

                        <div class="card mb-6">
                            <div class="card-header header-elements pb-4 border-bottom text-capitalize mb-4">
                                <h5 class="mb-0 me-2">Info</h5>

                                <div class="card-header-elements ms-auto">
                                    <a href="javascript:;" class="btn btn-xs btn-primary waves-effect waves-light"
                                        data-bs-target="#editUser" data-bs-toggle="modal">
                                        <span class="icon-base ti tabler-edit icon-xs me-1"></span>Edit Details
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-container">
                                    <ul class="list-unstyled mb-6">
                                        <li class="mb-2">
                                            <span class="h6 me-1">Contact Person:</span>
                                            <span>{{ $customer->contact_firstname }}
                                                {{ $customer->contact_lastname }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Email:</span>
                                            <span>{{ $customer->contact_email ?? 'N/A' }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Contact Mobile:</span>
                                            <span>{{ $customer->contact_mobile ?? 'N/A' }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Contact Phone:</span>
                                            <span>{{ $customer->contact_phone ?? 'N/A' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-6">
                            <div class="card-header header-elements">
                                <h5 class="mb-0 me-2">Financial Info</h5>

                                <div class="card-header-elements ms-auto">
                                    <a href="javascript:;" class="btn btn-xs btn-primary waves-effect waves-light"
                                        data-bs-target="#editFininfo" data-bs-toggle="modal">
                                        <span class="icon-base ti tabler-edit icon-xs me-1"></span>Add / Edit
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="info-container">
                                    <ul class="list-unstyled mb-6">
                                        <li class="mb-2">
                                            <span class="h6 me-1">Reg. Number:</span>
                                            <span>{{ $customer->findetails->registration_number ?? 'N/A' }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Assets:</span>
                                            <span>{{ formatPoundNumber($customer->findetails->assets ?? 0,2) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Net Assets:</span>
                                            <span>{{ formatPoundNumber($customer->findetails->net_assets ?? 0, 2) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Liabilities:</span>
                                            <span>{{ formatPoundNumber($customer->findetails->liabilities ?? 0, 2) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="h6 me-1">Cash in Bank:</span>
                                            <span>{{ formatPoundNumber($customer->findetails->cash_in_bank ?? 0, 2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /Customer-detail Card -->
            </div>
            <!--/ Customer Sidebar -->

            <!-- Customer Content -->
            <div class="col-xl-8 col-lg-8 col-md-8 order-0 order-md-1">
                <!-- Customer Pills -->
                <div class="nav-align-top">
                    <ul class="nav nav-pills nav-justified mb-6">

                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="modal" data-bs-target="#addNewAddress"
                                data-address-type="billing" @if (isset($customer->billingaddress->id) && $customer->billingaddress->id >= 0) style="background-color: darkgray;" disabled @endif>
                                <i class="icon-base ti tabler-map-pin icon-sm me-1_5"></i> Add Billing Address
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="modal" data-bs-target="#addNewAddress"
                                data-address-type="site">
                                <i class="icon-base ti tabler-map-pin icon-sm me-1_5"></i> Add Site Address
                            </button>
                        </li>

                    </ul>
                </div>
                <!--/ Customer Pills -->
                <!-- Address -->
                <div class="row">
                    <!-- Billing Address -->
                    @if ($customer->billingaddress)
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="d-flex flex-md-row flex-column">
                                    <div>
                                        <iframe width="100%" height="300" frameborder="0" style="border:0"
                                            referrerpolicy="no-referrer-when-downgrade"
                                            src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $customer->billingaddress->address ?? '' }}, {{ $customer->billingaddress->county ?? '' }}, {{ $customer->billingaddress->postcode ?? '' }}+{{ $customer->billingaddress->address ?? '' }}, {{ $customer->billingaddress->county ?? '' }}, {{ $customer->billingaddress->country ?? '' }}"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                    <div>
                                        <div class="card-body">
                                            <h5 class="card-title">Billing Address</h5>
                                            <p class="card-text">
                                                <span class="badge bg-label-dark mb-2">Contact: </span>
                                                {{ $customer->billingaddress->contact_firstname }}
                                                {{ $customer->billingaddress->contact_lastname }}
                                                </br>
                                                <span class="badge bg-label-dark mb-2">Email: </span>
                                                {{ $customer->billingaddress->contact_email }}
                                                </br>
                                                <span class="badge bg-label-dark mb-2">Phone: </span>
                                                {{ $customer->billingaddress->contact_phone }}
                                                </br>
                                                <span class="badge bg-label-dark mb-2">Mobile: </span>
                                                {{ $customer->billingaddress->contact_mobile }}
                                            </p>

                                            <p class="card-text">
                                                {{ $customer->billingaddress->address }},
                                                {{ $customer->billingaddress->county }},
                                                {{ $customer->billingaddress->country }}
                                            </p>
                                            <a href="javascript:void(0);"
                                                class="btn btn-outline-dark btn-sm waves-effect" data-bs-toggle="modal"  data-bs-target="#addNewAddress" data-address-type="billing" data-address-id="{{ $customer->billingaddress->id }}">Edit</a>
                                            <a href="javascript:void(0);"
                                                onclick="deleteCustomerAddress({{ $customer->billingaddress->id }})"
                                                class="btn btn-outline-danger btn-sm waves-effect">DELETE</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!--/ Billing Address -->
                </div>
                <div class="row">
                    <!-- Site Address -->
                    @if ($customer->siteaddress)
                        @foreach ($customer->siteaddress as $csaddress)
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="d-flex flex-md-row flex-column">
                                        <div>
                                            <iframe width="100%" height="300" frameborder="0" style="border:0"
                                                referrerpolicy="no-referrer-when-downgrade"
                                                src="https://www.google.com/maps/embed/v1/place?key={{ config('services.google.maps_api_key') }}&q={{ $csaddress->address }}, {{ $csaddress->county }}, {{ $csaddress->postcode }}+{{ $csaddress->address }}, {{ $csaddress->county }}, {{ $csaddress->country }}"
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                        <div>
                                            <div class="card-body">
                                                <h5 class="card-title">Site Address</h5>
                                                <p class="card-text">
                                                    <span class="badge bg-label-dark mb-2">Contact: </span>
                                                    {{ $csaddress->contact_firstname }}
                                                    {{ $csaddress->contact_lastname }}
                                                    </br>
                                                    <span class="badge bg-label-dark mb-2">Email: </span>
                                                    {{ $csaddress->contact_email }}
                                                    </br>
                                                    <span class="badge bg-label-dark mb-2">Phone: </span>
                                                    {{ $csaddress->contact_phone }}
                                                    </br>
                                                    <span class="badge bg-label-dark mb-2">Mobile: </span>
                                                    {{ $csaddress->contact_mobile }}
                                                </p>
                                                <p class="card-text">
                                                    {{ $csaddress->address }}, {{ $csaddress->county }},
                                                    {{ $csaddress->country }}
                                                </p>
                                                <a href="javascript:void(0);"
                                                    class="btn btn-outline-dark btn-sm waves-effect" data-bs-toggle="modal"  data-bs-target="#addNewAddress" data-address-type="site" data-address-id="{{ $csaddress->id }}">Edit</a>

                                                <a href="javascript:void(0);"
                                                    onclick="deleteCustomerAddress({{ $csaddress->id }})"
                                                    class="btn btn-outline-danger btn-sm waves-effect">DELETE</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!--/ Site Address -->

                </div>

                <!-- Address -->

            </div>
            <!--/ Customer Content -->
        </div>

        <!-- Modal -->
        <!-- Edit User Modal -->
        <div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h4 class="mb-2">Edit User Information</h4>
                        </div>
                        <form id="editUserForm" class="row g-6" onsubmit="return false">
                            @csrf
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserFirstName">First Name</label>
                                <input type="text" id="modalEditUserFirstName" name="contactPersonFirstName"
                                    class="form-control" placeholder="John" value="{{ $customer->contact_firstname }}"
                                    required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserLastName">Last Name</label>
                                <input type="text" id="modalEditUserLastName" name="contactPersonLastName"
                                    class="form-control" placeholder="Doe" value="{{ $customer->contact_lastname }}" required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditUserEmail">Email</label>
                                <input type="text" id="modalEditUserEmail" name="contactEmail" class="form-control"
                                    placeholder="example@domain.com" value="{{ $customer->contact_email }}" required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditPhoneNumber">Phone Number</label>
                                <input type="text" id="modalEditPhoneNumber" name="contactPhone" class="form-control"
                                    placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                            pattern="{{ simple_format_phone_pattern() }}"  inputmode1="numeric"
                                    value="{{ $customer->contact_phone }}"
                                    oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalMobileNumber">Mobile Number</label>
                                <input type="text" id="modalMobileNumber" name="contactMobile" class="form-control"
                                    placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                            pattern="{{ simple_format_phone_pattern() }}"  inputmode1="numeric"
                                    value="{{ $customer->contact_mobile }}"
                                    oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);" />
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Edit User Modal -->
        <!-- Edit Financial Info Modal -->
        <div class="modal fade" id="editFininfo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-financial-info">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h4 class="mb-2">Edit Financial Information</h4>
                        </div>
                        <form id="editFinForm" method="POST" class="row g-6"
                            action="{{ route('customer.updatefinancialdetails', $customer->id) }}">
                            @csrf
                            <div class="col-12">
                                <label class="form-label" for="modalEditUserRegNumber">Reg. Number</label>
                                <input type="text" id="modalEditUserRegNumber" name="registration_no"
                                    class="form-control"
                                    value="{{ $customer->findetails->registration_number ?? '' }}" />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditAssets">Assets</label>
                                <input type="text" id="modalEditAssets" name="assets" class="form-control"
                                    placeholder="0.00" value="{{ $customer->findetails->assets ?? 0 }}" min="0" required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditnetAssets">Net Assets</label>
                                <input type="text" id="modalEditnetAssets" name="netAssets" class="form-control"
                                    placeholder="0.00" value="{{ $customer->findetails->net_assets ?? 0 }}" min="0" required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditLiabilities">Liabilities</label>
                                <input type="number" min="0" id="modalEditLiabilities" name="liabilities"
                                    class="form-control" placeholder="0.00"
                                    value="{{ $customer->findetails->liabilities ?? 0 }}" min="0" required />
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalEditCashinBank">Cash in Bank</label>
                                <input type="number" min="0" id="modalEditCashinBank" name="cashInBank"
                                    class="form-control" placeholder="0.00"
                                    value="{{ $customer->findetails->cash_in_bank ?? 0 }}" min="0" required />
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Edit Financial Info Modal -->

        <!-- Add New Address Modal -->
        <div class="modal fade" id="addNewAddress" tabindex="-1">
            <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-6">
                            <h4 class="address-title mb-2">Add New Address</h4>
                            <h4 class="address-title mb-3" style="display: hide;">Edit Address</h4>
                        </div>
                        <form method="POST" class="row g-6"
                            action="{{ route('customer.storeaddress', $customer->id) }}">
                            @csrf
                            <input type="hidden" name="customeraddressid" id="customeraddressid" />
                            <input type="hidden" name="type" id="customeraddresstype" />

                            <div class="col-12 form-control-validation col-md-6">
                                <label class="form-label" for="modalAddressFirstName">First Name</label>
                                <input type="text" id="modalAddressFirstName" name="contactPersonFirstName"
                                    class="form-control" placeholder="John" required />
                            </div>
                            <div class="col-12 form-control-validation col-md-6">
                                <label class="form-label" for="modalAddressLastName">Last Name</label>
                                <input type="text" id="modalAddressLastName" name="contactPersonLastName"
                                    class="form-control" placeholder="Doe" />
                            </div>

                            <div class="col-12 form-control-validation col-md-4">
                                <label class="form-label" for="modalAddressLastName">eMail</label>
                                <input type="email" id="modalAddressemail" name="contactEmail" class="form-control"
                                    placeholder="info@abc.com" />
                            </div>

                            <div class="col-12 form-control-validation col-md-4">
                                <label class="form-label" for="modalAddressPhone">Phone</label>
                                <input type="text" id="modalAddressPhone" name="contactPhone" class="form-control"
                                    placeholder="14169884411" maxlength="11" pattern="\d{11}" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11);" />
                            </div>

                            <div class="col-12 form-control-validation col-md-4">
                                <label class="form-label" for="modalAddressMobile">Mobile</label>
                                <input type="text" id="modalAddressMobile" name="contactMobile" class="form-control"
                                    placeholder="14169884411" maxlength="11" pattern="\d{11}" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 11);" />
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="modalAddressPostcode">Postcode</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="modalAddressPostcode" name="postcode"
                                        placeholder="WR11 7EJ" aria-label="WR11 7EJ" aria-describedby="button-addon2"
                                        oninput="this.value = this.value.toUpperCase()" required />
                                    <button class="btn btn-outline-primary" type="button" id="button-addon2"
                                        onclick="checkaddress()">Check</button>
                                </div>

                            </div>
                            <div id="loading-spinner" style="display: none;">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Searching...
                            </div>
                            <!-- Suggestions Container -->
                            <div id="address-suggestions" class="list-group mt-1" style="display:none;"></div>
                            <input type="hidden" id="posttown" value="" />

                            <div class="col-12">
                                <label class="form-label" for="modalAddressAddress">Address</label>
                                <textarea id="modalAddressAddress" class="form-control" name="address" placeholder="12, Business Park" required></textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalAddressCounty">County</label>
                                <input type="text" id="modalAddressCounty" name="county" class="form-control"
                                    placeholder="Barkingham" />
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="modalAddressCountry">Country</label>
                                <input type="text" id="modalAddressCountry" name="country" class="form-control"
                                    placeholder="United Kingdom" />
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3">Submit</button>
                                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Add New Address Modal -->


        <!-- /Modal -->
    </div>
    <!-- / Content -->

@endsection
