@extends('layouts.master')

@section('title', 'Create Enquiry')

@section('scripts')
    @parent
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/enquiry-create.js') }}"></script>
@endsection

@section('styles')
    @parent
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <style>
        .new-customer {
            display: none;
            /* Hidden initially */
            padding: 15px;
            border: 1px solid #ccc;
            margin-top: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Sticky Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <form action="{{ route('enquiries.store') }}" method="POST" enctype="multipart/form-data"
                        class="row g-6">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div
                            class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                            <h5 class="card-title mb-sm-0 me-2">Add a new Enquiry</h5>
                            <div class="action-btns">
                                <button class="btn btn-label-primary me-4">
                                    <span class="align-middle"> Back</span>
                                </button>
                                <input id="submittype" type="hidden" name="submittype" value="AJAX" /> 
                                <button id="submitButton" type="submit" class="btn btn-primary">Save draft</button>
                            </div>
                        </div>
                        <div class="card-body pt-6">
                            <div class="row">

                                <!-- First column -->
                                <div class="col-12 col-lg-12">
                                    <!-- Customer Card -->
                                    <div class="card mb-6">
                                        <div class="card-body">
                                            <!-- Customers -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="mb-6 col ecommerce-select2-dropdown">
                                                    <label class="form-label mb-1" for="customer">
                                                        <span>Select Customer or Create New</span>
                                                    </label>
                                                    <select id="customer" class="select2 form-select"
                                                        data-placeholder="Select customer" name="customer_id">
                                                        <option value="">Select customer</option>
                                                        @foreach ($customers as $cust)
                                                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }} >{{ $cust->company_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="button"
                                                    class="fw-medium btn btn-md btn-label-primary ms-8 toggle-customer-btn"
                                                    onclick="toggleNewCustomer(event)">
                                                    <i class="icon-base ti tabler-plus icon-md"></i> Add New Customer
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Organize Card -->

                                    <!-- Customers -->
                                    <div class="card mb-6 new-customer">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">New Customer</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="card mb-6">
                                                <div class="card-header px-0 pt-0">
                                                    <div class="nav-align-top">
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <button type="button" class="nav-link active"
                                                                    data-bs-toggle="tab" data-bs-target="#form-tabs-company"
                                                                    aria-controls="form-tabs-company" role="tab"
                                                                    aria-selected="true">
                                                                    <span
                                                                        class="icon-base ti tabler-user icon-lg d-sm-none"></span>
                                                                    <span class="d-none d-sm-block">Company Info</span>
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <div class="tab-content p-0">
                                                        <!-- Compnay Info -->
                                                        <div class="tab-pane fade active show" id="form-tabs-company"
                                                            role="tabpanel">
                                                            <div class="row g-6">
                                                                <div class="col-md-12">
                                                                    <div class="mb-1">
                                                                        <label class="form-label"
                                                                            for="formtabs-user-company">Company</label>
                                                                        <input type="text" id="formtabs-user-company"
                                                                            class="form-control" placeholder="Company Name"
                                                                            aria-label="jdoe1 ltd" name="companyName" value="{{ old('companyName', '') }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="row mb-1">
                                                                        <label class="form-label"
                                                                            for="formtabs-contact-personname">Contact
                                                                            Person</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control"
                                                                                id="formtabs-contact-personfirstname"
                                                                                placeholder="First Name"
                                                                                name="contactPersonFirstName"
                                                                                aria-label="First Name" value="{{ old('contactPersonFirstName', '') }}" />
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <input type="text" class="form-control"
                                                                                id="formtabs-contact-personlastname"
                                                                                placeholder="Last Name"
                                                                                name="contactPersonLastName"
                                                                                aria-label="Last Name" value="{{ old('contactPersonLastName', '') }}"  />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <div class="mb-1">
                                                                        <label class="form-label"
                                                                            for="formtabs-contact-email">Contact
                                                                            email</label>
                                                                        <input type="email" id="formtabs-contact-email"
                                                                            class="form-control"
                                                                            placeholder="john.doe@example.com"
                                                                            aria-label="john.doe@example.com"
                                                                            name="contactEmail" value="{{ old('contactEmail', '') }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="mb-1">
                                                                        <label class="form-label"
                                                                            for="formtabs-contact-phone">Contact
                                                                            Phone</label>
                                                                        <input type="text" id="formtabs-contact-phone"
                                                                            name="contactPhone" class="form-control"
                                                                            placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                            pattern="{{ simple_format_phone_pattern() }}"  inputmode1="numeric"
                                                                            oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);" value="{{ old('contactPhone', '') }}" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="mb-1">
                                                                        <label class="form-label"
                                                                            for="formtabs-contact-mobile">Contact
                                                                            Mobile</label>
                                                                        <input type="text" id="formtabs-contact-mobile"
                                                                            name="contactMobile" class="form-control"
                                                                            placeholder="{{ simple_format_phone('') }}" maxlength="20"
                                                            pattern="{{ simple_format_phone_pattern() }}"  inputmode1="numeric"
                                                                            oninput1="this.value = this.value.replace(/\D/g, '').slice(0, 11);" value="{{ old('contactMobile', '') }}" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- /Customers -->
                                </div>
                                <!-- /First column -->

                                <!-- Second column-->
                                <div class="col-12 col-lg-8">
                                    <!-- Enquiry -->
                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-tile mb-0">Customer Enquiry</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Description -->
                                            <div class="mb-6">
                                                <div class="form-control p-0">
                                                    <div id="enquirytoolbar"
                                                        class="enquiry-toolbar border-0 border-bottom"></div>
                                                    <div class="enquiry-editor border-0 pb-6" data-target="#enquiry_input"
                                                        data-toolbar="#enquirytoolbar"></div>
                                                    <input type="hidden" name="enquiry" id="enquiry_input" value="{{ old('enquiry', '') }}" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Enquiry -->
                                    <!-- Enquiry Information -->
                                    <div class="card mb-6">
                                        <div class="card-header">
                                            <h5 class="card-tile mb-0">Access Details / Requirements / Enquiry Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <!-- Description -->
                                            <div class="mb-6">
                                                <div class="form-control p-0">
                                                    <div id="enquirydescriptiontoolbar"
                                                        class="enquiry-description-toolbar border-0 border-bottom"></div>
                                                    <div class="enquiry-description-editor border-0 pb-6"
                                                        data-target="#enquiry_description_input"
                                                        data-toolbar="#enquirydescriptiontoolbar"></div>
                                                    <input type="hidden" name="enquirydescription"
                                                        id="enquiry_description_input" value="{{ old('enquirydescription', '') }}" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Enquiry Information -->
                                    <!-- Enquiry Images -->
                                    <div class="row mb-6 gy-6">
                                        <div class="col-xl">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Upload Images</h5>
                                                    <small class="text-body-secondary float-end">(optional)</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-md-12 mb-4">
                                                        <label class="form-label" for="attachments">Attachments (Multiple
                                                            Images)</label>
                                                        <input type="file" class="form-control" id="attachments"
                                                            name="attachments[]" accept="image/*" multiple>

                                                        {{-- Preview container --}}
                                                        <div class="image-preview mt-2 d-flex flex-wrap gap-2"
                                                            id="preview">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Enquiry Images -->


                                </div>
                                <!-- /Second column -->

                                <!-- Third column -->
                                <div class="col-12 col-lg-4">
                                    <!-- Enquiry Location -->
                                    <div class="row mb-6 gy-6">
                                        <div class="col-xl">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Enquiry Location</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-6">
                                                        <div class="col-md-12">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    id="formtabs-enquiry-postcode" name="enquiry_postcode"
                                                                    placeholder="Check postcode"
                                                                    aria-label="Check postcode"
                                                                    aria-describedby="button-addon2" required value="{{ old('enquiry_postcode', '') }}" />
                                                                <button class="btn btn-outline-primary waves-effect"
                                                                    type="button" id="button-addon2"
                                                                    onclick="checkaddress()">Check</button>
                                                            </div>
                                                            <div id="loading-spinner" style="display: none;">
                                                                <span class="spinner-border spinner-border-sm"
                                                                    role="status" aria-hidden="true"></span>
                                                                Searching...
                                                            </div>
                                                            <!-- Suggestions Container -->
                                                            <div id="address-suggestions" class="list-group mt-1"
                                                                style="display:none;"></div>
                                                            <input type="hidden" id="posttown" value="" />
                                                        </div>

                                                        <div class="col-md-12">
                                                            <label class="form-label"
                                                                for="formtabs-enquiry-address">Address</label>
                                                            <textarea class="form-control" id="formtabs-enquiry-address" name="enquiry_address" placeholder="30H Burdi Street"
                                                                required>{{ old('enquiry_address', '') }}</textarea>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label"
                                                                for="formtabs-enquiry-county">County</label>
                                                            <input class="form-control" id="formtabs-enquiry-county"
                                                                name="enquiry_county" placeholder="Birmingham"
                                                                aria-label="Birmingham" required  value="{{ old('enquiry_county', '') }}" />
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label class="form-label"
                                                                for="formtabs-enquiry-country">Country</label>
                                                            <input class="form-control" id="formtabs-enquiry-country"
                                                                name="enquiry_country" value="United Kingdom" readonly  value="{{ old('enquiry_country', '') }}" />
                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Enquiry Location -->
                                    <!-- Enquiry Other Info -->
                                    <div class="row mb-6 gy-6">
                                        <div class="col-xl">
                                            <div class="card">

                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Other Information</h5>
                                                    <small class="text-body-secondary float-end">
                                                        (optional)
                                                    </small>
                                                </div>
                                                <div class="card-body">
                                                    {{-- Roofing --}}
                                                    <div class="mb-4">
                                                        <label class="form-label d-block fw-bold">Roofing</label>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="roofing_info[]"
                                                                id="roofing_info" value="repairs"
                                                                class="form-check-input" @if (in_array('repairs', old('roofing_info',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="roofing_info">Repairs</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="roofing_info[]"
                                                                id="roofing_overclad" value="overclad"
                                                                class="form-check-input" @if (in_array('overclad', old('roofing_info',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="roofing_overclad">Overclad</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="roofing_info[]"
                                                                id="roofing_info_large_works" value="large_works"
                                                                class="form-check-input" @if (in_array('large_works', old('roofing_info',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="roofing_info_large_works">Large Works</label>
                                                        </div>
                                                    </div>

                                                    {{-- Annual Maintenance --}}
                                                    <div class="mb-4">
                                                        <label class="form-label d-block fw-bold">Annual
                                                            Maintenance</label>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="annual_maintenance[]"
                                                                id="annual_maintenance" value="maintenance"
                                                                class="form-check-input" @if (in_array('maintenance', old('annual_maintenance',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="annual_maintenance">Yes</label>
                                                        </div>
                                                    </div>

                                                    {{-- Safety --}}
                                                    <div class="mb-4">
                                                        <label class="form-label d-block fw-bold">Safety</label>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="safety[]" id="safety_install"
                                                                value="install" class="form-check-input" @if (in_array('install', old('safety',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="safety_install">Install</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="safety[]" id="safety_repairs"
                                                                value="repairs" class="form-check-input" @if (in_array('repairs', old('safety',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="safety_repairs">Repairs</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" name="safety[]" id="safety_testing"
                                                                value="testing" class="form-check-input" @if (in_array('testing', old('safety',[]))) checked @endif>
                                                            <label class="form-check-label"
                                                                for="safety_testing">Testing</label>
                                                        </div>
                                                    </div>

                                                    {{-- Priority --}}
                                                    <div class="mb-4">
                                                        <label class="form-label d-block fw-bold">Priority</label>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="enquiry_priority"
                                                                id="priority_low" value="low" class="form-check-input"
                                                                 @if ( 'low' == old('enquiry_priority', 'low') ) checked @endif>
                                                            <label class="form-check-label" for="priority_low">Low</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="enquiry_priority"
                                                                id="priority_medium" value="medium"
                                                                class="form-check-input" @if ( 'medium' == old('enquiry_priority','') ) checked @endif>
                                                            <label class="form-check-label"
                                                                for="priority_medium">Medium</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" name="enquiry_priority"
                                                                id="priority_high" value="high"
                                                                class="form-check-input" @if ( 'high' == old('enquiry_priority','') ) checked @endif>
                                                            <label class="form-check-label"
                                                                for="priority_high">High</label>
                                                        </div>
                                                    </div>

                                                    {{-- Source --}}
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold"
                                                            for="enquiry_source">Source</label>
                                                        <textarea name="enquiry_source" id="enquiry_source" rows="2" class="form-control"
                                                            placeholder="{{ $appbrand->business_website??'' }}">{{ old('enquiry_source', '') }}</textarea>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    <!-- /Enquiry Other Info -->
                                    <!-- Enquiry Notes -->
                                    <div class="row mb-6 gy-6">
                                        <div class="col-xl">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Internal Notes</h5>
                                                    <small class="text-body-secondary float-end">(optional)</small>
                                                </div>
                                                <div class="card-body">

                                                    <div class="col-12 mb-6">
                                                        <div class="mb-6">
                                                            <div class="form-control p-0">
                                                                <div id="toolbar_2"
                                                                    class="enquiry-toolbar border-0 border-bottom">
                                                                </div>
                                                                <div class="enquiry-editor border-0 pb-6"
                                                                    data-target="#description_input_2"
                                                                    data-toolbar="#toolbar_2">
                                                                </div>
                                                                <input type="hidden" name="enquiryNote"
                                                                    id="description_input_2" value="{{ old('enquiryNote', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Enquiry Notes -->
                                </div>
                                <!-- /Third column -->
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- /Sticky Actions -->
    </div>
    <!-- / Content -->

@endsection
